<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле в $out выводятся сообщения что в массиве $comments
// файл предназначен для вывода всех коментариев пользователя с пагинацией

if (!isset($flag_replay)) $flag_replay = false;


foreach ($comments as $comment)
{
  extract ($comment);
  
  //$comment_content = mso_comments_content($comments_content) ;  
  $comment_content = mso_hook('comments_content', $comment_content);  

  
  $comment_date = _dialog_date('j F Y в H:i:s' , $comment_date_create);	 
  
	$comment_status = '';
	if ($comment_deleted)	$comment_status[] = $options['comment_deleted'];
	if (!$comment_approved)	$comment_status[] = $options['not_approved'];
	if ($comment_spam)	$comment_status[] = $options['spam'];
	// проверен/не проверен показываем только администраторам и модераторам
	if ( ($comment_check != '1') and ( ($comuser_role==2) or ($comuser_role==3))) $comment_status[] = $options['not_spam_check'];
  if ($comment_status) $comment_status = '<span class="red">' . implode(". " , $comment_status) . '</span>';
  else $comment_status = '';
							
									
  // если просматривает автор, модератор или администратор - то показывать ссылку на едактирование коммента
  if ( ($edit_profile['profile_user_id'] == $comment_creator_id) and ( (time()-$comment_date_create)<$options['allow_edit_time'] ) )
       $comment_action = '<a href ="' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '" title = "' . $options['do_edit'] . '"><img src="' . $template_url . 'images/edit.png" alt="' . $options['do_edit'] . '"></a> '; 
  elseif (($comuser_role == 2) or ($comuser_role == 3))
       $comment_action = '<a href ="' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '" title = "' . $options['do_moderate'] . '"><img src="' . $template_url . 'images/edit.png" alt="' . $options['do_moderate'] . '"></a> '; 
  else $comment_action = '';		
  
  // если нет флага ответа
  if (!$flag_replay)
    // выводим ссылку перейти к комменту
    $comment_action .= ' <a href="' . $siteurl . $options['goto_slug'] . '/disc/' .  $comment_discussion_id . '/comm/' . $comment_id . '" title="' . $options['goto_comment'] . '"><img src="' . $template_url . 'images/goto.png" alt="' . $options['goto_comment'] . '"></a>';
  // если кто-то залогинен и флаг ответа, выводим ссылку "Ответить"
  elseif ($comuser_id)
       $comment_action .= ' <a href="#form">' . $options['answer'] . '</a>';  
  					 
  $discussion_link = '<a href = "' . $siteurl . $options['discussion_slug'] . '/' . $comment_discussion_id . '">' . $discussion_title . '</a>';
  
  $out .= '<table width=100%>';
  $out .= '<tr><td><div class="comment_top">' . $comment_date . ' >> ' . $discussion_link . '<span class="right">' . $comment_action . '</span></div></td></tr><tr><td>' . $comment_status .
           $comment_content . '</td></tr>';
  $out .= '</table>';

}

     
?>