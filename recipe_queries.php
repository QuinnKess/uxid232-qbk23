<?php
declare(strict_types=1);

// Database read functions. Each one takes the mysqli connection.

// Every recipe with its category name, sorted A to Z by title.
// INNER JOIN pairs each recipe with the category its category_id points to.
// No user input, so mysqli_query is fine here.
function recipes_with_category(mysqli $db): array {
  $sql = 'SELECT recipes.recipe_id, recipes.title, recipes.subtitle,
                 recipes.servings, recipes.main_image,
                 categories.name AS category
          FROM recipes
          INNER JOIN categories ON recipes.category_id = categories.category_id
          ORDER BY recipes.title';

  $result = mysqli_query($db, $sql);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Only the recipes in one category, sorted A to Z by title.
// $category_id comes from the visitor's link, so it is user input:
// it reaches SQL only through the ? placeholder, never pasted into the string.
function recipes_in_category(mysqli $db, int $category_id): array {
  $sql = 'SELECT recipes.recipe_id, recipes.title, recipes.subtitle,
                 recipes.servings, recipes.main_image,
                 categories.name AS category
          FROM recipes
          INNER JOIN categories ON recipes.category_id = categories.category_id
          WHERE recipes.category_id = ?
          ORDER BY recipes.title';

  $result = mysqli_execute_query($db, $sql, [$category_id]);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Every category with how many recipes it has, sorted by name.
// LEFT JOIN keeps a category even if it has no recipes yet (count 0).
// No user input, so mysqli_query is fine here.
function categories_with_counts(mysqli $db): array {
  $sql = 'SELECT categories.category_id, categories.name,
                 COUNT(recipes.recipe_id) AS recipe_count
          FROM categories
          LEFT JOIN recipes ON recipes.category_id = categories.category_id
          GROUP BY categories.category_id, categories.name
          ORDER BY categories.name';

  $result = mysqli_query($db, $sql);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
