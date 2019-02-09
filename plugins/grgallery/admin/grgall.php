<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

	function edit_grgall($args)
	{
	global $MSO;
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
	
	
		
	// разрешенные типы файлов
	$allowed_types = mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz');

	
	
	#---- этот кусок кода добавляет папку с номером записи для картинок
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	$id = $args[0];	// получаем id страницы
	$new_dir = getinfo('uploads_dir').$grgll['uploads_pict_dir'];
	if (is_numeric($id))
		{	
		$new_dir .= '/'.$grgll['prefix'].$id;
		if ( !is_dir($new_dir) ) // если нету папки
			{
			@mkdir($new_dir, 0777); // нет каталога, пробуем создать
			@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
			@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
			}
		}
	#---- создали папки для разных картинок для текущей записи
	

	// описания файлов хранятся в виде серилизованного массива в
	// uploads/../_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $new_dir . '/_mso_i/_mso_descriptions.dat';

	if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
		write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив

	if (file_exists( $fn_mso_descritions )) // файл есть
		{
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
		}
	else $mso_descritions = array();

	# редактирование описания
	if ( $post = mso_check_post(array('f_session2_id',  'f_file_name', 'f_file_description', 'f_submit')) )
		{
		$file_name = $post['f_file_name'];
		foreach ($post['f_file_description'] as $key=>$descr)
			{
			mso_checkreferer();
			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$file_name[$key]]);
			$mso_descritions[$file_name[$key]]=$descr;
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл
			echo '<div class="update">' . t('Для файла ', 'admin').$file_name[$key].t(' описание обновлено!', 'admin') . '</div>';
			}
		}
	# end редактирования описания
	
	
	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session2_id', 'f_check_files', 'f_submit')) )
		{
		mso_checkreferer();
		foreach ($post['f_check_files'] as $file)
			{
			@unlink($new_dir .'/'. $file);
			@unlink($new_dir.'/_mso_i/' . $file);
			@unlink($new_dir.'/mini/' . $file);
			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$file]);
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл
			}
		echo '<div class="update">' . t('Выполнено', 'admin') . '</div>';
		}
	# end удаления выделенных файлов
		
	
	# запишем какая картинка главная
	if ( $post = mso_check_post(array('f_session2_id',  'f_radio_files', 'f_submit')) )
		{
		mso_checkreferer();
		$f_radio_files = $post['f_radio_files'];
		$res = mso_add_meta ('mpict', $id, 'page', $f_radio_files);
		}
	# end записывания о главной картинке
	
	
	# обновление всех миниатюр в каталоге
	if ( $post = mso_check_post(array('f_session2_id', 'f_update_mini_submit')) )
		{
		mso_checkreferer();
		require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
		// получаем все файлы в каталоге
		$uploads_dir = $new_dir;

		// все файлы в массиве $dirs
		$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
		if (!$dirs) $dirs = array();
		
		$allowed_ext = explode('|', $allowed_types);

		foreach ($dirs as $file)
			{
			if (@is_dir($uploads_dir . $file)) continue; // это каталог
			$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
			if (!in_array($ext, $allowed_ext)) continue; // запрещенный тип файла
			
			if ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png')
				{
				$up_data = array();
				$up_data['full_path'] = $uploads_dir . $file;
				$up_data['file_path'] = $uploads_dir;
				$up_data['file_name'] = $file;
				
				$r = array();
				$r['userfile_mini'] = 1; // делать миниатюру
				$r['userfile_mini_size'] = $post['f_userfile_mini_size'];
				$r['mini_type'] = $post['f_mini_type'];
				$r['prev_size'] = 150;
				
				mso_upload_mini($up_data, $r); // миниатюра 
				mso_upload_prev($up_data, $r); // превьюшка
				}
			}

			echo '<div class="update">' . t('Выполнено', 'admin') . '</div>';
		}
	# end обновления всех миниатюр в каталоге
	
	# загрузка нового файла
	if ( $post = mso_check_post(array('f_session2_id', 'f_submit')) )
		{
		mso_checkreferer();
		require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
		// параметры для mso_upload
		$mso_upload_ar1 = array( // конфиг CI-библиотеки upload
				'upload_path' => $new_dir,
				'allowed_types' => $allowed_types,
			);
		
		$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 300);
		$resize_images = (int) mso_get_option('resize_images', 'general', 600);
		$mso_upload_ar2 = array( // массив прочих опций
				'userfile_title' => '', // описание файла
				'fn_mso_descritions' => $fn_mso_descritions, // файл для описаний
				'userfile_resize' => TRUE, // нужно ли менять размер
				'userfile_resize_size' => $resize_images, // размер
				'userfile_water' => mso_get_option('use_watermark', 'general', 0), // нужен ли водяной знак
				'userfile_water_file' => getinfo('uploads_dir') . 'watermark.png', // файл водяного знака
				'water_type' => mso_get_option('watermark_type', 'general', 1), // тип водяного знака
				'userfile_mini' => TRUE, // делать миниатюру?
				'userfile_mini_size' => $size_image_mini, // размер миниатюры
				'mini_type' => mso_get_option('image_mini_type', 'general', 1), // тип миниатюры
				'prev_size' => 150, // размер превьюхи
				'message1' => '', // не выводить сообщение о загрузке каждого файла
				// 'message2' => '',
				
			);
		
		$new_files = mso_prepare_files('f_userfile');
		
		if ($new_files)
			{
			$res = false; // результат загрузки формируем поэлементно с загрузкой файлов
			foreach ($new_files as $key => $val)
				{
				$_FILES[$key] = $val; // формируем $_FILES для одиночного файла
				$res = mso_upload($mso_upload_ar1, $key, $mso_upload_ar2);
				unset($_FILES[$key]);
				}
		
			if (!$res) echo '<div class="error">' . t('Возникли ошибки при загрузке', 'admin') . '</div>';
		
			// после загрузки сразу обновим массив описаний - он ниже используется
			if (file_exists( $fn_mso_descritions )) // файла нет, нужно создать массив
				{
				// массив данных: fn => описание )
				$mso_descritions = unserialize(read_file($fn_mso_descritions)); // получим из файла все описания
				}
			else $mso_descritions = array();
			}
		}
	# end загрузки нового файла

	
	# редактирование существующих меток
	if ( $post = mso_check_post(array('f_session2_id', 'f_submit')) )
		{
		mso_checkreferer();
		require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку
		$tagsgroup = get_group_tag(array('cache' => true, 'inverse' => true));
		//$tagsgroup = get_group_tag(array('cache' => true, 'inverse' => true));
		$arr_post_price_tag_page = (isset($post['price_tag'])) ? $post['price_tag'] : array();
		$sarrusegroup = array();
		if ($id != '')	//---Обрабатываем теги страницы, цены потом отдельно
			{
			$data = array ('meta_key' => 'price', 'meta_id_obj' => $id, 'meta_table' => 'page');		
			$CI->db->where($data);
			$CI->db->delete('meta'); //--удаляем все цены тэгов страницы
			
			$data = array ('meta_key' => 'tags', 'meta_id_obj' => $id, 'meta_table' => 'page');		
			$CI->db->where($data);
			$CI->db->delete('meta'); //--удаляем все тэги страницы
		
			$ft = 0;
			if (isset ($post['use_tag']))
				{
					foreach ($post['use_tag'] as $key => $val)
						{
							if (in_array($val, $tagsgroup)) continue;
							$ft = 1;
							$data['meta_value'] = $val;
							$CI->db->insert('meta', $data);
							$arr_price_tag_page[$val] = (isset($arr_post_price_tag_page[$key])) ? $arr_post_price_tag_page[$key] : 0;
							if (isset($tagsgroup[$val]) and !isset($sarrusegroup[$tagsgroup[$val]]))
								{
								$sarrusegroup[$tagsgroup[$val]] = $tagsgroup[$val];
								$data['meta_value'] = $tagsgroup[$val];
								$CI->db->insert('meta', $data);
								}
						}
					if ($ft == 1)
						{
							$price_tag_page = serialize($arr_price_tag_page);
							$data = array ('meta_key' => 'price', 'meta_id_obj' => $id, 'meta_table' => 'page', 'meta_value' => $price_tag_page);
							$CI->db->insert('meta', $data);
						}
				}
			}
		}

	# редактирование новых меток
	if ( $post = mso_check_post(array('f_session2_id', 'new_tag', 'f_submit')) )
		{
		mso_checkreferer();
		if (isset($post['new_tag']) and $post['new_tag'] != '')
			{
			$ins_data = array ('meta_key'=>'tags', 'meta_id_obj'=>$id, 'meta_table'=>'page', 'meta_value'=>$post['new_tag']);
			$res = $CI->db->insert('meta', $ins_data);
			$res = $CI->db->insert_id();
			if ($post['new_tag_price'] != '')	
				{
				$CI->db->select('meta_value, meta_desc, meta_table, meta_menu_order, meta_id_obj, meta_key, meta_id');
				$CI->db->where('meta_id_obj', $id);
				$CI->db->group_by('meta_value');
				$query = $CI->db->get('meta');
				if ($query->num_rows() > 0)
					{
						$price_tags = array();
						foreach ($query->result_array() as $row)
							{
							if ($row['meta_key'] == 'price') $price_tags['price_tags_page'] = $row['meta_value'];
							}
					}				
					$price = @unserialize($price_tags['price_tags_page']);
					$price[$post['new_tag']] = $post['new_tag_price'];
					
					$data = array ('meta_key' => 'price', 'meta_id_obj' => $id, 'meta_table' => 'page');		
					$CI->db->where($data);
					$CI->db->delete('meta'); //--удаляем все тэги страницы
					$price_tag_page = serialize($price);
					$data = array ('meta_key' => 'price', 'meta_id_obj' => $id, 'meta_table' => 'page', 'meta_value' => $price_tag_page);
					$CI->db->insert('meta', $data);
				}
			}
		}
		# конец редактирования новых меток
	mso_flush_cache();
	}
?>