/**
 * User Settings Manager
 * Handles localStorage for Ham Radio Dashboard personalization
 */

const UserSettings = {
    // Default settings
    defaults: {
        callsign: 'OE3LCR',
        locator: 'JN87ct',
        language: 'de',
        timestamp: new Date().toISOString()
    },

    // Format Maidenhead Locator correctly (AA00aa format)
    formatLocator(locator) {
        if (!locator) return 'JN87ct';
        locator = locator.toUpperCase();
        // First 4 chars uppercase, last 2 lowercase
        if (locator.length === 6) {
            return locator.substring(0, 4) + locator.substring(4, 6).toLowerCase();
        }
        if (locator.length === 4) {
            return locator.substring(0, 4);
        }
        return locator;
    },

    // Get all settings from localStorage
    load() {
        const stored = localStorage.getItem('gwen_hp_settings');
        if (stored) {
            try {
                return JSON.parse(stored);
            } catch (e) {
                console.error('Error parsing settings:', e);
                return this.defaults;
            }
        }
        return this.defaults;
    },

    // Save settings to localStorage
    save(settings) {
        const toSave = {
            callsign: (settings.callsign || this.defaults.callsign).toUpperCase(),
            locator: this.formatLocator(settings.locator || this.defaults.locator),
            language: settings.language || this.defaults.language,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('gwen_hp_settings', JSON.stringify(toSave));
        console.log('✅ Settings saved to localStorage:', toSave);
        
        // Verify immediately
        const verify = localStorage.getItem('gwen_hp_settings');
        console.log('✅ Verified in localStorage:', verify);
        
        // Emit settings changed event
        window.dispatchEvent(new Event('settingsChanged'));
        
        return toSave;
    },

    // Check if first time visiting
    isFirstVisit() {
        return !localStorage.getItem('gwen_hp_settings');
    },

    // Reset to defaults
    reset() {
        localStorage.removeItem('gwen_hp_settings');
        console.log('Settings reset to defaults');
        // Emit reset event
        window.dispatchEvent(new Event('settingsChanged'));
        return this.defaults;
    },

    // Get current setting
    get(key) {
        const settings = this.load();
        return settings[key] || this.defaults[key];
    },

    // Set single setting
    set(key, value) {
        const settings = this.load();
        settings[key] = value;
        return this.save(settings);
    }
};

/**
 * Setup Modal Manager
 * Handles the first-visit setup modal
 */
const SetupModal = {
    showModal() {
        const modal = document.getElementById('setup-modal');
        if (modal) {
            modal.style.display = 'flex';
        }
    },

    hideModal() {
        const modal = document.getElementById('setup-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    },

    initializeForm() {
        const settings = UserSettings.load();
        const form = document.getElementById('setup-form');

        if (form) {
            form.callsign.value = settings.callsign;
            form.locator.value = settings.locator;
            form.language.value = settings.language;
        }
    },

    handleSubmit(e) {
        e.preventDefault();

        const form = document.getElementById('setup-form');
        const rawCallsign = form.callsign.value.toUpperCase() || 'OE3LCR';
        const rawLocator = form.locator.value.toUpperCase() || 'JN87ct';
        const language = form.language.value || 'de';

        // Validate BEFORE formatting
        if (!rawCallsign.match(/^[A-Z0-9\/]{2,10}$/)) {
            alert('❌ Ungültiges Rufzeichen! (z.B. OE3LCR)');
            return;
        }
        if (!rawLocator.match(/^[A-Z0-9]{4,6}$/)) {
            alert('❌ Ungültiger Maidenhead Locator! (z.B. JN87ct)');
            return;
        }

        // Format AFTER validation
        const settings = {
            callsign: rawCallsign,
            locator: UserSettings.formatLocator(rawLocator),
            language: language
        };

        // Save & Reload
        UserSettings.save(settings);
        this.hideModal();
        location.reload();
    },

    initialize() {
        // Show modal if first visit
        if (UserSettings.isFirstVisit()) {
            this.showModal();
        }

        // Setup form handler
        const form = document.getElementById('setup-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Setup settings button
        const settingsBtn = document.getElementById('settings-btn');
        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => this.showSettingsModal());
        }

        // Initialize form with current values
        this.initializeForm();
    },

    showSettingsModal() {
        const settings = UserSettings.load();
        const modalContent = `
            <div class="settings-modal-content">
                <div class="settings-header">
                    <h2>⚙️ ${this.getTranslation('settings')}</h2>
                    <button class="close-btn" onclick="SetupModal.hideSettingsModal()">×</button>
                </div>
                
                <div class="settings-info">
                    <p><strong>${this.getTranslation('callsign')}:</strong> ${settings.callsign}</p>
                    <p><strong>${this.getTranslation('locator')}:</strong> ${settings.locator}</p>
                    <p><strong>${this.getTranslation('language')}:</strong> ${settings.language === 'de' ? 'Deutsch' : 'English'}</p>
                </div>

                <form id="edit-form">
                    <div class="form-group">
                        <label>${this.getTranslation('callsign')}:</label>
                        <input type="text" name="callsign" value="${settings.callsign}" maxlength="10" required>
                    </div>
                    
                    <div class="form-group">
                        <label>${this.getTranslation('locator')}:</label>
                        <input type="text" name="locator" value="${settings.locator}" maxlength="6" required>
                    </div>
                    
                    <div class="form-group">
                        <label>${this.getTranslation('language')}:</label>
                        <select name="language">
                            <option value="de" ${settings.language === 'de' ? 'selected' : ''}>Deutsch</option>
                            <option value="en" ${settings.language === 'en' ? 'selected' : ''}>English</option>
                        </select>
                    </div>
                    
                    <div class="settings-buttons">
                        <button type="submit" class="btn-save">${this.getTranslation('save')}</button>
                        <button type="button" class="btn-reset" onclick="SetupModal.confirmReset()">${this.getTranslation('reset')}</button>
                        <button type="button" class="btn-cancel" onclick="SetupModal.hideSettingsModal()">${this.getTranslation('cancel')}</button>
                    </div>
                </form>
            </div>
        `;

        const modal = document.createElement('div');
        modal.id = 'settings-modal-overlay';
        modal.className = 'modal-overlay';
        modal.innerHTML = modalContent;
        document.body.appendChild(modal);

        // Handle form submit
        const form = modal.querySelector('#edit-form');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const rawCallsign = form.callsign.value.toUpperCase();
            const rawLocator = form.locator.value.toUpperCase();
            const language = form.language.value;

            // Validate BEFORE formatting
            if (!rawCallsign.match(/^[A-Z0-9\/]{2,10}$/)) {
                alert('❌ Ungültiges Rufzeichen! (z.B. OE3LCR)');
                return;
            }
            if (!rawLocator.match(/^[A-Z0-9]{4,6}$/)) {
                alert('❌ Ungültiger Maidenhead Locator! (z.B. JN87ct)');
                return;
            }

            // Format AFTER validation
            const newSettings = {
                callsign: rawCallsign,
                locator: UserSettings.formatLocator(rawLocator),
                language: language
            };
            
            console.log('📝 Saving new settings from modal:', newSettings);
            UserSettings.save(newSettings);
            
            // Verify saved
            const verify = UserSettings.load();
            console.log('✅ Verified after save:', verify);
            
            // Reload after short delay to ensure save completes
            setTimeout(() => {
                console.log('🔄 Reloading page with new settings...');
                location.reload();
            }, 300);
        });

        // Close on overlay click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hideSettingsModal();
            }
        });
    },

    hideSettingsModal() {
        const modal = document.getElementById('settings-modal-overlay');
        if (modal) {
            modal.remove();
        }
    },

    confirmReset() {
        if (confirm('⚠️ Alle Einstellungen zurücksetzen?')) {
            UserSettings.reset();
            location.reload();
        }
    },

    getTranslation(key) {
        const lang = UserSettings.get('language');
        const translations = {
            de: {
                'settings': 'Einstellungen',
                'callsign': 'Rufzeichen',
                'locator': 'Maidenhead Locator',
                'language': 'Sprache',
                'save': 'Speichern',
                'reset': 'Zurücksetzen',
                'cancel': 'Abbrechen',
                'welcome': 'Willkommen! Gib deine Daten ein:'
            },
            en: {
                'settings': 'Settings',
                'callsign': 'Callsign',
                'locator': 'Maidenhead Locator',
                'language': 'Language',
                'save': 'Save',
                'reset': 'Reset',
                'cancel': 'Cancel',
                'welcome': 'Welcome! Enter your data:'
            }
        };
        return translations[lang]?.[key] || translations['de'][key];
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    SetupModal.initialize();
});
