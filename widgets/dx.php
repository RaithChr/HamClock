<div class="card" style="display:flex; flex-direction:column; height:100%;">
    <div class="card-header">
        <span class="icon">🌍</span>
        <span class="card-title" data-i18n="card_dx">DX Cluster Spots</span>
    </div>
    <div id="dx-container" style="font-size:0.85em; line-height:1.7; flex:1; min-height:0; overflow-y:hidden; padding-right:2px;">
                    <div class="dx-spot" id="dx-row-1"></div>
                    <div class="dx-spot" id="dx-row-2"></div>
                    <div class="dx-spot" id="dx-row-3"></div>
                    <div class="dx-spot" id="dx-row-4"></div>
                    <div class="dx-spot" id="dx-row-5"></div>
                    <div class="dx-spot" id="dx-row-6"></div>
                    <div class="dx-spot" id="dx-row-7"></div>
                    <div class="dx-spot" id="dx-row-8"></div>
                    <div class="dx-spot" id="dx-row-9"></div>
                    <div class="dx-spot" id="dx-row-10"></div>
                    <div class="dx-spot" id="dx-row-11"></div>
                    <div class="dx-spot" id="dx-row-12"></div>
                    <div class="dx-spot" id="dx-row-13"></div>
                    <div class="dx-spot" id="dx-row-14"></div>
                    <div class="dx-spot" id="dx-row-15"></div>
                    <div class="dx-spot" id="dx-row-16"></div>
                    <div class="dx-spot" id="dx-row-17"></div>
                    <div class="dx-spot" id="dx-row-18"></div>
                    <div class="dx-spot" id="dx-row-19"></div>
                    <div class="dx-spot" id="dx-row-20"></div>
    </div>
</div>

<style>
.dx-spot {
    padding:6px 8px;
    border-bottom:1px solid rgba(255,255,255,0.07);
    cursor:pointer;
    display:none;
}
.dx-spot:hover { background:rgba(255,165,0,0.08); }
.dx-spot strong { color:#ffa502; }
.dx-spot .dx-time { color:#888; font-size:0.82em; }
</style>

<script>
// DX Scrollbar Toggle
function checkDXScroll() {
    const el = document.getElementById('dx-container');
    if (!el) return;
    el.style.overflowY = el.scrollHeight > el.clientHeight ? 'auto' : 'hidden';
}
if (window.ResizeObserver) {
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('dx-container');
        if (el) new ResizeObserver(checkDXScroll).observe(el);
    });
}
document.addEventListener('DOMContentLoaded', () => setTimeout(checkDXScroll, 1500));
</script>
