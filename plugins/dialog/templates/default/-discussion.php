<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница дискуссии форума

// получим дискуссию

$out = '';
$discussion = dialog_get_discussion(array('user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role ) , $options);

if ($discussion)
{

    // выясним - нужно ли выводить форму добавления
     if (!$comuser_id) $show_form = false;
     elseif ( ($comuser_role == 2) or ($comuser_role == 3)) $show_form = true;
     elseif($discussion['discussion_closed']) $show_form = false;
     else $show_form = true; 


   mso_head_meta('title', $discussion['discussion_title']); 
   mso_head_meta('description', $discussion['discussion_desc'] ); 
 // $flag_show_comments_js = true; // вывести в head js плагинов комментариев 
   
   $fn = 'head.php'; 
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
   else 
       require($template_default_dir . $fn); 
       
   // получаем сообщения
   $gc_par = array('count'=>$comuser['profile_comments_on_page'] ,'user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role , 'sort_field'=>'comment_date_create' , 'sort_order'=>'asc');
   if ($discussion['discussion_parent_comment_id']) $gc_par['parent_comment_id'] = $discussion['discussion_parent_comment_id'];
   
   
   $comments = dialog_get_сomments($gc_par , $pagination ,$options);
   
   
  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');

  echo NR . '<div class="dialog_page">' . NR;

  $fn = 'do.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
      require($template_dir . $fn);
  else 
      require($template_default_dir . $fn); 
         
  echo NR . '<div class="dialog_page_content">' . NR;

// Обработка входящих данных_____________________________________________________________ 


  // ф-я обработки изменения статуса дискуссии
    if ($post = mso_check_post(array('f_session_id', 'dialog_status_submit')) )
    {
        $fn = 'get_new_discussion.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
        require($template_default_dir . $fn); 
              
       // обновим дискуссию
       $discussion = dialog_get_discussion(array('user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role ) , $options);
    }

  // форма обработки изменения опций представления для просмотра пользователем
    elseif ($post = mso_check_post(array('f_session_id', 'dialog_status_profile_submit')) )
    {
       $flag_redirect = true;
        $fn = 'get_new_profile_status.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);        
       // обновим профайл
       $comuser = dialog_get_login_profile($options);
       if ($comuser)
       {
          $comuser_id = $comuser['comusers_id'];
          $comuser_role = $comuser['profile_user_role_id'];
        }
        else
        {
           $comuser_id =0;
           $comuser_role = 0;
           $comuser['profile_vid'] = 0;
           $comuser['profile_comments_on_page'] = $options['comments_on_page'];
        } 
        
            // получаем сообщения
            $comments = dialog_get_сomments($gc_par , $pagination , $options);     
    }
    
    // обработаем, если был запрос на добавление
    elseif ($show_form and ($post = mso_check_post(array('comments_session', 'dialog_submit', 'comments_content'))) )
    {
        $fn = 'get_new_comment.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);  
                   
          // переполучим дискуссию, если изменяли 
         $discussion = dialog_get_discussion(array('user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role ) , $options);
    }      
    else // просто просмотр (нет нужного post) - добавим просмотр
    {
       // добавим просмотр пользователем
       if ($comuser_id) dialog_add_wath(array( 'user_id' => $comuser_id , 'discussion_id' => $discussion['discussion_id'] , 'role_id' => $comuser_role) );
       // добавим (увеличим) вообще просмотр этой дискуссии
       dialog_view_count_first($discussion['discussion_id']);       
    }
    
    
// вывод  _____________________________________________________________ 
 

  // хлебные крошки
  if ($discussion['category_slug'])
  { 
    $category_link = '<a href="' . $siteurl . $options['main_slug'] . '/' . $discussion['category_slug'] . '">' . $discussion['category_title'] . '</a>' . $options['breadcrumbs_razd'];
  }
  else $category_link = '';
  $out .= '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $category_link . $discussion['discussion_title'] . '</div>';

   //Выводим  заголовок и описание  ______________________________ 
   $out .= '<H1>' . $discussion['discussion_title']  . '</H1>';
   if ($discussion['discussion_desc']) $out .= '<H3>' . $discussion['discussion_desc']  . '</H3>';
  

     if (!$discussion['discussion_approved']) $out .= '<div class="yellow">' . $options['not_approved']  . '</div>';
     if ($discussion['discussion_closed']) $out .= '<div class="bg-gray">' . $options['discussion_closed']  . '</div>';
     if ($discussion['discussion_private']) 
     {
        $members = dialog_get_names($options, $discussion['members']);
        $memb = '';
        foreach ($members as $uid) 
          $memb .= dialog_profile_link($uid['profile_user_id'], $uid['profile_psevdonim'], $options['profile_slug'] , $siteurl, $options['profile']) . ' ';
        $out .= '<div class="bg-gray">' . $options['discussion_private']  . '. ' . $options['private_members'] . ': ' . $memb . '</div>';
     }  
     
     
      
      // сылка вернуться в категорию
        if (trim($discussion['category_slug']))
           $return = '<a href="' . $siteurl . $options['main_slug'] . '/' . $discussion['category_slug'] . '" title="' . $options['return_category'] . ' ' .$discussion['category_title'] . '">' . $options['return_category'] . '</a>';
        else
           $return = '<a href="' . $siteurl . $options['main_slug'] . '" title="' . $options['return_main'] . '">' . $options['return_main'] . '</a>';       
        


       $out .= '<table class="disc_menu_top"><tr>';  
       
       $out .= '<td class="left">'; 
       $out .= $return; //return пригодится еще внизу 
 
     // если кто-то залогинен
     if ($comuser_id)
     {
       // действия
       // если есть доступ к изменению - покажем ссылки на редактирование дискуссии
        if ($discussion['autor'])
           $out .= ' | <a href ="' . $siteurl . $options['edit_discussion_slug'] . '/' . $discussion['discussion_id'] . '">' . $options['do_edit'] . '</a>';
        elseif (($comuser_role == 2) or ($comuser_role == 3))
           $out .= ' | <a href ="' . $siteurl . $options['edit_discussion_slug'] . '/' . $discussion['discussion_id'] . '">' . $options['do_moderate'] . '</a>';
        
        // покажем ссылку на форму ответить
        $out .= ' | <a href="javascript: void(0);" title="' . $options['to_form_title'] . '" onclick="javascript:addNew();">' . $options['answer'] . '</a>';
      }  
		  $out .= '</td>';  				   
        
 // выведем кнопки для управления статусом дискуссии 
        $out .= '<td class="right">'; 
         
        if ($comuser_id)  
        {
        $out .= '<form action="" method="post" class="discussion-form">' . mso_form_session('f_session_id');
				$out .= '<input type="hidden" value="' . $comuser['comusers_email'] . '" name="comment_email">';
				$out .= '<input type="hidden" value="' . $comuser['comusers_password'] . '" name="comment_password">';
				$out .= '<input type="hidden" name="comment_password_md" value="1">';
        $out .= '<input type="hidden" name="discussion_id" value="' . $discussion['discussion_id'] . '">';  
           
           if ( ($comuser_role == 2) or ($comuser_role == 3) )
           {
             if ($discussion['discussion_approved']) 
                $out .= '<input name="dialog_status_submit[unapproved]" type="submit" value="' . $options['form_unapproved'] . '" class="comments_submit unapproved">'; 
             else
                $out .= '<input name="dialog_status_submit[approved]" type="submit" value="' . $options['form_approved'] . '" class="comments_submit approved">';
           

				     if ($discussion['discussion_closed'] == '1')
				          $out .= '<input type="submit" name="dialog_status_submit[unclosed]" class="submit" value="' . $options['unclosed'] . '">';  
				     else
				          $out .= '<input type="submit" name="dialog_status_submit[closed]" class="submit" value="' . $options['closed'] . '">'; 
           }    
				          
				   
				   if ($discussion['watch_subscribe'] == '0')
				          $out .= '<input type="submit" name="dialog_status_profile_submit[subscribe]" class="submit" value="' . $options['subscribe'] . '">';  
				   else
				          $out .= '<input type="submit" name="dialog_status_profile_submit[unsubscribe]" class="submit" value="' . $options['unsubscribe'] . '">'; 

 // выведем кнопки для управления внешним видом дискуссии         

				   if ($comuser['profile_vid'] == '1')
				          $out .= '<input type="submit" name="dialog_status_profile_submit[vid0]" class="submit" value="' . $options['vid_min'] . '">';  
				   else
				          $out .= '<input type="submit" name="dialog_status_profile_submit[vid1]" class="submit" value="' . $options['vid_max'] . '">'; 

				          
				   if ($comuser['profile_comments_on_page'] == 10) $disabled = ' disabled ';
           else $disabled = '';      
				   $out .= '<input type="submit" name="dialog_status_profile_submit[10]" class="submit" value="10"' . $disabled . '>';  
				   if ($comuser['profile_comments_on_page'] == 20) $disabled = ' disabled ';
           else $disabled = '';  
				   $out .= '<input type="submit" name="dialog_status_profile_submit[20]" class="submit" value="20"' . $disabled . '>';  
				   if ($comuser['profile_comments_on_page'] == 30) $disabled = ' disabled ';
           else $disabled = '';  
				   $out .= '<input type="submit" name="dialog_status_profile_submit[30]" class="submit" value="30"' . $disabled . '>';  
				   // кнопка смены шрифта
			     $out .= '<input type="button" class="d_button_font" id="d_font_button" value="˄A˅"  title="' . $options['font_size_mody'] . '"onclick="javascript:font('.$comuser_id.');">'; 
		//	     $out .='<div id="r"></div>';
				   $out .= '</form>';  
       }
       else $out .= $options['only_registered'];
			 
			 $out .= '</td>';  				   

   $out .= '</tr></table>'; 				    

   echo $out;


   // есть сообщения - выводим
   if ($comments)
   {
      
      if ($options['title_comments']) echo $options['title_comments'];
      if ($show_form) $flag_replay = true;
      $flag_discussion = true; // флаг что мы в искуссии
      if ($pagination) mso_hook('pagination', $pagination);
      //$out = '';

      // кнопки навигации
   
      $navi_block = '<span class="navi_button_left">   
        <a href="#dialog-do" title = "' . $options['up'] . '"><img src="' . $template_url . 'images/up.png" alt="' . $options['up'] . '"></a><a href= "#comments_content" title = "' . $options['down'] . '"><img src="' . $template_url . 'images/down.png" alt="' . $options['down'] . '"></a></span><span class="navi_button_right">';   
       
      if ( ($pagination['maxcount']>1) and (mso_current_paged() !=1)) $navi_block .= 
         '<a href="' . $siteurl . $options['discussion_slug'] . '/' . $discussion['discussion_id'] . '" title = "' . $options['first'] . '"><img src="' . $template_url . 'images/first.png" alt="' . $options['first'] . '"></a>';
   
     if ( ($pagination['maxcount']>1) and (mso_current_paged() != $pagination['maxcount'])) $navi_block .= 
        '<a href="' . $siteurl . $options['discussion_slug'] . '/' . $discussion['discussion_id'] . '/' . $pagination['next_url'] . '/' . $pagination['maxcount'] . '" title = "' . $options['last'] . '"><img src="' . $template_url . 'images/last.png" alt="' . $options['last'] . '"></a>
        ';
      $navi_block .= '</span>'; 

      // подключим выбранный вид вывода комментариев, что в массиве $comments
        $fn = 'out-comments' . $comuser['profile_vid'] . '.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);       
      // echo $out;
      
         // нижнее меню дискуссии
      echo '<span class="disc_menu_buttom">'; 
      echo $return;
      echo ' | <a href="#dialog-do" title = "' . $options['disc_up_title'] . '">' . $options['disc_up'] . '</a>';
      echo '</span>';
   
   
      if ($pagination) mso_hook('pagination', $pagination);
      
   }


 	 // теперь нам нужно вывести форму добавления, если нужно
   if ($show_form)
   {
      // выведем якорь
      $form_title = $options['title_new_comment_form'];
      $form_desc = $options['desc_new_comment_form'];
      $new_discussion_flag = false; //учесть в форме что создается новая дискуссия
      $free_discussion_flag = false; //учесть в форме что создается новая дискуссия без категории
      $discussion_id = $discussion['discussion_id'];
      $comment_id = false;
        $fn = 'new_comment-form.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);      
   } 
   else 
   {
        $fn = 'form_login_register.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn); 
   }         	 
echo NR . '</div><!-- class="dialog_page_content" -->' . NR;

$fn = 'posle.php'; 
if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
    require($template_dir . $fn);
else 
    require($template_default_dir . $fn);
       
// конец вывода
 echo NR . '</div><!-- class="dialog_page" -->' . NR;
require(getinfo('shared_dir') . 'main/main-end.php');

 $error = false;
}
else $error = $options['out_of_discussion']; 

 
?>