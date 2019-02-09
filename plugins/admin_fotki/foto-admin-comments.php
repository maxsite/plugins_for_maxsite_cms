<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	$options_key = 'admin_fotki';

	
	mso_cur_dir_lang('admin');
	
	$f_all_comments = false; // только неразрешенные комментарии
	
	if (mso_segment(3) == 'all') $f_all_comments = true; 
	elseif (mso_segment(3) == 'moderation') $f_all_comments = false; 
	
	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев
	
	# разрешить или запретить
	if ( ( $post = mso_check_post(array('f_session_id', 'f_check_comments')) ) and 
		( isset($_POST['f_aproved_submit']) or isset($_POST['f_unaproved_submit']) ) )
		
	{
		mso_checkreferer();

		$action = '0'; // запретить по-умолчанию
		if (isset($post['f_aproved_submit'])) $action = '1'; // разрешить
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('foto_comments_id', $arr_ids);
		if ($CI->db->update('foto_comments', array('foto_comments_approved'=>$action) ) )
		{
			mso_flush_cache();
			
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			
			echo '<div class="update">' . t('Обновлено!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}
	
	
	# удалить комментарий
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comments')) )
	{
		mso_checkreferer();
		// pr($post);
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('comments_id', $arr_ids);
		
		if ( $CI->db->delete('comments') )
		{
			mso_flush_cache();
			
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			
			echo '<div class="update">' . t('Удалено!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
	}
	

?>
<h1><?= t('Комментарии') ?></h1>
<p class="info"><?= t('Последние комментарии') ?></p>
<p><strong><?= t('Фильтр:') ?></strong> <a href="<?= getinfo('site_admin_url') ?>foto-comments/all"><?= t('Все') ?></a> | <a href="<?= getinfo('site_admin_url') ?>foto-comments/moderation"><?= t('Только требующие модерации') ?></a></p>


<?php

	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', '&bull;', '+', t('Текст'),  t('Действие'));
	
	# подготавливаем выборку из базы
	
	$CI->db->select('SQL_CALC_FOUND_ROWS foto_comments_id, foto_comments_users_id, foto_comments_comusers_id, foto_comments_author_name, foto_comments_date, foto_comments_content, foto_comments_approved, foto_comments_author_ip, users.users_nik, comusers.comusers_nik, foto.foto_title, foto.foto_slug', false);
	$CI->db->from('foto_comments');
	$CI->db->join('users', 'users.users_id = foto_comments.foto_comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = foto_comments.foto_comments_comusers_id', 'left');
	$CI->db->join('foto', 'foto.foto_id = foto_comments.foto_comments_foto_id', 'left');
	
	if (!$f_all_comments) $CI->db->where('foto_comments_approved', 0);

	$CI->db->order_by('foto_comments_date', 'desc');
	
	$limit = 20;

	$CI->db->limit($limit, mso_current_paged() * $limit - $limit ); // не более $limit
	
	$query = $CI->db->get();

	$pagination = mso_sql_found_rows($limit); // определим общее кол-во записей для пагинации
	mso_hook('pagination', $pagination);
	
		
	// если есть данные, то выводим
	if ($query->num_rows() > 0)
	{
		$this_url = $MSO->config['site_admin_url'] . 'foto-comments/';
		$view_url = $MSO->config['site_url'] . 'foto/';
		
		foreach ($query->result_array() as $row)
		{
			$id = $row['foto_comments_id'];
			
			// для вывода делаем чекбокс + hidden всех комментов для того, чтобы проверить тех,
			// которые окажутся не отмечены - их POST не передает
			$id_out = '<input type="checkbox" name="f_check_comments[' . $id . ']">' . NR;
			
			$act = '<a href="' . $this_url . 'foto-comment-edit/'. $id . '">' . t('Изменить') . '</a>';
			
			$comments_date = $row['foto_comments_date'];
			
			$author = '';
			if ( $row['foto_comments_users_id'] ) $author = '<span class="admin">' . $row['users_nik'] . '</span>';
			elseif ($row['foto_comments_comusers_id']) $author = '<span class="comuser">' . $row['comusers_nik'] . '</span> (' . t('комюзер') . ' ' . $row['foto_comments_comusers_id'] . ')';
			else $author = '<span class="anonymous">' . $row['foto_comments_author_name'] . '</span> (' . t('анонимно') . ')';
			
			$page_slug = $row['foto_slug'];
			$page_title = '<a target="_blank" href="' . $view_url . $page_slug . '#comment-' . $id . '">' . htmlspecialchars( $row['foto_title'] ) . '</a>';
			
			// определим XSS и визуально выделим такой комментарий
			$comments_content_xss_start = mso_xss_clean($row['foto_comments_content'], '<span style="color: red">XSS!!! ', '');
			if ($comments_content_xss_start) $comments_content_xss_end = '</span>';
				else $comments_content_xss_end = '';
			
			$comments_content = htmlspecialchars($row['foto_comments_content']);
			$comments_content = str_replace('&lt;p&gt;', '<br>', $comments_content);
			$comments_content = str_replace('&lt;/p&gt;', '', $comments_content);
			$comments_content = str_replace('&lt;br /&gt;', '<br>', $comments_content);
			
			
			if ( $row['foto_comments_approved'] > 0 ) $comments_approved = '+';
				else $comments_approved = '-';
				
			$out = $comments_content_xss_start 
					. '<strong>' . $author . t(' написал в') 
					. ' «' . $page_title . '»</strong> (' . $comments_date. ') ip: ' 
					. $row['foto_comments_author_ip'] 
					. $comments_content_xss_end 
					. '<p>' . $comments_content . '</p>' . NR;
						
			
			$CI->table->add_row($id, $id_out, $comments_approved, $out, $act);
		}
	}
	

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	

	echo $CI->table->generate();
	
	echo '
		<p class="br">' . t('C отмеченными:') . '
		<input type="submit" name="f_aproved_submit" value="' . t('Разрешить') . '">
		<input type="submit" name="f_unaproved_submit" value="' . t('Запретить') . '">
		<input type="submit" name="f_delete_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p>
		';
	echo '</form>';
	
	mso_hook('pagination', $pagination);

	
?>