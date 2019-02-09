<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	mso_cur_dir_lang('admin');
	
	if ( !mso_check_allow('grgallery_edit') ) 
	{
	 echo t('Доступ запрещен', 'plugins/grgallery');
	 return;
	}
	
	$CI = & get_instance();
	require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик 
	require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования	
	
	$slug = mso_segment('4');
	
	$f_id = mso_get_cat_from_slug($slug, $full = false);
	
	# редактирование существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 
									'f_new_parent', 'f_new_name', 
									'f_new_desc', 'f_new_slug', 
									'f_new_order')) )
	{
		mso_checkreferer();
		$f_id = (int) $post['id_cat'];
		$data = array(
			'category_id_parent' => (int) $post['f_new_parent'],
			'category_name' => $post['f_new_name'],
			'category_desc' => $post['f_new_desc'],
			'category_slug' => $post['f_new_slug'],
			'category_menu_order' => (int) $post['f_new_order']
			);		
		
		
		if ($f_id == false)
			{//pr ($data);
			$result = mso_new_category($data);
			}
		else
			{
			$data['category_id'] = $f_id;
			$result = mso_edit_category($data);
			}
		
		if (isset($result['result']) and $result['result']) 
			{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Обновлено!', 'admin') . '</div>';
			}
		else
			{echo '<div class="error">' . t('Ошибка обновления', 'admin') . '</div>';}
	}

	# удаление существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'delcat', 'chcat')) )
	{
		mso_checkreferer(); 
		//require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования		
		// подготавливаем данные
		foreach ($post['chcat'] as $key => $val)
			{
			$data = array('category_id' => $val);
			$result = mso_delete_category($data);		
			if (isset($result['result']) and $result['result']) 
			{	
				echo '<div class="update">' . t('Удалено!', 'admin') . ' ' . $result['description'] . '</div>';
			}
			else
				echo '<div class="error">' . t('Ошибка удаления', 'admin') . ' ' . $result['description'] . '</div>';
			}
	}
	
	// вывод списка страниц
	
	mso_flush_cache();
	require_once($MSO->config['plugins_dir'] . 'grgallery/plugin/category/admin/list.php');
	echo $out;
	
	//if (mso_segment(3) != 'category') $pagination['next_url'] = 'category/next';
	//mso_hook('pagination', $pagination);
?>