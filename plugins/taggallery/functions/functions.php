<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

# преобразование даты
function taggallery_date($date = 0, $format = 'Y-m-d H:i:s', $do = '', $posle = '', $echo = true)
{
	if (!$date) return '';

	if (is_array($format)) // формат в массиве, значит там и замены
	{
		if (isset($format['format'])) $df = $format['format'];
			else $df = 'D, j F Y г.';

		if (isset($format['days'])) $dd = $format['days'];
			else $dd = t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье');

		if (isset($format['month'])) $dm = $format['month'];
			else $dm = t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря');
	}
	else
	{
		$df = $format;
		$dd = false;
		$dm = false;
	}

	// учитываем смещение времени time_zone
	$out = mso_date_convert($df, $date, true, $dd, $dm);

	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

 


?>