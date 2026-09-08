# CLAUDE.md

Read [AGENTS.md](AGENTS.md) first. It holds every rule for this repository: layout,
naming, comments, the language split, how a screen works and the design system. What
follows only adds Claude-specific notes.

## Skills

The way of working is written out as skills in `.claude/skills/`. Invoke the one that
matches before you start.

| Skill                | Use it when                                                |
| -------------------- | ---------------------------------------------------------- |
| `draagvlak-style`    | Before you write a line: comments, annotations, formatting |
| `draagvlak-screen`   | Adding or changing a screen, a form, a fragment            |
| `draagvlak-design`   | Touching CSS, tokens, spacing, focus or contrast           |
| `draagvlak-database` | A query, a repository, a column, a schema change           |
| `draagvlak-review`   | Before you hand work back, and before a pull request       |

## Session hygiene

Start a new session per task. `/rewind` beats correcting a thread that has drifted.
Compact around half the context, not at the edge.

## Scope

This repository is small on purpose. Deliver what was asked at the scope intended,
and make the routine calls yourself instead of asking. If you see a better approach,
say so in one sentence and continue with the task as asked.

Do not spawn subagents or workflows for work in this repository. Nothing here is large
enough or independent enough to be worth the overhead. If a session is started in a mode
that fans work out by default, say so once and work directly.

Do not add verification passes of your own output. Run `composer check`, open the screen,
report what you saw, stop.

## Stop conditions

Ask before you do any of these:

- deleting or renaming a file
- adding a dependency to `composer.json` or `package.json`
- changing how `bootstrap.php` builds the `App`, or the four steps in
  `src/Core/Page.php`: every screen depends on both
- changing a constant in `src/Features/Score/ScoreRules.php` (those numbers are the
  scenario)
- dropping or restructuring the database, or running `composer db:fresh` while a test
  session is running
- changing `data/scenario.json` while a test session is running
- replacing a palette or a typeface in `public/assets/css/base/tokens.css`
- editing `.github/`, `.vscode/`, `.php-cs-fixer.php` or `.prettierrc`

## Running it

```bash
composer install
composer db:setup       # database, tables and demo content
composer check          # syntax and formatting
```

The project runs on Laravel Herd at **http://draagvlak.test**, pinned to PHP 8.4 with
`herd isolate 8.4`. Herd finds `public/` by itself, so nothing outside it is reachable.
Without Herd, `composer start` serves the same thing on `http://localhost:8000`.

MySQL is the one on `127.0.0.1:3306` that `http://phpmyadmin.test` also talks to, so
what you see there is what the app uses.

`.mcp.json` sets up two MCP servers for this project: `draagvlak-files` for the files in
this folder, and `herd` for sites and PHP versions. They need approving once with `/mcp`.

Log in with `test@example.com` and the password `password`, contact code `SAM-2938`. Those credentials belong in
this file and in the README, never on a screen: a participant should never see that this
is a test build.

There is no test suite. Verification means opening the screen, at phone width too.

## Git

Work happens on `develop`. Branch off it, open the pull request against it, and leave
`main` alone: that is the branch a demo runs from.
