<div class="card" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header"><span class="icon">💻</span><span class="card-title" data-i18n="card_system">System Status</span></div>
    <div id="system-container" style="flex:1; min-height:0; overflow-y:hidden; padding:5px 0;">
        <div class="info-grid">
            <div class="info-box"><div class="info-label" data-i18n="callsign_label">Callsign</div><div class="info-value" style="color:#00ff88; font-weight:700;">OE3LCR</div></div>
            <div class="info-box"><div class="info-label" data-i18n="locator_label">QTH Locator</div><div class="info-value" style="color:#00ff88; font-weight:700;">JN87ct</div></div>
            <div class="info-box"><div class="info-label" data-i18n="server_label">Server</div><div class="info-value">craith.cloud</div></div>
            <div class="info-box"><div class="info-label" data-i18n="status_label">Status</div><div class="info-value" style="color:#00ff88;">🟢 Online</div></div>
        </div>
        <div style="margin-top:20px; padding-top:15px; border-top:1px solid rgba(0,255,136,0.2);">
            <div style="color:#00ff88; font-weight:600; margin-bottom:12px; font-size:0.9em;" data-i18n="live_metrics">⚙️ LIVE SYSTEM METRICS</div>
            <div style="margin-bottom:15px;"><div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span style="color:#aaa; font-size:0.9em;" data-i18n="cpu">🖥️ CPU</span><span style="color:#fff; font-weight:600;" id="cpu-value">--</span></div><div class="metric-bar-bg" style="background:rgba(0,255,136,0.1);"><div id="cpu-bar" class="metric-bar" style="background:linear-gradient(90deg,#00ff88,#00cc6f);"></div></div></div>
            <div style="margin-bottom:15px;"><div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span style="color:#aaa; font-size:0.9em;" data-i18n="ram">🧠 RAM</span><span style="color:#fff; font-weight:600;" id="ram-value">--</span></div><div class="metric-bar-bg" style="background:rgba(255,165,0,0.1);"><div id="ram-bar" class="metric-bar" style="background:linear-gradient(90deg,#ffa500,#ff8c00);"></div></div></div>
            <div style="margin-bottom:15px;"><div style="display:flex; justify-content:space-between; margin-bottom:6px;"><span style="color:#aaa; font-size:0.9em;" data-i18n="disk">💾 Disk</span><span style="color:#fff; font-weight:600;" id="disk-value">--</span></div><div class="metric-bar-bg" style="background:rgba(100,100,255,0.1);"><div id="disk-bar" class="metric-bar" style="background:linear-gradient(90deg,#6464ff,#5555dd);"></div></div></div>
            <div style="padding:10px; background:rgba(0,150,200,0.1); border-radius:6px;"><span style="color:#aaa; font-size:0.85em;" data-i18n="uptime_label">⏱️ Uptime:</span> <span style="color:#fff; font-weight:600;" id="uptime-value">--</span></div>
        </div>
    </div>
</div>
<script>
function checkSystemScroll() {
    const el = document.getElementById('system-container');
    if (!el) return;
    el.style.overflowY = el.scrollHeight > el.clientHeight ? 'auto' : 'hidden';
}
if (window.ResizeObserver) {
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('system-container');
        if (el) new ResizeObserver(checkSystemScroll).observe(el);
    });
}
document.addEventListener('DOMContentLoaded', () => setTimeout(checkSystemScroll, 1000));
</script>
