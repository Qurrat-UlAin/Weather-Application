<?php

namespace App\Http\Controllers;

use App\Models\RecentCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        $recentCities = RecentCity::orderByDesc('view_order')->get();
        return view('weather.index', compact('recentCities'));
    }

    public function search(Request $request)
    {
        $city = $request->input('city');
        
        // First, get coordinates using Geocoding API
        $geocodingResponse = Http::get("https://geocoding-api.open-meteo.com/v1/search", [
            'name' => $city,
            'count' => 1,
            'language' => 'en',
            'format' => 'json'
        ]);

        $geocodingData = $geocodingResponse->json();
        
        if (empty($geocodingData['results'])) {
            return response()->json(['error' => 'City not found'], 404);
        }

        $location = $geocodingData['results'][0];
        $lat = $location['latitude'];
        $lon = $location['longitude'];

        // Get weather data using Open-Meteo API
        $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'temperature_2m_max,temperature_2m_min,windspeed_10m_max',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);

        $weatherData = $weatherResponse->json();

        // Store in recent cities
        RecentCity::addRecentCity($city, $lat, $lon);

        return response()->json([
            'city' => $location['name'],
            'weather' => $weatherData
        ]);
    }

    public function recentCities()
    {
        $recentCities = RecentCity::orderByDesc('view_order')->get();
        return view('weather.recent', compact('recentCities'));
    }
}