<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - E-Dziennik</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    {{-- @vite(['resources/css/style.css']) --}}

    <style>
        .auth-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-box {
            max-width: 450px; 
        }
        .error-text {
            color: var(--danger);
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body class="auth-page">

    <div class="login-box">
        <h1>🎓 E-Dziennik</h1>
        
        <p style="text-align: center; color: var(--text-muted); margin-bottom: 30px;">
            Zaloguj się, aby uzyskać dostęp do panelu
        </p>

        {{-- Wyświetlanie błędów logowania --}}
        @if ($errors->any())
            <div class="error-text">
                Nieprawidłowy e-mail lub hasło.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email" style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: bold;">
                Adres E-mail
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="np. jan.kowalski@szkola.pl">

            <label for="password" style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: bold;">
                Hasło
            </label>
            <input type="password" id="password" name="password" required placeholder="••••••••">

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; font-size: 14px; color: var(--text-muted); cursor: pointer;">
                    <input type="checkbox" name="remember" style="width: auto; margin-bottom: 0; margin-right: 10px;">
                    Zapamiętaj mnie
                </label>
            </div>

            <button type="submit" style="width: 100%;">
                Zaloguj się
            </button>
        </form>

        <div style="text-align: center; margin-top: 30px; font-size: 13px; color: var(--text-muted);">
            &copy; 2026 System Zarządzania Szkołą
        </div>
    </div>

</body>
</html>
