@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="hero-title">
                @if(auth()->user()->isAdmin())
                    Video Library
                @else
                    Learning Videos
                @endif
            </h1>
            <p class="hero-subtitle">
                @if(auth()->user()->isAdmin())
                    Manage and organize your video content
                @else
                    Discover and watch educational content
                @endif
            </p>
            @if(auth()->user()->isAdmin())
            <div class="mt-6">
                <a href="{{ route('admin.videos.create') }}" class="btn-primary" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-upload"></i>
                    Upload New Video
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="card">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold" style="color: var(--text-primary);">
                        @if(auth()->user()->isAdmin())
                            All Videos
                        @else
                            Available Videos
                        @endif
                    </h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">
                        {{ $videos->total() }} {{ $videos->total() === 1 ? 'video' : 'videos' }} available
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="Search videos..." class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-color: var(--border-color);">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-color: var(--border-color);">
                        <option>All Categories</option>
                        <option>Recent</option>
                        <option>Most Watched</option>
                    </select>
                </div>
            </div>

            @if($videos->count() > 0)
                <div class="video-grid">
                    @foreach($videos as $video)
                        <div class="video-card">
                            @if($video->isReady())
                                <a href="{{ route('videos.show', $video) }}" class="block">
                            @endif
                            <div class="relative">
                                @if($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="video-thumbnail">
                                    @if($video->isReady())
                                        <!-- Play button overlay -->
                                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300">
                                            <div class="bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full p-4 shadow-xl transition-all duration-200 transform hover:scale-110">
                                                <i class="fas fa-play text-gray-800 text-xl"></i>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="video-thumbnail flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                        <i class="fas fa-video text-gray-400 text-4xl"></i>
                                        @if($video->isReady())
                                            <!-- Play button for videos without thumbnails -->
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-full p-4 shadow-xl transform hover:scale-110 transition-all duration-200">
                                                    <i class="fas fa-play text-white text-xl"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="absolute top-3 right-3">
                                    <span class="status-badge status-{{ $video->status }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ ucfirst($video->status) }}
                                    </span>
                                </div>
                                
                                @if($video->duration)
                                <div class="absolute bottom-3 right-3">
                                    <span class="bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                        {{ gmdate('H:i:s', $video->duration) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @if($video->isReady())
                                </a>
                            @endif
                            
                            <div class="video-info">
                                <h3 class="video-title">{{ $video->title }}</h3>
                                <div class="video-meta">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @if($video->duration)
                                            <span class="flex items-center">
                                                <i class="fas fa-clock mr-1 text-gray-400"></i>
                                                {{ gmdate('H:i:s', $video->duration) }}
                                            </span>
                                        @endif
                                        @if($video->resolution)
                                            <span class="flex items-center">
                                                <i class="fas fa-video mr-1 text-gray-400"></i>
                                                {{ $video->resolution }}
                                            </span>
                                        @endif
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                            {{ $video->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    @if($video->progress->count() > 0)
                                        @php
                                            $userProgress = $video->progress->where('user_id', auth()->id())->first();
                                        @endphp
                                        @if($userProgress)
                                            <div class="mt-3">
                                                <div class="flex items-center justify-between text-sm mb-1">
                                                    <span style="color: var(--text-secondary);">Progress</span>
                                                    <span style="color: var(--text-secondary);">{{ number_format($userProgress->progress_percentage, 1) }}%</span>
                                                </div>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: {{ $userProgress->progress_percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        @if($video->isReady())
                                            <a href="{{ route('videos.show', $video) }}" class="btn-primary text-sm py-2 px-4">
                                                <i class="fas fa-play mr-1"></i>
                                                Watch
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.videos.edit', $video) }}" class="btn-secondary text-sm py-2 px-4">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                    
                                    @if(auth()->user()->isAdmin())
                                        <button onclick="deleteVideo({{ $video->id }})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    {{ $videos->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-video text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">No videos available</h3>
                    <p class="text-sm mb-6" style="color: var(--text-secondary);">
                        @if(auth()->user()->isAdmin())
                            Get started by uploading your first video to the platform.
                        @else
                            Check back later for new educational content.
                        @endif
                    </p>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.videos.create') }}" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Your First Video
                    </a>
                    @endif
                </div>
            @endif
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
