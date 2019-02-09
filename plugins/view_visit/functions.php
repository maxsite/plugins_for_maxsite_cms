<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */

#Функции преобразования даты
function my_date_small($d)
  // Форматирование даты
{
  return date("j ",$d).date("m",$d).date(" Y - H:i:s",$d);
}

function return_sec_day($str){
	$arr = explode(':', $str);
	$sec = $arr[2];
	$sec = $sec+$arr[1]*60;
	$sec = $sec+$arr[0]*3600;
	return $sec;
}

function return_sec_mounth($str){
	$sec = ($str-1)*24*60*60;
	return $sec;
}

/////// функция организации пагинации///////////////////////
/// В качестве параметров функция принимает четыре параметра
///$query - запрос к БД
///$num_page - Номер вызываемой страницы пагинации
///$href - ссылка по которой проводится пагинация
///$col_str - Количество выводящихся сообщений
function pagination($query = '', $num_page = '', $href = '', $col_str){
	$CI = & get_instance();
	$post = null;
	if(is_object($query)) $cols = $query->num_rows();
	if(is_array($query)) $cols = count($query);
	if($CI->uri->segment(6)) $post = $CI->uri->segment(6);
	if (@intval($cols/$col_str) == @round($cols/$col_str, 3)){
		$pages = @intval($cols/$col_str);
	}else{
		$pages = @intval($cols/$col_str)+1;
	}
	$str_nums = '';
	if ($num_page != ''){
		$limit_num = $col_str*$num_page;
	}else{
		$limit_num = 0;
	}
	for ($i=0; $i<$pages; $i++) {
	$j=$i+1;
		if ($num_page != $i or $num_page == ''){
			if ($num_page == '' and $i == 0){
				$str_nums.=" <b>1/$pages</b> ";
			}else{
				if ($CI->uri->segment(5) != '') {
					$str_nums.="<a class = 'menu' href=$href/$i/".$CI->uri->segment(5).'/'.$post.">$j </a>";
				}else{					$str_nums.="<a class = 'menu' href=$href/$i/".$post.">$j </a>";				}
			}
		}else{
			$str_nums.=" <b>$j/$pages</b> ";								/////////////////
		}																	/////////////////
	}																		/////////////////
	$str_nums = "<div class='right'>$str_nums</div>";						/////////////////
	return $str_nums;   													/////////////////
}																			/////////////////
/////////////////////////////////////////////////////////////////////////////////////////////

#Функция возвращает массив с хостами
function array_hosts($array_period){	$CI = & get_instance();
	$col_all_hosts = array();
	$CI->db->order_by('time', 'desc');
	$query = $CI->db->get_where("view_visit", $array_period);
	foreach ($query->result() as $row)
	{
		$new_ip = $row->ip;
		if(!in_array($new_ip, $col_all_hosts)) $col_all_hosts[] = $new_ip;
	}
	return $col_all_hosts;}

#Функция возвращает количество хитов для конкретного хоста
function hits_in_host($host){	$CI = & get_instance();
	if (!is_numeric($host)) $host = ip2long($host);
	$query = $CI->db->get_where("view_visit", array('ip'=>$host));
	$col_all_hits = $query->num_rows();
	return $col_all_hits;}


#Функция подсчета хитов и хостов блога
function hosts_hits(){
	$CI = & get_instance();
	$col_day_hosts = array();
	$col_all_hosts = array();
	$sec_day = return_sec_day(date("G:i:s"));
	$day = time()-$sec_day;
	$query = $CI->db->get_where("view_visit", array('time >'=>$day));
	$col_day_hits = $query->num_rows();
	foreach ($query->result() as $row)
	{
		$new_ip = $row->ip;
		if(!in_array($new_ip, $col_day_hosts)) $col_day_hosts[] = $new_ip;
	}
	$text = t('Хитов за сутки: ', __FILE__).'<b>'.$col_day_hits.'</b><br>';
	$text.= t('Хостов за сутки: ', __FILE__).'<b>'.count($col_day_hosts).'</b><br>';
	$query = $CI->db->get_where("view_visit");
	$col_all_hits = $query->num_rows();
	foreach ($query->result() as $row)
	{
		$new_ip = $row->ip;
		if(!in_array($new_ip, $col_all_hosts)) $col_all_hosts[] = $new_ip;
	}
	$all_hits_arhive = 0;
	$all_hosts_arhive = 0;
	$query = $CI->db->get('view_visit_base');
	if ($query->num_rows() > 0){		$arhives = get_arhive();
		foreach($query->result() as $row) {
			$all_hits_arhive = $all_hits_arhive+$arhives[$row->date]['hits'];
			$all_hosts_arhive = $all_hosts_arhive+$arhives[$row->date]['hosts'];
		}
	}
	$all_hits = $col_all_hits+$all_hits_arhive;
	$all_hosts = count($col_all_hosts)+$all_hosts_arhive;
	$text.= t('Хитов за весь период: ', __FILE__).'<b>'.$all_hits.'</b><br>';
	$text.= t('Хостов за весь период: ', __FILE__).'<b>'.$all_hosts.'</b><br>';
	return $text;
}

