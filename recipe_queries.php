<?php
declare(strict_types=1);

// Database read functions. Each one takes the mysqli connection.

// Every recipe with its category name, sorted A to Z by title.
// INNER JOIN pairs each recipe with the category its category_id points to.
// No user input, so mysqli_query is fine here. Any function that takes a
// value from the user must use mysqli_execute_query with a ? placeholder.
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
