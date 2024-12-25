<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Weather App</h1>
            <div>
                <a href="{{ route('weather.index') }}" class="mr-4 hover:text-gray-300">Home</a>
                <a href="{{ route('weather.recent') }}" class="hover:text-gray-300">Recent Cities</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-center p-4 fixed bottom-0 w-full">
        <p>&copy; 2024 Weather App</p>
    </footer>
</body>
</html>