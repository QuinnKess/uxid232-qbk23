<?php
declare(strict_types=1);

// Database read functions. Each one takes the mysqli connection.
// Written with help from Claude (AI). See README, "AI use".
//
// Rule for every function here: a value that can come from the visitor
// (a search word, a category id, a recipe id) reaches SQL only through a
// ? placeholder and mysqli_execute_query, never pasted into the SQL string.

// Recipes for the list page, sorted A to Z by title.
// Both filters are optional: $search matches the title, subtitle,
// description or any ingredient name; $category_id limits to one category.
// The WHERE clause is built from fixed pieces of SQL only. Each value is
// added to $params in the same order as its ? appears in the SQL.
function find_recipes(mysqli $db, string $search = '', ?int $category_id = null): array {
  $conditions = [];
  $params = [];

  if ($category_id !== null) {
    $conditions[] = 'recipes.category_id = ?';
    $params[] = $category_id;
  }

  if ($search !== '') {
    // Escape % and _ so they are searched as normal characters, not wildcards.
    $like = '%' . addcslashes($search, '%_\\') . '%';

    $conditions[] = '(recipes.title LIKE ?
      OR recipes.subtitle LIKE ?
      OR recipes.description LIKE ?
      OR EXISTS (
        SELECT 1 FROM ingredients
        WHERE ingredients.recipe_id = recipes.recipe_id
          AND ingredients.name LIKE ?
      ))';
    array_push($params, $like, $like, $like, $like);
  }

  $sql = 'SELECT recipes.recipe_id, recipes.title, recipes.subtitle,
                 recipes.servings, recipes.main_image,
                 categories.name AS category
          FROM recipes
          INNER JOIN categories ON recipes.category_id = categories.category_id';

  if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
  }

  $sql .= ' ORDER BY recipes.title';

  $result = mysqli_execute_query($db, $sql, $params);
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

// One recipe with its category name, or null if no recipe has that id.
function find_recipe(mysqli $db, int $recipe_id): ?array {
  $sql = 'SELECT recipes.*, categories.name AS category
          FROM recipes
          INNER JOIN categories ON recipes.category_id = categories.category_id
          WHERE recipes.recipe_id = ?';

  $result = mysqli_execute_query($db, $sql, [$recipe_id]);
  return mysqli_fetch_assoc($result);
}

// A recipe's ingredient lines, in the order they are printed.
function recipe_ingredients(mysqli $db, int $recipe_id): array {
  $sql = 'SELECT full_text
          FROM ingredients
          WHERE recipe_id = ?
          ORDER BY sort_order';

  $result = mysqli_execute_query($db, $sql, [$recipe_id]);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// A recipe's cooking steps, in order.
function recipe_steps(mysqli $db, int $recipe_id): array {
  $sql = 'SELECT step_number, step_title, instructions, image, image_retina
          FROM steps
          WHERE recipe_id = ?
          ORDER BY step_number';

  $result = mysqli_execute_query($db, $sql, [$recipe_id]);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// A recipe's tip, or null if it has none (each recipe has at most one).
function recipe_tip(mysqli $db, int $recipe_id): ?array {
  $sql = 'SELECT tip_title, tip_text, image, tip_type
          FROM tips
          WHERE recipe_id = ?';

  $result = mysqli_execute_query($db, $sql, [$recipe_id]);
  return mysqli_fetch_assoc($result);
}

// The kitchen tools a recipe features. recipe_tools links recipes to tools.
function recipe_tools(mysqli $db, int $recipe_id): array {
  $sql = 'SELECT tools.name, tools.brand, tools.description, tools.image
          FROM recipe_tools
          INNER JOIN tools ON recipe_tools.tool_id = tools.tool_id
          WHERE recipe_tools.recipe_id = ?
          ORDER BY tools.name';

  $result = mysqli_execute_query($db, $sql, [$recipe_id]);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
