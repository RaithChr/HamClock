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

---

## 🌅 PRIORITÄT: Sonnen/Mond Auf/Untergang korrigieren

**Problem:**
Die aktuellen Berechnungen für Sonnen- und Mondauf/-untergang sind UNGENAU!

**Was falsch ist:**
1. ❌ Sonnenauf/-untergang nicht korrekt für Wien
2. ❌ Mondauf/-untergang basiert auf Fake-Formel (Mondphase * 3)
3. ❌ Tageslänge fehlt komplett

**Referenz:**
https://at.wetter.com/astro/mond/oesterreich/wien/ATAT10678/

**Was implementiert werden muss:**
1. ✅ Präzise Sonnenberechnung (Jean Meeus Algorithmen)
   - Datei vorbereitet: /tmp/astro-calc.js
   - Berücksichtigt: Breiten/Längengrad Wien (48.2082°N, 16.3738°E)
   - Output: Sunrise, Sunset, Day Length

2. ⏳ Präzise Mondberechnung
   - Sehr komplex (Meeus Kapitel 47)
   - Alternative: API verwenden (z.B. ipgeolocation.io astronomy API)
   - Oder: SunCalc.js Bibliothek integrieren

3. ⏳ UI Update
   - Tageslänge im QTH-Fenster anzeigen
   - Format: "9h 28m" oder "09:28"
   - Real-time Updates (nicht statisch!)

**Koordinaten (aus Locator JN87ct):**
- Lat: 47.8125°N (aus Locator)
- Lon: 16.2083°E (aus Locator)
- Aber wetter.com nutzt Wien-Center: 48.2082°N, 16.3738°E

**Nächste Schritte:**
1. astro-calc.js in index.html integrieren
2. updateTime() erweitern mit echten Berechnungen
3. Tageslänge UI-Element hinzufügen
4. Mondberechnung implementieren (API oder Bibliothek)
5. Testen gegen wetter.com Referenz

---

**Datum:** 10. Februar 2026, 21:26 UTC  
**Status:** ⏳ TODO (nächste Session)  
**Priorität:** 🔴 HOCH (falsche Daten werden angezeigt!)
