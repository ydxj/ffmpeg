<?php
/**
 * Lesson 4: Audio Extraction & Conversion
 * 
 * In this lesson, you'll learn:
 * - How to extract audio from videos
 * - How to convert to different audio formats (MP3, AAC, OGG, FLAC)
 * - How to adjust audio bitrate and sample rate
 * - How to process audio properties
 */

require '../vendor/autoload.php';

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Audio\Aac;
use FFMpeg\Format\Audio\Flac;
use FFMpeg\Format\Audio\Vorbis;

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
    <title>Lesson 4: Audio Extraction</title>
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
        
        <h1>🔊 Lesson 4: Audio Extraction & Conversion</h1>
        
        <div class="example">
            <h2>What You'll Learn</h2>
            <ul>
                <li>Extracting audio tracks from videos</li>
                <li>Converting to MP3 format</li>
                <li>Converting to AAC format</li>
                <li>Converting to OGG Vorbis format</li>
                <li>Converting to FLAC (lossless)</li>
                <li>Adjusting bitrate and sample rate</li>
                <li>Creating audio-only files</li>
            </ul>
        </div>
        
        <h2>1️⃣ Supported Audio Formats</h2>
        
        <table>
            <tr>
                <th>Format</th>
                <th>Class</th>
                <th>Quality</th>
                <th>Compression</th>
                <th>Best For</th>
            </tr>
            <tr>
                <td>MP3</td>
                <td>Mp3</td>
                <td>Lossy</td>
                <td>Good</td>
                <td>Most compatible, music</td>
            </tr>
            <tr>
                <td>AAC</td>
                <td>Aac</td>
                <td>Lossy</td>
                <td>Better than MP3</td>
                <td>Apple/iTunes, mobile</td>
            </tr>
            <tr>
                <td>OGG Vorbis</td>
                <td>Vorbis</td>
                <td>Lossy</td>
                <td>Variable Quality</td>
                <td>Open source, web</td>
            </tr>
            <tr>
                <td>FLAC</td>
                <td>Flac</td>
                <td>Lossless</td>
                <td>Good</td>
                <td>High quality archival</td>
            </tr>
        </table>
        
        <h2>2️⃣ Code Examples</h2>
        
        <div class="example">
            <h3>Extract Audio as MP3</h3>
            <div class="code">
use FFMpeg\Format\Audio\Mp3;

$video = $ffmpeg->open('video.mp4');
$audio = new Mp3();

// Set audio bitrate
$audio->setAudioKiloBitrate(192);

// Set sample rate (44100 Hz is standard)
$audio->setAudioChannels(2);

// Save extracted audio
$video->save($audio, 'output.mp3');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Audio as AAC</h3>
            <div class="code">
use FFMpeg\Format\Audio\Aac;

$video = $ffmpeg->open('video.mp4');
$audio = new Aac();

$audio->setAudioKiloBitrate(128);
$audio->setAudioChannels(2);

$video->save($audio, 'output.aac');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Audio as OGG Vorbis</h3>
            <div class="code">
use FFMpeg\Format\Audio\Vorbis;

$video = $ffmpeg->open('video.mp4');
$audio = new Vorbis();

$audio->setAudioKiloBitrate(160);
$audio->setAudioChannels(2);

$video->save($audio, 'output.ogg');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract Audio as FLAC (Lossless)</h3>
            <div class="code">
use FFMpeg\Format\Audio\Flac;

$video = $ffmpeg->open('video.mp4');
$audio = new Flac();

// FLAC is lossless, no quality loss
$audio->setAudioChannels(2);

$video->save($audio, 'output.flac');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract with Different Bitrates</h3>
            <div class="code">
