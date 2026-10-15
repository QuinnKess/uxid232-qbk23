# uxid241-qbk23

## php test file for assignment 1

# Assignment 2: Review and AI usage notes

**Project:** `uxid232_qbk23/alpha`
**Date:** 2026-10-08
**Files:** `index.php`, `style.css`

## 1. What was checked

- **Location:** `alpha/index.php` is served by MAMP at `http://localhost:8888/uxid232_qbk23/alpha/` and returns HTTP 200.
- **Syntax:** `php -l` (PHP 8.5.2) found no syntax errors.
- **Form behavior:** tested by sending POST requests to the running server:
  - valid input (`Pasta` / `a@b.com`)
  - invalid input (empty name, `bad` email)
  - an HTML/script injection attempt (`"><script>…`)
  - malformed input (`name[]=x`, which sends an array instead of a string)
- **Assets:** checked that the linked `style.css` loads.

## 2. What already worked

- Validation logic was correct: valid input passed, invalid input produced both error messages.
- Entered values stayed in the form after submit.
- Output escaping with `htmlspecialchars` (the `e()` helper) correctly neutralized the injection attempt.

## 3. Problems found

| # | Problem | Impact |
|---|---------|--------|
| 1 | `style.css` was linked but did not exist | 404 error; page unstyled |
| 2 | Errors and success state were never displayed in the HTML | User got no feedback once debug output was removed |
| 3 | `dump()` debug calls printed before `<!DOCTYPE html>` | Invalid HTML; debug output visible to users |
| 4 | `post_value()` passed arrays straight to `trim()` | Sending `name[]=x` caused a fatal `TypeError` |
| 5 | Label said "Name:" but the error said "Recipe name"; title was "Document" | Inconsistent wording |

## 4. Fixes applied

### `index.php`

- **Hardened input helper:** non-string values are treated as empty:
  ```php
  function post_value(string $key): string {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
  }
  ```
- **Removed debug output:** deleted the four `dump()` calls. The `dump()` function itself is kept for future debugging.
- **Added an error list** above the form, shown only when validation fails:
  ```php
  <?php if (!empty($errors)): ?>
    <ul class="errors">
      <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  ```
- **Added a success message**, shown only on a valid submission:
  ```php
  <?php if ($success): ?>
    <p class="success">Thanks, <?= e($name) ?> was submitted.</p>
  <?php endif; ?>
  ```
- **Wording:** label changed to "Recipe name:"; page title changed to "Recipe Submission".

### `style.css` (new)

- Basic layout: centered column, full-width inputs, spacing.
- `.errors`: red text on a light red background.
- `.success`: green text on a light green background.

## 5. Verification after fixes

| Test | Result |
|------|--------|
| PHP syntax check | No errors |
| First page load (GET) | No error or success message shown |
| Valid submission | "Thanks, Pasta was submitted." |
| Empty name + bad email | Both error messages listed |
| `name[]=x` | Shows "Recipe name is required." (no crash) |
| `style.css` | Loads (HTTP 200) |

## 6. Open items

- `beta/` and `final/` folders are still empty (expected for this stage).
- Submitted data is validated but not saved or sent anywhere yet.

## Goal

Make the recipe submission form look nicer while keeping it simple, using [matteprojects.com](https://matteprojects.com) as the style reference.

## What changed

### `index.php`
- Added Google Fonts: **Inter Tight** (sans-serif) and **Instrument Serif** (italic accent). Matte's exact fonts aren't public, so these are close free matches.
- Added a header with "IDM 232" and "Quinn Kessler".
- Added a bracketed `[ Assignment 2 ]` label and a large heading, "Forms and *User Input*", with the last two words in italic serif.
- Added a footer with "Assignment 2 — Forms and User Input" and "Drexel University".
- Cleaned up labels (removed colons and `<br>` tags).
- Page title changed to "Recipe Submission | IDM 232".
- PHP logic (validation, escaping, `post_value()`) is unchanged.

### `style.css`
- Dark theme: near-black background, off-white text, muted gray for secondary text.
- Small uppercase, letter-spaced text for the header, footer, labels and button.
- Underline-only text inputs that brighten on focus.
- Rounded outline Submit button that fills in on hover.
- Error and success messages restyled for the dark background.

## Verification
- `php -l index.php`: no syntax errors.
- Both files were written directly to `/Applications/MAMP/htdocs/uxid232_qbk23/alpha/`.
- `NOTES.md` was left unchanged.

# Assignment 3: Database and recipe list

**Files:** `database/schema.sql`, `db.php`, `config.example.php`, `alpha/recipes.php`

## Setup

1. In phpMyAdmin, create a database named `idm232_cookbook` (collation `utf8mb4_unicode_ci`).
2. Import `database/schema.sql` into it.
3. Copy `config.example.php` to `config.php` and add your database login. `config.php` is gitignored.
4. Open `http://localhost:8888/uxid232_qbk23/alpha/recipes.php`.

## What it does

- `schema.sql` creates the `recipes` table (primary key `recipe_id`) and related tables, seeded with 40 recipes.
- `db.php` connects with mysqli in exception mode.
- `recipes.php` lists every recipe with its photo, category and servings. Every value is escaped with `htmlspecialchars`.

The query on `recipes.php`:

```sql
SELECT recipe_id, title, subtitle, category, servings, main_image
FROM recipes
ORDER BY title
```

It returns one row per recipe (40 rows), sorted A to Z by title. It takes no user input, so a plain `mysqli_query` is used.

## AI use

- The recipe data came from the professor's PDFs. Parts cut off at page breaks were filled in with AI: 135 ingredient lines, 71 step titles, 18 whole steps, one description and the tools for 3 recipes. AI ingredient quantities are estimates.
- AI helped write `schema.sql`, `db.php` and `recipes.php`, and tested the import and page against MySQL.
