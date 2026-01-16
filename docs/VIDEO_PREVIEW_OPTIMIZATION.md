# Video Preview Optimization

## Overview

This system implements optimized video previews for the DJ management interface to improve performance while maintaining a great user experience.

## How It Works

### 1. **Automatic Preview Generation**

When a DJ video is uploaded, the system automatically generates:

- **Preview Video**: A smaller, compressed version (400px width, 500kbps bitrate) optimized for quick loading
- **Poster Image**: A static thumbnail extracted from the video for instant display

### 2. **Frontend Optimization**

The video preview implementation uses several performance optimizations:

#### Lazy Loading
- Videos use `preload="none"` to prevent automatic loading
- Only loads when needed, saving bandwidth

#### Play on Hover
- Videos only play when user hovers over them
- Pauses and resets when mouse leaves
- Provides interactive preview without auto-playing all videos

#### Poster Image Priority
- Displays poster image first for instant visual feedback
- Video loads only when user interacts
- Significantly reduces initial page load

#### Muted Loop
- Videos are muted to allow autoplay (browser requirement)
- Loops continuously while hovering
- No sound means smaller file sizes

### 3. **File Size Reduction**

Preview files are optimized to be much smaller than originals:

| Original | Preview | Savings |
|----------|---------|---------|
| 1080p, 5Mbps | 400px, 500kbps | ~90% |
| 50MB file | ~5MB preview | ~90% |

## Technical Implementation

### Backend: VideoPreviewService

Located at `app/Services/VideoPreviewService.php`, this service handles:

- FFmpeg-based video transcoding
- Poster image extraction
- File cleanup on deletion

#### Methods:

```php
// Generate a smaller preview video
generatePreview(string $videoPath): ?string

// Extract a poster/thumbnail image
generatePoster(string $videoPath): ?string

// Clean up preview files when video is deleted
deletePreviewFiles(string $videoPath): void

// Check if FFmpeg is available
isFfmpegAvailable(): bool
```

### Database Schema

Two new fields added to `d_j_s` table:

- `preview_video_path` - Path to compressed preview video
- `poster_path` - Path to thumbnail image

### Controller Updates

`app/Http/Controllers/Admin/DJController.php` now:

1. **On Upload**: Generates preview and poster automatically
2. **On List**: Returns preview URL (preferred) or original
3. **On Update**: Regenerates previews if video changes
4. **On Delete**: Cleans up all related files

### Frontend: DataTables Rendering

`resources/js/admin-djs-datatables.js` implements:

```javascript
// Video preview with poster and lazy loading
render: function(data, type, row) {
    const poster = row.poster ? `poster="${row.poster}"` : '';
    const videoId = `video-${row.id}`;
    return `<video id="${videoId}" 
                   preload="none"
                   ${poster}
                   muted loop>
        <source src="${data}">
    </video>`;
}

// Play on hover
window.playVideo = function(videoId) {
    document.getElementById(videoId).play();
};

// Pause on leave
window.pauseVideo = function(videoId) {
    const video = document.getElementById(videoId);
    video.pause();
    video.currentTime = 0;
};
```

## Requirements

### FFmpeg Installation

The preview generation requires FFmpeg to be installed on the server.

#### macOS (via Homebrew):
```bash
brew install ffmpeg
```

#### Ubuntu/Debian:
```bash
sudo apt update
sudo apt install ffmpeg
```

#### CentOS/RHEL:
```bash
sudo yum install epel-release
sudo yum install ffmpeg
```

### Verify Installation:
```bash
ffmpeg -version
```

## Fallback Behavior

If FFmpeg is not available:

1. System logs a warning
2. Original video is used for preview
3. No poster image is generated
4. Functionality continues to work

The system is designed to work with or without FFmpeg, degrading gracefully.

## Performance Benefits

### Before Optimization:
- ❌ All videos load on page load
- ❌ Full-size videos (~50MB each)
- ❌ Slow page load times
- ❌ High bandwidth usage
- ❌ Poor mobile experience

### After Optimization:
- ✅ Videos load on interaction only
- ✅ Small preview files (~5MB each)
- ✅ Fast page load with poster images
- ✅ Minimal bandwidth usage
- ✅ Great mobile experience
- ✅ Interactive hover previews

## Configuration

### FFmpeg Settings

To customize video quality, edit `app/Services/VideoPreviewService.php`:

```php
'-vf', 'scale=400:-2',  // Width (adjust as needed)
'-b:v', '500k',          // Video bitrate
'-b:a', '64k',           // Audio bitrate
'-preset', 'fast',       // Encoding speed
```

### Video Display Size

To change preview size, edit `resources/js/admin-djs-datatables.js`:

```javascript
// Current: 100px x 75px
width: 100px; height: 75px;
```

## Troubleshooting

### Preview Not Generating

1. Check if FFmpeg is installed: `ffmpeg -version`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify file permissions on storage directory
4. Check PHP `max_execution_time` (generation can take time)

### Videos Not Playing on Hover

1. Check browser console for errors
2. Verify video file is accessible (check Network tab)
3. Ensure `playVideo` and `pauseVideo` functions are loaded
4. Check if browser blocks autoplay (poster image will show)

### Large Storage Usage

Preview files are stored alongside originals. To clean up:

```bash
# Find preview files
find storage/app/public/djs -name "*_preview.*"

# Find poster files  
find storage/app/public/djs -name "*_poster.jpg"
```

## Future Enhancements

Potential improvements:

1. **Background Processing**: Use Laravel queues for preview generation
2. **Multiple Quality Levels**: Generate several sizes for different contexts
3. **Cloud Storage**: Upload previews to CDN
4. **WebP Posters**: Use modern image formats
5. **Adaptive Streaming**: HLS/DASH for larger videos
6. **Preview on Demand**: Generate only when requested

## Migration

To add these fields to existing installation:

```bash
php artisan migrate
```

To regenerate previews for existing videos:

```bash
php artisan tinker

# In tinker:
$djs = App\Models\DJ::whereNotNull('video_path')->get();
$service = new App\Services\VideoPreviewService();

foreach ($djs as $dj) {
    $preview = $service->generatePreview($dj->video_path);
    $poster = $service->generatePoster($dj->video_path);
    
    $dj->update([
        'preview_video_path' => $preview,
        'poster_path' => $poster
    ]);
}
```

## Summary

This optimization provides a **90% reduction in bandwidth** while improving the user experience with interactive hover previews. The system works with or without FFmpeg, making it flexible for different deployment environments.
