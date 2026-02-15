<div class="card" id="widget-weather-space-inner" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header">
        <span class="icon">⚡</span>
        <span class="card-title" data-i18n="space_weather">⚡ WELTRAUMWETTER</span>
    </div>
    <div id="space-weather-container" style="display:grid; gap:12px; padding:5px; flex:1; min-height:0; overflow-y:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="kindex">K-Index:</span>
            <span style="color:#fff; font-weight:600; font-variant-numeric:tabular-nums;" id="kIndexCombined">--</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="solarflux">Solar Flux:</span>
            <span style="color:#fff; font-weight:600; font-variant-numeric:tabular-nums;" id="solarFluxCombined">--</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="sunspots">Sunspot Number:</span>
            <span style="color:#fff; font-weight:600; font-variant-numeric:tabular-nums;" id="sunspotsCombined">--</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="aindex">A-Index:</span>
            <span style="color:#fff; font-weight:600; font-variant-numeric:tabular-nums;" id="aIndexCombined">--</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="aurora">Aurora:</span>
            <span style="color:#fff; font-weight:600;" id="auroraCombined">--</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="muf">MUF (est.):</span>
            <span style="color:#fff; font-weight:600; font-variant-numeric:tabular-nums;" id="mufCombined">-- MHz</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#aaa;" data-i18n="sw_status">Status:</span>
            <span style="color:#fff; font-weight:600;" id="spaceWeatherCombined">--</span>
        </div>
    </div>
</div>
<script>
function checkSpaceWeatherScroll() {
    const el = document.getElementById('space-weather-container');
    if (!el) return;
    el.style.overflowY = el.scrollHeight > el.clientHeight ? 'auto' : 'hidden';
}
if (window.ResizeObserver) {
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('space-weather-container');
        if (el) new ResizeObserver(checkSpaceWeatherScroll).observe(el);
    });
}
document.addEventListener('DOMContentLoaded', () => setTimeout(checkSpaceWeatherScroll, 1000));
</script>
