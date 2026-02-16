    <!-- Setup Modal -->
    <div id="setup-modal"><div class="modal-content">
        <div class="modal-header">🍯 <span id="modal-title">Willkommen zu OE3LCR Dashboard</span></div>
        <div class="modal-subtitle" id="welcome-text">Willkommen! Gib deine Daten ein:</div>
        <form id="setup-form">
            <div class="form-group"><label data-i18n="modal_callsign">Rufzeichen:</label><input type="text" name="callsign" placeholder="z.B. OE3LCR" maxlength="10" required></div>
            <div class="form-group"><label data-i18n="modal_locator">Maidenhead Locator:</label><input type="text" name="locator" placeholder="z.B. JN87ct" maxlength="6" required></div>
            <div class="form-group"><label data-i18n="modal_language">Sprache:</label><select name="language" id="language-select"><option value="de">Deutsch 🇩🇪</option><option value="en">English 🇬🇧</option></select></div>
            <div class="form-group"><label>📺 Display-Modus:</label><select name="display_profile" id="display-profile-select"><option value="auto">🤖 Automatisch (empfohlen)</option><option value="desktop">🖥️ Desktop (1920×1080)</option><option value="raspberry">📟 Raspberry Pi 7" (1024×600)</option><option value="tablet">📱 Tablet</option><option value="mobile">📲 Smartphone</option></select><small style="color:#888; display:block; margin-top:4px;">Automatisch erkennt die optimale Einstellung für dein Gerät</small></div>
            <div class="modal-buttons"><button type="submit" class="btn-primary">Start</button></div>
        </form>
    </div></div>

    <!-- QRZ Modal -->
    <div id="qrz-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:rgba(20,20,35,0.95); border:2px solid #00ff88; border-radius:15px; padding:30px; max-width:500px; width:90%; position:relative;">
            <button onclick="closeQRZModal()" style="position:absolute; top:15px; right:15px; background:none; border:none; color:#00ff88; font-size:28px; cursor:pointer;">×</button>
            <div style="text-align:center; margin-bottom:20px;"><h2 id="qrz-callsign" style="color:#00ff88; font-size:2em; margin:10px 0;">N0BUI</h2><p id="qrz-frequency" style="color:#ffa502; font-size:1.1em;">14.250 MHz</p></div>
            <div style="text-align:center; margin:20px 0;"><a id="qrz-link" href="#" target="_blank" rel="noopener noreferrer" style="display:inline-block; background:linear-gradient(135deg,#00ff88,#00ff64); color:#000; padding:12px 24px; border-radius:8px; text-decoration:none; font-weight:600;">📖 View on QRZ.com</a></div>
            <div style="text-align:center; margin-top:20px;"><button onclick="closeQRZModal()" style="background:rgba(255,100,130,0.3); border:1px solid #ff6482; color:#ff6482; padding:10px 24px; border-radius:8px; cursor:pointer; font-weight:600;">Close</button></div>
        </div>
    </div>
