<?php
session_start();

$sessionKey = 'montage_assets';
if (!isset($_SESSION[$sessionKey])) {
    $_SESSION[$sessionKey] = [
        'video' => '',
        'audio' => '',
        'second_video' => '',
        'timeline' => [],
    ];
}

$ffmpegBinary = 'C:/ffmpeg/bin/ffmpeg.exe';
$outputDir = '../outputs/montage/';
$uploadDir = $outputDir . 'uploads/';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function resolve_path($input)
{
    $input = trim((string) $input);
    if ($input == '') {
        return '';
    }

    if (preg_match('/^[A-Za-z]:\\\\/', $input) || strpos($input, '/') === 0) {
        return $input;
    }

    return __DIR__ . '/' . $input;
}

function ensure_extension($filename, $extension)
{
    $filename = trim((string) $filename);
    if ($filename == '') {
        return '';
    }

    if (strtolower(substr($filename, -strlen($extension))) === $extension) {
        return $filename;
    }

    return $filename . $extension;
}

function run_command($command)
{
    $output = [];
    $exitCode = 0;
    exec($command . ' 2>&1', $output, $exitCode);

    return [$exitCode, implode("\n", $output)];
}

function format_seconds($value)
{
    $value = (float) $value;
    $formatted = number_format($value, 3, '.', '');

    return rtrim(rtrim($formatted, '0'), '.');
}

function first_non_empty(...$values)
{
    foreach ($values as $value) {
        if ($value !== '') {
            return $value;
        }
    }

    return '';
}

function get_session_asset($key)
{
    global $sessionKey;

    return $_SESSION[$sessionKey][$key] ?? '';
}

function set_session_asset($key, $value)
{
    global $sessionKey;

    if ($value !== '') {
        $_SESSION[$sessionKey][$key] = $value;
    }
}

function to_web_path($path)
{
    if ($path === '') {
        return '';
    }

    $normalized = str_replace('\\', '/', $path);
    $rootPrefix = 'C:/xampp/htdocs/PHPWSS/ffmpeg/';

    if (strpos($normalized, $rootPrefix) === 0) {
        return substr($normalized, strlen($rootPrefix));
    }

    return $normalized;
}

function add_timeline_event($label, $start, $end, $file, $type)
{
    global $sessionKey;

    $_SESSION[$sessionKey]['timeline'][] = [
        'label' => $label,
        'start' => $start,
        'end' => $end,
        'file' => $file,
        'type' => $type,
        'created' => date('H:i:s'),
    ];
}

function get_timeline_events()
{
    global $sessionKey;

    return $_SESSION[$sessionKey]['timeline'] ?? [];
}

function handle_upload($fieldName, $uploadDir, &$errors)
{
    if (!isset($_FILES[$fieldName])) {
        return '';
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload failed for ' . $fieldName . '.';
        return '';
    }

    $name = basename($file['name']);
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    $target = $uploadDir . time() . '_' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $errors[] = 'Could not save uploaded file for ' . $fieldName . '.';
        return '';
    }

    return $target;
}

