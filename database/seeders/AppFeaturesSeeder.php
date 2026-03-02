<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppFeature;
use App\Models\AppConfig;

class AppFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('✨ Seeding app features & AdMob configs...');

        // ── AI Features — EXACTLY matching preview_screen.dart IDs ───────
        // IDs: enhance, restore, face, upscale, colorize, background
        $features = [
            [
                'feature_id' => 'enhance',
                'title' => 'AI Enhance',
                'description' => 'Auto-enhance with AI',
                'icon' => 'auto_fix_high_rounded',
                'color' => '4280391411',  // Color(0xFF6200EB) purple
                'is_premium' => false,
                'enabled' => true,
                'coins' => 1,
                'sort_order' => 1,
                'benefits' => [
                    'Instantly improve photo quality',
                    'AI-powered color correction',
                    'Remove noise and blur',
                    'Works on any photo',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
            [
                'feature_id' => 'restore',
                'title' => 'Old Photo Restore',
                'description' => 'Restore old & damaged photos',
                'icon' => 'history_rounded',
                'color' => '4278242777',  // Color(0xFF018786) teal
                'is_premium' => false,
                'enabled' => true,
                'coins' => 1,
                'sort_order' => 2,
                'benefits' => [
                    'Repair scratches and tears',
                    'Fix faded vintage photos',
                    'Remove dust and artifacts',
                    'Restore lost details',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
            [
                'feature_id' => 'face',
                'title' => 'Pro Face Retouch',
                'description' => 'Studio-quality portrait enhancement',
                'icon' => 'face_retouching_natural_rounded',
                'color' => '4290825215',  // Color(0xFFFF69B4) pink
                'is_premium' => false,
                'enabled' => true,
                'coins' => 1,
                'sort_order' => 3,
                'benefits' => [
                    'AI face restoration & sharpening',
                    'Smooth skin naturally',
                    'Brighten eyes and features',
                    'Works on group photos too',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
            [
                'feature_id' => 'upscale',
                'title' => 'Upscale to HD',
                'description' => 'Upscale to 4x resolution',
                'icon' => 'high_quality_rounded',
                'color' => '4284902273',  // Color(0xFF1565C0) blue
                'is_premium' => false,
                'enabled' => true,
                'coins' => 1,
                'sort_order' => 4,
                'benefits' => [
                    'Up to 4x resolution increase',
                    'No quality loss at all',
                    'Perfect for printing',
                    'Works on tiny images',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
            [
                'feature_id' => 'colorize',
                'title' => 'Magic Colorize',
                'description' => 'Turn B&W photos into color',
                'icon' => 'palette_rounded',
                'color' => '4293951616',  // Color(0xFFFF6F00) orange
                'is_premium' => true,
                'enabled' => true,
                'coins' => 2,
                'sort_order' => 5,
                'benefits' => [
                    'AI adds realistic natural colors',
                    'Works on historical photos',
                    'Intelligent color prediction',
                    'PRO exclusive feature',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
            [
                'feature_id' => 'background',
                'title' => 'Background Fix',
                'description' => 'Remove or enhance background',
                'icon' => 'image_aspect_ratio_rounded',
                'color' => '4284153087',  // Color(0xFF0097A7) cyan
                'is_premium' => true,
                'enabled' => true,
                'coins' => 2,
                'sort_order' => 6,
                'benefits' => [
                    'Instantly remove background',
                    'Replace with any backdrop',
                    'Studio-quality cutout',
                    'PRO exclusive feature',
                ],
                'before_url' => '',
                'after_url' => '',
            ],
        ];

        foreach ($features as $feature) {
            AppFeature::updateOrCreate(
                ['feature_id' => $feature['feature_id']],
                $feature
            );
        }

        // Remove old mismatched IDs if they exist
        AppFeature::whereIn('feature_id', ['retouch'])->delete();

        // ── AdMob Configs ─────────────────────────────────────────────
        $adConfigs = [
            ['key' => 'admob_enabled', 'value' => '1', 'group' => 'ads', 'type' => 'boolean', 'label' => 'AdMob Enabled'],
            ['key' => 'admob_banner_unit_id', 'value' => '', 'group' => 'ads', 'type' => 'string', 'label' => 'Banner Ad Unit ID'],
            ['key' => 'admob_interstitial_unit_id', 'value' => '', 'group' => 'ads', 'type' => 'string', 'label' => 'Interstitial Ad Unit ID'],
            ['key' => 'admob_rewarded_unit_id', 'value' => '', 'group' => 'ads', 'type' => 'string', 'label' => 'Rewarded Ad Unit ID'],
            ['key' => 'admob_app_open_unit_id', 'value' => '', 'group' => 'ads', 'type' => 'string', 'label' => 'App Open Ad Unit ID'],
            ['key' => 'admob_native_unit_id', 'value' => '', 'group' => 'ads', 'type' => 'string', 'label' => 'Native Ad Unit ID'],
        ];

        foreach ($adConfigs as $cfg) {
            AppConfig::updateOrCreate(['key' => $cfg['key']], $cfg);
        }

        $this->command->info('✅ Seeded ' . count($features) . ' features (enhance, restore, face, upscale, colorize, background)');
        $this->command->info('   + 6 AdMob config keys');
    }
}