//Функция определения языка пользователя.
function get_languages( $feature, $spare='' ){
	$a_languages = languages();
	$index = '';
	$complete = '';
	$found = false;
	$user_languages = array();

	if ( isset( $_SERVER["HTTP_ACCEPT_LANGUAGE"] ) ){
		$languages = strtolower( $_SERVER["HTTP_ACCEPT_LANGUAGE"] );
		$languages = str_replace( ' ', '', $languages );
		$languages = explode( ",", $languages );

		foreach ( $languages as $language_list ){
			$temp_array = array();
			$temp_array[0] = substr( $language_list, 0, strcspn( $language_list, ';' ) );
			$temp_array[1] = substr( $language_list, 0, 2 );
			$user_languages[] = $temp_array;
		}

		for ( $i = 0; $i < count( $user_languages ); $i++ ){
			foreach ( $a_languages as $index => $complete )
			{
				if ( $index == $user_languages[$i][0] )
				{
					$user_languages[$i][2] = $complete;
					$user_languages[$i][3] = substr( $complete, 0, strcspn( $complete, ' (' ) );
				}
			}
		}
	}else{
		$user_languages[0] = array( '','','','' );
	}

	if ( $feature == 'data' ){
		return $user_languages;
	}elseif ( $feature == 'header' ){
		switch ( $user_languages[0][1] ){
			case 'en':
				//$location = 'english.php';
				$found = true;
				break;
			case 'sp':
				//$location = 'spanish.php';
				$found = true;
				break;
			default:
				break;
		}
	}
}

