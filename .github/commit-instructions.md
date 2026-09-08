# Commit message instructions

This file is the brief behind the commit message button in the Source Control panel of
VS Code. Write the message for this repository, not a generic one. Nothing rejects a
commit that ignores this; it is the shape we want, not a gate.

## Shape

```
type(scope): short title

body
```

Title, one blank line, body. Write a body unless the change really is one line.

## Type

| Type       | For                                                               |
| ---------- | ----------------------------------------------------------------- |
| `feat`     | A screen, a form, a message, behaviour a participant can see      |
| `fix`      | Something that was broken                                         |
| `refactor` | Code moved or reshaped without changing what a screen does        |
| `style`    | Formatting only. How a screen looks is `feat` or `fix`, not this  |
| `docs`     | `docs/`, `README.md`, `AGENTS.md` and the other instruction files |
| `chore`    | Tooling, configuration, a dependency, `database/schema.sql`       |

There is no test suite, no build step and no CI here, so never write `test`, `build`,
`perf` or `ci`.

## Scope

The folder the change lives in, lowercase, in the parentheses. Leave the parentheses off
when the change touches more than one.

`auth` `contacts` `messages` `score` `home` `settings` `screening` `reset` `scenario`
`core` `design` `docs` `tooling`

## Title

English, present tense, lowercase after the colon, no full stop, at most 72 characters.
Say what the change does, not what the author did. So `add the contacts screen`, never
`added contacts screen`, `changes`, `update` or `wip`.

## Body

Wrap at 72 characters and pick the form that fits:

- **Bullets**, one `-` per thing, when the commit does several separate things.
- **A paragraph** when it does one thing that needs explaining: what it does, and why it
  was done this way.

Put in what the diff cannot say:

- Which screen someone opens to see it, as a URL.
- Why, whenever the reason is not obvious from the code.
- That a choice comes from the scenario and is not a technical one. Anything touching
  `src/Features/Score/ScoreRules.php` or `data/scenario.json` is a scenario choice.
- What the other person has to run afterwards, such as `composer install`.

## Footer

Add `BREAKING CHANGE:` and one sentence when `database/schema.sql` changed, because
everyone then has to run `composer db:fresh` and loses their local data.

## Never in a commit message

- A name, a date, or the name of an AI tool.
- The demo credentials. Those live in `README.md` and the instruction files only.
- Dutch. The screens are Dutch, everything in the repository is English.
- A list of the files that were touched. The diff already says that.

## Example

```
feat(contacts): add the contacts screen with a response window

Contacts are sorted by the time left on their response window, so the
person closest to a penalty stands at the top.

- Reading the list starts the clock on every message that has a window.
  That is the scenario: the pressure begins the moment you look.
- Removing a contact is the quietest control on the row, on purpose.

Open http://draagvlak.test/contacts.php to see it.
```
