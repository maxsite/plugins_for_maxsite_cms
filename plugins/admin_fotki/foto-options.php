<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	$options_key = 'admin_fotki';

	if ( $post = mso_check_post(array('f_session_id','f_submit', 'f_upload_path', 'f_size_mini', 'f_size_foto')) )
	{
		mso_checkreferer();

		$options = array();
		$options['upload_path'] = $post['f_upload_path'];
		$options['size_mini'] = $post['f_size_mini'];
		$options['size_foto'] = $post['f_size_foto'];
		$options['upload_origin'] = isset($post['f_upload_origin']) ? $post['f_upload_origin'] : 0;


		$options['url_page_foto'] = $post['f_url_page_foto'];
		$options['url_page_album'] = $post['f_url_page_album'];
		$options['url_page_gallery'] = $post['f_url_page_gallery'];
	
		$options['foto_count'] = $post['f_foto_count'];
		$options['other_foto_title'] = $post['f_other_foto_title'];
		$options['other_foto_count'] = $post['f_other_foto_count'];
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">Обновлено!</div>';
	}
?>
	
<h1>Настройка Фотки</h1>
<p class="info">На это странице можно задать значения параметров по умолчанию, используемых при загрузке фотографий</p>

<?php	
	$options = mso_get_option($options_key, 'plugins', array());

	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	if ( !isset($options['size_mini']) ) $options['size_mini'] = 250;
	if ( !isset($options['size_foto']) ) $options['size_foto'] = 800;
	if ( !isset($options['upload_origin']) ) $options['upload_origin'] = 1;

	if ( !isset($options['url_page_foto']) ) $options['url_page_foto'] = 'foto';
	if ( !isset($options['url_page_album']) ) $options['url_page_album'] = 'albums';
	if ( !isset($options['url_page_gallery']) ) $options['url_page_gallery'] = 'album';
	
	if ( !isset($options['foto_count']) ) $options['foto_count'] = 12;
	
	if ( !isset($options['other_foto_count']) ) $options['other_foto_count'] = 5;
	if ( !isset($options['other_foto_title']) ) $options['other_foto_title'] = 'Еще фотографии';
	
	$form = '';	
	
	$form .= '<p><span><strong>Путь загрузки: </strong></span><input name="f_upload_path" type="text" value="'.$options['upload_path'].'"><span> <i>Путь загрузки относительно папки uploads/</i></span></p>';
	$form .= '<p><span><strong>Размер миниатюры: </strong></span><input name="f_size_mini" type="text" value="'.$options['size_mini'].'"></p>';
	$form .= '<p><span><strong>Размер фотографии: </strong></span><input name="f_size_foto" type="text" value="'.$options['size_foto'].'"></p>';
	$checked = ( $options['upload_origin'] ) ? ' checked' : '';
	$form .= '<p><span><strong>Загружать оригинал: </strong></span><input name="f_upload_origin" type="checkbox" value="accept" ' . $checked . '></p>';
	
	$form .= '<p><span><strong>Страница "фото": </strong></span><input name="f_url_page_foto" type="text" value="'.$options['url_page_foto'].'"><span> <i>Адрес страницы с фотографией</i></span></p>';

	$form .= '<p><span><strong>Страница "альбомы": </strong></span><input name="f_url_page_album" type="text" value="'.$options['url_page_album'].'"><span> <i>Адрес страницы с альбомами</i></span></p>';

	$form .= '<p><span><strong>Страница "галереи": </strong></span><input name="f_url_page_gallery" type="text" value="'.$options['url_page_gallery'].'"><span> <i>Адрес страницы альбома с фотографиями</i></span></p>';
	
	$form .= '<p><span><strong>Кол-во на странице: </strong></span><input name="f_foto_count" type="text" value="'.$options['foto_count'].'"><span><i>Кол-во фотографий, отображаемых на странице в альбоме</i></span></p>';	
	
	$form .= '<br><h3>Блок миниатюр случайных фотографий</h3>';
	$form .= '<p><span><strong>Заголовок блока: </strong></span><input name="f_other_foto_title" type="text" value="'.$options['other_foto_title'].'"><span><i>Заголовок блока миниатюр случайных фотографий на странице</i></span></p>';
	
	$form .= '<p><span><strong>Кол-во миниатюр: </strong></span><input name="f_other_foto_count" type="text" value="'.$options['other_foto_count'].'"><span><i>Кол-во миниатюр, отображаемых на странице в блоке случайных миниатюр фотографий</i></span></p>';	
	
	echo '<form action="" method="post">'.mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value=" Сохранить изменения " />';
	echo '</form>';
		
?>