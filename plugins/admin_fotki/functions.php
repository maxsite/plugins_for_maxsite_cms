<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 



	function get_all_albums () {
		$CI = get_instance();
		$CI->db->select('*');
		$CI->db->from('foto_albums');
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
		{
			$res = $query->result_array();
		} 
		return $res;		
	}
	
 	function get_current_album( $id ) {
		$res = false;
		$CI = get_instance();
		$CI->db->select('*');
		$CI->db->from('foto_albums');
		$CI->db->where('foto_album_id', $id);
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
		{
			$res = $query->row_array();
		} 
		return $res;		
	} 
	
 	function get_album_parent( $id ) {
		$res = false;
		$CI = get_instance();
		$CI->db->select('*');
		$CI->db->from('foto_albums');
		$CI->db->where('foto_album_id', $id);
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
		{
			$res = $query->row_array();
		} 
		return $res;
	} 
	
 	function get_child_album( $all, $parent_id ) {
		$res = array();
		foreach ( $all as $album ) {
			if ( $album['foto_album_parent_id'] == $parent_id )
				$res[] = $album;
		}
		return $res;
	}
	
 	function build_tree_f( $all, $parent_id, $level ) {
		$res = array();
		foreach ( $all as $elem ) {
			if ( $elem['foto_album_parent_id'] == $parent_id ) {
				$id = $elem['foto_album_id'];
				
				$title = str_repeat( '&nbsp;', $level*2) . $elem['foto_album_title'];
				$res[ '0' . $id ] = $title;
				$level2 = $level + 1;
				$res2 = build_tree_f( $all, $elem['foto_album_id'], $level2 );
				$res = array_merge($res, (array)$res2);
			}
		}
		return $res;
	} 

	
 	function get_exifdata( $filename ) {
		global $plug_url;
		$Toolkit_Dir = getinfo('plugins_dir') . $plug_url . '/exif/';     // Ensure dir name includes trailing slash
		// Hide any unknown EXIF tags
		$GLOBALS['HIDE_UNKNOWN_TAGS'] = TRUE;

		include_once ( $Toolkit_Dir . 'Toolkit_Version.php' );          // Change: added as of version 1.11
		include_once ( $Toolkit_Dir . 'JPEG.php' );                     // Change: Allow this example file to be easily relocatable - as of version 1.11
		include_once ( $Toolkit_Dir . 'JFIF.php' );
		include_once ( $Toolkit_Dir . 'PictureInfo.php' );
		include_once ( $Toolkit_Dir . 'XMP.php' );
		include_once ( $Toolkit_Dir . 'Photoshop_IRB.php' );
		include_once ( $Toolkit_Dir . 'EXIF.php' );
		
		$exif = get_EXIF_JPEG( $filename );
		$exif_data = array(
		    'Model' => '',
		    'DateTimeOriginal' => '',
		    'ExposureTime' => '',
		    'FNumber' => '',
		    'ExposureProgram' => '',
		    'ISOSpeedRatings' => '',
		    'FocalLength' => '',
		    'ExposureMode' => '',
		    'MeteringMode' => '',
		    'SceneCaptureType' => '',
		);
		
 		if ( isset($exif[0][272]['Text Value'])) $exif_data['Model'] = $exif[0][272]['Text Value']; 
		if ( isset($exif[0][34665]['Data'][0]) ) {
			$exif = $exif[0][34665]['Data'][0];
			if (isset($exif[36867]['Text Value'])) $exif_data['DateTimeOriginal'] = $exif[36867]['Text Value'];
			if (isset($exif[33434]['Text Value'])) 
			{
				$numerator = $exif[33434]['Data'][0]['Numerator'];
				$denominator = $exif[33434]['Data'][0]['Denominator'];
				if ( $denominator == 1 ) 
					$exif_data['ExposureTime'] = $numerator . '.0';
				else
					$exif_data['ExposureTime'] = $numerator . '/' . $denominator;
				//$exif_data['ExposureTime'] = $exif[33434]['Text Value'];
			}	
			
			//pr( $exif[33437] );
			if (isset($exif[33437]['Text Value'])) {
				$numerator = $exif[33437]['Data'][0]['Numerator'];
				$denominator = $exif[33437]['Data'][0]['Denominator'];
				$value = $numerator / $denominator;
				$exif_data['FNumber'] = 'f/' . $value;
				
				//$exif_data['FNumber'] = $exif[33437]['Text Value'];
			}	
			
			if (isset($exif[34850])) {
				$val = $exif[34850]['Data'][0];
				switch ( $val ) {
					case 0: $exif_data['ExposureProgram'] = 'Не определен'; break;
					case 1: $exif_data['ExposureProgram'] = 'Ручной'; break;
					case 2: $exif_data['ExposureProgram'] = 'Автоматический'; break;
					case 3: $exif_data['ExposureProgram'] = 'Приоритет диафрагмы'; break;
					case 4: $exif_data['ExposureProgram'] = 'Приоритет выдержки'; break;
					case 5: $exif_data['ExposureProgram'] = 'Творческий'; break;
					case 6: $exif_data['ExposureProgram'] = 'Спорт '; break;
					case 7: $exif_data['ExposureProgram'] = 'Портрет'; break;
					case 8: $exif_data['ExposureProgram'] = 'Пейзаж'; break;
				}
			}
			if (isset($exif[34855]['Text Value'])) $exif_data['ISOSpeedRatings'] = $exif[34855]['Text Value'];
			//if (isset($exif[37377]['Text Value'])) $exif_data['ShutterSpeedValue'] = $exif[37377]['Text Value'];
			//if (isset($exif[37378]['Text Value'])) $exif_data['ApertureValue'] = $exif[37378]['Text Value'];
			//if (isset($exif[37385]['Text Value'])) $exif_data['Flash'] = $exif[37385]['Text Value'];
			if (isset($exif[37386]['Data'][0]['Numerator'])) $exif_data['FocalLength'] = $exif[37386]['Data'][0]['Numerator'];
			if (isset($exif[41986]['Text Value'])) $exif_data['ExposureMode'] = $exif[41986]['Text Value'];
			if (isset($exif[37383]['Data'][0])) {
				$val = $exif[37383]['Data'][0];
				switch( $val ) {
					case 0: $exif_data['MeteringMode'] = 'неопределен'; break;
					case 1: $exif_data['MeteringMode'] = 'Усредненный'; break;
					case 2: $exif_data['MeteringMode'] = 'Центрально-взвешенный'; break;
					case 3: $exif_data['MeteringMode'] = 'Spot'; break;
					case 4: $exif_data['MeteringMode'] = 'MultiSpot'; break;
					case 5: $exif_data['MeteringMode'] = 'Матричный'; break;
					case 6: $exif_data['MeteringMode'] = 'Частичный'; break;
					case 255: $exif_data['MeteringMode'] = 'другой'; break;
					default:
						$exif_data['MeteringMode'] = 'reserved';
				}
			}
			if (isset($exif[41990]['Data'][0])) {
				$val = $exif[41990]['Data'][0];
				switch( $val ) {
					case 0: $exif_data['SceneCaptureType'] = 'Стандартный'; break;
					case 1: $exif_data['SceneCaptureType'] = 'Пейзаж'; break;
					case 2: $exif_data['SceneCaptureType'] = 'Портрет'; break;
					case 3: $exif_data['SceneCaptureType'] = 'Ночная съемка'; break;
					default:
						$exif_data['SceneCaptureType'] = 'reserved';
				}
			}				
		}
		unset( $exif );	 
		return $exif_data;
	} 
	
 	function get_fotos( $count, $albumid, $sort, $start = 0, $exclude_foto = false, $from_album = false ) {
		
		$CI = get_instance();
		//$CI->db->select("f.foto_id, f.foto_album_id, f.foto_descr, f.foto_view_count, f.foto_title, f.foto_date, f.foto_slug, f.foto_path, f.foto_exif, a.foto_album_title, f.foto_rate_minus, f.foto_rate_plus, f.foto_rate_count, if( (f.foto_rate_plus - f.foto_rate_minus) < 0, (f.foto_rate_plus - f.foto_rate_plus), (f.foto_rate_plus - f.foto_rate_minus) ) as foto_rate", false);
		$CI->db->select("f.foto_id, f.foto_album_id, f.foto_descr, f.foto_view_count, f.foto_title, f.foto_date, f.foto_slug, f.foto_path, f.foto_exif, a.foto_album_title, f.foto_rate_minus, f.foto_rate_plus, f.foto_rate_count", false);
		$CI->db->from("foto f");
		$CI->db->join("foto_albums a", "f.foto_album_id = a.foto_album_id", "left" );
		$CI->db->order_by( 'foto_id', $sort );
		if ( $exclude_foto ) $CI->db->where_not_in( 'foto_id', $exclude_foto );
		if ( $from_album ) $CI->db->where_in('f.foto_album_id', $from_album);
		if ( $start == 0 ) $CI->db->limit( $count); 
		else $CI->db->limit( $count, $start );

		if ( $albumid !== false ) { $CI->db->where( 'f.foto_album_id', $albumid); }
		//pr( _sql() );
		$query = $CI->db->get();
	
		if ( $query->num_rows() > 0 )
		{
			$fotos = $query->result_array();
			return $fotos;
		} else return false;
	} 

 	function get_albums( $count, $albumid, $sort ) {
		
		$CI = get_instance();
		$CI->db->select("foto_album_id, foto_album_title, foto_album_slug, foto_album_parent_id", false);
		$CI->db->from("foto_albums");
		$CI->db->order_by( 'foto_album_id', 'desc' );
		if ( $count !== false ) $CI->db->limit( $count); 
		if ( $albumid !== false ) $CI->db->where( 'foto_album_parent_id', $albumid );
		$query = $CI->db->get();
	
		if ( $query->num_rows() > 0 )
		{
			$albums = $query->result_array();
			return $albums;
		} else return false;
	} 	
	
 	function get_foto_tags( $fotoid, $sep = ', ', $admin = false ) {
		$CI = get_instance();
		$CI->db->select('foto_tag_name, foto_tag_id');
		$CI->db->where('foto_id', $fotoid);
		$CI->db->from('foto_tags');
		$query = $CI->db->get();
		$meta = '';
		if ( $query->num_rows() > 0 )
		{
			$results = $query->result_array();
			$cnt = count( $results );
			$i = 1;
			foreach ( $results as $tag )
			{
				$meta .= '<span ';
				if ( $admin ) {
					$meta .= 'id="admin-foto-tag" tagid="' .  $tag['foto_tag_id'] . '">' . $tag['foto_tag_name'] . '<a href="" onclick="delete_tags('. $tag['foto_tag_id'].'); return false;">&nbsp;</a>';
				} else {
					$meta .= 'id="foto-tag" tagid="' .  $tag['foto_tag_id'] . '">' . $tag['foto_tag_name'];
				}
				if ( $i < $cnt ) $meta .= $sep;
				$meta .= '</span>';
				$i++;
			}
		}		
		return $meta;
	} 
	
 	function generate_albums_out( $albums ) {
	
		$form = '';
		if ( is_array( $albums ) ) {
			$form .= '<div class="type type_albums">';
			global $foto_albums;
			foreach ( $albums as $album ) {
				$form .= '<div class="album">';
				$url = getinfo('site_url') . $foto_albums . '/' . $album['foto_album_slug'];
				$form .= '<a href="' . $url . '" title="'.$album['foto_album_title'].'">' . $album['foto_album_title'] . '</a>';
				$form .= '</div>';
			}
			$form .= '</div>';
		}
		return $form;
	} 
	
 	function foto_view_count_first($unique = false, $name_cookies = 'maxsite-cms-fotki', $expire = 2592000) {
		global $_COOKIE, $_SESSION;

		if ( !mso_get_option('page_view_enable', 'templates', '1') AND !$unique) return true; //если нет такой опции или не пришло в функцию, то выходим
		if ( !$unique ) $unique = mso_get_option('page_view_enable', 'templates', '1');

		$slug = mso_segment(2);
		$all_slug = array();

		if( $unique == 0 ) return false; // не вести подсчет
		elseif ($unique == 1) //с помощью куки
		{
			if (isset($_COOKIE[$name_cookies]))	$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}
		elseif ($unique == 2) //с помощью сессии
		{
			session_start();
			if (isset($_SESSION[$name_cookies]))	 $all_slug = explode('|', $_SESSION[$name_cookies]); // значения текущей сессии
			if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}

		// нужно увеличить счетчик
		$all_slug[] = $slug; // добавляем текущий slug
		$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
		$all_slug = implode('|', $all_slug); // соединяем обратно в строку
		$expire = time() + $expire;

		if ($unique == 1) @setcookie($name_cookies, $all_slug, $expire); // записали в кук
		elseif ($unique == 2) $_SESSION[$name_cookies]=$all_slug; // записали в сессию

		// получим текущее значение page_view_count
		// и увеличиваем значение на 1
		$CI = get_instance();
		$CI->db->select('foto_view_count');
		
		//if(is_numeric($slug)) // ссылка вида http://site.com/page/1 
		//	$CI->db->where('page_id', $slug);
		//else
			$CI->db->where('foto_slug', $slug);

		$CI->db->limit(1);
		$query = $CI->db->get('foto');

		if ($query->num_rows() > 0)
		{
			$pages = $query->row_array();
			$page_view_count = $pages['foto_view_count'] + 1;

			$CI->db->where('foto_slug', $slug);
			$CI->db->update('foto', array('foto_view_count'=>$page_view_count));
			$CI->db->cache_delete('foto', $slug);

			return true;
		}	
	} 
	
 	function fotki_get_comments( $fotoid ) {
	//pr( $fotoid );
	# функция получения комментариев

		global $MSO;

		if ( !isset($r['limit']) )	$r['limit'] = false;
		if ( !isset($r['order']) )	$r['order'] = 'asc';
		if ( !isset($r['tags']) )	$r['tags'] = '<p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($r['tags_users']) )	$r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($r['tags_comusers']) )	$r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($r['anonim_comments']) )	$r['anonim_comments'] = array();
		if ( !isset($r['anonim_title']) )	$r['anonim_title'] = '';// ' ('. t('анонимно'). ')'; // дописка к имени для анонимов
		if ( !isset($r['anonim_no_name']) )	$r['anonim_no_name'] = t('Аноним', 'template');// Если не указано имя анонима
		
		// если аноним указывает имя с @, то это страница в твиттере - делаем ссылку
		if ( !isset($r['anonim_twitter']) )	$r['anonim_twitter'] = true; 

		// дописка к имени для комментаторов без ника
		if ( !isset($r['add_author_name']) )	$r['add_author_name'] = t('Комментатор');


		$CI = get_instance();
		
		// получим список всех комюзеров, где посдчитается количество их комментариев
		$all_comusers = mso_comuser_update_count_comment();

		$CI->db->select('foto.foto_id, foto.foto_slug, foto.foto_title, foto_comments.*,
		users.users_id, 
		users.users_nik,
		users.users_count_comments,
		users.users_url,
		users.users_email,
		users.users_avatar_url,
		
		comusers.comusers_id, 
		comusers.comusers_nik,
		comusers.comusers_count_comments,
		comusers.comusers_allow_publish,
		comusers.comusers_email,
		comusers.comusers_avatar_url
		');

		if ($fotoid) $CI->db->where('foto.foto_id', $fotoid);
		
		// если нет анономого коммента, то вводим условие на comments_approved=1 - только разрешенные
		if (!$r['anonim_comments'])
		{
			$CI->db->where('foto_comments.foto_comments_approved', '1');
		}
		else // есть массив с указанными комментариям - они выводятся отдельно
		{
			$CI->db->where('foto_comments.foto_comments_approved', '0');
			$CI->db->where_in('foto_comments.foto_comments_id', $r['anonim_comments']);
		}

		// вот эти два join жутко валят мускуль...
		// пока решение не найдено, все запросы к комментам следует кэшировать на уровне плагина
		$CI->db->join('users', 'users.users_id = foto_comments.foto_comments_users_id', 'left');
		$CI->db->join('comusers', 'comusers.comusers_id = foto_comments.foto_comments_comusers_id', 'left');

		
		// вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
		$CI->db->where($CI->db->dbprefix . 'foto.foto_id', $CI->db->dbprefix . 'foto_comments.foto_comments_foto_id', false);
		
		//$CI->db->where('page.page_status', 'publish');
		
		$CI->db->order_by('foto_comments.foto_comments_date', $r['order']);
		
		if ($r['limit']) $CI->db->limit($r['limit']);
		
		$CI->db->from('foto_comments, foto');
		
		//pr(_sql());

		$query = $CI->db->get();

		//return array();


		if ($query->num_rows() > 0)
		{
			$comments = $query->result_array();
			//pr($comments);
			foreach ($comments as $key=>$comment)
			{
				//pr($comment);

				$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
				

				if ($comment['comusers_id']) // это комюзер
				{
					if ($comment['comusers_nik']) $comment['foto_comments_author_name'] = $comment['comusers_nik'];
					else $comment['foto_comments_author_name'] = $r['add_author_name'] . ' ' . $comment['comusers_id'];
					$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
							. $comment['foto_comments_author_name'] . '</a>';
					$commentator = 1;

					if (isset($all_comusers[$comment['comusers_id']]))
						$comments[$key]['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];

				}
				elseif ($comment['users_id']) // это автор
				{
					if ($comment['users_url'])
							$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
						else $comment['comments_url'] = $comment['users_nik'];
					$commentator = 2;
				}
				else // просто аноним
				{
					if (!$comment['foto_comments_author_name']) $comment['foto_comments_author_name'] = $r['anonim_no_name'];
					if ($r['anonim_twitter']) // разрешено проверять это твиттер-логин?
					{
						
						if (strpos($comment['foto_comments_author_name'], '@') === 0) // первый символ @
						{	
							$lt = substr($comment['foto_comments_author_name'], 1); // вычленим @
							
							// проверим корректность логина
							if ($lt == mso_slug($lt))
								$comment['comments_url'] = '<a href="http://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
							else
								$comment['comments_url'] = $comment['foto_comments_author_name'] . $r['anonim_title']; 
						}
						else $comment['comments_url'] = $comment['foto_comments_author_name'] . $r['anonim_title']; 
					}
					else
					{
						$comment['comments_url'] = $comment['foto_comments_author_name'] . $r['anonim_title']; 
					}
				}


				$comments_content = $comment['foto_comments_content'];
				
				// защитим pre
				$t = $comments_content;
				$t = str_replace('&lt;/pre>', '</pre>', $t); // проставим pre - исправление ошибки CodeIgniter
				
				$t = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $t);

				if ($commentator==1) $t = strip_tags($t, $r['tags_comusers']);
				elseif ($commentator==2) $t = strip_tags($t, $r['tags_users']);
				else $t = strip_tags($t, $r['tags']);
				
				$t = mso_xss_clean($t);

				$t = str_replace('[html_base64]', '<pre>[html_base64]', $t); // проставим pre
				$t = str_replace('[/html_base64]', '[/html_base64]</pre>', $t);
				
				// обратная замена
				$t = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $t);
				
				$comments_content = $t; // сохраним как текст комментария
				
				$comments_content = mso_hook('comments_content', $comments_content);
				
				$comments_content = str_replace("\n", "<br>", $comments_content);
		
				$comments_content = str_replace('<p>', '&lt;p&gt;', $comments_content);
				$comments_content = str_replace('</p>', '&lt;/p&gt;', $comments_content);
				$comments_content = str_replace('<P>', '&lt;P&gt;', $comments_content);
				$comments_content = str_replace('</P>', '&lt;/P&gt;', $comments_content);
				
				
				if (mso_hook_present('comments_content_custom'))
				{
					$comments_content = mso_hook('comments_content_custom', $comments_content);
				}
				else
				{
					$comments_content = mso_auto_tag($comments_content, true);
					$comments_content = mso_hook('content_balance_tags', $comments_content);
				}
				
				$comments_content = mso_hook('comments_content_out', $comments_content);

				$comments[$key]['comments_content'] = $comments_content;
				$comments[$key]['comments_url'] = $comment['comments_url'];

			}
		}
		else
			$comments = array();

		return $comments;
	
	} 
	
	# функция добавляет новый коммент и выводит сообщение о результате
 	function foto_get_new_comment($args = array())
	{
		global $MSO;

		if ( $post = mso_check_post(array('foto_comments_session', 'foto_comments_submit', 'foto_comments_foto_id', 'comments_content')) )
		{
			// mso_checkreferer(); // если нужно проверять на реферер
			$CI = get_instance();
			
			// заголовок страницы
			if ( !isset($args['foto_title']) )		$args['foto_title'] = '';
			
			// стили
			if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
			if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-error';
			
			// разрешенные тэги
			if ( !isset($args['tags']) )		$args['tags'] = '<p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code>';
			
			// обрабатывать текст на xss-атаку
			if ( !isset($args['xss_clean']) )		$args['xss_clean'] = true;
			
			// если найдена xss-атака, то не публиковать комментарий
			if ( !isset($args['xss_clean_die']) )		$args['xss_clean_die'] = false;
			
			if ( !isset($args['noword']) )		$args['noword'] = array('.com', '.ru', '.net', '.org', '.info', '.ua', 
																		'.su', '.name', '/', 'www.', 'http', ':', '-', '"',
																		'«', '»', '%', '<', '>', '&', '*', '+', '\'' );
			
			mso_hook('add_new_comment');


			if (!mso_checksession($post['foto_comments_session']) )
				return '<div class="' . $args['css_error']. '">'. t('Ошибка сессии! Обновите страницу'). '</div>';

			if (!$post['foto_comments_foto_id']) return '<div class="' . $args['css_error']. '">'. t('Ошибка!'). '</div>';


			$comments_foto_id = $post['foto_comments_foto_id'];
			$id = (int) $comments_foto_id;
			if ( (string) $comments_foto_id != (string) $id ) $id = false; // $comments_page_id не число
			if (!$id) return '<div class="' . $args['css_error']. '">'. t('Ошибка!'). '</div>';


			// капчу проверим
			// если этот хук возвращает false, значит капча неверная
			if (!mso_hook('comments_new_captcha', true))
			{	
				// если определен хук на неверную капчу, отдаем его
				if (mso_hook_present('comments_new_captcha_error'))
				{
					return mso_hook('comments_new_captcha_error');
				}
				else
				{
					return '<div class="' . $args['css_error']. '">'. t('Ошибка! Неверно введены нижние символы!'). '</div>';
				}
			}
			
			// вычищаем от запрещенных тэгов
			if ($args['tags']) 
			{
				// перед этим нужно все pre защитить
				$t = $post['comments_content'];
				
				$t = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $t);
				
				$t = strip_tags($t, $args['tags']); // теперь оставим только разрешенные тэги
				
				$t = str_replace('[html_base64]', '<pre>[html_base64]', $t); // проставим pre
				$t = str_replace('[/html_base64]', '[/html_base64]</pre>', $t);
				
				// обратная замена
				$t = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $t);
				
				$post['comments_content'] = $t; // сохраним как текст комментария
			}
			
			// если указано рубить коммент при обнаруженной xss-атаке 
			if ($args['xss_clean_die'] and $mso_xss_clean($post['comments_content'], true, false) === true)
			{
				return '<div class="' . $args['css_error']. '">'. t('Обнаружена XSS-атака!'). '</div>';
			}
				
			if (!trim($post['comments_content'])) 
				return '<div class="' . $args['css_error']. '">'. t('Ошибка, нет текста!'). '</div>';

			// возможно есть текст, но только из одних html - не пускаем
			if ( !trim(strip_tags(trim($post['comments_content']))) )
				return '<div class="' . $args['css_error']. '">'. t('Ошибка, нет полезного текста!'). '</div>';
			
			// вычищаем текст от xss
			if ($args['xss_clean'])
			{
				$post['comments_content'] =  mso_xss_clean($post['comments_content']);
				// проставим pre исправление ошибки CodeIgniter
				$post['comments_content'] = str_replace('&lt;/pre>', '</pre>', $post['comments_content']); 
			}	
			
			$comments_author_ip = $_SERVER['REMOTE_ADDR'];
			$comments_date = date('Y-m-d H:i:s');

			$comments_content = mso_hook('new_comments_content', $post['comments_content']);

			// есть дли родитель у комментария
			$comments_parent_id = isset($post['foto_comments_parent_id']) ? $post['foto_comments_parent_id'] : '0'; 
			
			// провека на спам - проверим через хук new_comments_check_spam
			$comments_check_spam = mso_hook('new_comments_check_spam',
											array(
												'comments_content' => $comments_content,
												'comments_date' => $comments_date,
												'comments_author_ip' => $comments_author_ip,
												'comments_page_id' => $comments_foto_id,
												'comments_server' => $_SERVER,
												'comments_parent_id' => $comments_parent_id,
											), false);

			// если есть спам, то возвращается что-то отличное от comments_content
			// если спама нет, то должно вернуться false
			// если есть подозрения, то возвращается массив с moderation (comments_approved)
			// если есть параметр check_spam=true, значит определен спам и он вообще не пускается
			// сообщение для вывода в парметре 'message'

			// разрешение антиспама moderation
			// -1 - не определено, 0 - можно разрешить, 1 - отдать на модерацию
			$moderation = -1;

			if ($comments_check_spam)
			{
				if (isset($comments_check_spam['check_spam']) and $comments_check_spam['check_spam']==true)
				{
					if ( isset($comments_check_spam['message']) and $comments_check_spam['message'] )
						return '<div class="' . $args['css_error']. '">' . $comments_check_spam['message'] . '</div>';
					else
						return '<div class="' . $args['css_error']. '">'. t('Ваш комментарий определен как спам и удален.'). '</div>';
				}
				else
				{
					// спам не определен, но возможно стоит moderation - принудительная модерация
					if (isset($comments_check_spam['moderation'])) $moderation = $comments_check_spam['moderation'];
				}
			}

			

			// проверим есть ли уже такой комментарий
			// проверка по ip и тексту
			$CI->db->select('foto_comments_id');
			$CI->db->where(array (
				'foto_comments_foto_id' => $comments_foto_id,
				'foto_comments_author_ip' => $comments_author_ip,
				'foto_comments_content' => $comments_content,
				));

			$query = $CI->db->get('foto_comments');
			if ($query->num_rows()) // есть такой коммент
			{
				return '<div class="' . $args['css_error']. '">'. t('Похоже, вы уже отправили этот комментарий...'). '</div>';
			}
			
			
			
			if (is_login()) // коммент от автора
			{
				$comments_users_id = $MSO->data['session']['users_id'];

				$ins_data = array (
					'foto_comments_users_id' => $comments_users_id,
					'foto_comments_foto_id' => $comments_foto_id,
					'foto_comments_author_ip' => $comments_author_ip,
					'foto_comments_date' => $comments_date,
					'foto_comments_content' => $comments_content,
					'foto_comments_parent_id' => $comments_parent_id,
					'foto_comments_approved' => 1 // авторы могут сразу публиковать комменты без модерации
					);

				$res = ($CI->db->insert('foto_comments', $ins_data)) ? '1' : '0';

				if ($res)
				{
					mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $args['foto_title']);
					mso_flush_cache();
					$CI->db->cache_delete_all();
					mso_hook('new_comment');
					mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
				}
				else
					return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
			}
			else
			{
				if ( isset($post['comments_reg']) ) // комюзер или аноном
				{
					if ($post['comments_reg'] == 'reg') // нужно зарегистрировать или уже есть регистрация
					{
						
						// проверим есть ли разршение на комментарии от комюзеров
						// для случаев подделки post-запроса
						if ( !mso_get_option('allow_comment_comusers', 'general', '1') )
							return '<div class="' . $args['css_error']. '">'. t('Error allow_comment_comusers'). '</div>';
							

						if ( !isset($post['comments_email']) or !$post['comments_email'] )
							return '<div class="' . $args['css_error']. '">'. t('Нужно указать Email'). '</div>';

						if ( !isset($post['comments_password']) or !$post['comments_password'] )
							return '<div class="' . $args['css_error']. '">'. t('Нужно указать пароль'). '</div>';

						$comments_email = mso_strip($post['comments_email']);
						$comments_password = mso_strip($post['comments_password']);

						if ( !mso_valid_email($comments_email) )
							return '<div class="' . $args['css_error']. '">'. t('Ошибочный Email'). '</div>';

						// вначале нужно зарегистрировать comюзера - получить его id и только после этого добавить сам коммент
						// но вначале есть смысл проверить есть ли такой ком-пользователь

						$comusers_id = false;

						$CI->db->select('comusers_id, comusers_password');
						$CI->db->where('comusers_email', $comments_email);
						$query = $CI->db->get('comusers');
						if ($query->num_rows()) // есть такой комюзер
						{
							$row = $query->row_array(1);

							// пароль не нужно шифровать mso_md5
							if (isset($post['comments_password_md']) and $post['comments_password_md'])
							{
								if ($row['comusers_password'] != $comments_password) // пароль неверный
									return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
							}
							else
							{
								if ($row['comusers_password'] != mso_md5($comments_password)) // пароль неверный
									return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
							}

							$comusers_id = $row['comusers_id']; // получаем номер комюзера
						}
						else
						{
							// такого комюзера нет
							$ins_data = array (
								'comusers_email' => $comments_email,
								'comusers_password' => mso_md5($comments_password)
								);

							// генерируем случайный ключ активации
							$ins_data['comusers_activate_key'] = mso_md5(rand());
							$ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
							$ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
							$ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
							$ins_data['comusers_notify'] = '1'; // сразу включаем подписку на уведомления
							
							// Автоматическая активация новых комюзеров
							// если активация стоит автоматом, то сразу её и прописываем
							if ( mso_get_option('comusers_activate_auto', 'general', '0') )
								$ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];

							$res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

							if ($res)
							{
								$comusers_id = $CI->db->insert_id(); // номер добавленной записи

								// нужно добавить опцию в мета «новые комментарии, где я участвую» subscribe_my_comments
								// вначале грохаем если есть такой ключ
								$CI->db->where('meta_table', 'comusers');
								$CI->db->where('meta_id_obj', $comusers_id);
								$CI->db->where('meta_key', 'subscribe_my_comments');
								$CI->db->delete('meta');
								
								// теперь добавляем как новый
								$ins_data2 = array(
										'meta_table' => 'comusers',
										'meta_id_obj' => $comusers_id,
										'meta_key' => 'subscribe_my_comments',
										'meta_value' => '1'
										);
								
								$CI->db->insert('meta', $ins_data2);
						
								// почему CodeIgniter не может так?
								// INSERT INTO table SET column = 1, id=1 ON DUPLICATE KEY UPDATE column = 2
								
								
								// отправляем ему уведомление с кодом активации
								mso_email_message_new_comuser($comusers_id, $ins_data, mso_get_option('comusers_activate_auto', 'general', '0')); 
							}
							else
								return '<div class="' . $args['css_error']. '">'. t('Ошибка регистрации'). '</div>';
						}

						if ($comusers_id)
						{
							// Модерация комюзеров 1 - модерировать
							$comments_com_approved = mso_get_option('new_comment_comuser_moderate', 'general', 1);

							// если включена модерация комюзеров
							// и включена опция только первого комментария
							// то получаем кол-во комментариев комюзера
							if ($comments_com_approved and mso_get_option('new_comment_comuser_moderate_first_comment', 'general', 0)) 
							{
								$all_comusers = mso_comuser_update_count_comment(); // список комюзер => колво комментов
								
								// есть такой комюзер и у него более 1 комментария
								if (isset($all_comusers[$comusers_id]) and $all_comusers[$comusers_id] > 0)
									$comments_com_approved = 0; // разрешаем публикацию
							}
							
							// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
							$comments_com_approved = !$comments_com_approved;
							
							if ($moderation == 1) $comments_com_approved = 0; // антиспам определил, что нужно премодерировать

							if ($comments_com_approved == 1) // если разрешено
							{
								$comments_com_approved = mso_hook('new_comments_check_spam_comusers',
												array(
													'comments_page_id' => $comments_foto_id,
													'comments_comusers_id' => $comusers_id,
													'comments_com_approved' => $comments_com_approved,
												), 1);
							}


							// комюзер добавлен или есть
							// теперь сам коммент
							$ins_data = array (
								'foto_comments_foto_id' => $comments_foto_id,
								'foto_comments_comusers_id' => $comusers_id,
								'foto_comments_author_ip' => $comments_author_ip,
								'foto_comments_date' => $comments_date,
								'foto_comments_content' => $comments_content,
								'foto_comments_approved' => $comments_com_approved,
								'foto_comments_parent_id' => $comments_parent_id,
								);

							$res = ($CI->db->insert('foto_comments', $ins_data)) ? '1' : '0';
							if ($res)
							{
								
								$id_comment_new = $CI->db->insert_id();
								
								// посколько у нас идет редирект, то данные об отправленном комменте
								// сохраняем в сессии номер комментария
								if ( isset($MSO->data['session']) )
								{
									$CI->session->set_userdata(array( 'foto_comments' =>
														array(
														// $CI->db->insert_id()=>$comments_page_id
														$id_comment_new
														)));
								}
								mso_email_message_new_comment($id_comment_new, $ins_data, $args['page_title']);
								// mso_flush_cache();
								$CI->db->cache_delete_all();
								mso_hook('new_comment');
								
								
								
								
								# если комюзер не залогинен, то сразу логиним его
								
								$CI->db->select('comusers_id, comusers_password, comusers_email, 
										comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
								$CI->db->where('comusers_email', $comments_email);
								$CI->db->where('comusers_password', mso_md5($comments_password));
								$query = $CI->db->get('comusers');
								
								if ($query->num_rows()) // есть такой комюзер
								{
									$comuser_info = $query->row_array(1); // вся инфа о комюзере
									
									// сразу же обновим поле последнего входа
									$CI->db->where('comusers_id', $comuser_info['comusers_id']);
									$CI->db->update('comusers', array('comusers_last_visit'=>date('Y-m-d H:i:s')));
									
									$expire  = time() + 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд
									
									$name_cookies = 'maxsite_comuser';
									$value = serialize($comuser_info); 
									
									# ставим куку и редиректимся автоматом
									mso_add_to_cookie($name_cookies, $value, $expire, 
												mso_current_url(true) . '#comment-' . $id_comment_new);
									exit;
								}
								
								
								
								
								mso_redirect(mso_current_url() . '#comment-' . $id_comment_new);
							}
							else
								return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
						}
					}
					elseif  ($post['comments_reg'] == 'noreg')
					{
						// комментарий от анонима
						
						// проверим есть ли разрешение на комментарии от анонимов
						// для случаев подделки post-запроса
						if ( !mso_get_option('allow_comment_anonim', 'general', '1') )
							return '<div class="' . $args['css_error']. '">'. t('Error allow_comment_anonim'). '</div>';

						if ( isset($post['comments_author']) )
						{
							$comments_author_name = mso_strip($post['comments_author']);
							$comments_author_name = str_replace($args['noword'], '', $comments_author_name);
							$comments_author_name = trim($comments_author_name);
							if (!$comments_author_name) $comments_author_name = t('Аноним');
						}
						else $comments_author_name = 'Аноним';

						// можно ли публиковать без модерации?
						$comments_approved = mso_get_option('new_comment_anonim_moderate', 'general', 1);

						// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
						$comments_approved = !$comments_approved;

						if ($moderation==1) $comments_approved = 0; // антиспам определил, что нужно премодерировать

						$ins_data = array (
							'foto_comments_foto_id' => $comments_foto_id,
							'foto_comments_author_name' => $comments_author_name,
							'foto_comments_author_ip' => $comments_author_ip,
							'foto_comments_date' => $comments_date,
							'foto_comments_content' => $comments_content,
							'foto_comments_approved' => $comments_approved,
							'foto_comments_parent_id' => $comments_parent_id,
							);

						$res = ($CI->db->insert('foto_comments', $ins_data)) ? '1' : '0';

						if ($res)
						{
							// посколько у нас идет редирект, то данные об отправленном комменте
							// сохраняем в сессии номер комментария
							if ( isset($MSO->data['session']) )
							{
								$CI->session->set_userdata(array( 'foto_comments' =>
													array(
													// $CI->db->insert_id()=>$comments_page_id
													$CI->db->insert_id()
													)));
							}
							mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $args['page_title']);
							// mso_flush_cache();
							$CI->db->cache_delete_all();
							mso_hook('new_comment');
							mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
						}
						else
							return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
					}
				}
			}
		}
		// else return '<div class="comment-new">Комментарий добавлен и возможно ожидает модерации.</div>';
	}

	
?>