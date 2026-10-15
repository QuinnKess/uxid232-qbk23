<?php
declare(strict_types=1);

function dump(mixed $value): void {
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function post_value(string $key): string {
  $value = $_POST[$key] ?? '';
  return is_string($value) ? trim($value) : '';
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$name = '';
$email = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = post_value('name');
  $email = post_value('email');

  if ($name === '') {
    $errors[] = 'Recipe name is required.';
  } elseif (mb_strlen($name) > 100) {
    $errors[] = 'Recipe name must be 100 characters or fewer.';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
  }

  if (empty($errors)) {
    $success = true;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Recipe Submission | IDM 232</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;700&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">
  <span>IDM 232</span>
  <span>Quinn Kessler</span>
</header>

<main>

  <p class="eyebrow">[ Assignment 2 ]</p>
  <h1>Forms and <em>User Input</em></h1>

  <?php if (!empty($errors)): ?>
    <ul class="errors">
      <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if ($success): ?>
    <p class="success">Thanks, <?= e($name) ?> was submitted.</p>
  <?php endif; ?>

  <form method="POST">

    <label for="name">Recipe name</label>
    <input
      type="text"
      id="name"
      name="name"
      value="<?= e($name) ?>"
    >

    <label for="email">Email</label>
    <input
      type="email"
      id="email"
      name="email"
      value="<?= e($email) ?>"
    >

    <input type="submit" value="Submit">

  </form>

</main>

<footer class="site-footer">
  <span>Assignment 2 — Forms and User Input</span>
  <span>Drexel University</span>
</footer>

</body>
</html>
