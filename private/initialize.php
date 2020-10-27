<?php
// Timezone
date_default_timezone_set('Africa/Lagos');

// Turn on sessions
session_start();

// Database connection
require_once('db_conn.php');
$db = db_connect();

// Set default page title
$page_title_default = 'Coronavirus (COVID-19) Cases in Nigeria';

// File path constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . '/public');
define("SHARED_PATH", PRIVATE_PATH . '/shared');
define("FUNCTIONS_PATH", PRIVATE_PATH . '/functions');
define("ADMIN_PATH", PUBLIC_PATH . '/admin');
define("DIST_PATH", PUBLIC_PATH . '/dist');
define("IMG_PATH", PUBLIC_PATH . '/dist/img');

// URL constants
define("DIST_ROOT", ROOT . '/dist');
define("IMG_ROOT", ROOT . '/dist/img');
define("ADMIN_ROOT", ROOT . '/admin');
define("ADMIN_LOGIN", ROOT . '/admin/auth.php');
define("ADMIN_LOGOUT", ROOT . '/admin/logout.php');
define("ADMIN_UPDATE", ROOT . '/admin/update.php');
define("MAINTENANCE_PAGE", ROOT . '/maintenance.php');
define("STARBOX", 'https://starboxtech.com/');

// Require functions
require_once('functions.php');