<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	$foto_dir = $options['upload_path'];
?>

<h1><?= t('Фотографии') ?></h1>
<p class="info"><?= t('Список всех фотографий') ?></p>

<?php

    // проверим $_POST
	$get = mso_url_get();
	$get2 = mso_parse_url_get( $get );
    $step = 12;  // кол-во страниц на листе
	$offset = (isset( $get2['offset'] ) & !empty( $get2['offset'] ) ) ? $get2['offset'] : 0;
	$limit = (isset( $get2['limit'] ) & !empty( $get2['limit'] ) ) ? $get2['limit'] : $step;
	//$order = (isset( $get2['order'] ) & !empty( $get2['order'] ) ) ? $get2['order'] : 'page_date_publish';
	$sort = (isset( $get2['sort'] ) & !empty( $get2['sort'] ) ) ? $get2['sort'] : 'desc';
	
	
	//*****************************************************
    //		справа хорошо бы показать альбомы
	// 		показать сортировку по параметрам
	//*****************************************************
	// отображаем список фоток построчно без разбивки по альбомам
	//TODO: надо ввести пагинацию свою - получить общее кол-во и кол-во записей на страницу
	$CI = & get_instance();
	$CI->load->helper('form');

	$cnt = $CI->db->count_all('foto');
	
	$CI->db->select("f.foto_id, f.foto_album_id, f.foto_title, f.foto_date, f.foto_slug, f.foto_path, f.foto_exif, a.foto_album_title", false);
	$CI->db->from("foto f");
	$CI->db->join("foto_albums a", "f.foto_album_id = a.foto_album_id", "left" );
	$CI->db->limit( $limit,  $offset);  
	
	$query = $CI->db->get();
	
	if ( $query->num_rows() > 0 )
	{
	
		// pagination
		$url = getinfo('site_admin_url') . 'show-foto?sort=' . $sort;
		$pag = create_pagination( $cnt, $limit, $offset, $url );
		$pgnt = ceil($cnt / $limit );
		if ( $pgnt > 1 ) echo '<div class="admin-pagination">Страницы: ' . $pag . '</div>';
		echo '<p></p>';
	
		$results = $query->result_array();
		echo '<div class="admin-foto-list">';
		foreach ( $results as $result ) {
			extract( $result );
			
			$pathinfo = pathinfo( $foto_path );
			$filepath = ( $pathinfo['dirname'] == '.' ) ? '' : $pathinfo['dirname'];
			$filepath = getinfo('uploads_url') . $foto_dir . '/' . $filepath . 'mini/';
			$filename = $pathinfo['basename'];
			$edit_url = getinfo('site_url') . 'admin/edit-foto/' . $foto_id;
			$foto = '<a href="'.$edit_url.'"><img src="' . $filepath . $filename . '" /></a>';
			$data = '<span>' . mso_date_convert('d.m.Y H:i:s', $foto_date) . '</span>';
			$checkbox = '<input type="checkbox" value="accept" name="f_foto_del[]" class="list-foto" fotoid="' . $foto_id. '"> &nbsp;';


			//require_once($MSO->config['plugins_dir'] . $plug_url . '/functions.php');
			//$ff = getinfo('uploads_dir') . $foto_dir . '/' . 'origin/'  . $filename;
			//$exif_data = get_exifdata( $ff );
			$exif_data = unserialize($foto_exif);
			//pr( $exif_data );

			$div_foto = '<div class="admin-foto" fotoid="' .$foto_id.'">';
				$div_foto .= '<div class="admin-foto-url">' . $checkbox . $foto . '</div>';
				$div_foto .= '<div class="admin-foto-meta">';
					$div_foto .= '<div class="admin-foto-title"><strong>' . $foto_title . '</strong></div>';
					$div_foto .= '<div class="admin-foto-date">' . $data . '</div>';
					$div_foto .= '<div class="admin-foto-album"><a href="'.getinfo('site_url').'admin/show-album?id='.$foto_album_id.'">' . $foto_album_title . '</a></div>';
							
					$Model = $exif_data['Model'];
					$DateTimeOriginal = $exif_data['DateTimeOriginal'];
					$ExposureTime = $exif_data['ExposureTime'];
					$FNumber = $exif_data['FNumber'];
					$ISOSpeedRatings = $exif_data['ISOSpeedRatings'];
					$FocalLength = $exif_data['FocalLength'];
					$ExposureProgram = $exif_data['ExposureProgram'];
					$ExposureMode = $exif_data['ExposureMode'];
					$MeteringMode = $exif_data['MeteringMode'];
					$SceneCaptureType = $exif_data['SceneCaptureType'];
					//$	
					$div_foto .= '<div class="admin-foto-exif">' . 
					'<div class="admin-exif"><strong>Модель: </strong><span>'.$Model.'</span></div>' .
					'<div class="admin-exif"><strong>Дата снимка: </strong><span>'.$DateTimeOriginal.'</span></div>' .
					'<div class="admin-exif"><strong>Выдержка: </strong><span>'.$ExposureTime.'</span></div>' .
					'<div class="admin-exif"><strong>Диафрагма: </strong><span>'.$FNumber.'</span></div>' .
					'<div class="admin-exif"><strong>ISO: </strong><span>'.$ISOSpeedRatings.'</span></div>' .
					'<div class="admin-exif"><strong>Фокусное: </strong><span>'.$FocalLength.'</span></div>' .
					//'<p class="admin-exif"><strong>Фокусное:</strong><span>'.$FocalLength.'</span></p>' .
					'</div>';		
				$div_foto .= '</div>';
			$div_foto .= '</div>';
			
			//require getinfo('plugins_dir') . 'admin-fotki/functions.php';
			echo $div_foto;
			
		}
		echo '</div>';
		
		if ( $pgnt > 1 ) echo '<div class="admin-pagination">Страницы: ' . $pag . '</div>';
	}
					
	//**************************************
	//      нижние кнопки
	//**************************************
	echo '<br><hr><br>';
	$buttons = '<input type="button" name="foto-del" value="Удалить" onclick="del_foto()">';
	$buttons .= '&nbsp;';
	$buttons .= '<input type="button" name="foto-check-invert" value="Инвертировать выделение" onclick="toggle_check_foto()">';
	$buttons .= '&nbsp;';
	$buttons .= '<input type="button" name="foto-check-all" value="Отметить все" onclick="check_all_foto()">';
	$buttons .= '&nbsp;';
	$buttons .= '<input type="button" name="foto-check-uncheck" value="Снять выделение" onclick="uncheck_all_foto()">';

	echo $buttons;
//************************************************************************************************

	function create_pagination( $cnt, $limit, $offset, $url) {
		$out = '';
		$pgnt = ceil($cnt / $limit );
		$step = 0;
		for ( $i = 1; $i<=$pgnt; $i++) {
		
			if (( $limit + $offset ) == ( $i * $limit ) ) $cell = '<span class="current">' . $i . '</span>'; 
			else $cell = '<a href="'.$url.'&limit=' . $limit . '&offset=' . $step . '">' . $i . '</a>';
			$out .= $cell . '&nbsp;';
			$step = $step + $limit;
		}
		return $out;
	}	
?>