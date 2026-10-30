<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Comfort Aircon</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #162E5C;
            --primary-dark: #0e1e3d;
            --primary-light: #1E3A75;
            --accent: #0078BF;
            --accent-hover: #00609C;
            --accent-vibrant: #2B78E4;
            --bg-gradient: radial-gradient(circle at 80% 15%, rgba(0, 120, 191, 0.28) 0%, transparent 45%),
                radial-gradient(circle at 15% 85%, rgba(22, 46, 92, 0.65) 0%, transparent 50%),
                linear-gradient(145deg, #091528 0%, #162E5C 55%, #0c1a36 100%);
            --card-bg: #ffffff;
            --text-main: #162E5C;
            --text-body: #334155;
            --text-muted: #64748B;
            --border-color: #DCE7F0;
            --radius: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-body);
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glow Background Effects */
        body::before {
            content: '';
            position: absolute;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(0, 120, 191, 0.22) 0%, rgba(0, 0, 0, 0) 70%);
            top: -120px;
            right: -100px;
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(22, 46, 92, 0.5) 0%, rgba(0, 0, 0, 0) 70%);
            bottom: -180px;
            left: -150px;
            border-radius: 50%;
            pointer-events: none;
        }

        .login-card {
            background: var(--card-bg);
            width: 100%;
            max-width: 440px;
            border-radius: var(--radius);
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
            padding: 42px;
            position: relative;
            z-index: 10;
            animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0078BF 0%, #162E5C 100%);
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo-box {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .login-logo-img {
            height: 48px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            display: block;
        }

        .dealer-tag {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 800;
            color: #0078BF;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: #f0f7fc;
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid #dce7f0;
        }

        .pulse-dot-sm {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00b87c;
            box-shadow: 0 0 6px #00b87c;
        }

        .login-alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-weight: 600;
        }

        .login-alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .login-alert-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-main);
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .input-wrapper input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(0, 159, 227, 0.18);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-muted);
            user-select: none;
            font-weight: 600;
        }

        .checkbox-label input {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #162E5C 0%, #0078BF 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 800;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(22, 46, 92, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 120, 191, 0.4);
            background: linear-gradient(135deg, #1E3A75 0%, #008cdb 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            line-height: 1.5;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo-box">
                <img src="{{ asset('images/comfortair-logo.png') }}" alt="Comfort Aircon" class="login-logo-img">
            </div>

        </div>

        @if (session('info'))
            <div class="login-alert login-alert-info">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="login-alert login-alert-danger">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email / Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </span>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="admin@comfortaircon.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" required placeholder="••••••••••••">
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Keep me signed in</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <span>Sign In to Admin Panel</span>
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>


    </div>

</body>

</html>
