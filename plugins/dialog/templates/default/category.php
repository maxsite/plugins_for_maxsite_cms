<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница категории


$category = dialog_get_category($segment2);
   
if ($category)
{
   mso_head_meta('title', $category['category_title']); 
   mso_head_meta('description', $category['category_desc'] );

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


 // для каждого комюзера свой вид категории
 $cache_key = 'dialog_' . $comuser_id . '_' . $category['category_id'];
 if ($options['cache_flag']) $out = mso_get_cache($cache_key);
 else $out = '';

 if ($out) echo $out;
 else 
 {
 
   // хлебные крошки
    $out .= '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $category['category_title'] . '</div>'; 

   //Выводим  заголовок и описание  ______________________________ 
   $out .= '<H1>' . $category['category_title']  . '</H1>';
   if ($category['category_desc']) $out .= '<H3>' . $category['category_desc']  . '</H3>';
 
   
    // получим темы дискуссий
    $discussions = dialog_get_discussions(array('user_id' => $comuser_id , 'category_id' => $category['category_id'] , 'role_id' => $comuser_role), $options);
    if ($discussions)
    {
       $fn = 'out-discussions.php'; 
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
         require($template_dir . $fn);
       else 
         require($template_default_dir . $fn);         
    }

 	 if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
 	 
   echo($out);
 }


   
 	 // теперь нам нужно вывести форму добавления, если нужно
   if ($comuser_id) // если кто-то залогинен
   {
      // зададим параметры вывода формы
      $form_title = $options['title_discussions_form'];
      $form_desc = $options['desc_discussions_form'];
      $new_discussion_flag = true; //учесть в форме что создается новая дискуссия
      $free_discussion_flag = false; //учесть в форме что создается новая дискуссия без категории
      $comment_id = false;
      $discussion_id = false;
      $category_id = $category['category_id'];
      
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
} 
else $error = $options['out_of_category']; 

?>