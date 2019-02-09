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
if ( !isset($options['reply_edit_time']) ) $options['reply_edit_time'] = 300;

if ( !isset($post['f_dignity_forum_reply_text'])) $post['f_dignity_forum_reply_text'] = '';

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать сайтбар
$forum->hide_sidebar();

// меню
$forum->menu();

// присваиваем $id 3 сегмент
$id = mso_segment(3);

// число или не число
if (!is_numeric($id)) $id = false;
else $id = (int) $id;

// если число и вошел как комюзер или юзер
if ($id && is_login_comuser() || is_login())
{

	// загружаем данные из базы
	$CI->db->from('dignity_forum_reply');
	$CI->db->where('dignity_forum_reply_id', $id);
	$q = $CI->db->get();

	// если вошел админ
	if (is_login())
	{
		// если был пост
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_reply')) )
		{
			// проверяем реферала
			mso_checkreferer();
			
			// подгатавливаем массив для добавления в базу
			$ins_data = array (
							'dignity_forum_reply_text' => htmlspecialchars($post['f_dignity_forum_reply_text']),
							'dignity_forum_reply_dateupdate' => date('Y-m-d H:i:s'),
							);
					
			require_once( getinfo('plugins_dir') . 'dignity_forum/core/functions-edit.php');

			$res = edit_new_reply($ins_data, $id);

			// если OK, то редиректим на список записей
			if ($res['result'] == '1')
			{
				// если всё окей
				$ok = '';
				$ok .= '<div class="update">';
				$ok .= '<p>' . t('Обновлено!', __FILE__) . '</p>';
				foreach ($q->result_array() as $rw) 
				{
					$ok .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $rw['dignity_forum_reply_topic_id'] . '">' . t('Вернуться к теме', __FILE__) . '</a></p>';
				}
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
		else
		{

			// выводим заголовок
			echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';

			// разбираем массив
			foreach ($q->result_array() as $rw) 
			{

				$dignity_forum_reply_text = $rw['dignity_forum_reply_text'];

			}

			require_once(getinfo('plugins_dir') . 'dignity_forum/user/form/form_reply.php');
		}
	}
	// если коюмзер
	else
	{
		foreach ($q->result_array() as $rw)
		{
			if ($rw['dignity_forum_reply_comusers_id'] != getinfo('comusers_id'))
			{
				echo t('Вы не можете редактировать чужие ответы.', __FILE__);
			}
			else
			{

				$public_date = strtotime($rw['dignity_forum_reply_datecreate']);
				
				// текущее время
				$now = time();
								
				// время на редактирования, по умолчанию 300 секунд == 5 минут
				$allow_time = $options['reply_edit_time'];
				
				if ($public_date >= $now - $allow_time)
				{

					// если был пост
					if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_reply')) )
					{
						// проверяем реферала
						mso_checkreferer();
							
							// подгатавливаем массив для добавления в базу
							$ins_data = array (
											'dignity_forum_reply_text' => htmlspecialchars($post['f_dignity_forum_reply_text']),
											'dignity_forum_reply_dateupdate' => date('Y-m-d H:i:s'),
											);
									
							require_once( getinfo('plugins_dir') . 'dignity_forum/core/functions-edit.php');

							$res = edit_new_reply($ins_data, $id);

							// если OK, то редиректим на список записей
							if ($res['result'] == '1')
							{
								// если всё окей
								$ok = '';
								$ok .= '<div class="update">';
								$ok .= '<p>' . t('Обновлено!', __FILE__) . '</p>';
								$ok .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $rw['dignity_forum_reply_topic_id'] . '">' . t('Вернуться к теме', __FILE__) . '</a></p>';
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
						else
						{

							// выводим заголовок
							echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';

							// разбираем массив
							foreach ($q->result_array() as $rw) 
							{

								$dignity_forum_reply_text = $rw['dignity_forum_reply_text'];

							}

							require_once(getinfo('plugins_dir') . 'dignity_forum/user/form/form_reply.php');
						}
					
				}
				else
				{
					echo '<p>' . t('Время редактирование ответа истекло. Если вы хотите изменить ваш ответ, свяжитесь с администратором форума.', __FILE__) . '</p>';
				}
			}

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