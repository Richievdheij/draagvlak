# GEMINI.md

Read [AGENTS.md](AGENTS.md) first. It holds every rule for this repository: layout,
naming, comments, the language split, how a page works and the design system. What
follows only adds Gemini-specific notes.

## Before you change anything

Read the file you are about to edit and the files it includes. This repository is small
enough to read; do not answer from a summary of it. `bootstrap.php` explains what every
page can assume, and `src/support/view.php` explains what `page()` does.

## Output

Apply edits to the files. Do not print a full rewritten file in chat when an edit is
possible, and do not restate what you changed line by line. One short sentence on what
changed and what you ran is enough.

Answer the person in Dutch. Keep the code, comments and commit messages English.

## Stop conditions

Ask before deleting or renaming files, adding dependencies, changing how `bootstrap.php`
loads `src/` or how `page()` and the layout work together, changing a constant in
`src/score/score-rules.php`, dropping or restructuring the database, replacing a palette
or a typeface in `base/tokens.css`, touching `data/scenario.json` during a test session,
or editing `.github/`.

## Running it

```bash
composer install
composer db:setup       # database, tables and demo content
composer start          # http://localhost:8000
composer lint
```

Log in with `sam@draagvlak.test` and the password `draagvlak`.

## Git

Work happens on `develop`. Branch off it, open the pull request against it, and leave
`main` alone.
