<div class="card" id="kiosk-sun"
     style="display:flex; flex-direction:column; height:100%; overflow:hidden;">

    <div class="card-header" style="padding:3px 8px; min-height:0; margin-bottom:0; flex-shrink:0; display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:6px;">
            <span class="icon" style="font-size:1em;">☀️</span>
            <span class="card-title" style="font-size:0.72em;" data-i18n="sun_title">NASA SDO – Live</span>
        </div>
        
        <!-- View Mode Dropdown -->
        <select id="sdo-mode" style="
            font-size:0.65em;
            padding:2px 6px;
            background:#0a0a0f;
            color:#00ff88;
            border:1px solid #00ff88;
            border-radius:3px;
            cursor:pointer;
            font-weight:bold;
        ">
            <option value="visible">Visible</option>
            <option value="corona">Corona</option>
            <option value="chromosphere" selected>Chromosphere</option>
            <option value="quietcorona">Quiet Corona</option>
            <option value="flairing">Flairing</option>
        </select>
    </div>

    <div style="flex:1; width:100%; display:flex; align-items:center; justify-content:center; min-height:0; padding:4px 8px 8px; box-sizing:border-box;">
        <img id="sdo-image"
             src="/get-sdo-image.php?mode=chromosphere"
             style="max-width:100%; max-height:100%; width:auto; height:auto;
                    border-radius:50%; box-shadow:0 0 60px rgba(255,165,2,0.5);
                    object-fit:cover; display:block;"
             alt="Live Sonnenbild">
    </div>

</div>

<script>
// ============================================
// SDO Live Auto-Cycling with 5 Modes
// ============================================
(function() {
    const modes = ['visible', 'corona', 'chromosphere', 'quietcorona', 'flairing'];
    let currentIdx = 2; // Start at chromosphere
    let autoCycleActive = true;
    let cycleInterval = null;
    let lastUserInteraction = 0;

    const dropdown = document.getElementById('sdo-mode');
    const imgEl = document.getElementById('sdo-image');

    // Bild laden mit Cache-Buster
    function loadMode(mode) {
        const timestamp = Math.floor(Date.now() / 1000);
        imgEl.src = `/get-sdo-image.php?mode=${mode}&t=${timestamp}`;
    }

    // Auto-Cycling starten
    function startCycling() {
        if (cycleInterval) clearInterval(cycleInterval);
        autoCycleActive = true;
        cycleInterval = setInterval(() => {
            currentIdx = (currentIdx + 1) % modes.length;
            const mode = modes[currentIdx];
            loadMode(mode);
            dropdown.value = mode;
        }, 15000); // 15 Sekunden Auto-Cycle
    }

    // Dropdown-Änderung: Stoppe Auto-Cycling
    dropdown.addEventListener('change', (e) => {
        const mode = e.target.value;
        loadMode(mode);
        currentIdx = modes.indexOf(mode);
        
        // Stop auto-cycling
        if (cycleInterval) clearInterval(cycleInterval);
        autoCycleActive = false;
        lastUserInteraction = Date.now();
    });

    // Auf Bild-Klick: Weiterblättern oder zurück zu Auto-Cycling
    imgEl.addEventListener('click', () => {
        if (autoCycleActive) {
            // Already cycling - skip to next
            currentIdx = (currentIdx + 1) % modes.length;
            const mode = modes[currentIdx];
            loadMode(mode);
            dropdown.value = mode;
        } else {
            // Not cycling - return to auto
            startCycling();
        }
    });

    // Init: Start cycling
    loadMode(modes[currentIdx]);
    startCycling();
})();
</script>
