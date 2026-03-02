<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use App\Models\AppConfig;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default superadmin
        AdminUser::updateOrCreate(
            ['email' => 'admin@aiphotoenhancer.com'],
            [
                'name' => 'Super Admin',
                'password' => 'admin123456',
                'role' => 'superadmin',
            ]
        );

        // Seed default app configs
        $configs = [
            // General
            ['key' => 'app_name', 'value' => 'AI Photo Enhancer', 'group' => 'general', 'type' => 'string', 'label' => 'App Name'],
            ['key' => 'app_version', 'value' => '1.0.0', 'group' => 'general', 'type' => 'string', 'label' => 'App Version'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'general', 'type' => 'boolean', 'label' => 'Maintenance Mode'],
            ['key' => 'allow_registration', 'value' => '1', 'group' => 'general', 'type' => 'boolean', 'label' => 'Allow Registration'],
            ['key' => 'free_tier_enabled', 'value' => '1', 'group' => 'general', 'type' => 'boolean', 'label' => 'Free Tier Enabled'],
            ['key' => 'free_credits', 'value' => '5', 'group' => 'general', 'type' => 'integer', 'label' => 'Free Credits on Signup'],
            ['key' => 'support_email', 'value' => 'support@example.com', 'group' => 'general', 'type' => 'string', 'label' => 'Support Email'],
            ['key' => 'privacy_policy_url', 'value' => '', 'group' => 'general', 'type' => 'string', 'label' => 'Privacy Policy URL'],

            // AI
            ['key' => 'ai_provider', 'value' => 'replicate', 'group' => 'ai', 'type' => 'string', 'label' => 'Active AI Provider'],
            ['key' => 'replicate_api_key', 'value' => '', 'group' => 'ai', 'type' => 'string', 'label' => 'Replicate API Key'],
            ['key' => 'replicate_model', 'value' => 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b', 'group' => 'ai', 'type' => 'string', 'label' => 'Replicate Model'],
            ['key' => 'openai_api_key', 'value' => '', 'group' => 'ai', 'type' => 'string', 'label' => 'OpenAI API Key'],
            ['key' => 'openai_model', 'value' => 'dall-e-3', 'group' => 'ai', 'type' => 'string', 'label' => 'OpenAI Model'],
            ['key' => 'gemini_api_key', 'value' => '', 'group' => 'ai', 'type' => 'string', 'label' => 'Gemini API Key'],

            // Billing
            ['key' => 'pro_price_monthly', 'value' => '4.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Pro Monthly Price ($)'],
            ['key' => 'pro_price_yearly', 'value' => '39.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Pro Yearly Price ($)'],
            ['key' => 'premium_price', 'value' => '9.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Premium Price ($)'],
            ['key' => 'credit_pack_small', 'value' => '0.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Credit Pack Small ($)'],
            ['key' => 'credit_pack_medium', 'value' => '2.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Credit Pack Medium ($)'],
            ['key' => 'credit_pack_large', 'value' => '6.99', 'group' => 'billing', 'type' => 'string', 'label' => 'Credit Pack Large ($)'],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'billing', 'type' => 'string', 'label' => 'Currency'],
            ['key' => 'billing_provider', 'value' => 'google_play', 'group' => 'billing', 'type' => 'string', 'label' => 'Billing Provider'],
            ['key' => 'trial_days', 'value' => '0', 'group' => 'billing', 'type' => 'integer', 'label' => 'Trial Days'],
        ];

        foreach ($configs as $config) {
            AppConfig::updateOrCreate(['key' => $config['key']], $config);
        }

        $this->command->info('✅ Admin seeder completed!');
        $this->command->info('📧 Email: admin@aiphotoenhancer.com');
        $this->command->info('🔑 Password: admin123456');
    }
}
