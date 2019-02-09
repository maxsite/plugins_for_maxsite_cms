<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле в $out выводятся дискуссии в массиве $discussions
// также оформляется подписка или отписка на темы


// флаг - выводить ли кнопку Подписаться/Отписаться
if (!isset($flag_subscribe_button)) $flag_subscribe_button = false;

// обработаем управление подпиской на дискуссии
  if ($comuser_id and $flag_subscribe_button)
  {
    if ($post = mso_check_post(array('f_session_id', 'dialog_status_subscribe_submit')))
    {
		    $dis_id = mso_array_get_key($post['dialog_status_subscribe_submit']);
		    $upd_date = array ('watch_subscribe' => '1');
			  
			  $CI = & get_instance();
			        
			  $CI->db->where('watch_user_id', $comuser_id);
			  $CI->db->where('watch_discussion_id', $dis_id);
			  $res = ($CI->db->update('dwatch', $upd_date )) ? '1' : '0';
			
			  $CI->db->cache_delete_all();
			  mso_flush_cache(); // сбросим кэш
			
			  if (!$res) $errors = 'Ошибка БД при обновлении подписок';
			  else
			  {
			     $sub[$dis_id] = true;		 
			     echo '<div class="comment-ok">' . $options['subscribe-ok'] . '</div>'; 
			  }     
    }

    elseif ($post = mso_check_post(array('f_session_id', 'dialog_status_unsubscribe_submit')))
    {
		    $dis_id = mso_array_get_key($post['dialog_status_unsubscribe_submit']);
		    $upd_date = array ('watch_subscribe' => '0');
			  
			  $CI = & get_instance();
			        
			  $CI->db->where('watch_user_id', $comuser_id);
			  $CI->db->where('watch_discussion_id', $dis_id);
			  $res = ($CI->db->update('dwatch', $upd_date )) ? '1' : '0';
			
			  $CI->db->cache_delete_all();
			  mso_flush_cache(); // сбросим кэш
			
			  if (!$res) $errors = $options['error_db_subscribe'];
			  else
			  {
			     $sub[$dis_id] = false;		 
			     echo '<div class="comment-ok">' . $options['unsubscribe-ok'] . '</div>'; 
			  }     
    }

  }

$out .= '<form action="" method="post">' . mso_form_session('f_session_id');
        
$out .= '<table class="table_discussions">';
if (isset($discussions[0]['watch_comments_count']))
   $out .= '<tr><th>Дискуссия</th><th width="15%">Сообщений</th><th width="10%">Вы:</th><th width="10%">Просмотров</th><th width="15%">Последнее</th></tr>';
else 
   $out .= '<tr><th>Тема дискуссии</th><th width="15%">Сообщений</th><th width="10%">Просмотров</th><th width="15%">Последнее</th></tr>';

