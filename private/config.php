<?php
// Uncomment to set Environment = 'prod', else, default = 'dev'
// $environment = 'prod';

// Uncomment to set Host = 'remote', else, default = 'localhost'
// $host = 'remote';

// Set maintenance mode 'on' or 'off'
$maintenance = 'on';

if ($host === 'remote') {
    // Database Credentials @ starboxtech.com
    define("DB_HOST", "localhost");
    define("DB_USER", "starboxt_admin");
    define("DB_PASS", "2detHsX(?tmM=)TU");
    define("DB_NAME", "starboxt_covid_19_ng");
    define("BACKUP_FILE", dirname(__FILE__) . '/backup/covid19ng-' . date('Ymd-His') . '-db.sql.gz');
    define("LOG_FILE",  dirname(__FILE__) . '/log/php_errors.log');
    define("SQL_PATH", 'mysqldump');
    define("ROOT", 'https://covid19.starboxtech.com');
} elseif ($host === 'localhost' || !isset($host) || is_string($host)) {
    // Database Credentials @ Osita's MacBook Pro
    define("DB_HOST", "127.0.0.1");
    define("DB_USER", "admin");
    define("DB_PASS", "root");
    define("DB_NAME", "covid_19_ng");
    define("BACKUP_FILE", dirname(__FILE__) . '/backup/covid19ng-' . date('Ymd-His') . '-db.sql.gz');
    define("LOG_FILE",  dirname(__FILE__) . '/log/php_errors.log');
    define("SQL_PATH", '/Applications/MAMP/Library/bin/mysqldump');
    $public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
    $doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
    define("ROOT", $doc_root);
}

if ($environment === 'dev') {
    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 
    ini_set('display_startup_errors', 1); 
    ini_set('log_errors', true);
    ini_set('error_log', LOG_FILE);
} elseif ($environment === 'prod' || !isset($environment) || is_string($environment)) {
    error_reporting(0); 
    ini_set('display_errors', 0); 
    ini_set('display_startup_errors', 0); 
    ini_set('log_errors', true);
    ini_set('error_log', LOG_FILE);
}

?>