<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

 // выводим формы
	echo '<form action="" method="post" enctype="multipart/form-data">'.$fses;
	echo '<div class="foto-form">';
	echo '<p><span>Название: </span><input type="text" value="' . $f_header . '" name="f_header"></p>';
	//echo '<p><span>Описание: </span> <input type="text" rows="3" value="' . $f_descr . '" name="f_descr" /></p>';
	echo '<p><span>Описание: </span> <textarea type="text" cols="50" rows="3" name="f_descr">' . $f_descr . '</textarea></p>';
    echo '<p><span>Короткая ссылка: </span> <input type="text" value="'.$f_slug.'" name="f_slug"></p>';

	
	if ( ! $f_edit ) {
		// новое фото
		echo '<p><span>Путь к файлу: </span><input type="file" value="' . $f_file . '" name="f_file"></p>';
		// $f_upload_origin - получить из опций
		$check = ( $f_upload_origin ) ? ' checked' : '';
		echo '<p><span>Загружать оригинал: </span> <input type="checkbox" value="accept" name="f_upload_origin" ' . $check .'></p>';
		// f_resize - получить из опций
		echo '<p><span>Изменять размер до: </span> <input type="text" value="'.$f_size_foto.'" name="f_size_foto"></p>';
		//	f_size_mini - получить из опций	
		echo '<p><span>Размер миниатюры: </span> <input type="text" value="'.$f_size_mini.'" name="f_size_mini"></p>';
	} else {
		// редактирование фото
		// pr( $all_tags );
		echo '<input type="hidden" name="f_foto_id" value="' . $id . '">';
	}
	
	
	// получить список альбомов
	
	global $plug_url;

	require_once(getinfo('plugins_dir') . $plug_url . '/functions.php');
	$all_albums = get_all_albums();
	$childs = get_child_album( $all_albums, 0 );
	$level = 0;
	$tree = build_tree_f( $all_albums, 0, $level );

	$CI->load->helper('form');
	$albums_select = form_dropdown('f_album_id', $tree, $f_album_id);
	echo '<p><span>Альбом: </span> ' . $albums_select . '</p>';
	
	if ( ! $f_edit ) {
		echo '<p><span>Метки: </span> <input type="text" name="f_tags"></p>';
	
	} else {
		// выедем метки с крестиками для удаления
		$metki = '<p><div class="foto-meta"><strong>Метки: </strong> (<a href="#" id="admin-new-tag" onclick="add_new_tag()">новая метка</a>)';
		$metki .= '<div id="add-new-meta" style="display: none"><input type="textfield" name="add-new-meta-value"><input type="button" value="Добавить" onclick="add_new_meta('.$id.')"></div>';
		if ( count( $all_foto_tags) > 0 ) { 
			
			foreach ( $all_foto_tags as $foto_tags ) {
				$tag_id = $foto_tags['foto_tag_id'];
				$metki .= '<span id="admin-foto-tag" tagid="' .  $tag_id . '" >' . $foto_tags['foto_tag_name'] . '<a href="" onclick="delete_tags('. $tag_id.'); return false;">&nbsp;</a>' . '</span>';
			}

		}
		$metki .= '</div></p>';
		echo $metki;

		
	}

	
	//echo '<span>Дата: </span> <input type="text" name="f_date"><br>';
	echo '<p><span>Дата: </span>' . $date_d . ' - ' . $date_m . ' - ' . $date_y . '&nbsp;&nbsp;&nbsp;' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '</p>';
				
	
	//echo '<span>Статус: </span> <input type="select" name="f_status"><br>';
	
	if ( ! $f_edit ) {
		// загрузить
		echo '<input type="submit" name="f_submit_upload_foto" value=" Загрузить ">';
	} else {
		// сохранить
		echo '<input type="submit" name="f_submit_save_foto" value=" Сохранить ">';
	}
	
	echo '</div>';
	echo '</form>';
	
	
?>