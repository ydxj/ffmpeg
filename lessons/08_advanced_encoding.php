<?php
/**
 * Lesson 8: Advanced Encoding Techniques
 * 
 * In this lesson, you'll learn:
 * - How to use custom FFmpeg parameters
 * - How to create multiple quality outputs
 * - How to optimize for streaming
 * - How to handle advanced encoding options
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
    <title>Lesson 8: Advanced Encoding</title>
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
        
        <h1>⚙️ Lesson 8: Advanced Encoding Techniques</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Understanding H.264 encoding options</li>
                <li>Creating multiple quality tiers</li>
                <li>Optimizing for streaming platforms</li>
                <li>Advanced format configuration</li>
                <li>Batch processing with progress</li>
                <li>Performance optimization techniques</li>
            </ul>
        </div>
        
        <h2>1️⃣ H.264 Format Configuration</h2>
        
        <table>
            <tr>
                <th>Setting</th>
                <th>Method</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Bitrate</td>
                <td>setKiloBitrate()</td>
                <td>Video bitrate in Kbps (e.g., 2500)</td>
            </tr>
            <tr>
                <td>Audio Bitrate</td>
                <td>setAudioKiloBitrate()</td>
                <td>Audio bitrate in Kbps (e.g., 128)</td>
            </tr>
            <tr>
                <td>Audio Channels</td>
                <td>setAudioChannels()</td>
                <td>Number of channels (1 = mono, 2 = stereo)</td>
            </tr>
            <tr>
                <td>Preset</td>
                <td>setPreset()</td>
                <td>ultrafast to veryslow (quality/speed)</td>
            </tr>
            <tr>
                <td>CRF</td>
                <td>setCrf()</td>
                <td>Quality (0-51, default 23, lower = better)</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Premium Quality Encoding</h3>
            <div class="code">
$format = new X264();

// High quality settings
$format->setKiloBitrate(8000);      // 8 Mbps for 4K
$format->setAudioKiloBitrate(192);  // High quality audio
$format->setAudioChannels(2);       // Stereo

// Set quality preset (veryslow = best quality, slower processing)
$format->setPreset('slow');

// CRF for quality (lower = better, 23 is default)
$format->setCrf(18);  // Higher quality than default

$video = $ffmpeg->open('input.mp4');
$video->save($format, 'output_premium.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Fast/Streaming Quality Encoding</h3>
            <div class="code">
$format = new X264();

// Fast encoding for streaming
$format->setKiloBitrate(2500);      // HD streaming
$format->setAudioKiloBitrate(128);  // Standard audio
$format->setAudioChannels(2);

// Fast preset (faster = less quality, quicker processing)
$format->setPreset('faster');

// Moderate quality
$format->setCrf(24);

$video = $ffmpeg->open('input.mp4');
$video->save($format, 'output_streaming.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Create Multi-Quality Hierarchy</h3>
            <div class="code">
$qualityTiers = [
    'low' => [
        'bitrate' => 800,
        'audio' => 64,
        'preset' => 'faster',
        'crf' => 26,
        'description' => 'Mobile, slow connections'
    ],
    'medium' => [
        'bitrate' => 2500,
        'audio' => 128,
        'preset' => 'medium',
        'crf' => 23,
        'description' => 'Web streaming, tablets'
    ],
    'high' => [
        'bitrate' => 5000,
        'audio' => 192,
        'preset' => 'slow',
        'crf' => 20,
        'description' => 'HD quality, desktops'
    ],
    'premium' => [
        'bitrate' => 10000,
        'audio' => 256,
        'preset' => 'slower',
        'crf' => 18,
        'description' => '4K, archival quality'
    ]
];

foreach ($qualityTiers as $tier => $config) {
    try {
        $video = $ffmpeg->open('input.mp4');
        
        // Configure format
        $format = new X264();
        $format->setKiloBitrate($config['bitrate']);
        $format->setAudioKiloBitrate($config['audio']);
        $format->setAudioChannels(2);
        $format->setPreset($config['preset']);
        $format->setCrf($config['crf']);
        
        $output = 'outputs/video_' . $tier . '.mp4';
        $video->save($format, $output);
        
        echo "Generated: " . $tier . " - " . $config['description'] . "&lt;br&gt;";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
            </div>
        </div>
        
        <div class="example">
            <h3>Streaming Optimization (HLS/Adaptive)</h3>
            <div class="code">
// Optimized for streaming platforms (YouTube, Vimeo, etc.)
$format = new X264();

// YouTube recommended bitrates
$format->setKiloBitrate(8000);      // For 4K
//$format->setKiloBitrate(4500);     // For 1080p
//$format->setKiloBitrate(2500);     // For 720p

$format->setAudioKiloBitrate(128);
$format->setAudioChannels(2);

// Use frame rate
$format->setFramerate(30);

// Preset for platform processing
$format->setPreset('medium');

$video = $ffmpeg->open('input.mp4');
$video->save($format, 'output_youtube.mp4');
            </div>
        </div>
        
        <div class="example">
            <h3>Batch Processing with Error Handling</h3>
            <div class="code">
class BatchVideoProcessor {
    private $ffmpeg;
    private $outputDir;
    
    public function __construct($ffmpeg, $outputDir) {
        $this->ffmpeg = $ffmpeg;
        $this->outputDir = $outputDir;
    }
    
    public function processVideos($inputFiles, $quality = 'medium') {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($inputFiles as $inputFile) {
            try {
                $video = $this->ffmpeg->open($inputFile);
                
                $format = new X264();
                $this->configureQuality($format, $quality);
                
                $filename = basename($inputFile, '.mp4');
                $output = $this->outputDir . $filename . '_' . $quality . '.mp4';
                
                $video->save($format, $output);
                $results['success']++;
                
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = $inputFile . ': ' . $e->getMessage();
            }
        }
        
        return $results;
    }
    
    private function configureQuality($format, $quality) {
        switch ($quality) {
            case 'low':
                $format->setKiloBitrate(800);
                $format->setAudioKiloBitrate(64);
                $format->setPreset('fast');
                break;
            case 'high':
                $format->setKiloBitrate(5000);
                $format->setAudioKiloBitrate(192);
                $format->setPreset('slow');
                break;
            default: // medium
                $format->setKiloBitrate(2500);
                $format->setAudioKiloBitrate(128);
                $format->setPreset('medium');
        }
    }
}

// Usage
$processor = new BatchVideoProcessor($ffmpeg, 'outputs/');
$files = ['video1.mp4', 'video2.mp4', 'video3.mp4'];
$results = $processor->processVideos($files, 'medium');

echo "Success: " . $results['success'] . " Failed: " . $results['failed'];
            </div>
        </div>
        
        <h2>3️⃣ Encoding Presets Comparison</h2>
        
        <table>
            <tr>
                <th>Preset</th>
                <th>Speed</th>
                <th>Quality</th>
                <th>File Size</th>
                <th>Best For</th>
            </tr>
            <tr>
                <td>ultrafast</td>
                <td>Very Fast</td>
                <td>Lower</td>
                <td>Larger</td>
                <td>Real-time streaming</td>
            </tr>
            <tr>
                <td>faster</td>
                <td>Fast</td>
                <td>Low</td>
                <td>Large</td>
                <td>Quick processing</td>
            </tr>
            <tr>
                <td>fast</td>
                <td>Moderate</td>
                <td>Low-Medium</td>
                <td>Medium</td>
                <td>Web usage</td>
            </tr>
            <tr>
                <td>medium</td>
                <td>Balanced</td>
                <td>Medium</td>
                <td>Medium</td>
                <td>General purpose (default)</td>
            </tr>
            <tr>
                <td>slow</td>
                <td>Slow</td>
                <td>High</td>
                <td>Smaller</td>
                <td>High quality</td>
            </tr>
            <tr>
                <td>slower</td>
                <td>Very Slow</td>
                <td>Very High</td>
                <td>Very Small</td>
                <td>Archival/Premium</td>
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
                <button class="button" onclick="loadExample('streaming')">Streaming Quality</button>
                <button class="button" onclick="loadExample('premium')">Premium Quality</button>
                <button class="button" onclick="loadExample('multi_tier')">Multi-Tier Output</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'streaming') {
                try {
                    $output = $outputDir . 'streaming_optimized.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $format = new X264();
                        $format->setKiloBitrate(2500);
                        $format->setAudioKiloBitrate(128);
                        $format->setAudioChannels(2);
                        $format->setPreset('medium');
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Streaming Quality Encoding</h3>
                            <p><strong>Video Bitrate:</strong> 2500 Kbps</p>
                            <p><strong>Audio Bitrate:</strong> 128 Kbps</p>
                            <p><strong>Preset:</strong> Medium (balanced speed/quality)</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Best For:</strong> Web streaming, YouTube, Vimeo</p>
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
            } elseif ($action === 'premium') {
                try {
                    $output = $outputDir . 'premium_quality.mp4';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $format = new X264();
                        $format->setKiloBitrate(5000);
                        $format->setAudioKiloBitrate(192);
                        $format->setAudioChannels(2);
                        $format->setPreset('slow');
                        $format->setCrf(18);
                        $video->save($format, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / (1024 * 1024), 2);
                        ?>
                        <div class="result">
                            <h3>✓ Premium Quality Encoding</h3>
                            <p><strong>Video Bitrate:</strong> 5000 Kbps</p>
                            <p><strong>Audio Bitrate:</strong> 192 Kbps</p>
                            <p><strong>Preset:</strong> Slow (high quality)</p>
                            <p><strong>CRF:</strong> 18 (very high quality)</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> MB</p>
                            <p><strong>Best For:</strong> Archival, professional use</p>
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
            } elseif ($action === 'multi_tier') {
                try {
                    $tiers = [
                        'low' => ['bitrate' => 800, 'audio' => 64, 'label' => 'Low (Mobile)'],
                        'medium' => ['bitrate' => 2500, 'audio' => 128, 'label' => 'Medium (Web)'],
                        'high' => ['bitrate' => 5000, 'audio' => 192, 'label' => 'High (HD)'],
                    ];
                    
                    $results = [];
                    foreach ($tiers as $key => $config) {
                        $output = $outputDir . 'tier_' . $key . '.mp4';
                        if (!file_exists($output)) {
                            $video = $ffmpeg->open($videoFile);
                            $format = new X264();
                            $format->setKiloBitrate($config['bitrate']);
                            $format->setAudioKiloBitrate($config['audio']);
                            $format->setAudioChannels(2);
                            $format->setPreset('medium');
                            $video->save($format, $output);
                        }
                        
                        if (file_exists($output)) {
                            $size = round(filesize($output) / (1024 * 1024), 2);
                            $results[] = [
                                'tier' => $config['label'],
                                'bitrate' => $config['bitrate'] . ' Kbps',
                                'size' => $size
                            ];
                        }
                    }
                    
                    if (!empty($results)) {
                        ?>
                        <div class="result">
                            <h3>✓ Multi-Tier Encoding Complete</h3>
                            <p>Created 3 quality levels for adaptive streaming:</p>
                            <table>
                                <tr>
                                    <th>Tier</th>
                                    <th>Bitrate</th>
                                    <th>File Size</th>
                                </tr>
                                <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?php echo $r['tier']; ?></td>
                                    <td><?php echo $r['bitrate']; ?></td>
                                    <td><?php echo $r['size']; ?> MB</td>
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
        
        <h2>5️⃣ Key Points & Best Practices</h2>
        
        <div class="example">
            <ul>
                <li><strong>Quality vs Speed:</strong> Choose preset based on your time constraints</li>
                <li><strong>CRF over Bitrate:</strong> CRF gives better quality per file size</li>
                <li><strong>Audio Quality:</strong> Don't skimp on audio bitrate</li>
                <li><strong>Multi-Tier Strategy:</strong> Create multiple quality versions for adaptive streaming</li>
                <li><strong>Testing:</strong> Always test encoding settings on sample videos first</li>
                <li><strong>Batch Processing:</strong> Use error handling for large batch jobs</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
            <h2>🎓 Course Complete!</h2>
            <p>You've learned all the essential FFmpeg techniques. Now practice by creating your own video processing applications!</p>
            <a href="../index.php" class="button">← Back to Home</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>