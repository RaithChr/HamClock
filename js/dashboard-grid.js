// dashboard-grid.js – Gridstack v12.4.2 | OE3LCR Dashboard
const GRID_STORAGE_KEY = 'gwen_grid_layout';

const DEFAULT_LAYOUT = [
    {id:'widget-sun',           x:0,  y:0, w:3, h:5, minW:2},
    {id:'widget-qth',           x:3,  y:0, w:3, h:5, minW:2},
    {id:'widget-bands',         x:6,  y:0, w:4, h:5, minW:3},
    {id:'widget-clock',         x:10, y:0, w:2, h:5, minW:2},
    {id:'widget-weather-local', x:0,  y:5, w:4, h:4, minW:2},
    {id:'widget-weather-space', x:4,  y:5, w:8, h:4, minW:3},
    {id:'widget-satellites',    x:0,  y:9, w:4, h:6, minW:2},
    {id:'widget-dx',            x:4,  y:9, w:4, h:6, minW:2},
    {id:'widget-system',        x:8,  y:9, w:4, h:6, minW:2},
];

let grid = null;

function initGrid() {
    grid = GridStack.init({
        column: 12,
        cellHeight: 80,
        animate: true,
        float: false,
        resizable: { handles: 'se,sw' },
        draggable: { handle: '.card-header' },
        oneColumnSize: 768,
    });

    const saved = localStorage.getItem(GRID_STORAGE_KEY);
    if (saved) {
        try { grid.load(JSON.parse(saved)); }
        catch(e) { applyDefaultLayout(); }
    } else {
        applyDefaultLayout();
    }

    grid.on('change', () => {
        localStorage.setItem(GRID_STORAGE_KEY, JSON.stringify(grid.save(false)));
    });
    console.log('[Grid] ✅ 9 Widgets initialisiert');
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
}

function setGridKioskMode(active) {
    if (!grid) return;
    active ? grid.disable() : grid.enable();
}

document.addEventListener('DOMContentLoaded', initGrid);
