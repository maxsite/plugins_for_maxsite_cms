<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//***************************************************
  require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
if( !isset($options_admin['picasa_user'])) $options_admin['picasa_user'] = 'filsadovnik';   
if( !isset($options_admin['picasa_min'])) $options_admin['picasa_min'] = '144';   
if( !isset($options_admin['picasa_max'])) $options_admin['picasa_max'] = '600';  
if( !isset($options_admin['picasa_default_tags'])) $options_admin['picasa_default_tags'] = '';  

    if ( $post = mso_check_post(array('f_session_id', 'f_user_id' , 'f_photo_mini' , 'f_photo' , 'f_user_submit')) ) //если нажата кнопка установить пользователя
    {
	    mso_checkreferer();
	    $options_admin['picasa_user'] = $post['f_user_id'];
	    $options_admin['picasa_min'] = $post['f_photo_mini'];
	    $options_admin['picasa_max'] = $post['f_photo'];
      mso_add_option('taggallery_admin', $options_admin, 'plugins');
      echo '<div class="update">Пользователь установлен</div>';
    }
    
    
  if ( $post = mso_check_post(array('f_session_id', 'f_default_tags' , 'f_get_gallery_submit')) ) //если нажата кнопка получить картинки из альбома
  {
	    mso_checkreferer();
	    $album_id = mso_array_get_key($post['f_get_gallery_submit']);
	    $options_admin['picasa_default_tags'] = $post['f_default_tags'];
      mso_add_option('taggallery_admin', $options_admin, 'plugins');
      
	    $photos = getPicasaPhotos($options_admin['picasa_user'], $album_id);
	    foreach ($photos as $photo)
	    {
         $add_photo = array();
         
         
         // извлечем photoid
         $guid = stristr($photo['guid'], "photoid/");
         $guid = explode('?', $guid);
         $guid = str_replace("photoid/" , "" , $guid[0]);        
         $add_photo['picture_slug'] = mso_slug($guid);
         $add_photo['picture_file'] = $guid;

	       $add_photo['picture_date_file'] = date ("Y-m-d h:m:s" , $photo['date_timestamp']);
	       $add_photo['picture_title'] = $photo['title'];
	    //   $add_photo['picture_width'] = $cur_photo['width'];
	     //  $add_photo['picture_height'] = $cur_photo['height'];
	       $add_photo['picture_url'] = str_replace("s288" , "s". $options_admin['picasa_max'] , $photo['path']);
	       $add_photo['picture_mini_url'] = str_replace("s288" , "s". $options_admin['picasa_min'] , $photo['path']);

	       $add_photo['picture_tags'] = explode("," , $options_admin['picasa_default_tags']);
      
	       $add_photo['picture_source_id'] = $source_id;
	       $add_photo['picture_full_size_url'] = $photo['link'];
	       
	       $add_photo['picture_date_photo'] = date ("Y-m-d h:m:s" , $photo['date_timestamp']);
         $res = taggallery_add_picture($add_photo , false); //добавляем фото не изменяя описания mso_descriptions
        // pr($add_photo);
         if ($res['errors']) echo '<div class="error">' . $res['errors'] . '</div>';
         if ($res['messages']) echo '<div class="update">' . $res['messages'] . '</div>';	    
	    
	    }


  }
   
