<?php
declare(strict_types=1);

require __DIR__ . '/../theme.php';
require __DIR__ . '/../recipe_queries.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Load every recipe with its category name (see recipe_queries.php).
// If the database fails, log the real error privately and show the
// visitor a generic message, so table names never leak onto the page.
$recipes = [];
$load_failed = false;

try {
  require __DIR__ . '/../db.php';
  $recipes = recipes_with_category($db);
} catch (mysqli_sql_exception $error) {
  error_log($error->getMessage());
  http_response_code(500);
  $load_failed = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Recipes | IDM 232</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;700&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
</head>

<body class="theme-<?= e($theme) ?>">

<header class="site-header">
  <span>IDM 232 &middot; Quinn Kessler</span>
  <nav class="site-nav">
    <a href="index.php">Form</a>
    <a href="recipes.php" aria-current="page">Recipes</a>
    <a href="?theme=<?= e($other_theme) ?>"><?= $theme === 'dark' ? 'Light mode' : 'Dark mode' ?></a>
  </nav>
</header>

<main class="wide">

  <p class="eyebrow">[ Assignment 3 ]</p>
  <h1>Recipes from the <em>Database</em></h1>

  <p class="count"><?= count($recipes) ?> recipes</p>

  <?php if ($load_failed): ?>
    <p class="errors">The recipes couldn't be loaded. Please try again later.</p>
  <?php endif; ?>

  <ul class="recipe-grid">
    <?php foreach ($recipes as $recipe): ?>
      <li class="recipe-card">
        <img
          src="<?= e($recipe['main_image']) ?>"
          alt="<?= e($recipe['title']) ?>"
          loading="lazy"
        >
        <p class="recipe-meta">
          <?= e($recipe['category']) ?> &middot; Serves <?= e((string) $recipe['servings']) ?>
        </p>
        <h2><?= e($recipe['title']) ?></h2>
        <p class="recipe-subtitle"><?= e($recipe['subtitle']) ?></p>
      </li>
    <?php endforeach; ?>
  </ul>

</main>

<footer class="site-footer">
  <span>Assignment 3 — Databases and mysqli</span>
  <span>Drexel University</span>
</footer>

</body>
</html>
