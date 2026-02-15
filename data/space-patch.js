/**
 * space-patch.js — Space Weather Extended Updater
 * Lädt via dx-patch.php injection (vor </body>).
 * Holt Daten von /data/fetch-space-data.php (N0NBH + NOAA SWPC).
 * 
 * Befüllt:
 *   - Bestehende: kIndexCombined, solarFluxCombined, aIndexCombined,
 *                 auroraCombined, mufCombined, spaceWeatherCombined
 *   - Neue: xrayCombined, protonFluxCombined, electronFluxCombined
 * 
 * Farbkodierung:
 *   X-Ray: A/B=grün, C=orange, M=rot, X=magenta
 *   Proton: <10=grün, 10-100=orange, >100=rot
 *   K-Index: 0-2=grün, 3-4=orange, 5+=rot
 */

(function () {
    'use strict';

    var REFRESH_MS = 10 * 60 * 1000; // 10 Minuten

    function setEl(id, text, color) {
        var el = document.getElementById(id);
        if (!el) return;
        el.textContent = text;
        if (color) el.style.color = color;
    }

    function xrayColor(cls) {
        if (!cls || cls.length === 0) return '#aaa';
        var letter = cls.charAt(0).toUpperCase();
        if (letter === 'X') return '#ff00ff';
        if (letter === 'M') return '#ff4444';
        if (letter === 'C') return '#ffaa00';
        return '#00ff88'; // A, B
    }

    function protonColor(flux) {
        if (flux > 100)  return '#ff4444';
        if (flux >= 10)  return '#ffaa00';
        return '#00ff88';
    }

    function kIndexColor(k) {
        if (k >= 5) return '#ff4444';
        if (k >= 3) return '#ffaa00';
        return '#00ff88';
    }

    function spaceStatusColor(status) {
        var s = (status || '').toLowerCase();
        if (s.indexOf('severe') !== -1) return '#ff00ff';
        if (s.indexOf('storm')  !== -1) return '#ff4444';
        if (s.indexOf('unsettled') !== -1) return '#ffaa00';
        if (s.indexOf('active') !== -1) return '#ffdd00';
        return '#00ff88';
    }

    function auroraColor(aurora) {
        var a = (aurora || '').toLowerCase();
        if (a.indexOf('active') !== -1) return '#ff4444';
        if (a.indexOf('visible') !== -1) return '#64c8ff';
        return '#00ff88';
    }

    function formatElectron(flux) {
        if (flux <= 0) return '-- pfu';
        if (flux >= 1000) return flux.toFixed(0) + ' pfu';
        if (flux >= 10)   return flux.toFixed(1) + ' pfu';
        return flux.toFixed(2) + ' pfu';
    }

    function formatProton(flux) {
        if (flux <= 0) return '< 0.1 pfu';
        if (flux < 0.1)  return '< 0.1 pfu';
        if (flux >= 1000) return flux.toFixed(0) + ' pfu';
        if (flux >= 10)   return flux.toFixed(1) + ' pfu';
        return flux.toFixed(2) + ' pfu';
    }

    function update() {
        fetch('/data/fetch-space-data.php')
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (d) {
                if (!d.success) throw new Error('API error');

                var k = parseInt(d.kIndex) || 0;

                // Bestehende Felder
                setEl('kIndexCombined',    k + ' (' + (k >= 5 ? 'Storm' : k >= 3 ? 'Active' : 'Quiet') + ')', kIndexColor(k));
                setEl('solarFluxCombined', d.sfi,            '#fff');
                setEl('aIndexCombined',    d.aIndex,         k >= 5 ? '#ff4444' : '#fff');
                setEl('auroraCombined',    d.aurora,         auroraColor(d.aurora));
                setEl('mufCombined',       d.muf || '-- MHz', '#fff');
                setEl('spaceWeatherCombined', d.spaceStatus, spaceStatusColor(d.spaceStatus));

                // Neue Felder
                setEl('xrayCombined',       d.xray || '--',             xrayColor(d.xray));
                setEl('protonFluxCombined', formatProton(d.protonFlux),  protonColor(d.protonFlux));
                setEl('electronFluxCombined', formatElectron(d.electronFlux), '#aad4ff');

                // Timestamp aktualisieren (band-updated Span, falls vorhanden)
                var ts = document.getElementById('band-updated');
                if (ts) ts.textContent = d.updated || '--';
            })
            .catch(function (err) {
                console.warn('[space-patch] fetch error:', err);
            });
    }

    // Beim Laden sofort + danach alle 10 Min
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', update);
    } else {
        update();
    }

    setInterval(update, REFRESH_MS);

})();
