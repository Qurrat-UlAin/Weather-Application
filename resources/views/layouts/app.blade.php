<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Main wrapper to create stacking context -->
    <div class="relative min-h-screen">
        <!-- Navbar - now part of main content flow -->
        <nav class="bg-gray-800 bg-opacity-80 p-4 sticky top-0 z-50">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="wi wi-day-sunny mr-2 text-yellow-400"></i>
                    Weather App
                </h1>
                <div>
                    <a href="{{ route('weather.index') }}" class="mr-4 hover:text-gray-300">
                        <i class="wi wi-home mr-1"></i> Home
                    </a>
                    <a href="{{ route('weather.recent') }}" class="mr-4 hover:text-gray-300">
                        <i class="wi wi-time-3 mr-1"></i> Recent
                    </a>
                    <a href="{{ route('weather.favorites') }}" class="hover:text-gray-300">
                        <i class="wi wi-stars mr-1"></i> Favorites
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="container mx-auto px-4 py-8 relative z-10 mb-16">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 bg-opacity-80 text-center p-4 absolute bottom-0 w-full z-50">
            <p>&copy; 2024 Weather App</p>
        </footer>
    </div>

    <style>
        /* Glass Effect */
        .glass-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* Ensure proper stacking for video background */
        #video-background {
            z-index: -1 !important;
        }

        /* Ensure content stays above video */
        .relative {
            position: relative;
            z-index: 1;
        }
    </style>

    <script>
        // const weatherVideos = {
        //     clear: '/videos/sunny.mp4',
        //     cloudy: '/videos/cloudy.mp4',
        //     rain: '/videos/rainy.mp4',
        //     snow: '/videos/snow.mp4',
        //     storm: '/videos/stormy.mp4'
        // };

        function setWeatherVideo(weatherCode) {
            const video = document.getElementById('weather-video');
            if (!video) return;
            
            let videoSource = '';
            if (weatherCode <= 1) videoSource = weatherVideos.clear;
            else if (weatherCode <= 3) videoSource = weatherVideos.cloudy;
            else if (weatherCode <= 69) videoSource = weatherVideos.rain;
            else if (weatherCode <= 77) videoSource = weatherVideos.snow;
            else videoSource = weatherVideos.storm;

            if (video.src !== videoSource) {
                video.src = videoSource;
                video.load();
                video.play().catch(e => console.log('Video autoplay failed:', e));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('weather-video');
            if (video) {
                video.play().catch(e => console.log('Initial video autoplay failed:', e));
            }
        });
    </script>
</body>
</html>