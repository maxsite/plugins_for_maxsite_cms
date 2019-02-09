<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	//********************************************************************
	//********** страница отображения альбома с фотографиями *************
	//********************************************************************
	//$options_key = 'admin_fotki';
	//$options = mso_get_option($options_key, 'plugins', array());
	//if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	//$foto_dir = $options['upload_path'];
	
	global $foto_albums;
	global $foto_gallery;
	global $foto_url;
?>


<?php


	if  (!isset( $foto_album_id ) /*or empty( $foto_album_id )*/ ) $foto_album_id = 1;
	
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );

	// $pagination_url
	// $pagination_page
	
	if ( mso_segment(1) == $foto_albums ) {
		$pagintaion_url = mso_segment(3);
		$pagination_page = mso_segment(4);
	}
	$count = ( isset($options['foto_count']) && (!empty($options['foto_count'])) ) ? $options['foto_count'] : 12;
	if ( isset( $pagination_page) && ($pagination_page > 1) ) $start = $pagination_page * $count - $count; else $start = 0 ;

	
	//pr( '$pagination_page = ' . $pagination_page . '   ' . '$start = ' . $start . '   ' . '$count = ' . $count);
	$fotos = get_fotos( $count, $foto_album_id, 'desc', $start );
	
	
    
	// вычислить кол-во страниц
	$CI = get_instance();
	if ( $foto_album_id !== false ) $CI->db->where('foto_album_id', $foto_album_id);
	$CI->db->from('foto');
	$cnt = $CI->db->count_all_results();
	$pgnt = ceil($cnt / $count );	
	$pagination['maxcount'] = $pgnt;
	$pagination['limit'] = $count;

	if ( !is_array($fotos) ) return;
	
	if ($f = mso_page_foreach('foto-gallery')) require($f);
	else {
		$f = getinfo('plugins_dir') . $plug_url . '/foto-gallery.php';
		require_once( $f );
	}
	
	// echo '<div class="foto-list">';
	// if ( $foto_album_id == false ) echo '<h3>Все фотографии</h3>';
	// else echo '<h3>Фотографии альбома</h3>';
	// foreach ( $fotos as $foto ) {
		// extract( $foto );
		// $pathinfo = pathinfo( $foto_path );
		// $filepath = ( $pathinfo['dirname'] == '.' ) ? '' : $pathinfo['dirname'];
		// $filepath = getinfo('uploads_url') . $foto_dir . '/' . $filepath . 'mini/';
		// $filename = $pathinfo['basename'];
		// $view_url = getinfo('site_url') . $foto_url . '/' . $foto_slug;
		// $view_url = '<a href="'.$view_url.'" title="'.$foto_title.'"><img src="' . $filepath . $filename . '" alt="'.$foto_title.'"  /></a>';
		// $div_foto = '<div class="foto" fotoid="' .$foto_id.'">';
		// $div_foto .= '<div class="foto-url">' . $view_url . '</div>';
		// $div_foto .= '<span class="foto-view-count">' . $foto_view_count . '</span>';
		
		// $foto_comments_count = get_foto_comments_count( $foto_id );
		// $div_foto .= '<span class="foto-comments-count">' . $foto_comments_count . '</span>';
		// $div_foto .= '</div>';
		
		// echo $div_foto;
	// }
	// echo '</div>';
	// echo '<div class="break"></div>';
	
	mso_hook('pagination', $pagination);
?>