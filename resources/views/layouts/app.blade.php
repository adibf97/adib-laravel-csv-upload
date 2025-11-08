<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload App</title>
    <link rel="icon" type="image/png" href="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/1f4c4.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(90deg, #6dd5ed, #2193b0);
            min-height: 100vh;
        }
    </style>
    @yield('head')
</head>
<body>
    <main class="d-flex flex-column min-vh-100">
        <div class="flex-fill">
            @yield('content')
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
