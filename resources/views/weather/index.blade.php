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
        <h2 id="cityName" class="text-2xl font-bold mb-4"></h2>
        
        <div id="forecast" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <!-- Forecast items will be inserted here -->
        </div>
    </div>
</div>

<script>
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

        document.getElementById('weatherInfo').classList.remove('hidden');
        document.getElementById('cityName').textContent = data.city;

        const forecastContainer = document.getElementById('forecast');
        forecastContainer.innerHTML = '';

        // Process 7-day forecast
        const daily = data.weather.daily;
        for (let i = 0; i < daily.time.length; i++) {
            const date = new Date(daily.time[i]);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            
            const forecastItem = document.createElement('div');
            forecastItem.className = 'bg-gray-700 p-4 rounded-lg text-center';
            forecastItem.innerHTML = `
                <h3 class="font-bold">${dayName}</h3>
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