//Преобразование двойного кода страны в ее название
function languages()
{
	$a_languages = array(
	'af' => 'Afrikaans',
	'sq' => 'Albanian',
	'ar-dz' => 'Arabic (Algeria)',
	'ar-bh' => 'Arabic (Bahrain)',
	'ar-eg' => 'Arabic (Egypt)',
	'ar-iq' => 'Arabic (Iraq)',
	'ar-jo' => 'Arabic (Jordan)',
	'ar-kw' => 'Arabic (Kuwait)',
	'ar-lb' => 'Arabic (Lebanon)',
	'ar-ly' => 'Arabic (libya)',
	'ar-ma' => 'Arabic (Morocco)',
	'ar-om' => 'Arabic (Oman)',
	'ar-qa' => 'Arabic (Qatar)',
	'ar-sa' => 'Arabic (Saudi Arabia)',
	'ar-sy' => 'Arabic (Syria)',
	'ar-tn' => 'Arabic (Tunisia)',
	'ar-ae' => 'Arabic (U.A.E.)',
	'ar-ye' => 'Arabic (Yemen)',
	'ar' => 'Arabic',
	'hy' => 'Armenian',
	'as' => 'Assamese',
	'az' => 'Azeri',
	'eu' => 'Basque',
	'be' => 'Belarusian',
	'bn' => 'Bengali',
	'bg' => 'Bulgarian',
	'ca' => 'Catalan',
	'zh-cn' => 'Chinese (China)',
	'zh-hk' => 'Chinese (Hong Kong SAR)',
	'zh-mo' => 'Chinese (Macau SAR)',
	'zh-sg' => 'Chinese (Singapore)',
	'zh-tw' => 'Chinese (Taiwan)',
	'zh' => 'Chinese',
	'hr' => 'Croatian',
	'cs' => 'Czech',
	'da' => 'Danish',
	'div' => 'Divehi',
	'nl-be' => 'Dutch (Belgium)',
	'nl' => 'Dutch (Netherlands)',
	'en-au' => 'English (Australia)',
	'en-bz' => 'English (Belize)',
	'en-ca' => 'English (Canada)',
	'en-ie' => 'English (Ireland)',
	'en-jm' => 'English (Jamaica)',
	'en-nz' => 'English (New Zealand)',
	'en-ph' => 'English (Philippines)',
	'en-za' => 'English (South Africa)',
	'en-tt' => 'English (Trinidad)',
	'en-gb' => 'English (United Kingdom)',
	'en-us' => 'English (United States)',
	'en-zw' => 'English (Zimbabwe)',
	'en' => 'English',
	'us' => 'English (United States)',
	'et' => 'Estonian',
	'fo' => 'Faeroese',
	'fa' => 'Farsi',
	'fi' => 'Finnish',
	'fr-be' => 'French (Belgium)',
	'fr-ca' => 'French (Canada)',
	'fr-lu' => 'French (Luxembourg)',
	'fr-mc' => 'French (Monaco)',
	'fr-ch' => 'French (Switzerland)',
	'fr' => 'French (France)',
	'mk' => 'FYRO Macedonian',
	'gd' => 'Gaelic',
	'ka' => 'Georgian',
	'de-at' => 'German (Austria)',
	'de-li' => 'German (Liechtenstein)',
	'de-lu' => 'German (Luxembourg)',
	'de-ch' => 'German (Switzerland)',
	'de' => 'German (Germany)',
	'el' => 'Greek',
	'gu' => 'Gujarati',
	'he' => 'Hebrew',
	'hi' => 'Hindi',
	'hu' => 'Hungarian',
	'is' => 'Icelandic',
	'id' => 'Indonesian',
	'it-ch' => 'Italian (Switzerland)',
	'it' => 'Italian (Italy)',
	'ja' => 'Japanese',
	'kn' => 'Kannada',
	'kk' => 'Kazakh',
	'kok' => 'Konkani',
	'ko' => 'Korean',
	'kz' => 'Kyrgyz',
	'lv' => 'Latvian',
	'lt' => 'Lithuanian',
	'ms' => 'Malay',
	'ml' => 'Malayalam',
	'mt' => 'Maltese',
	'mr' => 'Marathi',
	'mn' => 'Mongolian (Cyrillic)',
	'ne' => 'Nepali (India)',
	'nb-no' => 'Norwegian (Bokmal)',
	'nn-no' => 'Norwegian (Nynorsk)',
	'no' => 'Norwegian (Bokmal)',
	'or' => 'Oriya',
	'pl' => 'Polish',
	'pt-br' => 'Portuguese (Brazil)',
	'pt' => 'Portuguese (Portugal)',
	'pa' => 'Punjabi',
	'rm' => 'Rhaeto-Romanic',
	'ro-md' => 'Romanian (Moldova)',
	'ro' => 'Romanian',
	'ru-md' => 'Russian (Moldova)',
	'ru' => 'Russian',
	'sa' => 'Sanskrit',
	'sr' => 'Serbian',
	'sk' => 'Slovak',
	'ls' => 'Slovenian',
	'sb' => 'Sorbian',
	'es-ar' => 'Spanish (Argentina)',
	'es-bo' => 'Spanish (Bolivia)',
	'es-cl' => 'Spanish (Chile)',
	'es-co' => 'Spanish (Colombia)',
	'es-cr' => 'Spanish (Costa Rica)',
	'es-do' => 'Spanish (Dominican Republic)',
	'es-ec' => 'Spanish (Ecuador)',
	'es-sv' => 'Spanish (El Salvador)',
	'es-gt' => 'Spanish (Guatemala)',
	'es-hn' => 'Spanish (Honduras)',
	'es-mx' => 'Spanish (Mexico)',
	'es-ni' => 'Spanish (Nicaragua)',
	'es-pa' => 'Spanish (Panama)',
	'es-py' => 'Spanish (Paraguay)',
	'es-pe' => 'Spanish (Peru)',
	'es-pr' => 'Spanish (Puerto Rico)',
	'es-us' => 'Spanish (United States)',
	'es-uy' => 'Spanish (Uruguay)',
	'es-ve' => 'Spanish (Venezuela)',
	'es' => 'Spanish (Traditional Sort)',
	'sx' => 'Sutu',
	'sw' => 'Swahili',
	'sv-fi' => 'Swedish (Finland)',
	'sv' => 'Swedish',
	'syr' => 'Syriac',
	'ta' => 'Tamil',
	'tt' => 'Tatar',
	'te' => 'Telugu',
	'th' => 'Thai',
	'ts' => 'Tsonga',
	'tn' => 'Tswana',
	'tr' => 'Turkish',
	'uk' => 'Ukrainian',
	'ur' => 'Urdu',
	'uz' => 'Uzbek',
	'vi' => 'Vietnamese',
	'xh' => 'Xhosa',
	'yi' => 'Yiddish',
	'zu' => 'Zulu' );

	return $a_languages;
}

# добавление в таблицу архива view_visit_base
function add_arhive($value){
	# если value массив или объект, то серилизуем его в строку
	if ( !is_scalar($value) ) $value = '_serialize_' . serialize($value);

	$CI = & get_instance();

	$data['data'] = $value;
	$CI->db->insert('view_visit_base', $data);

	return true;
}

