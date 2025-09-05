<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Video Progress') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($progress->count() > 0)
                        <div class="grid gap-6">
                            @foreach($progress as $item)
                                <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-start space-x-4">
                                        @if($item->video->thumbnail_url)
                                            <img src="{{ $item->video->thumbnail_url }}" 
                                                 alt="{{ $item->video->title }}" 
                                                 class="w-24 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                <a href="{{ route('videos.show', $item->video) }}" 
                                                   class="hover:text-blue-600 transition-colors">
                                                    {{ $item->video->title }}
                                                </a>
                                            </h3>
                                            
                                            @if($item->video->description)
                                                <p class="text-gray-600 mb-3">{{ $item->video->description }}</p>
                                            @endif
                                            
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-gray-500">Progress</span>
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ number_format($item->progress_percentage, 1) }}%
                                                    </span>
                                                </div>
                                                
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                                         style="width: {{ $item->progress_percentage }}%"></div>
                                                </div>
                                                
                                                <div class="flex items-center justify-between text-sm text-gray-500">
                                                    <span>{{ gmdate('H:i:s', $item->current_time) }} / {{ gmdate('H:i:s', $item->total_time) }}</span>
                                                    <span>
                                                        @if($item->is_completed)
                                                            <span class="text-green-600 font-medium">✓ Completed</span>
                                                        @else
                                                            Last watched: {{ $item->last_watched_at->diffForHumans() }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No progress yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Start watching videos to track your progress.</p>
                            <div class="mt-6">
                                <a href="{{ route('videos.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Browse Videos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
