<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

if ( $post = mso_check_post(array('f_session2_id')) AND $comusers_id = AccessOK($post['f_session2_id']) )
{
	$CI = & get_instance();
	$CI->load->helper('file_helper'); 
	
	
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );
	
	$allowed_types = (isset($options['allowed_types_comuser'])) ? $options['allowed_types_comuser'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';
	
	$subdir = (isset($options['user_subdir'])) ? $options['user_subdir'] : 'userfile';
	
	
	$path = getinfo('uploads_dir') . $subdir . '/' . $comusers_id . '/';
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';

	// нужно создать каталог,  _mso_i и mini если нет
	if ( ! is_dir(getinfo('uploads_dir') . $subdir) ) @mkdir(getinfo('uploads_dir') . $subdir, 0777); // нет каталога, пробуем создать
	
	if ( ! is_dir($path) ) @mkdir($path, 0777); // нет каталога, пробуем создать
	if ( ! is_dir($path . '_mso_i') ) @mkdir($path . '_mso_i', 0777); // нет каталога, пробуем создать
	if ( ! is_dir($path . 'mini') ) @mkdir($path . 'mini', 0777); // нет каталога, пробуем создать
	
	require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
	
	// параметры для mso_upload
	$mso_upload_ar1 = array( // конфиг CI-библиотеки upload
			'upload_path' => $path ,
			'allowed_types' => $allowed_types,
		);
		
	$mso_upload_ar2 = array( // массив прочих опций
			'userfile_title' => $post['f_userfile_title'], // описание файла
			'fn_mso_descritions' => $fn_mso_descritions, // файл для описаний
			'userfile_resize' => (isset($options['userfile_resize'])) ? $options['userfile_resize'] : '1', // нужно ли менять размер
			'userfile_resize_size' => (isset($options['userfile_resize_size'])) ? $options['userfile_resize_size'] : 800, // размер
			'userfile_water' => (isset($options['userfile_water'])) ? $options['userfile_water'] : '0', // нужен ли водяной знак
			'userfile_water_file' => getinfo('uploads_dir') . ((isset($options['userfile_water_file'])) ? $options['userfile_water_file'] : 'watermark.png'), // файл водяного знака
			'water_type' => (isset($options['water_type'])) ? $options['water_type'] : 4, // тип водяного знака
			'userfile_mini' => (isset($options['userfile_mini'])) ? $options['userfile_mini'] : '1', // делать миниатюру?
			'userfile_mini_size' => (isset($options['userfile_mini_size'])) ? $options['userfile_mini_size'] : 200, // размер миниатюры
			'mini_type' => (isset($options['mini_type'])) ? $options['mini_type'] : 1, // тип миниатюры
			'prev_size' => 100, // размер превьюхи
			'message1' => '', // не выводить сообщение о загрузке каждого файла			
		);
			
	 mso_flush_cache();

	$res = false; // результат загрузки
	$res = mso_upload($mso_upload_ar1, 'file', $mso_upload_ar2);
	
  recalc_userfiles($comusers_id);
	
	if ($res) {
		echo '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
	} else {
		echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}';
	}	
} else echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}' . t('Ошибка сессии - перегрузите страницу', 'admin');


function AccessOK($session_id)
{
	// Проверка сессии
	if(!mso_checksession($session_id)){
		echo '<div class="error">Wrong session!</div>';
		return FALSE;
	}

	// Проверка доступа и залогинености
	if (!($comuser = is_login_comuser()) ) 
	{
		echo '<div class="error">Wrong access!</div>';
		return FALSE;
	}
	
	return $comuser['comusers_id'];
}


function recalc_userfiles($comusers_id)
{
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
  $subdir = 'userfile';
	$subpath = $subdir . '/' . $comusers_id . '/';
	$path = getinfo('uploads_dir') . $subpath;
  
	// все файлы в массиве $dirs
	$dirs = directory_map($path, true); // только в текущем каталоге
	if (!$dirs) $dirs = array();

	$i=0;
	foreach ($dirs as $file)
	{
	  $file_full_path = $path . $file;
		if (@is_dir($file_full_path)) continue; // это каталог
		if ($file == 'avatar.jpg') continue; // это каталог
    $i++;
  }
  
  // добавляем метаполем
  $key = 'uplcount';
  
	// вначале грохаем если есть такой ключ
	$CI->db->where('meta_table', 'comusers');
	$CI->db->where('meta_id_obj', $comusers_id);
	$CI->db->where('meta_key', $key);
	$CI->db->delete('meta');
					
	// теперь добавляем как новый
	if ($i)
	{
	  $ins_data = array(
							'meta_table' => 'comusers',
							'meta_id_obj' => $comusers_id,
							'meta_key' => $key,
							'meta_value' => $i
		 );
					
	  $CI->db->insert('meta', $ins_data);  
	}  
	
}
?>