//*******************************************************


    $albums = getPicasaAlbumList($options_admin['picasa_user']);
    
		$form = '<table><th>' . t('Экспорт фотографий из picasa', 'plugins') . '</th>';
	   
		$form .= '<tr><td><H2>Настройки</H2></td></tr>';
		$form .= '<tr><td>' . t('User_ID: ') . '</td><td><input type="text" name="f_user_id" value="' . $options_admin['picasa_user'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Размер миниатюры: ') . '</td><td><input type="text" name="f_photo_mini" value="' . $options_admin['picasa_min'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Размер фото: ') . '</td><td><input type="text" name="f_photo" value="' . $options_admin['picasa_max'] . '"></td></tr>';
		$form .= '<tr><td><input type="submit" name="f_user_submit" value="' . t('Изменить', 'plugins') . '"  /></td></tr>';
	
		$form .= '</table><table><th>' . t('Экспорт фотографий из picasa', 'plugins') . '</th>';
		$form .= '<tr><td><H2>Добавление фото из альбомов  указанного юзера</H2></td></tr>';
		$form .= '<tr><td>' . t('Метки (разделенные запятыми), присваиваемые добавляемым фото<br />(фото автоматически попадут в соответствующие галереи): ') . '<input type="text" name="f_default_tags" value="' . $options_admin['picasa_default_tags'] . '"></td></tr>';
		$form .= '<tr><td><H3>Доступные альбомы:</H3></td></tr>';
		foreach ($albums as $album)
		{
		  $album_prev = '<strong>' . $album['title'] . '</strong>( guid = ' . $album['albumId'] . ' )'. $album['desc'];
		  $form .= '<tr><td>' . $album_prev . '</td><td><input type="submit" name="f_get_gallery_submit[' . $album['albumId'] . ']" value="Добавить фото из этого альбома" style="margin: 25px 0 5px 0;" /></td></tr>';
		}
    $form .= '</table>';

		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
    echo $form;
		echo '</form>';






function getPicasaAlbumList($user)
{
  $img_size = 200;
	// $rss = "http://picasaweb.google.com/data/feed/api/user/$user/?alt=rss&kind=album&hl=ru&access=public";
  $rss = fetch_rss("http://picasaweb.google.com/data/feed/base/user/".$user."?alt=rss&kind=album&hl=ru&access=public");	
	$rss = $rss->items;
	$albums = array();
	foreach ( $rss as $item )
	{ 	
			$title = $item['title'];
			$title = str_replace("'","",$title);
			$title = str_replace('"',"",$title);
			$album['title'] = $title;
			preg_match('/.*src="(.*?)".*/',$item['description'],$img_src);
			$path = $img_src[1];
			$path = str_replace("s160-","s".$img_size."-",$path);
      $album['thumbnail'] = $path;
      $album['desc'] = $item['description'];
      $guid = stristr($item['guid'], "albumid/");
      $guid = explode('?', $guid);
      $guid = str_replace("albumid/" , "" , $guid[0]);
      $album['albumId'] = $guid;
    //  $album['pubdate'] = getTagContent($tmp, "pubDate");
     // $album['nums'] = getTagContent($tmp, "gphoto:numphotos");
      array_push($albums, $album);
  }
  
	return $albums;
}

function getPicasaPhotos($user, $albumid) {
		$rss = fetch_rss("http://picasaweb.google.com/data/feed/base/user/".$user."/albumid/".$albumid."?alt=rss&kind=photo&hl=ru&access=public");
		$rss = $rss->items;
		$photos = array();
		foreach ( $rss as $item ) { 	
			$title = $item['title'];
			$title = str_replace("'","",$title);
			$title = str_replace('"',"",$title);
			preg_match('/.*src="(.*?)".*/',$item['description'],$img_src);
			$path = $img_src[1];
			$item['path'] = $path;
			$item['title'] = $title;
			$photos[] = $item;
			         
		}
  return $photos;
}

function getPicasaAlbum($user, $albumid) {

	$rss = "http://picasaweb.google.com/data/feed/api/user/$user/albumid/$albumid?kind=photo&alt=rss&access=public&thumbsize=144c";
	$album = array();
	$file = implode('', file($rss));
	$title = getTagContent($file, "title");
	$date = getTagContent($file, "lastBuildDate");
	$nums = getTagContent($file, "openSearch:totalResults");
	
	$album['title'] = $title;
	$album['date'] = $date;
	$album['nums'] = $nums;
	$album['desc'] = getTagContent($file, "description");
	$photos = array();
	$start = strpos($file, "<item>");
	$end = strrpos($file, "</item>");
	$substr = substr($file, $start, $end-$start+1);
	$items = explode("<item>", $substr);
	if(is_array($items) && count($items)>0) {
		foreach($items as $tmp) {
			if(trim($tmp) != "") {
				$title = getTagContent($tmp, "title");
				$photoid = getTagContent($tmp, "gphoto:id");
			//	$mediagroup = getTagContent($tmp, "media:group");
			//	$thumbnail = getTagContent($mediagroup, "media:thumbnail");
				$photo['title'] = $title;
				$photo['photoid'] = $photoid;
				$photo['thumbnail'] = $thumbnail['url'];
				array_push($photos, $photo);
			}
		}
	}
	$album['photos'] = $photos;
	return $album;
}

function getPicasaPhoto($user, $albumid, $photoid) {

	$rss = "http://picasaweb.google.com/data/feed/api/user/$user/albumid/$albumid/photoid/$photoid?alt=rss&thumbsize=144";
	$photo = array();
	$file = implode('', file($rss));
	$photo['title'] = getTagContent($file, "title");
	$photo['date'] = getTagContent($file, "lastBuildDate");
	//$photo['desc'] = getTagContent($file, "description");
	$photo['width'] = getTagContent($file, "gphoto:width");
	$photo['height'] = getTagContent($file, "gphoto:height");
	//$photo['size'] = getTagContent($file, "gphoto:size");
	$mediagroup = getTagContent($file, "media:group");
	$image = getTagContent($mediagroup, "media:content");
	$thumbnail = getTagContent($mediagroup, "media:thumbnail");
	$photo['thumbnail'] = $thumbnail['url'];
	$photo['url'] = $image['url']."?imgmax=720";
	
	return $photo;
}

function getTagContent($src, $tag) {
	$start = mb_strpos($src, "<".$tag.">");// + strlen($tag)+2;
	if($start === false) {
		$start = mb_strpos($src, "<".$tag) + strlen($tag)+1;
		$end = mb_strpos($src, "/>", $start)-1;
		$content = substr($src, $start, $end-$start+1);
		$return = array();
		$tmp = explode(' ', $content);
		
		if(is_array($tmp) && count($tmp)>0) {
			foreach($tmp as $line) {
				if(trim($line)!="") {
					$a = explode("=", $line);
					$return[$a[0]] = str_replace("'", "", trim($a[1]));
				}
			}
		}
	} else {
		$start+= strlen($tag)+2;
		$end = mb_strpos($src, "</".$tag.">")-1;
		$return = substr($src, $start, $end-$start+1);
	}
	return $return;
}

?>