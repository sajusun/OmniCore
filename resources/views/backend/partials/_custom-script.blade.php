{{-- summernote --}}
<script>
    if (typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            placeholder: 'text',
            tabsize: 2,
            height: 200
        });
    }
</script>

{{-- Night Mode / Dark Mode Controller --}}
<script>
    (function () {
        // Enforce dark mode state from localStorage
        function applyDarkMode(isDark) {
            if (isDark) {
                document.body.classList.add('dark-mode');
                document.documentElement.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
                document.documentElement.classList.remove('dark-mode');
            }
        }

        // Initialize state
        var currentMode = localStorage.getItem('omnicore_dark_mode');
        if (currentMode === 'dark') {
            applyDarkMode(true);
        } else if (currentMode === 'light') {
            applyDarkMode(false);
        }

        // Bind click event to all layout-setting toggles
        function setupToggle() {
            var toggles = document.querySelectorAll('.layout-setting, #night-mode-toggle');
            toggles.forEach(function (toggle) {
                // Ensure link does not jump
                toggle.setAttribute('href', 'javascript:void(0);');

                // Remove duplicate handlers if any
                toggle.onclick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var willBeDark = !document.body.classList.contains('dark-mode');
                    applyDarkMode(willBeDark);
                    localStorage.setItem('omnicore_dark_mode', willBeDark ? 'dark' : 'light');
                };
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupToggle);
        } else {
            setupToggle();
        }
    })();
</script>

{{-- Fullscreen Controller --}}
<script>
    (function () {
        function isNativeFullscreen() {
            return !!(
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            );
        }

        function isAnyFullscreen() {
            return isNativeFullscreen() || document.body.classList.contains('fullscreen-window-fallback');
        }

        function updateUI(active) {
            document.body.classList.toggle('fullscreen-mode', active);
            document.body.classList.toggle('fullscreen-window-fallback', !isNativeFullscreen() && active);

            var btns = document.querySelectorAll('.full-screen-link, #fullscreen-toggle');
            btns.forEach(function (btn) {
                btn.setAttribute('title', active ? 'Exit Fullscreen' : 'Toggle Fullscreen');
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        }

        function requestNativeFullscreen() {
            var el = document.documentElement;
            if (el.requestFullscreen) {
                return el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                return el.webkitRequestFullscreen();
            } else if (el.webkitRequestFullScreen) {
                return el.webkitRequestFullScreen();
            } else if (el.mozRequestFullScreen) {
                return el.mozRequestFullScreen();
            } else if (el.msRequestFullscreen) {
                return el.msRequestFullscreen();
            }
            return Promise.reject(new Error('Fullscreen API not supported'));
        }

        function exitNativeFullscreen() {
            if (document.exitFullscreen) {
                return document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                return document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                return document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                return document.msExitFullscreen();
            }
            return Promise.resolve();
        }

        function toggleFullScreen() {
            if (isAnyFullscreen()) {
                if (isNativeFullscreen()) {
                    exitNativeFullscreen().catch(function () {});
                }
                updateUI(false);
            } else {
                var promise = requestNativeFullscreen();
                if (promise && typeof promise.then === 'function') {
                    promise.then(function () {
                        updateUI(true);
                    }).catch(function (err) {
                        console.info('Native fullscreen unavailable or restricted in this container, applying full-window presentation view:', err);
                        updateUI(true);
                    });
                } else {
                    updateUI(true);
                }
            }
        }

        // Listen for native browser fullscreen changes
        var fsEvents = ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'];
        fsEvents.forEach(function (evt) {
            document.addEventListener(evt, function () {
                updateUI(isNativeFullscreen());
            });
        });

        // Listen for ESC key to exit full-window fallback
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                if (document.body.classList.contains('fullscreen-window-fallback')) {
                    updateUI(false);
                }
            }
        });

        // Bind delegated click event across entire document
        document.addEventListener('click', function (e) {
            var target = e.target.closest('.full-screen-link, #fullscreen-toggle');
            if (target) {
                e.preventDefault();
                toggleFullScreen();
            }
        });
    })();
</script>


