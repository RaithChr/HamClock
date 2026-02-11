<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OE3LCR Ham Radio Dashboard – Live Solar Data, Band Conditions, DX Cluster, Satelliten-Tracking und Weltraumwetter für Amateurfunker.">
    <title>OE3LCR - Ham Radio Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%23f5f5f5'/%3E%3Cdefs%3E%3Cfilter id='glow'%3E%3CfeGaussianBlur stdDeviation='2' result='coloredBlur'/%3E%3CfeMerge%3E%3CfeMergeNode in='coloredBlur'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Ccircle cx='100' cy='100' r='70' fill='none' stroke='%2300bb77' stroke-width='1.5' opacity='0.4' filter='url(%23glow)'/%3E%3Crect x='85' y='50' width='30' height='20' fill='%2300ff88' rx='3' filter='url(%23glow)'/%3E%3Crect x='55' y='65' width='25' height='10' fill='%2300cc66' rx='2' filter='url(%23glow)'/%3E%3Crect x='120' y='65' width='25' height='10' fill='%2300cc66' rx='2' filter='url(%23glow)'/%3E%3Cline x1='100' y1='50' x2='100' y2='25' stroke='%2300ff88' stroke-width='2' stroke-linecap='round' filter='url(%23glow)'/%3E%3Ccircle cx='100' cy='50' r='3' fill='%2300ff88' filter='url(%23glow)'/%3E%3C/svg%3E">
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
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>