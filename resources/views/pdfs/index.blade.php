@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="hero-title">
                @if(auth()->user()->isAdmin())
                    PDF Library
                @else
                    Learning Materials
                @endif
            </h1>
            <p class="hero-subtitle">
                @if(auth()->user()->isAdmin())
                    Manage and organize your PDF documents
                @else
                    Discover and download educational materials
                @endif
            </p>
            @if(auth()->user()->isAdmin())
            <div class="mt-6">
                <a href="{{ route('admin.pdfs.create') }}" class="btn-primary" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-upload"></i>
                    Upload New PDF
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
                            All PDFs
                        @else
                            Available PDFs
                        @endif
                    </h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">
                        {{ $pdfs->total() }} {{ $pdfs->total() === 1 ? 'PDF' : 'PDFs' }} available
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="Search PDFs..." class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-color: var(--border-color);">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-color: var(--border-color);">
                        <option>All Categories</option>
                        <option>Recent</option>
                        <option>Most Downloaded</option>
                    </select>
                </div>
            </div>

            @if($pdfs->count() > 0)
                <div class="video-grid">
                    @foreach($pdfs as $pdf)
                        <div class="video-card">
                            <a href="{{ route('pdfs.show', $pdf) }}" class="block">
                                <div class="relative">
                                    <!-- PDF Thumbnail/Icon -->
                                    <div class="video-thumbnail flex items-center justify-center bg-gradient-to-br from-red-100 to-red-200">
                                        <div class="text-center">
                                            <i class="fas fa-file-pdf text-red-500 text-6xl mb-2"></i>
                                            <div class="text-xs text-red-600 font-medium">PDF Document</div>
                                        </div>
                                    </div>
                                    
                                    <div class="absolute top-3 right-3">
                                        <span class="status-badge status-{{ $pdf->status }}">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            {{ ucfirst($pdf->status) }}
                                        </span>
                                    </div>
                                    
                                    @if($pdf->page_count)
                                    <div class="absolute bottom-3 right-3">
                                        <span class="bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                            {{ $pdf->page_count }} pages
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </a>
                            
                            <div class="video-info">
                                <h3 class="video-title">{{ $pdf->title }}</h3>
                                <div class="video-meta">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @if($pdf->formatted_file_size)
                                            <span class="flex items-center">
                                                <i class="fas fa-file mr-1 text-gray-400"></i>
                                                {{ $pdf->formatted_file_size }}
                                            </span>
                                        @endif
                                        @if($pdf->page_count)
                                            <span class="flex items-center">
                                                <i class="fas fa-file-alt mr-1 text-gray-400"></i>
                                                {{ $pdf->page_count }} pages
                                            </span>
                                        @endif
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                            {{ $pdf->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    @if($pdf->category)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background: {{ $pdf->category->color }}20; color: {{ $pdf->category->color }};">
                                                <i class="fas fa-folder mr-1"></i>
                                                {{ $pdf->category->name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pdfs.show', $pdf) }}" class="btn-primary text-sm py-2 px-4">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        <a href="{{ route('pdfs.download', $pdf) }}" class="btn-secondary text-sm py-2 px-4">
                                            <i class="fas fa-download mr-1"></i>
                                            Download
                                        </a>
                                        
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.pdfs.edit', $pdf) }}" class="btn-secondary text-sm py-2 px-4">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                    
                                    @if(auth()->user()->isAdmin())
                                        <button onclick="deletePdf({{ $pdf->id }})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    {{ $pdfs->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-pdf text-4xl text-red-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">No PDFs available</h3>
                    <p class="text-sm mb-6" style="color: var(--text-secondary);">
                        @if(auth()->user()->isAdmin())
                            Get started by uploading your first PDF document to the platform.
                        @else
                            Check back later for new educational materials.
                        @endif
                    </p>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.pdfs.create') }}" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Your First PDF
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function deletePdf(pdfId) {
    if (confirm('Are you sure you want to delete this PDF? This action cannot be undone.')) {
        fetch(`/admin/pdfs/${pdfId}`, {
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
                alert('Error deleting PDF: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the PDF.');
        });
    }
}
</script>
@endpush
@endsection
