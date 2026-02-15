# DEPLOY.md — Lokale Deployment-Referenz

> ⚠️ Diese Datei ist in .gitignore eingetragen und wird NIE gepusht.
> Sie liegt lokal als Erinnerung und Schutz vor Fehlveröffentlichungen.

Vollständige Infos: `~/.openclaw/workspace/DEPLOY.md`

## Schnell-Checkliste vor jedem git push

- [ ] Keine API Keys hardcoded?
- [ ] `.env` nicht gestagt?
- [ ] `MEMORY.md`, `memory/`, `SOUL.md` nicht gestagt?
- [ ] `myhoney/` nicht gestagt?
- [ ] `data/solar-data.json`, `data/dx-cache.txt` nicht gestagt?
- [ ] Kein `rand()` für echte Daten?
- [ ] `git status` geprüft bevor `git push`?

## Sicherheits-Scan

```bash
grep -rn "xkeysib\|ghp_\|sk_1f\|rand(\|password.*=" \
  --include="*.php" --include="*.js" .
```
