# CLAUDE.md

Read [AGENTS.md](AGENTS.md) first. It holds every rule for this repository: layout,
naming, comments, the language split, how a page works and the design system. What
follows only adds Claude-specific notes.

## Session hygiene

Start a new session per task. `/rewind` beats correcting a thread that has drifted.
Compact around half the context, not at the edge.

## Scope

This repository is small on purpose. Deliver what was asked at the scope intended,
and make the routine calls yourself instead of asking. If you see a better approach,
say so in one sentence and continue with the task as asked.

Do not spawn subagents for work in this repository. Nothing here is large enough or
independent enough to be worth the overhead.

Do not add verification passes of your own output. Run `composer lint`, open the page,
report what you saw, stop.

## Stop conditions

Ask before you do any of these:

- deleting or renaming a file
- adding a dependency to `composer.json` or `package.json`
- changing how `bootstrap.php` loads `src/`, or the contract between `page()` and
  `views/layouts/app.php`: every screen depends on both
- changing anything in `data/` while a test session is running
- replacing a palette or a typeface in `public/assets/css/tokens.css`
- editing `.github/`

## Running it

```bash
composer install
composer start          # http://localhost:8000
composer lint
```

There is no test suite. Verification means opening the page, at phone width too.

## Git

Work happens on `develop`. Branch off it, open the pull request against it, and leave
`main` alone: that is the branch a demo runs from.
