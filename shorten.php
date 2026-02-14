<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

ini_set('display_errors', 0);

require('config.php');

// add access control header for ajax requests
header('Access-Control-Allow-Origin: *');

// if request type is post, take longurl from post, 
// otherwise get from longurl query parameter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['longurl'])) {
	$url_to_shorten = LONGURL_PREFIX . trim($_POST['longurl']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['longurl'])) {
	$url_to_shorten = LONGURL_PREFIX . trim($_GET['longurl']);
} else {
	die("No URL provided!");
}

$url_to_shorten = LONGURL_PREFIX . trim($_REQUEST['longurl']);

// check if the client IP is allowed to shorten
if ($_SERVER['REMOTE_ADDR'] != LIMIT_TO_IP) {
	die('You are not allowed to shorten URLs with this service.');
}

// check if the URL is valid
if (CHECK_URL) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url_to_shorten);
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);
	$response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($response_status == '404') {
		die('Not a valid URL');
	}
}

// check if the URL has already been shortened
$stmt = $DB->prepare('SELECT id FROM ' . DB_TABLE . ' WHERE long_url = ?');
$stmt->bind_param('s', $url_to_shorten);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$already_shortened = $row[0] ?? null;
$stmt->close();

if (!empty($already_shortened)) {
	// URL has already been shortened
	$shortened_url = getShortenedURLFromID($already_shortened);
} else {
	// URL not in database, insert
	$DB->query('LOCK TABLES ' . DB_TABLE . ' WRITE;');
	$stmt = $DB->prepare('INSERT INTO ' . DB_TABLE . ' (long_url, created, creator) VALUES (?, ?, ?)');
	$created_time = time();
	$creator_ip = $_SERVER['REMOTE_ADDR'];
	$stmt->bind_param('sis', $url_to_shorten, $created_time, $creator_ip);
	$stmt->execute();
	$shortened_url = getShortenedURLFromID($DB->insert_id);
	$stmt->close();
	$DB->query('UNLOCK TABLES');
}
echo BASE_HREF . $shortened_url;

function getShortenedURLFromID($integer, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	$out = '';
	while ($integer > $length - 1) {
		$out = $base[fmod($integer, $length)] . $out;
		$integer = floor($integer / $length);
	}
	return $base[$integer] . $out;
}
