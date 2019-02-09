<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// Главная страница форума

// выводятится ссылка на приватные дискусии пользователя, если есть
// выводятся активные дискуссии
// выводятся категории, если есть
// выводятся дискуссии без категорий, если есть
// выводится форма добавления дскуссии с 0-категорией, если разрешено

$error = false; // тут ошибки 
   

mso_head_meta('title', $options['name']); 
mso_head_meta('description', $options['desc'] ); 
// $flag_show_comments_js = true; // вывести в head js плагинов комментариев чтобы подключить comments_buttons

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
   {
     $fn = 'get_new_comment.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
     else 
       require($template_default_dir . $fn);     
   } 
   
   
 // для каждого комюзера своя главная
 $cache_key = 'dialog_main_' . $comuser_id;
 if ($options['cache_flag']) $out = mso_get_cache($cache_key);
 else $out = '';

 if ($out) echo $out;
 else 
 {

    //Выводим  заголовок и описание  ______________________________ 
   $out .= '<H2>' . $options['name'];
   if ($options['desc']) $out .= ' ' . $options['desc'];
   $out .= '</H2>';
   
   // получаем ссылку на приватные дискуссии
   if ($comuser_id) 
   {
     $discussions_private = dialog_get_discussions(array('private' => true , 'user_id' => $comuser_id , 'role_id' => $comuser_role) , $options);
     if ($discussions_private)
     {
        $new_private = '';
        $npc = 0;
        $pc = 0;
        foreach ($discussions_private as $pd) 
        {
           if ($pd['news'] or !$pd['watch']) $new_private = '<span class="disc_watch_status">' . $options['disc_news'] . '</span> '; 
           if (!$pd['watch']) $npc++;
           $pc++;
        }    
            
        // формируем ссылку на приватные дискуссии
        $out .= $new_private . '<a href = "' . $siteurl . $options['main_slug'] . '/' . $options['private_slug'] . '" title = "' . $options['desc_private'] . '">' . $options['title_private'] . ': ' . $pc . '</a>';
        if ($npc) $out .= ' (Новых: ' . $npc . ')';
        
     }
   
   }
   
 
    // выводим активные
    if ($options['count_activity'])
    {
      $discussions = dialog_get_discussions(array('count'=>$options['count_activity'], 'sort_field'=>'discussion_date_last_active' , 'sort_order'=>'desc', 'no_private' => true , 'user_id' => $comuser_id , 'role_id' => $comuser_role) , $options);
      if ($discussions)
      {
        $link_on_aktivity = '<span class="link_activ"><a href = "' . $siteurl . $options['main_slug'] . '/' . $options['all-discussions_slug']  . '/' . $options['activity_slug'] . '" title="' .  $options['link_activity_title'] .'">' . $options['link_activity'] . '</a></span>';
        $table_title = $options['title_activity'] . $link_on_aktivity; // будет заголовком таблицы
        
        $fn = 'out-discussions.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);         
      }
    }
    
    
 
   // получаем разделы
   $forums = dialog_get_categorys(array('disc_count'=>$options['cat_disc_count'], 'sort_field'=>'discussion_comments_count' , 'sort_order'=>'desc', 'user_id' => $comuser_id, 'role_id' => $comuser_role));   
   // есть разделы  выводим
   if ($forums)
   {
        if ($options['title_category']) $out .= '<H3>' . $options['title_category'] . '</H3>';
        $fn = 'out-categorys.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);       
   }


    // получим (вдруг есть такие) дискуссии с 0-й категорией
    // если дискуссии без категорий разрешены
    if ($options['discussion_free'])
    {
      $discussions = dialog_get_discussions(array('no_private' => true , 'user_id' => $comuser_id , 'category_id' => 0 , 'role_id' => $comuser_role) , $options);
      if ($discussions)
      {
        $table_title = $options['title_free_discussions']; // будет заголовком таблицы
        $fn = 'out-discussions.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);         
      }
    }
    
    
 	 if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
 	 
   echo($out);
 }


   
 	 // теперь нам нужно вывести форму добавления, если нужно
   if ($options['discussion_free'] and $comuser_id)
   {
  
      $form_title = $options['title_free_discussions_form'];
      $form_desc = $options['desc_free_discussions_form'];
      $new_discussion_flag = true; //учесть в форме что создается новая дискуссия
      $free_discussion_flag = true; //учесть в форме что создается новая дискуссия без категории
      $comment_id = false;
      $discussion_id = false;

      $fn = 'new_comment-form.php';
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
      else 
          require($template_default_dir . $fn);       
   } 	
   
   if (!$comuser_id) require($template_dir . 'form_login_register.php');
 	 
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