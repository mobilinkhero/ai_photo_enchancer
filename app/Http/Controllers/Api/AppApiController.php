<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\AppFeature;
use App\Models\Photo;
use App\Models\ApiLog;
use App\Services\ApiLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AppApiController extends Controller
{
    private const APP_SECRET = 'my_super_secret_app_key_2026';
    private const SSL_VERIFY = false; // Disabling for local Windows dev fix

    /* ─────────────────────────────────────────────
       MIDDLEWARE: Validate X-App-Secret header
     ───────────────────────────────────────────── */
    private function validateSecret(Request $request): bool
    {
        return $request->header('X-App-Secret') === self::APP_SECRET;
    }

    /* ─────────────────────────────────────────────
       GET /api/config
       Flutter splash screen calls this.
       Returns config map + features list.
     ───────────────────────────────────────────── */
    public function config(Request $request): JsonResponse
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // All configs keyed by key
        $configs = AppConfig::all()->keyBy('key');

        // Check maintenance mode
        if ($configs->get('maintenance_mode')?->value == '1') {
            return response()->json([
                'success' => false,
                'maintenance' => true,
                'message' => 'App is under maintenance. Please try again later.',
            ], 503);
        }

        // Build config map for Flutter
        $config = [
            // App
            'app_name' => $configs->get('app_name')?->value ?? 'AI Photo Enhancer',
            'app_version' => $configs->get('app_version')?->value ?? '1.0.0',
            'allow_registration' => ($configs->get('allow_registration')?->value ?? '1') == '1',
            'free_tier_enabled' => ($configs->get('free_tier_enabled')?->value ?? '1') == '1',
            'initial_free_coins' => (int) ($configs->get('free_credits')?->value ?? 5),
            'support_email' => $configs->get('support_email')?->value ?? '',
            'privacy_policy_url' => $configs->get('privacy_policy_url')?->value ?? '',

            // AdMob (all optional — null means skip ad)
            'admob_enabled' => ($configs->get('admob_enabled')?->value ?? '1') == '1',
            'admob_banner_unit_id' => $configs->get('admob_banner_unit_id')?->value ?? '',
            'admob_interstitial_unit_id' => $configs->get('admob_interstitial_unit_id')?->value ?? '',
            'admob_rewarded_unit_id' => $configs->get('admob_rewarded_unit_id')?->value ?? '',
            'admob_app_open_unit_id' => $configs->get('admob_app_open_unit_id')?->value ?? '',
            'admob_native_unit_id' => $configs->get('admob_native_unit_id')?->value ?? '',

            // AI provider (for transparent display)
            'ai_provider' => $configs->get('ai_provider')?->value ?? 'replicate',

            // Pricing & Plan Names
            'pro_price_monthly' => $configs->get('pro_price_monthly')?->value ?? '4.99',
            'pro_price_yearly' => $configs->get('pro_price_yearly')?->value ?? '39.99',
            'premium_price' => $configs->get('premium_price')?->value ?? '9.99',
            'plan_monthly_name' => $configs->get('plan_monthly_name')?->value ?? 'Monthly Access',
            'plan_yearly_name' => $configs->get('plan_yearly_name')?->value ?? 'Yearly Access',
            'plan_lifetime_name' => $configs->get('plan_lifetime_name')?->value ?? 'Lifetime Access',
            'plan_monthly_subtitle' => $configs->get('plan_monthly_subtitle')?->value ?? 'Billed every month',
            'plan_yearly_subtitle' => $configs->get('plan_yearly_subtitle')?->value ?? 'Best Value - Save 60%',
            'plan_lifetime_subtitle' => $configs->get('plan_lifetime_subtitle')?->value ?? 'One-time payment',
            'plan_monthly_id' => $configs->get('plan_monthly_id')?->value ?? 'monthly_subsc',
            'plan_yearly_id' => $configs->get('plan_yearly_id')?->value ?? 'truni_pro_yearly',
            'plan_lifetime_id' => $configs->get('plan_lifetime_id')?->value ?? 'truni_pro_lifetime',
        ];

        // Features list (only enabled, sorted)
        $features = AppFeature::where('enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn($f) => $f->toAppArray())
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'config' => $config,
            'features' => $features,
        ]);
    }

    /* ─────────────────────────────────────────────
       POST /api/enhance   (and /api/{tool})
       Flutter sends: image file + tool + user_uid
       Returns: { success, result_url }
     ───────────────────────────────────────────── */
    public function enhance(Request $request, string $tool = 'enhance'): JsonResponse
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validate input
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $userUid = $request->input('user_uid');
        $logger = null;

        try {
            // Save original image
            $originalPath = $request->file('image')->store('uploads/original', 'public');

            // Create photo record
            $photo = Photo::create([
                'user_uid' => $userUid,
                'original_path' => $originalPath,
                'provider' => AppConfig::get('ai_provider', 'replicate'),
                'status' => 'processing',
            ]);

            // Start API logging
            $logger = ApiLogger::start($request, $userUid, $photo->id);

            // Get active AI provider
            $provider = AppConfig::get('ai_provider', 'replicate');

            $resultUrl = match ($provider) {
                'openai' => $this->processWithOpenAI($request, $logger, $photo, $tool),
                'gemini' => $this->processWithGemini($request, $logger, $photo, $tool),
                default => $this->processWithReplicate($request, $logger, $photo, $tool),
            };

            if ($resultUrl) {
                $photo->update([
                    'enhanced_path' => $resultUrl,
                    'status' => 'done',
                ]);
                $logger?->finish('success');

                return response()->json([
                    'success' => true,
                    'result_url' => $resultUrl,
                    'photo_id' => $photo->id,
                ]);
            }

            $photo->update(['status' => 'failed', 'error_message' => 'AI provider returned no output']);
            $logger?->finish('error', 'AI provider returned no output');

            return response()->json(['success' => false, 'message' => 'Enhancement failed. Please try again.'], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);

        } catch (\Exception $e) {
            $logger?->finish('error', $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /* ─────────────────────────────────────────────
       REPLICATE
     ───────────────────────────────────────────── */
    private function processWithReplicate(Request $request, ApiLogger $logger, Photo $photo, string $tool): ?string
    {
        $apiKey = AppConfig::get('replicate_api_key');

        // Map the Flutter app feature tool to specific Replicate models
        $model = match ($tool) {
            'enhance' => 'tencentarc/gfpgan:9283608cc6b7be6b65a8e44983db012355fde4132009bf99d976b2f0896856a3', // GFPGAN
            'face' => 'sczhou/codeformer:7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56', // CodeFormer
            'upscale' => 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b', // Real-ESRGAN
            'restore' => 'microsoft/bringing-old-photos-back-to-life:c75db81db6cbd809d93cc3b7e7a088a351a3349c9fa02b6d393e35e0d51ba799', // Old Photo Restore
            'colorize' => 'arielreplicate/deoldify_image:0da600fab0c45a66211339f3c16b71345d22f26ef5fea3dca1bb90bb5711e950', // DeOldify
            'background' => 'lucataco/remove-bg:95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1', // U2Net
            default => AppConfig::get('replicate_model', 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b'),
        };

        if (!$apiKey) {
            throw new \Exception('Replicate API key not configured in admin settings.');
        }

        // Get public URL to the uploaded image
        $imageUrl = Storage::disk('public')->url($photo->original_path);

        // Ensure it's not a relative path string
        if (!Str::startsWith($imageUrl, ['http://', 'https://'])) {
            $imageUrl = url($imageUrl);
        }

        // Build the specific inputs based on the model chosen
        $inputParams = match ($tool) {
            'enhance' => [ // GFPGAN
                'img' => $imageUrl,
                'scale' => 2,
                'version' => 'v1.4'
            ],
            'restore' => [ // Microsoft bringing-old-photos-back-to-life
                'image' => $imageUrl,
                'HR' => false,
                'with_scratch' => true
            ],
            'face' => [ // CodeFormer
                'image' => $imageUrl,
                'codeformer_fidelity' => 0.7,
                'background_enhance' => true,
                'face_upsample' => true,
                'upsample' => 2
            ],
            'background', 'colorize' => [
                'image' => $imageUrl
            ],
            default => [ // upscale (Real-ESRGAN)
                'image' => $imageUrl,
                'scale' => 4,
                'face_enhance' => true,
            ],
        };

        $payload = [
            'version' => Str::after($model, ':'),
            'input' => $inputParams,
        ];

        $logger->logAiRequest('replicate', 'https://api.replicate.com/v1/predictions', $payload);

        $start = microtime(true);

        // Create prediction
        $response = Http::withToken($apiKey)
            ->withOptions(['verify' => self::SSL_VERIFY])
            ->timeout(120)
            ->post('https://api.replicate.com/v1/predictions', $payload);

        $predictionData = $response->json();

        if (!$response->ok()) {
            $logger->logAiResponse($response->status(), $predictionData, (microtime(true) - $start) * 1000, $model);
            throw new \Exception('Replicate API error: ' . ($predictionData['detail'] ?? $response->body()));
        }

        $predictionId = $predictionData['id'];
        $getUrl = "https://api.replicate.com/v1/predictions/{$predictionId}";

        // Poll for result (max 2 minutes)
        for ($i = 0; $i < 40; $i++) {
            sleep(3);
            $poll = Http::withToken($apiKey)
                ->withOptions(['verify' => self::SSL_VERIFY])
                ->get($getUrl)
                ->json();

            if ($poll['status'] === 'succeeded') {
                $outputUrl = is_array($poll['output']) ? $poll['output'][0] : $poll['output'];
                $timeMs = (microtime(true) - $start) * 1000;
                $logger->logAiResponse(200, $poll, $timeMs, $model, $outputUrl);
                $photo->update(['model' => $model]);
                return $outputUrl;
            }

            if ($poll['status'] === 'failed') {
                $logger->logAiResponse(500, $poll, (microtime(true) - $start) * 1000, $model);
                throw new \Exception('Replicate prediction failed: ' . ($poll['error'] ?? 'Unknown'));
            }
        }

        throw new \Exception('Replicate prediction timed out after 2 minutes.');
    }

    /* ─────────────────────────────────────────────
       OPENAI (DALL-E image edit)
     ───────────────────────────────────────────── */
    private function processWithOpenAI(Request $request, ApiLogger $logger, Photo $photo, string $tool): ?string
    {
        $apiKey = AppConfig::get('openai_api_key');
        $model = 'dall-e-2'; // dall-e-3 is not supported for /v1/images/edits

        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured in admin settings.');
        }

        $endpoint = 'https://api.openai.com/v1/images/variations';

        $payload = [
            'n' => 1,
            'size' => '1024x1024',
        ];

        $logger->logAiRequest('openai', $endpoint, $payload);
        $start = microtime(true);

        $pngPath = $this->formatForOpenAI($request->file('image'));

        $response = Http::withToken($apiKey)
            ->attach('image', file_get_contents($pngPath), 'image.png')
            ->withOptions(['verify' => self::SSL_VERIFY])
            ->timeout(90)
            ->post($endpoint, $payload);

        $data = $response->json();
        $timeMs = (microtime(true) - $start) * 1000;

        $outputUrl = $data['data'][0]['url'] ?? null;
        $logger->logAiResponse($response->status(), $data, $timeMs, $model, $outputUrl);

        @unlink($pngPath);

        if (!$response->ok() || !$outputUrl) {
            throw new \Exception('OpenAI error: ' . ($data['error']['message'] ?? $response->body()));
        }

        $photo->update(['model' => $model]);
        return $outputUrl;
    }

    private function formatForOpenAI(\Illuminate\Http\UploadedFile $file): string
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $width = imagesx($image);
        $height = imagesy($image);

        // OpenAI requires square images for variations, max 1024x1024, < 4MB
        $cropDim = min($width, $height);
        $targetDim = min($cropDim, 1024);

        $newImage = imagecreatetruecolor($targetDim, $targetDim);

        // Center crop coordinates
        $srcX = max(0, ($width - $cropDim) / 2);
        $srcY = max(0, ($height - $cropDim) / 2);

        // Copy original image
        imagecopyresampled($newImage, $image, 0, 0, $srcX, $srcY, $targetDim, $targetDim, $cropDim, $cropDim);

        $tempFile = sys_get_temp_dir() . '/' . uniqid('openai_', true) . '.png';
        imagepng($newImage, $tempFile, 9); // Max compression for < 4MB

        imagedestroy($image);
        imagedestroy($newImage);

        return $tempFile;
    }

    /* ─────────────────────────────────────────────
       GEMINI
     ───────────────────────────────────────────── */
    private function processWithGemini(Request $request, ApiLogger $logger, Photo $photo, string $tool): ?string
    {
        $apiKey = AppConfig::get('gemini_api_key');

        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured in admin settings.');
        }

        $model = 'gemini-pro-vision';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $imageBase64 = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $mimeType = $request->file('image')->getMimeType();

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Please enhance, restore, and upscale this image to maximum quality.'],
                        ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageBase64]],
                    ],
                ]
            ],
        ];

        $logger->logAiRequest('gemini', $endpoint, ['model' => $model, 'has_image' => true]);
        $start = microtime(true);

        $response = Http::timeout(90)->post($endpoint, $payload);
        $data = $response->json();
        $timeMs = (microtime(true) - $start) * 1000;

        $outputUrl = null;
        // Gemini might return image parts or a URL
        if (isset($data['candidates'][0]['content']['parts'])) {
            foreach ($data['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['image_url'])) {
                    $outputUrl = $part['image_url'];
                    break;
                }
            }
        }

        $logger->logAiResponse($response->status(), $data, $timeMs, $model, $outputUrl);

        if (!$response->ok()) {
            throw new \Exception('Gemini error: ' . ($data['error']['message'] ?? $response->body()));
        }

        $photo->update(['model' => $model]);
        return $outputUrl;
    }
}
