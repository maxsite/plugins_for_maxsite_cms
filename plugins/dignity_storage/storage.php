<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

// начало шаблона
require(getinfo('template_dir') . 'main-start.php');

// загружаем опции
$options = mso_get_option('plugin_dignity_storage', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'storage';
if ( !isset($options['header']) ) $options['header'] = t('Хранилище', __FILE__);
if ( !isset($options['allowed_types']) ) $options['allowed_types'] = 'gif|jpg|jpeg|png'; 

// meta-тэги
mso_head_meta('title', $options['header']);

echo '<h1>' . $options['header'] . '</h1>';

$out = '';

if (is_login_comuser())
{
    
	$storage_path = getinfo('uploads_dir') . 'comusers_storage';
	$path = getinfo('uploads_dir') . 'comusers_storage/' . 'comuser' . getinfo('comusers_id');
	$delete_path = getinfo('uploads_dir') . 'comusers_storage/' . 'comuser' . getinfo('comusers_id') . '/';
    
	// нет каталога, пробуем создать
	if ( ! is_dir($storage_path) ) @mkdir($storage_path, 0777);
	if ( ! is_dir($path) ) @mkdir($path, 0777);
	
        $CI = & get_instance();
        $CI->load->helper('file');
        $CI->load->helper('directory');
        $CI->load->helper('form');
        
        // разрешенные типы файлов
        $allowed_types = $options['allowed_types'];
        
        if ( !isset($post['f_userfile_resize'])) $f_userfile_resize = '';
        if ( !isset($post['f_resize_images'])) $resize_images = 640;
                
        # загрузка нового файла
	if ( $post = mso_check_post(array('f_session_id', 'f_upload_submit')) )
	{
		mso_checkreferer();
		
		require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
		
		// параметры для mso_upload
		$mso_upload_ar1 = array( // конфиг CI-библиотеки upload
				'upload_path' => $path,
				'allowed_types' => $allowed_types,
                                'max_size' => '200',
			);
			
		$mso_upload_ar2 = array( // массив прочих опций
				'userfile_resize' => isset($post['f_userfile_resize']), // нужно ли менять размер
				'userfile_resize_size' => $post['f_userfile_resize_size'], // размер
				'mini_type' => 7, // тип миниатюры (Уменьшения и обрезки (crop) в квадрат=
				'prev_size' => 100, // размер превьюхи
				'message1' => '',
			);
		
		// запомним указанные размеры и выставим их для полей формы вновь
		$f_userfile_resize = isset($post['f_userfile_resize']);
		$f_userfile_resize_size = $post['f_userfile_resize_size'];
		
		// подготовим массив $_FILES - у нас множественная загрузка
		$new_files = mso_prepare_files('f_userfile');
		
		$res = false; // результат загрузки
		// формируем поэлементно с загрузкой файлов
		foreach ($new_files as $key => $val)
		{
			$_FILES[$key] = $val; // формируем $_FILES для одиночного файла
			$res = mso_upload($mso_upload_ar1, $key, $mso_upload_ar2);
			unset($_FILES[$key]);
		}
		
		if ($res) $out .= '<div class="update">' . t('Загрузка выполнена') . '</div>';
			else $out .= '<div class="error">' . t('Возникли ошибки при загрузке') . '</div>';
	}
	
	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session2_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();

		foreach ($post['f_check_files'] as $file)
		{
			@unlink($delete_path . $file);
			@unlink($delete_path . '_mso_i/' . $file);
			@unlink($delete_path . 'mini/' . $file);
		}
		
		$out .= '<div class="update">' . t('Выполнено') . '</div>';
	}
        
        // форма загрузки
	$out .= '
		<div class="upload_file">
		<p>' . t('Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере.', __FILE__) . '<br>'
                . t('После этого нажмите кнопку «Загрузить».', __FILE__) . '<br>'
                . t('Размер файла не должен превышать', __FILE__) . ' ' . '200Кб' . '<br>'
		. t('Разрешенные типы файлов:', __FILE__) . ' ' . str_replace('|', ', ', $options['allowed_types']) .'.</p>';
		
	$out .= '<form method="post" enctype="multipart/form-data" class="admin_uploads_form">' . mso_form_session('f_session_id');
                
        $out .= '<input type="file" name="f_userfile[]" size="40"><br>';
	$out .= '<input type="file" name="f_userfile[]" size="40"><br>';
	$out .= '<input type="file" name="f_userfile[]" size="40"><br>';
	$out .= '<input type="file" name="f_userfile[]" size="40"><br>';
	$out .= '<input type="file" name="f_userfile[]" size="40"><br>';
	
	$out .= '&nbsp;<input type="submit" name="f_upload_submit" value="' . t('Загрузить') . '"></p>
		<p><label><input type="checkbox" name="f_userfile_resize" ' . $f_userfile_resize . 'value=""> ' . t('Для изображений изменить размер до') . '</label>
		    <input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).') . '</p>
		</form>
		</div><hr>';
		
	$out .= '<p>' . t('Скопируйте BB-Code и вставьте его в нужном месте на сайте.', __FILE__) . '</p>';
		
	// нужно вывести навигацию по каталогам
	$all_dirs = directory_map($path, true);
	
	if (!$all_dirs) $all_dirs = array();
	
	#asort($all_dirs);
	
	$allowed_ext = explode('|', $allowed_types);
	
	$http_path = getinfo('uploads_url') . 'comusers_storage/' . 'comuser' . getinfo('comusers_id') . '/';
	
	$out .= '<form method="post">' . mso_form_session('f_session2_id');
	
	foreach ($all_dirs as $file)
	{
		
		// это каталог
		if (@is_dir($path . $file)) continue; 
	    
		// расширение файла
		$ext = strtolower(str_replace('.', '', strrchr($file, '.')));
		
		if (!in_array($ext, $allowed_ext)) continue; // запрещенный тип файла
		
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
            $out .= '<table><tr><td><img src="' . $http_path . $file . '" height="80px" width="80px"></td>';
            $out .= '<td><p>BB-Code: <input type="text" value="[img]'. $http_path . $file .'[/img]" size="60"></p>';
			$out .= 'Ссылка: <input type="text" value="'. $http_path . $file .'" size="60"></p></td></tr></table>';
		
			$out .= form_checkbox('f_check_files[]', $file, false, 'class="f_check_files"');
		
		}
	}
	
	$out .= '<p><input type="submit" name="f_delete_submit" value="' . t('Удалить') . '"></p>';
	$out .= '</form>';

}
else
{
    $out .= t('Только авторизованные пользователи могут загружать файлы на сайт.', __FILE__);
}

echo $out;

require(getinfo('template_dir') . 'main-end.php');

# end of file
