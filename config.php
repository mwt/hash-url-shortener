<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

// db options
define('DB_NAME', 'hash_urlshortener');
define('DB_USER', 'hash_urlshortener');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_TABLE', 'shortenedurls');

// connect to database with mysqli
$DB = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// base location of script (include trailing slash)
define('BASE_HREF', 'https://' . $_SERVER['HTTP_HOST'] . '/');

// change to limit short url creation to a single IP
define('LIMIT_TO_IP', $_SERVER['REMOTE_ADDR']);

// change to TRUE to start tracking referrals
define('TRACK', FALSE);

// check if URL exists first
define('CHECK_URL', FALSE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', TRUE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');

// prefix for shortened URLs (include trailing slash)
define('LONGURL_PREFIX', 'https://hash.mwt.me/');
