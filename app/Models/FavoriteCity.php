<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteCity extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];

    public static function toggleFavorite($name, $latitude, $longitude)
    {
        $favorite = self::where('name', $name)->first();
        
        if ($favorite) {
            $favorite->delete();
            return ['status' => 'removed'];
        }

        self::create([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
        
        return ['status' => 'added'];
    }
}