---
name: draagvlak-database
description: "Use when touching data in the Draagvlak prototype: writing a query, adding a repository method, adding a column or a table, changing database/schema.sql, or working with Account, Contact, Message and the contact code."
---

# Data in Draagvlak

Read `AGENTS.md` if you have not. MySQL through PDO. No ORM, no query builder, no
migrations.

## Where a query lives

In a repository, inside the feature it belongs to. Never in a page class and never in a
template.

```
src/Features/Auth/AccountRepository.php       the users table, passwords, contact codes
src/Features/Contacts/ContactRepository.php   the list, adding by code, removing
src/Features/Messages/MessageRepository.php   the inbox and what a choice costs
src/Features/Score/ScoreBoard.php             changing a number and writing down why
```

A repository hands back typed objects, not raw rows: `Account`, `Contact`, `Message`. If
you find yourself returning `array<string, mixed>` to a screen, add a `fromRow()` to the
object instead.

## Two rules that are not negotiable

Every value goes in as a parameter:

```php
$this->database->all('SELECT * FROM contacts WHERE user_id = ?', [$userId]);   // right
$this->database->all("SELECT * FROM contacts WHERE user_id = $userId");        // wrong
```

Every row is looked up together with the id of the visitor who owns it. Without that a
changed number in the URL reaches somebody else's data:

```php
$this->database->first('SELECT * FROM contacts WHERE id = ? AND user_id = ?', [$id, $userId]);
```

## The five methods

| Method                    | For                               |
| ------------------------- | --------------------------------- |
| `all($sql, $params)`      | More rows                         |
| `first($sql, $params)`    | One row, or null                  |
| `value($sql, $params)`    | One value: a count, a name, an id |
| `run($sql, $params)`      | INSERT, UPDATE, DELETE            |
| `insert($table, $values)` | One row, returns the new id       |

## The four tables

`users` is an account: a name, an address, a hashed password, a number, and the
`contact_code` it shares. `contacts` are the people on somebody's list. `messages` are
what those people sent. `score_events` is the trail: every change to a number is written
there as well, so a session can be read back afterwards.

Tables are plural snake_case, columns snake_case. The same value in PHP and JSON is
camelCase: `respond_within_seconds` becomes `respondWithinSeconds`.

## The one seam worth knowing

`contacts.contact_user_id` points at a real account when two people exchanged a code, and
is null for the people from `data/scenario.json`, who belong to nobody.

Every read in `ContactRepository` therefore joins `users` and uses `COALESCE`, so a
linked contact shows the account's current name and number and a scenario contact shows
the copies in its own row. Write a new read the same way, or reuse the `SELECT` constant
that is already there.

The same applies to writing: `ContactRepository::changeScore()` moves a linked account's
real score through `ScoreBoard`, and only touches `contacts.score` when there is no
account behind it.

## Contact codes

`ContactCode` builds them: three characters from the account, a dash, four random ones.
The alphabet leaves out I, O, 0 and 1 because those get misread.

- Generating one: `AccountRepository::create()` keeps drawing until the database says it
  is free. Nothing else generates a code, and a code never changes afterwards.
- Reading one somebody typed: always `ContactCode::normalise()` first. Lower case, a
  missing dash and stray spaces all have to land on the same code.

## Changing the schema

Edit `database/schema.sql`, add the column to the object that reads the row (`Account`,
`Contact`, `Message`), then:

```bash
composer db:fresh
```

That drops the database and builds it again, so everybody loses their local accounts and
answers. Never while somebody is testing. There are no migrations here on purpose: the
prototype lives for a few weeks.

A new column gets a comment in the schema when it is not obvious what it is for.

## Starting content

`data/scenario.json` is Dutch content the team maintains, and only the demo account gets
it, through `Scenario::seedFor()`. Somebody who registers starts with an empty list and
their own code. Do not add seeding to registration: an app that hands you strangers is
exactly what this screen is not.

The demo account itself is a fixture. `src/Features/Scenario/DemoSeeder.php` holds its
address, password and contact code as constants, so they are the same after every
`db:fresh` and the README can name them. It only exists while `app.demo` is true, because
that password is in the README. Seed it from there and from nowhere else.
