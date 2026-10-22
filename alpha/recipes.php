<?php
declare(strict_types=1);

require __DIR__ . '/../theme.php';
require __DIR__ . '/../recipe_queries.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Which category the visitor clicked, from a link like recipes.php?category=2.
// This is user input, so it must be a whole number of 1 or more.
// Anything else (missing, text, negative) gives false or null: show all.
$requested_id = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT, [
  'options' => ['min_range' => 1],
]);

$recipes = [];
$categories = [];
$selected_category = null;
$load_failed = false;

// Load the data (see recipe_queries.php). If the database fails, log the
// real error privately and show the visitor a generic message, so table
// names never leak onto the page.
try {
  require __DIR__ . '/../db.php';

  $categories = categories_with_counts($db);

  // Only filter if the requested id is a category that really exists.
  foreach ($categories as $category) {
    if ((int) $category['category_id'] === $requested_id) {
      $selected_category = $category;
    }
  }

  if ($selected_category !== null) {
    $recipes = recipes_in_category($db, $requested_id);
  } else {
    $recipes = recipes_with_category($db);
  }
} catch (mysqli_sql_exception $error) {
  error_log($error->getMessage());
  http_response_code(500);
  $load_failed = true;
}

$selected_id = $selected_category !== null ? (int) $selected_category['category_id'] : null;
$total_recipes = array_sum(array_column($categories, 'recipe_count'));

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

  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>

<body class="theme-<?= e($theme) ?>">

<header class="site-header">
  <span>IDM 232 &middot; Quinn Kessler</span>
  <nav class="site-nav">
    <a href="index.php">Form</a>
    <a href="recipes.php" aria-current="page">Recipes</a>
    <a href="?<?= e(http_build_query(['category' => $selected_id, 'theme' => $other_theme])) ?>"><?= $theme === 'dark' ? 'Light mode' : 'Dark mode' ?></a>
  </nav>
</header>

<main class="wide">

  <p class="eyebrow">[ Assignment 3 ]</p>
  <h1>Recipes from the <em>Database</em></h1>

  <nav class="category-links" aria-label="Filter recipes by category">
    <a href="recipes.php"<?= $selected_id === null ? ' aria-current="page"' : '' ?>>
      All <span class="link-count"><?= e((string) $total_recipes) ?></span>
    </a>
    <?php foreach ($categories as $category): ?>
      <a
        href="recipes.php?category=<?= e((string) $category['category_id']) ?>"
        <?= (int) $category['category_id'] === $selected_id ? 'aria-current="page"' : '' ?>
      >
        <?= e($category['name']) ?> <span class="link-count"><?= e((string) $category['recipe_count']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <p class="count">
    <?= count($recipes) ?> <?= count($recipes) === 1 ? 'recipe' : 'recipes' ?><?php if ($selected_category !== null): ?> in <?= e($selected_category['name']) ?><?php endif; ?>
  </p>

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
