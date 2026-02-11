<div class="card" id="widget-clock-inner" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header" style="padding:4px 8px; min-height:0;">
        <span class="icon">🕐</span>
        <span class="card-title" style="font-size:0.75em;">Uhrzeit</span>
    </div>
    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; gap:0; padding:8px 10px; overflow:hidden;">

    <div style="text-align:center; width:100%;">
        <div style="font-size:0.65em; font-weight:700; letter-spacing:3px; color:#ff6b35; opacity:0.7; margin-bottom:6px;">LOC</div>
        <div id="time"
             style="font-weight:800; color:#ff6b35; font-variant-numeric:tabular-nums; font-family:'Inter',monospace; letter-spacing:1px; line-height:1; white-space:nowrap; overflow:hidden; text-align:center;">
            00:00:00
        </div>
    </div>

    <div style="width:50%; height:1px; background:rgba(255,255,255,0.08); margin:10px auto;"></div>

    <div style="text-align:center; width:100%;">
        <div style="font-size:0.65em; font-weight:700; letter-spacing:3px; color:#70a1ff; opacity:0.7; margin-bottom:6px;">UTC</div>
        <div id="time-utc"
             style="font-weight:800; color:#70a1ff; font-variant-numeric:tabular-nums; font-family:'Inter',monospace; letter-spacing:1px; line-height:1; white-space:nowrap; overflow:hidden; text-align:center;">
            00:00:00
        </div>
    </div>

    <div style="width:50%; height:1px; background:rgba(255,255,255,0.08); margin:10px auto;"></div>

    <div id="date" style="color:#888; font-weight:600; text-align:center; white-space:nowrap; overflow:hidden;">
        --.--.----
    </div>

    </div>
</div>
<script>
(function(){
    function scaleClock() {
        const card = document.getElementById('widget-clock-inner');
        if (!card) return;
        const w = card.offsetWidth;
        const h = card.offsetHeight;
        const sz = Math.min(w * 0.28, h * 0.22, 120);
        const t1 = document.getElementById('time');
        const t2 = document.getElementById('time-utc');
        const dt = document.getElementById('date');
        if (t1) t1.style.fontSize = Math.max(sz, 20) + 'px';
        if (t2) t2.style.fontSize = Math.max(sz, 20) + 'px';
        if (dt) dt.style.fontSize = Math.max(sz * 0.35, 11) + 'px';
    }
    // Run on load
    window.addEventListener('DOMContentLoaded', () => setTimeout(scaleClock, 300));
    // Run on resize
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(scaleClock);
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('widget-clock-inner');
            if (el) ro.observe(el);
        });
    }
    // Also run when gridstack resizes
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.grid-stack')?.addEventListener('resizestop', scaleClock);
    });
})();
</script>
