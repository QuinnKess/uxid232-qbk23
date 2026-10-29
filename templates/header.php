<?php
declare(strict_types=1);

// Page top: <head>, site header and navigation. Opens <main>.
// Written with help from Claude (AI). See README, "AI use".
//
// Each page sets these before including this file:
//   $page_title   text for the browser tab
//   $current_page 'form' or 'recipes', to highlight the nav link
//   $page_params  the page's own query values, kept when switching theme
//   $main_class   'wide' for the grid pages, '' for narrow pages
// $theme and $other_theme come from includes/theme.php.

$page_params = $page_params ?? [];
$main_class = $main_class ?? '';

// Add the stylesheet's last-saved time so browsers load a fresh copy after edits.
$css_version = (string) filemtime(dirname($_SERVER['SCRIPT_FILENAME']) . '/style.css');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= e($page_title) ?> | IDM 232</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;700&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css?v=<?= e($css_version) ?>">
</head>

<body class="theme-<?= e($theme) ?>">

<header class="site-header">
  <span>IDM 232 &middot; Quinn Kessler</span>
  <nav class="site-nav">
    <a href="index.php"<?= $current_page === 'form' ? ' aria-current="page"' : '' ?>>Form</a>
    <a href="recipes.php"<?= $current_page === 'recipes' ? ' aria-current="page"' : '' ?>>Recipes</a>
    <a href="<?= e(page_link('', $page_params + ['theme' => $other_theme])) ?>"><?= $theme === 'dark' ? 'Light mode' : 'Dark mode' ?></a>
  </nav>
</header>

<main<?= $main_class !== '' ? ' class="' . e($main_class) . '"' : '' ?>>
