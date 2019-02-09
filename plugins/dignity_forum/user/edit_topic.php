<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// загружаем начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// получаем доступ к CI
$CI = & get_instance();

// присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать или показывать сайтбар?
$forum->hide_sidebar();

// меню
$forum->menu();

// присваиваем $id 3 сегмент
$id = mso_segment(3);

// число или не число
if (!is_numeric($id)) $id = false;
else $id = (int) $id;

// если число и вошел комюзер или юзер
if ($id && (is_login_comuser() || is_login()))
{
	// загружаем данные из базы
	$CI->db->from('dignity_forum_topic');
	$CI->db->where('dignity_forum_topic_id', $id);
	$q = $CI->db->get();
	$dignity_forum_comuser_id = '';
	$dignity_forum_user_id = '';
	if (is_login_comuser())
	{
		foreach ($q->result_array() as $rw)
		{
			$dignity_forum_comuser_id = $rw['dignity_forum_topic_сomusers_id'];
		}
	}
	
	// если id комюзера не совпадает с id автора темы, то выдаём ошибку
	if (getinfo('comusers_id') != $dignity_forum_comuser_id)
	{
		echo t('Вы не можете редактировать чужие темы.', __FILE__);
	}
	else
	{
		
		// если был пост
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_topic')) )
		{
			// проверяем реферала
			mso_checkreferer();
			
			// подгатавливаем массив для добавления в базу
			$ins_data = array (
				'dignity_forum_topic_subject' => $post['f_dignity_forum_topic_subject'],
				'dignity_forum_topic_text' => $post['f_dignity_forum_topic_text'],
				'dignity_forum_topic_dateupdate' => date('Y-m-d H:i:s'),
				);

			mso_xss_clean($ins_data);

			require_once( getinfo('plugins_dir') . 'dignity_forum/core/functions-edit.php');

			$res = edit_topic($ins_data, $id);
			
			// если OK, то редиректим на список записей
			if ($res['result'] == '1')
			{
				// если всё окей
				$ok = '';
				$ok .= '<div class="update">';
				$ok .= '<p>' . t('Обновлено!', __FILE__) . '</p>';
				$ok .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . mso_segment(3) . '">' . t('Вернуться к теме', __FILE__) . '</a></p>';
				$ok .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Вернуться на главную', __FILE__) . '</a></p>';
				$ok .= '</div>';
				
				echo $ok;
			}
			else
			{
				echo $res['message'];
				
				extract($ins_data);
			} // if $res

		}
		// если небыло поста
		else
		{
			// выводим заголовок
			echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';

			foreach ($q->result_array() as $rw)
			{
				$dignity_forum_topic_subject = $rw['dignity_forum_topic_subject'];
				$dignity_forum_topic_text = $rw['dignity_forum_topic_text'];
			}
			
			require_once(getinfo('plugins_dir') . 'dignity_forum/user/form/form_topic.php');
		}
	}
}
else
{
	// если id не верный, либо не число...
	echo t('Ошибочный номер.', __FILE__);
}

// загружаем конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file