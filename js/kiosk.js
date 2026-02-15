// kiosk.js — Vollbild Seiten-System (Vorschlag A)
const PAGE_KEY   = 'gwen_widget_pages';
const ROTATE_MS  = 30000;
let currentPage  = 1;
let rotateTimer  = null;
let kioskActive  = false;
let kioskSavedLayout = null;  // Original-Layout vor Kiosk speichern

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
    p1:   { label:'P1',  bg:'rgba(0,255,136,0.2)',   border:'#00ff88', color:'#00ff88' },
    p2:   { label:'P2',  bg:'rgba(112,161,255,0.2)', border:'#70a1ff', color:'#70a1ff' },
    both: { label:'1+2', bg:'rgba(255,165,0,0.2)',   border:'#ffa502', color:'#ffa502' },
};
const CYCLE = { p1:'p2', p2:'both', both:'p1' };

function applyBadge(badge, page) {
    const c = BADGE_CFG[page] || BADGE_CFG.p1;
    badge.textContent = c.label;
    // Object.assign statt cssText= : nur Aussehen-Properties setzen,
    // position/top/right/z-index etc. bleiben erhalten!
    Object.assign(badge.style, {
        fontSize:       '0.65em',
        fontWeight:     '700',
        padding:        '2px 6px',
        borderRadius:   '4px',
        cursor:         'pointer',
        background:     c.bg,
        border:         '1px solid ' + c.border,
        color:          c.color,
        userSelect:     'none',
        flexShrink:     '0',
        transition:     'all 0.2s',
    });
}

function initPageBadges() {
    document.querySelectorAll('.grid-stack-item').forEach(item => {
        const id = item.id;
        if (!id || item.querySelector('.page-badge')) return;

        const badge = document.createElement('span');
        badge.className = 'page-badge';
        applyBadge(badge, getWidgetPage(id));

        badge.addEventListener('click', e => {
            e.stopPropagation();
            const next = CYCLE[getWidgetPage(id)] || 'p1';
            savePage(id, next);
            applyBadge(badge, next);
            // Kiosk aktiv → sofort neu anwenden
            if (kioskActive) showKioskPage(currentPage);
        });

        const cardHeader = item.querySelector('.card-header');
        if (cardHeader) {
            // Normal: rechts im Card-Header — margin-left:auto explizit für Flex-Positionierung
            badge.style.marginLeft = 'auto';
            cardHeader.appendChild(badge);
        } else if (id === 'widget-header') {
            // Header-Widget: Badge position:absolute am Widget-Corner (NICHT in Flex-Flow).
            // So hat margin-left:auto aus applyBadge() keinen Flex-Effekt → kein Sprung.
            badge.style.cssText += 'position:absolute; top:6px; right:8px; z-index:200;';
            item.appendChild(badge);
        } else {
            // Fallback: direkt auf .grid-stack-item (position:absolute, kein overflow-Clip)
            badge.style.cssText += 'position:absolute; top:6px; right:6px; z-index:100;';
            item.appendChild(badge);
        }
    });
}

// === Kiosk Seiten-Filter — reines CSS, kein GridStack-Eingriff ===
// removeWidget/makeWidget/compact() haben immer wieder Widgets versteckt →
// jetzt nur noch visibility/opacity/pointerEvents per CSS steuern.
function showKioskPage(n) {
    currentPage = n;

    // Altes Kiosk-System nutzt view-top/view-bottom CSS-Klassen die Widgets per
    // "display:none !important" verstecken → diese Klassen IMMER entfernen!
    document.body.classList.remove('view-top', 'view-bottom');

    document.querySelectorAll('.grid-stack-item').forEach(item => {
        if (!item.id) return;
        const page = getWidgetPage(item.id);
        const show = (page === 'both') || (page === 'p' + n);

        item.style.visibility    = show ? '' : 'hidden';
        item.style.opacity       = show ? '' : '0';
        item.style.pointerEvents = show ? '' : 'none';
        // display nie anfassen — GridStack managed das selbst
    });

    const btn = document.getElementById('kiosk-page-btn');
    if (btn) btn.textContent = '▶ S' + n;
}

function nextKioskPage() {
    showKioskPage(currentPage === 1 ? 2 : 1);
    resetRotateTimer();
}

function startRotateTimer() { stopRotateTimer(); rotateTimer = setInterval(nextKioskPage, ROTATE_MS); }
function stopRotateTimer()   { clearInterval(rotateTimer); rotateTimer = null; }
function resetRotateTimer()  { if (kioskActive) startRotateTimer(); }

// Kiosk ein/aus
// WICHTIG: _origToggleKiosk wird NICHT aufgerufen — es würde 'kiosk-mode' auf body setzen
// und die alten CSS-Regeln (view-bottom #kiosk-sun {display:none!important}) würden feuern.
// Stattdessen: nur Fullscreen-API direkt aufrufen.
window.toggleKioskMode = function() {
    kioskActive = !kioskActive;

    if (kioskActive) {
        // Fullscreen
        const el = document.documentElement;
        if (el.requestFullscreen) el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();

        // Altes System defensiv stoppen (falls doch aktiv)
        if (typeof stopAutoRotate === 'function') stopAutoRotate();
        document.body.classList.remove('kiosk-mode', 'view-top', 'view-bottom');

        // Layout speichern
        kioskSavedLayout = [];
        if (window.grid) {
            window.grid.getGridItems().forEach(el => {
                const n = el.gridstackNode;
                if (n && el.id) kioskSavedLayout.push({id:el.id, x:n.x, y:n.y, w:n.w, h:n.h});
            });
        }
        showKioskPage(1);
        startRotateTimer();
        const btn = document.getElementById('kiosk-page-btn');
        if (btn) btn.style.display = 'inline-block';

    } else {
        // Exit Fullscreen
        if (document.exitFullscreen) document.exitFullscreen();
        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();

        stopRotateTimer();
        document.body.classList.remove('kiosk-mode', 'view-top', 'view-bottom');

        // Alle Widgets einblenden
        document.querySelectorAll('.grid-stack-item').forEach(item => {
            item.style.visibility    = '';
            item.style.opacity       = '';
            item.style.pointerEvents = '';
        });
        // Original-Layout wiederherstellen
        if (kioskSavedLayout && window.grid) {
            kioskSavedLayout.forEach(s => {
                const el = document.getElementById(s.id);
                if (el) window.grid.update(el, {x:s.x, y:s.y, w:s.w, h:s.h});
            });
            kioskSavedLayout = null;
        }
        const btn = document.getElementById('kiosk-page-btn');
        if (btn) btn.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', () => setTimeout(initPageBadges, 600));
