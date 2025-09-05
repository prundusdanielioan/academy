@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    @if(auth()->user()->isAdmin())
                        <h2 class="text-2xl font-bold">All Videos</h2>
                        <a href="{{ route('admin.videos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Upload Video
                        </a>
                    @else
                        <h2 class="text-2xl font-bold">Available Videos</h2>
                    @endif
                </div>

                @if($videos->count() > 0)
                    <div class="video-grid">
                        @foreach($videos as $video)
                            <div class="video-card">
                                <div class="relative">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="video-thumbnail">
                                    @else
                                        <div class="video-thumbnail flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute top-2 right-2">
                                        <span class="status-badge status-{{ $video->status }}">
                                            {{ ucfirst($video->status) }}
                                        </span>
                                    </div>
                                    
                                    @if($video->isReady())
                                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                            <a href="{{ route('videos.show', $video) }}" class="bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-4 py-2 rounded-lg font-medium transition-all duration-200">
                                                Watch
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="video-info">
                                    <h3 class="video-title">{{ $video->title }}</h3>
                                    <div class="video-meta">
                                        @if($video->duration)
                                            <div>Duration: {{ gmdate('H:i:s', $video->duration) }}</div>
                                        @endif
                                        @if($video->resolution)
                                            <div>Resolution: {{ $video->resolution }}</div>
                                        @endif
                                        <div>Uploaded: {{ $video->created_at->diffForHumans() }}</div>
                                        
                                        @if($video->progress->count() > 0)
                                            @php
                                                $userProgress = $video->progress->where('user_id', auth()->id())->first();
                                            @endphp
                                            @if($userProgress)
                                                <div class="mt-2">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: {{ $userProgress->progress_percentage }}%"></div>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ number_format($userProgress->progress_percentage, 1) }}% watched
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="mt-3 flex space-x-2">
                                        @if($video->isReady())
                                            <a href="{{ route('videos.show', $video) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Watch
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.videos.edit', $video) }}" class="text-gray-600 hover:text-gray-800 text-sm">
                                                Edit
                                            </a>
                                            <button onclick="deleteVideo({{ $video->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $videos->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No videos</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by uploading your first video.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.videos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Upload Video
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteVideo(videoId) {
    if (confirm('Are you sure you want to delete this video? This action cannot be undone.')) {
        fetch(`/admin/videos/${videoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting video: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the video.');
        });
    }
}
</script>
@endpush
@endsection
