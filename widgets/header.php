<div class="card" id="widget-header-inner"
     style="display:flex; align-items:center; justify-content:space-between;
            padding:10px 20px; gap:15px; flex-wrap:wrap; height:100%; box-sizing:border-box;">

    <!-- Rufzeichen: klickbar, Farbe wechselt, Größe dynamisch -->
    <div id="header-callsign"
         style="font-weight:800; white-space:nowrap; cursor:pointer; letter-spacing:2px; flex-shrink:0; transition:color 0.3s, text-shadow 0.3s; text-shadow:0 0 20px currentColor;"
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
        <button id="kiosk-page-btn" class="hdr-btn" onclick="nextKioskPage()"
                style="display:none; background:rgba(0,255,136,0.15); border:1px solid #00ff88; color:#00ff88;"
                title="Nächste Seite">▶ S1</button>
        <button class="hdr-btn" onclick="resetGridLayout()"
                style="background:rgba(255,165,0,0.15); border:1px solid #ffa502; color:#ffa502;"
                title="Layout zurücksetzen">🔄 Reset</button>
    </div>

</div>

<script>
(function(){
    const COLORS = [
        {color:'#ffe066', shadow:'rgba(255,224,102,0.6)'},
        {color:'#ff9500', shadow:'rgba(255,149,0,0.6)'},
        {color:'#ff4757', shadow:'rgba(255,71,87,0.6)'},
        {color:'#00ff88', shadow:'rgba(0,255,136,0.6)'},
        {color:'#70a1ff', shadow:'rgba(112,161,255,0.6)'},
    ];
    let idx = parseInt(localStorage.getItem('callsign_color') || '3');

    function applyColor(cs) {
        const c = COLORS[idx % COLORS.length];
        cs.style.color = c.color;
        cs.style.textShadow = '0 0 20px ' + c.shadow;
    }

    function initColor() {
        const cs = document.getElementById('header-callsign');
        if (!cs) { setTimeout(initColor, 100); return; }
        applyColor(cs);
        cs.addEventListener('click', () => {
            idx = (idx + 1) % COLORS.length;
            localStorage.setItem('callsign_color', idx);
            applyColor(cs);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initColor);
    } else {
        setTimeout(initColor, 100);
    }
})();
</script>