foreach ($discussions as $discussion)
{
  // инициализируем
  $watch_status = '';
  $status = '';
  $subscribe = '';
  $new_comments_out = '';
  $new_comment_url = '';
  $new_comment_link = '';
  $page_links = '';

  extract ($discussion);

  $out .= '<tr>';

  if ($comuser_id)
  {
     // статус просмотра дискуссии
     if (!$watch) $watch_status = $options['disc_new'];
     elseif ($news)
     {
        $watch_status = $options['disc_news'];
        // выведем кол-во новых комментов как ссылку на первый непросмотренный
        if (isset($new_comments[0]['comment_id']))
        {
           $new_comment_url = $siteurl . $options['goto_slug'] . '/disc/' . $discussion_id . '/comm/' . $new_comments[0]['comment_id'];
           $new_comment_link = '<a href="' . $new_comment_url . '" title="' . $options['goto_new_comments'] . '">' . $options['not_watch_count'] . '</a>';
           $new_comments_out = '<p>' . $new_comment_link . ' ' . count($new_comments) . '</p>';
        }      
     }   
     //else $watch_status = ''; // просмотрена
    if ($watch_status) $watch_status = '<span class="disc_watch_status">' . $watch_status . '</span> ';
    
    
     // статус дискуссии
     if ($discussion_parent_comment_id)
        $status .= '<span class="disc_parent"><a href="' . $siteurl . $options['goto_slug'] . '/disc/0/comm/' . $discussion_parent_comment_id . '" title="' . $options['parent_comment'] . '">' . $options['parent'] . '#' . $discussion_parent_comment_id . '</a></spav>';
     if (!$discussion_approved) $status .= '<span class="disc_need_approved">' . $options['need_approved'] . '</spav>';
     if ($discussion_closed) $status .= '<span class="disc_closed">' . $options['disc_closed'] . '</spav>';
     if ($discussion_private) $status .= '<span class="disc_private">' . $options['disc_private'] . '</spav>';
     if ($autor) $status .= '<span class="disc_you_autor">' . $options['you_autor'] . '</spav>';
     // подготовим статус к выводу
     if ($status) $status = '<span class="disc_status">' . $status . '</spav>';
     
     // кнопка подписки
     if(isset($subscribe) and $flag_subscribe_button)
     {
       if (isset($sub[$discussion_id])) $subscribe = $sub[$discussion_id];
       if ($subscribe) 
         $subscribe = '<br /><input name="dialog_status_unsubscribe_submit[' . $discussion_id  . ']" type="submit" value="' . $options['unsubscribe'] . '" class="comments_submit unsubscribe" title ="' . $options['unsubscribe'] . '">';
       else
         $subscribe = '<br /><input name="dialog_status_subscribe_submit[' . $discussion_id  . ']" type="submit" value="' . $options['subscribe'] . '" class="comments_submit subscribe" title ="' . $options['subscribe'] . '">';
     }
     else $subscribe = '';

  }
  
 
 if ($autor or ($comuser_role == 2) or ($comuser_role == 3))
         $edit = '<a href ="' . $siteurl . $options['edit_discussion_slug'] . '/' . $discussion_id . '" tite = "' . $options['edit_discussion'] . '"><img src="' . $template_url . 'images/edit.png" width="16" height="16" alt="' . $options['edit_discussion'] . '"/></a> '; 
 else $edit = ''; 
  
  
  $discussion_date_create = mso_page_date(date('Y-m-d H:i:s' , $discussion_date_create), 
									array(	'format' => 'j F Y', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	 
									
  $discussion_date_last_active = date('Y-m-d' , $discussion_date_last_active);
  /*
  $discussion_date_last_active = mso_page_date(date('Y-m-d H:i:s' , $discussion_date_last_active), 
									array(	'format' => 'j F Y', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	   
  */
  
  // если дискуссия не одобрена, сообщим об этом
  if (!$discussion_approved) $approved = $options['not_approved'] . ' '; else $approved = '';
  
  
  
  
  // выясним кол-во страниц дискуссии и выведем ссылки на каждую из них
  if ($comuser['profile_comments_on_page'])
  {
     $pages_count = (int) ($discussion_comments_count/$comuser['profile_comments_on_page']);
     if ( ($pages_count * $comuser['profile_comments_on_page']) < $discussion_comments_count) $pages_count = $pages_count + 1; 
  }
  else $pages_count = 1;
  if ($pages_count > 1) $page_links = dialog_get_pages_links($siteurl , $options['discussion_slug'] , $discussion_id , $pages_count);
  else $page_links = '';
  // добавим ссылку на первое новое сообщение
  if ($new_comment_url) $page_links =  '<a href ="' . $new_comment_url . '" title="' . $options['goto_new_comments'] . '">' . $options['first_new_coment'] . '</a> ' . $page_links;
  // подготовим для вывода
  if ($page_links) $page_links = '<span class="disc_pages_links">' . $page_links . '</span>';
  
  
  
  
  // если у дискуссии есть стиль внешнего вида
  if ($discussion_style_id == 1) $style = '<span class="red">!!!</span> ';
  else $style = '';
  
  $out .= '<td class="disc_title">' . $watch_status . $style . $approved . '<a href="' . $siteurl . $options['discussion_slug'] . '/' . $discussion_id . '" title="' . $discussion_desc . '">' . $discussion_title . '</a>'. $edit . '<p>' . dialog_profile_link($profile_user_id, $profile_psevdonim, $options['profile_slug'] , $siteurl) . ' : ' . $discussion_date_create . $status . '</p>' . $page_links . '</td>';
  
  // откорректируем период длительности дискуссии
  if ($options['discussion_lenght']<1) $options['discussion_lenght']=1;
  $plotnost = ($discussion_p*$options['discussion_lenght']);
  $out .= '<td class="comm_count">' . $new_comments_out . '<p>Всего: ' . $discussion_comments_count . '</p><p>В день: ' . $plotnost . '</p></td>';
  
  if (isset($watch_comments_count))  $out .= '<td>' . $watch_comments_count . '</td>';
  
  $out .= '<td class="views">' . $discussion_view_count . '</td>';
  $out .= '<td class="comm_last">' . dialog_profile_link($discussion_last_user_id, $last_user_psevdonim, $options['profile_slug'] , $siteurl) . '<br />' . $discussion_date_last_active . $subscribe . '</td>';
  
  
  $out .= '</tr>';
}
$out .= '</table>';

				$out .= '</form>';  
     
?>