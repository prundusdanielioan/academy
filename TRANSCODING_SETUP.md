# Video Transcoding Setup Guide

This application supports multiple video transcoding methods to work with different hosting environments.

## Available Methods

### 1. Simple MP4 Streaming (Default - No FFmpeg Required)
**Best for**: Basic hosting without FFmpeg support
```env
VIDEO_TRANSCODING_METHOD=simple
```
- Streams MP4 files directly without transcoding
- Creates simple placeholder thumbnails
- No adaptive streaming (single quality)
- Fastest setup, works on any hosting

### 2. FFmpeg (Local Processing)
**Best for**: VPS/dedicated servers with FFmpeg installed
```env
VIDEO_TRANSCODING_METHOD=ffmpeg
```
- Requires FFmpeg and FFprobe installed
- Creates HLS adaptive streaming
- Generates proper thumbnails
- Best quality and performance

### 3. External Processing Server
**Best for**: Shared hosting with separate processing server
```env
VIDEO_TRANSCODING_METHOD=external
PROCESSING_SERVER_URL=http://your-processing-server.com
```
- Uploads videos to external server with FFmpeg
- Processes videos remotely
- Returns HLS URLs when complete

### 4. Client-Side Processing
**Best for**: When you want to offload processing to users
```env
VIDEO_TRANSCODING_METHOD=client
```
- Users' browsers handle transcoding
- Requires JavaScript libraries (FFmpeg.wasm)
- Good for reducing server load

## Cloud-Based Alternatives

### AWS MediaConvert
```php
// Install: composer require aws/aws-sdk-php
// Configure AWS credentials in .env
```

### Cloudinary
```php
// Install: composer require cloudinary/cloudinary-php
// Configure Cloudinary credentials in .env
```

### Mux Video API
```php
// Install: composer require mux/mux-php
// Configure Mux credentials in .env
```

## Setup Instructions

1. **Choose your method** based on your hosting capabilities
2. **Set the environment variable** in your `.env` file:
   ```env
   VIDEO_TRANSCODING_METHOD=simple
   ```
3. **Clear caches**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Hosting Recommendations

- **Shared Hosting**: Use `simple` or `external`
- **VPS/Cloud**: Use `ffmpeg` or cloud services
- **Budget Hosting**: Use `simple` or `client`
- **High Traffic**: Use cloud services (AWS, Cloudinary, Mux)

## Performance Comparison

| Method | Setup Time | Quality | Server Load | Cost |
|--------|------------|---------|-------------|------|
| Simple | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| FFmpeg | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ |
| External | ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| Client | ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| Cloud | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ |
