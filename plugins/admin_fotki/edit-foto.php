<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	global $plug_url; 
	$CI = & get_instance();
	$editor_options = mso_get_option('editor_options', 'admin', array());

	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	
	$id = mso_segment(3);

	if ( $post = mso_check_post(array('f_session_id','f_submit_save_foto')) )
	{
		mso_checkreferer();
		// сохраним изменения
		$foto_id = $post['f_foto_id'];
		$title = $post['f_header'];
		$description = $post['f_descr'];
		$slug = $post['f_slug'];
		$album_id = $post['f_album_id'];
		$date_d = $post['f_date_d'];
		$date_m = $post['f_date_m'];
		$date_y = $post['f_date_y'];
		$time_h = $post['f_time_h'];
		$time_m = $post['f_time_m'];
		$time_s = $post['f_time_s'];

		$date = date('Y-m-d H:i:s', mktime($time_h, $time_m, $time_s, $date_m, $date_d, $date_y) );		
				
		if ( empty( $title) ) { 
			// ошибка
			echo '<div class="error">' . 'Укажите название фотографии!<br>' . '</div>';
		} else {	
			if ( empty( $slug ) ) {
				// ошибка
				$slug = mso_slug( $title );
			}
					
			$data = array(
				'foto_album_id' => $album_id,
				'foto_title' => $title,
				'foto_slug' => $slug,
				'foto_descr' => $description,
				'foto_date' => $date,
			);				
			$CI->db->where('foto_id', $foto_id);
			$CI->db->update('foto', $data );
			echo '<div class="update">' . 'Данные обновлены!<br>' . '</div>';
		}
	}
		
	echo '<h1 class="content">' . t('Редактирование фотографии', 'admin') . '</h1>';	
	$fses = mso_form_session('f_session_id');

	// получить данные фото из базы
	$CI->db->select('foto_album_id, foto_title, foto_slug, foto_descr, foto_exif, foto_path, foto_date');
	$CI->db->from('foto');
	$CI->db->where('foto_id', $id );
	$query = $CI->db->get();
	if ( $query->num_rows > 0 ) {
		$foto = $query->row();
		$f_header = $foto->foto_title; // заголовок фото
		$f_file = $foto->foto_path; // путь к файлу
		$f_descr = $foto->foto_descr;
		$f_slug = $foto->foto_slug;
		$f_album_id = $foto->foto_album_id;
		$f_edit = true; // т.е. add
	
		// разложить дату
		$part = explode(' ' , $foto->foto_date);
		if (isset($part[0])) $ymd = explode ('-', $part[0]);
			else $ymd = array (0,0,0);

		if (isset($part[1])) $hms = explode (':', $part[1]);
			else $hms = array (0,0,0);

		$date_cur_y = $ymd[0];
		$date_cur_m = $ymd[1];
		$date_cur_d = $ymd[2];
		$time_cur_h = $hms[0];
		$time_cur_m = $hms[1];
		$time_cur_s = $hms[2];
	
		$CI->load->helper('form');
		
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
		
		$time_h = form_dropdown('f_time_h', $time_all_h, $time_cur_h);
		$time_m = form_dropdown('f_time_m', $time_all_m, $time_cur_m);
		$time_s = form_dropdown('f_time_s', $time_all_s, $time_cur_s);		
		
		// взять метки
		$CI->db->select('foto_tag_id, foto_tag_name');
		$CI->db->from('foto_tags');
		$CI->db->where('foto_id', $id);
		$query = $CI->db->get();
		
		$all_foto_tags = array();
		if ( $query->num_rows() > 0 ) {
			$all_foto_tags = $query->result_array();
		}
		
		require($MSO->config['plugins_dir'] . $plug_url . '/form.php');
		
		// покажим фотку
		echo '<div class="admin-foto-current">';
			echo '<img src="'.getinfo('uploads_url'). $options['upload_path'] . '/current/' . $f_file . '" />';
		echo '</div>';
	
	} else {
		echo '<div class="error">' . t('Ошибка загрузки данных. Указанная фотография не существует.', 'admin') . '</div>';
	}
?>