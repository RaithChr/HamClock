// kiosk.js — Vollbild Seiten-System mit Off-Option
const PAGE_KEY        = 'gwen_widget_pages';
const KIOSK_P1_KEY    = 'gwen_kiosk_layout_p1';
const KIOSK_P2_KEY    = 'gwen_kiosk_layout_p2';
const ROTATE_MS       = 30000;
let currentPage       = 1;
let rotateTimer       = null;
let kioskActive       = false;
let kioskPageInited   = false;
let kioskSavedLayout  = null;

const DEFAULT_PAGES = {
    'widget-header':        'both',   // Reset-Default: alle Widgets auf 1+2
    'widget-sun':           'both',
    'widget-qth':           'both',
    'widget-bands':         'both',
    'widget-clock':         'both',
    'widget-weather-local': 'both',
    'widget-weather-space': 'both',
    'widget-satellites':    'both',
    'widget-dx':            'both',
    'widget-system':        'both',
};

const KIOSK_PAGE_LAYOUTS = {
    2: {
        'widget-clock':      {x: 0},
        'widget-dx':         {x: 0},
        'widget-satellites': {x: 4},
    }
};

// ── localStorage Helpers ───────────────────────────────────────────────────

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
function getKioskLayout(page) {
    const key = page === 1 ? KIOSK_P1_KEY : KIOSK_P2_KEY;
    try { return JSON.parse(localStorage.getItem(key)) || null; }
    catch(e) { return null; }
}
function saveKioskLayout(page) {
    if (!window.grid) return;
    const key = page === 1 ? KIOSK_P1_KEY : KIOSK_P2_KEY;
    const items = [];
    window.grid.getGridItems().forEach(el => {
        const n = el.gridstackNode;
        if (n && el.id) items.push({id:el.id, x:n.x, y:n.y, w:n.w, h:n.h});
    });
    localStorage.setItem(key, JSON.stringify(items));
}

// ── Layout für eine Kiosk-Seite anwenden ──────────────────────────────────

function applyKioskPageLayout(n) {
    if (!window.grid) return;
    const saved = getKioskLayout(n);
    if (saved) {
        saved.forEach(item => {
            const el = document.getElementById(item.id);
            if (el) window.grid.update(el, {x:item.x, y:item.y, w:item.w, h:item.h});
        });
    } else {
        if (kioskSavedLayout) {
            kioskSavedLayout.forEach(s => {
                const el = document.getElementById(s.id);
                if (el) window.grid.update(el, {x:s.x, y:s.y});
            });
        }
        const adj = KIOSK_PAGE_LAYOUTS[n];
        if (adj) {
            Object.entries(adj).forEach(([id, pos]) => {
                const el = document.getElementById(id);
                if (el) window.grid.update(el, pos);
            });
        }
    }
}

// ── Badges mit Off-Option ─────────────────────────────────────────────────

const BADGE_CFG = {
    p1:   { label:'P1',  bg:'rgba(0,255,136,0.2)',   border:'#00ff88', color:'#00ff88' },
    p2:   { label:'P2',  bg:'rgba(112,161,255,0.2)', border:'#70a1ff', color:'#70a1ff' },
    both: { label:'1+2', bg:'rgba(255,165,0,0.2)',   border:'#ffa502', color:'#ffa502' },
    off:  { label:'OFF', bg:'rgba(255,0,0,0.25)',    border:'#ff4444', color:'#ff4444' },
};

// Zyklus: p1 → p2 → both → off → p1
const CYCLE = { p1:'p2', p2:'both', both:'off', off:'p1' };

function applyBadge(badge, page) {
    const c = BADGE_CFG[page] || BADGE_CFG.p1;
    badge.textContent = c.label;
    Object.assign(badge.style, {
        fontSize:'0.65em', fontWeight:'700', padding:'2px 6px', borderRadius:'4px',
        cursor:'pointer', background:c.bg, border:'1px solid '+c.border, color:c.color,
        userSelect:'none', flexShrink:'0', transition:'all 0.2s',
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
            const cur  = getWidgetPage(id);
            const next = CYCLE[cur] || 'p1';
            savePage(id, next);
            applyBadge(badge, next);
            if (kioskActive) showKioskPage(currentPage);
        });
        const cardHeader = item.querySelector('.card-header');
        if (cardHeader) {
            badge.style.marginLeft = 'auto';
            cardHeader.appendChild(badge);
        } else if (id === 'widget-header') {
            badge.style.cssText += 'position:absolute; top:6px; right:8px; z-index:200;';
            item.appendChild(badge);
        } else {
            badge.style.cssText += 'position:absolute; top:6px; right:6px; z-index:100;';
            item.appendChild(badge);
        }
    });
}

// ── Kiosk-Seite anzeigen ─────────────────────────────────────────────────

function showKioskPage(n) {
    if (kioskPageInited && currentPage !== n) {
        saveKioskLayout(currentPage);
    }
    kioskPageInited = true;
    currentPage = n;
    document.body.classList.remove('view-top', 'view-bottom');

    applyKioskPageLayout(n);

    // Sichtbarkeit: page-match ODER off → ausblenden
    document.querySelectorAll('.grid-stack-item').forEach(item => {
        if (!item.id) return;
        const page = getWidgetPage(item.id);
        const show = (page !== 'off') && ((page === 'both') || (page === 'p' + n));
        item.style.visibility    = show ? '' : 'hidden';
        item.style.opacity       = show ? '' : '0';
        item.style.pointerEvents = show ? '' : 'none';
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

// ── Kiosk ein/aus ─────────────────────────────────────────────────────────

window.toggleKioskMode = function() {
    kioskActive = !kioskActive;
    window.kioskIsActive = kioskActive;

    if (kioskActive) {
        const el = document.documentElement;
        if (el.requestFullscreen) el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();

        if (typeof stopAutoRotate === 'function') stopAutoRotate();
        document.body.classList.remove('kiosk-mode', 'view-top', 'view-bottom');

        kioskSavedLayout = [];
        kioskPageInited  = false;
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
        if (kioskPageInited) saveKioskLayout(currentPage);

        if (document.exitFullscreen) document.exitFullscreen();
        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();

        stopRotateTimer();
        kioskPageInited = false;
        document.body.classList.remove('kiosk-mode', 'view-top', 'view-bottom');

        // Alle Widgets einblenden (auch off-Widgets im Normal-Modus sichtbar)
        document.querySelectorAll('.grid-stack-item').forEach(item => {
            item.style.visibility    = '';
            item.style.opacity       = '';
            item.style.pointerEvents = '';
        });
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
