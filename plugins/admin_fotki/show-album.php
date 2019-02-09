<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$sep = '&nbsp;&rarr;&nbsp;';
	
	$CI = & get_instance();
	
	// скрытое окно для редактирования альбома
	$hide_window = '<p><span><strong>Название: </strong></span> <input type="text" id="edit_album_title" name="f_edit_album_title"></p>';
	$hide_window .= '<p><span><strong>Ссылка: </strong></span> <input type="text" id="edit_album_slug" name="f_edit_album_slug"></p>';
	$hide_window .= '<p><input type="hidden" id="edit_album_id" name="f_edit_album_id" value=""></p>';
		
	echo '<div id="album_edit" class="visible">';	
	echo '<form action=""  method="post">'.mso_form_session('f_session_id');
	echo $hide_window;
	echo '<input type="submit" name="f_edit_album" value=" Изменить " />';
	echo '</form>';
	echo '</div>';
		
	
	
	//f_new_album
	if ( $post = mso_check_post(array('f_session_id','f_new_album', 'f_new_album_parent_id', 'f_new_album_title')) )
	{
		mso_checkreferer();

		if ( !empty( $post['f_new_album_title'] ) ) {
			$album_title = $post['f_new_album_title'];
			$album_parent_id = $post['f_new_album_parent_id'];
			$album_slug = trim($post['f_new_album_slug']);
			if ( empty($album_slug) ) $album_slug = mso_slug( $album_title );
			$CI->db->select('*');
			$CI->db->where('foto_album_parent_id', $album_parent_id);
			$CI->db->where('foto_album_slug', $album_slug);
			$CI->db->where('foto_album_title', $album_title);
			$query = $CI->db->get('foto_albums');
			if ($query->num_rows() > 0)
			{
				echo '<div class="error">Альбом уже существует!</div>';
			} else {
				$data = array( 'foto_album_title' => $album_title,
							   'foto_album_slug' => $album_slug,
							   'foto_album_parent_id' => $album_parent_id,
							   'foto_album_date' => date('Y-m-d H:i:s') );
				$CI->db->insert( 'foto_albums', $data ); 			   
			}
		}
	}

	

	global $plug_url; 
	// из get получаем id альбома
	// если id нет, то это корневые альбомы
	$cur_url = getinfo('site_url') . 'admin/' . 'show-album';
	
	require(getinfo('plugins_dir') . $plug_url . '/functions.php');
	
	// всегда есть альбом "Неразобранное" с id = 1
	// его удалить нельзя, изменить нельзя, добавить в него новые альбомы нельзя
	$get = mso_url_get();
	$get = mso_parse_url_get( $get );

	$albumid = ( isset($get['id']) && ($get['id'] > 1 )) ? $get['id'] : 0;
	
	

	
	if ( $albumid > 1 ) {
		// хлебные крошки по альбому
		
		$curalbum = get_current_album( $albumid );
		
		$tmpid = $albumid;
		$str = '<a href="' . $cur_url . '">Альбомы</a>';
		
		// ищем рекурсивно родителей
		$res = true;
		$str3 = '';
		while ( $res ) {
			$res = get_album_parent( $tmpid );
			if ( $res ) { 
				$tmpid = $res['foto_album_parent_id']; 
				$str3 = $sep . '<a href="' . $cur_url . '?id=' . $res['foto_album_id'] . '">' . $res['foto_album_title'] . '</a>' . $str3;
			}
		}

		$str .= $str3;
		echo $str;
		if ( $curalbum['foto_album_parent_id'] <> 1 ) 
			echo '<br><a href="' . $cur_url . '?id=' . $curalbum['foto_album_parent_id'] . '">Назад</a>';
	}
	
	echo '<br><br><hr><br>';
		echo '<a href="#" onclick="show_hide( \'#add-album\', \'visible\' )">Создать альбом</a>';
		//echo '<a href="">Переместить в другой альбом</a>';
		
		// форма создания альбома на jquery, по дефолту скрыта, по клику раскрывается
		$form = '<p><span><strong>Название: </strong></span> <input type="text" name="f_new_album_title"></p>';
		$form .= '<p><span><strong>Ссылка: </strong></span> <input type="text" name="f_new_album_slug" value=""></p>';
		$form .= '<p><input type="hidden" name="f_new_album_parent_id" value="'.$albumid.'"></p>';
		echo '<form class="visible" id="add-album"  action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_new_album" value=" Создать альбом " />';
		echo '</form>';
		
	echo '<br><br><hr><br>';
	
	$CI = & get_instance();
	
	$CI->db->select('*');
	$CI->db->from('foto_albums');
	$CI->db->where('foto_album_parent_id', $albumid);
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
	{
		// есть альбомы, выведем
		$form = '';
		 

		foreach ($query->result() as $row)
		{
			$title = $row->foto_album_title;
			$slug = $row->foto_album_slug;
			$id = $row->foto_album_id;
			$form .= '<div class="album" id="album_' . $row->foto_album_id . '">';
			$url = $cur_url . '?id=' . $row->foto_album_id;
			$form .= '<a href="' . $url . '">' . $row->foto_album_title . '</a>';
			if ( $id > 1 ) {
				$form .= '<div class="album-control">';
				$form .= '<a class="change" type="button" onclick="edit_album(' . $id . ', 0, \''.$title.'\', \''.$slug. '\')">Изменить</a>';
				$form .= '<a class="delete" type="button" onclick="edit_album(' . $id . ', 1, 0, 0)">Удалить</a>';
				$form .= '</div>';
			}
			$form .= '</div>';
		}
		echo $form;
	}
?>