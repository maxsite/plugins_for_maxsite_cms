<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 // редактирование атрибутов профилей
 
	// проверяем входящие данные если было обновление профайлов
	if ( $post = mso_check_post(array('f_session_id', 'f_role1_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_role1_submit']); 
	 $CI = & get_instance();
	  $upd_data = array('profile_user_role_id'=>1);
	  $CI->db->where('profile_user_id', $f_id);
		$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
    if ($res)  echo '<div class="update">Роль пользователя номер ' . $f_id . ' изменена.</div>';
    else echo '<div class="error">' .  'Ошибка изменения' . '</div>';		
  }
 
	if ( $post = mso_check_post(array('f_session_id', 'f_role2_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_role2_submit']); 
	 $CI = & get_instance();
	  $upd_data = array('profile_user_role_id'=>2);
	  $CI->db->where('profile_user_id', $f_id);
		$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
    if ($res)  echo '<div class="update">Роль пользователя номер ' . $f_id . ' изменена.</div>';
    else echo '<div class="error">' .  'Ошибка изменения' . '</div>';		
  } 

	if ( $post = mso_check_post(array('f_session_id', 'f_role3_submit')) )
	{
		# защита рефера
		mso_checkreferer();
	 $CI = & get_instance();
		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_role3_submit']); 
	  
	  $upd_data = array('profile_user_role_id'=>3);
	  $CI->db->where('profile_user_id', $f_id);
		$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
    if ($res)  echo '<div class="update">Роль пользователя номер ' . $f_id . ' изменена.</div>';
    else echo '<div class="error">' .  'Ошибка изменения' . '</div>';		
  }
   
 require ($plugin_dir . 'functions/access_db.php');
 
 
 // получим пользователей
  $profiles = dialog_get_profiles($options);

  $role0 = $role1 = $role2 = $role3 = '';
  foreach ($profiles as $profile)
  {

     if ($profile['profile_user_role_id'] == 1) 
     {
/*
if ($profile['profile_user_id'] == 38)
{
          $CI = & get_instance();
	  $upd_data = array('profile_moderate'=>'0' ,  'profile_spam_check'=>'0');
	  $CI->db->where('profile_user_id', 38);
$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
}*/

       $role1 .= '<tr><td>' . $profile['profile_user_id'] . '</td><td>' . $profile['profile_psevdonim'] . '__' . 
$profile['profile_moderate']
 . '__ ' . $profile['profile_spam_check'] . '___' . $profile['profile_comments_count'] . 

'</td><td>';
       $role1 .= '<input type="submit" name="f_role2_submit[' . $profile['profile_user_id'] . ']" value="' . t('Модератором') . '">';
       $role1 .= '<input type="submit" name="f_role3_submit[' . $profile['profile_user_id'] . ']" value="' . t('Администратором') . '">';
       $role1 .= '</td></tr>';
     }   
     elseif ($profile['profile_user_role_id'] == 2) 
     {
       $role2 .= '<tr><td>' . $profile['profile_user_id'] . '</td><td>' . $profile['profile_psevdonim'] . '</td><td>';
       $role2 .= '<input type="submit" name="f_role1_submit[' . $profile['profile_user_id'] . ']" value="' . t('Пользователем') . '">';
       $role2 .= '<input type="submit" name="f_role3_submit[' . $profile['profile_user_id'] . ']" value="' . t('Администратором') . '">';
       $role2 .= '</td></tr>';
     }   
     elseif ($profile['profile_user_role_id'] == 3) 
     {
       $role3 .= '<tr><td>' . $profile['profile_user_id'] . '</td><td>' . $profile['profile_psevdonim'] . '</td><td>';
       $role3 .= '<input type="submit" name="f_role1_submit[' . $profile['profile_user_id'] . ']" value="' . t('Пользователем') . '">';
       $role3 .= '<input type="submit" name="f_role2_submit[' . $profile['profile_user_id'] . ']" value="' . t('Модератором') . '">';
       $role3 .= '</td></tr>';
     }          
     else
     {
       $role0 .= '<tr><td>' . $profile['profile_user_id'] . '</td><td>' . $profile['profile_psevdonim'] . '</td><td>';
       $role0 .= '<input type="submit" name="f_role1_submit[' . $profile['profile_user_id'] . ']" value="' . t('Пользователем') . '">';
       $role0 .= '<input type="submit" name="f_role2_submit[' . $profile['profile_user_id'] . ']" value="' . t('Модератором') . '">';
       $role0 .= '<input type="submit" name="f_role3_submit[' . $profile['profile_user_id'] . ']" value="' . t('Администратором') . '">';
       $role0 .= '</td></tr>';
     }  
  
  }

 echo '<form action="" method="post">' . mso_form_session('f_session_id'); 
 
if ($role3)
{ 
 echo '<H1>Администраторы</H1>';
 echo '<table width=100%>';
 echo '<th><tr><td>' . 'Id' .'</td><td>' . 'Psevdonim' .'</td><td>' . 'Назначить роль:' .'</td></tr></th><tbody>'; 
 echo '<tr>' . $role3 . '</tr>';
 echo '</tbody></table>';
}

if ($role2)
{  
 echo '<H1>Модераторы</H1>';
 echo '<table width=100%>';
 echo '<th><tr><td>' . 'Id' .'</td><td>' . 'Psevdonim' .'</td><td>' . 'Назначить роль:' .'</td></tr></th><tbody>'; 
 echo $role2;
 echo '</tbody></table>'; 
}

if ($role1)
{ 
 echo '<H1>Пользователи</H1>';
 echo '<table width=100%>';
 echo '<th><tr><td>' . 'Id' .'</td><td>' . 'Psevdonim' .'</td><td>' . 'Назначить роль:' .'</td></tr></th><tbody>'; 
 echo '<tr>' . $role1 . '</tr>';
 echo '</tbody></table>'; 
}

if ($role0)
{  
 echo '<H1>Роль неопределена</H1>';
 echo '<table width=100%>';
 echo '<th><tr><td>' . 'Id' .'</td><td>' . 'Psevdonim' .'</td><td>' . 'Назначить роль:' .'</td></tr></th><tbody>'; 
 echo '<tr>' . $role0 . '</tr>';
 echo '</tbody></table>';  
} 
  
 echo '</form>';


?>