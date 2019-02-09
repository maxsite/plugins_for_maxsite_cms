<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

 обработчик атрибутов комментария
 здесь изменим атрибуты комментария

*/

 // функции модификации
 require ($plugin_dir . 'functions/modify_db.php');

 $errors = array();
 $new_comment = array();
 $status_name = '';
 
	if (!mso_checksession($post['comments_session']) )
			  $errors[] = 'Ошибка сессии! Обновите страницу!';


		if ( !isset($post['comment_email']) or !$post['comment_email'] )
						$errors[] = 'Нужно указать Email';

		if ( !isset($post['comment_password']) or !$post['comment_password'] )
						$errors[] = 'Нужно указать пароль';

		$new_comment['comment_email'] = mso_strip($post['comment_email']);
		$new_comment['comment_password'] = mso_strip($post['comment_password']);

		if ( !mso_valid_email($new_comment['comment_email']) ) $errors[] = 'Ошибочный Email';
  
		$new_comment['comment_ip'] = $_SERVER['REMOTE_ADDR'];
		$new_comment['comment_date'] = date('Y-m-d H:i:s');
		
    
		// если нужно редактировать существующий комментарий
		if (isset($post['comment_comment_id']))
		{
		    $new_comment_id = $post['comment_comment_id'];
		    $id = (int) $new_comment_id;
		    if ( (string) $new_comment_id != (string) $id ) $id = false; // $new_comment_id не число
		    if (!$id) $errors[] = 'Номер коммента не число';
		    $new_comment['comment_id'] = $id;     
		}   

    $user = array(
          'comment_password' => $new_comment['comment_password'],
          'comment_email' => $new_comment['comment_email']
        );
    if (isset($post['comment_password_md'])) $user['comment_password_md'] = $post['comment_password_md'];
        
	  // проверим пользователя
	  // здесь мы сравним чтобы id было как у залогиненного (заодно проверим залогиненность)
	  $comuser_id2 = dialog_get_comuser($user);
	  
	  if (!$comuser_id2 or ($comuser_id2 != $comuser_id)) $errors[] = 'Неверные регистрационные данные';

    // теперь можно комментарий изменить
    if (!$errors)
    {
		    $new_comment['comment_user_id'] = $comuser_id;
		    $new_comment['role_id'] = $comuser_role;
		    
		    $status_name = mso_array_get_key($post['dialog_status_submit']);
        if ($status_name == 'delete') $new_comment['comment_deleted'] = '1';
        if ($status_name == 'undelete') $new_comment['comment_deleted'] = '0';
        if ($status_name == 'approved') $new_comment['comment_approved'] = '1';
        if ($status_name == 'unapproved') $new_comment['comment_approved'] ='0';
        if ($status_name == 'spam') $new_comment['comment_spam'] = '1';
        if ($status_name == 'unspam') $new_comment['comment_spam'] = '0';        
        
        $res = dialog_edit_comment($new_comment, $options);
               
        if ($res['errors']) $errors = $errors + $res['errors'];
        else
        {
            //mso_redirect(mso_current_url()/* . '#comment-' . $id_comment_new*/);	      
        }
    }
    
   //выведем результат обработки
   if ($errors) 
   { 
       echo '<div class="comment-error">';
       foreach ($errors as $error) echo $error;
       echo '</div>'; 
   }      
   else
   {
      if ($status_name == 'delete') echo '<div class="comment-ok">' . $options['delete-ok'] . '</div>'; 
      if ($status_name == 'undelete') echo '<div class="comment-ok">' . $options['undelete-ok'] . '</div>'; 
      if ($status_name == 'approved') echo '<div class="comment-ok">' . $options['approved-ok'] . '</div>'; 
      if ($status_name == 'unapproved') echo '<div class="comment-ok">' . $options['unapproved-ok'] . '</div>'; 
      if ($status_name == 'spam') echo '<div class="comment-ok">' . $options['spam-ok'] . '</div>'; 
      if ($status_name == 'unspam') echo '<div class="comment-ok">' . $options['unspam-ok'] . '</div>'; 
   }   
   echo '<div class="break"></div>';
 //pr ($res['messages']);
 //pr ($errors);
 //pr ($new_comment);
 
?>