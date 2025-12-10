<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php if (isset($student) && is_array($student)): ?>
    <div class="min-h-screen max-w-md mx-auto bg-white">
        <!-- Header -->
        <div class="bg-blue-600 text-white px-4 py-4">
            <h1 class="text-lg font-semibold">Face Registration</h1>
        </div>
        
        <!-- Student Info -->
        <div class="p-4">
            <div class="flex items-center space-x-4 mb-6">
                <img src="<?php echo htmlspecialchars($student['image'] ?? '/public/default-avatar.png'); ?>" 
                     alt="Student Photo"
                     onerror="this.src='/public/default-avatar.png'"
                     class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                <div>
                    <div class="text-sm font-semibold text-gray-900">
                        <?php echo htmlspecialchars($student['student_name'] ?? 'N/A'); ?>
                    </div>
                    <div class="text-xs text-gray-500">Form: <?php echo htmlspecialchars($student['form_no'] ?? 'N/A'); ?></div>
                    <div class="text-xs text-gray-500">Class: <?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></div>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-amber-900 font-medium mb-2">Instructions:</p>
                <ul class="text-xs text-amber-800 space-y-1 ml-4 list-disc">
                    <li>Position your face within the guide</li>
                    <li>Ensure good lighting</li>
                    <li>Keep steady for 1-2 seconds</li>
                    <li>Look directly at the camera</li>
                </ul>
            </div>
            
            <!-- Capture Frame -->
            <div class="">
                <iframe id="captureFrame"
                        src="https://face.nafish.me/frame/capture" 
                        class="w-full border-0" allow="camera; microphone; autoplay"></iframe>
            </div>
            
            <!-- Actions -->
            <form method="POST" action="/third-party/register">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id'] ?? ''); ?>">
                <input type="hidden" name="form_no" value="<?php echo htmlspecialchars($student['form_no'] ?? ''); ?>">
                <input type="hidden" name="face_data" id="face_data" value="">
                
                <div class="flex flex-col space-y-3">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-full font-medium hover:bg-blue-700 transition">
                        Complete Registration
                    </button>
                    <button type="button" 
                            onclick="history.back()"
                            class="w-full bg-gray-100 text-gray-700 py-3 rounded-full font-medium hover:bg-gray-200 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="min-h-screen max-w-md mx-auto bg-white flex items-center justify-center p-4">
        <div class="text-center">
            <h2 class="text-lg font-semibold text-red-600 mb-2">Invalid Student Data</h2>
            <p class="text-sm text-gray-600">Unable to load student information. Please try again.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        const iframe = document.getElementById('captureFrame');
        
        // Keep track of last successful height response
        let lastHeightTime = 0;
        let lastHeight = 0;
        
        // Minimum and default heights (mobile-first)
        const MIN_HEIGHT = 300;
        const DEFAULT_RATIO = 0.60; // 60% of viewport height
        
        function applyFallbackHeight() {
            const fallback = Math.max(MIN_HEIGHT, Math.round(window.innerHeight * DEFAULT_RATIO));
            iframe.style.height = fallback + 'px';
            lastHeight = fallback;
        }
        
        // Request the iframe to send its height via postMessage.
        // The iframe page must listen for {type: 'getHeight'} and reply with {type:'resize', height: ...}
        function requestIframeHeight() {
            try {
                if (iframe.contentWindow) {
                    iframe.contentWindow.postMessage({ type: 'getHeight' }, '*');
                }
            } catch (e) {
                // ignore
            }
        }
        
        // Listen for messages from iframe
        window.addEventListener('message', function(event) {
            const d = event.data || {};
            if (d.type === 'resize' && d.height) {
                iframe.style.height = d.height + 'px';
                lastHeight = d.height;
                lastHeightTime = Date.now();
                return;
            }
            
            if (d.type === 'face_captured' && d.image) {
                document.getElementById('face_data').value = d.image;
                return;
            }
        });
        
        // On iframe load, apply fallback and request height
        iframe.addEventListener('load', function() {
            applyFallbackHeight();
            requestIframeHeight();
        });
        
        // Periodically request height if we haven't received a recent height
        setInterval(function() {
            const now = Date.now();
            // If we haven't received a height in 2s, request it
            if (now - lastHeightTime > 2000) {
                requestIframeHeight();
            }
            // If we never received any height after 5s, ensure fallback remains
            if (now - lastHeightTime > 5000) {
                applyFallbackHeight();
            }
        }, 700);
        
        // On parent window resize re-request iframe height and adjust fallback
        window.addEventListener('resize', function() {
            applyFallbackHeight();
            requestIframeHeight();
        });
    </script>
</body>
</html>
