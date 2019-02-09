<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	//********************************************************************
	//************* страница отображения фотографии и метаданных *********
	//********************************************************************
	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	$foto_dir = $options['upload_path'];
	
	global $foto_albums;
	
	//mso_page_view_count_first(); // для подсчета количества прочтений страницы
	
	// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
	//mso_head_meta('title', $pages, '%page_title%|%title%', ' » ', true ); // meta title страницы
	//mso_head_meta('description', $pages); // meta description страницы
	//mso_head_meta('keywords', $pages); // meta keywords страницы
?>


<?php
	require_once( getinfo('plugins_dir') . $plug_url . '/functions.php' );

	
	$segment = mso_segment(2);
	foto_view_count_first();
	$CI = & get_instance();
	//$CI->db->select('foto_id, foto_album_id, foto_title, foto_slug, foto_descr, foto_exif, foto_path, foto_date, foto_view_count, foto_rate_plus, foto_rate_minus, foto_rate_count, if( (foto_rate_plus - foto_rate_minus) < 0, (foto_rate_plus - foto_rate_plus), (foto_rate_plus - foto_rate_minus) ) as foto_rate');
	$CI->db->select('foto_id, foto_album_id, foto_title, foto_slug, foto_descr, foto_exif, foto_path, foto_date, foto_view_count, foto_rate_plus, foto_rate_minus, foto_rate_count');
	
	$CI->db->from('foto');
	$CI->db->where('foto_slug', $segment );	
	$query = $CI->db->get();
	if ( $query->num_rows > 0 ) {
		
		
		
		# начальная часть шаблона
	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_foto">' . NR;
		$foto = (array)$query->row();
		//pr( $foto );
		
		
		
		if ($f = mso_page_foreach('foto-page')) require($f);
		else {
			$f = getinfo('plugins_dir') . $plug_url . '/foto-page.php';
			require_once( $f );
		}
		
		echo NR . '</div><!-- class="type type_foto" -->' . NR;
	# конечная часть шаблона
	require(getinfo('template_dir') . 'main-end.php');	
	
	} else {
		header('HTTP/1.0 404 Not Found');
		
				# начальная часть шаблона
	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_foto">' . NR;
	
		if ($f = mso_page_foreach('pages-not-found')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else // стандартный вывод
		{

			echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
			echo '<p>' . t('Извините, ничего не найдено') . '</p>';
			echo mso_hook('page_404');
		}
			# конечная часть шаблона
	require(getinfo('template_dir') . 'main-end.php');	
	}
	

	

?>