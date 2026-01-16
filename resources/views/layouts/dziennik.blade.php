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
    </style>
</head>
<body>

    <main class="content-wrapper">
        @yield('content')
    </main>

    <footer class="main-footer">
        <p>&copy; 2026 System Dziennika Lekcyjnego</p>
    </footer>

</body>
</html>
