@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <div class="flex-grow max-w-3xl mx-auto w-full">
        <h2 class="text-2xl font-bold mb-6">Recently Viewed Cities</h2>
        
        <div class="grid gap-4 mb-8">
            @forelse ($recentCities as $city)
                <div class="bg-gray-800 rounded-lg p-4 shadow-lg">
                    <h3 class="text-xl font-semibold">{{ $city->name }}</h3>
                    <p class="text-gray-400">Last viewed: {{ $city->updated_at->diffForHumans() }}</p>
                    <a href="{{ route('weather.index', ['city' => $city->name]) }}"
                        class="text-blue-400 hover:text-blue-300">
                        View weather
                    </a>
                </div>
            @empty
                <p class="text-gray-400">No cities viewed yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection