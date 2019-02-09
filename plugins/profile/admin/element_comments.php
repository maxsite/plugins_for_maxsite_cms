<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	mso_cur_dir_lang(__FILE__);
	$CI = & get_instance();

	$options_key2 = 'profile_profiles';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$element['title'] = isset($post['f_e_title']) ? $post['f_e_title'] : 'Комментарий к статье';
		$element['name'] = isset($post['f_name']) ? $post['f_name'] : 'Комментарий';
		$element['all'] = isset($post['f_all']) ? $post['f_all'] : 'Все комментарии';
		$element['title_go'] = isset($post['f_title_go']) ? $post['f_title_go'] : 'Перейти к статье';
		$element['all_link'] = isset($post['f_all_link']) ? $post['f_all_link'] : 'comments';
		$element['img'] = isset($post['f_img']) ? $post['f_img'] : getinfo('plugins_url') . 'profile/img/comment.png';
		$element['filename'] = isset($post['f_filename']) ? $post['f_filename'] : 'comments';
		$element['slug'] = isset($post['f_slug']) ? $post['f_slug'] : 'comments';
		
		$elements =array($element);
		mso_add_option($options_key2, $elements, 'plugins');

		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

	echo '<h1>'. t('Плагин profile'). '</h1><p class="info">'. t('Настройки подключаемого события-элемента comments.'). '</p>';


	$options = mso_get_option($options_key2, 'plugins', array());
	$options1 = isset($options[0]) ? $options[0] : array();
	
		$options1['title'] = isset($options1['title']) ? $options1['title'] : 'Комментарий к статье';
		$options1['name'] = isset($options1['name']) ? $options1['name'] : 'Комментарий';
		$options1['all'] = isset($options1['all']) ? $options1['all'] : 'Все комментарии';
		$options1['title_go'] = isset($options1['title_go']) ? $options1['title_go'] : 'Перейти к статье';
		$options1['all_link'] = isset($options1['all_link']) ? $options1['all_link'] : 'comments';
		$options1['img'] = isset($options1['img']) ? $options1['img'] : getinfo('plugins_url') . 'profile/img/comment.png';
		$options1['filename'] = isset($options1['fiename']) ? $options1['fiename'] : 'comments';
		$options1['slug'] = isset($options1['slug']) ? $options1['slug'] : 'comments';
	
	$form = '';
	 
	$form .= '<div class="admin_plugin_options">';
	$form .= '<p>' . t('Файл, добавляющий события подключаемого элемента, находится в папке profiles.', 'plugins') . '</p>';
	$form .= '<div class="error">' . t('Обязательно нужно сохранить эти настройки перед использованием.<br>По дефолту настройки подключаемых событий не инициализируются.', 'plugins') . '</div>';
	$form .= '<p><strong>' . t('title') . ' </strong><input name="f_e_title" type="text" value="' . $options1['title'] . '" /> ' . t('Фраза перед заголовком элемента, который вызвал событие') . '</p>';
	$form .= '<p><strong>' . t('name') . ' </strong><input name="f_name" type="text" value="' . $options1['name'] . '" /> ' . t('Название события') . '</p>';
	$form .= '<p><strong>' . t('all') . ' </strong><input name="f_all" type="text" value="' . $options1['all'] . '" /> ' . t('title ссылки на страницу всех таких событий') . '</p>';
	$form .= '<p><strong>' . t('title_go') . ' </strong><input name="f_title_go" type="text" value="' . $options1['title_go'] . '" /> ' . t('title ссылки на событие на сайте') . '</p>';
	$form .= '<p><strong>' . t('all_link') . ' </strong><input name="f_all_link" type="text" value="' . $options1['all_link'] . '" /> ' . t('Ссылка (без домена) на особую страницу (если есть) всех таких событий.<br>Если оставить пустым ссылка будет на автоматические страницы на основе slug') . '</p>';
	$form .= '<p><strong>' . t('img') . ' </strong><input name="f_img" type="text" value="' . $options1['img'] . '" /> ' . t('Иконка события, с которой будет ссылка на все такие события') . '</p>';
	$form .= '<p><strong>' . t('filename') . ' </strong><input name="f_filename" type="text" value="' . $options1['filename'] . '" /> ' . t('Имя файла в profile/profiles/ (указывать без .php), возвращающего события') . '</p>';
	$form .= '<p><strong>' . t('slug') . ' </strong><input name="f_slug" type="text" value="' . $options1['slug'] . '" /> ' . t('Cлуг для автоматического формирования подстраниц с конкретным событием') . '</p>';	
	$form .= '</div>';


	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;"></form>';

