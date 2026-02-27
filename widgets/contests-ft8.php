<div class="card" id="kiosk-contests-ft8" style="display:flex; flex-direction:column; height:100%; overflow:hidden;">
    
    <div class="card-header" style="padding:3px 8px; min-height:0; margin-bottom:0; flex-shrink:0; display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:6px;">
            <span class="icon" style="font-size:1em;">🎙️</span>
            <span class="card-title" style="font-size:0.72em;">Contests & FT8</span>
            <span class="demo-badge" style="background:rgba(0,255,136,0.3); border:1px solid #00ff88; color:#00ff88; padding:2px 6px; border-radius:3px; font-size:0.65em; font-weight:700;">LIVE</span>
        </div>
        
        <!-- Mode Tabs -->
        <div style="display:flex; gap:4px;">
            <button id="mode-contests" style="
                font-size:0.65em; padding:2px 6px; background:#00ff88; color:#0a0a0f; 
                border:1px solid #00ff88; border-radius:2px; cursor:pointer; font-weight:bold;
            ">📅</button>
            <button id="mode-ft8-all" style="
                font-size:0.65em; padding:2px 6px; background:#0a0a0f; color:#00ff88; 
                border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;
            ">📡</button>
            <button id="mode-ft8-bands" style="
                font-size:0.65em; padding:2px 6px; background:#0a0a0f; color:#00ff88; 
                border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;
            ">🎯</button>
        </div>
    </div>

    <!-- Content Area -->
    <div style="flex:1; overflow-y:auto; overflow-x:hidden; padding:8px; min-height:0;">
        
        <!-- CONTESTS VIEW -->
        <div id="view-contests" style="display:block;">
            <div style="color:#aaa;">
                <div style="margin-bottom:8px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:3px; color:#00ff88; font-size:0.9em;">🇦🇹 ÖSTERREICH</div>
                <div id="contests-oe-list" style="max-height:120px; overflow-y:auto; margin-bottom:10px; font-size:0.9em;">
                    <div style="color:#888; padding:10px; text-align:center;">Loading...</div>
                </div>
                
                <div style="margin-bottom:8px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:3px; color:#ffdd59; font-size:0.9em;">🌍 INTL</div>
                <div id="contests-intl-list" style="max-height:100px; overflow-y:auto; font-size:0.9em;">
                    <div style="color:#888; padding:10px; text-align:center;">Loading...</div>
                </div>
            </div>
        </div>
        
        <!-- FT8 ALL VIEW -->
        <div id="view-ft8-all" style="display:none;">
            <div style="color:#aaa;">
                <div style="margin-bottom:4px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:3px; font-size:0.9em;">FT8 Spots</div>
                <div id="ft8-all-list" style="max-height:240px; overflow-y:auto; font-size:0.9em;">
                    <div style="color:#888; padding:10px; text-align:center;">Loading...</div>
                </div>
            </div>
        </div>
        
        <!-- FT8 BANDS VIEW -->
        <div id="view-ft8-bands" style="display:none;">
            <div style="color:#aaa;">
                <!-- 13 BAND BUTTONS (FROM BAND CONDITIONS) -->
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:2px; margin-bottom:6px;">
                    <button class="band-btn" data-band="160m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#00ff88; border:1px solid #00ff88; border-radius:2px; cursor:pointer; font-weight:bold;">160m</button>
                    <button class="band-btn" data-band="80m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">80m</button>
                    <button class="band-btn" data-band="60m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">60m</button>
                    <button class="band-btn" data-band="40m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">40m</button>
                    <button class="band-btn" data-band="30m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">30m</button>
                    <button class="band-btn" data-band="20m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">20m</button>
                    <button class="band-btn" data-band="17m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">17m</button>
                    <button class="band-btn" data-band="15m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">15m</button>
                    <button class="band-btn" data-band="12m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">12m</button>
                    <button class="band-btn" data-band="11m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">11m</button>
                    <button class="band-btn" data-band="10m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">10m</button>
                    <button class="band-btn" data-band="6m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">6m</button>
                    <button class="band-btn" data-band="2m" style="font-size:0.6em; padding:3px 4px; background:#0a0a0f; color:#666; border:1px solid #333; border-radius:2px; cursor:pointer; font-weight:bold;">2m</button>
                </div>
                <div style="margin-bottom:3px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:3px; font-size:0.9em;">Nach Band</div>
                <div id="ft8-bands-list" style="max-height:180px; overflow-y:auto; font-size:0.9em;">
                    <div style="color:#888; padding:10px; text-align:center;">Loading...</div>
                </div>
            </div>
        </div>
        
    </div>

</div>

