    <script>
    // ============================================
    // MAIN APPLICATION v4
    // ============================================
    const TIMEZONE = 'Europe/Vienna';
    const MOON_EMOJIS = ['🌑','🌒','🌓','🌔','🌕','🌖','🌗','🌘'];
    const $ = (id) => document.getElementById(id);

    // === TIMEZONE ===
    function getViennaTime(now) { return new Intl.DateTimeFormat('de-AT',{timeZone:TIMEZONE,hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false}).format(now); }
    function getViennaDate(now) { return new Intl.DateTimeFormat('de-AT',{timeZone:TIMEZONE,weekday:'long',year:'numeric',month:'2-digit',day:'2-digit'}).format(now); }

    function getMoonPhase(d) {
const newMoon=new Date(2026,0,29,12,0,0).getTime();
const age=((d.getTime()-newMoon)/86400000%29.53059+29.53059)%29.53059;
const phase=Math.round(age/29.53059*8)%8;
return phase;
}
    function getMoonRiseSet(d) {
        const p=getMoonPhase(d), h=(6+p*3)%24, s=(h+12)%24, v=(d.getDate()*50)%60;
        return {rise:String(h).padStart(2,'0')+':'+String(v).padStart(2,'0'), set:String(s).padStart(2,'0')+':'+String((v+30)%60).padStart(2,'0')};
    }


    // === SUN/MOON TIMES (US Naval Observatory API) ===
    let sunData = null;
    const MOON_PHASE_DE = {
        'New Moon':'Neumond','Waxing Crescent':'Zunehmende Sichel',
        'First Quarter':'Erstes Viertel','Waxing Gibbous':'Zunehmender Mond',
        'Full Moon':'Vollmond','Waning Gibbous':'Abnehmender Mond',
        'Last Quarter':'Letztes Viertel','Waning Crescent':'Abnehmende Sichel'
    };
    async function fetchSunData() {
        try {
            const s = UserSettings.load();
            const c = locatorToCoords(s.locator || 'JN87ct');
            // Calculate timezone offset from longitude (15° = 1h)
            const tzOffset = Math.round(c.lon / 15);
            const r = await fetch(`/fetch-sun-moon.php?lat=${c.lat}&lng=${c.lon}&tz=${tzOffset}`);
            if (!r.ok) throw 0;
            sunData = await r.json();
            // Sun times
            if ($('sunTimes') && sunData.sunrise)
                $('sunTimes').textContent = sunData.sunrise + ' / ' + sunData.sunset;
            // Day length
            if ($('dayLength') && sunData.day_length)
                $('dayLength').textContent = sunData.day_length;
            // Moon rise/set
            if ($('moonTimes') && sunData.moonrise)
                $('moonTimes').textContent = sunData.moonrise + ' / ' + sunData.moonset;
            // Moon phase text in QTH panel
            if ($('moonPhaseName') && sunData.moon_phase) {
                const lang = UserSettings.load().language || 'de';
                const phaseName = lang === 'de'
                    ? (MOON_PHASE_DE[sunData.moon_phase] || sunData.moon_phase)
                    : sunData.moon_phase;
                $('moonPhaseName').textContent = phaseName + ' (' + (sunData.moon_illum||'') + ')';
            }
        } catch(e) {
            console.warn('Sun/Moon data fetch failed:', e);
        }
    }

    function updateTime() {
        const now=new Date();
        if($('time'))$('time').textContent=getViennaTime(now);
        if($('time-utc'))$('time-utc').textContent=[now.getUTCHours(),now.getUTCMinutes(),now.getUTCSeconds()].map(v=>String(v).padStart(2,'0')).join(':');
        if($('date'))$('date').textContent=getViennaDate(now);
        if($('footer-time'))$('footer-time').textContent=getViennaTime(now);
        if($('moonEmoji'))$('moonEmoji').textContent=MOON_EMOJIS[getMoonPhase(now)];
        // Moon times: nur überschreiben wenn API-Daten noch nicht da (Fallback)
        if(!sunData) {
            const m=getMoonRiseSet(now);
            if($('moonTimes'))$('moonTimes').textContent=`${m.rise} / ${m.set}`;
        }
    }

    // === SOLAR DATA (parallel load) ===
    let realSolarData = null;
    const solarP = fetch('/data/solar-data.json?t='+Date.now()).then(r=>r.ok?r.json():null).then(d=>{realSolarData=d;}).catch(()=>{});

    function estimateMUF(sfi) { return Math.max(10,Math.round((Math.sqrt(sfi)*2.5+Math.sin((new Date().getUTCHours()-6)*Math.PI/12)*3)*10)/10); }

    function updateSolarData() {
        let sfi=95,ssn=63,kI=2,aI=24;
        if(realSolarData?.success){kI=realSolarData.kIndex||2;sfi=realSolarData.sfi||95;if(window.updateAllBandConditions)updateAllBandConditions();aI=realSolarData.aIndex||kI*12;ssn=Math.round(sfi/1.5);}
        let st=t('weather_quiet'); if(kI>5)st=t('weather_active'); if(kI>7)st=t('weather_storm');
        const u={kIndexCombined:kI,solarFluxCombined:sfi,sunspotsCombined:ssn,aIndexCombined:aI,auroraCombined:kI>4?'Visible':'Quiet',mufCombined:estimateMUF(sfi)+' MHz',spaceWeatherCombined:st};
        for(const[id,v]of Object.entries(u)){if($(id))$(id).textContent=v;}
        
    }

    // === REAL SATELLITE TRACKING with satellite.js ===
    let satRecords = {}; // {name: {satrec, color}}
    let tleData = null;

    async function loadTLEData() {
        try {
            const r = await fetch('/satellite-data.json?t='+Date.now());
            tleData = await r.json();
            if ($('tle-updated')) $('tle-updated').textContent = tleData.updated_at || '--';
            const sats = tleData.satellites;
            for (const [name, data] of Object.entries(sats)) {
                try {
                    const satrec = satellite.twoline2satrec(data.tle1, data.tle2);
                    satRecords[name] = { satrec, color: data.color || '#fff' };
                } catch(e) {}
            }
            updateSatellites();
        } catch(e) {
            if ($('satellite-container')) $('satellite-container').innerHTML = '<div style="color:#ff4757; padding:10px;">TLE data unavailable</div>';
        }
    }

    function updateSatellites() {
        const container = $('satellite-container');
        if (!container) return;

        // Observer position from TLE data (OE3LCR QTH)
        const obsLat = 47.8125 * Math.PI / 180;
        const obsLon = 16.2083 * Math.PI / 180;
        const obsAlt = 0.2; // km

        const now = new Date();
        const gmst = satellite.gstime(now);
        let html = '';

        for (const [name, {satrec, color}] of Object.entries(satRecords)) {
            try {
                const posVel = satellite.propagate(satrec, now);
                if (!posVel.position) { html += `<div style="padding:8px; color:${color}; border-bottom:1px solid rgba(255,255,255,0.1);"><strong>${name}</strong><div style="color:#ff4757; font-size:0.8em;">Propagation error</div></div>`; continue; }

                const eciPos = posVel.position;
                const ecfPos = satellite.eciToEcf(eciPos, gmst);
                const lookAngles = satellite.ecfToLookAngles({latitude:obsLat, longitude:obsLon, height:obsAlt}, ecfPos);

                const azDeg = lookAngles.azimuth * 180 / Math.PI;
                const elDeg = lookAngles.elevation * 180 / Math.PI;
                const distKm = lookAngles.rangeSat;

                const isVisible = elDeg > 0;
                const statusText = isVisible ? t('sat_visible') : t('sat_below');
                const elColor = isVisible ? '#00ff88' : '#666';

                html += `<div style="padding:8px; color:${color}; border-bottom:1px solid rgba(255,255,255,0.1);">` +
                    `<div><strong>${name}</strong></div>` +
                    `<div style="color:#aaa; font-size:0.8em; font-variant-numeric:tabular-nums;">Az: ${azDeg.toFixed(1).padStart(5)}° El: <span style="color:${elColor}">${elDeg.toFixed(1).padStart(5)}°</span> Dist: ${Math.round(distKm)} km</div>` +
                    `<div style="color:${isVisible?'#00ff88':'#666'}; font-size:0.75em; margin-top:2px;">${statusText}</div>` +
                    `</div>`;
            } catch(e) {
                html += `<div style="padding:8px; color:${color}; border-bottom:1px solid rgba(255,255,255,0.1);"><strong>${name}</strong><div style="color:#ff4757; font-size:0.8em;">Calc error</div></div>`;
            }
        }

        container.innerHTML = html || `<div style="color:#888; padding:10px;">${t('sat_loading')}</div>`;
    }

    // === DX CLUSTER (Live via HamQTH.com — kein API-Key, CORS *) ===
    async function updateDXCluster() {
        try {
            const r = await fetch('https://www.hamqth.com/dxc_csv.php?limit=20', {
                headers: { 'User-Agent': 'HamClockDashboard/1.0 OE3LCR' }
            });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const text = await r.text();
            // CSV: Spotter^FreqKHz^DXCall^Comment^Time Date^^^Continent^Band^Country^DXCC
            const lines = text.trim().split('\n').filter(l => l.includes('^'));
            const bandColor = {
                '160M':'#ff6b6b','80M':'#ffa502','60M':'#eccc68','40M':'#ffdd59',
                '30M':'#7bed9f','20M':'#2ed573','17M':'#1e90ff','15M':'#70a1ff',
                '12M':'#5352ed','10M':'#ff4081','6M':'#e84393','2M':'#00d2ff'
            };
            let shown = 0;
            for (let i = 0; i < lines.length && shown < 20; i++) {
                const p = lines[i].split('^');
                if (p.length < 9) continue;
                const spotter = (p[0]||'').trim().toUpperCase();
                const freqKhz = parseFloat(p[1]) || 0;
                const dxCall  = (p[2]||'').trim().toUpperCase();
                const comment = (p[3]||'').trim();
                const timeStr = (p[4]||'').trim();
                const cont    = (p[7]||'EU').trim();
                const band    = (p[8]||'').trim().toUpperCase();
                if (!dxCall || freqKhz === 0) continue;
                const freqMhz = (freqKhz / 1000).toFixed(3);
                const utcTime = timeStr.length >= 4
                    ? timeStr.substring(0,2)+':'+timeStr.substring(2,4)+' UTC'
                    : timeStr;
                const bColor = bandColor[band] || '#ffa502';
                const el = document.getElementById('dx-row-' + (shown + 1));
                if (!el) break;
                el.style.display = 'block';
                const note = comment ? comment.substring(0,28) : '\u2014';
                el.innerHTML = `<strong onclick="openQRZModal('${dxCall}','${freqMhz}')" style="color:${bColor}">${dxCall} ${freqMhz}</strong>`
                    + ` <span style="color:#888;font-size:0.78em">[${band}] ${cont}</span>`
                    + `<br><span class="dx-time" style="color:#666">${spotter} \u2192 ${note} &nbsp;${utcTime}</span>`;
                shown++;
            }
            for (let j = shown + 1; j <= 20; j++) {
                const el2 = document.getElementById('dx-row-' + j);
                if (el2) el2.style.display = 'none';
            }
        } catch(e) {
            console.warn('[DX Cluster] fetch failed:', e);
            const el = document.getElementById('dx-row-1');
            if (el) { el.style.display='block'; el.innerHTML='<span style="color:#ff4757">\u26a0\ufe0f DX Cluster nicht erreichbar</span>'; }
            for (let j = 2; j <= 20; j++) { const e2=document.getElementById('dx-row-'+j); if(e2) e2.style.display='none'; }
        }
        if (typeof checkDXScroll === 'function') setTimeout(checkDXScroll, 100);
    }
    // === LOCATOR ===
    function locatorToCoords(loc) {
        if(!loc||loc.length<4)return{lat:47.8125,lon:16.2083,display:'47.8125°N 16.2083°E'};
        loc=loc.toUpperCase();
        try{let lo=(loc.charCodeAt(0)-65)*20-180,la=(loc.charCodeAt(1)-65)*10-90;if(loc.length>=4){lo+=parseInt(loc[2])*2;la+=parseInt(loc[3]);}if(loc.length>=6){lo+=(loc.charCodeAt(4)-65)*(2/24)+(1/24);la+=(loc.charCodeAt(5)-65)*(1/24)+(1/48);}lo=Math.max(-180,Math.min(180,lo));la=Math.max(-90,Math.min(90,la));return{lat:la,lon:lo,display:`${Math.abs(la).toFixed(4)}°${la>=0?'N':'S'} ${Math.abs(lo).toFixed(4)}°${lo>=0?'E':'W'}`};}
        catch(e){return{lat:47.8125,lon:16.2083,display:'47.8125°N 16.2083°E'};}
    }
    function formatLocator(l){if(!l)return'JN87ct';l=l.toUpperCase();return l.length===6?l.substring(0,4)+l.substring(4,6).toLowerCase():l;}

    // === PERSONALIZATION ===
    function personalizeHomepage() {
        
    // Display Profile from settings
    const savedProfile = localStorage.getItem('gwen_display_profile_override');
    if (savedProfile && savedProfile !== 'auto') {
        if (window.DisplayProfile) {
            window.DisplayProfile.setOverride(savedProfile);
        }
    }

    const s=UserSettings.load(), call=(s.callsign||'OE3LCR').toUpperCase(), loc=formatLocator(s.locator||'JN87ct');
        if($('header-callsign'))$('header-callsign').textContent=`🎙️ ${call}`;
        if($('qth-locator'))$('qth-locator').textContent=loc;
        if($('qth-coords'))$('qth-coords').textContent=locatorToCoords(loc).display;
        document.title=`${call} - Ham Radio Dashboard`;
    }
    window.addEventListener('settingsChanged',()=>{personalizeHomepage();fetchLocalWeather();updatePageTranslations();updateSatellites();});
    window.addEventListener('storage',(e)=>{if(e.key==='gwen_hp_settings')personalizeHomepage();});

    // === WEATHER (OpenWeatherMap) ===
    async function fetchLocalWeather() {
        try {
            const c = locatorToCoords(UserSettings.load().locator||'JN87ct');
            const r = await fetch(`/fetch-weather.php?lat=${c.lat}&lon=${c.lon}`);
            if (!r.ok) throw 0;
            const d = await r.json();
            if (!d.current) throw 0;
            // Wind: m/s → km/h + compass direction
            const windKmh = Math.round((d.current.wind_speed || 0) * 3.6);
            const windDeg = d.current.wind_deg || 0;
            const dirs = ['N','NE','E','SE','S','SW','W','NW'];
            const windDir = dirs[Math.round(windDeg / 45) % 8];
            // Weather icon (day/night from OWM icon suffix)
            const iconCode = d.current.weather?.[0]?.icon || '01d';
            const isDay = iconCode.endsWith('d');
            const id = d.current.weather?.[0]?.id || 800;
            let icon = '❓';
            if      (id >= 200 && id < 300) icon = '⛈️';
            else if (id >= 300 && id < 400) icon = '🌦️';
            else if (id === 500)             icon = '🌦️';
            else if (id >= 501 && id < 505) icon = '🌧️';
            else if (id === 511)             icon = '🌨️';
            else if (id >= 520 && id < 532) icon = id >= 531 ? '⛈️' : '🌧️';
            else if (id >= 600 && id < 623) icon = '🌨️';
            else if (id >= 700 && id < 800) icon = '🌫️';
            else if (id === 800)             icon = isDay ? '☀️' : '🌙';
            else if (id === 801)             icon = isDay ? '🌤️' : '🌙';
            else if (id === 802)             icon = '⛅';
            else if (id >= 803)              icon = '☁️';
            const desc = d.current.weather?.[0]?.description || '';
            const descCap = desc.charAt(0).toUpperCase() + desc.slice(1);
            if ($('localTemp'))        $('localTemp').textContent        = `${d.current.temp.toFixed(1)}°C`;
            if ($('localHumidity'))    $('localHumidity').textContent    = `${d.current.humidity}%`;
            if ($('localWind'))        $('localWind').textContent        = `${windKmh} km/h ${windDir}`;
            if ($('localWeatherIcon')) $('localWeatherIcon').textContent = icon;
            if ($('localWeather'))     $('localWeather').textContent     = descCap;
        } catch(e) {
            if ($('localTemp'))        $('localTemp').textContent        = '--°C';
            if ($('localWeather'))     $('localWeather').textContent     = '❌ API Error';
            if ($('localWeatherIcon')) $('localWeatherIcon').textContent = '❓';
        }
    }

    // === QRZ ===
    function openQRZModal(cs,f){$('qrz-callsign').textContent=cs;$('qrz-frequency').textContent=f+' MHz';$('qrz-link').href=`https://www.qrz.com/lookup/${encodeURIComponent(cs)}`;$('qrz-modal').style.display='flex';}
    function closeQRZModal(){$('qrz-modal').style.display='none';}
    document.addEventListener('click',(e)=>{if(e.target===$('qrz-modal'))closeQRZModal();});

    // === SYSTEM STATS ===
    async function loadSystemStats(){try{const d=await(await fetch('/get-system-stats.php')).json();if($('cpu-value')){$('cpu-value').textContent=d.cpu_percent+'%';$('cpu-bar').style.width=d.cpu_percent+'%';}if($('ram-value')){$('ram-value').textContent=d.ram_percent+'%';$('ram-bar').style.width=d.ram_percent+'%';}if($('disk-value')){$('disk-value').textContent=d.disk_percent+'%';$('disk-bar').style.width=d.disk_percent+'%';}if($('uptime-value'))$('uptime-value').textContent=d.uptime;}catch(e){}}

    function refreshSDOImage(){if($('sdo-image'))$('sdo-image').src='/get-sdo-image.php?t='+Date.now();}

    // === INIT ===
    async function init(){
        personalizeHomepage(); updateTime();
        await solarP; updateSolarData(); if(window.updateAllBandConditions)updateAllBandConditions();
        fetchLocalWeather(); fetchSunData(); updateDXCluster(); refreshSDOImage(); loadSystemStats(); loadTLEData(); updatePageTranslations();
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init);}else{init();}

    setInterval(updateTime,1000);
    setInterval(()=>{fetch('/data/solar-data.json?t='+Date.now()).then(r=>r.json()).then(d=>{realSolarData=d;updateSolarData();}).catch(()=>{});},300000);
    setInterval(fetchLocalWeather,600000);
    setInterval(fetchSunData,1800000);
    setInterval(updateDXCluster,60000);
    setInterval(updateSatellites,7000); // Real tracking: every 5 sec
    setInterval(refreshSDOImage,300000);
    setInterval(loadSystemStats,10000);
    setInterval(loadTLEData,3600000); // Reload TLE every hour
    </script>


    <script src="/js/gridstack.min.js"></script>
    <script src="/js/dashboard-grid.js?v=20260215d"></script>
        <script src="/js/kiosk.js?v=20260216b"></script>
    <!-- Kiosk grid hook -->
    <script>
    const _origEnable = window.enableKioskMode;
    window.enableKioskMode = function() { if(typeof _origEnable==="function") _origEnable(); setGridKioskMode(true); };
    const _origDisable = window.disableKioskMode;
    window.disableKioskMode = function() { if(typeof _origDisable==="function") _origDisable(); setGridKioskMode(false); };
    </script>
    <!-- Mobile Mode Handler -->
    <script src="/js/display-profile.js?v=20260216c"></script>
</body>
</html>
