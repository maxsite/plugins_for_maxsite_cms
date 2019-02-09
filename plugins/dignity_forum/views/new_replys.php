<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// загружаем начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CI
$CI = & get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать сайтбар
$forum->hide_sidebar();

// меню
$forum->menu();

// загружаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
if ( !isset($options['use_admin_note_as_status']))  $options['use_admin_note_as_status'] = false;

$id = mso_segment(3);
// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// готовим пингацию
$pag = array();
$pag['limit'] = 10;
$CI->db->select('dignity_forum_reply_id');
$CI->db->from('dignity_forum_reply');

if (mso_segment(3) && mso_segment(3) != 'next')
{
	if (mso_segment(3) == getinfo('comusers_id'))
	{
		$CI->db->where('dignity_forum_reply_comusers_id', getinfo('comusers_id'));
	}
	elseif (mso_segment(3) == getinfo('users_id'))
	{
		$CI->db->where('dignity_forum_reply_users_id', getinfo('users_id'));
	}
	else
	{
		if ($id)
		{
			$CI->db->where('dignity_forum_reply_comusers_id', $id);
		}
	}
	
}

$query = $CI->db->get();
$pag_row = $query->num_rows();
if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']);

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}

// берём ответы из базы
$CI->db->from('dignity_forum_reply');
$CI->db->order_by('dignity_forum_reply_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_reply.dignity_forum_reply_comusers_id', 'left');
$CI->db->join('users', 'users.users_id = dignity_forum_reply.dignity_forum_reply_users_id', 'left');
$CI->db->join('dignity_forum_topic', 'dignity_forum_topic.dignity_forum_topic_id = dignity_forum_reply.dignity_forum_reply_topic_id');

if (mso_segment(3) && mso_segment(3) != 'next')
{
	if (mso_segment(3) == getinfo('comusers_id'))
	{
		$CI->db->where('dignity_forum_reply_comusers_id', getinfo('comusers_id'));
	}
	elseif (mso_segment(3) == getinfo('users_id'))
	{
		$CI->db->where('dignity_forum_reply_users_id', getinfo('users_id'));
	}
	else
	{
		if ($id)
		{
			$CI->db->where('dignity_forum_reply_comusers_id', $id);
		}
	}
	
}

if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();
	