$errors = [];
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action !== 'save_assets' && !file_exists($ffmpegBinary)) {
        $errors[] = 'FFmpeg binary not found at ' . $ffmpegBinary;
    } else {
        $timestamp = date('Ymd_His');

        if ($action === 'save_assets') {
            $mainVideoUpload = handle_upload('main_video_upload', $uploadDir, $errors);
            $mainAudioUpload = handle_upload('main_audio_upload', $uploadDir, $errors);
            $secondVideoUpload = handle_upload('main_second_video_upload', $uploadDir, $errors);

            if ($mainVideoUpload !== '') {
                set_session_asset('video', $mainVideoUpload);
            }
            if ($mainAudioUpload !== '') {
                set_session_asset('audio', $mainAudioUpload);
            }
            if ($secondVideoUpload !== '') {
                set_session_asset('second_video', $secondVideoUpload);
            }

            $mainVideoPath = resolve_path($_POST['main_video_path'] ?? '');
            $mainAudioPath = resolve_path($_POST['main_audio_path'] ?? '');
            $secondVideoPath = resolve_path($_POST['main_second_video_path'] ?? '');

            if ($mainVideoPath !== '') {
                if (file_exists($mainVideoPath)) {
                    set_session_asset('video', $mainVideoPath);
                } else {
                    $errors[] = 'Main video path not found.';
                }
            }
            if ($mainAudioPath !== '') {
                if (file_exists($mainAudioPath)) {
                    set_session_asset('audio', $mainAudioPath);
                } else {
                    $errors[] = 'Main audio path not found.';
                }
            }
            if ($secondVideoPath !== '') {
                if (file_exists($secondVideoPath)) {
                    set_session_asset('second_video', $secondVideoPath);
                } else {
                    $errors[] = 'Second video path not found.';
                }
            }

            if (!$errors) {
                $results[] = [
                    'label' => 'Library saved',
                    'file' => '',
                    'log' => 'Uploads and paths saved for this session.',
                ];
            }
        }

        if ($action === 'replace_audio') {
            $uploadedVideo = handle_upload('video_upload', $uploadDir, $errors);
            $uploadedAudio = handle_upload('audio_upload', $uploadDir, $errors);
            $videoInput = first_non_empty(
                $uploadedVideo,
                resolve_path($_POST['video_path'] ?? ''),
                get_session_asset('video')
            );
            $audioInput = first_non_empty(
                $uploadedAudio,
                resolve_path($_POST['audio_path'] ?? ''),
                get_session_asset('audio')
            );
            $outputName = ensure_extension($_POST['output_name'] ?? "audio_swap_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($videoInput === '') {
                $errors[] = 'Please provide a video path or upload a video.';
            }
            if ($audioInput === '') {
                $errors[] = 'Please provide an audio path or upload an audio file.';
            }
            if (!file_exists($videoInput)) {
                $errors[] = 'Video file not found.';
            }
            if (!file_exists($audioInput)) {
                $errors[] = 'Audio file not found.';
            }

            if (!$errors) {
                $command = sprintf(
                    '"%s" -y -i %s -i %s -map 0:v:0 -map 1:a:0 -c:v copy -c:a aac -shortest %s',
                    $ffmpegBinary,
                    escapeshellarg($videoInput),
                    escapeshellarg($audioInput),
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Audio replaced', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Audio replaced', 0, 0, $outputPath, 'audio');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to replace audio.';
                }
            }
        }

        if ($action === 'trim_audio') {
            $uploadedSource = handle_upload('audio_source_upload', $uploadDir, $errors);
            $sourceInput = first_non_empty(
                $uploadedSource,
                resolve_path($_POST['audio_source'] ?? ''),
                get_session_asset('audio'),
                get_session_asset('video')
            );
            $startTime = (float) ($_POST['start_time'] ?? 0);
            $endTime = (float) ($_POST['end_time'] ?? 0);
            $outputName = ensure_extension($_POST['audio_output_name'] ?? "audio_clip_{$timestamp}", '.m4a');
            $outputPath = $outputDir . $outputName;

            if ($sourceInput === '') {
                $errors[] = 'Please provide an audio/video path or upload a file.';
            }
            if (!file_exists($sourceInput)) {
                $errors[] = 'Audio source file not found.';
            }
            if ($endTime <= $startTime) {
                $errors[] = 'End time must be greater than start time.';
            }

            if (!$errors) {
                $command = sprintf(
                    '"%s" -y -ss %s -to %s -i %s -map 0:a:0 -vn -c:a aac %s',
                    $ffmpegBinary,
                    escapeshellarg((string) $startTime),
                    escapeshellarg((string) $endTime),
                    escapeshellarg($sourceInput),
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Audio trimmed', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Audio trim', $startTime, $endTime, $outputPath, 'audio');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to trim audio.';
                }
            }
        }

        if ($action === 'concat_videos') {
            $uploadedFirst = handle_upload('first_video_upload', $uploadDir, $errors);
            $uploadedSecond = handle_upload('second_video_upload', $uploadDir, $errors);
            $firstVideo = first_non_empty(
                $uploadedFirst,
                resolve_path($_POST['first_video'] ?? ''),
                get_session_asset('video')
            );
            $secondVideo = first_non_empty(
                $uploadedSecond,
                resolve_path($_POST['second_video'] ?? ''),
                get_session_asset('second_video')
            );
            $outputName = ensure_extension($_POST['video_output_name'] ?? "video_join_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($firstVideo === '') {
                $errors[] = 'Please provide the first video path or upload a file.';
            }
            if ($secondVideo === '') {
                $errors[] = 'Please provide the second video path or upload a file.';
            }
            if (!file_exists($firstVideo)) {
                $errors[] = 'First video file not found.';
            }
            if (!file_exists($secondVideo)) {
                $errors[] = 'Second video file not found.';
            }

            if (!$errors) {
                $listFile = $outputDir . "concat_list_{$timestamp}.txt";
                $listContent = "file '" . str_replace("'", "\\'", $firstVideo) . "'\n";
                $listContent .= "file '" . str_replace("'", "\\'", $secondVideo) . "'\n";
                file_put_contents($listFile, $listContent);

                $command = sprintf(
                    '"%s" -y -f concat -safe 0 -i %s -c:v libx264 -c:a aac -movflags +faststart %s',
                    $ffmpegBinary,
                    escapeshellarg($listFile),
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Videos concatenated', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Join videos', 0, 0, $outputPath, 'video');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to concatenate videos.';
                }
            }
        }

        if ($action === 'trim_video') {
            $uploadedVideo = handle_upload('trim_video_upload', $uploadDir, $errors);
            $videoInput = first_non_empty(
                $uploadedVideo,
                resolve_path($_POST['trim_video_path'] ?? ''),
                get_session_asset('video')
            );
            $startTime = (float) ($_POST['trim_video_start'] ?? 0);
            $endTime = (float) ($_POST['trim_video_end'] ?? 0);
            $outputName = ensure_extension($_POST['trim_video_output'] ?? "video_clip_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($videoInput === '') {
                $errors[] = 'Please provide a video path or upload a video.';
            }
            if (!file_exists($videoInput)) {
                $errors[] = 'Video file not found.';
            }
            if ($endTime <= $startTime) {
                $errors[] = 'End time must be greater than start time.';
            }

            if (!$errors) {
                $command = sprintf(
                    '"%s" -y -ss %s -to %s -i %s -c:v libx264 -c:a aac %s',
                    $ffmpegBinary,
                    escapeshellarg((string) $startTime),
                    escapeshellarg((string) $endTime),
                    escapeshellarg($videoInput),
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Video trimmed', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Video trim', $startTime, $endTime, $outputPath, 'video');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to trim video.';
                }
            }
        }

        if ($action === 'mute_audio') {
            $uploadedVideo = handle_upload('mute_video_upload', $uploadDir, $errors);
            $videoInput = first_non_empty(
                $uploadedVideo,
                resolve_path($_POST['mute_video_path'] ?? ''),
                get_session_asset('video')
            );
            $outputName = ensure_extension($_POST['mute_video_output'] ?? "video_muted_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($videoInput === '') {
                $errors[] = 'Please provide a video path or upload a video.';
            }
            if (!file_exists($videoInput)) {
                $errors[] = 'Video file not found.';
            }

            if (!$errors) {
                $command = sprintf(
                    '"%s" -y -i %s -c:v copy -an %s',
                    $ffmpegBinary,
                    escapeshellarg($videoInput),
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Audio muted', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Mute audio', 0, 0, $outputPath, 'audio');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to mute audio.';
                }
            }
        }

        if ($action === 'fade_media') {
            $uploadedVideo = handle_upload('fade_video_upload', $uploadDir, $errors);
            $videoInput = first_non_empty(
                $uploadedVideo,
                resolve_path($_POST['fade_video_path'] ?? ''),
                get_session_asset('video')
            );
            $duration = (float) ($_POST['fade_duration'] ?? 0);
            $fadeIn = (float) ($_POST['fade_in'] ?? 0);
            $fadeOut = (float) ($_POST['fade_out'] ?? 0);
            $outputName = ensure_extension($_POST['fade_output'] ?? "video_fade_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($videoInput === '') {
                $errors[] = 'Please provide a video path or upload a video.';
            }
            if (!file_exists($videoInput)) {
                $errors[] = 'Video file not found.';
            }
            if ($duration <= 0) {
                $errors[] = 'Duration must be greater than 0.';
            }
            if ($fadeIn < 0 || $fadeOut < 0) {
                $errors[] = 'Fade values must be 0 or greater.';
            }
            if ($duration > 0 && $fadeOut > $duration) {
                $errors[] = 'Fade out must be shorter than duration.';
            }

            if (!$errors) {
                $fadeOutStart = max(0, $duration - $fadeOut);
                $durationArg = format_seconds($duration);
                $fadeInArg = format_seconds($fadeIn);
                $fadeOutArg = format_seconds($fadeOut);
                $fadeOutStartArg = format_seconds($fadeOutStart);
                $command = sprintf(
                    '"%s" -y -i %s -t %s -vf "fade=t=in:st=0:d=%s,fade=t=out:st=%s:d=%s" -af "afade=t=in:st=0:d=%s,afade=t=out:st=%s:d=%s" -c:v libx264 -c:a aac %s',
                    $ffmpegBinary,
                    escapeshellarg($videoInput),
                    escapeshellarg($durationArg),
                    $fadeInArg,
                    $fadeOutStartArg,
                    $fadeOutArg,
                    $fadeInArg,
                    $fadeOutStartArg,
                    $fadeOutArg,
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Fade applied', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Fade in/out', 0, $duration, $outputPath, 'video');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to apply fade.';
                }
            }
        }

        if ($action === 'add_music') {
            $uploadedVideo = handle_upload('music_video_upload', $uploadDir, $errors);
            $uploadedMusic = handle_upload('music_audio_upload', $uploadDir, $errors);
            $videoInput = first_non_empty(
                $uploadedVideo,
                resolve_path($_POST['music_video_path'] ?? ''),
                get_session_asset('video')
            );
            $musicInput = first_non_empty(
                $uploadedMusic,
                resolve_path($_POST['music_audio_path'] ?? ''),
                get_session_asset('audio')
            );
            $musicVolume = (float) ($_POST['music_volume'] ?? 0.35);
            $outputName = ensure_extension($_POST['music_output'] ?? "video_music_{$timestamp}", '.mp4');
            $outputPath = $outputDir . $outputName;

            if ($videoInput === '') {
                $errors[] = 'Please provide a video path or upload a video.';
            }
            if ($musicInput === '') {
                $errors[] = 'Please provide a music path or upload a file.';
            }
            if (!file_exists($videoInput)) {
                $errors[] = 'Video file not found.';
            }
            if (!file_exists($musicInput)) {
                $errors[] = 'Music file not found.';
            }
            if ($musicVolume < 0 || $musicVolume > 1) {
                $errors[] = 'Music volume must be between 0 and 1.';
            }

            if (!$errors) {
                $musicVolumeArg = format_seconds($musicVolume);
                $command = sprintf(
                    '"%s" -y -i %s -i %s -filter_complex "[1:a]volume=%s[a1];[0:a][a1]amix=inputs=2:duration=shortest:dropout_transition=2[aout]" -map 0:v:0 -map "[aout]" -c:v copy -c:a aac -shortest %s',
                    $ffmpegBinary,
                    escapeshellarg($videoInput),
                    escapeshellarg($musicInput),
                    $musicVolumeArg,
                    escapeshellarg($outputPath)
                );

                [$exitCode, $outputText] = run_command($command);
                if ($exitCode === 0 && file_exists($outputPath)) {
                    $results[] = ['label' => 'Music mixed', 'file' => $outputPath, 'log' => $outputText];
                    add_timeline_event('Music bed', 0, 0, $outputPath, 'music');
                } else {
                    $errors[] = $outputText ?: 'FFmpeg failed to add background music.';
                }
            }
        }
    }
}

