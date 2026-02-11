            <div class="card" id="kiosk-weather" style="grid-column: 1 / -1;">
                <div class="card-header"><span class="icon">🌤️</span><span class="card-title" data-i18n="card_weather">Weather &amp; Space Weather</span></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:15px;">
                    <div style="padding:20px; background:linear-gradient(135deg, rgba(0,255,136,0.08), rgba(0,255,136,0.02)); border-left:4px solid #00ff88; border-radius:12px;">
                        <div style="color:#00ff88; font-weight:700; margin-bottom:15px; font-size:0.95em;" data-i18n="local_weather">📍 LOKALES WETTER</div>
                        <div style="font-size:3em; text-align:center; margin:15px 0;" id="localWeatherIcon">☀️</div>
                        <div style="display:grid; gap:12px;">
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="temp">🌡️ Temperatur</span><span style="color:#fff; font-weight:700;" id="localTemp">--°C</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="humidity">💧 Feuchtigkeit</span><span style="color:#fff; font-weight:700;" id="localHumidity">--%</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="wind">💨 Wind</span><span style="color:#fff; font-weight:700;" id="localWind">-- km/h</span></div>
                            <div style="margin-top:10px; padding-top:10px; border-top:1px solid rgba(0,255,136,0.2); text-align:center;"><span style="color:#fff; font-weight:600;" id="localWeather">--</span></div>
                        </div>
                    </div>
                    <div style="padding:15px; background:rgba(255,165,0,0.05); border-left:3px solid #ffa502; border-radius:8px;">
                        <div style="color:#ffa502; font-weight:600; margin-bottom:12px; font-size:0.9em;" data-i18n="space_weather">⚡ WELTRAUMWETTER</div>
                        <div style="display:grid; gap:10px;">
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="kindex">K-Index:</span><span style="color:#fff; font-weight:600;" id="kIndexCombined">--</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="solarflux">Solar Flux:</span><span style="color:#fff; font-weight:600;" id="solarFluxCombined">--</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="sunspots">Sunspot Number:</span><span style="color:#fff; font-weight:600;" id="sunspotsCombined">--</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="aindex">A-Index:</span><span style="color:#fff; font-weight:600;" id="aIndexCombined">--</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="aurora">Aurora:</span><span style="color:#fff; font-weight:600;" id="auroraCombined">Quiet</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="muf">MUF (est.):</span><span style="color:#fff; font-weight:600;" id="mufCombined">-- MHz</span></div>
                            <div style="display:flex; justify-content:space-between;"><span style="color:#aaa;" data-i18n="sw_status">Status:</span><span style="color:#fff; font-weight:600;" id="spaceWeatherCombined">--</span></div>
                        </div>
                    </div>
                </div>
            </div>