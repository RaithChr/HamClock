<div class="card" id="widget-header-inner"
     style="display:flex; align-items:center; justify-content:space-between;
            padding:10px 20px; gap:15px; flex-wrap:wrap; overflow:hidden;">

    <!-- Callsign: dynamische Schriftgröße -->
    <div id="header-callsign"
         style="font-weight:800; color:#00ff88; white-space:nowrap;
                font-size:clamp(1em, 3vw, 2em); letter-spacing:2px; flex-shrink:0;">
        🎙️ OE3LCR
    </div>

    <!-- Buttons -->
    <div class="btn-box" style="display:flex; gap:6px; flex-wrap:wrap; margin-left:auto; align-items:center;">
        <a href="support.html" style="text-decoration:none;">
            <button class="hdr-btn btn-support" data-i18n="support_btn">❤️ Support</button>
        </a>
        <a href="info.html" style="text-decoration:none;">
            <button class="hdr-btn btn-legend" data-i18n="legend_btn">ℹ️ Legende</button>
        </a>
        <button class="hdr-btn btn-kiosk" id="kiosk-btn" onclick="toggleKioskMode()" data-i18n="fullscreen_btn">📺 Vollbild</button>
        <button class="hdr-btn btn-settings" id="settings-btn" data-i18n="settings_btn">⚙️ Einstellungen</button>
        <button class="hdr-btn" onclick="resetGridLayout()"
                style="background:rgba(255,165,0,0.15); border:1px solid #ffa502; color:#ffa502;"
                title="Layout zurücksetzen">🔄 Reset</button>
    </div>

</div>

<script>
// Rufzeichen: ResizeObserver für dynamische Schriftgröße
(function(){
    function scaleCallsign() {
        const inner = document.getElementById('widget-header-inner');
        const cs    = document.getElementById('header-callsign');
        if (!inner || !cs) return;
        const w = inner.offsetWidth;
        cs.style.fontSize = Math.max(Math.min(w * 0.04, 32), 14) + 'px';
    }
    window.addEventListener('DOMContentLoaded', () => setTimeout(scaleCallsign, 300));
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(scaleCallsign);
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('widget-header-inner');
            if (el) ro.observe(el);
        });
    }
})();
</script>