function extractAudioInMultipleBitrates($ffmpeg, $videoFile, $outputDir) {
    $video = $ffmpeg->open($videoFile);
    
    $bitrates = [
        '64' => 'low_quality',
        '128' => 'medium_quality',
        '192' => 'high_quality',
        '320' => 'very_high_quality'
    ];
    
    foreach ($bitrates as $bitrate => $label) {
        try {
            $audio = new Mp3();
            $audio->setAudioKiloBitrate($bitrate);
            
            $output = $outputDir . 'audio_' . $label . '.mp3';
            $video->save($audio, $output);
            
            echo "Extracted: " . $label . " (" . $bitrate . " kbps)&lt;br&gt;";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

extractAudioInMultipleBitrates($ffmpeg, 'video.mp4', 'outputs/');
            </div>
        </div>
        
        <div class="example">
            <h3>Extract and Trim Audio</h3>
            <div class="code">
use FFMpeg\Coordinate\TimeCode;

$video = $ffmpeg->open('video.mp4');

// Apply filters to trim audio
$video->filters()
    ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds(30))
    ->synchronize();

$audio = new Mp3();
$audio->setAudioKiloBitrate(192);

// Save trimmed audio (first 30 seconds)
$video->save($audio, 'output_trimmed.mp3');
            </div>
        </div>
        
        <h2>3️⃣ Audio Bitrate Guidelines</h2>
        
        <table>
            <tr>
                <th>Bitrate</th>
                <th>Quality</th>
                <th>File Size (per minute)</th>
                <th>Use Case</th>
            </tr>
            <tr>
                <td>64 kbps</td>
                <td>Low</td>
                <td>~480 KB</td>
                <td>Speech, podcasts</td>
            </tr>
            <tr>
                <td>128 kbps</td>
                <td>Medium</td>
                <td>~960 KB</td>
                <td>Background music, streaming</td>
            </tr>
            <tr>
                <td>192 kbps</td>
                <td>High</td>
                <td>~1.4 MB</td>
                <td>Music, good quality</td>
            </tr>
            <tr>
                <td>256-320 kbps</td>
                <td>Very High</td>
                <td>~1.9-2.4 MB</td>
                <td>Audiophile, archival</td>
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
                <button class="button" onclick="loadExample('extract_mp3')">Extract as MP3</button>
                <button class="button" onclick="loadExample('extract_aac')">Extract as AAC</button>
                <button class="button" onclick="loadExample('multi_audio')">Multiple Formats</button>
            </div>
            
            <?php
            $action = $_GET['action'] ?? 'menu';
            
            if ($action === 'extract_mp3') {
                try {
                    $output = $outputDir . 'audio_extracted.mp3';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $audio = new Mp3();
                        $audio->setAudioKiloBitrate(192);
                        $video->save($audio, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / 1024, 2);
                        ?>
                        <div class="result">
                            <h3>✓ Audio Extracted as MP3</h3>
                            <p><strong>Format:</strong> MP3 (MPEG Audio)</p>
                            <p><strong>Bitrate:</strong> 192 kbps</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> KB</p>
                            <p><strong>File Name:</strong> audio_extracted.mp3</p>
                            <p style="margin-top: 15px;">
                                <audio controls style="width: 100%; max-width: 400px;">
                                    <source src="<?php echo str_replace('c:\\xampp\\htdocs\\PHPWSS\\', '../../', $output); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </p>
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
            } elseif ($action === 'extract_aac') {
                try {
                    $output = $outputDir . 'audio_extracted.aac';
                    if (!file_exists($output)) {
                        $video = $ffmpeg->open($videoFile);
                        $audio = new Aac();
                        $audio->setAudioKiloBitrate(128);
                        $video->save($audio, $output);
                    }
                    
                    if (file_exists($output)) {
                        $size = round(filesize($output) / 1024, 2);
                        ?>
                        <div class="result">
                            <h3>✓ Audio Extracted as AAC</h3>
                            <p><strong>Format:</strong> AAC (Advanced Audio Codec)</p>
                            <p><strong>Bitrate:</strong> 128 kbps</p>
                            <p><strong>File Size:</strong> <?php echo $size; ?> KB</p>
                            <p><strong>File Name:</strong> audio_extracted.aac</p>
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
            } elseif ($action === 'multi_audio') {
                try {
                    $formats = [
                        'mp3' => ['class' => new Mp3(), 'label' => 'MP3 (192 kbps)', 'bitrate' => 192],
                        'aac' => ['class' => new Aac(), 'label' => 'AAC (128 kbps)', 'bitrate' => 128],
                    ];
                    
                    $results = [];
                    foreach ($formats as $ext => $config) {
                        try {
                            $output = $outputDir . 'audio_export.' . $ext;
                            if (!file_exists($output)) {
                                $video = $ffmpeg->open($videoFile);
                                $config['class']->setAudioKiloBitrate($config['bitrate']);
                                $video->save($config['class'], $output);
                            }
                            
                            if (file_exists($output)) {
                                $size = round(filesize($output) / 1024, 2);
                                $results[] = [
                                    'format' => $ext,
                                    'label' => $config['label'],
                                    'size' => $size,
                                    'file' => $output
                                ];
                            }
                        } catch (Exception $e) {
                            echo "Error with " . $ext . ": " . $e->getMessage();
                        }
                    }
                    
                    if (!empty($results)) {
                        ?>
                        <div class="result">
                            <h3>✓ Multiple Audio Formats Extracted</h3>
                            <table>
                                <tr>
                                    <th>Format</th>
                                    <th>Bitrate</th>
                                    <th>File Size</th>
                                    <th>Player</th>
                                </tr>
                                <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?php echo strtoupper($r['format']); ?></td>
                                    <td><?php echo $r['label']; ?></td>
                                    <td><?php echo $r['size']; ?> KB</td>
                                    <td>
                                        <audio controls style="width: 100%; max-width: 250px;">
                                            <source src="<?php echo str_replace('c:\\xampp\\htdocs\\PHPWSS\\', '../../', $r['file']); ?>" type="audio/<?php echo ($r['format'] === 'aac' ? 'aac' : 'mpeg'); ?>">
                                        </audio>
                                    </td>
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
                <li><strong>Bitrate Selection:</strong> Higher bitrate = better quality but larger file</li>
                <li><strong>Format Compatibility:</strong> MP3 works everywhere, AAC for Apple devices</li>
                <li><strong>Lossless Option:</strong> Use FLAC for highest quality archival</li>
                <li><strong>Audio Channels:</strong> 2 channels for stereo, 1 for mono</li>
                <li><strong>Processing:</strong> Extraction can take time depending on file size</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="../index.php" class="button">← Back to Lessons</a>
            <a href="05_video_resizing.php" class="button">Next Lesson →</a>
        </div>
    </div>
    
    <script>
        function loadExample(example) {
            window.location.href = '?action=' + example;
        }
    </script>
</body>
</html>