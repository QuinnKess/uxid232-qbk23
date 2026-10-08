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

  <title>Recipe Submission</title>

  <link rel="stylesheet" href="style.css">
</head>

<body>

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

  <label for="name">Recipe name:</label>
  <input
    type="text"
    id="name"
    name="name"
    value="<?= e($name) ?>"
  >

  <br>

  <label for="email">Email:</label>
  <input
    type="email"
    id="email"
    name="email"
    value="<?= e($email) ?>"
  >

  <br>

  <input type="submit" value="Submit">

</form>

</body>
</html>