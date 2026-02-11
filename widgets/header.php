<div class="card" id="widget-header-inner"
     style="display:flex; align-items:center; justify-content:space-between;
            padding:10px 20px; gap:15px; flex-wrap:wrap; height:100%; box-sizing:border-box;">

    <!-- Rufzeichen: klickbar, Farbe wechselt, Größe dynamisch -->
    <div id="header-callsign"
         style="font-weight:800; white-space:nowrap; cursor:pointer;
                color:#00ff88; letter-spacing:2px; flex-shrink:0;
                transition:color 0.3s, text-shadow 0.3s;
                text-shadow:0 0 20px currentColor;"
         title="Farbe wechseln">
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
(function(){
    // === Farbwechsel beim Klick ===
    const COLORS = [
        { color:'#ffe066', shadow:'rgba(255,224,102,0.6)' },  // Gelb
        { color:'#ff9500', shadow:'rgba(255,149,0,0.6)' },    // Orange
        { color:'#ff4757', shadow:'rgba(255,71,87,0.6)' },    // Rot
        { color:'#00ff88', shadow:'rgba(0,255,136,0.6)' },    // Grün
        { color:'#70a1ff', shadow:'rgba(112,161,255,0.6)' },  // Blau
    ];
    let colorIdx = 3; // Start: Grün

    // Gespeicherte Farbe laden
    const savedColor = localStorage.getItem('callsign_color');
    if (savedColor !== null) colorIdx = parseInt(savedColor);

    function applyColor(el) {
        const c = COLORS[colorIdx % COLORS.length];
        el.style.color = c.color;
        el.style.textShadow = '0 0 20px ' + c.shadow;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const cs = document.getElementById('header-callsign');
        if (!cs) return;

        // Gespeicherte Farbe anwenden
        applyColor(cs);

        // Klick → nächste Farbe
        cs.addEventListener('click', () => {
            colorIdx = (colorIdx + 1) % COLORS.length;
            localStorage.setItem('callsign_color', colorIdx);
            applyColor(cs);
        });
    });

    // === Dynamische Schriftgröße ===
    function scaleCallsign() {
        const widget = document.getElementById('widget-header');
        const cs = document.getElementById('header-callsign');
        if (!widget || !cs) return;
        const w = widget.offsetWidth;
        const h = widget.offsetHeight;
        const size = Math.max(Math.min(w * 0.04, h * 0.45, 38), 13);
        cs.style.fontSize = size + 'px';
    }

    document.addEventListener('DOMContentLoaded', () => setTimeout(scaleCallsign, 400));

    if (window.ResizeObserver) {
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('widget-header');
            if (el) new ResizeObserver(scaleCallsign).observe(el);
        });
    }
})();
</script>
