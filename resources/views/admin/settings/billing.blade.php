@extends('admin.layouts.admin')

@section('title', 'Billing Settings')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.settings.general') }}">Settings</a>
    <i class="ri-arrow-right-s-line"></i> Billing
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>Billing & Pricing</h1>
            <p>Manage subscription plans, prices, and payment provider configuration.</p>
        </div>
    </div>

    <div class="settings-tabs">
        <a href="{{ route('admin.settings.general') }}" class="settings-tab">
            <i class="ri-settings-3-line"></i> General
        </a>
        <a href="{{ route('admin.settings.billing') }}" class="settings-tab active">
            <i class="ri-money-dollar-circle-line"></i> Billing
        </a>
        <a href="{{ route('admin.settings.ai') }}" class="settings-tab">
            <i class="ri-robot-2-line"></i> AI Provider
        </a>
    </div>

    <form action="{{ route('admin.settings.billing.update') }}" method="POST">
        @csrf

        {{-- Plan pricing cards --}}
        <div class="section-card mb-3">
            <div class="section-card-header">
                <h3><i class="ri-price-tag-3-line" style="color:var(--indigo-500);font-size:16px;"></i> Subscription Pricing
                </h3>
                <p>Set prices for your subscription plans (in USD).</p>
            </div>
            <div class="section-card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;">



                    {{-- Pro --}}
                    <div style="
                                            border: 2px solid var(--indigo-500);
                                            border-radius: 12px;
                                            overflow: hidden;
                                            position: relative;
                                        ">
                        <div style="
                                                position:absolute;top:12px;right:12px;
                                                background:var(--indigo-600);color:white;
                                                font-size:10px;font-weight:700;letter-spacing:0.5px;
                                                padding:3px 8px;border-radius:99px;text-transform:uppercase;
                                            ">Popular</div>
                        <div
                            style="padding:16px 18px;background:var(--indigo-50);border-bottom:2px solid var(--indigo-100);">
                            <span class="badge badge-indigo" style="margin-bottom:8px;">Pro Plan</span>
                            <div style="font-size:26px;font-weight:800;color:var(--indigo-700);">
                                ${{ $configs->get('pro_price_monthly')?->value ?? '4.99' }}
                                <span style="font-size:14px;font-weight:500;color:var(--indigo-400);">/mo</span>
                            </div>
                            <div style="font-size:12px;color:var(--indigo-500);">or
                                ${{ $configs->get('pro_price_yearly')?->value ?? '39.99' }}/yr</div>
                        </div>
                        <div style="padding:16px 18px;">
                            <ul
                                style="list-style:none;font-size:13px;color:var(--gray-600);display:flex;flex-direction:column;gap:8px;">
                                <li><i class="ri-check-line" style="color:var(--indigo-500);margin-right:6px;"></i>
                                    Unlimited credits</li>
                                <li><i class="ri-check-line" style="color:var(--indigo-500);margin-right:6px;"></i> Priority
                                    processing</li>
                                <li><i class="ri-check-line" style="color:var(--indigo-500);margin-right:6px;"></i> All
                                    enhancement models</li>
                            </ul>
                            <div class="grid-2 mt-2" style="gap:10px;">
                                <div class="form-group">
                                    <label class="form-label">Monthly Title</label>
                                    <input type="text" name="plan_monthly_name" class="form-control"
                                        value="{{ $configs->get('plan_monthly_name')?->value ?? 'Monthly Access' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Monthly Price ($)</label>
                                    <input type="text" name="pro_price_monthly" class="form-control"
                                        value="{{ $configs->get('pro_price_monthly')?->value ?? '4.99' }}">
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Google Play ID (Monthly)</label>
                                    <input type="text" name="plan_monthly_id" class="form-control"
                                        value="{{ $configs->get('plan_monthly_id')?->value ?? 'monthly_subsc' }}"
                                        placeholder="e.g. monthly_subsc">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Yearly Title</label>
                                    <input type="text" name="plan_yearly_name" class="form-control"
                                        value="{{ $configs->get('plan_yearly_name')?->value ?? 'Yearly Access' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Yearly Price ($)</label>
                                    <input type="text" name="pro_price_yearly" class="form-control"
                                        value="{{ $configs->get('pro_price_yearly')?->value ?? '39.99' }}">
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Google Play ID (Yearly)</label>
                                    <input type="text" name="plan_yearly_id" class="form-control"
                                        value="{{ $configs->get('plan_yearly_id')?->value ?? 'truni_pro_yearly' }}"
                                        placeholder="e.g. truni_pro_yearly">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Premium --}}
                    <div style="border:1px solid var(--amber-200);border-radius:12px;overflow:hidden;">
                        <div style="padding:16px 18px;background:var(--amber-50);border-bottom:1px solid var(--amber-200);">
                            <span class="badge badge-amber" style="margin-bottom:8px;"><i class="ri-vip-crown-line"></i>
                                Premium</span>
                            <div style="font-size:26px;font-weight:800;color:var(--amber-700);">
                                ${{ $configs->get('premium_price')?->value ?? '9.99' }}
                                <span style="font-size:14px;font-weight:500;color:var(--amber-500);">/mo</span>
                            </div>
                            <div style="font-size:12px;color:var(--amber-600);">One-time or subscription</div>
                        </div>
                        <div style="padding:16px 18px;">
                            <ul
                                style="list-style:none;font-size:13px;color:var(--gray-600);display:flex;flex-direction:column;gap:8px;">
                                <li><i class="ri-check-line" style="color:var(--amber-500);margin-right:6px;"></i>
                                    Everything in Pro</li>
                                <li><i class="ri-check-line" style="color:var(--amber-500);margin-right:6px;"></i> Batch
                                    processing</li>
                                <li><i class="ri-check-line" style="color:var(--amber-500);margin-right:6px;"></i> API
                                    access</li>
                            </ul>
                            <div class="grid-2 mt-2" style="gap:10px;">
                                <div class="form-group">
                                    <label class="form-label">Lifetime Title</label>
                                    <input type="text" name="plan_lifetime_name" class="form-control"
                                        value="{{ $configs->get('plan_lifetime_name')?->value ?? 'Lifetime Access' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Price ($)</label>
                                    <input type="text" name="premium_price" class="form-control"
                                        value="{{ $configs->get('premium_price')?->value ?? '9.99' }}">
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Google Play ID (Lifetime)</label>
                                    <input type="text" name="plan_lifetime_id" class="form-control"
                                        value="{{ $configs->get('plan_lifetime_id')?->value ?? 'truni_pro_lifetime' }}"
                                        placeholder="e.g. truni_pro_lifetime">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Save Billing Settings
            </button>
        </div>
    </form>

@endsection