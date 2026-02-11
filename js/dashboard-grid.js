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

    const saved = localStorage.getItem(GRID_STORAGE_KEY);
    if (saved) {
        try {
            JSON.parse(saved).forEach(item => {
                const el = document.getElementById(item.id);
                if (el) grid.update(el, {x:item.x, y:item.y, w:item.w, h:item.h});
            });
            console.log('[Grid] ✅ Layout geladen');
        } catch(e) {
            console.warn('[Grid] Ladefehler:', e);
            applyDefaultLayout();
        }
    } else {
        applyDefaultLayout();
    }

    grid.on('dragstop resizestop', saveLayout);
    grid.on('resizestop', scaleCallsign);
    setTimeout(scaleCallsign, 500);
}

function saveLayout() {
    const items = [];
    // DOM-id statt gs-id verwenden (zuverlässiger)
    grid.getGridItems().forEach(el => {
        const n = el.gridstackNode;
        if (n && el.id) items.push({id:el.id, x:n.x, y:n.y, w:n.w, h:n.h});
    });
    localStorage.setItem(GRID_STORAGE_KEY, JSON.stringify(items));
    console.log('[Grid] 💾 Layout gespeichert', items.length, 'Widgets');
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


// === Rufzeichen dynamische Schriftgröße ===
function scaleCallsign() {
    const widget = document.getElementById('widget-header');
    const cs     = document.getElementById('header-callsign');
    if (!widget || !cs) return;
    const size = Math.max(Math.min(widget.offsetWidth * 0.04, widget.offsetHeight * 0.5, 38), 13);
    cs.style.fontSize = size + 'px';
}

document.addEventListener('DOMContentLoaded', initGrid);
