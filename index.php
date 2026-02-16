<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP FFmpeg Video Processing Course - Complete Guide</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .intro {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 40px;
            border-left: 5px solid #667eea;
        }
        
        .intro h2 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .intro p {
            line-height: 1.8;
            color: #666;
            margin-bottom: 10px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .feature {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .feature:hover {
            transform: translateY(-5px);
        }
        
        .feature h3 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .feature p {
            font-size: 0.95em;
        }
        
        .lessons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .lesson-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }
        
        .lesson-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
            transform: translateY(-3px);
        }
        
        .lesson-number {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .lesson-card h3 {
            margin: 15px 0 10px 0;
            font-size: 1.3em;
        }
        
        .lesson-card p {
            color: #666;
            line-height: 1.6;
            font-size: 0.95em;
        }
        
        .lesson-features {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .lesson-features li {
            list-style: none;
            color: #888;
            padding: 5px 0;
            font-size: 0.9em;
        }
        
        .lesson-features li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .button-section {
            text-align: center;
            margin-top: 10px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
            margin-top: 15px;
        }
        
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        
        .requirements {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .requirements h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .requirements p {
            color: #856404;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎬 PHP FFmpeg Video Processing</h1>
            <p>Complete Guide to Video & Audio Manipulation</p>
        </div>
        
        <div class="content">
            <div class="intro">
                <h2>Welcome to FFmpeg Course!</h2>
                <p>FFmpeg is a powerful multimedia processing library that allows you to manipulate video and audio files programmatically. Whether you need to convert formats, extract thumbnails, adjust quality, or apply effects, FFmpeg has you covered.</p>
                <p>This comprehensive course will guide you through every aspect of video and audio processing in PHP, from basic operations to advanced techniques.</p>
            </div>
            
            <div class="requirements">
                <h3>📋 Requirements</h3>
                <p>✓ PHP 7.0 or higher</p>
                <p>✓ FFmpeg installed (C:/ffmpeg/bin/ffmpeg.exe for Windows)</p>
                <p>✓ PHP FFmpeg library (via Composer)</p>
                <p>✓ Basic PHP and command-line knowledge</p>
            </div>
            
            <h2 style="margin-bottom: 30px; color: #333;">What You'll Learn</h2>
            
            <div class="features">
                <div class="feature">
                    <h3>🎯</h3>
                    <p><strong>8+ Lessons</strong><br>Comprehensive tutorials</p>
                </div>
                <div class="feature">
                    <h3>💡</h3>
                    <p><strong>Real Examples</strong><br>Practical use cases</p>
                </div>
                <div class="feature">
                    <h3>⚡</h3>
                    <p><strong>Video Processing</strong><br>All techniques covered</p>
                </div>
                <div class="feature">
                    <h3>🎨</h3>
                    <p><strong>Effects & Filters</strong><br>Visual enhancements</p>
                </div>
            </div>
            
            <h2 style="margin-bottom: 30px; color: #333; margin-top: 50px;">Lessons</h2>
            
            <div class="lessons">
                <a href="lessons/01_basic_video_info.php" class="lesson-card">
                    <div class="lesson-number">1</div>
                    <h3>Basic Video Information</h3>
                    <p>Learn how to load videos and extract metadata like duration, resolution, codec, and bitrate.</p>
                    <ul class="lesson-features">
                        <li>Loading video files</li>
                        <li>Getting video duration</li>
                        <li>Video resolution</li>
                        <li>Format information</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/02_thumbnail_generation.php" class="lesson-card">
                    <div class="lesson-number">2</div>
                    <h3>Thumbnail Generation</h3>
                    <p>Extract frames from videos and create high-quality thumbnails at specific timestamps.</p>
                    <ul class="lesson-features">
                        <li>Frame extraction</li>
                        <li>TimeCode management</li>
                        <li>Batch generation</li>
                        <li>Format options</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/03_video_conversion.php" class="lesson-card">
                    <div class="lesson-number">3</div>
                    <h3>Video Conversion</h3>
                    <p>Convert videos between different formats with custom quality settings and codec options.</p>
                    <ul class="lesson-features">
                        <li>Format conversion</li>
                        <li>Quality settings</li>
                        <li>Codec selection</li>
                        <li>Progress tracking</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/04_audio_extraction.php" class="lesson-card">
                    <div class="lesson-number">4</div>
                    <h3>Audio Extraction</h3>
                    <p>Extract audio from videos and convert to various audio formats like MP3, AAC, and OGG.</p>
                    <ul class="lesson-features">
                        <li>Audio extraction</li>
                        <li>Format conversion</li>
                        <li>Bitrate control</li>
                        <li>Audio processing</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/05_video_resizing.php" class="lesson-card">
                    <div class="lesson-number">5</div>
                    <h3>Video Resizing & Scaling</h3>
                    <p>Resize videos to different dimensions while maintaining aspect ratio and adding effects.</p>
                    <ul class="lesson-features">
                        <li>Video scaling</li>
                        <li>Aspect ratio</li>
                        <li>Padding</li>
                        <li>Letterboxing</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/06_video_trimming.php" class="lesson-card">
                    <div class="lesson-number">6</div>
                    <h3>Video Trimming & Segments</h3>
                    <p>Cut specific segments from videos, trim duration, and manage video playback time.</p>
                    <ul class="lesson-features">
                        <li>Video trimming</li>
                        <li>TimeCode ranges</li>
                        <li>Segment extraction</li>
                        <li>Duration limits</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/07_filters_effects.php" class="lesson-card">
                    <div class="lesson-number">7</div>
                    <h3>Filters & Effects</h3>
                    <p>Apply professional effects to videos including watermarks, brightness, rotation, and more.</p>
                    <ul class="lesson-features">
                        <li>Watermarks</li>
                        <li>Brightness/Contrast</li>
                        <li>Rotation & Flip</li>
                        <li>Blur effects</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
                
                <a href="lessons/08_advanced_encoding.php" class="lesson-card">
                    <div class="lesson-number">8</div>
                    <h3>Advanced Encoding</h3>
                    <p>Master advanced techniques including custom parameters, quality tiers, and streaming.</p>
                    <ul class="lesson-features">
                        <li>Custom FFmpeg commands</li>
                        <li>Multiple quality output</li>
                        <li>Stream handling</li>
                        <li>Optimization</li>
                    </ul>
                    <div class="button-section">
                        <button class="btn">Start Lesson</button>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>PHP FFmpeg Video Processing Course</strong></p>
            <p>Complete guide to video and audio manipulation using PHP and FFmpeg</p>
            <p style="margin-top: 15px; font-size: 0.9em;">For more information, visit the <a href="README.md" style="color: #667eea;">README file</a></p>
        </div>
    </div>
</body>
</html>
