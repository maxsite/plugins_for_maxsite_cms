<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// верхний блок 
// этот файл подключается в каждом шаблонном файле вывода страницы форума в самом начале
// выводится меню
// выводится приветсвие или войти
// кнопки смены стиля


// сформируем массив меню
$links = array();
// $links[] = $main_link;
if ($comuser_id) $links[] = '<a href = "' . $siteurl . $options['main_slug'] . '/' . $options['news_slug'] . '">' . $options['news'] . '</a>';
$links[] = '<a href = "' . $siteurl . $options['main_slug'] . '/' . $options['all-comments_slug'] . '">' . $options['all-comments'] . '</a>';
$links[] = '<a href = "' . $siteurl . $options['main_slug'] . '/' . $options['all-discussions_slug'] . '">' . $options['all-discussions'] . '</a>';
$links[] = '<a href = "' . $siteurl . $options['profile_slug'] . '/' . $options['main_slug'] . '">' . $options['profiles'] . '</a>';

if ($comuser_id) 
{
   $links[] = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $comuser_id . '/' . $options['comments_slug']  . '">' . $options['you-comments'] . '</a>';
   $links[] = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $comuser_id . '/' . $options['subscribe_slug']  . '">' . $options['you-subscribe'] . '</a>'; 
}
   echo '<div id="dialog-do" class="dialog-do">';


   // выведем меню
   echo '<div class="dialog-menu">';

   foreach ($links as $link)
      echo $link	. ' | ' . NR;

 //  echo '<span class="right">' . $link_login . '</span>' . NR;
   echo '</div>';
   echo '<div class="break"></div>';

  // выведем кнопку смены стилей форума 
  if ($comuser_id and $options['style_button'])
  {
    // обработаем нажатие кнопки
    if ($post = mso_check_post(array('f_session_id', 'dialog_css_submit')))
    {
		$css_id = mso_array_get_key($post['dialog_css_submit']);
		$upd_date = array('profile_css' => $options['css'][$css_id]);
			  
		$CI = & get_instance();
		$CI->db->where('profile_user_id', $comuser_id);
		$res = ($CI->db->update('dprofiles', $upd_date )) ? '1' : '0';
			     
		if ($res) $comuser['profile_css'] = $options['css'][$css_id];
			     
		$CI->db->cache_delete_all();
    }  
  
    echo '<div class="right">';
    echo '<form action="" method="post">' . mso_form_session('f_session_id'); 
    if ($comuser['profile_css'] == $options['css'][0]) echo '<input name="dialog_css_submit[1]" type="submit" value="->" class="css_submit">';
    else echo '<input name="dialog_css_submit[0]" type="submit" value="<-" class="css_submit">';
    echo '</form></div>';   
  }
  
 
echo '</div>';

?>



