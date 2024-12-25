<?php

namespace App\Http\Controllers;

use App\Models\RecentCity;
use App\Models\FavoriteCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        $recentCities = RecentCity::orderByDesc('view_order')->get();
        $favoriteCities = FavoriteCity::pluck('name')->toArray();
        return view('weather.index', compact('recentCities', 'favoriteCities'));
    }

    public function search(Request $request)
    {
        $city = $request->input('city');
        
        // Geocoding API call
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

        // Weather API call
        $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'temperature_2m_max,temperature_2m_min,windspeed_10m_max,weathercode',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);

        $weatherData = $weatherResponse->json();

        // Store in recent cities
        RecentCity::addRecentCity($city, $lat, $lon);

        // Check if city is favorited
        $isFavorite = FavoriteCity::where('name', $location['name'])->exists();

        return response()->json([
            'city' => $location['name'],
            'weather' => $weatherData,
            'isFavorite' => $isFavorite,
            'coordinates' => ['lat' => $lat, 'lon' => $lon]
        ]);
    }

    public function toggleFavorite(Request $request)
    {
        $result = FavoriteCity::toggleFavorite(
            $request->name,
            $request->latitude,
            $request->longitude
        );
        return response()->json($result);
    }

    public function favorites()
    {
        $favorites = FavoriteCity::orderBy('name')->get();
        return view('weather.favorites', compact('favorites'));
    }

    public function recentCities()
    {
        $recentCities = RecentCity::orderByDesc('view_order')->get();
        $favoriteCities = FavoriteCity::pluck('name')->toArray();
        return view('weather.recent', compact('recentCities', 'favoriteCities'));
    }
}