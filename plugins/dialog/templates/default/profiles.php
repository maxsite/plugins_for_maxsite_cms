<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

  $error = false; // флаг о том - удачен ли перехват 404
//  require (getinfo('plugins_dir') . 'profile/functions_avatar.php' );
 
// все профайлы форума

// синхронизируем меню с плагином profile
  $profile_plugin_optoins = mso_get_option('profile', 'plugins', array());
  // получим страницы личного кабинета
  $profile_plugin_optoins['pages_main'] = isset($profile_plugin_optoins['pages_main']) ? $profile_plugin_optoins['pages_main'] : false;
	if ($profile_plugin_optoins['pages_main'])
	    mso_hook_add( 'head', 'profile_head');

  // варианты сортировки заданы массивом в подключаемом ниже файле sortable-profiles.php
   
  $fn = 'sortable-profiles.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
     require($template_default_dir . $fn); 

$par = array('user_id' => $comuser_id , 'role_id' => $comuser_role);
   
$allow_sort_field = true;  
  
$segment3 = mso_segment(3);

if ($segment3 == 'next') $segment3 = '';

if (!$segment3) $segment3 = 'rate';


if ($segment3)
   if (isset($array_fields[$segment3]))
      $allow_sort_field = true; 
   else 
   {
      $allow_sort_field = false;
	  $error = $options['out_of_profiles']; // 404
   }
else $allow_sort_field = true;  



if ($allow_sort_field)
{
   if ($segment3)
   { 
      $sortable_title = $array_fields[$segment3]['title'];
      $sortable_desc = $array_fields[$segment3]['desc'];
   }   
   else
   {
      $sortable_title = '';
      $sortable_desc = '';
   }  

   // может нужно сортировать?
   if ($segment3)
   {
      $par['sort_field'] = $array_fields[$segment3]['sort_field'];
      $par['sort_order'] = $array_fields[$segment3]['sort_order'];
   }  
}  
   
$profiles = dialog_get_profiles($options, $par);
/*
$CI = & get_instance();
	      $upd_data = array('profile_spam_check'=>'0','profile_moderate'=>'0');
	      $CI->db->where('profile_user_id', 152);
		    $res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';	
			*/
			
if (!$error and $profiles)
{
   mso_head_meta('title', $options['name'] . ' >> ' . $options['profiles']); 
   mso_head_meta('description', $options['desc'] ); 

    $fn = 'head.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn); 
        
   // Начало вывода_______________________________________________________________
   require(getinfo('shared_dir') . 'main/main-start.php');
   echo NR . '<div class="dialog_page">' . NR;
   
  if ($profile_plugin_optoins['pages_main'])  
  {
    echo '<div class="profile_menu">' . NR;
    $url = getinfo('siteurl') . $options['profile_slug'] . '/' ;
     foreach ($profile_plugin_optoins['pages_main'] as $slug=>$cur_title)
     {
      if ($slug == '0') $slug = '';
      if ( $slug == $segment2 ) echo $cur_title . ' | ' . NR;
      else echo '<a href="' . $url . $slug . '">' . $cur_title . '</a> | ' . NR;
     }  
    echo '</div>' . NR;  
  } 
   
    $fn = 'do.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn); 
    
  // хлебные крошки
  $out = '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['profiles'] . '</div>';

  //Выводим  заголовок и описание  ______________________________ 
  echo '<H1>' . $options['profiles']  . '</H1>';
 
    // выводим навигацию по сортировке
   echo  '<div class="sort_navi">' . $options['sortable'];
   
   foreach ($array_fields as $slug => $sortable)
   {
     if ($slug != $segment3)
        echo  '<a href ="' . $siteurl . $options['profile_slug'] . '/' . $options['main_slug'] . '/' . $slug . '" title ="' .  $sortable['desc'] . '">' . $sortable['title'] . '</a> | '; 
     else 
        echo  $sortable['title'] . ' | '; 
   }   
   echo '</div>'; 
   
 
  // вывоим ссылку на админ-действия пользователей
  echo '<div class="admin_log"><a href="' . getinfo('siteurl') . $options['main_slug']  . '/' . $options['log_slug'] . '" title="' . $options['all_log_desc'] . '">' . $options['all_log_title'] . '</a></div>';
 
 $out = '<table class="table_profiles">';
 	
 $out .= '<tr><th>Пользователь</th><th>Комментариев</th><th>Регистрация</th><th>Последняя активность</th><th>Последний визит</th><th>Рейтинг</th></tr>';
 $out .= '<tbody>';          	
 foreach ($profiles as $profile)
 {
 	  extract ($profile);
 	  //$register_date = _dialog_date('j F Y' , $profile_date_first_visit);
 	  
 	  if ($profile_user_role_id == 1) $profile_role = 'Пользователь';
 	  elseif ($profile_user_role_id == 2) $profile_role = 'Модератор';
 	  elseif ($profile_user_role_id == 3) $profile_role = 'Администратор';
	  else $profile_role = '';

      if ($profile_spam_check == 1) $profile_role = 'Забаненный ' . $profile_role;
      
		  $out .= '<tr><td>' . dialog_profile_link($profile_user_id, $profile_psevdonim , $options['profile_slug'], $siteurl , $options['profile']) . '<br />' . $profile_role .
 	          '</td><td>' . $profile_comments_count . 
 	          '</td><td>' .  _dialog_date('j F Y' , $profile_date_first_visit) . '</td>';
			  
 	          if ($profile_date_last_active) $out .= '<td>' . date('d.m.y H:i:s' , $profile_date_last_active) . '</td>';
              else 	$out .= '<td>' . $options['profile_no_active'] . '</td>';
			  
              $out .= '<td>' . date('d.m.y H:i:s' , strtotime($comusers_last_visit)) . 	          
 	          '</td><td>' . $profile_rate . '</td></tr>';
 	}
 	
 	$out .= '</tbody></table>';
 	
   echo $out;
   
    $fn = 'posle.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);
        
   // конец вывода
   
   echo NR . '</div><!-- class="dialog_page" -->' . NR;

   require(getinfo('shared_dir') . 'main/main-end.php');


}
else $error = $options['out_of_profiles']; // 404

 
?>