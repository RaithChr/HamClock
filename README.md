# 🎙️ HamClock – Amateur Radio Dashboard

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen.svg)]()
[![Made with](https://img.shields.io/badge/made%20with-HTML5%20%7C%20CSS3%20%7C%20JS%20%7C%20PHP-orange.svg)]()
[![Hosted](https://img.shields.io/badge/hosted-craith.cloud-green.svg)](https://craith.cloud)
[![Language](https://img.shields.io/badge/language-Deutsch%20%7C%20English-blue.svg)]()

🎙️ **Professional Amateur Radio Dashboard** with real-time Sun/Moon tracking, satellite positions, band conditions (N0NBH/HamQSL), weather, space weather, DX Cluster, live system metrics and fullscreen kiosk mode.

**✨ Live Demo:** https://craith.cloud  
**🇦🇹 QTH:** JN87ct (Vienna, Austria)  
**📡 Callsign:** OE3LCR

---

## 📸 Screenshots (Feb 10, 2026)

### Top Panel – Sun, QTH & Band Conditions
![OE3LCR Dashboard – Header](screenshot-header-2026-02-13.jpg)

### Bottom Panels – Full Dashboard
![OE3LCR Dashboard – Full View](screenshot-lower-2026-02-13.jpg)

---

## ✨ Features

### 📡 Ham Radio
| Feature | Details |
|---------|---------|
| **Band Conditions** | 13 Bands (160m–2m), N0NBH/HamQSL API, GOOD/FAIR/POOR, Day/Night |
| **Space Weather** | K-Index, Solar Flux (SFI), Sunspot Number, A-Index, Aurora, MUF |
| **DX Cluster** | Real-time spots, QRZ.com modal lookup |
| **Satellite Tracking** | ISS, NOAA-20/21, Meteor-M N2-3/4, SGP4 algorithm, TLE from CelesTrak |

### ☀️ Astronomy (via USNO API)
| Feature | Details |
|---------|---------|
| **Sunrise / Sunset** | Precise times from US Naval Observatory API |
| **Day Length** | Calculated daily for your QTH |
| **Moonrise / Moonset** | Exact times from USNO |
| **Moon Phase** | Name + illumination % (DE + EN), daily from USNO |
| **NASA SDO** | Live solar image (450px, EUV false-colour, 15 min refresh) |

### 🌤️ Weather
| Feature | Details |
|---------|---------|
| **Local Weather** | Open-Meteo API, WMO codes, temp / humidity / wind |
| **Weather Icon** | Day/Night aware (cloud/rain/snow icons) |

### 💻 System
| Feature | Details |
|---------|---------|
| **Live Metrics** | CPU, RAM, Disk (progress bars), Uptime – 10 sec refresh |
| **Daily Email** | System report at 22:00 UTC via Brevo SMTP |

### 🎨 UI / UX
| Feature | Details |
|---------|---------|
| **Bilingual** | Deutsch + English, auto-detect + manual toggle |
| **User Settings** | Callsign, Maidenhead Locator, Language (localStorage) |
| **📺 Fullscreen / Kiosk** | Auto-rotating 7 sec (Top: Sun+QTH+Bands / Bottom: Sat+DX+System+Weather) |
| **Timezone** | Derived from Maidenhead locator longitude |
| **SEO** | robots.txt, sitemap.xml, meta tags, bilingual keywords |

---

## 🗂️ File Structure

```
HamClock/
├── index.html                  # Main dashboard
├── info.html                   # Legende / Scientific documentation (DE + EN)
├── js/
│   ├── user-settings.js        # Settings manager + modals
│   ├── translations.js         # Bilingual strings
│   └── band-conditions.js      # N0NBH band conditions processor
├── data/
│   └── solar-data.json         # Cached NOAA solar data
├── fetch-solar-data.php        # NOAA SWPC K-Index / SFI fetcher
├── fetch-n0nbh.php             # N0NBH HamQSL band conditions proxy (1h cache)
├── fetch-sun-moon.php          # USNO sunrise/sunset/moonrise/moonset (1h cache)
├── fetch-tle.php               # CelesTrak TLE fetcher
├── get-system-stats.php        # Live CPU/RAM/Disk/Uptime endpoint
├── send-email.php              # Brevo SMTP module
├── send-daily-status-v5.php    # Daily email report (22:00 UTC)
├── robots.txt                  # SEO crawler rules
├── favicon.png                 # Satellite antenna favicon
├── README.md                   # This file
├── DEPLOY.md                   # Pre-deployment checklist
└── screenshots/
    └── dashboard-2026-02-08.jpg
```

**NOT included (local/private):**
- `.env` – API keys (protected by .gitignore)
- `MEMORY.md`, `memory/` – Personal workspace
- Private images

---

## 🚀 Installation

### Prerequisites
- Apache2 + PHP 7.4+ with cURL
- HTTPS (Let's Encrypt)
- Brevo account (free tier: 300 emails/day)

### Quick Setup

```bash
# 1. Clone
git clone https://github.com/RaithChr/HamClock.git
cd HamClock

# 2. Deploy
sudo cp -r index.html info.html js/ data/ *.php favicon.png robots.txt /var/www/html/
sudo chown -R www-data:www-data /var/www/html/

# 3. Create data dir
mkdir -p /var/www/html/data
sudo chown www-data:www-data /var/www/html/data

# 4. API keys
cat > /var/www/html/.env << 'EOF'
BREVO_API_KEY=xkeysib-...
BREVO_EMAIL=your@email.com
BREVO_SENDER_NAME=YourName
ELEVENLABS_API_KEY=sk_...   # optional
EOF

# 5. Cron jobs
(crontab -l 2>/dev/null; echo "0 3,15 * * * /usr/bin/php /var/www/html/fetch-solar-data.php") | crontab -
(crontab -l 2>/dev/null; echo "0 22 * * * /usr/bin/php /var/www/html/send-daily-status-v5.php") | crontab -
```

---

## 🔌 APIs & Data Sources

| Data | Source | Refresh |
|------|--------|---------|
| Band Conditions | [N0NBH / HamQSL](https://www.hamqsl.com/solarxml.php) | 3h (1h cache) |
| Sunrise/Sunset/Moon | [US Naval Observatory](https://aa.usno.navy.mil/api/) | Daily (1h cache) |
| Space Weather | [NOAA SWPC](https://services.swpc.noaa.gov/) | 2× daily |
| Sun Image | [NASA SDO](https://sdo.gsfc.nasa.gov/) | 15 min |
| Satellite TLE | [CelesTrak](https://celestrak.org/) | Manual / daily |
| Weather | [Open-Meteo](https://open-meteo.com/) | 10 min |
| Operator Lookup | [QRZ.com](https://www.qrz.com/) | On demand |

---

## 📺 Fullscreen / Kiosk Mode

Perfect for dedicated ham radio monitors:

```
Click 📺 Fullscreen → Auto-rotates every 7 seconds

View 1 (7s):  ☀️ NASA SDO Sun  |  📍 QTH + Moon/Sun times  |  📡 Band Conditions
View 2 (7s):  🛰️ Satellites     |  🌍 DX Cluster             |  💻 System  |  🌤️ Weather

Exit: ESC key or click Fullscreen again
```

---

## 🛡️ Security

- ✅ Zero credentials in repository
- ✅ `.gitignore` protects `.env`, `avatars/`, `memory/`, `MEMORY.md`
- ✅ Pre-deployment checklist: `DEPLOY.md`

---

## 📧 Support

☕ **[Buy Me A Coffee](https://www.buymeacoffee.com/christianraith)**  
💳 **[PayPal](https://paypal.me/christianraith151)**

---

## 🙋 Author

**Christian Raith (OE3LCR)**  
📡 JN87ct · Vienna, Austria · https://craith.cloud

---

## 🔗 Resources

- [CelesTrak](https://celestrak.org/) – Satellite TLE Data
- [NOAA SWPC](https://www.swpc.noaa.gov/) – Space Weather
- [NASA SDO](https://sdo.gsfc.nasa.gov/) – Solar Imagery
- [HamQSL / N0NBH](https://www.hamqsl.com/) – Band Conditions
- [USNO API](https://aa.usno.navy.mil/api/) – Astronomical Data
- [Open-Meteo](https://open-meteo.com/) – Weather

---

**Version:** 1.4.0 · **Updated:** Feb 10, 2026 · **Status:** ✅ Production Ready
