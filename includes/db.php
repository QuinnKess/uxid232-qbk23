<?php
declare(strict_types=1);

// Opens the database connection used by every page.
// Written with help from Claude (AI). See README, "AI use".

// Exception mode: any database error stops with a mysqli_sql_exception,
// which each page catches and turns into a friendly message.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$config = require __DIR__ . '/../config.php';

$db = mysqli_connect(
  $config['db_host'],
  $config['db_user'],
  $config['db_pass'],
  $config['db_name'],
  $config['db_port']
);

// Read and write text as UTF-8 so characters like ¾ display correctly.
mysqli_set_charset($db, 'utf8mb4');
