# 🎙️ HamClock - Amateur Radio Dashboard

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen.svg)]()
[![Stack](https://img.shields.io/badge/stack-PHP%20%7C%20JS%20%7C%20Gridstack.js-orange.svg)]()
[![Hosted](https://img.shields.io/badge/hosted-craith.cloud-green.svg)](https://craith.cloud)
[![Language](https://img.shields.io/badge/language-Deutsch%20%7C%20English-blue.svg)]()

🎙️ **Professional Amateur Radio Dashboard** — Real-time Sun/Moon tracking, NASA solar imaging with multi-wavelength modes, band conditions for 13 HF/VHF bands, satellite positions, live FT8 activity & contest calendar, local & space weather — with a fully draggable, resizable, kiosk-capable layout.

**✨ Live Demo:** https://craith.cloud  
**🇦🇹 QTH:** JN87ct (Vienna, Austria)  
**📡 Callsign:** OE3LCR

---

## ✨ Features Overview

### 📐 Drag & Drop Layout (Gridstack.js v12.4.2)
- **11 independent widgets** — drag by header, resize from corner
- **Layout persistence** via localStorage (`gwen_grid_layout`)
- **🔄 Reset button** in header — restores default layout instantly
- Per-widget **P1 / P2 / 1+2 badge** to assign pages for Kiosk Mode

### 📊 Real-Time Widgets (11 Total)

| Widget | Data Source | Features |
|--------|-------------|----------|
| ☀️ Solar | NASA/SDO EIT 304 + AIA | **NEW:** 5 wavelength modes (Visible/Corona/Chromosphere/Quiet/Flairing), 15s auto-cycling |
| 📍 QTH | USNO Navy API | **NEW:** Moon phase + direction (Zunehmend/Abnehmend), next Full/New Moon dates |
| 📡 Band Conditions | N0NBH HamQSL XML | 13 bands (160m → 2m), GOOD/FAIR/POOR |
| 🕐 Clock | Local JS | LOC + UTC live |
| 🌤️ Local Weather | OpenWeatherMap | Temp, Humidity, Wind |
| ⚡ Space Weather | NOAA SWPC | K-Index, SFI, Aurora |
| 🛰️ Satellites | CelesTrak TLE | ISS, NOAA, Meteor, Hubble |
| 🌍 DX Cluster | HamQTH.com | 20 spots, color-coded |
| 💻 System Stats | PHP backend | CPU/RAM/Disk/Uptime |
| 🎙️ Header | Static | Control buttons |
| **🎙️📡 Contests & FT8** | **OEVSV/ARRL + PSK Reporter** | **NEW:** 3 modes (Contests/FT8 All/FT8 Bands), 13-band filter, live activity counts |

---

## ☀️ Solar Box — 5 Wavelength Modes

| Mode | Wavelength | Use Case |
|------|-----------|----------|
| 📸 **Visible** | 1024 × 1024 px | Full solar disk, sunspots |
| 🔴 **Corona** | 94 Å | Extreme UV, CMEs |
| 🟠 **Chromosphere** | 304 Å | Hydrogen-alpha, flares |
| 🌫️ **Quiet Corona** | 193 Å | Cooler corona |
| ⚡ **Flairing** | AIA composite | Solar flares |

**Auto-cycling:** 15 seconds; click image to select mode manually.

---

## 🌙 Moon Phase Display

- **Current Phase** — Name + % (e.g., "Vollmond • 97%")
- **Direction** — Zunehmend (waxing) / Abnehmend (waning)
- **Next Phases** — Full Moon & New Moon dates

---

## 🎙️📡 Contests & FT8 Monitor

**3 Views:**
1. **📅 Contests** — OEVSV (🟢 green) + ARRL (🟡 yellow)
2. **📡 FT8 All** — Top 8 spots from PSK Reporter
3. **🎯 FT8 Bands** — Filter by 13 bands

**Band Activity:**
- 🟢 Green (●) — 3+ spots
- 🟡 Yellow (◐) — 1-2 spots
- ⚫ Gray (○) — 0 spots

---

## 🎯 Legend / Legende

### English — Widget Features & Indicators

#### Solar Box (☀️)
- **Visible** — Full solar disk in natural color
- **Corona** — Extreme ultraviolet (94 Å), shows solar corona & CMEs
- **Chromosphere** — Hydrogen-alpha region (304 Å), highlights flares
- **Quiet Corona** — Cooler corona (193 Å), structural details
- **Flairing** — AIA composite, transient events
- ⏱️ Auto-cycle every 15 seconds; click to select mode manually

#### Moon Phase (🌙)
- **Zunehmend** — Waxing phase (new → full)
- **Abnehmend** — Waning phase (full → new)
- **% Illumination** — 0–100% of moon visible
- **Vollmond** — Full Moon
- **Neumond** — New Moon

#### Band Conditions (📡)
- 🟢 **GOOD** — Excellent propagation
- 🟡 **FAIR** — Moderate conditions
- 🔴 **POOR** — Difficult propagation
- **13 bands:** 160m, 80m, 60m, 40m, 30m, 20m, 17m, 15m, 12m, 11m, 10m, 6m, 2m

#### Space Weather (⚡)
- **K-Index** — Geomagnetic activity (0–9); higher = more aurora
- **SFI** — Solar Flux Index; higher = better HF propagation
- **Sunspot #** — Active sunspot count
- **A-Index** — Cumulative geomagnetic index (daily)
- **Aurora** — Visible at poles; affects propagation

#### Satellite Tracking (🛰️)
- **Az** — Azimuth (0°–360°)
- **El** — Elevation (0°–90°)
- **Distance** — Range in km
- **Visibility** — 🟢 Visible / 🌙 Not visible

#### DX Cluster (🌍)
- **Call** — Spotted callsign
- **Band** — Frequency band (e.g., 20m)
- **Freq** — Exact frequency (kHz)
- **Time** — Report timestamp

#### Contests & FT8 (🎙️📡)
- **OEVSV** — Austrian contests (green)
- **ARRL** — International contests (yellow)
- **FT8 Spots** — Real-time reports from PSK Reporter
- **Band Activity:** 🟢 3+, 🟡 1–2, ⚫ 0 spots active

#### System Stats (💻)
- **CPU** — Processor usage %
- **RAM** — Memory usage %
- **Disk** — Storage usage %
- **Uptime** — Server online duration

---

### Deutsch — Widget Merkmale & Indikatoren

#### Sonnenbox (☀️)
- **Visible** — Sonnenoberfläche in Farbe
- **Corona** — Extremes Ultraviolett (94 Å), zeigt Sonnenkrone & CMEs
- **Chromosphäre** — Wasserstoff-Alpha (304 Å), hebt Flares hervor
- **Ruhige Corona** — Kühlere Krone (193 Å), Strukturdetails
- **Flare-aktiv** — AIA Composite, transiente Ereignisse
- ⏱️ Auto-Wechsel alle 15 Sekunden; Klick für manuellen Modus

#### Mondphase (🌙)
- **Zunehmend** — Wachsende Phase (Neumond → Vollmond)
- **Abnehmend** — Abnehmende Phase (Vollmond → Neumond)
- **% Beleuchtung** — 0–100% sichtbar
- **Vollmond** — Voller Mond
- **Neumond** — Neuer Mond

#### Bandvorhersage (📡)
- 🟢 **GOOD** — Ausgezeichnete Ausbreitung
- 🟡 **FAIR** — Moderate Bedingungen
- 🔴 **POOR** — Schwierige Ausbreitung
- **13 Bänder:** 160m, 80m, 60m, 40m, 30m, 20m, 17m, 15m, 12m, 11m, 10m, 6m, 2m

#### Weltraumwetter (⚡)
- **K-Index** — Geomagnetische Aktivität (0–9); höher = mehr Aurora
- **SFI** — Solar Flux Index; höher = bessere KW-Ausbreitung
- **Fleckenzahl** — Aktive Sonnenflecken
- **A-Index** — Kumulativer geomagnetischer Index (täglich)
- **Aurora** — An Polen sichtbar; beeinflusst Ausbreitung

#### Satellitenverfolg (🛰️)
- **Az** — Azimut (0°–360°)
- **El** — Elevation (0°–90°)
- **Distanz** — Entfernung in km
- **Sichtbarkeit** — 🟢 Sichtbar / 🌙 Nicht sichtbar

#### DX-Cluster (🌍)
- **Ruf** — Gemeldetes Rufzeichen
- **Band** — Frequenzbereich (z.B. 20m)
- **Freq** — Exakte Frequenz (kHz)
- **Zeit** — Zeitstempel

#### Contests & FT8 (🎙️📡)
- **OEVSV** — Österreichische Contests (grün)
- **ARRL** — Internationale Contests (gelb)
- **FT8 Spots** — Echtzeit-Meldungen von PSK Reporter
- **Bandaktivität:** 🟢 3+, 🟡 1–2, ⚫ 0 Meldungen

#### Systemstatus (💻)
- **CPU** — Prozessor-Auslastung %
- **RAM** — Speicher-Auslastung %
- **Disk** — Speicher-Auslastung %
- **Uptime** — Server-Online-Dauer

---

**Version:** 2.2.0 | **Last Updated:** Feb 27, 2026 | **Status:** ✅ Production Ready
