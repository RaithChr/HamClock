<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OE3LCR Ham Radio Dashboard – Live Solar Data, Band Conditions, DX Cluster, Satelliten-Tracking und Weltraumwetter für Amateurfunker.">
    <title>OE3LCR - Ham Radio Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <script src="/satellite.min.js"></script>
    <script src="/js/user-settings.js"></script>
    <script src="/js/band-conditions.js"></script>
    <script>
        // ============================================
        // FULL TRANSLATIONS (DE + EN)
        // ============================================
        const translations = {
            de: {
                settings_btn: '⚙️ Einstellungen', support_btn: '❤️ Support', legend_btn: 'ℹ️ Legende', fullscreen_btn: '📺 Vollbild',
                modal_callsign: 'Rufzeichen:', modal_locator: 'Maidenhead Locator:', modal_language: 'Sprache:',
                modal_setup_title: 'Willkommen zu OE3LCR Dashboard', modal_setup_desc: 'Willkommen! Gib deine Daten ein:',
                weather_quiet: 'Ruhig', weather_active: 'Aktiv', weather_storm: '⚠️ Sturm',
                card_qth: 'QTH', card_bands: 'Band Conditions', card_weather: 'Weather & Space Weather',
                card_satellites: 'Aktive Satelliten', card_dx: 'DX Cluster Spots', card_system: 'System Status',
                sunrise_sunset: '☀️ Sunrise/Sunset', moonrise_set: 'Moonrise/set',
                local_weather: '📍 LOKALES WETTER', space_weather: '⚡ WELTRAUMWETTER',
                temp: '🌡️ Temperatur', humidity: '💧 Feuchtigkeit', wind: '💨 Wind',
                kindex: 'K-Index:', solarflux: 'Solar Flux:', sunspots: 'Sunspot Number:',
                aindex: 'A-Index:', aurora: 'Aurora:', muf: 'MUF (est.):', sw_status: 'Status:',
                callsign_label: 'Callsign', locator_label: 'QTH Locator', server_label: 'Server', status_label: 'Status',
                live_metrics: '⚙️ LIVE SYSTEM METRICS', cpu: '🖥️ CPU', ram: '🧠 RAM', disk: '💾 Disk', uptime_label: '⏱️ Uptime:',
                sun_title: '☀️ Sonne (NASA SDO - Live)', footer_updated: 'Last updated:',
                w_clear:'Klar', w_mostly_clear:'Heiter', w_partly_cloudy:'Bedeckt', w_overcast:'Bewölkt', w_fog:'Neblig',
                w_light_rain:'L. Regen', w_mod_rain:'M. Regen', w_heavy_rain:'St. Regen', w_rain:'Regen',
                w_snow:'Schnee', w_heavy_snow:'St. Schnee', w_unknown:'Unbekannt',
                sat_visible: '✅ Sichtbar', sat_below: '⬇️ Unter Horizont', sat_loading: 'Lade TLE...',
                tle_source: 'TLE Quelle:', tle_updated: 'TLE aktualisiert:',
                moon_waxing: 'zunehmend', moon_waning: 'abnehmend',
            },
            en: {
                settings_btn: '⚙️ Settings', support_btn: '❤️ Support', legend_btn: 'ℹ️ Legend', fullscreen_btn: '📺 Fullscreen',
                modal_callsign: 'Callsign:', modal_locator: 'Maidenhead Locator:', modal_language: 'Language:',
                modal_setup_title: 'Welcome to OE3LCR Dashboard', modal_setup_desc: 'Welcome! Enter your details:',
                weather_quiet: 'Quiet', weather_active: 'Active', weather_storm: '⚠️ Storm',
                card_qth: 'QTH', card_bands: 'Band Conditions', card_weather: 'Weather & Space Weather',
                card_satellites: 'Active Satellites', card_dx: 'DX Cluster Spots', card_system: 'System Status',
                sunrise_sunset: '☀️ Sunrise/Sunset', moonrise_set: 'Moonrise/set',
                local_weather: '📍 LOCAL WEATHER', space_weather: '⚡ SPACE WEATHER',
                temp: '🌡️ Temperature', humidity: '💧 Humidity', wind: '💨 Wind',
                kindex: 'K-Index:', solarflux: 'Solar Flux:', sunspots: 'Sunspot Number:',
                aindex: 'A-Index:', aurora: 'Aurora:', muf: 'MUF (est.):', sw_status: 'Status:',
                callsign_label: 'Callsign', locator_label: 'QTH Locator', server_label: 'Server', status_label: 'Status',
                live_metrics: '⚙️ LIVE SYSTEM METRICS', cpu: '🖥️ CPU', ram: '🧠 RAM', disk: '💾 Disk', uptime_label: '⏱️ Uptime:',
                sun_title: '☀️ Sun (NASA SDO - Live)', footer_updated: 'Last updated:',
                w_clear:'Clear', w_mostly_clear:'Mostly Clear', w_partly_cloudy:'Partly Cloudy', w_overcast:'Overcast', w_fog:'Foggy',
                w_light_rain:'Light Rain', w_mod_rain:'Mod. Rain', w_heavy_rain:'Heavy Rain', w_rain:'Rain',
                w_snow:'Snow', w_heavy_snow:'Heavy Snow', w_unknown:'Unknown',
                sat_visible: '✅ Visible', sat_below: '⬇️ Below Horizon', sat_loading: 'Loading TLE...',
                tle_source: 'TLE Source:', tle_updated: 'TLE updated:',
                moon_waxing: 'waxing', moon_waning: 'waning',
            }
        };
        function t(key) {
            const lang = (typeof UserSettings!=='undefined' && UserSettings.get) ? (UserSettings.get('language')||'de') : 'de';
            return translations[lang]?.[key] || translations.de[key] || key;
        }
        function updatePageTranslations() { document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.getAttribute('data-i18n')); }); }
        function updateModalTranslations() {
            const e = (id) => document.getElementById(id);
            if (e('modal-title')) e('modal-title').textContent = t('modal_setup_title');
            if (e('welcome-text')) e('welcome-text').textContent = t('modal_setup_desc');
        }
        window.addEventListener('settingsChanged', () => { updatePageTranslations(); updateModalTranslations(); });
        document.addEventListener('DOMContentLoaded', () => { updateModalTranslations(); updatePageTranslations(); });
    </script>
    <link rel="stylesheet" href="/css/gridstack.min.css">
    <link rel="stylesheet" href="/css/dashboard.css?v=20260216y">
    <link rel="stylesheet" href="/css/dashboard.css?v=20260216y">
</head>