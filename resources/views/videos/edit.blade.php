@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Edit Video</h2>
                </div>

                <form id="editForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="title" name="title" value="{{ $video->title }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $video->description }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Video Information</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div><strong>Original File:</strong> {{ $video->original_filename }}</div>
                                <div><strong>Status:</strong> 
                                    <span class="status-badge status-{{ $video->status }}">
                                        {{ ucfirst($video->status) }}
                                    </span>
                                </div>
                                @if($video->duration)
                                    <div><strong>Duration:</strong> {{ gmdate('H:i:s', $video->duration) }}</div>
                                @endif
                                @if($video->resolution)
                                    <div><strong>Resolution:</strong> {{ $video->resolution }}</div>
                                @endif
                                @if($video->file_size)
                                    <div><strong>File Size:</strong> {{ number_format($video->file_size / 1024 / 1024, 2) }} MB</div>
                                @endif
                                <div><strong>Uploaded:</strong> {{ $video->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('videos.show', $video) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        _token: window.csrfToken,
        _method: 'PUT'
    };
    
    // Disable submit button
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    fetch('{{ route("videos.update", $video) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Video updated successfully!');
            window.location.href = '{{ route("videos.show", $video) }}';
        } else {
            alert('Update failed: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Video';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the video.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Video';
    });
});
</script>
@endpush
@endsection
