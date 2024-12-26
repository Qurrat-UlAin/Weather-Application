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
        
        $geocodingResponse = Http::get("https://geocoding-api.open-meteo.com/v1/search", [
            'name' => $city,
            'count' => 1,  // Changed to 1
            'language' => 'en',
            'format' => 'json'
        ]);
    
        $geocodingData = $geocodingResponse->json();
        
        if (empty($geocodingData['results'])) {
            return response()->json(['error' => 'City not found'], 404);
        }
    
        $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $geocodingData['results'][0]['latitude'],
            'longitude' => $geocodingData['results'][0]['longitude'],
            'daily' => 'temperature_2m_max,temperature_2m_min,windspeed_10m_max,weathercode',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);
    
        $weatherData = $weatherResponse->json();
        $location = $geocodingData['results'][0];
        $cityName = sprintf("%s, %s", $location['name'], $location['country']);
    
        return response()->json([
            'city' => $cityName,
            'weather' => $weatherData,
            'coordinates' => [
                'lat' => $location['latitude'], 
                'lon' => $location['longitude']
            ]
        ]);
    }
 
    public function weatherByCoordinates(Request $request)
    {
        $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'daily' => 'temperature_2m_max,temperature_2m_min,windspeed_10m_max,weathercode',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);

        $weatherData = $weatherResponse->json();
        $cityName = sprintf("%s, %s", 
            $request->input('name'),
            $request->input('country')
        );

        RecentCity::addRecentCity($cityName, $request->input('latitude'), $request->input('longitude'));
        $isFavorite = FavoriteCity::where('name', $cityName)->exists();

        return response()->json([
            'city' => $cityName,
            'weather' => $weatherData,
            'isFavorite' => $isFavorite,
            'coordinates' => [
                'lat' => $request->input('latitude'),
                'lon' => $request->input('longitude')
            ]
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