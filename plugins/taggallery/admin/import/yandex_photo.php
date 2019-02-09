<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
   
	
	// Возвращает массив альбомов пользователя
	function taggallery_yf_get_albums ($user = '' , $sort = '')
	{
	 $userpath = 'http://api-fotki.yandex.ru/api/users/' . $user . '/';
	 $albumspath = $userpath . 'albums/' . $sort . '/';
   $feed = simplexml_load_file($albumspath);
	 if ( empty($feed) ) return array();
		
	  $entries = $feed->children('http://www.w3.org/2005/Atom')->entry;
		$result = array();
    foreach ($entries as $entry) 
		{
       $details = $entry->children('http://www.w3.org/2005/Atom');
       $content = $details->content->attributes();
       $id = preg_replace('/.*:(\d+)$/', '\1', $details->id);
       $result[$id] = array(
                'id' => $id,
                'title' => $details->title,
                'image-count' => 0 // $details->image-count не получается
            );
    }
    return $result;
	}
	
	// Возвращает массив с результатом преобразования Atom Feed XML

	function taggallery_yf_parsephoto($entriesarray , $previewsize = '150' , $articlesize = '500')
	{
		$result = array();
		$counter = 0;
		
		foreach ($entriesarray as $entries) 
		{
	    foreach ($entries as $entry) 
			{
				$counter++;
				$details = $entry->children('http://www.w3.org/2005/Atom');
				$imagesrc = $details->content->attributes()->src;
				
				$result[] = array (
					'id' => $details->id,
					'title' => $details->title,
					'created' => $details->created,
					'preview_size_link' => preg_replace('/(.*)((_|-)+)(\w{1,4})$/', '$1$2' . $previewsize, $imagesrc),
					'article_size_link' => preg_replace('/(.*)((_|-)+)(\w{1,4})$/', '$1$2' . $articlesize, $imagesrc),
					'original_size_link' => preg_replace('/(.*)((_|-)+)(\w{1,4})$/', '$1$2orig', $imagesrc),
					'link_yandex_image_page' => $details->link[2]->attributes()->href
				);
			}
		}
		return $result;
	}	
	
	// Возвращает все изображения в указанном альбоме или все изображения изо всех альбомов, если $idAlbum опущен
	
	function taggallery_yf_get_photos($user = '' , $album_id = '' , $sort = '' , $previewsize , $articlesize)
	{
 	 $userpath = 'http://api-fotki.yandex.ru/api/users/' . $user . '/';

		/* возвращаем коллекцию фотографий из указанного альбома*/
		$photospath = $userpath . 'album/' . $album_id . '/photos/' . $sort . '/';
		$entries = array();
		while ( !empty($photospath) )
		{
				$feed = simplexml_load_file($photospath);
				$entries[] = $feed->children('http://www.w3.org/2005/Atom')->entry;
				$links = $feed->link;
				foreach ($links as $link) 
				{
					if ($link->attributes()->rel == "next")
						$photospath = $link->attributes()->href;
					else
						$photospath = null;
				}
		}
		return taggallery_yf_parsePhoto($entries ,$previewsize , $articlesize);
	}
   
   
