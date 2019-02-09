<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

 получение post изменения дискуссии

*/

 require ($plugin_dir . 'functions/modify_db.php');

 $errors = array();
 $new_discussion = array();
 $user = array();
 
 if (!isset($post['discussion_id']) or !$post['discussion_id']) $errors[] = 'Номер дискуссии отсутствует';
		
		$discussion_id = $post['discussion_id'];
		$id = (int) $discussion_id;
		if ( (string) $discussion_id != (string) $id ) $id = false; // $comment_discussion_id не число
		if (!$id) $errors[] = 'Номер дискуссии не число';
		$new_discussion['discussion_id'] = $id;     


		if ( !isset($post['comment_email']) or !$post['comment_email'] )
						$errors[] = 'Нужно указать Email';

		if ( !isset($post['comment_password']) or !$post['comment_password'] )
						$errors[] = 'Нужно указать пароль';



		$user['comment_email'] = mso_strip($post['comment_email']);
		$user['comment_password'] = mso_strip($post['comment_password']);

		if ( !mso_valid_email($user['comment_email']) ) $errors[] = 'Ошибочный Email';


    if (isset($post['comment_password_md'])) $user['comment_password_md'] = $post['comment_password_md'];
        
	  // проверим пользователя
	  // здесь мы сравним чтобы id было как у залогиненного (заодно проверим залогиненность)
	  $comuser_id2 = dialog_get_comuser($user);
	  
	  if (!$comuser_id2 or ($comuser_id2 != $comuser_id)) $errors[] = 'Неверные регистрационные данные';


		$new_discussion['comment_ip'] = $_SERVER['REMOTE_ADDR'];
		$new_discussion['comment_date'] = date('Y-m-d H:i:s');
		
		$status_name = mso_array_get_key($post['dialog_status_submit']);
		
    if ($status_name == 'edit')
    {
       $new_discussion['discussion_desc'] = $post['discussion_desc'];
		   $new_discussion['discussion_title'] = isset($post['discussion_title']) ? trim(strip_tags($post['discussion_title'])) : '';
		   if (isset($post['select_category_id'])) $new_discussion['discussion_category_id'] = $post['select_category_id'];
    }
    elseif ($status_name == 'unapproved')
       $new_discussion['discussion_approved'] = 0;
    elseif ($status_name == 'approved')
       $new_discussion['discussion_approved'] = 1; 
       
    elseif ($status_name == 'closed')
       $new_discussion['discussion_closed'] = 1; 
    elseif ($status_name == 'unclosed')
       $new_discussion['discussion_closed'] = 0; 
        
   if (isset($post['discussion_order'])) 
		   $new_discussion['discussion_order'] = $post['discussion_order'];
        
   if (isset($post['discussion_style_id'])) 
		   $new_discussion['discussion_style_id'] = $post['discussion_style_id'];
		   
   if (isset($post['f_tags'])) 
		   $new_discussion['discussion_tags'] = $post['f_tags'];		   
		
   if (isset($post['room_members'])) 
   {
      // члены приватной комнаты
		  // пройдемся по массиву и составим массив из ID
		  $new_discussion['discussion_members'] = array(); // список всех где ON
		  foreach ($post['room_members'] as $id_member=>$val)
			   if ($val) $new_discussion['discussion_members'][] = $id_member;  
	 }      
	              
    // может нужно изменить параметры пользователя

    // теперь можно редактировать
    if (!$errors)
    {
      if ($new_discussion)
      {
         $res = dialog_edit_discussion($new_discussion, $options);
         if ($res['errors']) $errors = $errors + $res['errors'];
      }
      else $errors[] = 'Нет данных';
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
      if ($status_name == 'edit') $message_info = $options['edit-ok']; 
      if ($status_name == 'approved') $message_info = $options['approved-ok']; 
      if ($status_name == 'unapproved') $message_info = $options['unapproved-ok'];
      if ($status_name == 'closed') $message_info = $options['closed-ok'];
      if ($status_name == 'unclosed') $message_info = $options['unclosed-ok'];
       
      echo '<div class="comment-ok">' . $message_info . '</div>';       
   }    
   echo '<div class="break"></div>';
 //pr ($errors);
 //pr ($new_comment);
 
 ?>