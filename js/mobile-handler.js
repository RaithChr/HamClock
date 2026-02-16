/**
 * Mobile Mode Handler for HamClock Dashboard
 * Auto-detects mobile devices and switches to single-column layout
 * PC/Desktop layout remains completely unchanged
 */

(function() {
    'use strict';

    const MOBILE_BREAKPOINT = 768;
    let isMobileMode = false;
    let originalColumnCount = 12;

    // Check if mobile
    function isMobile() {
        return window.innerWidth < MOBILE_BREAKPOINT || screen.width < MOBILE_BREAKPOINT;
    }

    // Apply mobile styles
    function enableMobileMode() {
        if (isMobileMode) return;
        isMobileMode = true;
        
        document.body.classList.add('mobile-mode');
        
        // Gridstack: switch to 1 column
        if (window.grid && typeof window.grid.column === 'function') {
            window.grid.column(1);
            // Compact to stack vertically
            window.grid.compact();
        }
        
        // Save state
        localStorage.setItem('gwen_mobile_mode', 'true');
        
        console.log('[Mobile] Mobile mode enabled');
    }

    // Restore desktop layout
    function disableMobileMode() {
        if (!isMobileMode) return;
        isMobileMode = false;
        
        document.body.classList.remove('mobile-mode');
        
        // Restore original grid columns
        if (window.grid && typeof window.grid.column === 'function') {
            window.grid.column(originalColumnCount);
        }
        
        localStorage.setItem('gwen_mobile_mode', 'false');
        
        console.log('[Mobile] Desktop mode restored');
    }

    // Auto-detect and apply
    function checkAndApply() {
        if (isMobile()) {
            enableMobileMode();
        } else {
            disableMobileMode();
        }
    }

    // Initialize after grid is ready
    function init() {
        // Wait for gridstack to be initialized
        if (!window.grid) {
            setTimeout(init, 100);
            return;
        }
        
        // Store original column count
        originalColumnCount = window.grid.opts.column || 12;
        
        // Initial check
        checkAndApply();
        
        // Listen for resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(checkAndApply, 250);
        });
        
        // Orientation change (mobile rotate)
        window.addEventListener('orientationchange', function() {
            setTimeout(checkAndApply, 300);
        });
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(init, 500); // Wait for gridstack init
        });
    } else {
        setTimeout(init, 500);
    }

    // Expose for manual toggle (debug)
    window.MobileMode = {
        enable: enableMobileMode,
        disable: disableMobileMode,
        toggle: function() { isMobileMode ? disableMobileMode() : enableMobileMode(); },
        isActive: function() { return isMobileMode; }
    };

})();
