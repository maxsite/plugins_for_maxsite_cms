<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
?>
<h1><?= t('Посещения', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет просматривать посещения сайта.', __FILE__) ?></p>
<?
echo mso_load_jquery();
echo mso_load_jquery('jquery.tablesorter.js');
echo '<script type="text/javascript">
            $(function() {
               $("table.tablesorter th").animate({opacity: 0.7});
               $("table.tablesorter th").hover(function(){ $(this).animate({opacity: 1}); }, function(){ $(this).animate({opacity: 0.7}); });
               $("#visits").tablesorter();
            });
            </script>';

echo '<table class="page tablesorter" border="0" width="99%" id="visits">';
echo '<thead><tr>
		<th style="cursor: pointer;">'.t('Номер', __FILE__).'</th>
		<th style="cursor: pointer;">Ip адрес</th>
		<th style="cursor: pointer;">'.t('Платформа', __FILE__).'</th>
		<th style="cursor: pointer;">'.t('Браузер', __FILE__).'</th>
		<th style="cursor: pointer;">'.t('Реферер', __FILE__).'</th>
		<th style="cursor: pointer;">'.t('Ссылка', __FILE__).'</th>
		<th style="cursor: pointer;">'.t('Время', __FILE__).'</th>
		<th style="cursor: pointer;">'.t('Контроль', __FILE__).'</th>
	   </tr></thead>';

//Начало выбора периода просмотра///////////////////////////////////////////////////////////
echo '<a href="'.getinfo('site_admin_url').'plugin_view_visit/list/0/day">'.t('За сутки', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/list/0/mounth">'.t('За месяц', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/list/0/all">'.t('За весь период', __FILE__).'</a><br>';
$sec_day = return_sec_day(date("G:i:s"));
$day = time()-$sec_day;
$mounth = time()-return_sec_mounth(date("j"))-$sec_day;

//echo my_date_small($day);
$array_period = null;
if (mso_segment(5) == 'day') $array_period = array('time >'=>$day);
if (mso_segment(5) == 'mounth') $array_period = array('time >'=>$mounth);
////////////////////////////////////////////////////////////////////////////////////////////

//Начало пагинации//////////////////////////////////////////////////////Для выполнения//////
$query = $CI->db->get_where("view_visit", $array_period); 			////////////////////////
if ($CI->uri->segment(3) == 'list'){								///////пагинации////////
	$num_str = $CI->uri->segment(4);								//используется функция//
	$limit = $num_str*$col_str;										////////////////////////
}else{																///////pagination///////	$num_str = 0;													////////////////////////
	$limit = 0;														////////////////////////}																	////////////////////////
echo pagination($query, $num_str, getinfo('site_admin_url').'plugin_view_visit/list', $col_str);/////
////////////////////////////////////////////////////////////////////////////////////////////

$CI->db->order_by('time', 'desc');
$CI->db->limit($col_str, $limit);
$query = $CI->db->get_where("view_visit", $array_period);

echo hosts_hits();

if($query->num_rows() > 0){
	foreach ($query->result() as $row)
	{
		$num = '';
		$ip = '';
		$referer = '';
		$link = '';
		$browser = '';
		$platform = '';
		$browser_small = '';
		$time = '';
		if (isset($row->num)) $num = $row->num;
		if (isset($row->ip)) $ip = long2ip($row->ip);
		if ((!isset($options['link_to_link'])) or ($row->referer == '0' )){
			$referer = urldecode($row->referer);
		}else{			$referer = '<a href="'.urldecode($row->referer).'">'.urldecode($row->referer).'</a>';		}
		if ((!isset($options['link_to_link'])) or ($row->link == '0' )){
			$link = urldecode($row->link);
		}else{
			$link = '<a href="'.urldecode($row->link).'">'.urldecode($row->link).'</a>';
		}
		if (isset($row->browser)) $browser = $row->browser;
		if (isset($row->platform)) $platform = $row->platform;
		if (isset($row->browser_small)) $browser_small = $row->browser_small;
		if (isset($row->time)) $time = $row->time;
		$country = '';
		if (isset($row->country) and $row->country!='') $country = '('.$row->country.')';

		echo '<tr>
				<td>'.$num.'</td>
				<td title="'.t('Отследить переходы с ', __FILE__).$ip.'"><a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/day/'.$ip.'">'.$ip.$country.'</a></td>
				<td>'.$platform.'</td>
				<td title="'.$browser.'">'.$browser_small.'</td>
				<td>'.$referer.'</td>
				<td>'.$link.'</td>
				<td>'.my_date_small($time).'</td>
				<td>&nbsp;</td>
			  </tr>';
	}
}
echo '</table>';
?>