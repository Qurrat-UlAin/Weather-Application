<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentCity extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'view_order'];

    public static function addRecentCity($name, $latitude, $longitude)
    {
        // Get current count
        $count = self::count();
        
        // If we already have this city, update its order to most recent
        $existingCity = self::where('name', $name)->first();
        if ($existingCity) {
            self::where('view_order', '>', $existingCity->view_order)
                ->decrement('view_order');
            $existingCity->update(['view_order' => $count - 1]);
            return;
        }

        // If we have 5 cities, remove the oldest one
        if ($count >= 5) {
            self::where('view_order', 0)->delete();
            self::decrement('view_order');
        }

        // Add new city as most recent
        self::create([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'view_order' => $count >= 5 ? 4 : $count
        ]);
    }
}