<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

 // редактирование альбомов

 


	// проверяем входящие данные если было обновление
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем album_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']);
		
		$par['album_title'] = $post['f_title'][$f_id];	
		$par['album_thumb'] = $post['f_thumb'][$f_id];		
			
		$par['album_desc'] = $post['f_desc'][$f_id];
		$f_tags = $post['f_tags'][$f_id];		
		$par['album_slug'] = $post['f_slug'][$f_id];
		$par['album_date'] = $post['f_date'][$f_id];
		
		$par['album_tags'] = mso_explode($post['f_tags'][$f_id], false, false , true);
		$par['album_id'] = $f_id;
		$err = taggallery_edit_album($par);
		
    if (!$err)  echo '<div class="update">Данные альбома номер ' . $par['album_id'] . ' изменены.</div>';
    else echo '<div class="error">' .  $err . '</div>';
	}


	// проверяем входящие данные если было удаление
	if ( $post = mso_check_post(array('f_session_id' , 'f_delete_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_submit']);
		
    $err = taggallery_delete_album(array('album_id' => $f_id));
   
    if (!$err)  echo '<div class="update">Альбом удален.</div>';
    else echo '<div class="error">' .  $err . '</div>';	}



	// проверяем входящие данные если было добавление нового 
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 'f_new_slug', 'f_new_title', 'f_new_desc' , 'f_new_tags' , 'f_new_thumb')) )
	{
		# защита рефера
		mso_checkreferer();

		$par['album_title'] = $post['f_new_title'];		
		$par['album_desc'] = $post['f_new_desc'];
		$f_tags = $post['f_new_tags'];		
		$par['album_slug'] = $post['f_new_slug'];
		$par['album_thumb'] = $post['f_new_thumb'];
		$par['album_tags'] = mso_explode($f_tags, false, false);
		$err = taggallery_add_album($par);
		
    if (!$err)  echo '<div class="update">Альбом создан.</div>';
    else echo '<div class="error">' .  $err . '</div>';
   
	}

	
///////////////////////////////////////////////////////////////////////////////////////////////////////


?>

<h1><?= t('Альбомы') ?></h1>
<p class="info"><?= t('Глереи изображений можно объединять в альбомы') ?></p>
<?php
  
// выводим навигатор по альбомам и галереям_________________________________________________________
    $out = '<table><tr><td>';
    $out .= '<H3>Неразобранные по альбомам:</H3>';
    $gallerys = taggallery_get_gallerys(array('nerazobrannoe' => true, 'hash_tags'=>true)); 
    require($plugin_dir . '/admin/out/gallerys.php');
    $out .= '</td><td>';
    require($plugin_dir . '/admin/out/albums.php');
    $out .= '<H3><a href="' . $plugin_url . 'gallerys/nerazobrannoe">' . 'Неразобранные картинки' . '</a></H3>';
    $out .= '</td></tr></table>';

    echo $out;

  echo  '<H1>Добавление или редактирование альбомов:</H1>'; 

    
$albums = taggallery_get_albums();
 
 
$form = '';

if ($albums)
{
  foreach ($albums as $album)
  {
  
    $tags = '';
    if (isset($album['gallerys']) and  $album['gallerys'])
      foreach ($album['gallerys'] as $album_gallery)
      {
         if ($tags) $tags .= ', ' . $album_gallery['gallery_name'];
         else $tags .= $album_gallery['gallery_name'];
       }
    
		$tags = '<textarea rows="3" name="f_tags[' . $album['album_id'] . ']">'	. $tags . '</textarea>';
		
		$title = '<input type="text" name="f_title[' . $album['album_id'] . ']" value="' . $album['album_title'] . '">';
		$slug = '<input type="text" name="f_slug[' . $album['album_id'] . ']" value="' . $album['album_slug'] . '">';
		$desc = '<input type="text" name="f_desc[' . $album['album_id'] . ']" value="' . $album['album_desc'] . '">';
		$thumb = '<input type="text" name="f_thumb[' . $album['album_id'] . ']" value="' . $album['album_thumb'] . '">';
		$date = '<input type="text" name="f_date[' . $album['album_id'] . ']" value="' . $album['album_date'] . '">';
		$act = '<input type="submit" name="f_edit_submit[' . $album['album_id'] . ']" value="' . t('Изменить') . '">';
		$act2 = '<input type="submit" name="f_delete_submit[' . $album['album_id'] . ']" value="' . t('Удалить альбом') . '">';
		
		$form .= '<H2><strong>' . t('Опции альбома: ') . '</strong>' . $album['album_title'] . '</H2>';
		$form .= $act2;	
		$form .= '<div class="admin_plugin_options">';
		$form .= '<strong>' . t('Название: ') . '</strong>' . $title;
		$form .= '<strong>' . t('Слуг: ') . '</strong>' . $slug;
		$form .= '<strong>' . t('Описание: ') . '</strong>' . $desc;
		$form .= '<strong>' . t('Метки: ') . '</strong>' . $tags;
		$form .= '<strong>' . t('Дата: ') . '</strong>' . $date;
		$form .= '<strong>' . t('URL обложки: ') . '</strong>' . $thumb;
		$form .= $act;	
		$form .= '</div>';
  }

  // метки/галереи не в альбомах
  $gallerys = '';
  //$gallerys = taggalery_get_gallery_not_in_album();
  if ($gallerys)
  { 
    $form .= '<strong>' . t('Неразобранные метки (не учавствуют в выводе): <br/>') . '</strong>';
	  $form .= '<div class="admin_plugin_options">';
	  
	  $tags = '';
    foreach ($gallerys as $gallery)
    {
      if ($tags) $tags .= ', ' . $gallery['gallery_name'];
      else $tags .= $gallery['gallery_name'];
    }
	  
    $form .= $tags;
	  $form .= '</div>';
  }
}
  

	# форма добавления альбома
	$form .= '<H1>' . t('Добавление новго альбома:') . '</H1>';
	
	$title = '<input type="text" name="f_new_title" value="">';

	$slug = '<input type="text" name="f_new_slug" value="">';
	
	$tags = '<textarea rows="10" name="f_new_tags"></textarea>';

	$desc = '<input type="text" name="f_new_desc" value="">';
	
	$thumb = '<input type="text" name="f_new_thumb" value="">';
		
	$act = '<input type="submit" name="f_new_submit" value="' . t('Добавить новый альбом') . '">';
		
	$form .= '<div class="admin_plugin_options">';
	$form .= '<strong>' . t('Название: ') . '</strong>' . $title;
	$form .= '<strong>' . t('Слуг: ') . '</strong>' . $slug;
	$form .= '<strong>' . t('Описание: ') . '</strong>' . $desc;
	$form .= '<strong>' . t('Метки: ') . '</strong>' . $tags;
	$form .= '<strong>' . t('URL обложки: ') . '</strong>' . $thumb;

	$form .= '</div>';
	$form .= $act;		

	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form; // вывод подготовленной формы
	echo '</form>'; 

  


?>