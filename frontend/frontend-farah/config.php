<?php
/**
 * Global configuration. Loaded at the top of every page.
 * CHANGE THESE TWO LINES to match your team's setup.
 */
define('API_BASE', 'http://localhost:3000/api');   // base URL of the shared RESTful backend
define('BASE_URL', '/frontend-farah');             // folder name inside htdocs (XAMPP)
define('APP_NAME', 'EventHub');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/api.php';
