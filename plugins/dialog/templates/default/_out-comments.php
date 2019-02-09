<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле в $out выводятся сообщения что в массиве $comments

$out .= '<table width=100%>';

foreach ($comments as $comment)
{
  extract ($comment);
  $comment['users_avatar_url'] = false;
  $comment['users_email'] = false;
    $comment_content = mso_hook('comments_content', $comment_content);  

  $out .= '<tr id="' . $comment_id . '">';
  
  $comment_date = mso_page_date($comment_date_create, 
									array(	'format' => 'j F Y в H:i:s', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	 
									 
  $register_date = mso_page_date($profile_date_first_visit, 
									array(	'format' => 'j F Y', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	  
									
  // если просматривает автор, модератор или администратор - то показывать ссылку на едактирование коммента
  if ($autor) 
       $comment_action = '<a href = "' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '">' . $options['do_edit'] . '</a>';
  elseif (($comuser_role == 2) or ($comuser_role == 3))
       $comment_action = '<a href = "' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '">' . $options['do_moderate'] . '</a>';		
  else $comment_action = '';							 

  
  // если кто-то залогинен, выводим ссылку "Ответить"
  if ($comuser_id)
       $comment_action .= ' <a href="#form">' . $options['answer'] . '</a>';
  
  $out .= '<td width=25%>';
  $out .=  
           mso_avatar($comment , '') . '<br />' . 
           dialog_profile_link($profile_user_id, $profile_psevdonim, $options['profile_slug'] , $siteurl) . '<br />' . 
           $register_date . '<br />' .
           'Кол-во: ' . $profile_comments_count . '<br />' .
           'Рейтинг: ' . $profile_rate
           ;
  
  $out .= '</td>';
  
  $out .= '<td>';
  
  $out .= '<table width=100%>';
  $out .= '<tr><td>' . $comment_date . '</td></tr><tr><td>' .
           $comment_content . '</td></tr><tr><td>' .
           $profile_podpis . '<span class="right">' . $comment_action . '</span></td></tr>';
  $out .= '</table>';
  
  $out .= '</td>';

  $out .= '</td>';
  
  $out .= '</tr>';
}
$out .= '</table>';

     
?>