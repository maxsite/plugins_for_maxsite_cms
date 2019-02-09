<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	global $plug_url; 
	$CI = & get_instance();
	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	if ( !isset($options['size_mini']) ) $options['size_mini'] = 250;
	if ( !isset($options['size_foto']) ) $options['size_foto'] = 800;
	if ( !isset($options['upload_origin']) ) $options['upload_origin'] = 1;
	
	$f_upload_origin = $options['upload_origin'];
	$f_user_id = $MSO->data['session']['users_id'];
	$f_size_foto = $options['size_foto'];
	$f_size_mini = $options['size_mini'];
	$f_upload_path = $options['upload_path'];
	
	$editor_options = mso_get_option('editor_options', 'admin', array());

	
	if ( $post = mso_check_post(array('f_session_id','f_submit_upload_foto')) )
	{
		mso_checkreferer();

		$file = $_FILES['f_file']['name'];
		$size_foto = $post['f_size_foto'];
		$size_mini = $post['f_size_mini'];
		$title = $post['f_header'];
		$description = $post['f_descr'];
		$slug = $post['f_slug'];
		$upload_orig = $post['f_upload_origin'];
		$album_id = $post['f_album_id'];
		$tags = $post['f_tags'];
		//$date = $post['f_date'];
		//$status = $post['f_status'];
		$date_d = $post['f_date_d'];
		$date_m = $post['f_date_m'];
		$date_y = $post['f_date_y'];
		$time_h = $post['f_time_h'];
		$time_m = $post['f_time_m'];
		$time_s = $post['f_time_s'];
		if ( empty($file) ) {
			echo '<div class="error">Выберите файл!</div>';
		} else {
			
			//$date = date('Y-m-d H:i:s'); // вычислить дату	
			$date = date('Y-m-d H:i:s', mktime($time_h, $time_m, $time_s, $date_m, $date_d, $date_y) );
			$pathinfo = pathinfo( $file );
			$filename = $pathinfo['basename'];
			$s_filename = $filename;
			//pr( $pathinfo );
			$ff_name = $pathinfo['filename'];
			$ff_name = mso_slug( $ff_name );
			$ff_ext = strtolower($pathinfo['extension']);
			$filename = $ff_name . '.' . $ff_ext;
			$path = $filename; // тут путь, куда закачали

			// закачиваем файл
			// готовим массив данных
			$config_library['upload_path'] = getinfo('uploads_dir') . $f_upload_path;
			$config_library['allowed_types'] = 'gif|jpg|jpeg|png|JPG|JPEG|PNG|GIF';
			$config_library['remove_spaces'] = true;
			$config_library['file_name'] = $filename;
			$CI->load->library('upload', $config_library);
			$res = $CI->upload->do_upload('f_file');
			
			if ( $res ) {
				$up_data = $CI->upload->data();
				$CI->load->library('image_lib');
				//pr( $CI->upload->data() );
				// файл закачан
				// скопируем его в origin, если указана опция
				$f_full_name = $up_data['full_path'];
				$f_origin_name = $up_data['file_path']. 'origin/'. $up_data['file_name'];
				$f_current_name = $up_data['file_path']. 'current/'. $up_data['file_name'];
				$f_mini_name = $up_data['file_path']. 'mini/'. $up_data['file_name'];
				if ( $f_upload_origin ) {
					// копируем файл
					
					copy( $f_full_name, $f_origin_name);	
				}
				// создадим миниатюрю и закачаем в mini/
				//copy( $f_full_name, $f_mini_name);
				//copy( $f_full_name, $f_current_name);					
				# меняем размер
				// отресайзим до нужного размера и закачаем в current
				if ( $size_foto ) // нужно изменить размер
				{
					$size = abs((int)$size_foto);
					($up_data['image_width'] >= $up_data['image_height']) ? ($max = $up_data['image_width']) : ($max = $up_data['image_height']);
					if ( $size > 1 and $size < $max ) // корректный размер
					{
						$image_info = GetImageSize($f_full_name);
						$image_width = $image_info[0];
						$image_height = $image_info[1];
						
						$r_conf['new_image'] = $f_current_name;
						$r_conf['source_image'] = $f_full_name;
						$r_conf['image_library'] = 'gd2';
						$r_conf['width'] = $size;
						$r_conf['height'] = $size;
						$r_conf['maintain_ratio'] = true;
							
						if ($image_width > $image_height) // Если ширина больше высоты
						{
							$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
							$r_conf['height'] = $resize;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Ресайз изображения:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($image_width < $image_height) // Если высота больше ширины
						{
							$resize = round($size * $image_height / $image_width);
							$r_conf['width'] = $resize;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						else // Равны
						{
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}							
						
					}
					
					
				}
				// теперь делаем мини
				if ( $size_mini ) // нужно изменить размер
				{
					$image_info = GetImageSize($f_current_name);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
						
					$size = abs((int)$size_mini);
					($image_width >= $image_height) ? ($max = $image_width) : ($max = $image_height);
					if ( $size > 1 and $size < $max ) // корректный размер
					{
						$r_conf['new_image'] = $f_mini_name;
						$r_conf['source_image'] = $f_current_name;
						$r_conf['width'] = $size;
						$r_conf['height'] = $size;
						$r_conf['maintain_ratio'] = true;
							
						if ($image_width > $image_height) // Если ширина больше высоты
						{
							$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
							$r_conf['width'] = $resize;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Ресайз изображения:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
								
							$image_info = GetImageSize($f_mini_name);
							$image_width = $image_info[0];
							$image_height = $image_info[1];
							$r_conf['new_image'] = $f_mini_name;
							$r_conf['source_image'] = $f_mini_name;
						
							$r_conf['x_axis'] = round(($resize - $size) / 2);
							$r_conf['y_axis'] = 0;
							$r_conf['width'] = $size;
							$r_conf['maintain_ratio'] = false;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
								
						}
						elseif ($image_width < $image_height) // Если высота больше ширины
						{
							$resize = round($size * $image_height / $image_width);
							$r_conf['height'] = $resize;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
							$image_info = GetImageSize($f_mini_name);
							$image_width = $image_info[0];
							$image_height = $image_info[1];
							$r_conf['new_image'] = $f_mini_name;
							$r_conf['source_image'] = $f_mini_name;
						
							$r_conf['y_axis'] = round(($resize - $size) / 2);
							$r_conf['x_axis'] = 0;
							$r_conf['height'] = $size;
							$r_conf['maintain_ratio'] = false;
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
														
						}
						else // Равны
						{
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}							
						
					}
					
					
				}

				// получить EXIF !!!!
				require_once($MSO->config['plugins_dir'] . $plug_url . '/functions.php');
				//$ff = getinfo('uploads_dir') . $foto_dir . '/' . 'origin/'  . $filename;
				$exif_data = get_exifdata( $up_data['full_path'] );
				$exif_data = serialize($exif_data);
			
				// удалим оригинал
				@unlink($up_data['full_path']);
				
				if ( empty( $title) ) { 
					$title = $s_filename;
					$ftitle = $pathinfo['filename'];
				}
				if ( empty( $slug ) ) {
					if ( isset($ftitle) ) $slug = mso_slug( $ftitle );
					else $slug = mso_slug( $title );
				}
			
				if ( empty( $album_id )) $album_id = 1;
				// заносим в таблицу foto
				$data = array(
					'foto_album_id' => $album_id,
					'foto_title' => $title,
					'foto_slug' => $slug,
					'foto_descr' => $description,
					'foto_path' => $path,
					'foto_date' => $date,
					'foto_exif' => $exif_data,
				);
				// проверка на повторность
				$CI->db->select('*');
				$CI->db->from('foto');
				$CI->db->where('foto_title', $title);
				$CI->db->where('foto_slug', $slug);
				$CI->db->where('foto_path', $path);
				$query = $CI->db->get();
				if ($query->num_rows() > 0) {
					echo '<div class="error">Такая фотография уже была загружена!</div>';
				} else {
					$CI->db->insert('foto', $data );
					$id = $CI->db->insert_id();
					
					// добавление меток
					if ( !empty($tags) ) {
						$tags = mso_explode($tags, false, false); 
						// проверим наличие меток для новой страницы
						$CI->db->select('foto_tag_id');
						$def_data = array (	'foto_id' => $id );
						$CI->db->where($def_data);	
						$query = $CI->db->get('foto_tags');

						if (!$query->num_rows()) // нет меток для этой страницы
						{	// значит инсерт
							foreach ($tags as $key=>$val)
							{
								$ins_data = $def_data;
								$ins_data['foto_tag_name'] = $val;
								$CI->db->insert('foto_tags', $ins_data);
							}
						}					
					}
					
					//header('Location: ' . getinfo('site_url') . 'admin/edit-foto');
					echo '<div class="update">' . 'Новая фотография успешно добавлена!</div>';
					header('Location: ' . getinfo('site_url') . 'admin/edit-foto/' . $id);	
				}
			}	else {
				$CI->upload->display_errors('<div class="error">', '</div>');	
			}	
		}
	}
	
	echo '<h1 class="content">' . t('Загрузить фотографию', 'admin') . '</h1>';	
	$fses = mso_form_session('f_session_id');
	
	$f_header = ''; // заголовок фото
	$f_file = ''; // путь к файлу
	$f_descr = '';
	$f_slug = '';
	$f_album_id = 1;
	$f_edit = false; // т.е. add
	
	/* текущая дата в комбобоксы */
	$CI->load->helper('form');
	$date_cur_y = date('Y');
	$date_cur_m = date('m');
	$date_cur_d = date('d');	
	$tyme_cur_h = date('H');
	$tyme_cur_m = date('i');
	$tyme_cur_s = date('s');
	$date_all_y = array();
	for ($i=2005; $i<2021; $i++) $date_all_y[$i] = $i;
	
	$date_all_m = array();
	for ($i=1; $i<13; $i++) $date_all_m[$i] = $i;
	
	$date_all_d = array();
	for ($i=1; $i<32; $i++) $date_all_d[$i] = $i;
	
	$date_y = form_dropdown('f_date_y', $date_all_y, $date_cur_y);
	$date_m = form_dropdown('f_date_m', $date_all_m, $date_cur_m);
	$date_d = form_dropdown('f_date_d', $date_all_d, $date_cur_d);
	
	$time_all_h = array();
	for ($i=0; $i<24; $i++) $time_all_h[$i] = $i;
	
	$time_all_m = array();
	for ($i=0; $i<60; $i++) $time_all_m[$i] = $i;

	$time_all_s = $time_all_m;
	
	$time_h = form_dropdown('f_time_h', $time_all_h, $tyme_cur_h);
	$time_m = form_dropdown('f_time_m', $time_all_m, $tyme_cur_m);
	$time_s = form_dropdown('f_time_s', $time_all_s, $tyme_cur_s);
	/* ------------------------------- */
	
	
	require_once($MSO->config['plugins_dir'] . $plug_url . '/form.php');
	
	// переходим на редактирование фотки
	

?>