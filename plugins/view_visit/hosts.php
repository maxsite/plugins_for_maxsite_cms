<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
?>
<h1><?= t('Сводная таблица по уникальным посетителям', __FILE__) ?></h1>
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
if (preg_match("/([1-2]?[0-9]?[0-9])\.([1-2]?[0-9]?[0-9])\.([1-2]?[0-9]?[0-9])\.([1-2]?[0-9]?[0-9])/",$CI->uri->segment(6))){	echo '<strong>'.t('Переходы с хоста ', __FILE__).mso_segment(6).'</strong><br>';

	echo '<p><a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/day/'.mso_segment(6).'">'.t('За сутки', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/mounth/'.mso_segment(6).'">'.t('За месяц', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/all/'.mso_segment(6).'">'.t('За все время', __FILE__).'</a>';

	$host = ip2long(mso_segment(6))."<br>";

	$sec_day = return_sec_day(date("G:i:s"));
	$day = time()-$sec_day;
	$mounth = time()-return_sec_mounth(date("j"))-$sec_day;

	$array_period = null;
	if (mso_segment(5) == 'day') $array_period = array('time >'=>$day, 'ip'=>$host);
	if (mso_segment(5) == 'mounth') $array_period = array('time >'=>$mounth, 'ip'=>$host);
	if (mso_segment(5) == 'all') $array_period = array('ip'=>$host);

	//Начало пагинации//////////////////////////////////////////////////////Для выполнения//////
	$CI->db->order_by('time', 'desc');
	$query = $CI->db->get_where("view_visit", $array_period); 			////////////////////////
	if (is_numeric(mso_segment(4))){								///////пагинации////////
		$num_str = mso_segment(4);								//используется функция//
		$limit = $num_str*$col_str;										////////////////////////
	}else{																///////pagination///////
		$num_str = 0;													////////////////////////
		$limit = 0;														////////////////////////
	}																	////////////////////////
	echo pagination($query, $num_str, getinfo('site_admin_url').'plugin_view_visit/hosts/', $col_str);/////
	////////////////////////////////////////////////////////////////////////////////////////////

	echo '<table class="page tablesorter" border="0" width="99%" id="visits">';
	echo '<thead><tr>
			<th style="cursor: pointer;">'.t('Номер перехода', __FILE__).'</th>
			<th style="cursor: pointer;">'.t('Пришел с', __FILE__).'</th>
			<th style="cursor: pointer;">'.t('на', __FILE__).'</th>
			<th style="cursor: pointer;">'.t('Время', __FILE__).'</th>
			<th style="cursor: pointer;">'.t('Примечание', __FILE__).'</th>
		   </tr></thead>';

	$old_link = '';
	$old_referer = '';
	$color = '#09afaa';
	$CI->db->order_by('time', 'desc');
	$CI->db->limit($col_str, $limit);
	$query = $CI->db->get_where("view_visit", $array_period);

	if($query->num_rows() > 0){		foreach ($query->result() as $row){
			$num = '';
			$referer = '';
			$link = '';
			$time = '';
			if (isset($row->num)) $num = $row->num;
			if ((!isset($options['link_to_link'])) or ($row->referer == '0' )){
				$referer = urldecode($row->referer);
			}else{
				$referer = '<a href="'.urldecode($row->referer).'">'.urldecode($row->referer).'</a>';
			}
			if ((!isset($options['link_to_link'])) or ($row->link == '0' )){
				$link = urldecode($row->link);
			}else{
				$link = '<a href="'.urldecode($row->link).'">'.urldecode($row->link).'</a>';
			}
			if (isset($row->time)) $time = $row->time;
			if ($old_referer == $link){
				$color = '#A5CBD3';
				$still = "&#8593;";
			}else{
				$color = '#09afaa';
				$still = "&#8596;";			}
			echo '<tr>
					<td>'.$num.'</td>
					<td>'.$referer.'</td>
					<td>'.$link.'</td>
					<td>'.my_date_small($time).'</td>
					<td bgcolor = "'.$color.'" align = center>'.$still.'</td>
				  </tr>';
			$old_referer = $referer;
		}
	}
echo '</table>';
}else{
	echo '<p><a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/day">'.t('За сутки', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/all">'.t('За все время', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/all_table/'.mso_segment(6).'">'.t('Показать все хосты (таблица)', __FILE__).'</a></p>';

	if (mso_segment(5) == 'all_table'){

	echo '<table class="page tablesorter" border="0" width="99%" id="visits">';
	echo '<thead><tr>
			<th style="cursor: pointer;">'.t('Хост', __FILE__).'</th>
			<th style="cursor: pointer;">'.t('Переходы', __FILE__).'</th>
		   </tr></thead>';
		$array_hosts = array_hosts(null);
		if (count($array_hosts) > 0){
			foreach($array_hosts as $host){
				$col_hits = hits_in_host($host);
				$ip = getinfo('site_admin_url').'plugin_view_visit/hosts/0/day/'.long2ip($host);
				echo '<tr>
					<td><a href="'.$ip.'">'. long2ip($host). '</a></td>
					<td>'.$col_hits.'</td>
				  </tr>';
			}
		}	echo '</table>';
	}else{
		$sec_day = return_sec_day(date("G:i:s"));
		$day = time()-$sec_day;
		$array_period = null;
		if (mso_segment(5) == 'day') $array_period = array('time >'=>$day);
		if (mso_segment(5) == 'all') $array_period = null;

		//Начало пагинации//////////////////////////////////////////////////////Для выполнения//////
		$array_hosts = array_hosts($array_period);							////////////////////////
		if (is_numeric(mso_segment(4))){								///////пагинации////////
			$num_str = mso_segment(4);								//используется функция//
			$limit = $num_str*$col_str;										////////////////////////
		}else{																///////pagination///////
			$num_str = 0;													////////////////////////
			$limit = 0;														////////////////////////
		}																	////////////////////////
		echo pagination($array_hosts, $num_str, getinfo('site_admin_url').'plugin_view_visit/hosts', $col_str);////
		$array_hosts = array_slice($array_hosts, $limit, $col_str);
		////////////////////////////////////////////////////////////////////////////////////////////

		if (count($array_hosts) > 0){			foreach($array_hosts as $host){				$col_hits = hits_in_host($host);
				$ip = getinfo('site_admin_url').'plugin_view_visit/hosts/0/day/'.long2ip($host);
				echo 'Хост: <a href="'.$ip.'">'. long2ip($host). '</a> ('.$col_hits.')<br>';			}		}
	}
}
?>