<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\AppFeature;
use App\Services\ApiLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiTestController extends Controller
{
    /* ─────────────────────────────────────────────
       GET /admin/ai-test
    ───────────────────────────────────────────── */
    public function index()
    {
        $features = AppFeature::where('enabled', true)->orderBy('sort_order')->get();
        $provider = AppConfig::get('ai_provider', 'replicate');
        $hasKey = (bool) AppConfig::get($provider . '_api_key');

        return view('admin.ai_test.index', compact('features', 'provider', 'hasKey'));
    }

    /* ─────────────────────────────────────────────
       POST /admin/ai-test/process
       Called via AJAX — returns JSON with result_url & timing
    ───────────────────────────────────────────── */
    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'tool' => 'required|string',
        ]);

        $startAll = microtime(true);

        try {
            // Save image
            $path = $request->file('image')->store('uploads/test', 'public');
            $imageUrl = Storage::disk('public')->url($path);

            // If it's a relative path or resolving to localhost, make it absolute to the real host
            if (Str::startsWith($imageUrl, 'http://localhost')) {
                $imageUrl = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $imageUrl);
            } elseif (!Str::startsWith($imageUrl, ['http://', 'https://'])) {
                $imageUrl = request()->getSchemeAndHttpHost() . (Str::startsWith($imageUrl, '/') ? '' : '/') . $imageUrl;
            }

            $provider = AppConfig::get('ai_provider', 'replicate');
            $apiKey = AppConfig::get($provider . '_api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => "No API key set for provider: {$provider}. Go to Settings → AI Provider.",
                ], 422);
            }

            $tool = $request->input('tool');

            [$resultUrl, $aiMs, $logs] = match ($provider) {
                'openai' => $this->runOpenAI($request, $apiKey, $tool),
                'gemini' => $this->runGemini($request, $apiKey),
                default => $this->runReplicate($imageUrl, $apiKey, $tool),
            };

            $totalMs = round((microtime(true) - $startAll) * 1000);

            if (!$resultUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI returned no output. Check your API key and model settings.',
                    'logs' => $logs ?? [],
                ], 500);
            }

            return response()->json([
                'success' => true,
                'result_url' => $resultUrl,
                'provider' => $provider,
                'tool' => $tool,
                'ai_ms' => $aiMs,
                'total_ms' => $totalMs,
                'overhead_ms' => $totalMs - $aiMs,
                'logs' => $logs ?? [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /* ─── REPLICATE ─── */
    private function runReplicate(string $imageUrl, string $apiKey, string $tool): array
    {
        // Map the test tool to specific Replicate models
        $model = match ($tool) {
            'enhance' => 'tencentarc/gfpgan:9283608cc6b7be6b65a8e44983db012355fde4132009bf99d976b2f0896856a3', // GFPGAN
            'face' => 'sczhou/codeformer:7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56', // CodeFormer
            'upscale' => 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b', // Real-ESRGAN
            'restore' => 'microsoft/bringing-old-photos-back-to-life:c75db81db6cbd809d93cc3b7e7a088a351a3349c9fa02b6d393e35e0d51ba799', // Old Photo Restore
            'colorize' => 'arielreplicate/deoldify_image:0da600fab0c45a66211339f3c16b71345d22f26ef5fea3dca1bb90bb5711e950', // DeOldify
            'background' => 'lucataco/remove-bg:95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1', // U2Net
            default => AppConfig::get('replicate_model', 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b'),
        };

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

        $start = microtime(true);
        $response = Http::withToken($apiKey)
            ->withOptions(['verify' => false])
            ->timeout(20)
            ->post('https://api.replicate.com/v1/predictions', $payload);
        $data = $response->json();

        if (!$response->ok()) {
            throw new \Exception('Replicate: ' . ($data['detail'] ?? $response->body()));
        }

        $pollUrl = "https://api.replicate.com/v1/predictions/{$data['id']}";
        $logs = [['at' => 0, 'status' => 'starting', 'msg' => 'Prediction created: ' . $data['id']]];

        for ($i = 0; $i < 40; $i++) {
            sleep(3);
            $poll = Http::withToken($apiKey)
                ->withOptions(['verify' => false])
                ->get($pollUrl)->json();
            $elapsed = round((microtime(true) - $start) * 1000);
            $logs[] = ['at' => $elapsed, 'status' => $poll['status'], 'msg' => 'Poll #' . ($i + 1)];

            if ($poll['status'] === 'succeeded') {
                $out = is_array($poll['output']) ? $poll['output'][0] : $poll['output'];
                return [$out, $elapsed, $logs];
            }
            if ($poll['status'] === 'failed') {
                throw new \Exception('Replicate failed: ' . ($poll['error'] ?? 'unknown'));
            }
        }

        throw new \Exception('Replicate timed out after 2 minutes.');
    }

    /* ─── OPENAI ─── */
    private function runOpenAI(Request $request, string $apiKey, string $tool): array
    {
        $model = 'dall-e-2'; // dall-e-3 is not supported for /v1/images/edits
        $start = microtime(true);
        $endpoint = 'https://api.openai.com/v1/images/variations';

        $pngPath = $this->formatForOpenAI($request->file('image'));

        $response = Http::withToken($apiKey)
            ->attach('image', file_get_contents($pngPath), 'image.png')
            ->withOptions(['verify' => false])
            ->timeout(90)
            ->post($endpoint, [
                'n' => 1,
                'size' => '1024x1024',
            ]);

        $data = $response->json();
        $aiMs = round((microtime(true) - $start) * 1000);
        $outUrl = $data['data'][0]['url'] ?? null;

        @unlink($pngPath);

        if (!$response->ok() || !$outUrl) {
            throw new \Exception('OpenAI: ' . ($data['error']['message'] ?? $response->body()));
        }

        return [$outUrl, $aiMs, []];
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

    /* ─── GEMINI ─── */
    private function runGemini(Request $request, string $apiKey): array
    {
        $model = 'gemini-pro-vision';
        $start = microtime(true);
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $b64 = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $mime = $request->file('image')->getMimeType();

        $response = Http::timeout(90)->post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Enhance and upscale this image to maximum quality.'],
                        ['inline_data' => ['mime_type' => $mime, 'data' => $b64]],
                    ],
                ]
            ],
        ]);

        $data = $response->json();
        $aiMs = round((microtime(true) - $start) * 1000);

        if (!$response->ok()) {
            throw new \Exception('Gemini: ' . ($data['error']['message'] ?? $response->body()));
        }

        return [null, $aiMs, $data]; // Gemini doesn't typically return image URLs
    }
}
