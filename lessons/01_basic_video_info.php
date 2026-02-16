<?php
/**
 * Lesson 1: Getting Basic Video Information
 * 
 * In this lesson, you'll learn:
 * - How to load video files with FFmpeg
 * - How to get video metadata (duration, resolution, bitrate)
 * - How to extract format information
 * - How to check if a video is valid
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;

// Get the action from URL parameter
$action = $_GET['action'] ?? 'menu';
$videoFile = '../assets/video.mp4';

// Check if video file exists
$videoExists = file_exists($videoFile);

// Helper functions
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = intval($seconds % 60);
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}

function formatBitrate($bitrate) {
    if ($bitrate >= 1000000) {
        return number_format($bitrate / 1000000, 2) . ' Mbps';
    } elseif ($bitrate >= 1000) {
        return number_format($bitrate / 1000, 2) . ' Kbps';
    }
    return $bitrate . ' bps';
}

// Initialize FFmpeg
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
    <title>Lesson 1: Basic Video Information</title>
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
        
        <h1>📊 Lesson 1: Basic Video Information</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Loading video files using FFmpeg</li>
                <li>Getting video duration</li>
                <li>Extracting resolution (width & height)</li>
                <li>Getting bitrate and codec information</li>
                <li>Checking if video file is valid</li>
                <li>Working with TimeCode objects</li>
            </ul>
        </div>
        
        <h2>1️⃣ Basic Functions Reference</h2>
        
        <table>
            <tr>
                <th>Function</th>
                <th>Description</th>
                <th>Example</th>
            </tr>
            <tr>
                <td><code>$ffmpeg->open()</code></td>
                <td>Opens a video file</td>
                <td><code>$video = $ffmpeg->open('video.mp4')</code></td>
            </tr>
            <tr>
                <td><code>$video->getFormat()</code></td>
                <td>Gets format object with metadata</td>
                <td><code>$format = $video->getFormat()</code></td>
            </tr>
            <tr>
                <td><code>$video->getDuration()</code></td>
                <td>Gets video duration as TimeCode object</td>
                <td><code>$time = $video->getDuration()</code></td>
            </tr>
            <tr>
                <td><code>$format->get('width')</code></td>
                <td>Gets video width in pixels</td>
                <td><code>$width = $format->get('width')</code></td>
            </tr>
            <tr>
                <td><code>$format->get('height')</code></td>
                <td>Gets video height in pixels</td>
                <td><code>$height = $format->get('height')</code></td>
            </tr>
            <tr>
                <td><code>$format->get('bit_rate')</code></td>
                <td>Gets bitrate</td>
                <td><code>$bitrate = $format->get('bit_rate')</code></td>
            </tr>
        </table>
        
        <h2>2️⃣ Function Reference & Examples</h2>
        
        <div class="example">
            <h3>✓ Initialize FFmpeg</h3>
            <div class="code">
&lt;?php
require 'vendor/autoload.php';

use FFMpeg\FFMpeg;

/**
 * FFMpeg::create()
 * Initializes FFmpeg with binary paths
 * 
 * Parameters:
 *   - 'ffmpeg.binaries': Path to ffmpeg.exe
 *   - 'ffprobe.binaries': Path to ffprobe.exe
 * 
 * Returns: FFMpeg instance
 */
$ffmpeg = FFMpeg::create([
    'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
    'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
]);
?&gt;
            </div>
        </div>
        
        <div class="example">
            <h3>✓ Load & Get Video Information</h3>
            <div class="code">
/**
 * $ffmpeg->open($videoPath)
 * Opens a video file and returns Video instance
 * 
 * Parameters:
 *   - $videoPath: Path to video file
 * 
 * Returns: Video instance
 * Throws: Exception if file not found
 */
$video = $ffmpeg->open('video.mp4');

/**
 * $video->getFormat()
 * Returns format/metadata object
 * 
 * Returns: Format instance with all metadata
 */
$format = $video->getFormat();
            </div>
        </div>
        
        <div class="example">
            <h3>✓ Extract Duration</h3>
            <div class="code">
/**
 * $video->getDuration()
 * Gets total video duration as TimeCode object
 * Call ->get() to get the numeric seconds value
 * 
 * Returns: TimeCode instance (call ->get() for seconds)
 *
 * Example usage:
 */
