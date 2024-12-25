@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">Favorite Cities</h2>

    <div class="grid gap-4">
        @forelse ($favorites as $city)
            <div class="bg-gray-800 rounded-lg p-4 shadow-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold">{{ $city->name }}</h3>
                    <button 
                        class="favorite-btn text-yellow-500 hover:text-yellow-400"
                        data-city="{{ $city->name }}"
                        data-lat="{{ $city->latitude }}"
                        data-lon="{{ $city->longitude }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-400">Added: {{ $city->created_at->diffForHumans() }}</p>
                <a href="{{ route('weather.index', ['city' => $city->name]) }}" 
                   class="text-blue-400 hover:text-blue-300">
                    View weather
                </a>
            </div>
        @empty
            <p class="text-gray-400">No favorite cities yet.</p>
        @endforelse
    </div>
</div>

<script>
document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', async function() {
        const name = this.dataset.city;
        const latitude = this.dataset.lat;
        const longitude = this.dataset.lon;
        
        try {
            const response = await fetch('/weather/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name, latitude, longitude })
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endsection