<script>
(function() {
    const modeContests = document.getElementById('mode-contests');
    const modeFT8All = document.getElementById('mode-ft8-all');
    const modeFT8Bands = document.getElementById('mode-ft8-bands');
    
    const viewContests = document.getElementById('view-contests');
    const viewFT8All = document.getElementById('view-ft8-all');
    const viewFT8Bands = document.getElementById('view-ft8-bands');
    
    modeContests.addEventListener('click', () => switchMode('contests'));
    modeFT8All.addEventListener('click', () => switchMode('ft8-all'));
    modeFT8Bands.addEventListener('click', () => switchMode('ft8-bands'));
    
    function switchMode(mode) {
        viewContests.style.display = 'none';
        viewFT8All.style.display = 'none';
        viewFT8Bands.style.display = 'none';
        [modeContests, modeFT8All, modeFT8Bands].forEach(btn => {
            btn.style.background = '#0a0a0f';
            btn.style.borderColor = '#333';
        });
        
        if (mode === 'contests') {
            viewContests.style.display = 'block';
            modeContests.style.background = '#00ff88';
            modeContests.style.borderColor = '#00ff88';
            loadContests();
        } else if (mode === 'ft8-all') {
            viewFT8All.style.display = 'block';
            modeFT8All.style.background = '#00ff88';
            modeFT8All.style.borderColor = '#00ff88';
            loadFT8All();
        } else if (mode === 'ft8-bands') {
            viewFT8Bands.style.display = 'block';
            modeFT8Bands.style.background = '#00ff88';
            modeFT8Bands.style.borderColor = '#00ff88';
            loadFT8Bands();
            updateBandActivity();
        }
    }
    
    function loadContests() {
        fetch('/fetch-contests.php').then(r => r.json()).then(data => {
            let htmlOE = '', htmlIntl = '';
            (data.oevsv || []).forEach(c => {
                const d = new Date(c.date).toLocaleDateString('de-AT', {month:'short', day:'numeric'});
                htmlOE += `<div style="padding:4px; border-bottom:1px solid #222; margin-bottom:3px;"><div style="color:#00ff88; font-weight:bold;">${c.name}</div><div style="color:#666; font-size:0.85em;">${d} ${c.time} | ${c.band}</div></div>`;
            });
            (data.arrl || []).forEach(c => {
                const d = new Date(c.date).toLocaleDateString('de-AT', {month:'short', day:'numeric'});
                htmlIntl += `<div style="padding:4px; border-bottom:1px solid #222; margin-bottom:3px;"><div style="color:#ffdd59; font-weight:bold;">${c.name}</div><div style="color:#666; font-size:0.85em;">${d}</div></div>`;
            });
            document.getElementById('contests-oe-list').innerHTML = htmlOE || '<div style="color:#888;">-</div>';
            document.getElementById('contests-intl-list').innerHTML = htmlIntl || '<div style="color:#888;">-</div>';
        }).catch(() => {
            document.getElementById('contests-oe-list').innerHTML = '<div style="color:#f44;">API Error</div>';
        });
    }
    
    function loadFT8All() {
        fetch('/fetch-ft8-spots.php').then(r => r.json()).then(data => {
            let html = '';
            (data.spots || []).slice(0,8).forEach(s => {
                html += `<div style="padding:4px; border-bottom:1px solid #222; margin-bottom:2px;"><div style="display:flex; justify-content:space-between;"><span style="color:#00ff88; font-weight:bold;">${s.call}</span><span style="color:#ffdd59;">${s.band}</span></div><div style="color:#666; font-size:0.85em;">${s.freq} | SNR ${s.snr}</div></div>`;
            });
            document.getElementById('ft8-all-list').innerHTML = html || '<div style="color:#888;">-</div>';
        }).catch(() => {
            document.getElementById('ft8-all-list').innerHTML = '<div style="color:#f44;">API Error</div>';
        });
    }
    
    function loadFT8Bands(selectedBand = '160m') {
        fetch('/fetch-ft8-spots.php').then(r => r.json()).then(data => {
            let html = '';
            const spots = selectedBand === 'all' ? (data.spots || []) : (data.spots || []).filter(s => s.band === selectedBand);
            spots.slice(0,6).forEach(s => {
                html += `<div style="padding:4px; border-bottom:1px solid #222; margin-bottom:2px;"><div style="display:flex; justify-content:space-between;"><span style="color:#00ff88;">${s.call}</span><span style="color:#ffdd59;">${s.freq}</span></div><div style="color:#666; font-size:0.85em;">SNR ${s.snr}</div></div>`;
            });
            document.getElementById('ft8-bands-list').innerHTML = html || '<div style="color:#888;">-</div>';
        }).catch(() => {
            document.getElementById('ft8-bands-list').innerHTML = '<div style="color:#f44;">API Error</div>';
        });
    }
    
    function updateBandActivity() {
        fetch('/fetch-ft8-spots.php').then(r => r.json()).then(data => {
            const activity = data.band_activity || {};
            document.querySelectorAll('.band-btn').forEach(btn => {
                const band = btn.dataset.band;
                const count = activity[band] || 0;
                if (count >= 3) {
                    btn.style.color = '#00ff88';
                    btn.style.borderColor = '#00ff88';
                } else if (count > 0) {
                    btn.style.color = '#ffdd59';
                    btn.style.borderColor = '#ffdd59';
                } else {
                    btn.style.color = '#666';
                    btn.style.borderColor = '#333';
                }
            });
        });
    }
    
    document.querySelectorAll('.band-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.band-btn').forEach(b => {
                b.style.borderColor = '#333';
                b.style.background = '#0a0a0f';
            });
            btn.style.borderColor = '#00ff88';
            btn.style.background = 'rgba(0,255,136,0.1)';
            loadFT8Bands(btn.dataset.band);
        });
    });
    
    function autoRefresh() {
        if (viewFT8All.style.display !== 'none') loadFT8All();
        if (viewFT8Bands.style.display !== 'none') {
            const selectedBand = document.querySelector('.band-btn[style*="border-color: rgb(0, 255, 136)"]')?.dataset.band || '160m';
            loadFT8Bands(selectedBand);
            updateBandActivity();
        }
    }
    
    loadContests();
    setInterval(autoRefresh, 60000);
})();
</script>
