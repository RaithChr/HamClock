# 🎙️ HamClock - Amateur Radio Dashboard

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen.svg)]()
[![Stack](https://img.shields.io/badge/stack-PHP%20%7C%20JS%20%7C%20Gridstack.js-orange.svg)]()
[![Hosted](https://img.shields.io/badge/hosted-craith.cloud-green.svg)](https://craith.cloud)
[![Language](https://img.shields.io/badge/language-Deutsch%20%7C%20English-blue.svg)]()

🎙️ **Professional Amateur Radio Dashboard** — Real-time Sun/Moon tracking, satellite positions, band conditions, local & space weather, DX Cluster integration — with a fully draggable, resizable, kiosk-capable layout.

**✨ Live Demo:** https://craith.cloud  
**🇦🇹 QTH:** JN87ct (Vienna, Austria)  
**📡 Callsign:** OE3LCR

---

## ✨ Features Overview

### 📐 Drag & Drop Layout (Gridstack.js v12.4.2)
- **10 independent widgets** — drag by header, resize from corner
- **Layout persistence** via localStorage (`gwen_grid_layout`)
- **🔄 Reset button** in header — restores default layout instantly
- Per-widget **P1 / P2 / 1+2 badge** to assign pages for Kiosk Mode

### 📺 Kiosk Mode (2-Page Auto-Rotate)
- **Full-screen** via `📺 Vollbild` button or `▶ S1/S2` manual toggle
- **Page 1** — Ham radio essentials (Sun, QTH, Bands, Clock, System)
- **Page 2** — Weather + satellite tracking (Local Weather, Space Weather, DX, Satellites)
- **Auto-rotate** every 30 seconds
- **Per-page layout saving** — positions saved separately (`gwen_kiosk_layout_p1` / `_p2`)
- **Horizontal gap correction** — P2 widgets auto-reposition to fill left-to-right without gaps
- **Widget visibility control** — each widget can be individually hidden via the **OFF** badge
- Reset clears normal layout, kiosk layouts and all badge states

### 📊 Real-Time Widgets

| Widget | Data Source | Update |
|--------|-------------|--------|
| ☀️ Solar Image | NASA/SOHO EIT 304 (PHP proxy) | 5 min |
| 📍 QTH / Sun-Moon | USNO Navy API (PHP proxy) | 30 min |
| 📡 Band Conditions | N0NBH HamQSL XML (PHP proxy) | 1 h |
| 🕐 Clock | Local JS | 1 sec |
| 🌤️ Local Weather | OpenWeatherMap One Call 3.0 | 10 min |
| ⚡ Space Weather | NOAA SWPC (cron cached) | 5 h |
| 🛰️ Satellites | CelesTrak TLE + satellite.js | 7 sec |
| 🌍 DX Cluster | HamQTH.com CSV | 60 sec |
| 💻 System Stats | PHP backend (CPU/RAM/Disk) | 10 sec |
| 🎙️ Header | Static + buttons | — |

### 📡 Ham Radio Intelligence
- **Band Conditions** — 13 bands (160m → 2m), real NOAA K-Index, GOOD/FAIR/POOR neon indicators
- **Space Weather** — K-Index, Solar Flux (SFI), Sunspot Number, A-Index, Aurora status, MUF estimate
- **Satellite Tracking** — ISS, NOAA-20/21, Meteor-M N2-3/4, Hubble — real-time Az/El/Distance/Visibility
- **DX Cluster** — 20 spots, band color-coded, QRZ.com popup, auto-scroll

### 🌤️ Weather
- **Local:** Temperature, Humidity, Wind speed + compass direction (e.g. `12 km/h NW`)
- **Source:** OpenWeatherMap One Call API 3.0 via PHP server-side proxy (API key never exposed)
- **10-minute server cache**

### 👤 User Personalization
- **⚙️ Settings Modal** — Callsign, Maidenhead Locator, Language
- **Auto-detect language** from browser locale (DE/EN)
- **All settings in localStorage** — persist across sessions

---

## 🏗️ Architecture

### Stack
- **Server:** Apache2 + PHP 7.4+ (Ubuntu)
- **Frontend:** Vanilla JS + CSS3
- **Layout:** [Gridstack.js v12.4.2](https://gridstackjs.com/)
- **Satellite Math:** [satellite.js](https://github.com/shashwatak/satellite-js)
- **HTTPS:** Let's Encrypt
- **Email:** Brevo SMTP (300/day free tier)

### Modular PHP Structure
```
index.php               ← Entry point, loads includes/ + widgets/
includes/
├── head.php            ← CSS, meta, external scripts
├── modals.php          ← QRZ lookup, Settings, Support modals
└── footer.php          ← Main application JS (init, weather, DX, satellites…)
widgets/
├── header.php          ← 🎙️ Callsign + control buttons (Vollbild, Einstellungen, Reset)
├── sun.php             ← ☀️ NASA/SOHO solar image
├── qth.php             ← 📍 QTH info, Sunrise/Sunset, Moon phase
├── bands.php           ← 📡 Band conditions (13 bands)
├── clock.php           ← 🕐 LOC + UTC live clock
├── weather-local.php   ← 🌤️ Local weather display
├── weather-space.php   ← ⚡ Space weather display
├── satellites.php      ← 🛰️ Satellite tracking list
├── dx.php              ← 🌍 DX Cluster spots
└── system.php          ← 💻 CPU / RAM / Disk / Uptime
```

### JavaScript Modules
```
js/
├── gridstack.min.js    ← Gridstack v12.4.2 (layout engine)
├── dashboard-grid.js   ← Grid init, drag/resize save, reset
├── kiosk.js            ← Kiosk mode, page system, per-page layout save
├── band-conditions.js  ← Band condition calculation logic
└── user-settings.js    ← Settings manager + i18n translations
```

### PHP Backend Proxies
```
fetch-weather.php       ← OpenWeatherMap One Call 3.0 (10-min cache)
fetch-n0nbh.php         ← HamQSL band data (1h cache)
fetch-sun-moon.php      ← USNO Navy sun/moon times (1h cache)
get-sdo-image.php       ← SOHO EIT 304 solar image (5-min cache)
get-system-stats.php    ← Live CPU / RAM / Disk / Uptime
fetch-solar-data.php    ← NOAA SWPC K-Index / SFI (cron triggered)
fetch-tle.php           ← CelesTrak TLE satellite data
send-daily-status-v5.php ← Daily HTML email report (22:00 UTC)
send-email.php          ← Brevo SMTP module
```

---

## 🚀 Installation

### Prerequisites
- Ubuntu / Debian server
- Apache2 with `mod_rewrite` + HTTPS (Let's Encrypt)
- PHP 7.4+ with `curl`, `json` extensions
- OpenWeatherMap API key ([One Call by Call plan](https://openweathermap.org/price) — 1000 calls/day free)
- Brevo account for daily email reports (optional)

### Step 1 — Clone
```bash
git clone https://github.com/RaithChr/HamClock.git
cd HamClock
```

### Step 2 — Deploy to Web Root
```bash
sudo rsync -av --exclude='.git' --exclude='.env' . /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
sudo chmod 755 /var/www/html
```

### Step 3 — Configure API Keys
Create `/var/www/html/.env`:
```bash
# OpenWeatherMap (One Call API 3.0)
OWM_API_KEY=your_key_here

# Brevo SMTP (daily email reports)
BREVO_API_KEY=xkeysib-...
BREVO_EMAIL=your@email.com
BREVO_SENDER_NAME=YourName
```
> ⚠️ `.env` is git-ignored — never commit API keys!

Also set your OWM key in `fetch-weather.php`:
```php
$apiKey = 'your_owm_key_here';
```

### Step 4 — Set Up Cron Jobs
```bash
# Solar data (2x daily)
(crontab -l 2>/dev/null; echo "0 3,15 * * * /usr/bin/php /var/www/html/fetch-solar-data.php") | crontab -

# Daily email report (22:00 UTC)
(crontab -l 2>/dev/null; echo "0 22 * * * /usr/bin/php /var/www/html/send-daily-status-v5.php") | crontab -
```

### Step 5 — Verify
```bash
curl -I https://your-domain.com
```

---

## ⚙️ Configuration

### User Settings (in-browser)
Click **⚙️ Einstellungen** in the header:
- **Callsign** — e.g. `OE3LCR`
- **Maidenhead Locator** — e.g. `JN87ct` (coordinates auto-calculated)
- **Language** — Deutsch / English

Settings are stored in `localStorage` key `gwen_hp_settings`.

### Kiosk Mode — Page Assignment
Each widget has a **P1 / P2 / 1+2** badge in its header (click to cycle):

| Badge | Meaning |
|-------|---------|
| **P1** (green) | Visible on Kiosk Page 1 only |
| **P2** (blue) | Visible on Kiosk Page 2 only |
| **1+2** (orange) | Visible on both pages |

Default assignment:
- **P1:** Sun, QTH, Bands, System
- **P2:** Local Weather, Space Weather, DX Cluster, Satellites
- **1+2:** Header, Clock

Click cycle per widget: **P1 → P2 → 1+2 → OFF → P1**

| Badge | Colour | Behaviour |
|-------|--------|-----------|
| **P1** | 🟢 Green | Visible on Kiosk Page 1 only |
| **P2** | 🔵 Blue | Visible on Kiosk Page 2 only |
| **1+2** | 🟠 Orange | Visible on both kiosk pages |
| **OFF** | 🔴 Red | Hidden in kiosk mode (all pages) |

> **Note:** OFF-widgets remain visible in normal (non-kiosk) mode.

### localStorage Keys
| Key | Contents |
|-----|----------|
| `gwen_grid_layout` | Normal mode widget positions |
| `gwen_kiosk_layout_p1` | Kiosk Page 1 positions |
| `gwen_kiosk_layout_p2` | Kiosk Page 2 positions |
| `gwen_widget_pages` | Per-widget P1/P2/1+2/OFF assignments |
| `gwen_hp_settings` | Callsign, Locator, Language |

All keys are cleared by the **🔄 Reset** button.

---

## 🛡️ Security

- ✅ API keys in `.env` — git-ignored, never in repo
- ✅ All external APIs proxied server-side (keys never exposed to browser)
- ✅ Input validation in PHP proxies (coordinate bounds, etc.)
- ✅ HTTPS enforced

**See SECURITY_NOTE.md for full guidelines.**

---

## 🌐 Browser Support

| Browser | Support |
|---------|---------|
| Chrome / Edge 90+ | ✅ Full |
| Firefox 88+ | ✅ Full |
| Safari 14+ | ✅ Full |
| Mobile (iOS/Android) | ✅ Responsive |

---

## 📧 Daily Email Report

Sent every day at **22:00 UTC** via Brevo SMTP:
- Current solar data (K-Index, SFI, Aurora)
- System metrics (CPU, RAM, Disk, Uptime)
- Satellite TLE update status
- Server health summary

---

## 🔗 Data Sources & Credits

| Source | Used For |
|--------|----------|
| [NASA/SOHO](https://soho.nascom.nasa.gov/) | Solar EIT 304 image |
| [NOAA SWPC](https://www.swpc.noaa.gov/) | K-Index, SFI, A-Index |
| [N0NBH HamQSL](https://www.hamqsl.com/) | Band condition XML |
| [USNO Navy](https://aa.usno.navy.mil/api/) | Sun/Moon rise/set times |
| [CelesTrak](https://celestrak.org/) | Satellite TLE data |
| [HamQTH.com](https://www.hamqth.com/) | DX Cluster spots |
| [OpenWeatherMap](https://openweathermap.org/) | Local weather + wind |
| [QRZ.com](https://www.qrz.com/) | Callsign lookup (modal) |
| [Gridstack.js](https://gridstackjs.com/) | Drag & drop grid layout |
| [satellite.js](https://github.com/shashwatak/satellite-js) | SGP4 satellite propagation |

---

## 📧 Support

☕ **[Buy Me A Coffee](https://www.buymeacoffee.com/christianraith)**  
💳 **[PayPal](https://paypal.me/christianraith151)**

---

## 📄 License

MIT License — see [LICENSE](LICENSE) for details.

---

## 🙋 Author

**Christian Raith (OE3LCR)**  
📡 JN87ct — Vienna, Austria  
🌐 https://craith.cloud

---

**Version:** 2.1.0  
**Last Updated:** Feb 15, 2026 (v2.1.0)  
**Status:** ✅ Production Ready
