<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница вывода всех комментов, отсортированных заданным способом

  // варианты сортировки заданы массивом в подключаемом ниже файле sortable-comments.php
   
  $fn = 'sortable-comments.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
     require($template_default_dir . $fn); 

  
$allow_sort_field = true;  
  
$sort_field = 'comment_date_create'; 

$segment3 = mso_segment(3);

if ($segment3 == 'next') $segment3 = '';

if (!$segment3) $segment3 = 'undate';


if ($segment3)
   if (isset($array_fields[$segment3]))
      $allow_sort_field = true; 
   else 
      $allow_sort_field = false;
else $allow_sort_field = true;  



if ($allow_sort_field)
{

   if ($segment3)
   { 
      $sortable_title = $array_fields[$segment3]['title'];
      $sortable_desc = $array_fields[$segment3]['desc'];
   }   
   else
   {
      $sortable_title = '';
      $sortable_desc = '';
   }  

   // получаем сообщения
   $par = array('count'=>$comuser['profile_comments_on_page'] ,'user_id' => $comuser_id , 'role_id' => $comuser_role);
   // может нужно сортировать?
   if ($segment3)
   {
      $par['sort_field'] = $array_fields[$segment3]['sort_field'];
      $par['sort_order'] = $array_fields[$segment3]['sort_order'];
   }   
   
   $comments = dialog_get_сomments($par , $pagination, $options); 

  mso_head_meta('title', $options['name'] . ' >> ' .$options['all-comments']  . ' >> ' . $sortable_title); 
  mso_head_meta('description', $options['desc']   . ' >> ' . $sortable_desc);
   
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
 
  // если есть post то обработка изменения опций просмотра пользователем
    if ($post = mso_check_post(array('f_session_id', 'dialog_status_profile_submit')) )
    {
        $fn = 'get_new_profile_status.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
           require($template_dir . $fn);
        else 
           require($template_default_dir . $fn);     
               
       // обновим профайл
       $comuser = dialog_get_login_profile($options);
    }
    

   
  // хлебные крошки
    echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['all-comments'] . '</div>';
    
   //Выводим  заголовок и описание  ______________________________ 
   echo '<H1>' . $options['all-comments']  . '. ' . $sortable_desc . '.</H1>';


   // выводим навигацию по сортировке
   echo '<div class="sort_navi">' . $options['sortable'];
   foreach ($array_fields as $slug => $sortable)
   {
     if ($slug != $segment3)
        echo '<a href ="' . $siteurl . $options['main_slug'] . '/' . $options['all-comments_slug'] . '/' . $slug . '" title ="' .  $sortable['desc'] . '">' . $sortable['title'] . '</a> | '; 
     else 
        echo $sortable['title'] . ' | '; 
   }   
   echo '</div>'; 




   
   // может нужно сортировать?
   if ($segment3)
   {
      $par['sort_field'] = $array_fields[$segment3]['sort_field'];
      $par['sort_order'] = $array_fields[$segment3]['sort_order'];
   }
     
     
   // есть комменты -  выводим
   if ($comments)
   {
   
     // если кто-то залогинен
     if ($comuser_id)
     {
        $out = '<div class="actions">';
        $out .= '<form action="" method="post" class="discussion-form">' . mso_form_session('f_session_id');
				$out .= '<input type="hidden" value="' . $comuser['comusers_email'] . '" name="comment_email">';
				$out .= '<input type="hidden" value="' . $comuser['comusers_password'] . '" name="comment_password">';
				$out .= '<input type="hidden" name="comment_password_md" value="1">';
           
        $out .= '<span class="right">'; 
				   
        $fn = 'buttons_out_comments.php'; // кнопки управления видом дискуссии
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
           require($template_dir . $fn);
        else 
           require($template_default_dir . $fn); 
      
				$out .= '</span>';  				   
        
				$out .= '</form>';  
        $out .= '</div>'; 
        
           echo $out;   

       }
   
   
      echo $options['title_comments'];
      if ($pagination) mso_hook('pagination', $pagination);
      $out = '';
      
      // вывод комментариев в $out заданным видом
      $fn = 'out-comments' . $comuser['profile_vid'] . '.php'; 
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
         require($template_dir . $fn);
      else 
         require($template_default_dir . $fn); 
                      
      echo $out;
      
      if ($pagination) mso_hook('pagination', $pagination);
   }
   
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
else $error = $options['error_sortable']; 

 
?>