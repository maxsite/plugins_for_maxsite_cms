<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
?>
<h1><?= t('Статистика посетителей', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет просматривать посещения сайта.', __FILE__) ?></p>
<?
	echo '<p><a href="'.getinfo('site_admin_url').'plugin_view_visit/stat/browser/">'.t('Браузеры', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/stat/platform/">'.t('Платформы', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/stat/resol/">'.t('Разрешение монитора', __FILE__).'</a> | <a href="'.getinfo('site_admin_url').'plugin_view_visit/stat/lang/">'.t('Язык', __FILE__).'</a>';
	if (isset($options['ip_to_country']) and $options['ip_to_country'] == 'on'){		echo ' | <a href="'.getinfo('site_admin_url').'plugin_view_visit/stat/country/">'.t('Страны', __FILE__).'</a></p>';
	}else{		echo '</p>';	}

	switch (mso_segment(4)){		case 'browser':		echo mso_load_jquery();
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
										<th style="cursor: pointer;">'.t('Браузер', __FILE__).'</th>
										<th style="cursor: pointer;">'.t('Количество', __FILE__).'</th>
										<th style="cursor: pointer;">%</th>
									   </tr></thead>';
							$arr = array();
							$CI->db->select('browser_small');
							$CI->db->order_by('browser_small', 'desc');
							$query = $CI->db->get_where("view_visit");
							if($query->num_rows() > 0){
								foreach ($query->result() as $row){									$browser = ereg_replace('[0-9.]', '', $row->browser_small);
									$browser = trim($browser);
									@$arr[$browser]++;								}
							}
							$summ = array_sum($arr);
							$t_google = 't:';
							$chl_google = '';
							foreach ($arr as $browser=>$col){								$per = round(($col/$summ)*100, 2);
								echo '<tr>
								<td>'.$browser.'</td>
								<td>'.$col.'</td>
								<td>'.$per.'</td>';
								$t_google = $t_google.$per.',';
								$chl_google = $chl_google.$browser.'|';
							}
							echo '</table>';
							$len_t_google = strlen($t_google)-1;
							$t_google = substr($t_google, 0, $len_t_google);
							echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd='.$t_google.'&chs=350x100&chl='.$chl_google.'&chco=0000FF">';
							break;
		case 'platform':	echo mso_load_jquery();
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
										<th style="cursor: pointer;">'.t('Платформа', __FILE__).'</th>
										<th style="cursor: pointer;">'.t('Количество', __FILE__).'</th>
										<th style="cursor: pointer;">%</th>
									   </tr></thead>';
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
							$summ = array_sum($arr);
							$t_google = 't:';
							$chl_google = '';
							foreach ($arr as $platform=>$col){
								$per = round(($col/$summ)*100, 2);
								echo '<tr>
								<td>'.$platform.'</td>
								<td>'.$col.'</td>
								<td>'.$per.'</td>';
								$t_google = $t_google.$per.',';
								$chl_google = $chl_google.$platform.'|';
							}
							$len_t_google = strlen($t_google)-1;
							$t_google = substr($t_google, 0, $len_t_google);
							echo '</table>';
							echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd='.$t_google.'&chs=350x100&chl='.$chl_google.'&chco=0000FF">';
							break;
		case 'resol':		echo '<i>'.t('Разрешение экрана выбирается для каждого уникального посетителя при последнем посещении. Если разрешение не было определено - он в статистику не попадает.', __FILE__).'</i>';
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
										<th style="cursor: pointer;">'.t('Разрешение монитора', __FILE__).'</th>
										<th style="cursor: pointer;">'.t('Количество', __FILE__).'</th>
										<th style="cursor: pointer;">%</th>
									   </tr></thead>';
							$arr = array();
							$hosts = array_hosts(null);
							foreach($hosts as $host){
								$CI->db->select('resolution');
								$CI->db->order_by('time', 'desc');
								$query = $CI->db->get_where("view_visit", array('ip'=>$host), 1);
								if ($query->num_rows() > 0){									foreach ($query->result() as $row){
										$resol = trim($row->resolution);
										if ($resol != ''){
											@$arr[$resol]++;
										}
									}								}
							}
							$summ = array_sum($arr);
							$t_google = 't:';
							$chl_google = '';
							foreach ($arr as $resol=>$col){
								$per = round(($col/$summ)*100, 2);
								echo '<tr>
								<td>'.$resol.'</td>
								<td>'.$col.'</td>
								<td>'.$per.'</td>';
								$t_google = $t_google.$per.',';
								$chl_google = $chl_google.$resol.'|';
							}
							$len_t_google = strlen($t_google)-1;
							$t_google = substr($t_google, 0, $len_t_google);
							echo '</table>';
							echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd='.$t_google.'&chs=350x100&chl='.$chl_google.'&chco=0000FF">';
							break;
		case 'lang':		echo '<i>'.t('Язык выбирается для каждого уникального посетителя при последнем посещении.', __FILE__).'</i>';
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
										<th style="cursor: pointer;">'.t('Язык', __FILE__).'</th>
										<th style="cursor: pointer;">'.t('Количество', __FILE__).'</th>
										<th style="cursor: pointer;">%</th>
									   </tr></thead>';
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
							$summ = array_sum($arr);
							$t_google = 't:';
							$chl_google = '';
							foreach ($arr as $lang=>$col){
								$per = round(($col/$summ)*100, 2);
								echo '<tr>
								<td>'.$lang.'</td>
								<td>'.$col.'</td>
								<td>'.$per.'</td>';
								$t_google = $t_google.$per.',';
								$chl_google = $chl_google.$lang.'|';
							}
							$len_t_google = strlen($t_google)-1;
							$t_google = substr($t_google, 0, $len_t_google);
							echo '</table>';
							echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd='.$t_google.'&chs=350x100&chl='.$chl_google.'&chco=0000FF">';
							break;
		case 'country':		if(!isset($options['ip_to_country'])) break;
							require(getinfo('plugins_dir') . 'view_visit/iptocountry.php');
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
										<th style="cursor: pointer;">'.t('Страна', __FILE__).'</th>
										<th style="cursor: pointer;">'.t('Количество', __FILE__).'</th>
										<th style="cursor: pointer;">%</th>
									   </tr></thead>';
							$arr = array();
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
							$summ = array_sum($arr);
							$t_google = 't:';
							$chl_google = '';
							foreach ($arr as $country=>$col){
								$per = round(($col/$summ)*100, 2);
								$country = $country_list[$country];
								echo '<tr>
								<td>'.$country.'</td>
								<td>'.$col.'</td>
								<td>'.$per.'</td>';
								$t_google = $t_google.$per.',';
								$chl_google = $chl_google.$country.'|';
							}
							$len_t_google = strlen($t_google)-1;
							$t_google = substr($t_google, 0, $len_t_google);
							echo '</table>';
							echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd='.$t_google.'&chs=350x100&chl='.$chl_google.'&chco=0000FF">';
							break;
		default:			echo t('Выберите раздел', __FILE__);
							$str = '';
							$t_google = 't:';
							$t_google_hosts = 't:';
							$chl_google = '';
							$all_hits = 0;
							$all_hosts = 0;
							$CI->db->order_by('date', 'desc');
							$query = $CI->db->get('view_visit_base');
							if ($query->num_rows() > 0){
								echo '<p>'.t('Данные в архиве: ', __FILE__).'</p>';
								$arhives = get_arhive();
								foreach($query->result() as $row) {									$time = trim($row->date);
									$date_time = explode(' ', $time);
									$chl_google .= $date_time[0].'|';
									$all_hits = $all_hits+$arhives[$row->date]['hits'];
									$all_hosts = $all_hosts+$arhives[$row->date]['hosts'];
									$t_google .= $arhives[$row->date]['hits'].',';
									$t_google_hosts .= $arhives[$row->date]['hosts'].',';
									//echo "<p><b>".$date_time[0]."</b>:</p> ";
									//echo "Хитов - ".$arhives[$row->date]['hits'].';<br>';
									//echo "Уникальных посетителей - ".$arhives[$row->date]['hosts'].';<br>';
									foreach($arhives[$row->date]['browser'] as $browser=>$col){										$all_browser[$browser] = @$all_browser[$browser]+$col;
										$str .= $browser."(".$col.") ";									}
									//echo "Браузеры - ".count($arhives[$row->date]['browser']).': '.$str.';<br>';
									$str = '';
									foreach($arhives[$row->date]['platform'] as $platform=>$col){										$all_platform[$platform] = @$all_platform[$platform]+$col;
										$str .= $platform."(".$col.") ";
									}
									//echo "Платформы - ".count($arhives[$row->date]['platform']).': '.$str.';<br>';
									$str = '';
									foreach($arhives[$row->date]['resolution'] as $resolution=>$col){										$all_resolution[$resolution] = @$all_resolution[$resolution]+$col;
										$str .= $resolution."(".$col.") ";
									}
									//echo "Расширения - ".count($arhives[$row->date]['resolution']).': '.$str.';<br>';
									$str = '';
									foreach($arhives[$row->date]['language'] as $language=>$col){										$all_language[$language] = @$all_language[$language]+$col;
										$str .= $language."(".$col.") ";
									}
									//echo "Языки - ".count($arhives[$row->date]['language']).': '.$str.';<br>';
									$str = '';
									foreach($arhives[$row->date]['country'] as $country=>$col){										$all_country[$country] = @$all_country[$country]+$col;
										$str .= $country."(".$col.") ";
									}
									//echo "Страны - ".count($arhives[$row->date]['country']).': '.$str.';<br>';
									$str = '';
								}
								$len_t_google = strlen($t_google)-1;
								$t_google = substr($t_google, 0, $len_t_google);
								$len_t_google = strlen($t_google_hosts)-1;
								$t_google_hosts = substr($t_google_hosts, 0, $len_t_google);
								echo t('Хиты: ', __FILE__).'<br>';
								echo '<img src="http://chart.apis.google.com/chart?chs=350x200&chd='.$t_google.'&cht=ls&chl='.$chl_google.'&chxt=x,y"><br>';
								echo t('Уникальные посетители: ', __FILE__).'<br>';
								echo '<img src="http://chart.apis.google.com/chart?chs=350x200&chd='.$t_google_hosts.'&cht=ls&chl='.$chl_google.'&chxt=x,y&chxp=1,10,15,35&chco=34626B"><br>';
								echo t('Общие показатели в архиве: ', __FILE__).'<br>';
								echo '<b>'.t('Просмотры', __FILE__).'</b> - '.$all_hits.';<br>';
								echo '<b>'.t('Уникальные посетители', __FILE__).'</b> - '.$all_hosts.';<br>';
								echo '<b>'.t('Браузеры', __FILE__).'</b>: <br>';
								if (isset($all_browser) and is_array($all_browser) and count($all_browser) > 0){
									arsort($all_browser);
									foreach($all_browser as $browser=>$col){
										echo "&nbsp;&nbsp;&nbsp;&nbsp;".$browser." - ".$col."<br>";
									}
								}
								if (isset($all_platform) and is_array($all_platform) and count($all_platform) > 0){
									arsort($all_platform);
									echo '<b>'.t('Платформы', __FILE__).'</b>: <br>';
									foreach($all_platform as $platform=>$col){										echo "&nbsp;&nbsp;&nbsp;&nbsp;".$platform." - ".$col."<br>";									}
								}
								if (isset($all_resolution) and is_array($all_resolution) and count($all_resolution) > 0){
									arsort($all_resolution);
									echo '<b>'.t('Разрешения', __FILE__).'</b>: <br>';
									foreach($all_resolution as $resolution=>$col){
										echo "&nbsp;&nbsp;&nbsp;&nbsp;".$resolution." - ".$col."<br>";
									}
								}
								if (isset($all_language) and is_array($all_language) and count($all_language) > 0){
									arsort($all_language);
									echo '<b>'.t('Языки', __FILE__).'</b>: <br>';
									foreach($all_language as $language=>$col){
										echo "&nbsp;&nbsp;&nbsp;&nbsp;".$language." - ".$col."<br>";
									}
								}
								if (isset($all_country) and is_array($all_country) and count($all_country) > 0){
									arsort($all_country);
									echo '<b>'.t('Страны', __FILE__).'</b>: <br>' ;
									foreach($all_country as $country=>$col){
										echo "&nbsp;&nbsp;&nbsp;&nbsp;".$country." - ".$col."<br>";
									}
								}
							}
							break;	}
?>