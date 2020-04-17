<?php

// Options PROD, DEV
define("STATE", "DEV");
// Set true if you want to test the session handler
define("CHECK_SESSION", false);
// Options: openssl, sodium, ...
define("CRYPTO_ALGO", "sodium");

// 
define("CRYPTO_BASE_PATH", "\\AngelosKanatsos\\XM\PHP\\Core\\Crypto\\");
define("SESSION_NAME", "XM_SESSION_ID");
define("SERVER_PATH", "http://" . $_SERVER['SERVER_NAME'] . "/AngelosKanatsos/XM/PHP/");
define("COMPANIES_URL", "https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json");
define("COMPANIES_HISTORICAL_QUOTES_BASE_URL", "https://www.quandl.com/api/v3/datasets/WIKI/");
define("LOCAL_SERVICES_URL", SERVER_PATH . 'services/');

// Global error array
$GLOBALS['messages'] = [];

// Session cookie hardening
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// ini_set('session.use_strict_mode', 1);
if (strcmp(STATE, "PROD") == 0) {
    ini_set('session.cookie_secure', 1);    
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
