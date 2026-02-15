/**
 * Band Conditions v2 — Patch Override
 * Lädt nach band-conditions.js via dx-patch.php injection.
 * Features:
 *  - K-Index Degradation (K≥3 → GOOD→FAIR, K≥5 alles degradiert)
 *  - 160m: Tag=POOR, Nacht=80m-Gruppe
 *  - 6m/2m VHF: Aurora bei K≥5, sonst FAIR/POOR
 *  - Tooltip mit K, SFI, MUF pro Band
 *  - Timestamp "band-updated" im Widget
 *  - Auto-Refresh alle 30 Minuten
 */
(function () {
    'use strict';

    // --- Hilfsfunktionen ---

    function applyKIndexDegradation(condition, kIndex) {
        var k = parseInt(kIndex) || 0;
        if (k >= 5) {
            if (condition === 'GOOD') return 'FAIR';
            if (condition === 'FAIR') return 'POOR';
            return 'POOR';
        }
        if (k >= 3) {
            if (condition === 'GOOD') return 'FAIR';
        }
        return condition;
    }

    function getVHFCondition(kIndex) {
        var k = parseInt(kIndex) || 0;
        if (k >= 5) return 'AURORA';
        if (k >= 3) return 'FAIR';
        return 'POOR';
    }

    function get160mCondition(isDaytime, baseCondition80m) {
        if (isDaytime) return 'POOR';
        return baseCondition80m;
    }

    function updateBandBox(band, condition, kIndex, sfi, muf, isVHF) {
        // Band-IDs: band160-box, band80-box, band6-box, band2-box etc.
        // Entferne 'm' am Ende: '160m' → '160', '6m' → '6'
        var idBand = band.replace('m', '');
        var box = document.getElementById('band' + idBand + '-box');
        if (!box) return;

        var cssClass = 'band-box ';
        var displayText = condition;

        if (condition === 'AURORA') {
            cssClass += 'aurora';
            displayText = isVHF ? '🌌 AUR' : 'FAIR';
        } else if (condition === 'GOOD') {
            cssClass += 'good';
        } else if (condition === 'FAIR') {
            cssClass += 'fair';
        } else {
            cssClass += 'poor';
        }

        box.className = cssClass;

        var condEl = box.querySelector('.band-condition');
        if (condEl) condEl.textContent = displayText;

        var tooltip = isVHF
            ? (condition === 'AURORA'
                ? 'Aurora Scatter  K=' + kIndex
                : 'Lokal/Tropo  K=' + kIndex)
            : 'K=' + kIndex + '  SFI=' + sfi + '  MUF=' + muf + ' MHz';
        box.title = tooltip;
    }

    // --- Haupt-Update-Funktion ---

    var originalUpdate = window.updateAllBandConditions;

    window.updateAllBandConditions = async function () {
        try {
            var response = await fetch('/fetch-n0nbh.php');
            if (!response.ok) throw new Error('N0NBH fetch failed: HTTP ' + response.status);
            var n0nbh = await response.json();

            var kIndex = parseInt((n0nbh.solarData || {}).kIndex) || 0;
            var sfi    = (n0nbh.solarData || {}).sfi  || '--';
            var muf    = (n0nbh.solarData || {}).muf  || '--';

            // Condition-Map aufbauen
            var condMap = { day: {}, night: {} };
            var bandConds = n0nbh.bandConditions || [];
            for (var i = 0; i < bandConds.length; i++) {
                var bc   = bandConds[i];
                var time = bc.time || 'day';
                if (!condMap[time]) condMap[time] = {};
                condMap[time][bc.name] = (bc.condition || 'FAIR').toUpperCase();
            }

            // Tag / Nacht bestimmen (UTC)
            var hour      = new Date().getUTCHours();
            var isDaytime = (hour >= 6 && hour <= 18);
            var timeKey   = isDaytime ? 'day' : 'night';

            // HF-Band-Mapping
            var bandRanges = {
                '80m': '80m-40m', '60m': '80m-40m', '40m': '80m-40m',
                '30m': '30m-20m', '20m': '30m-20m',
                '17m': '17m-15m', '15m': '17m-15m',
                '12m': '12m-10m', '11m': '12m-10m', '10m': '12m-10m'
            };

            var bands = Object.keys(bandRanges);
            for (var j = 0; j < bands.length; j++) {
                var b        = bands[j];
                var range    = bandRanges[b];
                var rawCond  = (condMap[timeKey] && condMap[timeKey][range]) ? condMap[timeKey][range] : 'FAIR';
                var finalCond = applyKIndexDegradation(rawCond, kIndex);
                updateBandBox(b, finalCond, kIndex, sfi, muf, false);
            }

            // 160m — spezielle Behandlung
            var base80m  = (condMap[timeKey] && condMap[timeKey]['80m-40m'])
                           ? condMap[timeKey]['80m-40m'] : 'FAIR';
            var cond160  = get160mCondition(isDaytime, applyKIndexDegradation(base80m, kIndex));
            updateBandBox('160m', cond160, kIndex, sfi, muf, false);

            // 6m + 2m — VHF
            var vhfCond = getVHFCondition(kIndex);
            updateBandBox('6m', vhfCond, kIndex, sfi, muf, true);
            updateBandBox('2m', vhfCond, kIndex, sfi, muf, true);

            // Timestamp im Widget aktualisieren
            var tsEl = document.getElementById('band-updated');
            if (tsEl) {
                var now   = new Date();
                var hhmm  = now.getUTCHours().toString().padStart(2, '0') + ':' +
                            now.getUTCMinutes().toString().padStart(2, '0') + ' UTC';
                tsEl.textContent = hhmm;
            }

            console.log('\u2705 Band Conditions v2 — K=' + kIndex + ' SFI=' + sfi + ' MUF=' + muf);

        } catch (e) {
            console.error('band-patch.js update error:', e);
            if (typeof originalUpdate === 'function') originalUpdate();
        }
    };

    // --- Auto-Refresh alle 30 Minuten ---
    setInterval(function () {
        if (typeof window.updateAllBandConditions === 'function') {
            window.updateAllBandConditions();
        }
    }, 30 * 60 * 1000);

    // --- Initial load ---
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.updateAllBandConditions();
        });
    } else {
        setTimeout(function () {
            window.updateAllBandConditions();
        }, 500);
    }

})();
