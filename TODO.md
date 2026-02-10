# TODO - Offene Punkte

## 🔴 PRIORITÄT: Kiosk Mode - Panel-Breiten

**Problem:**
Im Kiosk-Modus (Bottom View) sind die drei Panels (Satelliten, DX, System) NICHT gleichmäßig auf die Breite verteilt.

**Aktuelle Situation:**
- DX Cluster ist breiter als die anderen beiden
- Satelliten und System sind schmaler
- Pixel-basierte Erzwingung funktioniert nicht wie erwartet

**Was bereits versucht wurde:**
1. ❌ Flexbox mit calc(33.333% - 15px)
2. ❌ Grid mit repeat(3, 1fr)
3. ❌ CSS !important mit width: 33.333%
4. ❌ JavaScript inline styles (cssText)
5. ❌ setInterval mit kontinuierlicher Erzwingung
6. ❌ Pixel-basiert mit offsetWidth/3

**Vermutung:**
- Möglicherweise hat `.card` inherente Eigenschaften (padding, border, box-sizing)
- Oder es gibt verstecktes CSS das überschreibt
- Container könnte nicht die richtige Breite haben

**Nächste Schritte (für später):**
1. Browser DevTools Inspection auf die Panels
2. Computed Styles checken (was wird tatsächlich angewendet?)
3. Container-Width messen (ist grid-3 wirklich 100%?)
4. Eventuell: Flex-basis statt width verwenden
5. Eventuell: Komplett neue HTML-Struktur nur für Kiosk-Mode

**Workaround aktuell:**
- Funktioniert aktuell nicht perfekt, aber verwendbar
- Kiosk-Mode ist ansonsten voll funktional

---

**Datum:** 10. Februar 2026, 21:24 UTC  
**Status:** ⏸️ PAUSIERT (später beheben)
