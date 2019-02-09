<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	

	if ( $post = mso_check_post(array('f_session_id', 'f_start_submit')) )
  {
		mso_checkreferer();
    $res = taggallery_start();
    
    if (!$res) echo '<div class="update">Инициализация прошла успешно.<br/>' . $res . '</div>';
    else echo '<div class="error">' .  $res . '</div>';    
  }

	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	  
		if ( isset($post['f_template']) ) $options['template'] = $post['f_template']; else $options['template'] = 'simple';
		
		if ( isset($post['f_gallery_name']) ) $options['gallery_name'] = trim($post['f_gallery_name']);
		if ( isset($post['f_gallery_desc']) ) $options['gallery_desc'] = trim($post['f_gallery_desc']); 
		if ( isset($post['f_gallery_pag_count']) ) $options['gallery_pag_count'] = trim($post['f_gallery_pag_count']); 
		if ( isset($post['f_all_gallerys_slug']) ) $options['all_gallerys_slug'] = trim($post['f_all_gallerys_slug']);
		if ( isset($post['f_gallery_slug']) ) $options['gallery_slug'] = trim($post['f_gallery_slug']);
		if ( isset($post['f_picture_slug']) ) $options['picture_slug'] = trim($post['f_picture_slug']);
		if ( isset($post['f_album_slug']) ) $options['album_slug'] = trim($post['f_album_slug']);
		if ( isset($post['f_gallery_prefix']) ) $options['gallery_prefix'] = trim($post['f_gallery_prefix']);
		if ( isset($post['f_picture_prefix']) ) $options['picture_prefix'] = trim($post['f_picture_prefix']);
		if ( isset($post['f_album_prefix']) ) $options['album_prefix'] = trim($post['f_album_prefix']);
		if ( isset($post['f_main_slug']) ) $options['main_slug'] = trim($post['f_main_slug']);
		if ( isset($post['f_comments_plugin']) ) $options['comments_plugin'] = trim($post['f_comments_plugin']);
		if ( isset($post['f_default_album_thumb_url']) ) $options['default_album_thumb_url'] = trim($post['f_default_album_thumb_url']);
		if ( isset($post['f_default_gallery_thumb_url']) ) $options['default_gallery_thumb_url'] = trim($post['f_default_gallery_thumb_url']);

		if ( isset($post['f_all_gallerys_text']) ) $options['all_gallerys_text'] = trim($post['f_all_gallerys_text']);
		if ( isset($post['f_all_gallerys_slug']) ) $options['all_gallerys_slug'] = trim($post['f_all_gallerys_slug']);
		if ( isset($post['f_all_gallerys_desc']) ) $options['all_gallerys_desc'] = trim($post['f_all_gallerys_desc']);
		
		if ( isset($post['f_gallerys_not_in_text']) ) $options['gallerys_not_in_text'] = trim($post['f_gallerys_not_in_text']);
		if ( isset($post['f_gallerys_not_in_slug']) ) $options['gallerys_not_in_slug'] = trim($post['f_gallerys_not_in_slug']);
		if ( isset($post['f_gallerys_not_in_desc']) ) $options['gallerys_not_in_desc'] = trim($post['f_gallerys_not_in_desc']);		
		
		if ( isset($post['f_date_field']) ) $options['date_field'] = trim($post['f_date_field']);
		if ( isset($post['f_end_text']) ) $options['end_text'] = trim($post['f_end_text']);

    $options['cache_flag']  = isset($post['f_cache_flag']) ? TRUE : FALSE;

		mso_add_option('taggallery', $options , 'plugins');
		$template_setting_url = getinfo('siteurl') . 'admin/plugin_options/taggallery/templates/' . $options['template']; // адрес опций шаблона нужно поменять

  	$message = 'Обновлено!' ;
		echo '<div class="update">' . t($message, 'plugins') . '</div>';
	}

	
		
	# получим имеющиеся шаблоны 
	$templates = directory_map(getinfo('plugins_dir') . 'taggallery/templates', true); // 
	$list_templates = array();
	
	if ($templates)
	 foreach($templates as $template)
   {
	  $template_dir = getinfo('plugins_dir') . 'taggallery/templates/' . $template;
		if (is_dir($template_dir) and file_exists($template_dir . '/index.php'))
		{
		  $list_templates[$template] = $template;
		} 
	 }		
	 
	// $date_fields = array('Дата добавления' => 'picture_date' , 'Дата файла' => 'picture_date_file' , 'Дата фото' => 'picture_date_photo');
	 $date_fields = array('picture_date' => 'Дата добавления' , 'picture_date_file' => 'Дата файла' , 'picture_date_photo' => 'Дата фото');
        
		$form = '';
		$form .= '<th>' . t('Настройки вывода альбомов', 'plugins') . '</th>';

		$form .= '<tr><td><H3>' . 'Основные'  . '</H3></td></tr>';

		$form .= '<tr><td>' . t('Шаблон галереи:', 'plugins') . ' </td><td>' . form_dropdown('f_template', $list_templates , $options['template']) . '</td></tr>';		

 // сформируем ссылку на настройки шаблона
		$form .= '<tr><td><strong><a href="' . $template_setting_url .'" target="_blank">Опции шаблона</a></strong></td></tr>';
		
		$form .= '<tr><td>' . t('Название галереи изображений', 'plugins') . ' </td>' . '<td><input name="f_gallery_name" type="text" value="' . $options['gallery_name'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Описание галереи изображений', 'plugins') . ' </td>' . '<td><textarea rows="3" name="f_gallery_desc">' . $options['gallery_desc'] . '</textarea></td></tr>';
		
		$form .= '<tr><td>' . t('Картинок на странице', 'plugins') . ' </td>' . '<td><input name="f_gallery_pag_count" type="text" value="' . $options['gallery_pag_count'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Обложка альбома, если не установлена', 'plugins') . ' </td>' . '<td> <input name="f_default_album_thumb_url" type="text" value="' . $options['default_album_thumb_url'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Обложка галереи, если не установлена', 'plugins') . ' </td>' . '<td> <input name="f_default_gallery_thumb_url" type="text" value="' . $options['default_gallery_thumb_url'] . '"></td></tr>';

		$form .= '<tr><td><H3>' . 'Формирование адреса страниц'  . '</H3></td></tr>';


		$form .= '<tr><td>' . t('Slug для галереи', 'plugins') . ' </td>' . '<td> <input name="f_gallery_slug" type="text" value="' . $options['gallery_slug'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Slug для картинки', 'plugins') . ' </td>' . '<td> <input name="f_picture_slug" type="text" value="' . $options['picture_slug'] . '"></td></tr>';		

		$form .= '<tr><td>' . t('Slug для альбома', 'plugins') . ' </td>' . '<td> <input name="f_album_slug" type="text" value="' . $options['album_slug'] . '"></td></tr>';
		    
		$form .= '<tr><td>' . t('Префикс галереи', 'plugins') . ' </td>' . '<td> <input name="f_gallery_prefix" type="text" value="' . $options['gallery_prefix'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Префикс картинки', 'plugins') . ' </td>' . '<td> <input name="f_picture_prefix" type="text" value="' . $options['picture_prefix'] . '"></td></tr>';		

		$form .= '<tr><td>' . t('Префикс альбома', 'plugins') . ' </td>' . '<td> <input name="f_album_prefix" type="text" value="' . $options['album_prefix'] . '"></td></tr>';
	
		$form .= '<tr><td>' . t('Slug для главной', 'plugins') . ' </td>' . '<td> <input name="f_main_slug" type="text" value="' . $options['main_slug'] . '"></td></tr>';
				
		$form .= '<tr><td><H3>' . 'Дополнительные страницы'  . '</H3></td></tr>';
				
		$form .= '<tr><td>' . t('2-й сегмент для всех галерей', 'plugins') . ' </td>' . '<td> <input name="f_all_gallerys_slug" type="text" value="' . $options['all_gallerys_slug'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Название всех галерей<br>(пусто - не выводится)', 'plugins') . ' </td>' . '<td> <input name="f_all_gallerys_text" type="text" value="' . $options['all_gallerys_text'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Desc страницы всех галерей', 'plugins') . ' </td>' . '<td><textarea rows="3" name="f_all_gallerys_desc">' . $options['all_gallerys_desc'] . '</textarea></td></tr>';				

		$form .= '<tr><td>' . t('2-й сегмент для других галерей<br>(не вошедших ни в один альбом)', 'plugins') . ' </td>' . '<td> <input name="f_gallerys_not_in_slug" type="text" value="' . $options['gallerys_not_in_slug'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Название других галерей<br>(пусто - не выводится)', 'plugins') . ' </td>' . '<td> <input name="f_gallerys_not_in_text" type="text" value="' . $options['gallerys_not_in_text'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Desc страницы других галерей', 'plugins') . ' </td>' . '<td><textarea rows="3" name="f_gallerys_not_in_desc">' . $options['gallerys_not_in_desc'] . '</textarea></td></tr>';	

		$form .= '<tr><td><H3>' . 'Дополнительные опции'  . '</H3></td></tr>';
		
     $chckout = ''; 
     if (!isset($options['cache_flag']))  $options['cache_flag'] = false;
     if ( (bool)$options['cache_flag'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<tr><td>' . t('Кешировать страницы галереи', 'plugins') . '</td>' . '<td> <input name="f_cache_flag" type="checkbox" ' . $chckout . '"></td></tr>';		

		$form .= '<tr><td>' . t('Плагин для Комментариев<br>other_comments', 'plugins') . ' </td>' . '<td> <input name="f_comments_plugin" type="text" value="' . $options['comments_plugin'] . '"></td></tr>';
						
		$form .= '<tr><td>' . t('Выводить, как дату:', 'plugins') . ' </td><td>' . form_dropdown('f_date_field', $date_fields , $options['date_field']) . '</td></tr>';					

		$form .= '<tr><td>' . t('Текст в самом конце<br>например AdSenese', 'plugins') . ' </td>' . '<td><textarea rows="5" name="f_end_text">'  . $options['end_text'] . '</textarea></td></tr>';
				
   $form .= '<tr><td><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" /></td></tr>';
				
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<table>';
        echo $form;
    echo '</table>';

    echo '<table border="1"><tr><td><H3>Восстановление БД из массивов mso_descriptions.dat</H3></td></tr>';
		echo '<tr><td align = "center"><input type="submit" name="f_start_submit" value="' . t('Восстановить', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		echo '</td></tr></table>';
		echo '</form>';

?>