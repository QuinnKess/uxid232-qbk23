# Cookbook — IDM 232

**Quinn Kessler** · Drexel University · Fall 2026

## What is this project?

A cookbook web app built with PHP and MySQL, for home cooks looking for a recipe to make. It stores 40 recipes in a relational database (categories, recipes, and each recipe's ingredients, steps, tools, tips and photos). Visitors can already browse every recipe, search by name or ingredient, filter by category, and open a full recipe page with its ingredients and step-by-step instructions. By the final, an admin will also be able to log in and add, edit and delete recipes.

## How do I run it locally?

1. Install **XAMPP** or **MAMP** and start Apache and MySQL.
2. Put this project folder inside your `htdocs` folder (XAMPP: `xampp/htdocs`, MAMP: `MAMP/htdocs`).
3. Open phpMyAdmin, create a database named `idm232_cookbook` with collation `utf8mb4_unicode_ci`, then use the **Import** tab to load `database/schema.sql`.
4. Make a copy of `config.example.php` in the same folder, name it `config.php`, and fill in your database login. Usual defaults: XAMPP is port `3306`, user `root`, empty password; MAMP is port `8889`, user `root`, password `root`.
5. Open the recipe list in your browser, using your project folder's name:
   - XAMPP: `http://localhost/<project-folder>/alpha/recipes.php`
   - MAMP: `http://localhost:8888/<project-folder>/alpha/recipes.php`

## Features so far

- **Browse:** `alpha/recipes.php` lists every recipe with its photo, category and servings.
- **Search:** a search box matches recipe titles, subtitles, descriptions and ingredient names.
- **Filter:** category links narrow the list, and work together with search.
- **Recipe detail:** `alpha/recipe.php?id=1` shows one recipe's full content: description, ingredients, numbered steps with photos, its tip and its kitchen tool. Unknown ids show a "Recipe not found" page with a 404 status.
- **Light and dark mode**, and a recipe form with server-side validation (`alpha/index.php`).

## What's in the repo

Config, logic and markup are kept separate:

| Path | What it is |
|------|------------|
| `config.example.php` | Template for `config.php`, which holds the database login and is gitignored. |
| `database/schema.sql` | Creates all tables and seeds 6 categories and 40 recipes. Every table has an `AUTO_INCREMENT` primary key. `recipes.category_id` is a foreign key to `categories`, and the other tables link to `recipes` by `recipe_id`. |
| `includes/db.php` | Connects with mysqli in exception mode (`MYSQLI_REPORT_ERROR \| MYSQLI_REPORT_STRICT`). |
| `includes/recipe_queries.php` | All database read functions (list, search, categories, one recipe and its ingredients, steps, tip and tools). |
| `includes/helpers.php` | `e()` for escaping output and `page_link()` for building links. |
| `includes/theme.php` | Light/dark mode. Only accepts `light` or `dark`. |
| `templates/header.php`, `templates/footer.php` | Shared page top (head, navigation) and bottom. |
| `alpha/recipes.php` | Recipe library page: browse, search and filter. |
| `alpha/recipe.php` | Recipe detail page. |
| `alpha/index.php` | Recipe form with server-side validation (week 2). |
| `alpha/images/` | Recipe photos. Paths are stored in the database. |

## Data access and security

- **Every value from the visitor reaches SQL through a `?` placeholder with `mysqli_execute_query`:** the search words, the category id and the recipe id. No variable is ever pasted into a SQL string. Only queries with no variables at all (the category list) use `mysqli_query`.
- **Search and filter** use one function, `find_recipes()`. It builds its `WHERE` clause from fixed pieces of SQL and adds each value to the parameter array in the same order as its `?`. `%` and `_` in search words are escaped so they don't act as wildcards.
- **Input is validated first:** ids must be whole numbers of 1 or more (`filter_input` with `FILTER_VALIDATE_INT`); search text must be a string, is trimmed and is capped at 100 characters. Invalid input shows all recipes, or "Recipe not found".
- **Joins:** recipes are listed with their category name through an `INNER JOIN`; category counts use a `LEFT JOIN` with `COUNT` and `GROUP BY`; a recipe's tools come through the `recipe_tools` link table.
- Every value written to the page is escaped with `htmlspecialchars`.
- Database errors are caught, logged with `error_log`, and replaced with a generic message, so error details never reach the page.
- Every PHP file starts with `declare(strict_types=1);`.

## AI use

**AI tools used: Claude (Anthropic).** No other AI tools were used.

How Claude was used:

- **Recipe data:** turned the professor's recipe PDFs into the spreadsheet behind `schema.sql`, and filled in content cut off at page breaks: 135 ingredient lines, 71 step titles, 18 whole steps, one description and the tools for 3 recipes. AI ingredient quantities are estimates.
- **Database:** converted the spreadsheet into `schema.sql`, and added the `categories` table, foreign keys and `AUTO_INCREMENT` keys.
- **Code:** reviewed and fixed the week 2 form; wrote the files in `includes/` and `templates/`, `recipes.php` (category filter and search), `recipe.php` (detail page) and the CSS; and split shared code into `includes/` and `templates/`. Each of these files says so in a comment at the top.
- **Testing:** test-imported the schema into MySQL and checked the pages, including invalid and malicious input in the search box, category filter and recipe id.
- **Learning and setup:** explained the code and concepts, walked through phpMyAdmin and git commands, and helped write this README.
