<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница репутации (благодарностей) пользователя


   // получаем благодарности
   
   $dankes = dialog_get_dankes($edit_profile['profile_user_id']);
   echo '<div class="page_user_dankes">'; 
   echo '<ul>';
   // есть благодарности - выводим
   if ($dankes)
   {    
      foreach ($dankes as $danke)
      {
         $date = _dialog_date('j F Y' , $danke['gud_date']);	
         $user_link = dialog_profile_link($danke['gud_user_id'], $danke['profile_psevdonim'], $options['profile_slug'] , $siteurl, $options['profile']);
         $comment_link = '<a href="' . $siteurl . $options['goto_slug'] . '/disc/' . $danke['comment_discussion_id'] . '/comm/' . $danke['gud_comment_id'] . '" title ="' . $options['goto_comment'] . '">-></a>';         
          $discussion_link = '<a href="' . $siteurl . $options['discussion_slug'] . '/' . $danke['comment_discussion_id'] . '" title="' . $options['discussion_page'] . '">' . $danke['discussion_title'] . '</a>';
         echo '<li>';   
         echo '<H3>' . $user_link . $options['breadcrumbs_razd'] . $discussion_link . ' (' . $date . ')<span class="right">' . $comment_link . '</span></H3>'; 
         
           $comment_content = mso_hook('comments_content', $danke['comment_content']);  
           $comment_content_small = mso_str_word($comment_content, $counttext = 10, $sep = ' ');
         
         echo '<span class="full_comment" id="full' . $danke['gud_comment_id'] . '">' . $comment_content . '</span>';
       //  echo '<span class="small_comment" id="small'  .$danke['gud_comment_id'] . '">' . $comment_content_small . '</span>';
     //   echo '<span class="right">' . $comment_link . '</span>';
         echo '</li>';   
      }
   }
   echo '</ul>';
   echo '</div>'; 

 
?>