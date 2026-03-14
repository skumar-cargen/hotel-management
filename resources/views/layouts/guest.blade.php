<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --auth-accent: #3b82f6;
            --auth-accent-hover: #2563eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #0f172a 0%, #1a2744 40%, #1e40af 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glow effects */
        body::before {
            content: '';
            position: fixed;
            top: -25%;
            right: -15%;
            width: 55%;
            height: 70%;
            background: radial-gradient(ellipse, rgba(59, 130, 246, 0.14) 0%, transparent 70%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -10%;
            left: -10%;
            width: 45%;
            height: 45%;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ===== DESKTOP: Two-column centered ===== */
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2.5rem;
            gap: 4rem;
            position: relative;
            z-index: 1;
        }

        /* Left: Branding */
        .auth-brand-panel {
            max-width: 400px;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 68px;
            height: 68px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 1.125rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand-logo i { font-size: 2.25rem; color: #60a5fa; }

        .brand-content h1 {
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 0.6rem;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        .brand-content > p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.6;
            margin-bottom: 2.25rem;
        }

        .brand-features { width: 100%; }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .brand-feature:last-child { border-bottom: none; }

        .brand-feature-icon {
            width: 34px;
            height: 34px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-feature-icon i { font-size: 1rem; color: #93c5fd; }
        .brand-feature span { font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); }

        /* Right: Form Card */
        .auth-form-panel {
            background: #fff;
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.06);
            width: 100%;
            max-width: 400px;
            flex-shrink: 0;
        }

        .auth-form-container { width: 100%; }

        /* Mobile Brand Header (hidden on desktop) */
        .auth-mobile-header { display: none; }

        /* ===== Form Styles ===== */
        .auth-form-header { margin-bottom: 1.75rem; }

        .auth-form-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .auth-form-header p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .auth-form .form-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .auth-form .form-control {
            padding: 0.625rem 0.8rem;
            border-radius: 0.5rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background: #fff;
        }

        .auth-form .form-control:focus {
            border-color: var(--auth-accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .auth-form .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            color: #94a3b8;
            border-radius: 0.5rem 0 0 0.5rem;
        }

        .auth-form .input-group .form-control {
            border-left: none;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        .auth-form .input-group:focus-within .input-group-text,
        .auth-form .input-group:focus-within .form-control {
            border-color: var(--auth-accent);
        }

        .btn-auth-primary {
            background: var(--auth-accent);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #fff;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-auth-primary:hover {
            background: var(--auth-accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
            color: #fff;
        }

        .btn-auth-primary .btn-loader {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-auth-primary:disabled {
            cursor: not-allowed;
            transform: none;
        }

        .auth-form .form-check-input:checked {
            background-color: var(--auth-accent);
            border-color: var(--auth-accent);
        }

        .auth-alert {
            border-radius: 0.5rem;
            font-size: 0.85rem;
            padding: 0.65rem 0.875rem;
            margin-bottom: 1rem;
        }

        .auth-card-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .auth-card-footer i { margin-right: 0.2rem; }

        /* ===== TABLET & MOBILE (< 992px) ===== */
        @media (max-width: 991.98px) {
            .auth-brand-panel { display: none; }

            .auth-wrapper {
                flex-direction: column;
                justify-content: center;
                padding: 2rem;
                gap: 0;
            }

            .auth-mobile-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                margin-bottom: 1.75rem;
                color: #fff;
            }

            .auth-mobile-header .mobile-logo {
                width: 58px;
                height: 58px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1rem;
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            }

            .auth-mobile-header .mobile-logo i {
                font-size: 1.75rem;
                color: #60a5fa;
            }

            .auth-mobile-header h1 {
                font-size: 1.5rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                margin-bottom: 0.3rem;
            }

            .auth-mobile-header p {
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.5);
            }

            .auth-form-panel {
                max-width: 400px;
                padding: 2rem 1.75rem;
            }
        }

        /* ===== MOBILE (< 576px) ===== */
        @media (max-width: 575.98px) {
            .auth-wrapper {
                padding: 1.25rem;
                padding-top: 3rem;
                justify-content: flex-start;
            }

            .auth-mobile-header { margin-bottom: 1.5rem; }

            .auth-mobile-header .mobile-logo {
                width: 50px;
                height: 50px;
                border-radius: 0.875rem;
                margin-bottom: 0.875rem;
            }

            .auth-mobile-header .mobile-logo i { font-size: 1.5rem; }
            .auth-mobile-header h1 { font-size: 1.3rem; }
            .auth-mobile-header p { font-size: 0.8rem; }

            .auth-form-panel {
                padding: 1.75rem 1.5rem;
                border-radius: 1rem;
            }

            .auth-form-header { margin-bottom: 1.25rem; }
            .auth-form-header h2 { font-size: 1.25rem; }
            .auth-form-header p { font-size: 0.85rem; }
        }

        /* ===== Small phones (< 380px) ===== */
        @media (max-width: 379.98px) {
            .auth-wrapper { padding: 1rem; padding-top: 2.5rem; }

            .auth-mobile-header .mobile-logo {
                width: 44px;
                height: 44px;
                margin-bottom: 0.75rem;
            }

            .auth-mobile-header .mobile-logo i { font-size: 1.35rem; }
            .auth-mobile-header h1 { font-size: 1.15rem; }

            .auth-form-panel { padding: 1.5rem 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Desktop: Left Branding -->
        <div class="auth-brand-panel">
            <div class="brand-content">
                <div class="brand-logo">
                    <i class='bx bxs-building-house'></i>
                </div>
                <h1>Dubai Apartments</h1>
                <p>Manage your hotel listings, bookings, and domains from one powerful admin panel.</p>
                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class='bx bx-globe'></i></div>
                        <span>Manage 70+ hotel listing domains</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class='bx bx-hotel'></i></div>
                        <span>Top 20 hotels per location</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class='bx bx-credit-card'></i></div>
                        <span>Direct booking with Mashreq payment</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
                        <span>Real-time analytics & SEO tools</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Top Brand Header -->
        <div class="auth-mobile-header">
            <div class="mobile-logo">
                <i class='bx bxs-building-house'></i>
            </div>
            <h1>Dubai Apartments</h1>
            <p>Admin Control Panel</p>
        </div>

        <!-- Form Card -->
        <div class="auth-form-panel">
            <div class="auth-form-container">
                {{ $slot }}

                <div class="auth-card-footer">
                    <i class='bx bx-lock-alt'></i> Secure admin access only
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
