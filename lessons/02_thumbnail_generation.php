<?php
/**
 * Lesson 2: Generating Video Thumbnails
 * 
 * Learn how to:
 * - Extract a frame at specific timestamps
 * - Generate thumbnail images from videos
 * - Save thumbnails in different sizes
 * - Create image sequences
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

$action = $_GET['action'] ?? 'menu';
$videoFile = '../assets/video.mp4';
$videoExists = file_exists($videoFile);
$outputDir = '../outputs/thumbnails';

// Create output directory
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$ffmpeg = null;
$error = null;

if ($videoExists) {
    try {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
        ]);
    } catch (Exception $e) {
        $error = "Error initializing FFmpeg: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson 2: Thumbnail Generation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .example {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .code {
            background: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px 10px 0;
            cursor: pointer;
            border: none;
            font-size: 1em;
        }
        .button:hover {
            background: #764ba2;
        }
        .result {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .result h3 {
            color: #2e7d32;
            margin-top: 0;
        }
        .error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #c62828;
        }
        .back-link {
            margin-bottom: 20px;
        }
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .thumbnail-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .thumbnail-item img {
            width: 100%;
            height: auto;
            display: block;
        }
        .thumbnail-info {
            padding: 10px;
            background: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-link">
            <a href="../index.php" class="button">← Back to Lessons</a>
        </div>
        
        <h1>🖼️ Lesson 2: Thumbnail Generation</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Extracting frames from videos at specific timestamps</li>
                <li>Working with TimeCode objects</li>
                <li>Saving frames as image files (JPG, PNG)</li>
                <li>Generating multiple thumbnails from one video</li>
                <li>Batch processing thumbnails</li>
                <li>Creating preview strips</li>
            </ul>
        </div>
        
        <h2>1️⃣ Functions Reference</h2>
        
        <table>
            <tr>
                <th>Function</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><code>$video->frame(TimeCode::fromSeconds($seconds))</code></td>
                <td>Extract a frame at specific seconds</td>
            </tr>
            <tr>
                <td><code>TimeCode::fromSeconds(10)</code></td>
                <td>Create a TimeCode from seconds</td>
            </tr>
            <tr>
                <td><code>TimeCode::fromString('00:00:10')</code></td>
                <td>Create a TimeCode from formatted string</td>
            </tr>
            <tr>
                <td><code>$frame->save('output.jpg')</code></td>
                <td>Save extracted frame to disk</td>
            </tr>
            <tr>
                <td><code>$frame->save('output.png')</code></td>
                <td>Save frame as PNG</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Extract Single Frame at 2 Seconds</h3>
            <div class="code">
use FFMpeg\Coordinate\TimeCode;

$video = $ffmpeg->open('video.mp4');

// Extract frame at 2 seconds
$frame = $video->frame(TimeCode::fromSeconds(2));

// Save as JPG
$frame->save('thumbnail.jpg');

// Or save as PNG
$frame->save('thumbnail.png');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Frame at Specific Time Format</h3>
            <div class="code">
// Extract frame at 00:01:30 (1 minute 30 seconds)
$frame = $video->frame(TimeCode::fromString('00:01:30'));
$frame->save('thumbnail_at_1m30s.jpg');

// Extract frame from percentage of video
$duration = $video->getFormat()->getDuration();
$midpoint = $duration / 2; // Middle of video
$frame = $video->frame(TimeCode::fromSeconds($midpoint));
$frame->save('thumbnail_middle.jpg');
            </div>
        </div>
        
        <div class="example">
            <h3>Generate Multiple Thumbnails</h3>
            <div class="code">
// Generate 5 thumbnails at different intervals
$timestamps = [2, 5, 10, 15, 20]; // seconds

foreach ($timestamps as $time) {
    try {
        $frame = $video->frame(TimeCode::fromSeconds($time));
        $frame->save('outputs/thumbnail_' . $time . 's.jpg');
        echo "Thumbnail at " . $time . "s created!&lt;br&gt;";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "&lt;br&gt;";
    }
}
            </div>
        </div>
        
        <div class="example">
            <h3>Generate Thumbnails at Equal Intervals</h3>
            <div class="code">
// Generate 6 thumbnails distributed throughout the video
$duration = (int)$video->getFormat()->getDuration();
$interval = $duration / 6; // Divide video into 6 parts

for ($i = 1; $i <= 6; $i++) {
    $time = $i * $interval;
    $frame = $video->frame(TimeCode::fromSeconds($time));
    $frame->save('outputs/thumb_' . $i . '.jpg');
}
            </div>
        </div>
        
        <div class="example">
            <h3>Advanced: Batch Processing</h3>
            <div class="code">
class ThumbnailGenerator {
    private $ffmpeg;
    private $outputDir;
    
    public function __construct($ffmpeg, $outputDir) {
        $this->ffmpeg = $ffmpeg;
        $this->outputDir = $outputDir;
    }
    
    public function generateThumbnails($videoPath, $count = 5, $prefix = 'thumb') {
        $video = $this->ffmpeg->open($videoPath);
        $duration = (int)$video->getFormat()->getDuration();
        $interval = $duration / $count;
        
        $results = [];
        for ($i = 1; $i <= $count; $i++) {
            $time = $i * $interval;
            try {
                $frame = $video->frame(TimeCode::fromSeconds($time));
                $filename = $this->outputDir . $prefix . '_' . $i . '.jpg';
                $frame->save($filename);
                $results[] = $filename;
            } catch (Exception $e) {
                echo "Error generating thumbnail " . $i . ": " . $e->getMessage();
            }
        }
        
        return $results;
    }
}

// Usage
$generator = new ThumbnailGenerator($ffmpeg, 'outputs/');
$thumbnails = $generator->generateThumbnails('video.mp4', 6);
            </div>
        </div>
        
        <h2>3️⃣ Live Examples</h2>
        
        <?php if (!$videoExists): ?>
            <div class="error">
                <strong>⚠️ No Video File Found</strong><br>
                Please upload a video file to: <code>../assets/video.mp4</code>
            </div>
        <?php else: ?>
            <div style="margin: 20px 0;">
                <button class="button" onclick="loadExample('single_frame')">Extract at 2s</button>
                <button class="button" onclick="loadExample('middle_frame')">Extract Middle Frame</button>
                <button class="button" onclick="loadExample('multiple_thumbnails')">Generate 6 Thumbnails</button>
            </div>
            
            <?php
            if ($action === 'single_frame') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $frame = $video->frame(TimeCode::fromSeconds(2));
                    
                    $outputFile = $outputDir . 'thumbnail_2s.jpg';
                    $frame->save($outputFile);
                    
                    // Check if file exists
                    if (file_exists($outputFile)) {
                        $fileSize = filesize($outputFile);
                        $fileSizeKB = round($fileSize / 1024, 2);
                        ?>
                        <div class="result">
                            <h3>✓ Frame Extracted at 2 Seconds</h3>
                            <div class="thumbnail-grid">
                                <div class="thumbnail-item">
                                    <img src="<?php echo str_replace('c:\\xampp\\htdocs\\PHPWSS\\', '../../', $outputFile); ?>" alt="Thumbnail at 2s">
                                    <div class="thumbnail-info">
                                        <p><strong>Time:</strong> 2 seconds</p>
                                        <p><strong>File:</strong> thumbnail_2s.jpg</p>
                                        <p><strong>Size:</strong> <?php echo $fileSizeKB; ?> KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } catch (Exception $e) {
                    ?>
                    <div class="error">
                        <strong>Error:</strong> <?php echo $e->getMessage(); ?>
                    </div>
                    <?php
                }
            } elseif ($action === 'middle_frame') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $duration = (int)$video->getFormat()->getDuration();
                    $midpoint = $duration / 2;
                    
                    $frame = $video->frame(TimeCode::fromSeconds($midpoint));
                    $outputFile = $outputDir . 'thumbnail_middle.jpg';
                    $frame->save($outputFile);
                    
                    if (file_exists($outputFile)) {
                        $fileSize = filesize($outputFile);
                        $fileSizeKB = round($fileSize / 1024, 2);
                        ?>
                        <div class="result">
                            <h3>✓ Frame Extracted at Middle (<?php echo round($midpoint); ?>s)</h3>
                            <div class="thumbnail-grid">
                                <div class="thumbnail-item">
                                    <img src="<?php echo str_replace('c:\\xampp\\htdocs\\PHPWSS\\', '../../', $outputFile); ?>" alt="Thumbnail middle">
                                    <div class="thumbnail-info">
                                        <p><strong>Time:</strong> <?php echo round($midpoint); ?> seconds</p>
                                        <p><strong>File:</strong> thumbnail_middle.jpg</p>
                                        <p><strong>Size:</strong> <?php echo $fileSizeKB; ?> KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } catch (Exception $e) {
                    ?>
                    <div class="error">
                        <strong>Error:</strong> <?php echo $e->getMessage(); ?>
                    </div>
                    <?php
                }
            } elseif ($action === 'multiple_thumbnails') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $duration = (int)$video->getFormat()->getDuration();
                    $interval = $duration / 6;
                    
                    $thumbnails = [];
                    for ($i = 1; $i <= 6; $i++) {
                        $time = ($i * $interval);
                        $frame = $video->frame(TimeCode::fromSeconds($time));
                        $outputFile = $outputDir . 'thumb_' . $i . '.jpg';
                        $frame->save($outputFile);
                        
                        if (file_exists($outputFile)) {
                            $thumbnails[] = [
                                'path' => $outputFile,
                                'time' => round($time),
                                'number' => $i,
                                'size' => round(filesize($outputFile) / 1024, 2)
                            ];
                        }
                    }
                    
                    if (!empty($thumbnails)) {
                        ?>
                        <div class="result">
                            <h3>✓ Generated 6 Thumbnails</h3>
                            <p><strong>Distribution:</strong> <?php echo round($interval); ?> second intervals</p>
                            <div class="thumbnail-grid">
                                <?php foreach ($thumbnails as $thumb): ?>
                                    <div class="thumbnail-item">
                                        <img src="<?php echo str_replace('c:\\xampp\\htdocs\\PHPWSS\\', '../../', $thumb['path']); ?>" alt="Thumbnail <?php echo $thumb['number']; ?>">
                                        <div class="thumbnail-info">
                                            <p><strong>#<?php echo $thumb['number']; ?></strong></p>
                                            <p>Time: <?php echo $thumb['time']; ?>s</p>
                                            <p>Size: <?php echo $thumb['size']; ?> KB</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    }
                } catch (Exception $e) {
                    ?>
                    <div class="error">
                        <strong>Error:</strong> <?php echo $e->getMessage(); ?>
                    </div>
                    <?php
                }
            }
            ?>
        <?php endif; ?>
        
        <h2>4️⃣ Key Points</h2>
        
        <div class="example">
            <ul>
                <li><strong>TimeCode Objects:</strong> Always use TimeCode for specifying frame positions</li>
                <li><strong>Format Support:</strong> You can save as JPG or PNG format</li>
                <li><strong>Performance:</strong> Extracting frames takes time, batch process carefully</li>
                <li><strong>Resolution:</strong> Thumbnails inherit source video dimensions</li>
                <li><strong>Error Handling:</strong> Always wrap frame extraction in try-catch</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="03_video_conversion.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>