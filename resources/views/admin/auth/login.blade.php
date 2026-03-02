<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — AI Photo Enhancer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0d0f14;
            --bg-surface: #13161e;
            --bg-card: #1a1e2a;
            --primary: #6c63ff;
            --primary-glow: rgba(108, 99, 255, 0.3);
            --accent: #00d4aa;
            --danger: #ff4d6d;
            --border: rgba(255, 255, 255, 0.07);
            --text: #f0f2f8;
            --text-muted: #55607a;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            animation: floatGlow 8s ease-in-out infinite;
            pointer-events: none;
        }

        .bg-glow-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(108, 99, 255, 0.15), transparent);
            top: -100px;
            left: -100px;
        }

        .bg-glow-2 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.12), transparent);
            bottom: -100px;
            right: -50px;
            animation-delay: -4s;
        }

        @keyframes floatGlow {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, 20px) scale(1.05);
            }
        }

        /* Grid pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(108, 99, 255, 0.1);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 20px 20px 0 0;
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 36px;
        }

        .login-logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary), #00d4aa);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 8px 24px var(--primary-glow);
        }

        .login-logo-text h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
        }

        .login-logo-text p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #8892a4;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 44px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .alert-danger {
            background: rgba(255, 77, 109, 0.1);
            border: 1px solid rgba(255, 77, 109, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            color: #ff4d6d;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary), #4e47cc);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.25s;
            box-shadow: 0 6px 20px var(--primary-glow);
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px var(--primary-glow);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .login-footer span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: rgba(6, 214, 160, 0.1);
            border: 1px solid rgba(6, 214, 160, 0.2);
            border-radius: 99px;
            color: #06d6a0;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon">✨</div>
            <div class="login-logo-text">
                <h1>AI Photo Enhancer</h1>
                <p>Admin Control Panel</p>
            </div>
        </div>

        <p class="login-subtitle">
            Sign in to your admin account to manage users, photos, and AI provider settings.
        </p>

        @if($errors->any())
            <div class="alert-danger">
                <i class="ri-error-warning-line"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-danger">
                <i class="ri-error-warning-line"></i>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" id="login-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="ri-mail-line input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com"
                        value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class="ri-lock-line input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="toggle-pass" onclick="togglePassword()">
                        <i class="ri-eye-line" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="login-btn">
                <i class="ri-login-box-line"></i>
                Sign In to Admin Panel
            </button>
        </form>

        <div class="login-footer">
            <span class="security-badge">
                <i class="ri-shield-check-line"></i>
                Secure Session
            </span>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'ri-eye-off-line';
            } else {
                pwd.type = 'password';
                icon.className = 'ri-eye-line';
            }
        }

        // Add loading state
        document.getElementById('login-form').addEventListener('submit', function () {
            const btn = document.getElementById('login-btn');
            btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite"></i> Signing in...';
            btn.disabled = true;
        });
    </script>
    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</body>

</html>