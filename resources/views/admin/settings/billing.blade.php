@extends('admin.layouts.admin')

@section('title', 'Billing & Plans')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.settings.general') }}">Settings</a>
    <i class="ri-arrow-right-s-line"></i> Billing
@endsection

@push('styles')
    <style>
        .plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .plan-card {
            background: var(--white);
            border-radius: 16px;
            border: 2px solid var(--gray-200);
            overflow: hidden;
            transition: box-shadow 0.2s, border-color 0.2s;
            position: relative;
        }

        .plan-card:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        }

        .plan-card.monthly {
            border-color: var(--indigo-400);
        }

        .plan-card.yearly {
            border-color: var(--emerald-500);
        }

        .plan-card.lifetime {
            border-color: var(--amber-400);
        }

        .plan-card.custom {
            border-color: var(--gray-300);
            border-style: dashed;
        }

        .plan-card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .plan-card.monthly .plan-card-header {
            background: #eef2ff;
        }

        .plan-card.yearly .plan-card-header {
            background: #ecfdf5;
        }

        .plan-card.lifetime .plan-card-header {
            background: #fffbeb;
        }

        .plan-card.custom .plan-card-header {
            background: var(--gray-50);
        }

        .plan-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .monthly .plan-icon {
            background: var(--indigo-100);
            color: var(--indigo-600);
        }

        .yearly .plan-icon {
            background: var(--emerald-100);
            color: var(--emerald-600);
        }

        .lifetime .plan-icon {
            background: var(--amber-100);
            color: var(--amber-600);
        }

        .custom .plan-icon {
            background: var(--gray-200);
            color: var(--gray-500);
        }

        .plan-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-800);
        }

        .plan-subtitle {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 2px;
        }

        .plan-badge {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 9px;
            border-radius: 99px;
            text-transform: uppercase;
            position: absolute;
            top: 14px;
            right: 14px;
        }

        .monthly .plan-badge {
            background: var(--indigo-100);
            color: var(--indigo-700);
        }

        .yearly .plan-badge {
            background: var(--emerald-100);
            color: var(--emerald-700);
        }

        .lifetime .plan-badge {
            background: var(--amber-100);
            color: var(--amber-700);
        }

        .plan-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .plan-price-display {
            display: flex;
            align-items: baseline;
            gap: 4px;
            font-size: 32px;
            font-weight: 800;
        }

        .monthly .plan-price-display {
            color: var(--indigo-700);
        }

        .yearly .plan-price-display {
            color: var(--emerald-700);
        }

        .lifetime .plan-price-display {
            color: var(--amber-700);
        }

        .price-period {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.6;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .field-row.full {
            grid-template-columns: 1fr;
        }

        .plan-delete-btn {
            background: none;
            border: 1px solid var(--red-300);
            color: var(--red-500);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .plan-delete-btn:hover {
            background: var(--red-50);
            border-color: var(--red-400);
        }

        .plan-save-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.15s;
        }

        .plan-save-btn:hover {
            opacity: 0.85;
        }

        .monthly .plan-save-btn {
            background: var(--indigo-600);
            color: white;
        }

        .yearly .plan-save-btn {
            background: var(--emerald-600);
            color: white;
        }

        .lifetime .plan-save-btn {
            background: var(--amber-500);
            color: white;
        }

        .custom .plan-save-btn {
            background: var(--gray-700);
            color: white;
        }

        .add-plan-card {
            background: var(--gray-50);
            border: 2px dashed var(--gray-300);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--gray-500);
        }

        .add-plan-card:hover {
            border-color: var(--indigo-400);
            color: var(--indigo-600);
            background: #eef2ff;
        }

        .add-plan-card i {
            font-size: 28px;
            margin-bottom: 8px;
            display: block;
        }

        .add-plan-card span {
            font-size: 14px;
            font-weight: 600;
        }

        .collapse-form {
            display: none;
        }

        .collapse-form.open {
            display: block;
        }
    </style>
@endpush

