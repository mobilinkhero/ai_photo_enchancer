<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppUser;
use App\Models\Photo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding test users & photos...');

        $names = [
            'Aisha Khan',
            'Mohammed Ali',
            'Fatima Malik',
            'Omar Raza',
            'Sara Ahmed',
            'James Miller',
            'Emily Johnson',
            'Liam Davis',
            'Olivia Wilson',
            'Noah Brown',
            'Emma Taylor',
            'William Anderson',
            'Ava Martinez',
            'Benjamin Garcia',
            'Sophia Lee',
            'Lucas Thomas',
            'Isabella Jackson',
            'Mason White',
            'Mia Harris',
            'Ethan Clark',
            'Charlotte Lewis',
            'Aiden Robinson',
            'Amelia Walker',
            'Henry Hall',
            'Harper Allen',
        ];

        $emails = [
            'aisha@example.com',
            'mohali@gmail.com',
            'fatima.malik@yahoo.com',
            'omar.raza@hotmail.com',
            'sara.ahmed@gmail.com',
            'james.miller@example.com',
            'emily.j@protonmail.com',
            'liam.davis@gmail.com',
            'olivia.w@icloud.com',
            'noah_brown@gmail.com',
            'emma.taylor@example.com',
            'william.a@gmail.com',
            'ava.martinez@yahoo.com',
            'ben.garcia@gmail.com',
            'sophia.lee@gmail.com',
            'lucas.t@example.com',
            'isabella.j@gmail.com',
            'mason.w@hotmail.com',
            'mia.harris@gmail.com',
            'ethan.clark@example.com',
            'charlotte.l@yahoo.com',
            'aiden.r@gmail.com',
            'amelia.w@protonmail.com',
            'henry.hall@gmail.com',
            'harper.allen@icloud.com',
        ];

        $platforms = ['android', 'android', 'android', 'ios', 'ios'];
        $subscriptions = ['free', 'free', 'free', 'pro', 'premium'];
        $providers = ['replicate', 'replicate', 'openai', 'gemini'];
        $models = [
            'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b',
            'tencentarc/gfpgan:9283608cc6b7be6b65a8e44983db012355fde4132009bf99d976b2f0896856a3',
            'dall-e-3',
            'gemini-pro-vision',
        ];
        $statuses = ['done', 'done', 'done', 'done', 'failed', 'pending', 'processing'];

        foreach ($names as $i => $name) {
            $platform = $platforms[array_rand($platforms)];
            $subscription = $subscriptions[array_rand($subscriptions)];
            $createdAt = Carbon::now()->subDays(rand(0, 90))->subHours(rand(0, 23));

            $user = AppUser::create([
                'uid' => 'uid_' . Str::random(20),
                'name' => $name,
                'email' => $emails[$i] ?? Str::lower(Str::slug($name)) . rand(10, 99) . '@example.com',
                'platform' => $platform,
                'subscription' => $subscription,
                'credits' => match ($subscription) {
                    'premium' => rand(200, 500),
                    'pro' => rand(50, 199),
                    default => rand(0, 10),
                },
                'photos_enhanced' => rand(0, 80),
                'is_banned' => (rand(1, 20) === 1),
                'subscription_expires_at' => $subscription !== 'free'
                    ? Carbon::now()->addDays(rand(5, 365))
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Generate 0–12 photos per user
            $photoCount = match ($subscription) {
                'premium' => rand(5, 12),
                'pro' => rand(2, 8),
                default => rand(0, 3),
            };

            for ($p = 0; $p < $photoCount; $p++) {
                $status = $statuses[array_rand($statuses)];
                $provider = $providers[array_rand($providers)];
                $photoDate = (clone $createdAt)->addDays(rand(0, 30));

                Photo::create([
                    'user_uid' => $user->uid,
                    'original_path' => 'uploads/original/' . Str::uuid() . '.jpg',
                    'enhanced_path' => $status === 'done' ? 'uploads/enhanced/' . Str::uuid() . '.jpg' : null,
                    'provider' => $provider,
                    'model' => $models[array_rand($models)],
                    'status' => $status,
                    'error_message' => $status === 'failed'
                        ? collect(['Rate limit exceeded', 'Invalid image format', 'Model timeout', 'API quota exceeded', 'Connection refused'])->random()
                        : null,
                    'processing_time' => $status === 'done' ? round(rand(8, 120) / 10, 2) : null,
                    'created_at' => $photoDate,
                    'updated_at' => $photoDate,
                ]);
            }
        }

        $total = AppUser::count();
        $totalPhotos = Photo::count();
        $failed = Photo::where('status', 'failed')->count();

        $this->command->info("✅ Created {$total} users and {$totalPhotos} photos ({$failed} failed).");
    }
}
