<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /* ─── GENERAL ─── */
    public function general()
    {
        $configs = AppConfig::where('group', 'general')->get()->keyBy('key');
        return view('admin.settings.general', compact('configs'));
    }

    public function updateGeneral(Request $request)
    {
        $fields = [
            'app_name',
            'app_version',
            'support_email',
            'privacy_policy_url',
            'free_credits',
            'maintenance_mode',
            'allow_registration',
            'free_tier_enabled'
        ];

        foreach ($fields as $key) {
            $value = $request->input($key, '0');
            AppConfig::updateOrCreate(['key' => $key], [
                'value' => $value,
                'group' => 'general',
            ]);
        }

        return back()->with('success', 'General settings saved.');
    }

    /* ─── BILLING ─── */
    public function billing()
    {
        $configs = AppConfig::where('group', 'billing')->get()->keyBy('key');
        return view('admin.settings.billing', compact('configs'));
    }

    public function updateBilling(Request $request)
    {
        $plan = $request->input('_plan', 'all');

        // Fields per plan
        $planFields = [
            'monthly' => ['plan_monthly_name', 'pro_price_monthly', 'plan_monthly_subtitle', 'plan_monthly_id'],
            'yearly' => ['plan_yearly_name', 'pro_price_yearly', 'plan_yearly_subtitle', 'plan_yearly_id'],
            'lifetime' => ['plan_lifetime_name', 'premium_price', 'plan_lifetime_subtitle', 'plan_lifetime_id'],
        ];

        $fields = $planFields[$plan] ?? array_merge(...array_values($planFields));

        // Validate price field for this plan only
        $priceField = ['monthly' => 'pro_price_monthly', 'yearly' => 'pro_price_yearly', 'lifetime' => 'premium_price'];
        if (isset($priceField[$plan])) {
            $request->validate([$priceField[$plan] => 'required|numeric|min:0']);
        }


        foreach ($fields as $key) {
            if ($request->has($key)) {
                AppConfig::updateOrCreate(['key' => $key], [
                    'value' => $request->input($key, ''),
                    'group' => 'billing',
                    'type' => 'string',
                ]);
            }
        }

        $labels = ['monthly' => 'Monthly', 'yearly' => 'Yearly', 'lifetime' => 'Lifetime'];
        return back()->with('success', ($labels[$plan] ?? 'Billing') . ' plan saved successfully.');
    }

    /* ─── AI ─── */
    public function aiSettings()
    {
        $configs = AppConfig::where('group', 'ai')->get()->keyBy('key');
        return view('admin.settings.ai', compact('configs'));
    }

    public function updateAi(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:replicate,openai,gemini',
        ]);

        $fields = [
            'ai_provider',
            'replicate_api_key',
            'openai_api_key',
            'gemini_api_key',
            'replicate_model',
            'openai_model',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                AppConfig::updateOrCreate(
                    ['key' => $field],
                    ['value' => $request->input($field, ''), 'group' => 'ai']
                );
            }
        }

        return back()->with('success', 'AI provider settings saved.');
    }

    /* ─── ADS (AdMob) ─── */
    public function ads()
    {
        $configs = AppConfig::where('group', 'ads')->get()->keyBy('key');
        return view('admin.settings.ads', compact('configs'));
    }

    public function updateAds(Request $request)
    {
        $fields = [
            'admob_enabled',
            'admob_banner_unit_id',
            'admob_interstitial_unit_id',
            'admob_rewarded_unit_id',
            'admob_app_open_unit_id',
            'admob_native_unit_id',
        ];

        foreach ($fields as $key) {
            $value = $key === 'admob_enabled'
                ? ($request->boolean($key) ? '1' : '0')
                : $request->input($key, '');

            AppConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'ads']
            );
        }

        return back()->with('success', 'AdMob settings saved.');
    }
}

