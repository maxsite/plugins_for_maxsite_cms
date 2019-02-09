<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

mso_cur_dir_lang('admin');

?>

<h1><?= t('Редактирование комментария') ?></h1>
<p><a href="<?= getinfo('site_admin_url') . 'foto-comments' ?>"><?= t('К списку комментариев') ?></a></p>

<?php

	$CI = & get_instance();

	$id = mso_segment(4); // номер пользователя по сегменту url

	// проверим, чтобы это было число
	$id1 = (int) $id;
	if ( (string) $id != (string) $id1 ) $id = false; // ошибочный id

	if ($id) // есть корректный сегмент
	{
		require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев
		
		# отредактировать комментарий
		if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_comments_content', 'f_comments_date', 'f_comments_author', 'f_comments_approved', 'f_comments_email_subscribe')) )
		{
			mso_checkreferer();
			// pr($post);

			$CI->db->where('foto_comments_id', $id);

			$data = array(
				'foto_comments_content' => $post['f_comments_content'],
				'foto_comments_date' => $post['f_comments_date'],
				'foto_comments_approved' => (int) $post['f_comments_approved']
			);

			if ( substr($post['f_comments_author'], 0, 1) == '0' )
			{
				if ( !isset($post['f_comments_author_name']) or trim($post['f_comments_author_name']) == '' )
					$data['foto_comments_author_name'] = t('Аноним');
				else
					$data['foto_comments_author_name'] = trim($post['f_comments_author_name']);
				$data['foto_comments_users_id'] = 'NULL';
				$data['foto_comments_comusers_id'] = 'NULL';
			}
			elseif ( substr($post['f_comments_author'], 0, 1) == '1' )
			{
				$data['foto_comments_users_id'] = (int) substr($post['f_comments_author'], 2);
				$data['foto_comments_comusers_id'] = 'NULL';
			}
			else
			{
				$data['foto_comments_comusers_id'] = (int) substr($post['f_comments_author'], 2);
				$data['foto_comments_users_id'] = 'NULL';
			}

			if ($CI->db->update('foto_comments', $data ) )
				echo '<div class="update">' . t('Обновлено!') . '</div>';
			else
				echo '<div class="error">' . t('Ошибка обновления') . '</div>';

			$CI->db->cache_delete_all();
			
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();

			if ($post['f_comments_email_subscribe']) // разослать подписчикам
			{
				require_once( getinfo('common_dir') . 'comments.php' );

				// получим по номеру коммента номер страницы и её титул - нужно для отправки
				$CI->db->select('foto_comments_foto_id, foto_title');
				$CI->db->from('foto_comments, foto');
				$CI->db->where('foto_comments_foto_id = foto_id');
				$CI->db->where('foto_comments_id', $id);

				$query = $CI->db->get();
				if ($query->num_rows() > 0)
				{
					$row = $query->row_array();

					mso_email_message_new_comment_subscribe(array(
						'id' => $id,
						'comments_approved' => (int) $post['f_comments_approved'],
						'comments_content' => $post['f_comments_content'],
						'comments_page_id' => $row['foto_comments_foto_id'],
						'page_title' => $row['foto_title']
						));
				}
			}
		}
		elseif ($post = mso_check_post(array('f_session_id', 'f_submit_delete')))
		{
			// удалить комментарий
			mso_checkreferer();

			$CI->db->where_in('foto_comments_id', $id);
			if ( $CI->db->delete('foto_comments') )
			{
				mso_flush_cache();
				
				// синхронизация количества комментариев у комюзеров
				mso_comuser_update_count_comment();
				mso_redirect('admin/foto-comments');
			}
			else 
			{
				echo '<div class="error">' . t('Ошибка удаления') . '</div>';
			}
		}

		# вывод данных комментария
		$CI->db->select('foto_comments.*, users.users_nik, users.users_id, comusers.comusers_nik, foto.foto_title, foto.foto_slug, foto.foto_id');
		$CI->db->from('foto_comments');
		$CI->db->join('users', 'users.users_id = foto_comments.foto_comments_users_id', 'left');
		$CI->db->join('comusers', 'comusers.comusers_id = foto_comments.foto_comments_comusers_id', 'left');
		$CI->db->join('foto', 'foto.foto_id = foto_comments.foto_comments_foto_id', 'left');
		$CI->db->where('foto_comments_id', $id);

		$query = $CI->db->get();

		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			if ( $row['users_nik'] )
			{
				echo '<p><strong>' . t('Автор') . '</strong>: '
				. '<a href="' . getinfo('site_admin_url') . 'users/edit/' . $row['users_id'] . '">'
				. $row['users_nik']
				. '</a></p>';
			}

			if ( $row['comusers_nik'] )
			{
				echo '<p><strong>' . t('Автор') . '</strong>: '
				. '<a href="' . getinfo('site_admin_url') . 'comusers/edit/' . $row['comments_comusers_id'] . '">'
				. $row['comusers_nik']
				. '</a>'
				. ' | <a href="' . getinfo('site_url') . 'users/' . $row['comments_comusers_id'] . '">Персональная страница</a>'
				.'</p>';
			}

			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			//echo '<h3>' . t('Текст', 'admin') . '</h3>';

			// хуки для текстового поля комментирования
			mso_hook('admin_comment_edit');
			mso_hook('comments_content_start');


			$text = mso_xss_clean($row['foto_comments_content']);
			if ($text != $row['foto_comments_content'])
			{
				echo '<div class="error">Внимание! Возможна XSS-атака! Полный текст комментария</div><textarea>'
					. htmlspecialchars($row['foto_comments_content']) . '</textarea><p>Исправленный текст комментария</p>';
			}

			echo '<p><textarea name="f_comments_content" id="comments_content">' . htmlspecialchars($text) . '</textarea></p>';

			echo '<h3>' . t('Дата') . '</h3>
				<p><input name="f_comments_date" type="text" value="' . htmlspecialchars($row['foto_comments_date']) .'"></p>';

			$comments_author_name = trim(htmlspecialchars($row['foto_comments_author_name']));
			if ( !($comments_author_name or $row['foto_comments_users_id'] or $row['foto_comments_comusers_id']) ) $comments_author_name = t('Аноним');
			echo '<h3>' . t('Автор') . '</h3>';

			$out  = '<p><input name="f_comments_author_name" type="text" value="' . $comments_author_name . '"><select name="f_comments_author">' . NR;
			$out .= '<option value="0"' . ( (!($row['foto_comments_users_id'] or $row['foto_comments_comusers_id']))?(' selected="selected"'):('') ) . '>' . t('Аноним') . '</option>' . NR;

			$CI->db->select('users_id, users_nik');
			$users = $CI->db->get('users');
			if ($users->num_rows() > 0) // больше нуля, можно работать
			{
				$out .= '<optgroup label="' . t('Авторы') . '">' . NR;
				foreach ($users->result_array() as $user) // обходим в цикле
				{
					$out .= '<option value="1-'. $user['users_id']. '"'. ( ($row['foto_comments_users_id'] == $user['users_id'])?(' selected="selected"'):('') ).'>' . $user['users_nik'] . '</option>'. NR;
				}
				$out .= '</optgroup>' . NR;
			}

			$CI->db->select('comusers_id , comusers_nik ');
			$users = $CI->db->get('comusers');
			if ($users->num_rows() > 0) // больше нуля, можно работать
			{
				$out .= '<optgroup label="' . t('Комментаторы') . '">' . NR;
				foreach ($users->result_array() as $user) // обходим в цикле
				{
					if (!$user['comusers_nik']) $user['comusers_nik'] = '! ' . t('Комментатор') . ' ' . $user['comusers_id'];
					$out .= '<option value="2-'. $user['comusers_id']. '"'. ( ($row['foto_comments_comusers_id'] == $user['comusers_id'])?(' selected="selected"'):('') ).'>' . $user['comusers_nik'] . '</option>'. NR;
				}
				$out .= '</optgroup>' . NR;
			}
			$out .= '</select></p>' . NR;
			echo t('<p>Выберите пользователя или комментатора, которого вы хотите назначить автором комментария, либо выберите «Аноним» и введите имя анонимного комментатора.</p>') . $out;

			$checked1 = $checked2 = '';

			if ($row['foto_comments_approved'])
				$checked1 = 'checked="checked"';
			else
				$checked2 = 'checked="checked"';

			echo '<h3>'. t('Модерация') .'</h3><p><label><input type="radio" name="f_comments_approved" value="1" ' . $checked1 . '> ' . t('Одобрить')
				. '</label> <label><input type="radio" name="f_comments_approved" value="0" ' . $checked2 . '> ' . t('Запретить')
				. '</label></p>';

			echo '<p><input type="hidden" name="f_comments_email_subscribe" value="0"><label><input type="checkbox" name="f_comments_email_subscribe" value="1" ' . $checked2 . '> '
				. t('Сразу разослать подписчикам')
				. '</label></p>';
			echo '<p class="br"><input type="submit" name="f_submit" value="' . t('Готово') . '">' 
				. ' <input type="submit" name="f_submit_delete" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить комментарий') . '">'
				. '</p>';



			echo '</form>';

			echo '<p><a href="' . getinfo('siteurl') . 'foto/' . $row['foto_slug'] . '#comment-' . $id . '">'
				. t('Вернуться к комментарию на сайте') . '</a>'

				. ' | <a href="' . getinfo('site_admin_url') . 'edit-foto/' . $row['foto_id'] . '">'
				. t('Редактировать запись') . '</a>'


				. '</p>';

			// pr($row);
		}
		else echo '<div class="error">' . t('Ошибочный комментарий') . '</div>';
	}
	else
	{
		echo '<div class="error">' . t('Ошибочный запрос') . '</div>'; // id - ошибочный
	}
?>