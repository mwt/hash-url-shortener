<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

ini_set('display_errors', 0);

if (!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url'])) {
	die('That is not a valid short url');
}

require('config.php');

$shortened_id = getIDFromShortenedURL($_GET['url']);

if (CACHE) {
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if (empty($long_url) || !str_starts_with($long_url, LONGURL_PREFIX)) {
		$stmt = $DB->prepare('SELECT long_url FROM ' . DB_TABLE . ' WHERE id = ?');
		$stmt->bind_param('i', $shortened_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$long_url = $row[0] ?? '';
		$stmt->close();
		@mkdir(CACHE_DIR, 0777);
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
} else {
	$stmt = $DB->prepare('SELECT long_url FROM ' . DB_TABLE . ' WHERE id = ?');
	$stmt->bind_param('i', $shortened_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$long_url = $row[0] ?? '';
	$stmt->close();
}

if (TRACK) {
	$stmt = $DB->prepare('UPDATE ' . DB_TABLE . ' SET referrals=referrals+1 WHERE id = ?');
	$stmt->bind_param('i', $shortened_id);
	$stmt->execute();
	$stmt->close();
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;

function getIDFromShortenedURL($string, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	$size = strlen($string) - 1;
	$string = str_split($string);
	$out = strpos($base, array_pop($string));
	foreach ($string as $i => $char) {
		$out += strpos($base, $char) * pow($length, $size - $i);
	}
	return $out;
}
