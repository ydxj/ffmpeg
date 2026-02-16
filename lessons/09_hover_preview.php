<?php
$videoFile = '../assets/video.mp4';
$thumbnailFile = '../assets/thumbnail.jpg';
$videoExists = file_exists($videoFile);
$thumbnailExists = file_exists($thumbnailFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson 9: Hover Preview (YouTube Style)</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;600;700&display=swap');

        :root {
            --ink: #111827;
            --muted: #6b7280;
            --panel: #ffffff;
            --accent: #0ea5e9;
            --accent-deep: #0f766e;
            --edge: #e5e7eb;
            --shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: 'IBM Plex Sans', 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 10% 20%, #cffafe 0%, transparent 35%),
                        radial-gradient(circle at 80% 10%, #fde68a 0%, transparent 40%),
                        linear-gradient(135deg, #f8fafc 0%, #ecfeff 100%);
            color: var(--ink);
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: var(--panel);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 32px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: "";
            position: absolute;
            top: -80px;
            right: -120px;
            width: 260px;
            height: 260px;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(15, 118, 110, 0.05));
            border-radius: 50%;
            filter: blur(10px);
        }

        .back-link a {
            color: var(--accent-deep);
            text-decoration: none;
            font-weight: 600;
        }

        h1 {
            margin: 16px 0 8px;
            font-size: clamp(2rem, 4vw, 3rem);
        }

        h2 {
            margin-top: 32px;
            font-size: 1.5rem;
        }

        p {
            color: var(--muted);
            line-height: 1.7;
        }

        .callout {
            background: #f0fdfa;
            border-left: 4px solid var(--accent-deep);
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
        }

        .code {
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            background: #0f172a;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 12px;
            overflow-x: auto;
            font-size: 0.95rem;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .preview-card {
            border: 1px solid var(--edge);
            border-radius: 16px;
            overflow: hidden;
            background: #0f172a;
            color: white;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.2);
            position: relative;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeUp 0.6s ease forwards;
        }

        .preview-card:nth-child(2) { animation-delay: 0.05s; }
        .preview-card:nth-child(3) { animation-delay: 0.1s; }
        .preview-card:nth-child(4) { animation-delay: 0.15s; }

        .preview-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.3);
        }

        .preview-media {
            position: relative;
            background: #0f172a;
        }

        .preview-media video {
            width: 100%;
            height: auto;
            display: block;
        }

        .preview-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 16px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.9) 100%);
            transition: opacity 0.3s ease;
        }

        .preview-card.is-playing .preview-overlay {
            opacity: 0;
        }

        .preview-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #bae6fd;
        }

        .preview-title {
            font-size: 1.1rem;
            margin: 6px 0 0;
        }

        .preview-footer {
            padding: 14px 16px 16px;
            background: #111827;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
            font-size: 0.9rem;
            color: #cbd5f5;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(14, 165, 233, 0.18);
            color: #e0f2fe;
            font-weight: 600;
            font-size: 0.75rem;
            margin-top: 6px;
        }

        .warning {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 16px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            position: relative;
            width: 90%;
            max-width: 1000px;
            background: #0f172a;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }

        .modal-video {
            width: 100%;
            height: auto;
            display: block;
            background: #000;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.7);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
            z-index: 1001;
        }

        .modal-close:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .modal-info {
            padding: 16px;
            background: #111827;
            color: #e2e8f0;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0 0 8px;
        }

        .modal-description {
            font-size: 0.95rem;
            color: #cbd5f5;
            margin: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 700px) {
            body { padding: 16px; }
            .container { padding: 24px; }
            
            .modal-content {
                width: 95%;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-link">
            <a href="../index.php">Back to course</a>
        </div>

        <h1>Lesson 9: Hover Preview (YouTube Style)</h1>
        <p>Build a lightweight preview interaction where hovering a thumbnail plays a muted clip. This mimics how YouTube reveals a quick preview without clicking.</p>

        <?php if (!$videoExists || !$thumbnailExists): ?>
            <div class="warning">
                <strong>Missing assets.</strong> Please add <code>../assets/video.mp4</code> and <code>../assets/thumbnail.jpg</code>.
            </div>
        <?php endif; ?>

        <h2>1) The Core Idea</h2>
        <div class="callout">
            Use a normal <code>&lt;video&gt;</code> tag with <code>poster</code>, keep it muted, and play on hover. Reset time on mouse leave to keep a snappy loop.
        </div>

        <div class="code">
&lt;div class="preview-card" data-start="6"&gt;
    &lt;video muted playsinline preload="metadata" poster="../assets/thumbnail.jpg"&gt;
        &lt;source src="../assets/video.mp4" type="video/mp4"&gt;
    &lt;/video&gt;
&lt;/div&gt;
        </div>

        <h2>2) Live Examples</h2>
        <p>Hover each thumbnail to preview a different moment. On touch devices, tap once to play and tap again to stop.</p>

        <div class="preview-grid">
            <div class="preview-card" data-preview-card data-start="0" data-title="Intro Scene" data-description="Quick look at the first moments.">
                <div class="preview-media">
                    <video muted playsinline preload="metadata" poster="../assets/thumbnail.jpg">
                        <source src="../assets/video.mp4" type="video/mp4">
                    </video>
                    <div class="preview-overlay">
                        <div class="preview-label">Hover Preview</div>
                        <div class="preview-title">Intro Scene</div>
                        <span class="badge">Start 0s</span>
                    </div>
                </div>
                <div class="preview-footer">Quick look at the first moments.</div>
            </div>

            <div class="preview-card" data-preview-card data-start="5" data-title="Chapter Highlight" data-description="Jump straight to the action.">
                <div class="preview-media">
                    <video muted playsinline preload="metadata" poster="../assets/thumbnail.jpg">
                        <source src="../assets/video.mp4" type="video/mp4">
                    </video>
                    <div class="preview-overlay">
                        <div class="preview-label">Hover Preview</div>
                        <div class="preview-title">Chapter Highlight</div>
                        <span class="badge">Start 5s</span>
                    </div>
                </div>
                <div class="preview-footer">Jump straight to the action.</div>
            </div>

            <div class="preview-card" data-preview-card data-start="10" data-title="Midpoint Glimpse" data-description="Preview the middle section.">
                <div class="preview-media">
                    <video muted playsinline preload="metadata" poster="../assets/thumbnail.jpg">
                        <source src="../assets/video.mp4" type="video/mp4">
                    </video>
                    <div class="preview-overlay">
                        <div class="preview-label">Hover Preview</div>
                        <div class="preview-title">Midpoint Glimpse</div>
                        <span class="badge">Start 10s</span>
                    </div>
                </div>
                <div class="preview-footer">Preview the middle section.</div>
            </div>

            <div class="preview-card" data-preview-card data-start="15" data-title="Final Teaser" data-description="End-of-video tease.">
                <div class="preview-media">
                    <video muted playsinline preload="metadata" poster="../assets/thumbnail.jpg">
                        <source src="../assets/video.mp4" type="video/mp4">
                    </video>
                    <div class="preview-overlay">
                        <div class="preview-label">Hover Preview</div>
                        <div class="preview-title">Final Teaser</div>
                        <span class="badge">Start 15s</span>
                    </div>
                </div>
                <div class="preview-footer">End-of-video tease.</div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" id="modalClose">&times;</button>
            <video class="modal-video" id="modalVideo" controls playsinline>
                <source src="../assets/video.mp4" type="video/mp4">
            </video>
            <div class="modal-info">
                <h3 class="modal-title" id="modalTitle">Video Title</h3>
                <p class="modal-description" id="modalDescription">Video description</p>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('videoModal');
        const modalVideo = document.getElementById('modalVideo');
        const modalClose = document.getElementById('modalClose');
        const modalTitle = document.getElementById('modalTitle');
        const modalDescription = document.getElementById('modalDescription');
        const cards = document.querySelectorAll('[data-preview-card]');

        // Modal control functions
        const openModal = (title, description, startTime = 0) => {
            modalTitle.textContent = title;
            modalDescription.textContent = description;
            modalVideo.currentTime = startTime;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            modalVideo.play();
        };

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            modalVideo.pause();
        };

        // Modal close button and background click
        modalClose.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });

        // Card interactions
        cards.forEach((card) => {
            const video = card.querySelector('video');
            const start = parseFloat(card.dataset.start || '0');
            const title = card.dataset.title || 'Untitled';
            const description = card.dataset.description || '';

            const playPreview = () => {
                if (!video) {
                    return;
                }
                video.currentTime = start;
                video.play();
                card.classList.add('is-playing');
            };

            const stopPreview = () => {
                if (!video) {
                    return;
                }
                video.pause();
                video.currentTime = start;
                card.classList.remove('is-playing');
            };

            card.addEventListener('mouseenter', playPreview);
            card.addEventListener('mouseleave', stopPreview);

            // Click to open modal
            card.addEventListener('click', (e) => {
                if (window.matchMedia('(hover: none)').matches) {
                    // Touch device behavior
                    if (video.paused) {
                        playPreview();
                    } else {
                        stopPreview();
                    }
                } else {
                    // Desktop - open modal on click
                    openModal(title, description, start);
                }
            });
        });
    </script>
</body>
</html>
