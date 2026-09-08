---
name: draagvlak-review
description: "Use before handing work back or opening a pull request on the Draagvlak prototype. The checklist that catches the mistakes this repository actually makes: a file in the wrong feature, Dutch in src/, an unescaped value, a hex code outside tokens, a query without the user id, team things printed on a screen."
---

# Before you hand it back

Work through this in order. Every line is here because it went wrong before.

## Run it

```bash
composer check    # every PHP file parses, and the formatting is right
npm run check     # the JavaScript, and the layout of CSS and Markdown
composer start
```

Open the screen you changed at `http://localhost:8000`, and again in device view at
390px wide. A green check only says the syntax and the layout of the file are right.

Say plainly what you could not run.

## Placement

- The screen is four files that follow from each other: `public/<screen>.php`,
  `src/Features/<Name>/Pages/<Name>Page.php`, `views/features/<name>/<screen>.php`,
  `assets/css/features/<name>/`.
- Everything about one subject sits in that feature. Nothing about it is left in another
  folder.
- Something two features use is in `components/`, or in `Core/` when it is plumbing.
- The namespace matches the folder, the file is named after the class, one class per
  file, and the class is `final` unless something extends it.

## Language

- No Dutch sentence anywhere in `src/`. The one exception is the return of `title()`.
- No English on a screen a participant reads.
- File names and URLs are English.
- A class returns a key (`answered_in_time`); the Dutch for it is in the template that
  prints it, or in `views/components/notices.php`.
- Every flash key you queued has a line in `views/components/notices.php`. A key without
  one prints nothing at all.

## Nothing from the team on a screen

- No demo credentials, no seeded strangers, no "test" in anything a participant can
  read. Those belong in the README and in `CLAUDE.md`.
- A new account still starts with an empty list and its own code.

## Printing

- Everything that is not a literal you typed goes through `$this->e()`.
- Attributes built from data go through `$this->attributes()`.
- A template only prints. No counting, comparing or formatting between two tags; that
  belongs in `data()` or on the object being printed.

## Data

- Every query is a prepared statement with the values passed separately.
- Every row is looked up together with the id of the logged-in user.
- The query lives in a repository inside its feature.
- A repository returns a typed object, not a raw row.

## Styling

- No literal hex code, pixel value or font name outside `base/tokens.css`.
- A new colour has both a light and a dark value in one `light-dark()`.
- The stylesheet is in the folder of its feature, or in `components/` when a second
  feature uses it, and named after what it styles.
- The focus outline is still there, still the only one, and no input gained a hover
  state.
- Touch targets are still at least 44px.

## Comments

- Every class has one sentence saying why it exists.
- A method has a docblock only where the name and the signature do not say it.
- Every `array` has a shape in the docblock, or an object is returned instead.
- Every template lists its `@var` inputs.
- No comment that repeats the next line, no history, no name, no date, no commented-out
  code.

## The scenario

- No screen got friendlier. No reassuring disclaimer appeared. The opt-out still does
  not work.
- The numbers in `src/Features/Score/ScoreRules.php` are unchanged, unless changing them
  was the task and it was agreed.

## The pull request

Against `develop`, never `main`. Branch name English with a `feature/`, `fix/` or
`docs/` prefix. Commit message in English, present tense, saying what the change does.
