<?php
declare(strict_types=1);

// Opens the database connection used by every page.

// Exception mode: any database error stops the page with a clear message.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$config = require __DIR__ . '/config.php';

$db = mysqli_connect(
  $config['db_host'],
  $config['db_user'],
  $config['db_pass'],
  $config['db_name'],
  $config['db_port']
);

// Read and write text as UTF-8 so characters like ¾ display correctly.
mysqli_set_charset($db, 'utf8mb4');
