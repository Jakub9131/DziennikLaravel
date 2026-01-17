<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dziennik Elektroniczny')</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .content-wrapper {
            flex: 1;
            width: 100%;
        }
        .main-footer {
            text-align: left;
            padding: 20px;
            color: rgba(0,0,0,0.5);
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            margin: 20px auto;
            max-width: 1100px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-left: 5px solid;
            background: white;
            animation: fadeIn 0.3s ease;
        }
        .alert-success {
            border-color: var(--primary-color);
            color: #2d5a27;
            background-color: #f0fff4;
        }
        .alert-danger {
            border-color: var(--danger);
            color: #a94442;
            background-color: #fff5f5;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <main class="content-wrapper">
        {{-- SEKCJA KOMUNIKATÓW I WALIDACJI --}}
        <div class="container" style="padding: 0 20px;">
            {{-- Sukces (np. dodano ocenę) --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Błędy walidacji (np. błędny format maila) --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong style="display: block; margin-bottom: 5px;">
                        <i class="fas fa-exclamation-triangle"></i> Wystąpiły błędy:
                    </strong>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="main-footer">
        <p>&copy; 2026 System Dziennika Lekcyjnego</p>
    </footer>

</body>
</html>
