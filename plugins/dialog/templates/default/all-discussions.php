<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
  // страница вывода всех дискуссий, отсортированных заданным способом
   
  // варианты сортировки
  $fn = 'sortable-discussions.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
     require($template_default_dir . $fn); 

$allow_sort_field = true;  
  
$sort_field = 'discussion_date_create'; 

$segment3 = mso_segment(3);

if ($segment3 == 'next') $segment3 = '';

if (!$segment3) $segment3 = 'date';


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

   mso_head_meta('title', $options['name'] . ' >> ' . $options['all-discussions'] . ' >> ' .  $sortable_title); 
   mso_head_meta('description', $options['desc'] ); 

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
         
  // хлебные крошки
    echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['all-discussions'] . '</div>';
   //Выводим  заголовок и описание  ______________________________ 
   
   echo '<H1>' . $options['all-discussions'] . '. ' . $sortable_desc . '.</H1>';

   echo '<div class="sort_navi">' . $options['sortable'];
   foreach ($array_fields as $slug => $sortable)
   {
     if ($slug != $segment3)
        echo '<a href ="' . $siteurl . $options['main_slug'] . '/' . $options['all-discussions_slug'] . '/' . $slug . '" title ="' .  $sortable['desc'] . '">' . $sortable['title'] . '</a> | '; 
     else 
        echo $sortable['title'] . ' | '; 
   }   
   echo '</div>'; 


   $par = array('user_id' => $comuser_id , 'role_id' => $comuser_role);
   // может нужно сортировать?
   if ($segment3)
   {
      $par['sort_field'] = $array_fields[$segment3]['sort_field'];
      $par['sort_order'] = $array_fields[$segment3]['sort_order'];
   }
 
   
    // получим темы дискуссий
    $discussions = dialog_get_discussions($par , $options);
    if ($discussions)
    {
        $flag_subscribe_button = true; // флаг о том что ужно выводить Пописаться/Отписаться
        $out = '';
        // вывод дискуссий, что в массиве $discussions
        
       $fn = 'out-discussions.php'; 
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
           require($template_dir . $fn);
       else 
           require($template_default_dir . $fn);
                 
       echo $out;
    }
    
    $error = false; 
 
   $fn = 'posle.php'; 
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
   else 
     require($template_default_dir . $fn);
     
  // конец вывода
  echo NR . '</div><!-- class="dialog_page" -->' . NR;
  require(getinfo('shared_dir') . 'main/main-end.php');

}
else $error = $options['error_sortable']; 

?>