<?php
declare(strict_types=1);

// Light / dark mode.
// Written with help from Claude (AI). See README, "AI use".
// The choice comes from the ?theme= link or a saved cookie. Both come from
// the user, so only the two allowed values are accepted.

$allowed_themes = ['dark', 'light'];

$requested_theme = $_GET['theme'] ?? $_COOKIE['theme'] ?? 'dark';

if (is_string($requested_theme) && in_array($requested_theme, $allowed_themes, true)) {
  $theme = $requested_theme;
} else {
  $theme = 'dark';
}

// Remember a new choice for a year, on every page of the site.
if (isset($_GET['theme'])) {
  setcookie('theme', $theme, [
    'expires' => time() + 60 * 60 * 24 * 365,
    'path' => '/',
    'samesite' => 'Lax',
  ]);
}

$other_theme = $theme === 'dark' ? 'light' : 'dark';
