<div class="card" id="widget-clock-inner" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header" style="padding:4px 8px; min-height:0; margin-bottom:0; border-bottom:1px solid rgba(255,255,255,0.1);">
        <span class="icon">🕐</span>
        <span class="card-title" style="font-size:0.75em;">Uhrzeit</span>
    </div>

    <!-- Äußerer Wrapper: immer column -->
    <div style="display:flex; flex-direction:column; flex:1; padding:6px 10px 4px; overflow:hidden; box-sizing:border-box; justify-content:center;">

        <!-- Zeiten-Container: column (schmal) ↔ row (breit) -->
        <div id="clock-times" style="display:flex; flex-direction:column; align-items:stretch; flex:1; min-height:0;">

            <!-- LOC -->
            <div id="clock-loc" style="display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; min-height:0; min-width:0;">
                <div style="font-size:0.65em; font-weight:700; letter-spacing:2px; color:#ff6b35; opacity:0.7; margin-bottom:2px;">LOC</div>
                <div id="time" style="font-weight:800; color:#ff6b35; font-variant-numeric:tabular-nums; font-family:'Inter',monospace; letter-spacing:0; line-height:1; white-space:nowrap; text-align:center; width:100%;">00:00:00</div>
            </div>

            <!-- Trennlinie -->
            <div id="clock-divider" style="background:rgba(255,255,255,0.1); flex-shrink:0; align-self:center; width:50%; height:1px; margin:5px auto;"></div>

            <!-- UTC -->
            <div id="clock-utc" style="display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; min-height:0; min-width:0;">
                <div style="font-size:0.65em; font-weight:700; letter-spacing:2px; color:#70a1ff; opacity:0.7; margin-bottom:2px;">UTC</div>
                <div id="time-utc" style="font-weight:800; color:#70a1ff; font-variant-numeric:tabular-nums; font-family:'Inter',monospace; letter-spacing:0; line-height:1; white-space:nowrap; text-align:center; width:100%;">00:00:00</div>
            </div>

        </div>

        <!-- Datum -->
        <div style="text-align:center; flex-shrink:0; padding-top:3px;">
            <div id="date" style="color:#888; font-weight:600; white-space:nowrap; overflow:hidden; text-align:center;">--.--.----</div>
        </div>

    </div>
</div>
<script>
(function(){
    // Threshold: Breite Box wenn BEIDE Zeiten + Divider bequem nebeneinander passen
    // Mindest-Fontgröße im Wide-Mode × 2 Blöcke + Overhead ≈ 400px
    var WIDE = 400;

    function scaleClock() {
        var card = document.getElementById('widget-clock-inner');
        if (!card) return;
        var w = card.offsetWidth;
        var h = card.offsetHeight;
        if (w < 20 || h < 30) return;

        var times = document.getElementById('clock-times');
        var divEl = document.getElementById('clock-divider');
        var t1    = document.getElementById('time');
        var t2    = document.getElementById('time-utc');
        var dt    = document.getElementById('date');
        if (!times || !t1 || !t2) return;

        // Effektiv verfügbare Breite (Card-Padding 20px×2 + Wrapper-Padding 10px×2)
        var avail = w - 60;

        if (w > WIDE) {
            // ── WIDE: LOC | UTC nebeneinander ─────────────────────────
            times.style.flexDirection = 'row';
            times.style.alignItems    = 'center';
            if (divEl) {
                divEl.style.width     = '1px';
                divEl.style.height    = '65%';
                divEl.style.margin    = '0 6px';
                divEl.style.alignSelf = 'center';
            }
            // Hälfte abzüglich Divider
            var hw  = (avail - 14) / 2;
            // "00:00:00" = 8 Zeichen, Monospace Inter-Digits ~0.55em breit
            var sz  = Math.min(hw / 4.5, (h - 55) * 0.32, 60);
            t1.style.fontSize = Math.max(sz, 12) + 'px';
            t2.style.fontSize = Math.max(sz, 12) + 'px';
            if (dt) dt.style.fontSize = Math.max(sz * 0.32, 10) + 'px';

        } else {
            // ── NARROW: LOC über UTC ──────────────────────────────────
            times.style.flexDirection = 'column';
            times.style.alignItems    = 'stretch';
            if (divEl) {
                divEl.style.width     = '55%';
                divEl.style.height    = '1px';
                divEl.style.margin    = '5px auto';
                divEl.style.alignSelf = 'center';
            }
            // Volle Breite verfügbar, aber padding abziehen
            var sz = Math.min(avail / 4.5, (h - 55) * 0.28, 65);
            t1.style.fontSize = Math.max(sz, 12) + 'px';
            t2.style.fontSize = Math.max(sz, 12) + 'px';
            if (dt) dt.style.fontSize = Math.max(sz * 0.35, 10) + 'px';
        }
    }

    function attach() {
        setTimeout(scaleClock, 250);
        if (window.ResizeObserver) {
            var el = document.getElementById('widget-clock-inner');
            if (el) new ResizeObserver(scaleClock).observe(el);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attach);
    } else {
        attach();
    }
})();
</script>