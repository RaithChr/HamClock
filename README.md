# 🎙️ OE3LCR Ham Radio Dashboard

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Status](https://img.shields.io/badge/status-Active-brightgreen.svg)]()
[![Version](https://img.shields.io/badge/version-1.0-blue.svg)]()
[![Made with](https://img.shields.io/badge/made%20with-HTML5%20%7C%20CSS3%20%7C%20JS-orange.svg)]()

Ein vollständiges Amateur Radio Dashboard mit **Echtzeit-Sonne/Mond-Tracking**, **Satellit-Positionen**, **Band Conditions** und **DX Cluster Spots**.

---

## ✨ Features

- **☀️ NASA SDO Live-Bild** - Echtzeit Solar Dynamics Observatory
- **🌙 Mondphase & Auf-/Untergang** - Präzise astronomische Berechnungen
- **🛰️ Active Satellites** - ISS, NOAA, Hubble mit Az/El/Distance (10s Updates)
- **📊 Band Conditions** - Echtzeit Propagation Forecast (80m bis 6m)
- **☀️ Solar Activity** - SFI, K-Index, Space Weather Status
- **🌍 DX Cluster** - Live Spots von der ganzen Welt
- **📡 Propagation Indices** - SSN, R-Index, Aurora Activity
- **📍 QTH Information** - Maidenhead Locator, Koordinaten
- **ℹ️ Legenden & Erklärungen** - Vollständige Dokumentation aller Parameter

---

## 📍 Standort (QTH)

| Parameter | Wert |
|-----------|------|
| **Maidenhead** | JN87ct |
| **Koordinaten** | 47.8125°N 16.2083°E |
| **Callsign** | OE3LCR |
| **Land** | Österreich 🇦🇹 |

---

## 🖼️ Screenshots

![OE3LCR Dashboard](https://craith.cloud/dashboard-screenshot.jpg)

*Live Dashboard mit NASA SDO Sonne, Band Conditions, Active Satellites und DX Cluster*

---

## 🛠️ Tech Stack

| Komponente | Details |
|-----------|---------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **APIs** | NASA SDO, Satellite.js (TLE) |
| **Hosting** | Apache2 on Linux VPS |
| **Deployment** | craith.cloud |

---

## 📁 Dateien

| Datei | Beschreibung |
|-------|-------------|
| `index.html` | Main Dashboard (Echtzeit) |
| `info.html` | Legenden & Erklärungen |
| `README.md` | Diese Datei |
| `LICENSE` | MIT License |
| `.gitignore` | Security Config |

---

## 🚀 Schnellstart

### 1. Repository klonen
```bash
git clone git@github.com:RaithChr/craith.cloud.git
cd craith.cloud
```

### 2. Lokal öffnen
```bash
# Einfach im Browser öffnen
open index.html

# Oder auf einem Webserver
python3 -m http.server 8000
# Dann: http://localhost:8000
```

### 3. Auf VPS deployen
```bash
cp index.html info.html /var/www/html/
# Done! Verfügbar unter craith.cloud
```

---

## 🔒 Sicherheit

- ✅ **Keine API Keys** im Code
- ✅ **Keine Credentials** hardcodiert
- ✅ **Client-Side Berechnung** (keine Server-Last)
- ✅ **Mobile-responsive** Design

---

## 📊 Dashboard Übersicht

### Hauptbereich
- **NASA SDO Sonne** (Live 400x400px) mit Sonnenflecken & Koronale Aktivität
- **QTH Daten** - Maidenhead, Koordinaten, Sunrise/Sunset, Moonrise/Set
- **System Status** - Server, Call, Online Status

### Mittlerer Bereich
- **Solar Activity** - SFI, Sunspots, K-Index, Space Weather
- **Propagation** - SSN, R-Index, Aurora, MUF
- **Mondphase** - Phase, Emoji, Beleuchtung

### Rechter Bereich
- **Band Conditions** - 8 Bänder (80m bis 6m) mit Farben:
  - 🟢 **GREEN** = GOOD
  - 🟠 **ORANGE** = FAIR
  - 🔴 **RED** = POOR
- **Active Satellites** - ISS, NOAA-18/19, Hubble
  - Azimuth, Elevation, Distance
  - Visible/Below Status
- **DX Cluster** - 8 aktuelle Spots weltweit

---

## 📖 Verwendung

### Info-Seite
Klick auf **"ℹ️ Legenden"** um vollständige Erklärungen zu sehen:
- Band Conditions Bedeutung
- Solar Activity Indices
- Satellit-Parameter
- Propagation Informationen
- QTH Konzepte

### Echtzeit-Updates
- ⏰ **Uhrzeit** - Jede Sekunde
- 🌙 **Mondphase** - Kontinuierlich berechnet
- 🛰️ **Satelliten** - Alle 10 Sekunden
- ☀️ **Solar-Daten** - Alle 5 Minuten
- 🌍 **DX Cluster** - Alle 60 Sekunden
- 📡 **NASA Sonne** - Alle 5 Minuten

---

## 💡 Tipps für Funkamateure

- Nutze **Band Conditions** zur Planung von Kontakten
- Beobachte **K-Index** für Aurora Activity (VHF/UHF)
- Nutze **Moonrise/Set** Zeiten für EME (Moonbounce)
- Verfolge **ISS Passes** für SSB/FM Relais-Kontakte
- Nutze **DX Cluster** um seltene DX-Stationen zu finden

---

## 📜 Lizenz

**MIT License** - Frei verwendbar, modifizierbar und verteilbar.

```
Copyright (c) 2026 OE3LCR (Christian Raith)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files...
```

Siehe [LICENSE](LICENSE) für vollständigen Text.

---

## 🤝 Support & Kontakt

| Kategorie | Info |
|-----------|------|
| **Issues & Bugs** | [GitHub Issues](https://github.com/RaithChr/craith.cloud/issues) |
| **Telegram** | @DerDracheChrisu |
| **Email** | craith@craith.cloud |
| **QTH** | JN87ct, Österreich 🇦🇹 |

---

## 💰 Unterstütze das Projekt

Wenn dir das Dashboard gefällt und du es unterstützen möchtest:

### 🌟 Kostenlos
- **Star** auf GitHub (⭐ oben rechts)
- **Fork** das Projekt
- **Share** mit anderen Funkamateuren
- **Issues & PRs** sind willkommen!

### 💳 Unterstütze dieses Projekt
Die Arbeit an diesem Dashboard kostet Zeit & Energie. Falls du es magst und helfen möchtest:

- ☕ **[Buy Me A Coffee](https://www.buymeacoffee.com/christianraith)** - Schnelle kleine Unterstützung
- 🅿️ **[PayPal](https://paypal.me/christianraith151)** - Spende in jeder Höhe

Jede Unterstützung wird geschätzt! 🙏

---

## 🙏 Danksagungen

- **NASA** - Solar Dynamics Observatory (SDO) Live Images
- **TLE Updates** - Space-Track.org
- **Satellite.js** - JavaScript Orbital Mechanics Library
- **Font** - Inter & Orbitron from Google Fonts

---

## 🔗 Links

| Link | Beschreibung |
|------|------------|
| [craith.cloud](https://craith.cloud) | Live Dashboard |
| [info.html](https://craith.cloud/info.html) | Legenden & Erklärungen |
| [QRZ.com OE3LCR](https://www.qrz.com/db/OE3LCR) | Amateur Radio Directory |

---

## 📝 Changelog

### v1.0 - 2026-02-05
- ✅ Initial Release
- ✅ Live Sun/Moon Tracking
- ✅ Satellite Positions (ISS, NOAA, Hubble)
- ✅ Band Conditions
- ✅ Solar Activity Monitoring
- ✅ DX Cluster Integration
- ✅ Info Page & Legends

---

**Made with ❤️ & 🍯 (myhoney) by OE3LCR**

*Amateur Radio Dashboard • Real-time Propagation • DX Information*

---

*Last Updated: 2026-02-05*
