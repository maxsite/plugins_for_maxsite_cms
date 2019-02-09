<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	echo '<div class="foto-list">';
	if ( $foto_album_id == false ) echo '<h3>Все фотографии</h3>';
	else echo '<h3>Фотографии альбома</h3>';
	foreach ( $fotos as $foto ) {
		extract( $foto );
		$pathinfo = pathinfo( $foto_path );
		$filepath = ( $pathinfo['dirname'] == '.' ) ? '' : $pathinfo['dirname'];
		$filepath = getinfo('uploads_url') . $foto_dir . '/' . $filepath . 'mini/';
		$filename = $pathinfo['basename'];
		$view_url = getinfo('site_url') . $foto_url . '/' . $foto_slug;
		$view_url = '<a href="'.$view_url.'" title="'.$foto_title.'"><img src="' . $filepath . $filename . '" alt="'.$foto_title.'"  /></a>';
		$div_foto = '<div class="foto" fotoid="' .$foto_id.'">';
		$div_foto .= '<div class="foto-url">' . $view_url . '</div>';
		$div_foto .= '<span class="foto-view-count">' . $foto_view_count . '</span>';
		
		$foto_comments_count = get_foto_comments_count( $foto_id );
		$div_foto .= '<span class="foto-comments-count">' . $foto_comments_count . '</span>';
		$div_foto .= '</div>';
		
		echo $div_foto;
	}
	echo '</div>';
	echo '<div class="break"></div>';
?>