//Выборка данных из архива
function get_arhive($date = ''){	$CI = & get_instance();

	if ($date == ''){		$CI->db->order_by('date', 'desc');
		$query = $CI->db->get('view_visit_base');	}
	if ($query->num_rows >0){
		foreach($query->result() as $row){			if (@preg_match( '|_serialize_|A', $row->data)){
				$result_ser = preg_replace( '|_serialize_|A', '', $row->data, 1 );
				$result[$row->date] = @unserialize($result_ser);
			}		}
	}
	return $result;}

//Массив браузеров
function array_browser(){	$CI = & get_instance();	$CI->db->select('browser_small');
	$CI->db->order_by('browser_small', 'desc');
	$query = $CI->db->get_where("view_visit");
	if($query->num_rows() > 0){
		foreach ($query->result() as $row){
			$browser = ereg_replace('[0-9.]', '', $row->browser_small);
			$browser = trim($browser);
			@$arr[$browser]++;
		}
	}
	return $arr;}

//массив платформ
function array_platform(){	$CI = & get_instance();
	$arr = array();
	$CI->db->select('platform');
	$CI->db->order_by('platform', 'desc');
	$query = $CI->db->get_where("view_visit");
	if($query->num_rows() > 0){
		foreach ($query->result() as $row){
			//$browser = ereg_replace('[0-9.]', '', $row->browser_small);
			$platform = trim($row->platform);
			@$arr[$platform]++;
		}
	}
	return $arr;}

//Массив разрешений
function array_resolution(){	$CI = & get_instance();
	$arr = array();
	$hosts = array_hosts(null);
	foreach($hosts as $host){
		$CI->db->select('resolution');
		$CI->db->order_by('time', 'desc');
		$query = $CI->db->get_where("view_visit", array('ip'=>$host), 1);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$resol = trim($row->resolution);
				if ($resol != ''){
					@$arr[$resol]++;
				}
			}
		}
	}
	return $arr;}

//Массив языков
function array_language(){	$CI = & get_instance();
	$arr = array();
	$hosts = array_hosts(null);
	foreach($hosts as $host){
		$CI->db->select('lang');
		$CI->db->order_by('time', 'desc');
		$query = $CI->db->get_where("view_visit", array('ip'=>$host), 1);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$lang = trim($row->lang);
				if (($lang != '0') and ($lang != '')){
					@$arr[$lang]++;
				}
			}
		}
	}
	return $arr;}

//Массив стран
function array_country(){	$CI = & get_instance();	$arr = array();
	$hosts = array_hosts(null);
	foreach($hosts as $host){
		$CI->db->select('country');
		$CI->db->order_by('time', 'desc');
		$query = $CI->db->get_where("view_visit", array('ip'=>$host), 1);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$country = trim($row->country);
				if ($country != ''){
					@$arr[$country]++;
				}
			}
		}
	}
	return $arr;}

########################################################################
###                 Auhtor: MAX, max-3000@list.ru                     ###
###                      http://maxsite.org/                         ###
########################################################################

//Набор функций проверки кодировки и перекодировки

function maxsite_detect_utf($Str) {
	for ($i=0; $i<strlen($Str); $i++) {
		if (ord($Str[$i]) < 0x80) $n=0;
		elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1;
		elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2;
		elseif ((ord($Str[$i]) & 0xF0) == 0xF0) $n=3;
		else return false;
	for ($j=0; $j<$n; $j++) {
		if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80)) return false;
		}
	}
	return true;
}

function maxsite_conv_in($text) {
	if ( function_exists('mb_convert_encoding') )
		$text= mb_convert_encoding($text, 'WINDOWS-1251', 'UTF-8');
		else
		{
		if (function_exists('iconv'))
			$text = iconv('UTF-8', 'WINDOWS-1251', $text );
		}
	return $text;
}

function maxsite_conv_out($text) {
	if ( maxsite_detect_utf($text) ) return $text;
	if ( function_exists('mb_convert_encoding') )
		$text= mb_convert_encoding($text, 'UTF-8', 'WINDOWS-1251');
		else
		{
		if (function_exists('iconv'))
			$text = iconv('WINDOWS-1251', 'UTF-8', $text );
		}
	return $text;
}
#############################################################################

//Функция удаляет папку со всем вложеным содержимим
function removedir($directory) {	//Открываем папку-жертву
	$dir = opendir($directory);
	//считываем ее содержимое и понеслась..
	while(($file = readdir($dir))){		//если это файл - удаляем
		if ( is_file ($directory."/".$file)){
			unlink ($directory."/".$file);
			//если это папка - функция вызывает сама себя и все сначала
		}else if( is_dir ($directory."/".$file) && ($file != ".") && ($file != "..")){			//самовызов
			removedir ($directory."/".$file);
		}
	}
	//закрываем папку-жертву
	closedir ($dir);
	//удаляем папку-жертву
	rmdir ($directory);
	//рапорт об успешной очистке
	return TRUE;
}
?>