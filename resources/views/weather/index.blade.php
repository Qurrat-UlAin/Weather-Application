@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-gray-800 rounded-lg p-6 shadow-lg">
    <div class="mb-6">
        <div class="flex gap-2">
            <input type="text" id="cityInput" placeholder="Enter city name" 
                   class="flex-1 p-2 rounded bg-gray-700 text-white">
            <button id="searchBtn" 
                    class="px-4 py-2 bg-blue-600 rounded hover:bg-blue-700">Search</button>
        </div>
    </div>

    <div id="weatherInfo" class="hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 id="cityName" class="text-2xl font-bold"></h2>
            <button id="favoriteBtn" class="text-yellow-500 hover:text-yellow-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </button>
        </div>
        
        <div id="forecast" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <!-- Forecast items will be inserted here -->
        </div>
    </div>
</div>

<script>
// Weather code to icon mapping
const weatherIcons = {
    0: '☀️',  // Clear sky
    1: '🌤️',  // Mainly clear
    2: '⛅',  // Partly cloudy
    3: '☁️',  // Overcast
    45: '🌫️', // Foggy
    48: '🌫️', // Depositing rime fog
    51: '🌧️', // Light drizzle
    53: '🌧️', // Moderate drizzle
    55: '🌧️', // Dense drizzle
    61: '🌧️', // Slight rain
    63: '🌧️', // Moderate rain
    65: '🌧️', // Heavy rain
    71: '🌨️', // Slight snow
    73: '🌨️', // Moderate snow
    75: '🌨️', // Heavy snow
    77: '🌨️', // Snow grains
    80: '🌧️', // Slight rain showers
    81: '🌧️', // Moderate rain showers
    82: '🌧️', // Violent rain showers
    85: '🌨️', // Slight snow showers
    86: '🌨️', // Heavy snow showers
    95: '⛈️', // Thunderstorm
    96: '⛈️', // Thunderstorm with slight hail
    99: '⛈️'  // Thunderstorm with heavy hail
};

let currentCity = null;

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
        btn.querySelector('svg').style.fill = data.status === 'added' ? 'currentColor' : 'none';
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
        favoriteBtn.querySelector('svg').style.fill = data.isFavorite ? 'currentColor' : 'none';
        favoriteBtn.onclick = () => toggleFavorite(currentCity.name, currentCity.latitude, currentCity.longitude);

        const forecastContainer = document.getElementById('forecast');
        forecastContainer.innerHTML = '';

        // Process 7-day forecast
        const daily = data.weather.daily;
        for (let i = 0; i < daily.time.length; i++) {
            const date = new Date(daily.time[i]);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            const weatherCode = daily.weathercode[i];
            const weatherIcon = weatherIcons[weatherCode] || '❓';
            
            const forecastItem = document.createElement('div');
            forecastItem.className = 'bg-gray-700 p-4 rounded-lg text-center';
            forecastItem.innerHTML = `
                <h3 class="font-bold">${dayName}</h3>
                <div class="text-4xl my-2">${weatherIcon}</div>
                <p class="text-sm">High: ${daily.temperature_2m_max[i]}°C</p>
                <p class="text-sm">Low: ${daily.temperature_2m_min[i]}°C</p>
                <p class="text-sm">Wind: ${daily.windspeed_10m_max[i]} km/h</p>
            `;
            forecastContainer.appendChild(forecastItem);
        }
    } catch (error) {
        alert(error.message);
    }
});
</script>
@endsection