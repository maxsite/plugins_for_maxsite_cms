<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
?>
<h1><?= t('Переходы', __FILE__) ?></h1>
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

$arr = array();
if($CI->uri->segment(4) != '' or $CI->uri->segment(4) != null){	$url = str_replace("_", "/", $CI->uri->segment(4));
	echo '<p><strong>'.t('Переходы на URL ', __FILE__).'<i><a href='.$url.'>'.$url.'</a></i>:</strong></p>';
	$CI->db->select('referer, ip, time');
	$CI->db->order_by('time', 'desc');
	$query = $CI->db->get_where("view_visit", array('link'=>$url));
	if($query->num_rows() > 0){		echo '<table class="page tablesorter" border="0" width="99%" id="visits">';
		echo '<thead><tr>
				<th style="cursor: pointer;">'.t('Реферер', __FILE__).'</th>
				<th style="cursor: pointer;">'.t('IP', __FILE__).'</th>
				<th style="cursor: pointer;">'.t('Время', __FILE__).'</th>
			   </tr></thead>';
		foreach ($query->result() as $row){			$ip = long2ip($row->ip);
			echo '<tr>
					<td>'.$row->referer.'</td>
					<td title="'.t('Отследить переходы с ', __FILE__).$ip.'"><a href="'.getinfo('site_admin_url').'plugin_view_visit/hosts/0/day/'.$ip.'">'.$ip.'</a></td>
					<td>'.my_date_small($row->time).'</td>
				  </tr>';
		}
		echo '</table>';	}}else{
	$CI->db->select('link');	$query = $CI->db->get("view_visit");
	if($query->num_rows() > 0){		echo '<table class="page tablesorter" border="0" width="99%" id="visits">';
		echo '<thead><tr>
				<th style="cursor: pointer;">'.t('Номер', __FILE__).'</th>
				<th style="cursor: pointer;">URL</th>
				<th style="cursor: pointer;">'.t('Переходы', __FILE__).'</th>
			   </tr></thead>';
		foreach ($query->result() as $row){			@$arr[$row->link]++;
		}
		$i = 1;
		arsort($arr);
		echo '<p>'.t('Количество URL сайта, по которым были переходы: ', __FILE__).'<b>'.count($arr).'</b></p>';
		foreach ($arr as $url=>$col){			$sub_url = str_replace("/", "_", $url);			echo '<tr>
					<td>'.$i.'</td>
					<td><a href="'.getinfo('site_admin_url').'plugin_view_visit/sort/'.$sub_url.'">'.$url.'</a></td>
					<td>'.$col.'</td>
				  </tr>';
			$i++;		}
		echo '</table>';
	}else{		echo t('Нет переходов.', __FILE__);	}
}
?>