//***************************************************
 if( !isset($options['yf_user'])) $options['yf_user'] = 'filsadovnik';   
 if( !isset($options['yf_min'])) $options['yf_min'] = 'S';   
 if( !isset($options['yf_max'])) $options['yf_max'] = 'L';  
 if( !isset($options['yf_sort'])) $options['yf_sort'] = 'updated'; 
 
 $albums = taggallery_yf_get_albums($options['yf_user']);
 
    if ( $post = mso_check_post(array('f_session_id', 'f_yf_user' , 'f_yf_min' , 'f_yf_max' , 'f_user_submit')) ) //если нажата кнопка установить пользователя
    {
	    mso_checkreferer();
	    $options['yf_user'] = $post['f_yf_user'];
	    $options['yf_min'] = $post['f_yf_min'];
	    $options['yf_max'] = $post['f_yf_max'];
      mso_add_option('taggallery', $options, 'plugins');
      echo '<div class="update">Настройки изменены</div>';
    } 

    if ( $post = mso_check_post(array('f_session_id', 'f_get_gallery_submit')) ) //если нажата кнопка удалить
    {
	    mso_checkreferer();
	    $album_id = mso_array_get_key($post['f_get_gallery_submit']);
      $photos = taggallery_yf_get_photos($options['yf_user'] , $album_id , $options['yf_sort'] ,$options['yf_min'] ,$options['yf_max']);
      $add_photos = array();
      $i = 0;
      foreach ($photos as $cur_photo)
      {
         $add_photo = array();
 	       $add_photo['tags'] = array();
 	       $add_photo['tags'][] = (string) trim($albums[$album_id]['title']);
	       $add_photo['date'] = (string) $cur_photo['created'];
	       $add_photo['desc'] = (string) $cur_photo['title'];
	       $imagesize = getimagesize($cur_photo['article_size_link']);
	       $add_photo['width'] = (string) $imagesize[0];
	       $add_photo['height'] = (string) $imagesize[1];
	       $add_photo['mini_link'] = (string) $cur_photo['preview_size_link'];
	       $add_photo['link'] =  (string) $cur_photo['article_size_link'];
	       $add_photo['file'] = '';
	       $add_photo['source'] = 'yandex_photo';
	       $add_photo['source_link'] = htmlspecialchars($cur_photo['link_yandex_image_page']);
	       $add_photo['dir'] = '';
	       $add_photo['full_size_link'] = (string) $cur_photo['original_size_link'];
     		 if ($add_photo['width'] > $add_photo['height']) $add_photo['type'] = 1;
		     elseif ($add_photo['width'] < $add_photo['height']) $add_photo['type'] = 2;
		     else $add_photo['type'] = 0;
		     $photo_id = stristr($cur_photo['id'] , "photo:");
		     $key = trim(str_replace("photo:" , "" , $photo_id));
	       $i++;
	       $add_photo['id'] = $key;
	       $add_photos[$key] = $add_photo;
	 /*      echo 
	       $add_photo['date'] . '<br/>'.
	       $add_photo['desc'] . '<br/>'.
	       $add_photo['width'] . '<br/>'.
	       $add_photo['height'] . '<br/>'.
	       $add_photo['mini_link'] . '<br/>'.
	       $add_photo['link'] . '<br/>'.
	       $add_photo['dir'] . '<br/>'.
	       $add_photo['full_size_link'] . '<br/>'.
	       $add_photo['id'] . '<br/>';
	   */    
	       
      }
      taggallery_add_pictures($add_photos , $options['create_pages']);
      echo '<div class="update">Найдено ' . $i . ' фотографий.</div>';
    }

 
 $image_sizes = 	array(
 	'XXXS' => '50px',
	'XXS' => '75px',
	'XS' => '100px',
	'S' => '150px',
	'M' => '300px',
	'L' => '500px',
	'XL' => '800px',
	'orig' => 'Оригинальный размер изображения');

 $sortable = 	array(
	'updated'  => 'по времени последнего изменения, от новых к старым',
	'rupdated' => 'по времени последнего изменения, от старых к новым',
	'published' => 'по времени загрузки от новых к старым',
	'rpublished' => 'по времени загрузки от старых к новым',
	'created' => 'по времени создания согласно EXIF-данным, от новых к старым',
	'rcreated' => 'по времени создания согласно EXIF-данным, от старых к новым ');

	   
		$form = '<tr><td><H2>Настройки</H2></td></tr>';
		$form .= '<tr><td>' . t('User_ID: ') . '</td><td><input type="text" name="f_yf_user" value="' . $options['yf_user'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Размер миниатюры: ') . '</td><td>' . form_dropdown('f_yf_min' , $image_sizes , $options['yf_min']) . '</td></tr>';
		$form .= '<tr><td>' . t('Размер фото: ') . '</td><td>' . form_dropdown('f_yf_max' , $image_sizes , $options['yf_max']) . '</td></tr>';
		$form .= '<tr><td>' . t('Сортировка: ') . '</td><td>' . form_dropdown('f_yf_sort' , $sortable , $options['yf_sort']) . '</td></tr>';
		
		$form .= '<tr><td><input type="submit" name="f_user_submit" value="' . t('Изменить', 'plugins') . '"  /></td></tr>';
		
		$form .= '<tr><td><H2>Доступные альбомы:</H2></td></tr>';
		foreach ($albums as $id => $album)
		{
		  $album['image-count'] = 'Неизвестно';
		  $album_prev = '<p><strong>' . $album['title'] . '</strong>  ' . $album['image-count'] . ' фото</p>';
		  $form .= '<tr><td>' . $album_prev . '</td><td><input type="submit" name="f_get_gallery_submit[' . $id . ']" value="Добавить фото из этого альбома" style="margin: 25px 0 5px 0;" /></td></tr>';
		}


	
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<table>';
        echo $form;
        echo '</table>';
		echo '</form>';

?>