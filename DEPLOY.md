# 🚀 DEPLOY CHECKLIST - HamClock Repository

**Before every push/update to GitHub, verify this checklist!**

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### 🔐 Security & Privacy

- [ ] **API Keys Protection**
  - [ ] No Brevo API Keys in public files
  - [ ] No GitHub Tokens exposed
  - [ ] No credentials in code
  - [ ] All tokens stored in `.env` only
  - `grep -r "sk_\|ghp_\|api_key\|token" /var/www/html/*.js /var/www/html/*.html`

- [ ] **Private Folders NOT on GitHub**
  - [ ] ❌ `/avatars/` - Private images (NOT tracked)
  - [ ] ❌ `/myhoney/` - Gallery directory (NOT tracked)
  - [ ] ❌ `/memory/` - Personal logs (NOT tracked)
  - [ ] ❌ `.env` - API Keys (NOT tracked)
  - [ ] ❌ `GWEN_*.md` - Performance metrics (NOT tracked)
  - `git ls-files | grep -E "avatars|myhoney|memory|GWEN_"`

- [ ] **.gitignore Verification**
  - [ ] Contains: `avatars/`
  - [ ] Contains: `/myhoney/`
  - [ ] Contains: `memory/`
  - [ ] Contains: `.env`
  - [ ] Contains: `GWEN_*.md`
  - [ ] Permissions: 600 chmod for `.env`

- [ ] **No Password Leaks**
  - [ ] No sudo passwords exposed
  - [ ] No email passwords in files
  - [ ] No SSH keys in public repo
  - `grep -r "144email1&email2\|password\|passwd" /home/chris-admin/.openclaw/workspace/`

---

### 📝 Documentation

- [ ] **README.md Complete**
  - [ ] Has production screenshot included
  - [ ] Has Support/Donate buttons (Coffee + PayPal)
  - [ ] Has description of all features
  - [ ] Has installation instructions
  - [ ] Has security section (WITHOUT /myhoney/ mention)
  - [ ] Has license (MIT) ✓
  - [ ] Last updated date correct
  - [ ] Version number accurate

- [ ] **SECURITY_NOTE.md Present**
  - [ ] Explains private protection
  - [ ] Lists what's NOT on GitHub
  - [ ] Explains why (/myhoney/ is private)

---

### 🔧 Code Quality

- [ ] **No Debug Logs**
  - [ ] Remove console.log() calls
  - [ ] Remove development comments
  - [ ] Remove test/dummy data

- [ ] **No Hardcoded Secrets**
  - [ ] Check all PHP files for API keys
  - [ ] Check all JavaScript for tokens
  - [ ] Check HTML for credentials
  - `grep -r "api_key\|API_KEY\|token\|TOKEN" /var/www/html/`

- [ ] **All Links Working**
  - [ ] Dashboard URL: https://craith.cloud ✓
  - [ ] GitHub URL: https://github.com/RaithChr/HamClock ✓
  - [ ] Support links functional
  - [ ] QRZ.com links correct

---

### 📦 Git Operations

- [ ] **Git Status Clean**
  - [ ] `git status` shows "nothing to commit"
  - [ ] No untracked private files
  - [ ] No modified .env file
  - `git status`

- [ ] **Git History Clean**
  - [ ] No API keys in commit history
  - [ ] No passwords in commit messages
  - [ ] Commits have clear messages
  - `git log --oneline -10`

- [ ] **Remote Configuration**
  - [ ] Remote is set to GitHub (master)
  - [ ] No old/abandoned remotes
  - `git remote -v`

- [ ] **Branch Correct**
  - [ ] On `master` branch (not main)
  - [ ] Master is DEFAULT branch on GitHub
  - `git branch` and GitHub settings

---

### 🌐 GitHub Repository

- [ ] **Repository Settings**
  - [ ] Default branch: `master` ✓
  - [ ] Visibility: `Public` ✓
  - [ ] License: `MIT` ✓
  - [ ] Description: Set correctly ✓
  - [ ] No empty branches (delete `main` if exists)

