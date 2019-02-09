<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница добавления новой приватной дискуссии

   
if ($comuser_id)
{
   mso_head_meta('title', $options['new_private_disc']); 
   mso_head_meta('description', $options['new_private_disc'] );

   $fn = 'head.php'; 
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
   else 
     require($template_default_dir . $fn);
     
   // Начало вывода_______________________________________________________________
   require(getinfo('shared_dir') . 'main/main-start.php');
   echo NR . '<div class="dialog_page">' . NR;

   $fn = 'do.php'; 
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
   else 
     require($template_default_dir . $fn); 
       
   echo NR . '<div class="dialog_page_content">' . NR;

   // обработаем, если был запрос на добавление
   if ( $post = mso_check_post(array('comments_session', 'dialog_submit', 'comments_content')) )
          require ($template_dir . 'get_new_comment.php'); 

 
   // хлебные крошки
    echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['new_private_disc'] . '</div>'; 

   //Выводим  заголовок и описание  ______________________________ 
   echo '<H1>' . $options['new_private_disc']  . '</H1>';


 	 // теперь нам нужно вывести форму добавления

      // зададим параметры вывода формы
      $form_title = $options['title_private_discussions_form'];
      $form_desc = $options['desc_private_discussions_form'];
      $new_discussion_flag = true; //учесть в форме что создается новая дискуссия
      $free_discussion_flag = true; //учесть в форме что создается новая дискуссия без категории
      $comuser_in_room_id = true; //учесть в форме что создается новая приватная дискуссия
      
      $comment_id = false;
      $discussion_id = false;
      $category_id = false;
      
      $fn = 'new_comment-form.php'; 
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
      else 
        require($template_default_dir . $fn);      
   } 	
   else
   { 
      // призыв залогиниться или зарегистрироваться
      $fn = 'form_login_register.php'; 
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
      else 
        require($template_default_dir . $fn);   
   }
 	 
 $error = false; // тут ошибки невозможны
 
echo NR . '</div><!-- class="dialog_page_content" -->' . NR;

$fn = 'posle.php'; 
if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
   require($template_dir . $fn);
else 
   require($template_default_dir . $fn);
     
// конец вывода
echo NR . '</div><!-- class="dialog_page" -->' . NR;
require(getinfo('shared_dir') . 'main/main-end.php');


?>