# 🔒 SECURITY & PRIVACY NOTICE

## PRIVATE FOLDERS - NIEMALS AUF GITHUB!

Diese Ordner/Dateien sind PRIVAT und dürfen NICHT öffentlich sein:

### ❌ VERBOTEN AUF GITHUB:
- `avatars/` - Private images
- `memory/` - Personal session logs
- `.env` - API Keys, Credentials
- `GWEN_*.md` - Performance tracking

### ✅ SAFE ZU PUSHEN:
- `README.md` - Public documentation
- `screenshots/` - Dashboard screenshots only
- `.gitignore` - Protection rules
- `AGENTS.md`, `SOUL.md`, `USER.md` - Configuration (no secrets)

## .gitignore Rules

```
# PRIVATE FOLDERS - NEVER PUSH!
avatars/

# PRIVATE FILES
GWEN_*.md
.env
*.key
*.pem
credentials.json
```

## If Secrets Leak

If you accidentally commit secrets:
1. Delete the GitHub repository
2. Create new with clean history
3. Force push safe content only

**Remember:** Hackers search GitHub for credentials to exploit projects!

---
**Last Updated:** 2026-02-08 12:02 UTC
**Maintainer:** Christian Raith (OE3LCR)