$video = $ffmpeg->open('video.mp4');
$duration = $video->getDuration()->get(); // e.g., 125 (2 minutes 5 seconds)

// Helper function to format nicely
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = intval($seconds % 60);
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}

echo formatDuration($duration); // Output: 00:02:05
            </div>
        </div>
        
        <div class="example">
            <h3>✓ Get Video Dimensions</h3>
            <div class="code">
/**
 * $format->get('width') and $format->get('height')
 * Gets video width and height in pixels
 * 
 * Returns: Integer (width/height)
 */
$video = $ffmpeg->open('video.mp4');
$format = $video->getFormat();

$width = $format->get('width');   // e.g., 1920
$height = $format->get('height'); // e.g., 1080

echo "Resolution: " . $width . "x" . $height; // Output: 1920x1080
            </div>
        </div>
        
        <div class="example">
            <h3>✓ Get Advanced Metadata</h3>
            <div class="code">
/**
 * $format->get($key)
 * Gets any metadata value by key
 * 
 * Common keys:
 *   - 'bit_rate': Bitrate in bps
 *   - 'codec_name': Codec name (h264, vp9, etc)
 *   - 'r_frame_rate': Frame rate (30/1 = 30fps)
 *   - 'nb_frames': Total frames
 */
$bitrate = $format->get('bit_rate');      // e.g., 2500000
$codec = $format->get('codec_name');      // e.g., 'h264'
$frameRate = $format->get('r_frame_rate'); // e.g., '30/1'
$frames = $format->get('nb_frames');      // e.g., 3750

// Format bitrate nicely
function formatBitrate($bitrate) {
    if ($bitrate >= 1000000) {
        return number_format($bitrate / 1000000, 2) . ' Mbps';
    }
    return number_format($bitrate / 1000, 2) . ' Kbps';
}

echo "Bitrate: " . formatBitrate($bitrate); // Output: 2.50 Mbps
            </div>
        </div>
        
        <div class="example">
            <h3>✓ Complete Metadata Function</h3>
            <div class="code">
/**
 * Complete function to extract all important video info
 */
