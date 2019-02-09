<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// обрабатываем ссылку на коммент, перенаправляя на соответствующую страницу пагинации в дискуссии

// извлечем из урла discussion_id и comment_id 
// осуществим редирект на страницу с комментом в дискуссии

if ( ($segment2 == 'disc') and (mso_segment(4) == 'comm'))
{

    $discussion_id = mso_segment(3);
    $comment_id = mso_segment(5);
    $error = true;
    $comment_page_no = false;
    /* вычислим номер страницы пагинации на которой находится коммент
       в рамках кол-ва сообщений на странице для этого пользователя
    */
    $comment_page_no = dialog_get_comment_page($comment_id , $comuser['profile_comments_on_page'] , $discussion_id , $comuser_role , $comuser_id);
    
    // если ошибка - нам нужно проверить не перенесен ли коммент в другую дискуссию
    // найдем комент по id и его дискуссию    
    if (!$comment_page_no)
    {
      // получим номер дискуссии коммента
      $new_discussion_id = dialog_get_comment_discussion_id($comment_id);
      // если номер дискуссии новый
      if ($discussion_id != $new_discussion_id) 
      {
         // переопределим дискуссию
         $discussion_id = $new_discussion_id;
         $comment_page_no = dialog_get_comment_page($comment_id , $comuser['profile_comments_on_page'] , $discussion_id , $comuser_role , $comuser_id);
      }   
    }
        
    if ($comment_page_no)
    { 
       echo '<H1>' . $options['goto_title'] . '</H1>';

       if ($comment_page_no == 1 ) $url = $options['discussion_slug'] . '/' . $discussion_id . '#key' . $comment_id;
       else $url = $options['discussion_slug'] . '/' . $discussion_id . '/next/' . $comment_page_no . '#key' . $comment_id;
       
       echo $options['redirect_title'] . '<a href="' . $siteurl . $url . '">' . $siteurl . $url . '</a>';
       
       // редирект на эту страницу пагинации в категории и на этот комент
       mso_redirect($url);
       $error = false;
    }   
    else $error = $options['out_of_comment'];

}
else $error = 'не тот сегмент';



 
?>