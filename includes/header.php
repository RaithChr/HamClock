<header class="site-header">
    <div class="header-left">
        <div class="callsign" id="header-callsign">🎙️ OE3LCR</div>
    </div>

    <div class="header-center">
        <div class="clock-box">
            <div class="clock-row">
                <span class="clock-label loc-label">LOC</span>
                <span class="clock-time loc-time" id="time">00:00:00</span>
                <span class="clock-sep">·</span>
                <span class="clock-label utc-label">UTC</span>
                <span class="clock-time utc-time" id="time-utc">00:00:00</span>
            </div>
            <div class="clock-date" id="date">--.--.----</div>
        </div>
    </div>

    <div class="header-right">
        <div class="btn-box">
            <a href="support.html" style="text-decoration:none;">
                <button class="hdr-btn btn-support" data-i18n="support_btn">❤️ Support</button>
            </a>
            <a href="info.html" style="text-decoration:none;">
                <button class="hdr-btn btn-legend" data-i18n="legend_btn">ℹ️ Legende</button>
            </a>
            <button class="hdr-btn btn-kiosk" id="kiosk-btn" onclick="toggleKioskMode()" data-i18n="fullscreen_btn">📺 Vollbild</button>
            <button class="hdr-btn btn-settings" id="settings-btn" data-i18n="settings_btn">⚙️ Einstellungen</button>
        </div>
    </div>
</header>