- [ ] **Content Visible**
  - [ ] README.md displays (check GitHub repo)
  - [ ] Screenshots visible
  - [ ] All 12 files present
  - [ ] No "404" or missing content

---

### 🚀 Deployment Steps

**BEFORE PUSH:**

1. Run full security check:
   ```bash
   cd /home/chris-admin/.openclaw/workspace
   
   # Check for secrets
   grep -r "ghp_\|sk_\|api_key" . --exclude-dir=.git
   
   # Check for passwords
   grep -r "password\|passwd" . --exclude-dir=.git
   
   # Check git status
   git status
   ```

2. Verify .gitignore:
   ```bash
   cat .gitignore | grep -E "avatars|myhoney|memory|env|GWEN"
   ```

3. Check what would be pushed:
   ```bash
   git diff --cached --name-only
   ```

4. Verify GitHub settings:
   - [ ] Default branch is `master`
   - [ ] `/myhoney/` NOT mentioned anywhere in docs
   - [ ] Support buttons visible

5. **DEPLOY:**
   ```bash
   git add .
   git commit -m "Update: [DESCRIPTION] (Date time UTC)"
   git push -u origin clean_master:master --force
   ```

---

## 🔒 Critical Reminders

### NEVER push:
- ❌ API Keys (Brevo, GitHub, etc.)
- ❌ Passwords (sudo, email, SSH)
- ❌ Private images (avatars/, bikini, lingerie)
- ❌ Personal memory files (memory/)
- ❌ Gallery directory (/myhoney/)
- ❌ Performance metrics (GWEN_*.md)
- ❌ .env files

### ALWAYS protect:
- ✅ Keep credentials in .env (local only)
- ✅ Keep GitHub token in .env
- ✅ Keep /myhoney/ password-protected locally
- ✅ Keep private images locally only
- ✅ Document security measures

### ALWAYS include:
- ✅ README.md with screenshot
- ✅ Support/Donate buttons
- ✅ SECURITY_NOTE.md
- ✅ Clean git history
- ✅ MIT License
- ✅ Author attribution

---

## 📋 Quick Checklist (TL;DR)

Before every push:
```bash
# 1. Security
grep -r "ghp_\|sk_\|password" /home/chris-admin/.openclaw/workspace/ --exclude-dir=.git

# 2. Git clean
git status

# 3. Check what's pushed
git diff --cached --name-only

# 4. Verify files (should be 12)
git ls-files | wc -l

# 5. Verify NO private folders
git ls-files | grep -E "avatars|myhoney|memory|GWEN_" && echo "⚠️ ALERT!" || echo "✅ SAFE"

# 6. Push
git push -u origin clean_master:master --force
```

---

## 🎯 Example: Adding New Feature

```bash
# 1. Make changes to /var/www/html/
# 2. Update README.md with new feature

# 3. BEFORE PUSH - Run this:
cd /home/chris-admin/.openclaw/workspace

# Security check
grep -r "api_\|token\|password" . --exclude-dir=.git || echo "✅ No secrets found"

# Git check
git status
git ls-files | grep -E "avatars|myhoney|memory" || echo "✅ Private folders protected"

# 4. Commit
git add -A
git commit -m "Update: New Feature Name (Feb 8, 2026 12:00 UTC)"

# 5. Push
git push -u origin clean_master:master --force
```

---

## 🔗 Related Files

- **Repository:** https://github.com/RaithChr/HamClock
- **Dashboard:** https://craith.cloud
- **Token Storage:** /home/chris-admin/.env (GITHUB_TOKEN)
- **.gitignore:** /home/chris-admin/.openclaw/workspace/.gitignore
- **README.md:** Primary documentation
- **SECURITY_NOTE.md:** Privacy & protection details

---

**Last Updated:** 2026-02-08 12:57 UTC  
**Purpose:** Prevent accidental exposure of private data  
**Responsibility:** Review before EVERY push!

---

🛡️ **SAFETY FIRST** - This checklist is your guardian! ✨
