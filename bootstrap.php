<?php

declare(strict_types=1);

$config = require __DIR__ . '/config/config.php';
$GLOBALS['APP_CONFIG'] = $config;

require __DIR__ . '/config/db.php';
require __DIR__ . '/helpers/url.php';
require __DIR__ . '/helpers/flash.php';
require __DIR__ . '/helpers/upload.php';
require __DIR__ . '/helpers/auth.php';