function getVideoInfo($ffmpeg, $videoPath) {
    try {
        $video = $ffmpeg->open($videoPath);
        $format = $video->getFormat();
        $duration = $video->getDuration()->get();
        
        return [
            'filename' => basename($videoPath),
            'duration' => [
                'seconds' => $duration,
                'formatted' => formatDuration($duration),
            ],
            'resolution' => [
                'width' => $format->get('width'),
                'height' => $format->get('height'),
                'display' => $format->get('width') . 'x' . $format->get('height'),
            ],
            'bitrate' => [
                'bps' => $format->get('bit_rate'),
                'formatted' => formatBitrate($format->get('bit_rate')),
            ],
            'codec' => $format->get('codec_name'),
            'frame_rate' => $format->get('r_frame_rate'),
            'frames' => $format->get('nb_frames'),
        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Usage:
$info = getVideoInfo($ffmpeg, 'video.mp4');
            </div>
        </div>
        
        <h2>3️⃣ Live Examples with Your Video</h2>
        
        <?php if (!$videoExists): ?>
            <div class="error">
                <strong>⚠️ No Video File Found</strong><br>
                Expected: <code>../assets/video.mp4</code><br>
                Please upload a video file to the assets folder to see live examples.
            </div>
        <?php elseif ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php else: ?>
            <div style="margin: 20px 0;">
                <button class="button" onclick="loadExample('duration')">Get Duration</button>
                <button class="button" onclick="loadExample('resolution')">Get Resolution</button>
                <button class="button" onclick="loadExample('bitrate')">Get Bitrate</button>
                <button class="button" onclick="loadExample('full_info')">Get All Info</button>
            </div>
            
            <?php
            // Helper functions
            function formatDuration($seconds) {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $secs = intval($seconds % 60);
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            }
            
            function formatBitrate($bitrate) {
                if ($bitrate >= 1000000) {
                    return number_format($bitrate / 1000000, 2) . ' Mbps';
                } elseif ($bitrate >= 1000) {
                    return number_format($bitrate / 1000, 2) . ' Kbps';
                }
                return $bitrate . ' bps';
            }
            
            if ($action === 'duration') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $duration = $video->getDuration()->get();
                    ?>
                    <div class="result">
                        <h3>✓ Video Duration Information</h3>
                        <table>
                            <tr>
                                <td><strong>Duration (Seconds)</strong></td>
                                <td><?php echo number_format($duration, 2); ?> seconds</td>
                            </tr>
                            <tr>
                                <td><strong>Duration (Formatted)</strong></td>
                                <td><?php echo formatDuration($duration); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Minutes</strong></td>
                                <td><?php echo number_format($duration / 60, 2); ?> minutes</td>
                            </tr>
                        </table>
                        
                        <h4 style="margin-top: 20px;">Code Used:</h4>
                        <div class="code">
&lt;?php
$video = $ffmpeg->open('../assets/video.mp4');
$duration = $video->getDuration()->get();

echo formatDuration($duration); // <?php echo formatDuration($duration); ?>
?&gt;
                        </div>
                    </div>
                    <?php
                } catch (Exception $e) {
                    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } elseif ($action === 'resolution') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $format = $video->getFormat();
                    $width = $format->get('width');
                    $height = $format->get('height');
                    ?>
                    <div class="result">
                        <h3>✓ Video Resolution Information</h3>
                        <table>
                            <tr>
                                <td><strong>Width</strong></td>
                                <td><?php echo $width; ?> pixels</td>
                            </tr>
                            <tr>
                                <td><strong>Height</strong></td>
                                <td><?php echo $height; ?> pixels</td>
                            </tr>
                            <tr>
                                <td><strong>Resolution</strong></td>
                                <td><?php echo $width . 'x' . $height; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Aspect Ratio</strong></td>
                                <td><?php echo number_format($width / $height, 2); ?>:1</td>
                            </tr>
                            <tr>
                                <td><strong>Total Pixels</strong></td>
                                <td><?php echo number_format($width * $height); ?></td>
                            </tr>
                        </table>
                        
                        <h4 style="margin-top: 20px;">Code Used:</h4>
                        <div class="code">
&lt;?php
$video = $ffmpeg->open('../assets/video.mp4');
$format = $video->getFormat();
$width = $format->get('width');
$height = $format->get('height');

echo "Resolution: " . $width . "x" . $height; // <?php echo $width . 'x' . $height; ?>
?&gt;
                        </div>
                    </div>
                    <?php
                } catch (Exception $e) {
                    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } elseif ($action === 'bitrate') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $format = $video->getFormat();
                    $bitrate = $format->get('bit_rate');
                    ?>
                    <div class="result">
                        <h3>✓ Video Bitrate Information</h3>
                        <table>
                            <tr>
                                <td><strong>Bitrate (bps)</strong></td>
                                <td><?php echo number_format($bitrate); ?> bps</td>
                            </tr>
                            <tr>
                                <td><strong>Bitrate (Formatted)</strong></td>
                                <td><?php echo formatBitrate($bitrate); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Quality Level</strong></td>
                                <td><?php 
                                    $mbps = $bitrate / 1000000;
                                    if ($mbps > 5) echo "High Quality (4K/Full HD)";
                                    elseif ($mbps > 2.5) echo "Medium Quality (HD)";
                                    elseif ($mbps > 1) echo "Standard Quality (Web)";
                                    else echo "Low Quality (Mobile)";
                                ?></td>
                            </tr>
                        </table>
                        
                        <h4 style="margin-top: 20px;">Code Used:</h4>
                        <div class="code">
&lt;?php
$video = $ffmpeg->open('../assets/video.mp4');
$format = $video->getFormat();
$bitrate = $format->get('bit_rate');

echo formatBitrate($bitrate); // <?php echo formatBitrate($bitrate); ?>
?&gt;
                        </div>
                    </div>
                    <?php
                } catch (Exception $e) {
                    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } elseif ($action === 'full_info') {
                try {
                    $video = $ffmpeg->open($videoFile);
                    $format = $video->getFormat();
                    $duration = $video->getDuration()->get();
                    $width = $format->get('width');
                    $height = $format->get('height');
                    $bitrate = $format->get('bit_rate');
                    $codec = $format->get('codec_name');
                    $frameRate = $format->get('r_frame_rate');
                    $frames = $format->get('nb_frames');
                    ?>
                    <div class="result">
                        <h3>✓ Complete Video Information</h3>
                        <table>
                            <tr>
                                <td><strong>File Name</strong></td>
                                <td>video.mp4</td>
                            </tr>
                            <tr>
                                <td><strong>Duration</strong></td>
                                <td><?php echo formatDuration($duration); ?> (<?php echo number_format($duration, 2); ?>s)</td>
                            </tr>
                            <tr>
                                <td><strong>Resolution</strong></td>
                                <td><?php echo $width . 'x' . $height; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Bitrate</strong></td>
                                <td><?php echo formatBitrate($bitrate); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Video Codec</strong></td>
                                <td><?php echo ucfirst($codec); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Frame Rate</strong></td>
                                <td><?php echo $frameRate; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Frames</strong></td>
                                <td><?php echo number_format($frames); ?> frames</td>
                            </tr>
                            <tr>
                                <td><strong>Aspect Ratio</strong></td>
                                <td><?php echo number_format($width / $height, 2); ?>:1</td>
                            </tr>
                        </table>
                        
                        <h4 style="margin-top: 20px;">Code Used:</h4>
                        <div class="code">
&lt;?php
$video = $ffmpeg->open('../assets/video.mp4');
$format = $video->getFormat();
$duration = $video->getDuration()->get();

$info = [
    'Duration' => formatDuration($duration),
    'Resolution' => $format->get('width') . 'x' . $format->get('height'),
    'Bitrate' => formatBitrate($format->get('bit_rate')),
    'Codec' => $format->get('codec_name'),
    'Frame Rate' => $format->get('r_frame_rate'),
];

foreach ($info as $key => $value) {
    echo $key . ": " . $value . "&lt;br&gt;";
}
?&gt;
                        </div>
                    </div>
                    <?php
                } catch (Exception $e) {
                    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            ?>
        <?php endif; ?>
        
        <h2>4️⃣ Key Concepts You Learned</h2>
        
        <div class="example">
            <ul>
                <li><strong>FFMpeg::create():</strong> Initialize FFmpeg with binary paths</li>
                <li><strong>$ffmpeg->open():</strong> Load a video file and return Video instance</li>
                <li><strong>$video->getFormat():</strong> Get Format object containing all metadata</li>
                <li><strong>$video->getDuration()->get():</strong> Get duration in seconds (call ->get() on TimeCode)</li>
                <li><strong>$format->get('key'):</strong> Access any FFprobe metadata by key (width, height, bit_rate, etc)</li>
                <li><strong>Error Handling:</strong> Always wrap in try-catch for robustness</li>
            </ul>
        </div>
        
        <h2>5️⃣ Common Metadata Keys</h2>
        
        <table style="margin: 20px 0;">
            <tr>
                <th>Key</th>
                <th>Return Type</th>
                <th>Example Value</th>
                <th>Purpose</th>
            </tr>
            <tr>
                <td><code>'bit_rate'</code></td>
                <td>Integer</td>
                <td>2500000</td>
                <td>Bitrate in bits per second</td>
            </tr>
            <tr>
                <td><code>'codec_name'</code></td>
                <td>String</td>
                <td>h264</td>
                <td>Video codec used</td>
            </tr>
            <tr>
                <td><code>'r_frame_rate'</code></td>
                <td>String</td>
                <td>30/1</td>
                <td>Frame rate (frames/second)</td>
            </tr>
            <tr>
                <td><code>'nb_frames'</code></td>
                <td>Integer</td>
                <td>3000</td>
                <td>Total frames in video</td>
            </tr>
            <tr>
                <td><code>'duration'</code></td>
                <td>Float</td>
                <td>100.5</td>
                <td>Duration in seconds (alternative to getDuration()->get())</td>
            </tr>
            <tr>
                <td><code>'width'</code></td>
                <td>Integer</td>
                <td>1920</td>
                <td>Video width in pixels</td>
            </tr>
            <tr>
                <td><code>'height'</code></td>
                <td>Integer</td>
                <td>1080</td>
                <td>Video height in pixels</td>
            </tr>
        </table>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="02_thumbnail_generation.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>