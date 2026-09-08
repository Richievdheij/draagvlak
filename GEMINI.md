# GEMINI.md

Read [AGENTS.md](AGENTS.md) first. It holds every rule for this repository: layout,
naming, comments, the language split, how a screen works and the design system. What
follows only adds Gemini-specific notes.

## Before you change anything

Read the file you are about to edit and the files it works with. This repository is
small enough to read; do not answer from a summary of it.

A screen is four files whose names follow from each other:

```
public/contacts.php                             the URL
src/Features/Contacts/Pages/ContactsPage.php    what it does
views/features/contacts/contacts.php            what it looks like
public/assets/css/features/contacts/            how it looks
```

`src/Core/App.php` shows what a screen can reach, and `src/Core/Page.php` shows the four
steps every screen runs through.

## Where things go

Feature-first: everything about one subject lives in one folder, and `src/Features/`,
`views/features/` and `assets/css/features/` mirror each other. Something two features
need is a component; something that is plumbing is `Core/`.

## Comments

Short. One sentence on a class, a docblock on a method only when the name and the
signature do not already say it, and an annotation wherever an editor cannot infer a
type: an `array` always gets a shape, and a template lists its `@var` inputs. Never a
comment that repeats the next line, and never a name, a date or a history note.

## Output

Apply edits to the files. Do not print a full rewritten file in chat when an edit is
possible, and do not restate what you changed line by line. One short sentence on what
changed and what you ran is enough.

Do not hand-format: `composer format` and `npm run format` own the layout of a file.

Answer the person in Dutch. Keep the code, comments, file names and commit messages
English.

## Stop conditions

Ask before deleting or renaming files, adding dependencies, changing how `bootstrap.php`
builds the `App` or how `src/Core/Page.php` runs a screen, changing a constant in
`src/Features/Score/ScoreRules.php`, dropping or restructuring the database, replacing a
palette or a typeface in `base/tokens.css`, touching `data/scenario.json` during a test
session, or editing `.github/`, `.vscode/` or the formatter configuration.

## Running it

```bash
composer install
composer db:setup       # database, tables and demo content
composer start          # http://localhost:8000
composer check          # syntax and formatting
```

Log in with `test@example.com` and the password `password`, contact code `SAM-2938`. Those credentials stay in
this file and in the README; they never end up on a screen.

## Git

Work happens on `develop`. Branch off it, open the pull request against it, and leave
`main` alone.