// если есть что выводить
if ($query->num_rows() > 0)	
{	
	$reply_out = '';
	$reply_count = 0;

	// если нет 3 сегмента
	if (!mso_segment(3))
	{
		$reply_out .= '<div class="forum_header_topic">';
		$reply_out .= '<h1>' . t('Последние сообщения на форуме', __FILE__) . '</h1>';
		$reply_out .= '</div>';
	}
	// если есть третий сегмент и вошел комюзер
	elseif (mso_segment(3) == getinfo('comusers_id') || getinfo('users_id'))
	{
		$reply_out .= '<div class="forum_header_topic">';
		$reply_out .= '<h1>' . t('Ваши сообщения', __FILE__) . '</h1>';
		$reply_out .= '</div>';
	}
	else
	{
		$reply_out .= '<div class="forum_header_topic">';
		$reply_out .= '<h1>' . t('Последние сообщения пользователя', __FILE__) . '</h1>';
		$reply_out .= '</div>';
	}

	foreach ($query->result_array() as $reply) 
	{
		
		// определяем кто автор темы
		$reply_nik = '';
		if ($reply['dignity_forum_reply_comusers_id'])
		{
				$reply_nik = $reply['comusers_nik'];
                $reply_site = getinfo('siteurl') . 'users/' . $reply['dignity_forum_reply_comusers_id'];
		}
		else
		{
				$reply_nik = $reply['users_nik'];
				$reply_site = getinfo('siteurl') . 'author/' . $reply['dignity_forum_reply_users_id'];
		}
				
		$reply_out .= '<div class="forum_block">';
				
			$reply_out .= '<div class="forum_title">';
					
				if (is_login())
				{
						$reply_out .= '<p class="right">';
						$reply_out .= '<a href="' . getinfo('site_admin_url') . 'dignity_forum/editone_reply/' . $reply['dignity_forum_reply_id'] . '">
								<input type="button" class="forum_answer_edit" title="' . t('Изменить через панель управление', __FILE__) . '" value="' . t('Изменить', __FILE__) . '"></a>';
						$reply_out .= '</p>';
				}
						
				// аватарка
				$avatar = '';
				if ($reply['comusers_avatar_url'])
				{
						$avatar = $reply['comusers_avatar_url'];
				}
				elseif ($reply['users_avatar_url'])
				{
						$avatar = $reply['users_avatar_url'];
				}
				else
				{
						$avatar = getinfo('plugins_url') . 'dignity_forum/img/noavatar.jpg';
				}
						
				$reply_out .= '<p>' . t('Ответ в тему', __FILE__) .
							' <a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $reply['dignity_forum_reply_topic_id'] . '#answer-'. $reply['dignity_forum_reply_id'] . '">'
						. $reply['dignity_forum_topic_subject'] . '</a>' . ' в '
							. mso_date_convert($format = 'H:i → d.m.Y', $reply['dignity_forum_reply_datecreate']) .  '</p>';
						
			$reply_out .= '</div>'; // конец div forum_title
			
			// начало div forum_comment
			$reply_out .= '<div class="forum_comment">'; 
					
				$reply_out .= '<div style="width: 76%; float:right;">';
				$reply_out .= '<p style="padding:10px;">' . $forum->bb_parser($reply['dignity_forum_reply_text']) . '</p>';
				$reply_out .= '</div>';
						
				$reply_out .= '<div class="forum_comment_info">';
						
					$reply_out .= '<p><img src="' . $avatar . '" height="96px" width="96px"></p>';
							
					$reply_out .= '<p><a href="' . $reply_site . '">' . $reply_nik . '</a></p>';

					if ($reply['comusers_admin_note'] && $options['use_admin_note_as_status'])
					{
						$reply_out .= '<span style="color:red;">' . $reply['comusers_admin_note'] . '</span><br>';
					}
							
					// подсчитываем количество тем от комюзера и юзера
							
					$CI->db->from('dignity_forum_topic');
					if ($reply['comusers_id'])
					{
							$CI->db->where('dignity_forum_topic_сomusers_id', $reply['comusers_id']);
					}
					else
					{
							$CI->db->where('dignity_forum_topic_users_id', $reply['users_id']);
					}
					$count_all_topics = $CI->db->count_all_results();
							
					// подсчитываем количество ответов от комюзера и юзера
					$CI->db->from('dignity_forum_reply');
					if ($reply['comusers_id'])
					{
							$CI->db->where('dignity_forum_reply_comusers_id', $reply['comusers_id']);
					}
					else
					{
							$CI->db->where('dignity_forum_reply_users_id', $reply['users_id']);
					}
					$count_all_posts = $CI->db->count_all_results();
							
					$count_all = $count_all_topics + $count_all_posts;

					if ($reply['comusers_id'])
					{
						$autor = $reply['comusers_id'];
					}
					else
					{
						$autor = $reply['users_id'];
					}

					if ($count_all_topics > 0)
					{
						$reply_out .= t('Тем: ', __FILE__) . '<a href="' . getinfo('siteurl') . $options['slug'] . '/topics/' . $autor . '">' . $count_all_topics . '</a><br>';
					}
					else
					{
						$reply_out .= t('Тем: ', __FILE__) . $count_all_topics . '<br>';
					}

					if ($count_all_posts > 0)
					{
						$reply_out .= t('Сообщений: ', __FILE__) . '<a href="' . getinfo('siteurl') . $options['slug'] . '/replys/' . $autor . '">' . $count_all_posts . '</a><br>';
					}
					else
					{
						$reply_out .= t('Сообщений: ', __FILE__) . $count_all_posts . '<br>';
					}

					$reply_out .= t('Всего сообщений: ', __FILE__) . $count_all . '<br>';
							
					if($reply['comusers_id'])
					{
							$reply_out .= t('Зарегистрирован: ', __FILE__) . mso_date_convert($format = 'd.m.Y', $reply['comusers_date_registr']);
					}
					else
					{
							$reply_out .= t('Зарегистрирован: ', __FILE__) . mso_date_convert($format = 'd.m.Y', $reply['users_date_registr']);
					}
							
					// путь к картинкам
					$path = getinfo('plugins_url') . 'dignity_forum/img/social/';
						
					// twitter
					if ($reply['comusers_msn'])
					{
							$reply_out .= '<br>' . '<a href="http://twitter.com/' . $reply['comusers_msn'] . '" ref="nofollow" title="Twitter" target="_blank"><img src="' . $path . 'twitter.png' . '"></a>';
					}
					elseif($reply['users_msn'])
					{
							$reply_out .= '<br>' . '<a href="http://twitter.com/' . $reply['users_msn'] . '" ref="nofollow" title="Twitter" target="_blank"><img src="' . $path . 'twitter.png' . '"></a>';
					}
							
					// jabber
					if ($reply['comusers_jaber'])
					{
							$reply_out .= ' <a href="xmpp:' . $reply['comusers_jaber'] . '?message" ref="nofollow" title="Jabber"><img src="' . $path . 'jabber.png' . '"></a>';
					}
					elseif($reply['users_jaber'])
					{
							$reply_out .= ' <a href="xmpp:' . $reply['users_jaber'] . '?message" ref="nofollow" title="Jabber"><img src="' . $path . 'jabber.png' . '"></a>';
					}
							
					// skype
					if ($reply['comusers_skype'])
					{
							$reply_out .= ' <a href="skype:' . $reply['comusers_skype'] . '?call" ref="nofollow" title="Skype"><img src="' . $path . 'skype.png' . '"></a>';
					}
					elseif($reply['users_skype'])
					{
							$reply_out .= ' <a href="skype:' . $reply['users_skype'] . '?call" ref="nofollow" title="Skype"><img src="' . $path . 'skype.png' . '"></a>';
					}
							
					$reply_out .= '</div>';
				
				// конец div forum_comment
				$reply_out .= '</div>';
					
			$reply_out .= '<div style="clear:both;"></div>';
				
		$reply_out .= '</div>'; // конец div forum_block
	}
	
	// выводим
	echo $reply_out;
	
	// хук на пагинацию
	mso_hook('pagination', $pag);

}
elseif (mso_segment(3) == getinfo('comusers_id') || mso_segment(3) == getinfo('users_id'))
{
	echo t('Напишите ваш первый ответ на форуме.', __FILE__);
}
elseif (mso_segment(3))
{
	echo t('У пользователя нет ответов.', __FILE__);
}
else
{
	echo t('Новых ответов нет.', __FILE__);
}

// выводим конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
