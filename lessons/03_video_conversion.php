<?php
/**
 * Lesson 3: Video Conversion
 * 
 * In this lesson, you'll learn:
 * - How to convert videos to different formats
 * - How to set video quality and bitrate
 * - How to change video codecs
 * - How to handle conversion options
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Video\WebM;

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
    <title>Lesson 3: Video Conversion</title>
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
        
        <h1>🎞️ Lesson 3: Video Conversion</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Converting videos between different formats (MP4, WebM, etc.)</li>
                <li>Setting video quality and bitrate</li>
                <li>Changing video codecs</li>
                <li>Frame rate adjustments</li>
                <li>Progress tracking during conversion</li>
                <li>Handling conversion errors</li>
            </ul>
        </div>
        
        <h2>1️⃣ Supported Formats</h2>
        
        <table>
            <tr>
                <th>Format</th>
                <th>Class</th>
                <th>Codec</th>
                <th>Use Case</th>
            </tr>
            <tr>
                <td>MP4</td>
                <td>X264</td>
                <td>H.264</td>
                <td>Universal compatibility, web</td>
            </tr>
            <tr>
                <td>MP4</td>
                <td>X265</td>
                <td>H.265 (HEVC)</td>
                <td>Better compression, modern</td>
            </tr>
            <tr>
                <td>WebM</td>
                <td>WebM</td>
                <td>VP8/VP9</td>
                <td>Web videos, open format</td>
            </tr>
            <tr>
                <td>Theora</td>
                <td>Theora</td>
                <td>Theora</td>
                <td>Open-source alternative</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Basic MP4 Conversion with Quality</h3>
            <div class="code">
use FFMpeg\Format\Video\X264;

$video = $ffmpeg->open('input.mp4');
$format = new X264();

// Set bitrate (quality)
$format->setKiloBitrate(2500); // 2500 kbps

// Set audio bitrate
$format->setAudioKiloBitrate(128);

// Save converted video
$video->save($format, 'output.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Convert to WebM (VP9)</h3>
            <div class="code">
use FFMpeg\Format\Video\WebM;

$video = $ffmpeg->open('input.mp4');
$format = new WebM();

// Set quality
$format->setKiloBitrate(2500);
$format->setAudioKiloBitrate(128);

// Save as WebM
$video->save($format, 'output.webm');
            </div>
        </div>
        
        <div class="example">
            <h3>Different Quality Presets</h3>
            <div class="code">
// Low quality (streaming on slow connection)
$lowQuality = new X264();
$lowQuality->setKiloBitrate(500);
$lowQuality->setAudioKiloBitrate(64);
$video->save($lowQuality, 'output_low.mp4');

// Medium quality
$mediumQuality = new X264();
$mediumQuality->setKiloBitrate(1500);
$mediumQuality->setAudioKiloBitrate(128);
$video->save($mediumQuality, 'output_medium.mp4');

// High quality (HD)
$highQuality = new X264();
$highQuality->setKiloBitrate(5000);
$highQuality->setAudioKiloBitrate(192);
$video->save($highQuality, 'output_high.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>With Additional Parameters</h3>
            <div class="code">
$format = new X264();
$format->setKiloBitrate(2500);
$format->setAudioKiloBitrate(128);

// Set preset (ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow)
$format->setPreset('medium');

// Set CRF (constant rate factor) - lower = better quality, 0-51, default 23
$format->setCrf(23);

// Additional filters can be added
$video->filters()
    ->scale(1280, 720)           // Resize to 1280x720
    ->synchronize();              // Sync audio and video

$video->save($format, 'output.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Batch Conversion to Multiple Formats</h3>
            <div class="code">
function convertToMultipleFormats($ffmpeg, $inputFile, $outputDir) {
    $video = $ffmpeg->open($inputFile);
    
    $formats = [
        'mp4' => new X264(),
        'webm' => new WebM(),
    ];
    
    foreach ($formats as $ext => $format) {
        try {
            $format->setKiloBitrate(2500);
            $outputFile = $outputDir . 'converted.' . $ext;
            $video->save($format, $outputFile);
            echo "Converted to: " . $ext . "&lt;br&gt;";
        } catch (Exception $e) {
            echo "Error converting to " . $ext . ": " . $e->getMessage();
        }
    }
}

convertToMultipleFormats($ffmpeg, 'input.mp4', 'outputs/');
            </div>
        </div>
        
        <h2>3️⃣ Bitrate Guidelines</h2>
        
        <table>
            <tr>
                <th>Quality</th>
                <th>Resolution</th>
                <th>Video Bitrate</th>
                <th>Audio Bitrate</th>
                <th>Use Case</th>
            </tr>
            <tr>
                <td>Low</td>
                <td>360p</td>
                <td>500-1000 kbps</td>
                <td>64-96 kbps</td>
                <td>Mobile, slow connection</td>
            </tr>
            <tr>
                <td>Medium</td>
                <td>720p</td>
                <td>1500-2500 kbps</td>
                <td>128 kbps</td>
                <td>General web streaming</td>
            </tr>
            <tr>
                <td>High</td>
                <td>1080p</td>
                <td>4000-6000 kbps</td>
                <td>192-256 kbps</td>
                <td>HD streaming</td>
            </tr>
            <tr>
                <td>Very High</td>
                <td>2K/4K</td>
                <td>8000+ kbps</td>
                <td>256+ kbps</td>
                <td>4K streaming, archival</td>
            </tr>
        </table>
        
        <h2>4️⃣ Live Examples</h2>
        
        <?php if (!$videoExists): ?>
            <div class="error">
                <strong>⚠️ No Video File Found</strong><br>
                Please upload a video file to: <code>../assets/video.mp4</code>
            </div>
        <?php else: ?>
            <div style="margin: 20px 0;">
                <button class="button" onclick="loadExample('convert_mp4')">Convert to MP4</button>
                <button class="button" onclick="loadExample('convert_webm')">Convert to WebM</button>
                <button class="button" onclick="loadExample('multi_quality')">Generate Multi-Quality</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'convert_mp4') {
                try {
                    $output = $outputDir . 'converted_medium.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $format = new X264();
                        $format->setKiloBitrate(1500);
                        $format->setAudioKiloBitrate(128);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ MP4 Conversion Complete</h3>
                            <p><strong>Output File:</strong> converted_medium.mp4</p>
                            <p><strong>Bitrate:</strong> 1500 kbps (Medium Quality)</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Format:</strong> H.264 Video Codec</p>
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
            } elseif ($action === 'convert_webm') {
                try {
                    $output = $outputDir . 'converted.webm';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $format = new WebM();
                        $format->setKiloBitrate(1500);
                        $format->setAudioKiloBitrate(128);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ WebM Conversion Complete</h3>
                            <p><strong>Output File:</strong> converted.webm</p>
                            <p><strong>Bitrate:</strong> 1500 kbps</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Format:</strong> VP9 Video Codec (Open Source)</p>
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
            } elseif ($action === 'multi_quality') {
                try {
                    $files = [];
                    
                    $qualities = [
                        'low' => ['bitrate' => 800, 'label' => 'Low (800 kbps)'],
                        'medium' => ['bitrate' => 2000, 'label' => 'Medium (2000 kbps)'],
                        'high' => ['bitrate' => 4000, 'label' => 'High (4000 kbps)'],
                    ];
                    
                    foreach ($qualities as $key => $config) {
                        $output = $outputDir . 'video_' . $key . '.mp4';
                        if (!file_exists($output)) {
                            $video = $ffmpeg->open($videoFile);
                            $format = new X264();
                            $format->setKiloBitrate($config['bitrate']);
                            $format->setAudioKiloBitrate(128);
                            $video->save($format, $output);
                        }
                        
                        if (file_exists($output)) {
                            $size = round(filesize($output) / (1024 * 1024), 2);
                            $files[] = [
                                'name' => 'video_' . $key . '.mp4',
                                'label' => $config['label'],
                                'size' => $size
                            ];
                        }
                    }
                    
                    if (!empty($files)) {
                        ?>
                        <div class="result">
                            <h3>✓ Multi-Quality Conversion Complete</h3>
                            <table>
                                <tr>
                                    <th>Quality</th>
                                    <th>File Name</th>
                                    <th>File Size</th>
                                </tr>
                                <?php foreach ($files as $file): ?>
                                <tr>
                                    <td><?php echo $file['label']; ?></td>
                                    <td><?php echo $file['name']; ?></td>
                                    <td><?php echo $file['size']; ?> MB</td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
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
        
        <h2>5️⃣ Key Points</h2>
        
        <div class="example">
            <ul>
                <li><strong>Bitrate Matters:</strong> Higher bitrate = better quality but larger file</li>
                <li><strong>Format Choice:</strong> MP4 for compatibility, WebM for open source</li>
                <li><strong>Audio:</strong> Don't forget to set audio bitrate</li>
                <li><strong>Processing Time:</strong> Conversion takes time, be patient</li>
                <li><strong>Quality Presets:</strong> Use appropriate presets for your use case</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="04_audio_extraction.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>