// dashboard-grid.js – Gridstack v12.4.2 | OE3LCR
const GRID_STORAGE_KEY = 'gwen_grid_layout';

const DEFAULT_LAYOUT = [
    {id:'widget-header',        x:0,  y:0,  w:12, h:2,  minW:6},
    {id:'widget-sun',           x:0,  y:2,  w:3,  h:5,  minW:2},
    {id:'widget-qth',           x:3,  y:2,  w:3,  h:5,  minW:2},
    {id:'widget-bands',         x:6,  y:2,  w:3,  h:5,  minW:2},
    {id:'widget-clock',         x:9,  y:2,  w:3,  h:5,  minW:2},
    {id:'widget-weather-local', x:0,  y:7,  w:4,  h:4,  minW:2},
    {id:'widget-weather-space', x:4,  y:7,  w:8,  h:4,  minW:3},
    {id:'widget-satellites',    x:0,  y:11, w:4,  h:6,  minW:2},
    {id:'widget-dx',            x:4,  y:11, w:4,  h:6,  minW:2},
    {id:'widget-system',        x:8,  y:11, w:4,  h:6,  minW:2},
];

let grid = null;

function initGrid() {
    grid = GridStack.init({
        column: 12,
        cellHeight: 80,
        animate: false,
        float: true,
        resizable: { handles: 'se,sw' },
        draggable: { handle: '.card-header' },
        oneColumnSize: 768,
    });

    // Layout laden: grid.update() pro Widget (sicher bei float:true)
    const saved = localStorage.getItem(GRID_STORAGE_KEY);
    if (saved) {
        try {
            const items = JSON.parse(saved);
            items.forEach(item => {
                const el = document.getElementById(item.id);
                if (el) grid.update(el, {x:item.x, y:item.y, w:item.w, h:item.h});
            });
            console.log('[Grid] ✅ Layout aus localStorage geladen');
        } catch(e) {
            console.warn('[Grid] Fehler beim Laden:', e);
            applyDefaultLayout();
        }
    } else {
        applyDefaultLayout();
    }

    // Nur beim Loslassen speichern
    grid.on('dragstop resizestop', saveLayout);
}

function saveLayout() {
    const items = grid.save(false).map(n => ({
        id: n.id, x: n.x, y: n.y, w: n.w, h: n.h
    }));
    localStorage.setItem(GRID_STORAGE_KEY, JSON.stringify(items));
}

function applyDefaultLayout() {
    DEFAULT_LAYOUT.forEach(item => {
        const el = document.getElementById(item.id);
        if (el) grid.update(el, {x:item.x, y:item.y, w:item.w, h:item.h});
    });
}

function resetGridLayout() {
    localStorage.removeItem(GRID_STORAGE_KEY);
    applyDefaultLayout();
    console.log('[Grid] Layout zurückgesetzt');
}

function setGridKioskMode(active) {
    if (!grid) return;
    active ? grid.disable() : grid.enable();
}

document.addEventListener('DOMContentLoaded', initGrid);
