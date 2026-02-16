/**
 * Display Profile Handler for HamClock Dashboard
 * Supports: Desktop, Raspberry Pi 7" (1024x600), Tablet, Mobile
 * Auto-detection with manual override option
 */

(function() {
    'use strict';

    const PROFILES = {
        desktop: {
            name: 'desktop',
            columns: 12,
            cellHeight: 80,
            margin: 10,
            minWidth: 1200,
            className: 'desktop-mode'
        },
        raspberry: {
            name: 'raspberry',
            columns: 4,
            cellHeight: 55,
            margin: 4,
            detect: (w, h) => (w === 1024 && h === 600) || (w === 600 && h === 1024) || 
                              (w >= 980 && w <= 1050 && h >= 550 && h <= 650),
            className: 'raspberry-mode'
        },
        tablet: {
            name: 'tablet',
            columns: 6,
            cellHeight: 60,
            margin: 8,
            minWidth: 768,
            maxWidth: 1199,
            className: 'tablet-mode'
        },
        mobile: {
            name: 'mobile',
            columns: 1,
            cellHeight: 50,
            margin: 3,
            maxWidth: 767,
            className: 'mobile-mode'
        }
    };

    let currentProfile = null;
    let manualOverride = localStorage.getItem('gwen_display_profile_override');
    let isInitialized = false;

    function getScreenSize() {
        return {
            width: window.innerWidth,
            height: window.innerHeight,
            availWidth: screen.availWidth,
            availHeight: screen.availHeight
        };
    }

    function detectProfile() {
        // Manual override has priority
        if (manualOverride && PROFILES[manualOverride]) {
            return PROFILES[manualOverride];
        }

        const { width: w, height: h } = getScreenSize();

        // Check specific detections first
        if (PROFILES.raspberry.detect(w, h)) {
            return PROFILES.raspberry;
        }

        // Then by breakpoints
        if (w >= PROFILES.desktop.minWidth) return PROFILES.desktop;
        if (w >= PROFILES.tablet.minWidth) return PROFILES.tablet;
        if (w <= PROFILES.mobile.maxWidth) return PROFILES.mobile;
        
        // Fallback
        return PROFILES.desktop;
    }

    function applyProfile(profile, force = false) {
        if (!profile) return;
        if (currentProfile === profile.name && !force) return;
        
        console.log('[Display] Applying profile:', profile.name);
        
        // Remove old profile classes
        Object.values(PROFILES).forEach(p => {
            document.body.classList.remove(p.className);
        });

        // Add new profile class
        document.body.classList.add(profile.className);
        currentProfile = profile.name;

        // Apply grid settings if grid is ready
        if (window.grid && typeof window.grid.column === 'function') {
            // Disable animation for smoother transition
            const wasAnimated = window.grid.opts.animate;
            window.grid.animate(false);
            
            // Apply new grid configuration
            window.grid.column(profile.columns);
            window.grid.cellHeight(profile.cellHeight);
            window.grid.margin(profile.margin);
            
            // Apply layout for this profile
            applyLayoutForProfile(profile.name);
            
            // Re-enable animation
            if (wasAnimated) {
                setTimeout(() => window.grid.animate(true), 100);
            }
        }

        // Store current profile
        localStorage.setItem('gwen_display_profile', profile.name);
        
        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('displayProfileChanged', { 
            detail: profile 
        }));
    }

    function applyLayoutForProfile(profileName) {
        if (!window.grid) return;
        
        // Mobile: Immer das vorgegebene Layout verwenden (gespeichertes ignorieren)
        if (profileName === 'mobile') {
            console.log('[Display] Mobile: applying hardcoded layout');
        }

        const layouts = 
            raspberry: [
                { id: 'widget-header', x: 0, y: 0, w: 4, h: 1 },
                { id: 'widget-sun', x: 0, y: 1, w: 2, h: 3 },
                { id: 'widget-qth', x: 2, y: 1, w: 2, h: 3 },
                { id: 'widget-bands', x: 0, y: 4, w: 4, h: 2 },
                { id: 'widget-clock', x: 0, y: 6, w: 2, h: 2 },
                { id: 'widget-weather-local', x: 2, y: 6, w: 2, h: 2 },
                { id: 'widget-weather-space', x: 0, y: 8, w: 2, h: 2 },
                { id: 'widget-system', x: 2, y: 8, w: 2, h: 2 },
                { id: 'widget-satellites', x: 0, y: 10, w: 4, h: 3 },
                { id: 'widget-dx', x: 0, y: 13, w: 4, h: 4 },
            ],
            mobile: [
                { id: 'widget-header', x: 0, y: 0, w: 1, h: 1 },
                { id: 'widget-clock', x: 0, y: 1, w: 1, h: 2 },      // ⏰ Uhr direkt nach Header
                { id: 'widget-sun', x: 0, y: 3, w: 1, h: 3 },
                { id: 'widget-qth', x: 0, y: 6, w: 1, h: 3 },
                { id: 'widget-bands', x: 0, y: 9, w: 1, h: 3 },
                { id: 'widget-weather-local', x: 0, y: 12, w: 1, h: 2 },
                { id: 'widget-weather-space', x: 0, y: 14, w: 1, h: 2 },
                { id: 'widget-system', x: 0, y: 16, w: 1, h: 2 },
                { id: 'widget-satellites', x: 0, y: 18, w: 1, h: 3 },
                { id: 'widget-dx', x: 0, y: 21, w: 1, h: 4 },
            ]
        };

        const layout = layouts[profileName];
        
        if (layout) {
            // Update each widget position
            layout.forEach(item => {
                const el = document.querySelector(`[gs-id="${item.id}"]`);
                if (el) {
                    const node = window.grid.engine.nodes.find(n => n.el === el);
                    if (node) {
                        window.grid.update(el, { 
                            x: item.x, 
                            y: item.y, 
                            w: item.w, 
                            h: item.h 
                        });
                    }
                }
            });
            window.grid.compact();
        } else {
            // For desktop/tablet: just compact
            window.grid.compact();
        }
        
        // Force visibility update
        document.querySelectorAll('.grid-stack-item').forEach(item => {
            item.style.visibility = 'visible';
            item.style.display = '';
        });
    }

    function checkAndApply(force = false) {
        const profile = detectProfile();
        applyProfile(profile, force);
    }

    // Manual override functions
    function setManualOverride(profileName) {
        if (profileName === null || profileName === 'auto') {
            manualOverride = null;
            localStorage.removeItem('gwen_display_profile_override');
        } else if (PROFILES[profileName]) {
            manualOverride = profileName;
            localStorage.setItem('gwen_display_profile_override', profileName);
        }
        checkAndApply(true);
    }

    // Initialize
    function init() {
        if (isInitialized) return;
        
        if (!window.grid) {
            console.log('[Display] Waiting for grid...');
            setTimeout(init, 200);
            return;
        }
        
        isInitialized = true;
        console.log('[Display] Initializing...');

        // Initial check
        checkAndApply(true);

        // Listen for resize with debounce
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => checkAndApply(), 300);
        });

        // Orientation change
        window.addEventListener('orientationchange', function() {
            setTimeout(() => checkAndApply(true), 500);
        });
    }

    // Start - wait for both DOM and GridStack
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(init, 800);
        });
    } else {
        setTimeout(init, 800);
    }

    // Expose API
    window.DisplayProfile = {
        getCurrent: () => currentProfile,
        getAll: () => Object.keys(PROFILES),
        setOverride: setManualOverride,
        clearOverride: () => setManualOverride(null),
        isRaspberry: () => currentProfile === 'raspberry',
        isMobile: () => currentProfile === 'mobile',
        forceDetect: () => checkAndApply(true),
        init: init
    };

})();
