@extends('layouts.app')

@section('content')
<!-- Video Background Container -->
<div id="video-background" class="fixed inset-0 z-0">
    <video id="weather-video" class="w-full h-full object-cover" autoplay muted loop playsinline>
        <source src="/videos/sunny.mp4" type="video/mp4">
    </video>
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
</div>

<div class="max-w-3xl mx-auto glass-effect rounded-lg p-6">
    <div class="mb-6">
        <div class="flex gap-2">
            <input type="text" id="cityInput" placeholder="Enter city name" 
                   class="flex-1 p-2 rounded bg-gray-700 bg-opacity-50 text-white border border-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <button id="searchBtn" 
                    class="px-4 py-2 bg-blue-600 bg-opacity-80 rounded hover:bg-blue-700 transition duration-200">
                Search
            </button>
        </div>
    </div>

    <div id="weatherInfo" class="hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 id="cityName" class="text-2xl font-bold"></h2>
            <button id="favoriteBtn" class="text-yellow-500 hover:text-yellow-400 transition duration-200">
                <i class="wi wi-stars text-2xl"></i>
            </button>
        </div>
        
        <div id="forecast" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <!-- Forecast items will be inserted here -->
        </div>
    </div>
</div>

<style>
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }

    #video-background {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: -1;
        overflow: hidden;
    }

    #weather-video {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        object-fit: cover;
    }
</style>

<script>
// Weather code to icon mapping using weather-icons classes
const weatherIcons = {
    0: 'wi-day-sunny',           // Clear sky
    1: 'wi-day-sunny-overcast',  // Mainly clear
    2: 'wi-day-cloudy',          // Partly cloudy
    3: 'wi-cloudy',              // Overcast
    45: 'wi-fog',                // Foggy
    48: 'wi-fog',                // Depositing rime fog
    51: 'wi-sprinkle',           // Light drizzle
    53: 'wi-rain',               // Moderate drizzle
    55: 'wi-rain-wind',          // Dense drizzle
    61: 'wi-rain',               // Slight rain
    63: 'wi-rain',               // Moderate rain
    65: 'wi-rain-wind',          // Heavy rain
    71: 'wi-snow',               // Slight snow
    73: 'wi-snow',               // Moderate snow
    75: 'wi-snow-wind',          // Heavy snow
    77: 'wi-snow',               // Snow grains
    80: 'wi-showers',            // Slight rain showers
    81: 'wi-rain',               // Moderate rain showers
    82: 'wi-rain-wind',          // Violent rain showers
    85: 'wi-snow',               // Slight snow showers
    86: 'wi-snow',               // Heavy snow showers
    95: 'wi-thunderstorm',       // Thunderstorm
    96: 'wi-storm-showers',      // Thunderstorm with slight hail
    99: 'wi-storm-showers'       // Thunderstorm with heavy hail
};

const weatherVideos = {
    clear: '/videos/sunny.mp4',
    cloudy: '/videos/cloudy.mp4',
    rain: '/videos/rainy.mp4',
    snow: '/videos/snowy.mp4',
    storm: '/videos/stormy.mp4'
};

let currentCity = null;

function setWeatherVideo(weatherCode) {
    const video = document.getElementById('weather-video');
    let videoSource = '';

    if (weatherCode <= 1) videoSource = weatherVideos.clear;
    else if (weatherCode <= 3) videoSource = weatherVideos.cloudy;
    else if (weatherCode <= 69) videoSource = weatherVideos.rain;
    else if (weatherCode <= 77) videoSource = weatherVideos.snow;
    else videoSource = weatherVideos.storm;

    const videoElement = document.querySelector('#weather-video source');
    if (videoElement && videoElement.src !== videoSource) {
        videoElement.src = videoSource;
        video.load();
        video.play().catch(e => console.log('Auto-play failed:', e));
    }
}

async function toggleFavorite(name, latitude, longitude) {
    try {
        const response = await fetch('/weather/favorite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name, latitude, longitude })
        });
        
        const data = await response.json();
        const btn = document.getElementById('favoriteBtn');
        btn.classList.toggle('text-yellow-500');
        btn.classList.toggle('text-gray-400');
    } catch (error) {
        console.error('Error:', error);
    }
}

document.getElementById('searchBtn').addEventListener('click', async () => {
    const city = document.getElementById('cityInput').value.trim();
    if (!city) {
        alert('Please enter a city name');
        return;
    }

    try {
        const response = await fetch(`/weather/search?city=${encodeURIComponent(city)}`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to fetch weather data');
        }

        currentCity = {
            name: data.city,
            latitude: data.coordinates.lat,
            longitude: data.coordinates.lon
        };

        document.getElementById('weatherInfo').classList.remove('hidden');
        document.getElementById('cityName').textContent = data.city;

        // Update favorite button
        const favoriteBtn = document.getElementById('favoriteBtn');
        favoriteBtn.className = data.isFavorite ? 'text-yellow-500' : 'text-gray-400';
        favoriteBtn.onclick = () => toggleFavorite(currentCity.name, currentCity.latitude, currentCity.longitude);

        const forecastContainer = document.getElementById('forecast');
        forecastContainer.innerHTML = '';

        // Set video background based on current weather
        const currentWeatherCode = data.weather.daily.weathercode[0];
        setWeatherVideo(currentWeatherCode);

        // Process 7-day forecast
        const daily = data.weather.daily;
        for (let i = 0; i < daily.time.length; i++) {
            const date = new Date(daily.time[i]);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            const weatherCode = daily.weathercode[i];
            const iconClass = weatherIcons[weatherCode] || 'wi-na';
            
            const forecastItem = document.createElement('div');
            forecastItem.className = 'glass-card rounded-lg p-4 text-center';
            forecastItem.innerHTML = `
                <h3 class="font-bold mb-2">${dayName}</h3>
                <i class="wi ${iconClass} text-4xl mb-3"></i>
                <div class="space-y-2">
                    <p class="text-sm flex items-center justify-center">
                        <i class="wi wi-thermometer text-red-400 mr-1"></i>
                        ${daily.temperature_2m_max[i]}°C
                    </p>
                    <p class="text-sm flex items-center justify-center">
                        <i class="wi wi-thermometer-exterior text-blue-400 mr-1"></i>
                        ${daily.temperature_2m_min[i]}°C
                    </p>
                    <p class="text-sm flex items-center justify-center">
                        <i class="wi wi-strong-wind text-gray-400 mr-1"></i>
                        ${daily.windspeed_10m_max[i]} km/h
                    </p>
                </div>
            `;
            forecastContainer.appendChild(forecastItem);
        }
    } catch (error) {
        alert(error.message);
    }
});

// Initialize with default video
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('weather-video');
    if (video) {
        video.play().catch(e => console.log('Auto-play failed:', e));
    }
});
</script>
@endsection