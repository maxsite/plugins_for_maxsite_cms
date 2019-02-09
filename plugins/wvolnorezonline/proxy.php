<?php
error_reporting(0);
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	|| $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'
	|| !count($_POST) || !isset($_POST['ajax_url']))
		die("0|Неверный запрос");
$url_info = parse_url($_POST['ajax_url']);
unset($_POST['ajax_url']);

$data = array();
foreach ($_POST as $k => $v) $data[] = $k.'='.$v;
$data = implode('&', $data);


if (!$fp = fsockopen($url_info['host'], 80))
{
	return false;
}

fwrite($fp, "POST " . @$url_info['path'] . " HTTP/1.0\r\n");
fwrite($fp, "Host: " . @$url_info['host'] . "\r\n");
fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
fwrite($fp, "Content-Length: " . strlen($data) . "\r\n");
fwrite($fp, "Connection: Close\r\n\r\n");
fwrite($fp, $data);
$in = '';
while (($line = fgets($fp, 8192))!==false) $in .= $line;
fclose($fp);

$out = substr($in, strpos($in, "\r\n\r\n") + 4);//(strrpos($in, "\r\n\r\n") - 3)-(strpos($in, "\r\n\r\n") + 8) );
print $out;
?>