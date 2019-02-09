<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 // забаненные пользователи
 
	if ( $post = mso_check_post(array('f_session_id', 'f_unbaned')) )
	{
		# защита рефера
		mso_checkreferer();
	    $CI = & get_instance();
		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_unbaned']); 
	  
        $upd_data = array('profile_spam_check'=>'0');	    
		$CI->db->where('profile_user_id', $f_id);
		$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
        
		if ($res)  echo '<div class="update">Пользователь номер ' . $f_id . ' разбанен.</div>';
        else echo '<div class="error">' .  'Ошибка изменения' . '</div>';		
    }

  require ($plugin_dir . 'functions/access_db.php');
 
 
 // получим пользователей
  $profiles = dialog_get_profiles($options , array('only_spam_check' => true));
 
 echo '<H1>Забаненные</H1>';
 
 echo '<form action="" method="post">' . mso_form_session('f_session_id'); 
 if (!$profiles) echo '<H2>Нет</H2>';
 echo '<table width=100%>';
 echo '<th><tr><td>Id</td><td>Psevdonim</td><td></td><td>Назначить роль:</td><td>Последний коммент</td></tr></th><tbody>'; 
 
 $last_comments = dialog_get_last_comments(array('cache_flag'=>$options['cache_flag'],'comusers'=>$profiles , 'and_deleted'=>true , 'and_spam_check'=>true , 'and_not_approved'=>true));

 foreach ($profiles as $profile)
 {
	$user_link = '<a href = "' . $siteurl . $options['profile_slug'] . '/' . $profile['profile_user_id'] . '" title = "Публичная страница" target="blank">' . $profile['profile_psevdonim'] . '</a>';

    echo '<tr><td>' . $profile['profile_user_id'] . '</td><td>' . $user_link . '</td><td>' . $profile['profile_comments_count'] .  
       '</td><td>';
    echo '<input type="submit" name="f_unbaned[' . $profile['profile_user_id'] . ']" value="' . t('Разбанить') . '">';
	echo '</td><td>' . htmlspecialchars($last_comments[$profile['profile_user_id']]['comment_content']);
  
  echo '</td></tr>';
 }

 echo '</tbody></table>';  
 echo '</form>';


?>