@section('content')

    <div class="page-header">
        <div>
            <h1>Billing & Plans</h1>
            <p>Create, edit, and manage your subscription plans independently. Each plan is pushed live to the app
                instantly.</p>
        </div>
    </div>

    <div class="settings-tabs">
        <a href="{{ route('admin.settings.general') }}" class="settings-tab"><i class="ri-settings-3-line"></i> General</a>
        <a href="{{ route('admin.settings.billing') }}" class="settings-tab active"><i
                class="ri-money-dollar-circle-line"></i> Billing</a>
        <a href="{{ route('admin.settings.ai') }}" class="settings-tab"><i class="ri-robot-2-line"></i> AI Provider</a>
        <a href="{{ route('admin.settings.ads') }}" class="settings-tab"><i class="ri-advertisement-line"></i> Ads</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">
            <i class="ri-check-circle-line"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── PLAN CARDS ── --}}
    <div class="plan-grid">

        {{-- MONTHLY PLAN --}}
        <div class="plan-card monthly">
            <span class="plan-badge">Monthly</span>
            <div class="plan-card-header">
                <div class="plan-icon"><i class="ri-calendar-line"></i></div>
                <div>
                    <div class="plan-title">Monthly Access</div>
                    <div class="plan-subtitle">Recurring monthly subscription</div>
                </div>
            </div>
            <div class="plan-card-body">
                <div class="plan-price-display">
                    $<span id="monthly-price-display">{{ $configs->get('pro_price_monthly')?->value ?? '4.99' }}</span>
                    <span class="price-period">/month</span>
                </div>
                <form action="{{ route('admin.settings.billing.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_plan" value="monthly">
                    <div class="field-row">
                        <div class="form-group">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="plan_monthly_name" class="form-control"
                                value="{{ $configs->get('plan_monthly_name')?->value ?? 'Monthly Access' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (USD)</label>
                            <input type="text" name="pro_price_monthly" class="form-control"
                                value="{{ $configs->get('pro_price_monthly')?->value ?? '4.99' }}"
                                oninput="document.getElementById('monthly-price-display').textContent=this.value||'0'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="plan_monthly_subtitle" class="form-control"
                            value="{{ $configs->get('plan_monthly_subtitle')?->value ?? 'Billed every month' }}"
                            placeholder="e.g. Billed every month">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Google Play Product ID</label>
                        <input type="text" name="plan_monthly_id" class="form-control"
                            value="{{ $configs->get('plan_monthly_id')?->value ?? 'monthly_subsc' }}"
                            placeholder="e.g. monthly_subsc">
                        <small class="text-muted">Must match exactly your Play Console in-app product ID.</small>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="plan-save-btn"><i class="ri-save-line"></i> Save Plan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- YEARLY PLAN --}}
        <div class="plan-card yearly">
            <span class="plan-badge">⭐ Best Value</span>
            <div class="plan-card-header">
                <div class="plan-icon"><i class="ri-calendar-2-line"></i></div>
                <div>
                    <div class="plan-title">Yearly Access</div>
                    <div class="plan-subtitle">Billed once per year — save up to 60%</div>
                </div>
            </div>
            <div class="plan-card-body">
                <div class="plan-price-display">
                    $<span id="yearly-price-display">{{ $configs->get('pro_price_yearly')?->value ?? '39.99' }}</span>
                    <span class="price-period">/year</span>
                </div>
                <form action="{{ route('admin.settings.billing.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_plan" value="yearly">
                    <div class="field-row">
                        <div class="form-group">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="plan_yearly_name" class="form-control"
                                value="{{ $configs->get('plan_yearly_name')?->value ?? 'Yearly Access' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (USD)</label>
                            <input type="text" name="pro_price_yearly" class="form-control"
                                value="{{ $configs->get('pro_price_yearly')?->value ?? '39.99' }}"
                                oninput="document.getElementById('yearly-price-display').textContent=this.value||'0'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="plan_yearly_subtitle" class="form-control"
                            value="{{ $configs->get('plan_yearly_subtitle')?->value ?? 'Best Value - Save 60%' }}"
                            placeholder="e.g. Best Value - Save 60%">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Google Play Product ID</label>
                        <input type="text" name="plan_yearly_id" class="form-control"
                            value="{{ $configs->get('plan_yearly_id')?->value ?? 'truni_pro_yearly' }}"
                            placeholder="e.g. truni_pro_yearly">
                        <small class="text-muted">Must match exactly your Play Console in-app product ID.</small>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="plan-save-btn"><i class="ri-save-line"></i> Save Plan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- LIFETIME PLAN --}}
        <div class="plan-card lifetime">
            <span class="plan-badge">👑 Lifetime</span>
            <div class="plan-card-header">
                <div class="plan-icon"><i class="ri-vip-crown-line"></i></div>
                <div>
                    <div class="plan-title">Lifetime Access</div>
                    <div class="plan-subtitle">One-time payment, access forever</div>
                </div>
            </div>
            <div class="plan-card-body">
                <div class="plan-price-display">
                    $<span id="lifetime-price-display">{{ $configs->get('premium_price')?->value ?? '79.99' }}</span>
                    <span class="price-period">one-time</span>
                </div>
                <form action="{{ route('admin.settings.billing.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_plan" value="lifetime">
                    <div class="field-row">
                        <div class="form-group">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="plan_lifetime_name" class="form-control"
                                value="{{ $configs->get('plan_lifetime_name')?->value ?? 'Lifetime Access' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (USD)</label>
                            <input type="text" name="premium_price" class="form-control"
                                value="{{ $configs->get('premium_price')?->value ?? '79.99' }}"
                                oninput="document.getElementById('lifetime-price-display').textContent=this.value||'0'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="plan_lifetime_subtitle" class="form-control"
                            value="{{ $configs->get('plan_lifetime_subtitle')?->value ?? 'One-time payment' }}"
                            placeholder="e.g. One-time payment, yours forever">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Google Play Product ID</label>
                        <input type="text" name="plan_lifetime_id" class="form-control"
                            value="{{ $configs->get('plan_lifetime_id')?->value ?? 'truni_pro_lifetime' }}"
                            placeholder="e.g. truni_pro_lifetime">
                        <small class="text-muted">Must match exactly your Play Console in-app product ID.</small>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="plan-save-btn"><i class="ri-save-line"></i> Save Plan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- ── PLAN FEATURES INFO BOX ── --}}
    <div class="section-card mb-4">
        <div class="section-card-header">
            <h3><i class="ri-information-line" style="color:var(--indigo-500);font-size:16px;"></i> How Plans Work</h3>
            <p>All plans grant users "Pro" status inside the app. The difference is only how they appear on the paywall
                screen.</p>
        </div>
        <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                <div style="background:var(--gray-50);border-radius:10px;padding:14px;">
                    <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:6px;">
                        <i class="ri-smartphone-line" style="color:var(--indigo-500);"></i> Flutter App
                    </div>
                    <p style="font-size:12px;color:var(--gray-500);margin:0;">
                        The app fetches plan IDs, names, and prices from <code>/api/config</code>. Changes here go live
                        instantly after the app restarts.
                    </p>
                </div>
                <div style="background:var(--gray-50);border-radius:10px;padding:14px;">
                    <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:6px;">
                        <i class="ri-google-play-line" style="color:var(--emerald-600);"></i> Google Play
                    </div>
                    <p style="font-size:12px;color:var(--gray-500);margin:0;">
                        The <strong>Product ID</strong> must exactly match what you create in Google Play Console → Monetize
                        → Products.
                    </p>
                </div>
                <div style="background:var(--gray-50);border-radius:10px;padding:14px;">
                    <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:6px;">
                        <i class="ri-lock-line" style="color:var(--amber-600);"></i> Pro Status
                    </div>
                    <p style="font-size:12px;color:var(--gray-500);margin:0;">
                        After a successful purchase, Google Play verifies the payment. RevenueCat grants "Pro" status which
                        unlocks all premium features.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection