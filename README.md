# PHP FFmpeg Library Course

Welcome to the comprehensive PHP FFmpeg Library course! This course covers everything you need to know about video and audio manipulation in PHP using FFmpeg.

## Course Structure

### 1. **01_basic_video_info.php** - Getting Video Information
- Loading video files
- Extracting video metadata (duration, codec, resolution, etc.)
- Checking if videos are valid
- Getting detailed format information

### 2. **02_thumbnail_generation.php** - Creating Thumbnails
- Extracting frames from videos at specific timestamps
- Creating multiple thumbnails
- Saving thumbnails in different formats
- Batch thumbnail generation

### 3. **03_video_conversion.php** - Converting Video Formats
- Converting videos to different formats (MP4, WebM, AVI, etc.)
- Setting video quality and bitrate
- Changing video codecs
- Preserving aspect ratio

### 4. **04_audio_extraction.php** - Extracting and Converting Audio
- Extracting audio from videos
- Converting to MP3, AAC, OGG formats
- Setting audio bitrate and sample rate
- Creating audio only files from videos

### 5. **05_video_resizing.php** - Resizing and Scaling Videos
- Resizing video dimensions
- Maintaining aspect ratio
- Scaling for different devices
- Padding and letterboxing

### 6. **06_video_duration.php** - Managing Video Duration
- Cutting/trimming videos
- Extracting specific segments
- Setting start and end times
- Merging clips

### 7. **07_filters_effects.php** - Applying Filters and Effects
- Adding watermarks
- Applying brightness/contrast adjustments
- Rotating and flipping videos
- Adding visual effects

### 8. **08_advanced_encoding.php** - Advanced Encoding Techniques
- Custom FFmpeg parameters
- Multiple quality output
- Progressive encoding
- Stream handling

## Prerequisites

- PHP 7.0 or higher
- FFmpeg installed on server (C:/ffmpeg/bin/ffmpeg.exe for Windows)
- FFprobe installed (for video information)
- Composer and php-ffmpeg library

## Installation

```bash
composer require php-ffmpeg/php-ffmpeg
```

## How to Use This Course

1. Start with `01_basic_video_info.php` and work through each file sequentially
2. Each file is self-contained with working examples
3. View each file in your browser to see the results
4. Upload your own video files to test (recommended video format: MP4)
5. Check the `outputs/` folder for generated files

## Setup

### 1. Install FFmpeg

**Windows:**
- Download from: https://ffmpeg.org/download.html
- Extract to: C:/ffmpeg/
- Ensure ffmpeg.exe and ffprobe.exe are in C:/ffmpeg/bin/

**Linux:**
```bash
sudo apt-get install ffmpeg
```

**macOS:**
```bash
brew install ffmpeg
```

### 2. Install Composer Package

```bash
cd ffmpeg
composer require php-ffmpeg/php-ffmpeg
```

### 3. Create directories

```
ffmpeg/
├── assets/          (place your videos here)
├── outputs/         (auto-generated files)
├── lessons/         (lesson files)
└── public/          (accessible via web)
```

## Supported Formats

### Video Formats
- MP4 (H.264, H.265)
- WebM (VP8, VP9)
- AVI
- MOV
- MKV
- FLV

### Audio Formats
- MP3
- AAC
- OGG Vorbis
- FLAC
- WAV

## Common Functions Reference

### Basic Setup
```php
$ffmpeg = FFMpeg::create([
    'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
    'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
]);
```

### Open a Video
```php
$video = $ffmpeg->open('path/to/video.mp4');
```

### Get Video Information
```php
$format = $video->getFormat();
$duration = $format->getDuration();
$width = $format->getWidth();
$height = $format->getHeight();
```

### Extract a Frame
```php
$frame = $video->frame(TimeCode::fromSeconds(10));
$frame->save('output.jpg');
```

### Convert to MP3
```php
$format = new Mp3();
$video->save($format, 'output.mp3');
```

### Apply Filters
```php
$video->filters()->scale(1280, 720)->synchronize();
$video->save(new X264(), 'output.mp4');
```

## Navigation

Use `index.php` for easy navigation between all examples.

## Tips and Tricks

1. Always handle errors with try-catch blocks
2. Use TimeCode for accurate frame extraction
3. Optimize bitrate for web delivery (2000-5000k for HD)
4. Test with small files first before processing large videos
5. Use output directories to organize generated files
6. Monitor memory usage for batch operations

## Resources

- **Official Documentation**: https://github.com/PHP-FFMpeg/PHP-FFMpeg
- **FFmpeg Documentation**: https://ffmpeg.org/documentation.html
- **Video Codecs**: https://trac.ffmpeg.org/wiki/Encode
- **Audio Codecs**: https://trac.ffmpeg.org/wiki/Encode/HighQualityAudio

## Troubleshooting

### FFmpeg not found
- Ensure ffmpeg.exe path is correctly set
- Check Windows PATH environment variable
- Verify ffmpeg.exe exists in specified directory

### Memory issues
- Process videos in smaller batches
- Increase PHP memory_limit in php.ini
- Use streaming for large files

### Low quality output
- Increase bitrate in encoding options
- Use appropriate codec for your needs
- Check source video quality