function display_path($path)
{
    if ($path == '') {
        return '';
    }

    return str_replace('\\', '/', $path);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson 10: Montage Audio and Video</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@400;600;700&display=swap');

        :root {
            --ink: #0f172a;
            --muted: #475569;
            --accent: #0f766e;
            --accent-2: #f97316;
            --panel: #ffffff;
            --surface: rgba(255, 255, 255, 0.92);
            --border: #e2e8f0;
            --shadow: 0 25px 60px rgba(15, 23, 42, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 20% 0%, rgba(14, 165, 233, 0.18), transparent 40%),
                        radial-gradient(circle at 90% 10%, rgba(251, 146, 60, 0.2), transparent 35%),
                        linear-gradient(130deg, #f8fafc 0%, #ecfeff 100%);
            color: var(--ink);
            min-height: 100vh;
            padding: 28px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: var(--panel);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 32px;
            border: 1px solid rgba(148, 163, 184, 0.25);
        }

        .studio {
            display: grid;
            grid-template-columns: minmax(0, 2.4fr) minmax(260px, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        .preview-panel {
            background: #0b1120;
            border-radius: 18px;
            padding: 16px;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .preview-frame {
            background: #0f172a;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 16 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-frame video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .preview-placeholder {
            color: #94a3b8;
            text-align: center;
            font-size: 0.95rem;
        }

        .preview-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #cbd5f5;
            margin-top: 12px;
            font-size: 0.85rem;
            flex-wrap: wrap;
            gap: 8px;
        }

        .preview-meta span {
            background: rgba(14, 116, 144, 0.22);
            padding: 6px 12px;
            border-radius: 999px;
        }

        .inspector {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px;
        }

        .inspector h3 {
            margin-top: 0;
        }

        .track-list {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .track-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            background: rgba(15, 118, 110, 0.08);
            font-size: 0.85rem;
        }

        .track-row strong {
            color: var(--accent);
        }

        .timeline-panel {
            margin-top: 24px;
            background: #0b1120;
            border-radius: 18px;
            padding: 16px;
            color: #cbd5f5;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .timeline-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .timeline-grid {
            display: grid;
            gap: 10px;
        }

        .timeline-track {
            background: #111827;
            border-radius: 12px;
            padding: 10px;
        }

        .timeline-track-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .timeline-blocks {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .timeline-block {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            background: rgba(14, 116, 144, 0.35);
            border: 1px solid rgba(14, 116, 144, 0.6);
        }

        .timeline-block.video {
            background: rgba(59, 130, 246, 0.3);
            border-color: rgba(59, 130, 246, 0.6);
        }

        .timeline-block.audio {
            background: rgba(34, 197, 94, 0.3);
            border-color: rgba(34, 197, 94, 0.6);
        }

        .timeline-block.music {
            background: rgba(249, 115, 22, 0.35);
            border-color: rgba(249, 115, 22, 0.6);
        }

        .timeline-empty {
            color: #64748b;
            font-size: 0.9rem;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .top-bar a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        h1 {
            margin: 16px 0 8px;
            font-size: clamp(2.1rem, 4vw, 3rem);
        }

        p {
            color: var(--muted);
            line-height: 1.7;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
        }

        .panel h2 {
            margin-top: 0;
            font-size: 1.35rem;
        }

        .panel h2 span {
            color: var(--accent-2);
        }

        label {
            display: block;
            font-weight: 600;
            margin: 12px 0 6px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'DM Mono', 'Courier New', monospace;
            background: #ffffff;
        }

        .row {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }

        .button {
            display: inline-block;
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 600;
            margin-top: 16px;
            cursor: pointer;
        }

        .button:hover {
            background: #0d9488;
        }

        .note {
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .callout {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 16px;
        }

        .error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 16px;
            color: #b91c1c;
        }

        .result-list {
            margin-top: 20px;
        }

        .result-card {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .result-card a {
            color: #7dd3fc;
            text-decoration: none;
        }

        .code {
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 0.85rem;
            background: rgba(15, 23, 42, 0.6);
            padding: 8px 10px;
            border-radius: 10px;
            margin-top: 8px;
            white-space: pre-wrap;
        }

        .pill {
            display: inline-block;
            background: rgba(15, 118, 110, 0.12);
            color: var(--accent);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 6px;
        }

        .tips {
            margin-top: 24px;
            padding: 16px;
            border-radius: 14px;
            border: 1px dashed var(--border);
            background: #f8fafc;
        }

        .tips ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <a href="../index.php">← Back to Lessons</a>
            <span class="pill">Montage Toolkit</span>
        </div>

        <h1>Lesson 10: Montage Audio and Video</h1>
        <p>Upload your main clip once, preview it live, then build your montage with fast tools underneath. Provide absolute paths (like C:/videos/intro.mp4) or paths relative to this lesson folder (like ../assets/video.mp4).</p>

        <?php if ($errors): ?>
            <div class="error">
                <strong>Something went wrong:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($results): ?>
            <div class="result-list">
                <?php foreach ($results as $result): ?>
                    <?php $relative = str_replace('\\', '/', $result['file']); ?>
                    <?php $relative = str_replace('C:/xampp/htdocs/PHPWSS/ffmpeg/', '', $relative); ?>
                    <div class="result-card">
                        <strong><?php echo htmlspecialchars($result['label']); ?></strong>
                        <?php if ($result['file'] !== ''): ?>
                            <div>
                                Output: <a href="<?php echo htmlspecialchars($relative); ?>" target="_blank"><?php echo htmlspecialchars(display_path($relative)); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="code"><?php echo htmlspecialchars($result['log']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        $sessionVideo = get_session_asset('video');
        $sessionAudio = get_session_asset('audio');
        $sessionSecondVideo = get_session_asset('second_video');
        ?>

        <?php
        $previewSrc = to_web_path($sessionVideo);
        $previewLabel = $sessionVideo !== '' ? basename($sessionVideo) : 'No video loaded';
        $timelineEvents = get_timeline_events();
        ?>

        <div class="studio">
            <div class="preview-panel">
                <div class="preview-frame">
                    <?php if ($previewSrc !== ''): ?>
                        <video id="previewVideo" src="<?php echo htmlspecialchars($previewSrc); ?>" controls></video>
                    <?php else: ?>
                        <div class="preview-placeholder">Upload a main video to preview your montage.</div>
                    <?php endif; ?>
                </div>
                <div class="preview-meta">
                    <span>Preview Frame</span>
                    <span><?php echo htmlspecialchars($previewLabel); ?></span>
                </div>
            </div>

            <div class="inspector">
                <h3>Montage Library</h3>
                <p>Upload once, then reuse below. Paths still work for power users.</p>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_assets">

                    <label for="main_video_path">Main video path</label>
                    <input type="text" id="main_video_path" name="main_video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                    <label for="main_video_upload">Or upload main video</label>
                    <input type="file" id="main_video_upload" name="main_video_upload" accept="video/*">

                    <label for="main_audio_path">Main audio path</label>
                    <input type="text" id="main_audio_path" name="main_audio_path" placeholder="C:/media/audio.mp3" value="<?php echo htmlspecialchars($sessionAudio); ?>">

                    <label for="main_audio_upload">Or upload main audio</label>
                    <input type="file" id="main_audio_upload" name="main_audio_upload" accept="audio/*">

                    <label for="main_second_video_path">Second video path</label>
                    <input type="text" id="main_second_video_path" name="main_second_video_path" placeholder="C:/media/part2.mp4" value="<?php echo htmlspecialchars($sessionSecondVideo); ?>">

                    <label for="main_second_video_upload">Or upload second video</label>
                    <input type="file" id="main_second_video_upload" name="main_second_video_upload" accept="video/*">

                    <button class="button" type="submit">Save to Library</button>
                    <div class="note">Files stay available during this browser session.</div>
                </form>

                <div class="track-list">
                    <div class="track-row">
                        <strong>Video</strong>
                        <span><?php echo htmlspecialchars($sessionVideo ? basename($sessionVideo) : 'Not set'); ?></span>
                    </div>
                    <div class="track-row">
                        <strong>Audio</strong>
                        <span><?php echo htmlspecialchars($sessionAudio ? basename($sessionAudio) : 'Not set'); ?></span>
                    </div>
                    <div class="track-row">
                        <strong>Second</strong>
                        <span><?php echo htmlspecialchars($sessionSecondVideo ? basename($sessionSecondVideo) : 'Not set'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="timeline-panel">
            <div class="timeline-header">
                <strong>Montage Timeline</strong>
                <span class="pill">Auto-build from actions</span>
            </div>
            <?php if (!$timelineEvents): ?>
                <div class="timeline-empty">No clips yet. Run a tool below to populate the timeline.</div>
            <?php else: ?>
                <div class="timeline-grid">
                    <div class="timeline-track">
                        <div class="timeline-track-title">Video Track</div>
                        <div class="timeline-blocks">
                            <?php foreach ($timelineEvents as $event): ?>
                                <?php if ($event['type'] === 'video'): ?>
                                    <div class="timeline-block video">
                                        <?php echo htmlspecialchars($event['label']); ?>
                                        <?php if ($event['end'] > 0): ?>
                                            (<?php echo htmlspecialchars(format_seconds($event['start'])); ?> - <?php echo htmlspecialchars(format_seconds($event['end'])); ?>s)
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="timeline-track">
                        <div class="timeline-track-title">Audio Track</div>
                        <div class="timeline-blocks">
                            <?php foreach ($timelineEvents as $event): ?>
                                <?php if ($event['type'] === 'audio'): ?>
                                    <div class="timeline-block audio">
                                        <?php echo htmlspecialchars($event['label']); ?>
                                        <?php if ($event['end'] > 0): ?>
                                            (<?php echo htmlspecialchars(format_seconds($event['start'])); ?> - <?php echo htmlspecialchars(format_seconds($event['end'])); ?>s)
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="timeline-track">
                        <div class="timeline-track-title">Music Track</div>
                        <div class="timeline-blocks">
                            <?php foreach ($timelineEvents as $event): ?>
                                <?php if ($event['type'] === 'music'): ?>
                                    <div class="timeline-block music">
                                        <?php echo htmlspecialchars($event['label']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid">
            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>1.</span> Replace Audio Track</h2>
                <p>Keep the video visuals, swap in a new audio file.</p>
                <input type="hidden" name="action" value="replace_audio">

                <label for="video_path">Video file</label>
                <input type="text" id="video_path" name="video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="video_upload">Or upload video</label>
                <input type="file" id="video_upload" name="video_upload" accept="video/*">

                <label for="audio_path">Audio file</label>
                <input type="text" id="audio_path" name="audio_path" placeholder="C:/media/new-audio.mp3" value="<?php echo htmlspecialchars($sessionAudio); ?>">

                <label for="audio_upload">Or upload audio</label>
                <input type="file" id="audio_upload" name="audio_upload" accept="audio/*">

                <label for="output_name">Output filename</label>
                <input type="text" id="output_name" name="output_name" placeholder="audio_swap_custom.mp4">

                <button class="button" type="submit">Replace Audio</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>2.</span> Trim Audio Segment</h2>
                <p>Cut a clean section of audio from a file or video.</p>
                <input type="hidden" name="action" value="trim_audio">

                <label for="audio_source">Audio or video file</label>
                <input type="text" id="audio_source" name="audio_source" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionAudio ?: $sessionVideo); ?>">

                <label for="audio_source_upload">Or upload audio/video</label>
                <input type="file" id="audio_source_upload" name="audio_source_upload" accept="audio/*,video/*">

                <div class="row">
                    <div>
                        <label for="start_time">Start (seconds)</label>
                        <input type="number" step="0.1" id="start_time" name="start_time" placeholder="5.0" required>
                    </div>
                    <div>
                        <label for="end_time">End (seconds)</label>
                        <input type="number" step="0.1" id="end_time" name="end_time" placeholder="12.5" required>
                    </div>
                </div>

                <label for="audio_output_name">Output filename</label>
                <input type="text" id="audio_output_name" name="audio_output_name" placeholder="audio_clip_custom.m4a">

                <button class="button" type="submit">Trim Audio</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>3.</span> Join Two Videos</h2>
                <p>Concatenate two videos into a single MP4.</p>
                <input type="hidden" name="action" value="concat_videos">

                <label for="first_video">First video</label>
                <input type="text" id="first_video" name="first_video" placeholder="C:/media/part1.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="first_video_upload">Or upload first video</label>
                <input type="file" id="first_video_upload" name="first_video_upload" accept="video/*">

                <label for="second_video">Second video</label>
                <input type="text" id="second_video" name="second_video" placeholder="C:/media/part2.mp4" value="<?php echo htmlspecialchars($sessionSecondVideo); ?>">

                <label for="second_video_upload">Or upload second video</label>
                <input type="file" id="second_video_upload" name="second_video_upload" accept="video/*">

                <label for="video_output_name">Output filename</label>
                <input type="text" id="video_output_name" name="video_output_name" placeholder="video_join_custom.mp4">

                <button class="button" type="submit">Join Videos</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>4.</span> Trim Video Clip</h2>
                <p>Cut a clean video segment with audio.</p>
                <input type="hidden" name="action" value="trim_video">

                <label for="trim_video_path">Video file</label>
                <input type="text" id="trim_video_path" name="trim_video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="trim_video_upload">Or upload video</label>
                <input type="file" id="trim_video_upload" name="trim_video_upload" accept="video/*">

                <div class="row">
                    <div>
                        <label for="trim_video_start">Start (seconds)</label>
                        <input type="number" step="0.1" id="trim_video_start" name="trim_video_start" placeholder="2.0" required>
                    </div>
                    <div>
                        <label for="trim_video_end">End (seconds)</label>
                        <input type="number" step="0.1" id="trim_video_end" name="trim_video_end" placeholder="9.5" required>
                    </div>
                </div>

                <label for="trim_video_output">Output filename</label>
                <input type="text" id="trim_video_output" name="trim_video_output" placeholder="video_clip_custom.mp4">

                <button class="button" type="submit">Trim Video</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>5.</span> Mute Original Audio</h2>
                <p>Remove all audio from a video in one click.</p>
                <input type="hidden" name="action" value="mute_audio">

                <label for="mute_video_path">Video file</label>
                <input type="text" id="mute_video_path" name="mute_video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="mute_video_upload">Or upload video</label>
                <input type="file" id="mute_video_upload" name="mute_video_upload" accept="video/*">

                <label for="mute_video_output">Output filename</label>
                <input type="text" id="mute_video_output" name="mute_video_output" placeholder="video_muted_custom.mp4">

                <button class="button" type="submit">Mute Audio</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>6.</span> Fade In + Out</h2>
                <p>Add smooth fade in/out for video and audio.</p>
                <input type="hidden" name="action" value="fade_media">

                <label for="fade_video_path">Video file</label>
                <input type="text" id="fade_video_path" name="fade_video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="fade_video_upload">Or upload video</label>
                <input type="file" id="fade_video_upload" name="fade_video_upload" accept="video/*">

                <div class="row">
                    <div>
                        <label for="fade_duration">Clip duration (seconds)</label>
                        <input type="number" step="0.1" id="fade_duration" name="fade_duration" placeholder="12.0" required>
                    </div>
                    <div>
                        <label for="fade_in">Fade in (seconds)</label>
                        <input type="number" step="0.1" id="fade_in" name="fade_in" placeholder="0.6" required>
                    </div>
                    <div>
                        <label for="fade_out">Fade out (seconds)</label>
                        <input type="number" step="0.1" id="fade_out" name="fade_out" placeholder="0.8" required>
                    </div>
                </div>

                <label for="fade_output">Output filename</label>
                <input type="text" id="fade_output" name="fade_output" placeholder="video_fade_custom.mp4">

                <button class="button" type="submit">Apply Fade</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>

            <form method="post" class="panel" enctype="multipart/form-data">
                <h2><span>7.</span> Add Background Music</h2>
                <p>Mix a music bed with the original audio.</p>
                <input type="hidden" name="action" value="add_music">

                <label for="music_video_path">Video file</label>
                <input type="text" id="music_video_path" name="music_video_path" placeholder="../assets/video.mp4" value="<?php echo htmlspecialchars($sessionVideo); ?>">

                <label for="music_video_upload">Or upload video</label>
                <input type="file" id="music_video_upload" name="music_video_upload" accept="video/*">

                <label for="music_audio_path">Music file</label>
                <input type="text" id="music_audio_path" name="music_audio_path" placeholder="C:/media/music.mp3" value="<?php echo htmlspecialchars($sessionAudio); ?>">

                <label for="music_audio_upload">Or upload music</label>
                <input type="file" id="music_audio_upload" name="music_audio_upload" accept="audio/*">

                <label for="music_volume">Music volume (0 to 1)</label>
                <input type="number" step="0.05" id="music_volume" name="music_volume" placeholder="0.35" value="0.35">

                <label for="music_output">Output filename</label>
                <input type="text" id="music_output" name="music_output" placeholder="video_music_custom.mp4">

                <button class="button" type="submit">Mix Music</button>
                <div class="note">Output saved to ../outputs/montage/</div>
            </form>
        </div>

        <div class="tips">
            <strong>Tips:</strong>
            <ul>
                <li>For best results, concatenate videos with the same resolution and codec.</li>
                <li>Audio trimming outputs AAC .m4a to keep size small.</li>
                <li>Use absolute paths if your files are outside the project folder.</li>
            </ul>
        </div>
    </div>
    <script>
        const previewVideo = document.getElementById('previewVideo');
        const mainUpload = document.getElementById('main_video_upload');

        if (previewVideo && mainUpload) {
            mainUpload.addEventListener('change', (event) => {
                const file = event.target.files && event.target.files[0];
                if (file) {
                    previewVideo.src = URL.createObjectURL(file);
                    previewVideo.load();
                }
            });
        }
    </script>
</body>
</html>
