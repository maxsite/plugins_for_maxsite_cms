<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	//********************************************************************
	//************* страница отображения альбомов ************************
	//********************************************************************
	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	$foto_dir = $options['upload_path'];
	
	global $foto_albums;
	
?>


<?php

	# начальная часть шаблона
	require(getinfo('template_dir') . 'main-start.php');
	
    $out = '';
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );
	$CI = get_instance();
	$segment = mso_segment(2);
	if ( empty($segment) ) {
		// отобразим только корневые альбомы
		echo '<h2>Все альбомы</h2>';
		$albums = get_albums( false, false, 'desc' );
		$out = generate_albums_out( $albums );
		echo $out;	
	} else {
		// отображаем только дочерние альбомы указанного в slug альбома
		$CI->db->select('foto_album_id, foto_album_title');
		$CI->db->from('foto_albums');
		$CI->db->where('foto_album_slug', $segment);
		$CI->db->limit( 1 );
		$query = $CI->db->get();
		if ( $query->num_rows() > 0 ) {
			$result = $query->row();
			$foto_album_id = $result->foto_album_id;
			$albums = get_albums( false, $foto_album_id, 'desc' );
			
			$out .= '<h2>Альбом "'.$result->foto_album_title.'"</h2>';
			$out .= generate_albums_out( $albums );
			echo $out;
			
			// теперь выведем фотки альбома
			
			
			require_once( getinfo('plugins_dir') . $plug_url . '/gallery.php' );
			
		} else {
			// ниче нет
		}
		
		
		
		
	}
	
	
	
	# конечная часть шаблона
	require(getinfo('template_dir') . 'main-end.php');	
?>