// kiosk.js — Vollbild Seiten-System (Vorschlag A)
// P1 | P2 | Beide pro Widget, Auto-Rotate im Kiosk-Modus

const PAGE_KEY     = 'gwen_widget_pages';
const ROTATE_MS    = 30000; // 30 Sekunden pro Seite
let   currentPage  = 1;
let   rotateTimer  = null;
let   kioskActive  = false;

// Defaults: alle Widgets auf Seite 1
const DEFAULT_PAGES = {
    'widget-header':        'both',
    'widget-sun':           'p1',
    'widget-qth':           'p1',
    'widget-bands':         'p1',
    'widget-clock':         'both',
    'widget-weather-local': 'p2',
    'widget-weather-space': 'p2',
    'widget-satellites':    'p2',
    'widget-dx':            'p2',
    'widget-system':        'p1',
};

function getPages() {
    try { return JSON.parse(localStorage.getItem(PAGE_KEY)) || {}; }
    catch(e) { return {}; }
}
function savePage(id, val) {
    const p = getPages(); p[id] = val;
    localStorage.setItem(PAGE_KEY, JSON.stringify(p));
}
function getWidgetPage(id) {
    return getPages()[id] || DEFAULT_PAGES[id] || 'p1';
}

const BADGE_CFG = {
    p1:   { label:'P1',    bg:'rgba(0,255,136,0.2)',  border:'#00ff88', color:'#00ff88' },
    p2:   { label:'P2',    bg:'rgba(112,161,255,0.2)',border:'#70a1ff', color:'#70a1ff' },
    both: { label:'1+2',   bg:'rgba(255,165,0,0.2)',  border:'#ffa502', color:'#ffa502' },
};
const CYCLE = { p1:'p2', p2:'both', both:'p1' };

function applyBadge(badge, page) {
    const c = BADGE_CFG[page] || BADGE_CFG.p1;
    badge.textContent    = c.label;
    badge.style.cssText  = `
        margin-left:auto; font-size:0.65em; font-weight:700;
        padding:2px 6px; border-radius:4px; cursor:pointer;
        background:${c.bg}; border:1px solid ${c.border}; color:${c.color};
        user-select:none; flex-shrink:0; transition:all 0.2s;
    `;
}

function initPageBadges() {
    document.querySelectorAll('.grid-stack-item').forEach(item => {
        const id = item.id;
        if (!id) return;

        // Schon ein Badge? Überspringen
        if (item.querySelector('.page-badge')) return;

        const badge = document.createElement('span');
        badge.className = 'page-badge';
        applyBadge(badge, getWidgetPage(id));

        badge.addEventListener('click', e => {
            e.stopPropagation();
            const next = CYCLE[getWidgetPage(id)] || 'p1';
            savePage(id, next);
            applyBadge(badge, next);
        });

        const header = item.querySelector('.card-header');
        if (header) {
            // Normal: am Card-Header anhängen
            header.appendChild(badge);
        } else {
            // Fallback: absolut positioniert oben rechts
            badge.style.cssText += `
                position:absolute; top:6px; right:6px; z-index:50;
            `;
            const content = item.querySelector('.grid-stack-item-content');
            if (content) content.appendChild(badge);
        }
    });
}

// === Kiosk Seiten-Filter ===
function showKioskPage(n) {
    currentPage = n;
    document.querySelectorAll('.grid-stack-item').forEach(item => {
        const page = getWidgetPage(item.id);
        const show = page === 'both' || page === 'p' + n;
        item.style.visibility = show ? 'visible' : 'hidden';
        item.style.opacity    = show ? '1' : '0';
    });
    // Seiten-Anzeige im Button aktualisieren
    const btn = document.getElementById('kiosk-page-btn');
    if (btn) btn.textContent = '▶ S' + n;
}

function nextKioskPage() {
    showKioskPage(currentPage === 1 ? 2 : 1);
    resetRotateTimer();
}

function startRotateTimer() {
    stopRotateTimer();
    rotateTimer = setInterval(nextKioskPage, ROTATE_MS);
}
function stopRotateTimer()  { clearInterval(rotateTimer); rotateTimer = null; }
function resetRotateTimer() { if (kioskActive) startRotateTimer(); }

// Kiosk aktivieren/deaktivieren (Hook für toggleKioskMode)
window._origToggleKiosk = window.toggleKioskMode || function(){};
window.toggleKioskMode = function() {
    window._origToggleKiosk();
    kioskActive = !kioskActive;
    if (kioskActive) {
        showKioskPage(1);
        startRotateTimer();
        // Seiten-Pfeil-Button einblenden
        const btn = document.getElementById('kiosk-page-btn');
        if (btn) btn.style.display = 'inline-block';
    } else {
        stopRotateTimer();
        // Alle Widgets wieder einblenden
        document.querySelectorAll('.grid-stack-item').forEach(item => {
            item.style.visibility = '';
            item.style.opacity    = '';
        });
        const btn = document.getElementById('kiosk-page-btn');
        if (btn) btn.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initPageBadges, 600);
});
