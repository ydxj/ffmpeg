<?php
/**
 * Lesson 6: Video Trimming & Segments
 * 
 * In this lesson, you'll learn:
 * - How to trim videos
 * - How to extract specific segments
 * - How to cut from start/end of video
 * - How to work with TimeCode ranges
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
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
    <title>Lesson 6: Video Trimming</title>
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
        
        <h1>✂️ Lesson 6: Video Trimming & Segments</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Trimming videos to specific duration</li>
                <li>Extracting segments from specific start/end times</li>
                <li>Working with TimeCode objects for precise timing</li>
                <li>Creating clips from longer videos</li>
                <li>Batch processing and segmentation</li>
                <li>Duration manipulation</li>
            </ul>
        </div>
        
        <h2>1️⃣ TimeCode Methods Reference</h2>
        
        <table>
            <tr>
                <th>Method</th>
                <th>Parameters</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>TimeCode::fromSeconds()</td>
                <td>Seconds as integer/float</td>
                <td>Create TimeCode from seconds</td>
            </tr>
            <tr>
                <td>TimeCode::fromString()</td>
                <td>String like "00:01:30"</td>
                <td>Create from HH:MM:SS format</td>
            </tr>
            <tr>
                <td>$format->getDuration()</td>
                <td>-</td>
                <td>Get total duration in seconds</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Trim First N Seconds</h3>
            <div class="code">
use FFMpeg\Coordinate\TimeCode;

$video = $ffmpeg->open('input.mp4');

// Trim to first 30 seconds
$video->filters()
    ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds(30))
    ->synchronize();

$format = new X264();
$video->save($format, 'output_first_30s.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Segment from Middle</h3>
            <div class="code">
// Extract from 10 seconds to 30 seconds
$video->filters()
    ->clip(TimeCode::fromSeconds(10), TimeCode::fromSeconds(30))
    ->synchronize();

$video->save(new X264(), 'output_10s_to_30s.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Using Time String Format</h3>
            <div class="code">
// Extract from 00:00:10 to 00:01:30
$video->filters()
    ->clip(TimeCode::fromString('00:00:10'), TimeCode::fromString('00:01:30'))
    ->synchronize();

$video->save(new X264(), 'output_segment.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Remove Last N Seconds</h3>
            <div class="code">
$format = $video->getFormat();
$duration = $format->getDuration();

// Remove last 5 seconds (trim to duration - 5)
$trimTo = $duration - 5;

$video->filters()
    ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds($trimTo))
    ->synchronize();

$video->save(new X264(), 'output_trimmed.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Create Multiple Segments from One Video</h3>
            <div class="code">
function createSegments($ffmpeg, $inputFile, $outputDir, $segmentLength) {
    $video = $ffmpeg->open($inputFile);
    $duration = (int)$video->getFormat()->getDuration();
    
    $segments = [];
    $start = 0;
    $segmentNumber = 1;
    
    while ($start < $duration) {
        $end = min($start + $segmentLength, $duration);
        
        try {
            $video = $ffmpeg->open($inputFile);
            $video->filters()
                ->clip(TimeCode::fromSeconds($start), TimeCode::fromSeconds($end))
                ->synchronize();
            
            $output = $outputDir . 'segment_' . $segmentNumber . '.mp4';
            $video->save(new X264(), $output);
            
            $segments[] = $output;
            $segmentNumber++;
        } catch (Exception $e) {
            echo "Error creating segment: " . $e->getMessage();
        }
        
        $start = $end;
    }
    
    return $segments;
}

// Create 10-second segments
$segments = createSegments($ffmpeg, 'video.mp4', 'outputs/', 10);
            </div>
        </div>
        
        <h2>3️⃣ Live Examples</h2>
        
        <?php if (!$videoExists): ?>
            <div class="error">
                <strong>⚠️ No Video File Found</strong><br>
                Please upload a video file to: <code>../assets/video.mp4</code>
            </div>
        <?php else: ?>
            <?php
            $video = $ffmpeg->open($videoFile);
            $duration = (int)$video->getFormat()->getDuration();
            $trimPoint = min(10, $duration);
            ?>
            
            <div style="margin: 20px 0;">
                <button class="button" onclick="loadExample('trim_10s')">Trim to First 10s</button>
                <button class="button" onclick="loadExample('extract_middle')">Extract Middle</button>
                <button class="button" onclick="loadExample('multiple_segments')">Create Segments</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'trim_10s') {
                try {
                    $output = $outputDir . 'trimmed_10s.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds(10))
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Video Trimmed to 10 Seconds</h3>
                            <p><strong>Duration:</strong> 10 seconds</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>File Name:</strong> trimmed_10s.mp4</p>
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
            } elseif ($action === 'extract_middle') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $duration = (int)$video->getFormat()->getDuration();
                    $quarter = $duration / 4;
                    $threeQuarters = ($duration * 3) / 4;
                    
                    $output = $outputDir . 'middle_segment.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $video->filters()
                            ->clip(TimeCode::fromSeconds($quarter), TimeCode::fromSeconds($threeQuarters))
                            ->synchronize();
                        
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        $segmentDuration = (int)($threeQuarters - $quarter);
                        ?>
                        <div class="result">
                            <h3>✓ Middle Segment Extracted</h3>
                            <p><strong>Duration:</strong> <?php echo $segmentDuration; ?> seconds</p>
                            <p><strong>Start Time:</strong> <?php echo round($quarter); ?>s</p>
                            <p><strong>End Time:</strong> <?php echo round($threeQuarters); ?>s</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>File Name:</strong> middle_segment.mp4</p>
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
            } elseif ($action === 'multiple_segments') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $duration = (int)$video->getFormat()->getDuration();
                    $segmentLength = max(5, min(10, $duration / 3)); // Adapt to video length
                    
                    $segments = [];
                    $start = 0;
                    $segmentNumber = 1;
                    
                    while ($start < $duration && $segmentNumber <= 3) {
                        $end = min($start + $segmentLength, $duration);
                        
                        $output = $outputDir . 'segment_' . $segmentNumber . '.mp4';
                        if (!file_exists($output)) {
                            $video = $ffmpeg->open($videoFile);
                            $video->filters()
                                ->clip(TimeCode::fromSeconds($start), TimeCode::fromSeconds($end))
                                ->synchronize();
                            
                            $format = new X264();
                            $format->setKiloBitrate(2000);
                            $video->save($format, $output);
                        }
                        
                        if (file_exists($output)) {
                            $size = round(filesize($output) / (1024 * 1024), 2);
                            $segDuration = (int)($end - $start);
                            $segments[] = [
                                'number' => $segmentNumber,
                                'start' => round($start),
                                'duration' => $segDuration,
                                'size' => $size
                            ];
                        }
                        
                        $start = $end;
                        $segmentNumber++;
                    }
                    
                    if (!empty($segments)) {
                        ?>
                        <div class="result">
                            <h3>✓ Video Segmented into <?php echo count($segments); ?> Parts</h3>
                            <table>
                                <tr>
                                    <th>Segment</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>File Size</th>
                                </tr>
                                <?php foreach ($segments as $seg): ?>
                                <tr>
                                    <td>Segment <?php echo $seg['number']; ?></td>
                                    <td><?php echo $seg['start']; ?>s</td>
                                    <td><?php echo $seg['duration']; ?>s</td>
                                    <td><?php echo $seg['size']; ?> MB</td>
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
        
        <h2>4️⃣ Key Points</h2>
        
        <div class="example">
            <ul>
                <li><strong>TimeCode Objects:</strong> Always use TimeCode for precise timing</li>
                <li><strong>Synchronize:</strong> Call synchronize() to align audio and video</li>
                <li><strong>Format Strings:</strong> Use HH:MM:SS format for readable timestamps</li>
                <li><strong>Performance:</strong> Trimming re-encodes the video, takes time</li>
                <li><strong>Precision:</strong> Timestamps are frame-accurate with FFmpeg</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="07_filters_effects.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>