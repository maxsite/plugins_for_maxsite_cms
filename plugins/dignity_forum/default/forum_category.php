<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// /forum/view/

// доступ к CI
$CI = &get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// получаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
if ( !isset($options['hide_pathway']))  $options['hide_pathway'] = false;

// показывать или скрывать сайдбар?
$forum->hide_sidebar();

// меню
$forum->menu();

// id == 3 сегменту
$id = mso_segment(3);

// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число...
if ($id)
{

	echo '<h1><div class="forum_header_topic"> ' . $forum->get_category_name() . '</div></h1>';

	if (is_login())
	{
		echo '<p><span style="color:red;">' . t('Внимание:', __FILE__) . '</span> ' . t('Вы вошли как администратор (user)!', __FILE__) . '</p>';
	}

	if (!$options['hide_pathway'])
	{
		$pathway = '';
		$pathway .= '<div class="forum_pathway">';
		$pathway .= '<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Список форумов', __FILE__) . '</a>';
		$pathway .= ' → ';
		$pathway .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . mso_segment(3) . '">' . $forum->get_category_name() . '</a>';
		$pathway .= '</div>';
		echo $pathway;
	}

	echo '<div class="forum_info">';
	echo '<span style="float:right; width: 7%;">' . t('Ответы', __FILE__) . '</span>';
	echo '<span style="float:right; width: 18%;">' . t('Просмотры', __FILE__) . '</span>';
	echo '<span style="width: 70%; margin: 10px">' . t('Темы форума') . '</span>';
	echo '</div>';

	// выводим все подкрепленные темы
	$out_categorys = $forum->get_topic_ontop();

	// если есть что выводить
	if ($out_categorys['num_rows'] > 0)	
	{
		foreach ($out_categorys['all_topics_in_category'] as $rw)
		{
			// выводим количество ответов
			$CI->db->from('dignity_forum_reply');
			$CI->db->where('dignity_forum_reply_topic_id', $rw['dignity_forum_topic_id']);
			$all_replys = $CI->db->count_all_results();
									
			echo '<span style="color: #767676; float: right; width: 5%; margin: 10px;">'. t('', __FILE__) . $all_replys . '</span>';

			if ($rw['dignity_forum_topic_views'])
			{
				echo '<span style="color: #767676; float: right; width: 13%; margin: 10px;">' . t('', __FILE__) . $rw['dignity_forum_topic_views'] . '</span>';
			}
			else
			{
				echo '<span style="color: #767676; float: right; width: 13%; margin: 10px;">' . t('', __FILE__) . '0' . '</span>';
			}

			// выводим заголовок
			echo '<div class="forum_topic" id="' . $rw['dignity_forum_topic_id'] . '">';

			echo '<h2>';
			echo '<h2>';
			if ($rw['dignity_forum_topic_closed'] == true)
			{
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topic_close.png' . '" alt="topic_closed" title="' . t('Тема закрыта.', __FILE__) . '"> ';
			}
			else
			{
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topic_read.png' . '" alt=""> ';
			}
			echo '<a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $rw['dignity_forum_topic_id'] . '">' . $rw['dignity_forum_topic_subject'] . '</a>';
			echo '</h2>';

			if (!isset($options['hide_view_author']))  $options['hide_view_author'] = true;
			if ($options['hide_view_author'])
			{
				// определяем кто автор темы
				$author_nik = '';
				if ($rw['comusers_id'])
				{
					$author_nik = $rw['comusers_nik'];
					$author_site = getinfo('siteurl') . $options['slug'] . '/profile/#' . $rw['dignity_forum_topic_сomusers_id'];
				}
				else
				{
					$author_nik = $rw['users_nik'];
					$author_site = getinfo('siteurl') . 'author/' . $rw['dignity_forum_topic_users_id'];
				}
										
				echo '<p>';
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""> <a href="' . $author_site . '">' . $author_nik . '</a> ';	
				echo $forum->get_last_reply($rw['dignity_forum_topic_id']);
				echo '</p>';
			}

			echo '</div>';
		}

	}

	// выводим все темы
	$out_categorys = $forum->get_topic();

	// если есть что выводить
	if ($out_categorys['num_rows'] > 0)	
	{
		foreach ($out_categorys['all_topics_in_category'] as $rw)
		{
			// выводим количество ответов
			$CI->db->from('dignity_forum_reply');
			$CI->db->where('dignity_forum_reply_topic_id', $rw['dignity_forum_topic_id']);
			$all_replys = $CI->db->count_all_results();
									
			echo '<span style="color: #767676; float: right; width: 5%; margin: 10px;">'. t('', __FILE__) . $all_replys . '</span>';

			if ($rw['dignity_forum_topic_views'])
			{
				echo '<span style="color: #767676; float: right; width: 13%; margin: 10px;">' . t('', __FILE__) . $rw['dignity_forum_topic_views'] . '</span>';
			}
			else
			{
				echo '<span style="color: #767676; float: right; width: 13%; margin: 10px;">' . t('', __FILE__) . '0' . '</span>';
			}

			// выводим заголовок
			echo '<div class="forum_topic" id="' . $rw['dignity_forum_topic_id'] . '">';

			echo '<h2>';
			if ($rw['dignity_forum_topic_closed'] == true)
			{
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topic_close.png' . '" alt="topic_closed" title="' . t('Тема закрыта.', __FILE__) . '"> ';
			}
			else
			{
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topic_read.png' . '" alt=""> ';
			}
			echo '<a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $rw['dignity_forum_topic_id'] . '">' . $rw['dignity_forum_topic_subject'] . '</a>';
			echo '</h2>';

			if (!isset($options['hide_view_author']))  $options['hide_view_author'] = true;
			if ($options['hide_view_author'])
			{
				// определяем кто автор темы
				$author_nik = '';
				if ($rw['comusers_id'])
				{
					$author_nik = $rw['comusers_nik'];
					$author_site = getinfo('siteurl') . $options['slug'] . '/profile/#' . $rw['dignity_forum_topic_сomusers_id'];
				}
				else
				{
					$author_nik = $rw['users_nik'];
					$author_site = getinfo('siteurl') . 'author/' . $rw['dignity_forum_topic_users_id'];
				}
				
				echo '<p>';					
				echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""> <a href="' . $author_site . '">' . $author_nik . '</a> ';	
				echo $forum->get_last_reply($rw['dignity_forum_topic_id']);
				echo '</p>';
			}

			echo '</div>';
		}

		// хук на пагинацию
		mso_hook('pagination', $out_categorys['pag']);

	}

	// если не авторизовался, предлагаем войти и потом выводим форму
	if(is_login_comuser() && $forum->comuser_activate() === false)
	{
		echo '<p>' . t('Вам необходимо активировать ваш аккаунт!', __FILE__) . ' <a href="' . getinfo('siteurl') . 'users/' . getinfo('comusers_id') . '">' . t('Сделать это сейчас →') . '</a>' . '</p>';
	}
	elseif (is_login_comuser() or is_login())
	{
		require_once(getinfo('plugins_dir') . 'dignity_forum/user/new_topic.php');
	}
	else
	{
		echo '<p>' . t('Вам необходимо <a href="/login">авторизоваться</a>, чтобы отвечать в темах в этом форуме.', __FILE__) . '</p>';
	}

}
else
{
	echo t('Запрошенного форума не существует.', __FILE__);
}