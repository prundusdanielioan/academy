@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Upload Video</h2>
                    <p class="text-gray-600 mt-2">Upload your video file. Supported formats: MP4, AVI, MOV, WMV, FLV, WEBM (max 100MB)</p>
                </div>

                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="title" name="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter video title">
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter video description"></textarea>
                    </div>

                    <div class="mb-6">
                        <label for="video" class="block text-sm font-medium text-gray-700 mb-2">Video File</label>
                        
                        <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                            <div id="dropZoneContent">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <label for="video" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload a file</span>
                                        <input id="video" name="video" type="file" class="sr-only" accept="video/*" required>
                                    </label>
                                    or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">MP4, AVI, MOV, WMV, FLV, WEBM up to 100MB</p>
                            </div>
                            
                            <div id="fileInfo" class="hidden mt-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span id="fileName" class="text-sm font-medium text-gray-900"></span>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="uploadProgress" class="hidden mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Uploading...</span>
                            <span id="progressPercent" class="text-sm text-gray-500">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('videos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Upload Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedFile = null;

// File input change handler
document.getElementById('video').addEventListener('change', function(e) {
    handleFileSelect(e.target.files[0]);
});

// Drag and drop handlers
const dropZone = document.getElementById('dropZone');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropZone.classList.add('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFileSelect(files[0]);
    }
});

function handleFileSelect(file) {
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please select a valid video file.');
        return;
    }
    
    // Validate file size (100MB)
    if (file.size > 100 * 1024 * 1024) {
        alert('File size must be less than 100MB.');
        return;
    }
    
    selectedFile = file;
    
    // Also set the file input value for proper form submission
    const fileInput = document.getElementById('video');
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileInput.files = dataTransfer.files;
    
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileInfo').classList.remove('hidden');
    document.getElementById('dropZoneContent').classList.add('hidden');
}

function removeFile() {
    selectedFile = null;
    document.getElementById('video').value = '';
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('dropZoneContent').classList.remove('hidden');
}

// Form submission
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('video');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a video file.');
        return;
    }
    
    const formData = new FormData();
    formData.append('title', document.getElementById('title').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('video', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Debug: Log form data
    console.log('Form data:', {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        file: file,
        fileSize: file.size,
        fileName: file.name,
        fileType: file.type
    });
    
    // Show progress bar
    document.getElementById('uploadProgress').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').textContent = 'Uploading...';
    
    // Create XMLHttpRequest for upload with progress
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progressBar').style.width = percentComplete + '%';
            document.getElementById('progressPercent').textContent = Math.round(percentComplete) + '%';
        }
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert('Video uploaded successfully! Processing will begin shortly.');
                window.location.href = '{{ route("videos.index") }}';
            } else {
                let errorMessage = 'Upload failed: ' + (response.message || 'Unknown error');
                if (response.errors) {
                    errorMessage += '\n\nValidation errors:\n';
                    for (const field in response.errors) {
                        errorMessage += field + ': ' + response.errors[field].join(', ') + '\n';
                    }
                }
                alert(errorMessage);
                resetForm();
            }
        } else if (xhr.status === 422) {
            const response = JSON.parse(xhr.responseText);
            let errorMessage = 'Validation failed:\n';
            if (response.errors) {
                for (const field in response.errors) {
                    errorMessage += field + ': ' + response.errors[field].join(', ') + '\n';
                }
            }
            alert(errorMessage);
            resetForm();
        } else {
            alert('Upload failed with status: ' + xhr.status + '. Please try again.');
            resetForm();
        }
    });
    
    xhr.addEventListener('error', function() {
        alert('Upload failed. Please check your connection and try again.');
        resetForm();
    });
    
    xhr.open('POST', '{{ route("videos.store") }}');
    xhr.send(formData);
});

function resetForm() {
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitBtn').textContent = 'Upload Video';
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('progressPercent').textContent = '0%';
}
</script>
@endpush
@endsection
