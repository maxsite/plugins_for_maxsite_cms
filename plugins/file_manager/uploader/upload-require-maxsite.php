<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

if ( $post = mso_check_post(array('f_session2_id')) AND AccessOK($post['f_session2_id']) ){
	$CI = & get_instance();
	$CI->load->helper('file_helper'); 
	
	$current_dir = $post['f_directory'];	
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );
	$allowed_types = (isset($options['allowed_types'])) ? $options['allowed_types'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';
	$path = getinfo('uploads_dir') . $current_dir;
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';
	
	require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
	
	// параметры для mso_upload
	$mso_upload_ar1 = array( // конфиг CI-библиотеки upload
			'upload_path' => getinfo('uploads_dir') . $current_dir,
			'allowed_types' => $allowed_types,
		);
		
	$mso_upload_ar2 = array( // массив прочих опций
			'userfile_title' => $post['f_userfile_title'], // описание файла
			'fn_mso_descritions' => $fn_mso_descritions, // файл для описаний
			'userfile_resize' => (isset($post['f_userfile_resize'])) and ($post['f_userfile_resize'] == '1'), // нужно ли менять размер
			'userfile_resize_size' => $post['f_userfile_resize_size'], // размер
			'userfile_water' => (isset($post['f_userfile_water'])) and ($post['f_userfile_water'] == '1'), // нужен ли водяной знак
			'userfile_water_file' => getinfo('uploads_dir') . 'watermark.png', // файл водяного знака
			'water_type' => $post['f_water_type'], // тип водяного знака
			'userfile_mini' => (isset($post['f_userfile_mini'])) and ($post['f_userfile_mini'] == '1'), // делать миниатюру?
			'userfile_mini_size' => $post['f_userfile_mini_size'], // размер миниатюры
			'mini_type' => $post['f_mini_type'], // тип миниатюры
			'prev_size' => 100, // размер превьюхи
			'message1' => '', // не выводить сообщение о загрузке каждого файла			
		);
			
	$res = false; // результат загрузки
	$res = mso_upload($mso_upload_ar1, 'file', $mso_upload_ar2);
	
	if ($res) {
		echo '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
	} else {
		echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"} <div class="error">' . t('Возникли ошибки при загрузке', 'admin') . '</div>';
	}	
} else echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"} <div class="error">Слишком большой файл</div>';

function AccessOK($session_id){
	// Проверка сессии
	if(!mso_checksession($session_id)){
		echo '<div class="error">Wrong session!</div>';
		return FALSE;
	}
	
	// Проверка доступа и залогинености
	if ( !mso_check_allow('file_manager') OR !is_login() ) 
	{
		echo '<div class="error">Wrong access!</div>';
		return FALSE;
	}
	
	return TRUE;
}
?>