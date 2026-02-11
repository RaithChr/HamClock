// dashboard-grid.js – Gridstack init + layout persistence
// OE3LCR Dashboard | gridstack v12.4.2

const GRID_STORAGE_KEY = 'gwen_grid_layout';

const DEFAULT_LAYOUT = [
    {id:'widget-sun',        x:0, y:0,  w:4, h:5, minW:2},
    {id:'widget-qth',        x:4, y:0,  w:4, h:5, minW:2},
    {id:'widget-bands',      x:8, y:0,  w:4, h:5, minW:3},
    {id:'widget-weather',    x:0, y:5,  w:12,h:4, minW:4},
    {id:'widget-satellites', x:0, y:9,  w:4, h:6, minW:2},
    {id:'widget-dx',         x:4, y:9,  w:4, h:6, minW:2},
    {id:'widget-system',     x:8, y:9,  w:4, h:6, minW:2},
];

let grid = null;

function initGrid() {
    grid = GridStack.init({
        column: 12,
        cellHeight: 80,
        animate: true,
        float: false,
        resizable: { handles: 'se, sw' },
        draggable: { handle: '.card-header' },
        disableOneColumnMode: false,
        oneColumnSize: 768,
    });

    // Load saved layout or use default
    const saved = localStorage.getItem(GRID_STORAGE_KEY);
    if (saved) {
        try {
            const layout = JSON.parse(saved);
            grid.load(layout);
        } catch(e) {
            console.warn('[Grid] Could not load saved layout:', e);
            applyDefaultLayout();
        }
    } else {
        applyDefaultLayout();
    }

    // Save on change
    grid.on('change', () => {
        const layout = grid.save(false);
        localStorage.setItem(GRID_STORAGE_KEY, JSON.stringify(layout));
    });

    console.log('[Grid] Initialized ✅');
}

function applyDefaultLayout() {
    DEFAULT_LAYOUT.forEach(item => {
        const el = document.getElementById(item.id);
        if (el) grid.update(el, {x: item.x, y: item.y, w: item.w, h: item.h});
    });
}

function resetGridLayout() {
    localStorage.removeItem(GRID_STORAGE_KEY);
    applyDefaultLayout();
    console.log('[Grid] Layout reset to default ✅');
}

// Kiosk mode: disable/enable grid drag
function setGridKioskMode(enabled) {
    if (!grid) return;
    if (enabled) {
        grid.disable();
    } else {
        grid.enable();
    }
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', initGrid);
