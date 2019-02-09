<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// осуществим отписку пользователя от сообщений
//  во 2-м сегменте уникальный ключ отписки пользователя, в 3-м сегменте - от какой искуссии отписаться
// получим Id всех подписанных дискуссий пользователя с таким ключом
$subscribe_id_array = dialog_get_unsubscribe_array($segment2);

// есть ли среди подписанных указанная в 3-м сегменте
$subscribe_id = 0;
foreach ($subscribe_id_array as $cur_id)
   if ($cur_id['watch_discussion_id'] == mso_segment(3)) 
   {
      $subscribe_id = $cur_id['watch_discussion_id'];
      $unsubscribe_user_id = $cur_id['profile_user_id'];
      break;
   }
   
// если есть кого и на что отписывать
if ($subscribe_id)
{
			  $CI = & get_instance();
			        
			  $CI->db->where('watch_user_id', $unsubscribe_user_id);
			  $CI->db->where('watch_discussion_id', $subscribe_id);
			  $upd_date = array ('watch_subscribe' => '0');
			  $res = ($CI->db->update('dwatch', $upd_date )) ? '1' : '0';
			
			  $CI->db->cache_delete_all();
			  mso_flush_cache(); // сбросим кэш
			
			  if (!$res) $errors = 'Ошибка БД при обновлении подписок';
			  else
			  {
			     $error = false;	
			     
           mso_head_meta('title', $options['name']); 
           mso_head_meta('description', $options['desc']); 
           require($template_dir . 'head.php'); 

           // Начало вывода_______________________________________________________________
           require(getinfo('template_dir') . 'main-start.php');
           echo NR . '<div class="dialog_page">' . NR;    

           require($template_dir . 'do.php');
			     echo '<div class="comment-ok">' . $options['unsubscribe-ok'] . '</div>'; 
           require($template_dir . 'posle.php');

           // конец вывода
           echo NR . '</div><!-- class="dialog_page" -->' . NR;
           require(getinfo('template_dir') . 'main-end.php');				     
			  }     
}
else $error = 'Пользователь не подписан на такую дискуссию';



 
?>