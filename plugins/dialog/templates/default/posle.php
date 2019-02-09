<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// что вывести в самом конце

 $cache_key = 'dialog_posle';
 if ($options['cache_flag']) $out = mso_get_cache($cache_key);
 else $out = '';

 if ($out) echo $out;
 else 
 {
   $out .= '<div class="statistic">';
   $out .= '<a href = "' . $siteurl . $options['main_slug'] . '/' . $options['all-comments_slug'] . '" title="' . $options['all-comments'] . '">Комментариев:</a> ' . dialog_get_comments_count(); 
   $out .= ' | <a href = "' . $siteurl . $options['main_slug'] . '/' . $options['all-discussions_slug'] . '" title="' . $options['all-discussions'] . '">Дискуссий:</a> ' . dialog_get_discussions_count(); 
   
   $all_users = dialog_get_profiles($options , array('sort_field' => 'profile_date_first_visit'));
   if ($all_users) 
   { 
     $last_user = $all_users[0];
     $out .= ' | <a href = "' . $siteurl . $options['profile_slug'] . '/' . $options['main_slug'] . '" title="' . $options['profiles'] . '">Пользователей:</a> ' . count($all_users); 
     $out .= ' | Новый пользователь: ' . dialog_profile_link($last_user['profile_user_id'] , $last_user['profile_psevdonim'] , $options['profile_slug'], $siteurl , $options['profile']); 
   }
   $out .= '</div>';
   
 	 if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
   echo $out;
  } 

  $date = date('Y-m-d H:i:s'); //текущая дата
  $date = mso_page_date($date, 
									array(	'format' => 'j F Y H:i:s', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	

  
  //echo '<div class="statistic">';
  //echo 'Dialog<span class="right">Сейчас: ' . $date . '</span>';   
  //echo '</div>';    
     
?>