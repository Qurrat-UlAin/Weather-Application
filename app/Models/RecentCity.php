<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentCity extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'view_order', 'last_viewed_at'];

    public static function addRecentCity($name, $latitude, $longitude)
    {
        $now = now();//current timestamp
        
        // If we already have this city, update its timestamp
        $existingCity = self::where('name', $name)->first();
        if ($existingCity) {
            $existingCity->update([
                'last_viewed_at' => $now,
                'updated_at' => $now
            ]);
            return;
        }

        // If we have 5 cities, remove the oldest one
        if (self::count() >= 5) {
            self::oldest('last_viewed_at')->first()->delete();
        }

        // Add new city
        self::create([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'last_viewed_at' => $now
        ]);
    }
}