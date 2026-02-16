<?php
/**
 * Lesson 5: Video Resizing & Scaling
 * 
 * In this lesson, you'll learn:
 * - How to resize videos to different dimensions
 * - How to maintain aspect ratio
 * - How to add padding/letterboxing
 * - How to scale for different devices
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;

$videoFile = '../assets/video.mp4';
$outputDir = '../outputs/';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

try {
    $ffmpeg = FFMpeg::create([
        'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
        'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
    ]);
} catch (Exception $e) {
    die("Error initializing FFmpeg: " . $e->getMessage());
}

$videoExists = file_exists($videoFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson 5: Video Resizing</title>
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
        
        <h1>📐 Lesson 5: Video Resizing & Scaling</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Resizing videos to specific dimensions</li>
                <li>Maintaining aspect ratio</li>
                <li>Adding padding/letterboxing</li>
                <li>Scaling for different devices (mobile, tablet, desktop)</li>
                <li>Cropping videos</li>
                <li>Advanced filter chains</li>
            </ul>
        </div>
        
        <h2>1️⃣ Common Video Resolutions</h2>
        
        <table>
            <tr>
                <th>Resolution</th>
                <th>Aspect Ratio</th>
                <th>Device</th>
                <th>Use Case</th>
            </tr>
            <tr>
                <td>320x240 (QVGA)</td>
                <td>4:3</td>
                <td>Old Mobile</td>
                <td>Legacy devices</td>
            </tr>
            <tr>
                <td>640x480 (VGA)</td>
                <td>4:3</td>
                <td>Mobile</td>
                <td>Small screens</td>
            </tr>
            <tr>
                <td>1280x720 (720p)</td>
                <td>16:9</td>
                <td>HD</td>
                <td>Web streaming, tablets</td>
            </tr>
            <tr>
                <td>1920x1080 (1080p)</td>
                <td>16:9</td>
                <td>Full HD</td>
                <td>Desktops, high quality</td>
            </tr>
            <tr>
                <td>2560x1440 (1440p)</td>
                <td>16:9</td>
                <td>2K</td>
                <td>High-end devices</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Simple Resize to Specific Dimensions</h3>
            <div class="code">
$video = $ffmpeg->open('input.mp4');

$video->filters()
    ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_FIT, true)
    ->synchronize();

$format = new X264();
$video->save($format, 'output.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Resize Maintaining Aspect Ratio</h3>
            <div class="code">
// Resize to fit within 1280x720 while keeping aspect ratio
$video->filters()
    ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_INSET, true)
    ->synchronize();

$video->save(new X264(), 'output.mp4');

// Or constrain a different max size
$video->filters()
    ->resize(new Dimension(720, 1280), ResizeFilter::RESIZEMODE_INSET, true)
    ->synchronize();
            </div>
        </div>
        
        <div class="example">
            <h3>Resize with Padding (Letterboxing)</h3>
            <div class="code">
// Resize to 1280x720 with padding (letterbox)
$video->filters()
    ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_INSET, true)
    ->pad(new Dimension(1280, 720))
    ->synchronize();

$video->save(new X264(), 'output.mp4');

// Use RESIZEMODE_INSET to keep aspect ratio inside the target box
            </div>
        </div>
        
        <div class="example">
            <h3>Generate Multiple Quality Versions</h3>
            <div class="code">
function generateMultipleResolutions($ffmpeg, $inputFile, $outputDir) {
    $resolutions = [
        'mobile' => ['width' => 640, 'height' => 480, 'bitrate' => 800],
        'tablet' => ['width' => 1280, 'height' => 720, 'bitrate' => 2500],
        'desktop' => ['width' => 1920, 'height' => 1080, 'bitrate' => 5000],
    ];
    
    foreach ($resolutions as $device => $config) {
        try {
            $video = $ffmpeg->open($inputFile);
            
            $video->filters()
                ->resize(new Dimension($config['width'], $config['height']), ResizeFilter::RESIZEMODE_FIT, true)
                ->synchronize();
            
            $format = new X264();
            $format->setKiloBitrate($config['bitrate']);
            
            $output = $outputDir . 'video_' . $device . '.mp4';
            $video->save($format, $output);
            
            echo "Generated: " . $device . " version&lt;br&gt;";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
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
                <button class="button" onclick="loadExample('resize_720p')">Resize to 720p</button>
                <button class="button" onclick="loadExample('resize_mobile')">Resize to Mobile (640x480)</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'resize_720p') {
                try {
                    $output = $outputDir . 'resized_720p.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_FIT, true)
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Video Resized to 720p (1280x720)</h3>
                            <p><strong>Resolution:</strong> 1280x720 pixels</p>
                            <p><strong>Bitrate:</strong> 2500 kbps</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Perfect For:</strong> Web streaming, tablets</p>
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
            } elseif ($action === 'resize_mobile') {
                try {
                    $output = $outputDir . 'resized_mobile.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->resize(new Dimension(640, 480), ResizeFilter::RESIZEMODE_FIT, true)
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(800);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Video Resized for Mobile (640x480)</h3>
                            <p><strong>Resolution:</strong> 640x480 pixels</p>
                            <p><strong>Bitrate:</strong> 800 kbps</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Perfect For:</strong> Mobile devices, slow connections</p>
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
                <li><strong>Aspect Ratio:</strong> Always choose resolutions with 16:9 aspect ratio for modern videos</li>
                <li><strong>Performance:</strong> Larger resolutions take longer to process</li>
                <li><strong>File Size:</strong> Smaller resolutions = smaller files</li>
                <li><strong>Quality Loss:</strong> Scaling down reduces quality, scaling up introduces artifacts</li>
                <li><strong>Auto Scaling:</strong> Use -1 for width or height to auto-calculate</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="06_video_trimming.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>