<div class="card" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header">
        <span class="icon">🛰️</span>
        <span class="card-title" data-i18n="card_satellites">Aktive Satelliten</span>
        <span class="demo-badge">LIVE</span>
    </div>
    <div id="satellite-container" style="
        font-size:0.85em;
        line-height:1.8;
        flex:1;
        min-height:0;
        overflow-y:hidden;
        padding-right:2px;
    "></div>
    <div style="margin-top:8px; font-size:0.7em; color:#555; text-align:center; flex-shrink:0;">
        <span data-i18n="tle_source">TLE Quelle:</span> CelesTrak •
        <span data-i18n="tle_updated">TLE aktualisiert:</span>
        <span id="tle-updated">--</span>
    </div>
</div>

<script>
// Scrollbar nur zeigen wenn Inhalt überläuft
function checkSatScroll() {
    const el = document.getElementById('satellite-container');
    if (!el) return;
    el.style.overflowY = el.scrollHeight > el.clientHeight ? 'auto' : 'hidden';
}
// Nach jedem Daten-Update prüfen
const _origLoadTLE = window.loadTLEData;
window.loadTLEData = function() {
    if (_origLoadTLE) _origLoadTLE.apply(this, arguments);
    setTimeout(checkSatScroll, 500);
};
// Und bei Widget-Resize
if (window.ResizeObserver) {
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('satellite-container');
        if (el) new ResizeObserver(checkSatScroll).observe(el);
    });
}
document.addEventListener('DOMContentLoaded', () => setTimeout(checkSatScroll, 1000));
</script>
