<?php 
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	global $_COOKIE, $_SESSION;


	function album_delete( $id ) {
		$CI = & get_instance();
		$res = 0;
		// проверим данные, если есть фотки, перенесем их в неразобранные
		$CI->db->select( '*' );
		$CI->db->from( 'foto_albums' );
		$CI->db->where( 'foto_album_parent_id', $id );
		$query = $CI->db->get();
		if ( $query->num_rows() > 0 ) {
			// есть подальбомы
			$res = 1;
		}

		$CI->db->select( '*' );
		$CI->db->from( 'foto' );
		$CI->db->where( 'foto_album_id', $id );
		$query = $CI->db->get();
		if ( $query->num_rows() > 0 ) {
			// есть фотки
			$res = $res + 2;
		}
		
		switch ( $res ) {
			case 0: 
					// удалим	
					$CI->db->where( 'foto_album_id', $id );
					$CI->db->delete( 'foto_albums' );
					return true;
			case 1: 
					return 'Альбом не может быть удален, т.к. содержит другие альбомы. Сначала удалите их.';
			case 2: 
					return 'Альбом не может быть удален, т.к. содержит фотографии. Сначала удалите фотографии, а потом сам альбом.';
			case 3:
					return 'Альбом не пуст. Удаление невозможно.';
		}
	}	

	function album_edit( $id ) {

	}

	function foto_delete( $id ) {
	
		$options_key = 'admin_fotki';
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
		$foto_dir = $options['upload_path'];
	
		$CI = & get_instance();
		// удаляем фотку и данные от нее (метки, привязка к альбому)
		// получить путь к фоткам
		$CI->db->select('foto_path');
		$CI->db->from('foto');
		$CI->db->where('foto_id', $id);
		$query = $CI->db->get();
		// удалить фотки физически
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			$foto_path = $row->foto_path;
			
			unlink( getinfo('uploads_dir') . $foto_dir . '/origin/' . $foto_path );
			unlink( getinfo('uploads_dir') . $foto_dir . '/mini/' . $foto_path );
			unlink( getinfo('uploads_dir') . $foto_dir . '/current/' . $foto_path );
			// origin
			// current
			// mini
			
			// удалить комменты
			$CI->db->where('foto_comments_foto_id', $id );
			$CI->db->delete('foto_comments');
		}
		
		$CI->db->where( 'foto_id', $id );
		$CI->db->delete( 'foto_tags' );		
		
		$CI->db->where( 'foto_id', $id );
		$CI->db->delete( 'foto' );				
	}
	
	mso_checkreferer(); // защищаем реферер
		
	if ( $post = mso_check_post(array('album_id', 'action')) )
	{
		$albumid = $post['album_id'];		
		$action = $post['action'];	

		$res = 'false';
		switch ( $action ) {
			case "delete": 
							$res = album_delete( $albumid );
							break;
							
			case "edit":
							$res = album_edit( $albumid );
							break;
		
		}
		echo $res;
	}
	
	
	if ( $post = mso_check_post(array('fotoid', 'action')) )
	{
		$fotoid = $post['fotoid'];
		//$fotos_id = explode(',', $fotos_id );
		$action = $post['action'];	
		$res = 'true';
	
		$out = '';
		//foreach ( $fotos_id as $fotoid ) {
			switch ( $action ) {
				case "delete": 
							foto_delete( $fotoid );
							echo $fotoid;	
							break;
				case "add-description" :
							$descr = $post['value'];
							foto_add_description( $fotoid, $descr );
							echo $fotoid;	
							break;
				case "rating-change" :
							$value = $post['value'];
							//echo  'Хуй!!!';
							foto_change_rating( $fotoid, $value );
							break;
			}
		//}
		
	}	
	
	
	if ( $post = mso_check_post(array('tagid')) ) {
		$CI = & get_instance();
		$tag_id = $post['tagid'];
		$CI->db->where('foto_tag_id', $tag_id );
		$CI->db->delete('foto_tags');
	}

	if ( $post = mso_check_post(array('fotoid', 'metavalue')) ) {
		$CI = & get_instance();
		$foto_id = $post['fotoid'];
		$meta_value = $post['metavalue'];
		
		// добавление меток
		if ( !empty($meta_value) ) {
			$tags = mso_explode($meta_value, false, false); 
			
			$new_meta = '';
			foreach ($tags as $key=>$val)
			{

				// проверим наличие метки для новой страницы
				$CI->db->select('foto_tag_id');
				$def_data = array (	'foto_id' => $foto_id, 'foto_tag_name' => $val );
				$CI->db->where($def_data);	
				$query = $CI->db->get('foto_tags');

				if ( $query->num_rows() > 0 ) // 
				{
					// метки есть, удалим их
					$tag = $query->row();
					$CI->db->where('foto_tag_id', $tag->foto_tag_id );
					$CI->db->delete('foto_tags');
				}	
			
				$ins_data = $def_data;
				$ins_data['foto_tag_name'] = $val;
				$CI->db->insert('foto_tags', $ins_data);
				$tag_id = $CI->db->insert_id();
			}
			// зачитаем все метки
			
			$CI->db->select('foto_tag_name, foto_tag_id');
			$CI->db->where('foto_id', $foto_id);
			$CI->db->from('foto_tags');
			$query = $CI->db->get();
			if ( $query->num_rows() > 0 )
			{
				$results = $query->result_array();
				foreach ( $results as $tag )
				{
					$new_meta .= '<span id="admin-foto-tag" tagid="' .  $tag['foto_tag_id'] . '" >' . 
							 $tag['foto_tag_name'] . 
							 '<a href="#" onclick="delete_tags('. $tag['foto_tag_id'].', \'foto-meta\'); return false;"></a>' . '</span>';
				}
			}
			// возвращаем новую часть блока с метками
			echo $new_meta;
				
		}
	}
	
	function foto_add_description( $fotoid, $descr ) {
		$CI = get_instance();
		$data = array( 'foto_descr' => $descr );
		$CI->db->update( 'foto', $data, array( 'foto_id' => $fotoid ) );
	}

	function foto_change_rating( $fotoid, $value ) {
	    
		$rr = set_allready_vote( $fotoid );
		
		if ( $rr == false  ) { echo "allready_vote"; return; }
		// ограничим голосование по кукисам
		// если уже голосовали, то отобразим такую надпись вместо кнопок 
		// на странице вывода надо тоже предусмотреть это, чтобы кнопки не отображать
		
		$CI = get_instance();
		$CI->db->select('foto_rate_minus, foto_rate_plus, foto_rate_count');
		$CI->db->where('foto_id', $fotoid );
		$query = $CI->db->get('foto');
		if ( $query->num_rows() > 0 )
		{
			$result = $query->row();
			$rate_value_minus = $result->foto_rate_minus;
			$rate_value_plus = $result->foto_rate_plus;
			$rate_count = $result->foto_rate_count;
			if ( $value == 'good' )      $rate_value_plus++;
			else if ( $value == 'bad' )  $rate_value_minus++;
			$rate_count++;
			$data = array( 'foto_rate_plus' => $rate_value_plus,
							'foto_rate_minus' => $rate_value_minus,
							'foto_rate_count' => $rate_count );
			$CI->db->where('foto_id', $fotoid );
			$CI->db->update( 'foto', $data );
			$res = array( 'rate_value_minus' => $rate_value_minus, 'rate_value_plus' => $rate_value_plus, 'rate_count' => $rate_count );
			$res = json_encode( $res );
			echo $res;
		}
		
		//$res = array( 'rate_value_minus' => $rate_value_minus, 'rate_value_plus' => $rate_value_plus, 'rate_count' => $rate_count );
		//$res = json_encode( $res );
		//$res = "Хуй!!!";
		//echo $res;
		//exit();
	}
	
	function set_allready_vote ( $fotoid ) {
				// определим, голосовал ли уже?
				$name_cookies = 'maxsite_fotki_rate';
				$expire = 60 * 60 * 24 * 30 * 12; // 1 год  -----------30 дней = 2592000 секунд
					  
				// через сессии
				session_start();
				if (isset($_SESSION[$name_cookies]))	$all_pages = $_SESSION[$name_cookies]; // значения текущего кука
				else $all_pages = ''; // нет такой куки вообще

				
				
				$all_pages = explode(' ', $all_pages); // разделим в массив
				if ( in_array($fotoid, $all_pages) )
				{
					// вы уже голосовали
					//echo 'allready_vote';
					return false;
				} else {
					$all_pages[] = $fotoid;
					$all_pages = array_unique($all_pages); // удалим дубли на всякий пожарный
					$all_pages = implode(' ', $all_pages); // соединяем обратно в строку
					$expire = time() + $expire;
					//@setcookie($name_cookies, $all_pages, $expire); // записали в куку
					// записали в сессию
					$_SESSION[$name_cookies]=$all_pages; 	
					return true;
				}
	}
	
?>