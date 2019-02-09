<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// доступ к CI
$CI = &get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// показывать или скрывать сайдбар?
$forum->hide_sidebar();

// меню
$forum->menu();

if (is_login())
{
	echo '<p><span style="color:red;">' . t('Внимание:', __FILE__) . '</span> ' . t('Вы вошли как администратор (user)!', __FILE__) . '</p>';
}

// получаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
if ( !isset($options['reply_limit']) ) $options['reply_limit'] = 10;
if ( !isset($options['permanent_link']) ) $options['permanent_link'] = false;
if ( !isset($options['reply_edit_time']) ) $options['reply_edit_time'] = 300;
if ( !isset($options['use_admin_note_as_status']))  $options['use_admin_note_as_status'] = false;
if ( !isset($options['hide_elapsed_time']))  $options['hide_elapsed_time'] = false;
if ( !isset($options['hide_pathway']))  $options['hide_pathway'] = false;
if ( !isset($options['hide_date']))  $options['hide_date'] = true;

$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число...
if ($id)
{
	// выводим тему
	$topic = $forum->get_topic_show();

	// если есть что выводить
	if ($topic['num_rows'] > 0)	
	{
		$topic_out = '';
			
		// готовим вывод темы
		foreach ($topic['topic'] as $rw) 
		{

			// подсчёт количества просмотров через cookie
			global $_COOKIE;
			$name_cookies = 'dignity-forum';
			$expire = 2592000;
			$slug = getinfo('siteurl') . $options['slug'] . '/' . mso_segment(2) . '/' . mso_segment(3);
			$all_slug = array();
				
			if (isset($_COOKIE[$name_cookies]))
			{
				$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			}
				
			if (in_array($slug, $all_slug))
			{
				false; // уже есть текущий урл - не увеличиваем счетчик
			}
			else
			{
				// нужно увеличить счетчик
				$all_slug[] = $slug; // добавляем текущий slug
				$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
				$all_slug = implode('|', $all_slug); // соединяем обратно в строку
				$expire = time() + $expire;
					
				@setcookie($name_cookies, $all_slug, $expire);
					
				$page_view_count = $rw['dignity_forum_topic_views'] + 1;
					
				$CI->db->where('dignity_forum_topic_id', $id);
				$CI->db->update('dignity_forum_topic', array('dignity_forum_topic_views'=>$page_view_count));
			}

			// добавляем мета-данные
			mso_head_meta('title', $rw['dignity_forum_topic_subject']);
			mso_head_meta('description', $rw['dignity_forum_topic_subject']);
			mso_head_meta('keywords', $rw['dignity_forum_topic_subject']);

			if (!$options['hide_pathway'])
			{
			
				$pathway = '';
				$pathway .= '<div class="forum_pathway">';
				$pathway .= '<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Список форумов', __FILE__) . '</a>';
				$pathway .= ' → ';
				$pathway .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $rw['dignity_forum_topic_category'] . '">' . $rw['dignity_forum_category_name'] . '</a>';
				$pathway .= ' → ';
				$pathway .= '<a href="' . getinfo('site_url') . $options['slug'] . '/topic/' . $rw['dignity_forum_topic_id'] . '">' . $rw['dignity_forum_topic_subject'] . '</a>';
			    $pathway .= '</div>';

			    echo $pathway;
		    }

		    // выводим заголовок темы
			$topic_out .= '<div class="forum_header_topic">';
            $topic_out .= '<h1>' . $rw['dignity_forum_topic_subject'] . '</h1>';
            $topic_out .= '</div>';

            // если коюзер === автор записи, разрешаем редактировать
			if (getinfo('comusers_id') === $rw['dignity_forum_topic_сomusers_id'])
			{
				$topic_out .= '<p class="right">';
                $topic_out .= '<a href="' . getinfo('siteurl') . $options['slug'] . '/edit/' . $rw['dignity_forum_topic_id'] . '">
					<input type="button" class="forum_answer_edit" title="Редактировать" value="Редактировать"></a>';
                $topic_out .= '</p>';
			}
			// если юзер === автор записи, разрешаем редактировать или изменять
			elseif (is_login())
			{
				$topic_out .= '<p class="right">';
                $topic_out .= '<a href="' . getinfo('site_admin_url') . 'dignity_forum/editone_topic/' . $rw['dignity_forum_topic_id'] . '">
					<input type="button" class="forum_answer_edit" title="Изменить через панель управления" value="Изменить"></a>';
                $topic_out .= '</p>';
					
				if (getinfo('users_id') === $rw['dignity_forum_topic_users_id'])
				{
					$topic_out .= '<p class="right">';
                    $topic_out .= '<a href="' . getinfo('siteurl') . $options['slug'] . '/edit/' . $rw['dignity_forum_topic_id'] . '">
							<input type="button" class="forum_answer_edit" title="Редактировать" value="Редактировать"></a>';
                    $topic_out .= '</p>';	
				}
			}

			// определяем автора, написавшего новую тему
			$author_nik = '';
			$author_site = '';
			if ($rw['dignity_forum_topic_сomusers_id'])
			{
				$author_nik = $rw['comusers_nik'];
				$author_site = getinfo('siteurl') . '/users/' . $rw['dignity_forum_topic_сomusers_id'];
			}
			else
			{
				$author_nik = $rw['users_nik'];
				$author_site = getinfo('siteurl') . 'author/' . $rw['dignity_forum_topic_users_id'];
			}

			// аватарка
			$avatar = '';
			$avatar_url = '';
			$grav_email = '';

			if ($rw['comusers_avatar_url']) $avatar_url = $rw['comusers_avatar_url'];
			elseif ($rw['users_avatar_url']) $avatar_url = $rw['users_avatar_url'];

			if ($rw['users_email']) $grav_email = $rw['users_email'];
			elseif ($rw['comusers_email']) $grav_email = $rw['comusers_email'];

			if (!$avatar_url)
			{
				if (!empty($_SERVER['HTTPS'])) 
				{
				   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
						. md5($grav_email)
						. "&amp;size=80";
				} 
				else 
				{
				   	$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
						. md5($grav_email)
						. "&amp;size=80";
				}

				$avatar = $avatar_url;
			}

			if ($avatar_url)
			{
				if ($rw['comusers_avatar_url'])
				{
					$avatar = $rw['comusers_avatar_url'];
				}
				elseif ($rw['users_avatar_url'])
				{
					$avatar = $rw['users_avatar_url'];
				}
			}

			// выводим автора темы
			$topic_out .= '<img src="' . $avatar . '" height="40px" width="40px" style="padding:3px 15px 3px 0px;">';
            $topic_out .= '<span style="font-size: 0.8em; padding: 5px 0px;">';
            $topic_out .= t('Автор темы ', __FILE__) . '<a href="' . $author_site . '">' . $author_nik . '</a> ';

            if ($options['hide_date'])
			{
				// выводим дату создания темы
				$topic_out .= t('открыта в', __FILE__) . ' ' . mso_date_convert($format = 'H:i → d.m.Y', $rw['dignity_forum_topic_datecreate']);
			}

			$topic_out .= '</span>';
				
			// преобразовываем bb-code в html
			$topic_out .= '<div class="forum_content">';
			$topic_out .= '<p>' . $forum->bb_parser($rw['dignity_forum_topic_text']) . '</p>';
			$topic_out .= '</div>';

            echo $topic_out;

            // показывать социальные кнопки?
			if (!isset($options['show_social']))  $options['show_social'] = false;
			if($options['show_social'])
			{
				echo '<p><script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>';
				echo '<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div></p>';

			}
		
			// если включена опция постоянная ссылка на тему
			if ($options['permanent_link'])
			{
				echo '<p>' . t('Постоянная ссылка на тему:', __FILE__) . '<br>' . $forum->current_url() . '</p>';	
			}

			 // выводим ответы

            $out = '';
			$count_replys = 0;

			$replys = $forum->get_replys_in_topics();

			foreach ($replys['replys'] as $row)
			{
				$count_replys = $count_replys + 1;

				// определяем кто автор темы
				$reply_nik = '';
				if ($row['dignity_forum_reply_comusers_id'])
				{
					$reply_nik = $row['comusers_nik'];
                    $reply_site = getinfo('siteurl') . 'users/' . $row['dignity_forum_reply_comusers_id'];
				}
				else
				{
					$reply_nik = $row['users_nik'];
					$reply_site = getinfo('siteurl') . 'author/' . $row['dignity_forum_reply_users_id'];
				}

				$out .= '<div class="forum_block">';
                $out .= '<a id="answer-'. $row['dignity_forum_reply_id'] .'"></a>';

                $out .= '<div class="forum_title">';

                // редактирования комментария в течении определёного времени
				if (getinfo('comusers_id') === $row['dignity_forum_reply_comusers_id'])
				{
					// время из базы
					$public_date = strtotime($row['dignity_forum_reply_datecreate']);
					// текущее время
					$now = time();
					// время на редактирования, по умолчанию 300 секунд == 5 минут
					$allow_time = $options['reply_edit_time'];
					
					if ($public_date >= $now - $allow_time)
					{
						$out .= '<p class="right"><a href="' . getinfo('siteurl') . $options['slug'] . '/edit_reply/' . $row['dignity_forum_reply_id'] . '">
					<input type="button" class="forum_answer_edit" title="Редактировать" value="Редактировать"></a></p>';
					}
					
				}
				// если юзер
				elseif (getinfo('users_id') === $row['dignity_forum_reply_users_id'])
				{
					$out .= '<p class="right"><a href="' . getinfo('siteurl') . $options['slug'] . '/edit_reply/' . $row['dignity_forum_reply_id'] . '">
					<input type="button" class="forum_answer_edit" title="Редактировать" value="Редактировать"></a></p>';
				}

				// аватарка
				$avatar = '';
				$avatar_url = '';
				$grav_email = '';

				if ($row['comusers_avatar_url']) $avatar_url = $row['comusers_avatar_url'];
				elseif ($row['users_avatar_url']) $avatar_url = $row['users_avatar_url'];

				if ($row['users_email']) $grav_email = $row['users_email'];
				elseif ($row['comusers_email']) $grav_email = $row['comusers_email'];

				if (!$avatar_url)
				{
					if (!empty($_SERVER['HTTPS'])) 
					{
					   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
							. md5($grav_email)
							. "&amp;size=80";
					} 
					else 
					{
					   $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
							. md5($grav_email)
							. "&amp;size=80";
					}

					$avatar = $avatar_url;
				}

				if ($avatar_url)
				{
					if ($row['comusers_avatar_url'])
					{
						$avatar = $row['comusers_avatar_url'];
					}
					elseif ($row['users_avatar_url'])
					{
						$avatar = $row['users_avatar_url'];
					}
				}

				if ($options['hide_date'])
				{
					$out .= '<p>' . t('Ответ от', __FILE__) . ' <a href="' . $reply_site . '">' . $reply_nik . '</a>' . t(' в ', __FILE__)
					. mso_date_convert($format = 'H:i → d.m.Y', $row['dignity_forum_reply_datecreate']) . '<span>№' . $count_replys . '</span></p>';
				}
				else
				{
					$out .= '<p>' . t('Ответ от', __FILE__) . ' <a href="' . $reply_site . '">' . $reply_nik . '</a>' . '<span>№' . $count_replys . '</span></p>';
				}

				$out .= '</div>'; // конец div forum_title
				
				$out .= '<div class="forum_comment">'; 
				
				$out .= '<div class="forum_comment_reply_text">';
				$out .= '<p>' . $forum->bb_parser($row['dignity_forum_reply_text']) . '</p>';
				$out .= '</div>';
				
				$out .= '<div class="forum_comment_reply_info">';
				
				$out .= '<p><img src="' . $avatar . '" height="96px" width="96px" style="padding:3px 10px 5px 0px;"></p>';
				
				$out .= '<p><a href="' . $reply_site . '">' . $reply_nik . '</a></p>';

				if ($row['comusers_admin_note'] && $options['use_admin_note_as_status'])
				{
					$out .= '<span style="color:red;">' . $row['comusers_admin_note'] . '</span><br>';
				}

				// подсчитываем количество тем от комюзера и юзера
				$CI->db->from('dignity_forum_topic');
				if ($row['comusers_id'])
				{
					$CI->db->where('dignity_forum_topic_сomusers_id', $row['comusers_id']);
				}
				else
				{
					$CI->db->where('dignity_forum_topic_users_id', $row['users_id']);
				}
				$count_all_topics = $CI->db->count_all_results();

				// подсчитываем количество ответов от комюзера и юзера
				$CI->db->from('dignity_forum_reply');
				if ($row['comusers_id'])
				{
					$CI->db->where('dignity_forum_reply_comusers_id', $row['comusers_id']);
				}
				else
				{
					$CI->db->where('dignity_forum_reply_users_id', $row['users_id']);
				}
				$count_all_posts = $CI->db->count_all_results();
				
				$count_all = $count_all_topics + $count_all_posts;

				if ($row['comusers_id'])
				{
					$autor = $row['comusers_id'];
				}
				else
				{
					$autor = $row['users_id'];
				}

				if ($count_all_topics > 0)
				{
					$out .= t('Тем: ', __FILE__) . '<a href="' . getinfo('siteurl') . $options['slug'] . '/topics/' . $autor . '">' . $count_all_topics . '</a><br>';
				}
				else
				{
					$out .= t('Тем: ', __FILE__) . $count_all_topics . '<br>';
				}

				if ($count_all_posts > 0)
				{
					$out .= t('Сообщений: ', __FILE__) . '<a href="' . getinfo('siteurl') . $options['slug'] . '/replys/' . $autor . '">' . $count_all_posts . '</a><br>';
				}
				else
				{
					$out .= t('Сообщений: ', __FILE__) . $count_all_posts . '<br>';
				}

				$out .= t('Всего сообщений: ', __FILE__) . $count_all . '<br>';

				if($row['comusers_id'])
				{
					$out .= t('Зарегистрирован: ', __FILE__) . mso_date_convert($format = 'd.m.Y', $row['comusers_date_registr']);
				}
				else
				{
					$out .= t('Зарегистрирован: ', __FILE__) . mso_date_convert($format = 'd.m.Y', $row['users_date_registr']);
				}
				
				// путь к картинкам
				$path = getinfo('plugins_url') . 'dignity_forum/img/social/';
			
				// twitter
				if ($row['comusers_msn'])
				{
					$out .= '<br>' . '<a href="http://twitter.com/' . $row['comusers_msn'] . '" ref="nofollow" title="Twitter" target="_blank"><img src="' . $path . 'twitter.png' . '"></a>';
				}
				elseif($row['users_msn'])
				{
					$out .= '<br>' . '<a href="http://twitter.com/' . $row['users_msn'] . '" ref="nofollow" title="Twitter" target="_blank"><img src="' . $path . 'twitter.png' . '"></a>';
				}
				
				// jabber
				if ($row['comusers_jaber'])
				{
					$out .= ' <a href="xmpp:' . $row['comusers_jaber'] . '?message" ref="nofollow" title="Jabber"><img src="' . $path . 'jabber.png' . '"></a>';
				}
				elseif($row['users_jaber'])
				{
					$out .= ' <a href="xmpp:' . $row['users_jaber'] . '?message" ref="nofollow" title="Jabber"><img src="' . $path . 'jabber.png' . '"></a>';
				}
				
				// skype
				if ($row['comusers_skype'])
				{
					$out .= ' <a href="skype:' . $row['comusers_skype'] . '?call" ref="nofollow" title="Skype"><img src="' . $path . 'skype.png' . '"></a>';
				}
				elseif($row['users_skype'])
				{
					$out .= ' <a href="skype:' . $row['users_skype'] . '?call" ref="nofollow" title="Skype"><img src="' . $path . 'skype.png' . '"></a>';
				}
				
				$out .= '</div>';

				$out .= '</div>'; // end div forum_comment
				
				$out .= '<div style="clear:both;"></div>';

				if (is_login() or is_login_comuser())
				{
					// цитирование
					$out .=  '<div class="forum_answer">';
					
					$out .= '<input type="button" class="forum_answer_button" title="Ответить" value="Ответить"
						onclick="document.getElementById(\'reply\').value=\'[b]' . $reply_nik . ',[/b] \'"> ';

					if (is_login())
					{
						$out .= '<a href="' . getinfo('site_admin_url') . 'dignity_forum/editone_reply/' . $row['dignity_forum_reply_id'] . '">
							<input type="button" class="forum_answer_edit" title="Редактировать через панель управление" value="Редактировать"></a>';
					}

					$out .= '</div>';				
				}
				
				$out .= '</div>'; // конец div forum_block

			}

			// выводим ответы
			echo $out;

			// добавляем пагинацию
			mso_hook('pagination', $replys['pag']);

           	// если включена опция заблокировать тему, то...
			if ($rw['dignity_forum_topic_closed'] == true)
			{
				echo '<p>' . t('Эта тема закрыта, вы не можете оставлять сообщения в ней.', __FILE__) . '</p>';
			}
			else
			{
				require_once(getinfo('plugins_dir') . 'dignity_forum/user/new_reply.php');
                
                if (is_login_comuser() && $forum->comuser_activate() === false)
                {
                    echo '<p>' . t('Вам необходимо активировать ваш аккаунт!', __FILE__) . ' <a href="' . getinfo('siteurl') . 'users/' . getinfo('comusers_id') . '">' . t('Сделать это сейчас →') . '</a>' . '</p>';
                }
                else
                {
                	if(!is_login_comuser() && !is_login() && $rw['dignity_forum_topic_closed'] == false)
                	{
						echo '<p>' . t('Чтобы оставить свой ответ, вам нужно', __FILE__) . ' <a href="' . getinfo('siteurl') . 'registration">' . t('зарегистироваться', __FILE__) . '</a> ' . t('или',__FILE__) . ' <a href="' . getinfo('siteurl') . 'login">' . t('войти на сайт', __FILE__) . '.</a></p>';
                	}
                }
			}

		}
	}

	if ($options['hide_elapsed_time'])
	{
		echo '<p style="text-align:center;">' . t('Cтраница сгенерирована за: ', __FILE__) . $CI->benchmark->elapsed_time() . t(' секунд', __FILE__) . '</p>';
	}
}
else
{
	echo t('Запрошенной темы не существует.', __FILE__);
}
