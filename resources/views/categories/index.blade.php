@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="hero-title">Browse by Category</h1>
            <p class="hero-subtitle">Explore videos organized by topic and subject matter</p>
        </div>
    </div>
</div>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($categories as $category)
                    <div class="card group cursor-pointer" onclick="window.location.href='{{ route('categories.show', $category) }}'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($category->color)
                                    <div class="w-5 h-5 rounded-full mr-3 shadow-sm" style="background-color: {{ $category->color }}"></div>
                                @else
                                    <div class="w-5 h-5 rounded-full mr-3 bg-gradient-to-br from-blue-500 to-purple-600"></div>
                                @endif
                                <h3 class="text-xl font-semibold" style="color: var(--text-primary);">{{ $category->name }}</h3>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <i class="fas fa-arrow-right text-blue-500"></i>
                            </div>
                        </div>
                        
                        @if($category->description)
                            <p class="mb-6" style="color: var(--text-secondary); line-height: 1.6;">{{ $category->description }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-video text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">
                                    {{ $category->videos_count }} {{ \Illuminate\Support\Str::plural('video', $category->videos_count) }}
                                </span>
                            </div>
                            <a href="{{ route('categories.show', $category) }}" 
                               class="btn-primary text-sm py-2 px-4">
                                <i class="fas fa-eye mr-1"></i>
                                Browse
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-folder text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">No categories available</h3>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">
                    Categories will appear here once they are created by administrators.
                </p>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.categories.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Create Category
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
