<? if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */

$path = getinfo('uploads_dir').'iptocountry/';

define('FILENAME', $path.'iptocountry');

define('IP1', 0);
define('IP2', 1);
define('CODE', 4);
define('COUNTRY', 6);

define('COUNTRY_FILE', $path.'countries.tmp');

$handle = fopen(FILENAME.".csv", 'r');
if (!is_resource($handle)) die(t('Невозможно открыть входной файл csv!', __FILE__) );

$hw = fopen(FILENAME.".dat", 'w');
if (!is_resource($hw)) die(t('Невозможно открыть выходной файл dat!', __FILE__) );

$lines = 0;
$country = array();

while (!feof($handle)){
	$buf = fgetcsv($handle);

	if (sizeof($buf) < CODE || $buf[0]{0} == '#') continue;
	if (strlen($buf[CODE]) != 2 || !strcasecmp($buf[CODE], 'ZZ')) continue;

	$ip1 = intval($buf[IP1] - 2147483648);
	$ip2 = intval($buf[IP2] - 2147483648);

	if (isset($data)){
		if ($ip1 < $data[1]){
			if ($ip1 > $data[0])
				if (!array_key_exists($data[2], $country)){
					$country[$data[2]] = $data[3];
				}
				fwrite($hw, pack('VVa2', $data[0], $ip1 - 1, $data[2], $data[3]));
				if (!(++$lines % 100)) print ".";

			if ($ip2 >= $data[1])
				$data = array($ip1, $ip2, $buf[CODE], $buf[COUNTRY]);
			else{
				if (!array_key_exists($data[2], $country)){
					$country[$data[2]] = $data[3];
				}
				fwrite($hw, pack('VVa2', $ip1, $ip2, $buf[CODE], $buf[COUNTRY] ));
				if (!(++$lines % 100)) print ".";
				$data[0] = $ip2 + 1;
			}
			continue;
		}
		if ($data[2] == $buf[CODE] && ($data[1] + 1) == $ip1){
			$data[1] = $ip2;
			continue;
		}
		if (!array_key_exists($data[2], $country)){
			$country[$data[2]] = $data[3];
		}
		fwrite($hw, pack('VVa2', $data[0], $data[1], $data[2]));
		if (!(++$lines % 100));
	}
	$data = array($ip1, $ip2, $buf[CODE], $buf[COUNTRY]);
}
if (isset($data)){	if (!array_key_exists($data[2], $country)){
		$country[$data[2]] = $data[3];
	}
	fwrite($hw, pack('VVa2', $data[0], $data[1], $data[2]));
}
fclose($handle);
fclose($hw);

print $lines.t(' позиций было преобразовано (без локальных и резервных IP).', __FILE__);

$hws = fopen(COUNTRY_FILE, 'w');

asort($country);
foreach($country as $code => $title)
	fprintf($hws, "  '%s'=>\"%s\",\n", $code, $title);

fclose($hws);

///////////////////////////////////////////////////////////
function write($data){
	global $hw, $lines, $country;

	if (!array_key_exists($data[2], $country)){
		$country[$data[2]] = $data[3];
	}
	fwrite($hw, pack('VVa2', $data[0], $data[1], $data[2]));

}
?>