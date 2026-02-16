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
        if (manualOverride && PROFILES[manualOverride]) {
            return PROFILES[manualOverride];
        }

        const { width: w, height: h } = getScreenSize();

        if (PROFILES.raspberry.detect(w, h)) {
            return PROFILES.raspberry;
        }

        if (w >= PROFILES.desktop.minWidth) return PROFILES.desktop;
        if (w >= PROFILES.tablet.minWidth) return PROFILES.tablet;
        if (w <= PROFILES.mobile.maxWidth) return PROFILES.mobile;
        
        return PROFILES.desktop;
    }

    function applyProfile(profile, force = false) {
        if (!profile) return;
        if (currentProfile === profile.name && !force) return;
        
        console.log('[Display] Applying profile:', profile.name);
        
        Object.values(PROFILES).forEach(p => {
            document.body.classList.remove(p.className);
        });

        document.body.classList.add(profile.className);
        currentProfile = profile.name;

        if (window.grid && typeof window.grid.column === 'function') {
            const wasAnimated = window.grid.opts.animate;
            window.grid.animate(false);
            
            // WICHTIG: Gridstack Konfiguration anpassen
            window.grid.column(profile.columns);
            window.grid.cellHeight(profile.cellHeight);
            window.grid.margin(profile.margin);
            
            // Layout anwenden
            applyLayoutForProfile(profile.name);
            
            if (wasAnimated) {
                setTimeout(() => window.grid.animate(true), 100);
            }
        }

        localStorage.setItem('gwen_display_profile', profile.name);
        
        window.dispatchEvent(new CustomEvent('displayProfileChanged', { 
            detail: profile 
        }));
    }

    function applyLayoutForProfile(profileName) {
        if (!window.grid) return;

        if (profileName === 'mobile') {
            console.log('[Display] Mobile: Force single column layout');
            
            // ALLE Widgets auf eine Spalte zwingen
            const mobileLayout = [
                { id: 'widget-header', x: 0, y: 0, w: 1, h: 1 },
                { id: 'widget-clock', x: 0, y: 1, w: 1, h: 2 },
                { id: 'widget-sun', x: 0, y: 3, w: 1, h: 3 },
                { id: 'widget-qth', x: 0, y: 6, w: 1, h: 3 },
                { id: 'widget-bands', x: 0, y: 9, w: 1, h: 3 },
                { id: 'widget-weather-local', x: 0, y: 12, w: 1, h: 2 },
                { id: 'widget-weather-space', x: 0, y: 14, w: 1, h: 2 },
                { id: 'widget-system', x: 0, y: 16, w: 1, h: 2 },
                { id: 'widget-satellites', x: 0, y: 18, w: 1, h: 3 },
                { id: 'widget-dx', x: 0, y: 21, w: 1, h: 4 },
            ];

            // Zuerst alle Widgets auf x=0 zwingen
            const items = window.grid.getGridItems();
            items.forEach(el => {
                if (!el.id) return;
                const layoutItem = mobileLayout.find(l => l.id === el.id);
                if (layoutItem) {
                    window.grid.update(el, { 
                        x: 0, 
                        y: layoutItem.y, 
                        w: 1, 
                        h: layoutItem.h 
                    });
                }
            });
            
            // Grid neu ordnen
            window.grid.compact();
            
        } else {
            // Für andere Profile: nur compact
            window.grid.compact();
        }
        
        // Sichtbarkeit erzwingen
        document.querySelectorAll('.grid-stack-item').forEach(item => {
            item.style.visibility = 'visible';
            item.style.display = '';
            item.style.opacity = '1';
        });
    }

    function checkAndApply(force = false) {
        const profile = detectProfile();
        applyProfile(profile, force);
    }

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

    function init() {
        if (isInitialized) return;
        
        if (!window.grid) {
            console.log('[Display] Waiting for grid...');
            setTimeout(init, 200);
            return;
        }
        
        isInitialized = true;
        console.log('[Display] Initializing...');

        checkAndApply(true);

        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => checkAndApply(), 300);
        });

        window.addEventListener('orientationchange', function() {
            setTimeout(() => checkAndApply(true), 500);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(init, 800);
        });
    } else {
        setTimeout(init, 800);
    }

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
