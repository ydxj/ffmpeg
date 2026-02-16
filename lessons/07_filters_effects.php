<?php
/**
 * Lesson 7: Filters & Effects
 * 
 * In this lesson, you'll learn:
 * - How torotate and flip videos
 * - How to adjust brightness and contrast
 * - How to add watermarks
 * - How to apply blur effects
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
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
    <title>Lesson 7: Filters & Effects</title>
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
        
        <h1>🎨 Lesson 7: Filters & Effects</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Applying rotation and flip effects</li>
                <li>Adjusting brightness and contrast</li>
                <li>Adding watermarks to videos</li>
                <li>Applying blur and other effects</li>
                <li>Combining multiple filters</li>
                <li>Advanced filter chains</li>
            </ul>
        </div>
        
        <h2>1️⃣ Available Filters</h2>
        
        <table>
            <tr>
                <th>Filter</th>
                <th>Parameters</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>rotate()</td>
                <td>Degrees (90, 180, 270, etc.)</td>
                <td>Rotate video by specified degrees</td>
            </tr>
            <tr>
                <td>flipVertical()</td>
                <td>None</td>
                <td>Flip video vertically</td>
            </tr>
            <tr>
                <td>flipHorizontal()</td>
                <td>None</td>
                <td>Flip video horizontally (mirror)</td>
            </tr>
            <tr>
                <td>scale()</td>
                <td>Width, Height</td>
                <td>Resize video</td>
            </tr>
            <tr>
                <td>synchronize()</td>
                <td>None</td>
                <td>Sync audio and video</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Rotate Video 90 Degrees</h3>
            <div class="code">
$video = $ffmpeg->open('input.mp4');

$video->filters()
    ->rotate(90)  // Rotate 90 degrees clockwise
    ->synchronize();

$format = new X264();
$video->save($format, 'output_rotated.mp4');

// Other rotation options: 90, 180, 270
            </div>
        </div>
        
        <div class="example">
            <h3>Flip Video (Mirror Effect)</h3>
            <div class="code">
// Horizontal flip (mirror)
$video->filters()
    ->flipHorizontal()
    ->synchronize();

$video->save(new X264(), 'output_mirrored.mp4');

// Vertical flip
$video->filters()
    ->flipVertical()
    ->synchronize();

$video->save(new X264(), 'output_flipped.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Combine Multiple Filters</h3>
            <div class="code">
// Apply multiple effects in sequence
$video->filters()
    ->scale(1280, 720)      // Resize
    ->flipHorizontal()      // Mirror
    ->synchronize();        // Sync audio/video

$format = new X264();
$video->save($format, 'output_effects.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Create Various Effects</h3>
            <div class="code">
function applyMultipleEffects($ffmpeg, $inputFile, $outputDir) {
    $effects = [
        'original' => function($video) {
            return $video;
        },
        'rotated_90' => function($video) {
            $video->filters()->rotate(90)->synchronize();
            return $video;
        },
        'mirrored' => function($video) {
            $video->filters()->flipHorizontal()->synchronize();
            return $video;
        },
        'scaled_720p' => function($video) {
            $video->filters()->scale(1280, 720)->synchronize();
            return $video;
        },
    ];
    
    foreach ($effects as $name => $effect) {
        try {
            $video = $ffmpeg->open($inputFile);
            $video = $effect($video);
            
            $output = $outputDir . 'effect_' . $name . '.mp4';
            $video->save(new X264(), $output);
            
            echo "Created: " . $name . "&lt;br&gt;";
        } catch (Exception $e) {
            echo "Error with " . $name . ": " . $e->getMessage();
        }
    }
}

applyMultipleEffects($ffmpeg, 'input.mp4', 'outputs/');
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
                <button class="button" onclick="loadExample('rotate_90')">Rotate 90°</button>
                <button class="button" onclick="loadExample('mirror')">Mirror Effect</button>
                <button class="button" onclick="loadExample('scale_and_rotate')">Scale + Rotate</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'rotate_90') {
                try {
                    $output = $outputDir . 'rotated_90.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->rotate(90)
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Video Rotated 90 Degrees</h3>
                            <p><strong>Rotation:</strong> 90° Clockwise</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Effect:</strong> Portrait mode video</p>
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
            } elseif ($action === 'mirror') {
                try {
                    $output = $outputDir . 'mirrored.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->flipHorizontal()
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Video Mirrored (Horizontal Flip)</h3>
                            <p><strong>Effect:</strong> Horizontal Flip</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Use Case:</strong> Mirror/reflection effects</p>
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
            } elseif ($action === 'scale_and_rotate') {
                try {
                    $output = $outputDir . 'scaled_rotated.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->scale(1280, 720)
                            ->rotate(90)
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Combined Effects Applied</h3>
                            <p><strong>Effects:</strong> Scale to 1280x720 + Rotate 90°</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Result:</strong> Professional transformation</p>
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
                <li><strong>Filter Chain:</strong> Chain multiple filters for complex effects</li>
                <li><strong>Synchronize:</strong> Always call synchronize() after applying filters</li>
                <li><strong>Performance:</strong> More filters = longer processing time</li>
                <li><strong>Order Matters:</strong> Apply filters in logical order (resize, then effects)</li>
                <li><strong>Quality:</strong> Processing can affect quality, use appropriate bitrate</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="08_advanced_encoding.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>