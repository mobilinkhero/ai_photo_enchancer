<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiLog;
use App\Models\AppUser;
use App\Models\Photo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiLogSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔌 Seeding API logs...');

        $users = AppUser::pluck('uid')->toArray();
        $photos = Photo::pluck('id')->toArray();

        $providers = ['replicate', 'openai', 'gemini'];

        $aiEndpoints = [
            'replicate' => 'https://api.replicate.com/v1/predictions',
            'openai' => 'https://api.openai.com/v1/images/edits',
            'gemini' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent',
        ];

        $aiModels = [
            'replicate' => 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b',
            'openai' => 'dall-e-3',
            'gemini' => 'gemini-pro-vision',
        ];

        $clientHeaders = [
            'user-agent' => ['AI Photo Enhancer/1.0 (Android)'],
            'content-type' => ['multipart/form-data'],
            'accept' => ['application/json'],
            'x-app-version' => ['1.2.4'],
        ];

        $errorMessages = [
            'Rate limit exceeded. Please wait before retrying.',
            'Invalid image format. Only JPEG and PNG are supported.',
            'Model is currently loading, please retry after a few seconds.',
            'API quota exceeded for this billing period.',
            'Connection timed out after 30 seconds.',
            'Image dimensions too large. Maximum 4096x4096 pixels.',
        ];

        $statuses = ['success', 'success', 'success', 'success', 'success', 'error', 'timeout'];

        for ($i = 0; $i < 60; $i++) {
            $provider = $providers[array_rand($providers)];
            $status = $statuses[array_rand($statuses)];
            $userUid = !empty($users) ? $users[array_rand($users)] : null;
            $photoId = !empty($photos) ? $photos[array_rand($photos)] : null;
            $isSuccess = $status === 'success';
            $isTimeout = $status === 'timeout';
            $aiTime = $isSuccess ? rand(2000, 18000) : ($isTimeout ? 30000 : rand(200, 2000));
            $totalTime = $aiTime + rand(50, 500);
            $createdAt = Carbon::now()->subDays(rand(0, 14))->subMinutes(rand(0, 1440));

            $requestPayload = [
                'image_url' => 'https://storage.example.com/uploads/original/' . Str::uuid() . '.jpg',
                'user_uid' => $userUid,
                'mode' => 'enhance',
                'scale' => rand(2, 4),
            ];

            $aiPayload = match ($provider) {
                'replicate' => [
                    'version' => $aiModels[$provider],
                    'input' => [
                        'image' => $requestPayload['image_url'],
                        'scale' => $requestPayload['scale'],
                        'face_enhance' => true,
                    ],
                ],
                'openai' => [
                    'model' => $aiModels[$provider],
                    'prompt' => 'Enhance and upscale this photo while preserving natural details.',
                    'size' => '1024x1024',
                    'n' => 1,
                ],
                'gemini' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Enhance this image quality'],
                                ['image_url' => ['url' => $requestPayload['image_url']]],
                            ]
                        ]
                    ],
                ],
            };

            $outputUrl = $isSuccess ? 'https://storage.example.com/uploads/enhanced/' . Str::uuid() . '.jpg' : null;
            $httpStatus = $isSuccess ? 200 : ($isTimeout ? 408 : collect([400, 422, 429, 500, 503])->random());

            $responseBody = $isSuccess ? match ($provider) {
                'replicate' => [
                    'id' => 'pred_' . Str::random(20),
                    'status' => 'succeeded',
                    'output' => [$outputUrl],
                    'metrics' => ['predict_time' => round($aiTime / 1000, 3)],
                    'urls' => ['get' => 'https://api.replicate.com/v1/predictions/pred_xxx'],
                ],
                'openai' => [
                    'created' => now()->timestamp,
                    'data' => [['url' => $outputUrl, 'revised_prompt' => 'Enhanced photo']],
                    'usage' => ['prompt_tokens' => 12, 'total_tokens' => 12],
                ],
                'gemini' => [
                    'candidates' => [['content' => ['parts' => [['image_url' => $outputUrl]], 'role' => 'model']]],
                    'usageMetadata' => ['promptTokenCount' => 10],
                ],
            } : [
                'error' => $isTimeout ? 'Request timeout' : 'Request failed',
                'message' => $isTimeout ? 'Connection timed out after 30s' : $errorMessages[array_rand($errorMessages)],
                'status' => $httpStatus,
            ];

            ApiLog::create([
                'request_id' => Str::uuid(),
                'client_ip' => '192.168.1.' . rand(1, 254),
                'client_endpoint' => 'api/enhance',
                'client_method' => 'POST',
                'client_headers' => $clientHeaders,
                'client_payload' => $requestPayload,
                'ai_provider' => $provider,
                'ai_endpoint' => $aiEndpoints[$provider],
                'ai_request_payload' => $aiPayload,
                'ai_request_size_bytes' => strlen(json_encode($aiPayload)),
                'ai_response_status' => $httpStatus,
                'ai_response_body' => $responseBody,
                'ai_response_time_ms' => $aiTime,
                'ai_model' => $aiModels[$provider],
                'ai_output_url' => $outputUrl,
                'status' => $status,
                'error_message' => !$isSuccess ? ($responseBody['message'] ?? 'Unknown error') : null,
                'total_time_ms' => $totalTime,
                'user_uid' => $userUid,
                'photo_id' => $photoId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('✅ Created 60 API log entries.');
    }
}
