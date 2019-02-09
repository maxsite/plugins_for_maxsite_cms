<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**


 здесь получим новый комментарий, обработаем и отправим на добавление
 комментарий может отправляться из формы
 1. новой дискуссии без категории
 2. новой дискуссии из категории
 3. нового сообщения в дискуссии
 4. отредактированного сообщения
 5. новой приватной дискуссии

*/

 require ($plugin_dir . 'functions/modify_db.php');

 $errors = array();
 $new_comment = array();
 $new_comment['comment_discussion_id'] = 0;
 $status = '';
 $moderate = false;
 
 if (!isset($post['comment_discussion_id'])) $post['comment_discussion_id'] = 0;
 if (!isset($post['new_discussion_flag'])) $post['new_discussion_flag'] = false;
 if (!isset($post['free_discussion_flag'])) $post['free_discussion_flag'] = false;
 
 // члены приватной комнаты
 if (!isset($post['room_members'])) $post['room_members'] = false;
 else 
 {
	// пройдемся по массиву и составим массив из ID
	$arr_members = array(); // список всех где ON
	foreach ($post['room_members'] as $id_member=>$val)
		if ($val) $arr_members[] = $id_member;  
			
	$post['room_members'] = $arr_members;	 
 }
 
 if (!isset($post['comment_category_id'])) $new_comment['comment_category_id'] = 0;
 else $new_comment['comment_category_id'] = $post['comment_category_id'];	
	
 //может дискуссия без категории
 if ($post['free_discussion_flag']) $new_comment['comment_category_id'] = 0; 
 
 $discussion_id = false;
 
 if (!mso_checksession($post['comments_session']) ) $errors[] = 'Ошибка сессии! Обновите страницу!';

 // если не передали дискуссию (наверное нужно добавить новую) - то нужно подтвержение ее добавления
 if (!$post['comment_discussion_id'] and !$post['new_discussion_flag'] and !$post['comment_comment_id']) $errors[] = 'Ошибка дискуссии';
		
 if ($post['comment_discussion_id'])
 {
    $discussion_id = $post['comment_discussion_id'];
	$id = (int) $discussion_id;
	if ( (string) $discussion_id != (string) $id ) $id = false; // $comment_discussion_id не число
	if (!$id) $errors[] = 'Номер дискуссии не число';
	$new_comment['comment_discussion_id'] = $id;     
 }

 if (isset($post['comment_parent_id']) and $post['comment_parent_id'])
 {
    $comment_parent_id = $post['comment_parent_id'];
	$id = (int) $comment_parent_id;
	if ( (string) $comment_parent_id != (string) $id ) $id = false; // $comment_parent_id не число
	if (!$id) $errors[] = 'Номер родителя не число';
	$new_comment['comment_parent_id'] = $id;     
 }
    
    
 if ( !isset($post['comment_email']) or !$post['comment_email'] )
						$errors[] = 'Нужно указать Email';


 if ( !isset($post['comment_password']) or !$post['comment_password'] )
						$errors[] = 'Нужно указать пароль';


 $new_comment['comment_email'] = mso_strip($post['comment_email']);
 $new_comment['comment_password'] = mso_strip($post['comment_password']);

 // if ( !mso_valid_email($new_comment['comment_email']) ) $errors[] = 'Ошибочный Email';
  
 $new_comment['comment_ip'] = $_SERVER['REMOTE_ADDR'];
 $new_comment['comment_date'] = date('Y-m-d H:i:s');
		
 $new_comment['comment_content'] = $post['comments_content'];
 $new_comment['comment_parent_id'] = isset($post['comment_parent_id']) ? $post['comment_parent_id'] : '0'; 
 
 $new_comment['new_discussion_flag'] = $post['new_discussion_flag'];
 $new_comment['discussion_title'] = isset($post['discussion_title']) ? trim(strip_tags($post['discussion_title'])) : '';
 
 $result = dialog_prepare_new_comment($new_comment, $options); // подготовка контента коммента к добавлению
		
 if ($result['errors']) $errors[] = $result['errors'];
 else $new_comment['comment_content'] = $result['comment_content'];
		         

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

    // теперь можно комментарий добавить
    if (!$errors)
    {
      //сперва проверим - может нужно добавить дискуссию
      if ($post['new_discussion_flag'])
      {
        if (!$new_comment['discussion_title']) $errors[] = 'Укажите заголовок дискуссии';
        else
        {
           // добавим дискуссию
           
		       // выясним категорию дискуссии
           $res = dialog_add_discussion(array('discussion_user_id' => $comuser_id , 'discussion_category_id' => $new_comment['comment_category_id'] , 'discussion_title' => $new_comment['discussion_title'] , 'room_members' => $post['room_members']) , $options);

           if ($res['errors']) $errors = $errors + $res['errors'];
           else 
           {
             $new_comment['comment_discussion_id'] = $res['discussion_id'];
             $discussion = false; // получить дискуссию в функции добавления
           }  
        }
       } 
       // если все в порядке 
       if (!$errors)
       {
		    $new_comment['comment_user_id'] = $comuser_id;
		    $new_comment['role_id'] = $comuser_role;
		        
            // echo $new_comment['comment_id'];
            
            // наконец то добавим сообщение
            if (isset($new_comment['comment_id']))
            {
               $status = 'edit';
               $res = dialog_edit_comment($new_comment , $options);
            }   
            else
            {
               $status = 'add';
               $res = dialog_add_comment($new_comment , $options , $comuser , $discussion);
            } 
             
			 
            if ($res['errors']) $errors = $errors + $res['errors'];
			
            elseif ($status == 'add')
			{
			  if ($res['moderate']) $moderate = true;

			  // редирект на новый коммент
              if ($options['flag_redirect'])
			  {
				mso_redirect($options['goto_slug'] . dialog_get_url($discussion['discussion_id'] , $res['comment_id']));	    
			  }
			}  
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
       if ($status == 'add') echo '<div class="comment-ok">' . $options['add_ok'] . '</div>';
       elseif ($status == 'edit') echo '<div class="comment-ok">' . $options['edit-ok'] . '</div>';
       if ($moderate) echo '<div class="comment-ok">' . $options['moderate-ok'] . '</div>';
	   
   }   
   
   echo '<div class="break"></div>';
 //pr ($errors);
 //pr ($new_comment );
?>