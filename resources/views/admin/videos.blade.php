@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Videos</h1>
        <a href="{{ route('admin.videos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Upload New Video
        </a>
    </div>

    @if($videos->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($videos as $video)
                    <li>
                        <div class="px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    @if($video->thumbnail_path)
                                        <img class="h-16 w-16 object-cover rounded" src="{{ Storage::disk('public')->url($video->thumbnail_path) }}" alt="{{ $video->title }}">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('videos.show', $video) }}" class="hover:text-blue-600">
                                            {{ $video->title }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Uploaded by: {{ $video->user->name }} ({{ $video->user->email }})
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $video->original_filename }} • {{ number_format($video->file_size / 1024 / 1024, 2) }} MB
                                    </div>
                                    @if($video->category)
                                        <div class="text-sm text-gray-500">
                                            Category: {{ $video->category->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($video->status === 'completed') bg-green-100 text-green-800
                                    @elseif($video->status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($video->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($video->status) }}
                                </span>
                                <a href="{{ route('admin.videos.edit', $video) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this video?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
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
            <p class="mt-1 text-sm text-gray-500">Get started by uploading a new video.</p>
            <div class="mt-6">
                <a href="{{ route('admin.videos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Upload Video
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
