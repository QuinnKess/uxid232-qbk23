# Cookbook — IDM 232

**Quinn Kessler** · Drexel University · Fall 2026

## What is this project?

A cookbook web app built with PHP and MySQL. It stores 40 recipes in a relational database (categories, recipes, and each recipe's ingredients, steps, tools, tips and photos) and shows them on a recipe list page. By the final, users will be able to browse every recipe, search and filter by category, open a full recipe page with its ingredients and step-by-step instructions, and add or edit recipes through validated forms.

## How do I run it locally?

1. Install **XAMPP** or **MAMP** and start Apache and MySQL.
2. Put this project folder inside your `htdocs` folder (XAMPP: `xampp/htdocs`, MAMP: `MAMP/htdocs`).
3. Open phpMyAdmin, create a database named `idm232_cookbook` with collation `utf8mb4_unicode_ci`, then use the **Import** tab to load `database/schema.sql`.
4. Make a copy of `config.example.php` in the same folder, name it `config.php`, and fill in your database login. Usual defaults: XAMPP is port `3306`, user `root`, empty password; MAMP is port `8889`, user `root`, password `root`.
5. Open the recipe list in your browser, using your project folder's name:
   - XAMPP: `http://localhost/<project-folder>/alpha/recipes.php`
   - MAMP: `http://localhost:8888/<project-folder>/alpha/recipes.php`

## What's in the repo

| Path | What it is |
|------|------------|
| `database/schema.sql` | Creates all tables and seeds 6 categories and 40 recipes. Every table has an `AUTO_INCREMENT` primary key. `recipes.category_id` is a foreign key to `categories`, and the other tables link to `recipes` by `recipe_id`. |
| `db.php` | Connects with mysqli in exception mode (`MYSQLI_REPORT_ERROR \| MYSQLI_REPORT_STRICT`). |
| `config.example.php` | Template for `config.php`, which holds the database login and is gitignored. |
| `recipe_queries.php` | Database read functions: `recipes_with_category()`, `recipes_in_category()` and `categories_with_counts()`. |
| `alpha/recipes.php` | Recipe list page, read from the database, with links to filter by category. |
| `alpha/index.php` | Recipe submission form with server-side validation (week 2). |
| `theme.php` | Light/dark mode toggle. Only accepts `light` or `dark`. |
| `alpha/images/` | Recipe photos. Paths are stored in the database. |

## Data access and security

- The recipe list calls `recipes_with_category()`, which uses an `INNER JOIN` to return one row per recipe with its category name, sorted by title. It takes no user input, so it uses `mysqli_query`.
- The category links (`recipes.php?category=2`) send a value from the user. The page first checks it is a whole number of 1 or more with `filter_input(..., FILTER_VALIDATE_INT)`, then `recipes_in_category()` passes it to SQL only through a `?` placeholder with `mysqli_execute_query`. Anything invalid shows all recipes.
- The category links show recipe counts from `categories_with_counts()`, which uses a `LEFT JOIN` with `COUNT` and `GROUP BY`.
- Every value written to the page is escaped with `htmlspecialchars`.
- Database errors are caught, logged with `error_log`, and replaced with a generic message, so error details never reach the page.
- Every PHP file starts with `declare(strict_types=1);`.

## AI use

**AI tools used: Claude (Anthropic).** No other AI tools were used.

How Claude was used:

- **Recipe data:** turned the professor's recipe PDFs into the spreadsheet behind `schema.sql`, and filled in content cut off at page breaks: 135 ingredient lines, 71 step titles, 18 whole steps, one description and the tools for 3 recipes. AI ingredient quantities are estimates.
- **Database:** converted the spreadsheet into `schema.sql`, and added the `categories` table, foreign keys and `AUTO_INCREMENT` keys.
- **Code:** reviewed and fixed the week 2 form, and wrote `db.php`, `recipe_queries.php`, `recipes.php` (including the category filter), `theme.php` and the CSS.
- **Testing:** test-imported the schema into MySQL and checked the pages, including invalid and malicious input on the category filter.
- **Learning and setup:** explained the code and concepts, walked through phpMyAdmin and git commands, and helped write this README.
