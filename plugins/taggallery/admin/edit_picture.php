<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

 // редактирование атрибутов картинки

  $picture_id = mso_segment(4);

	// проверяем входящие данные если было обновление
	if ( $post = mso_check_post(array('f_session_id', 'f_picture_title' , 'f_picture_desc' , 'f_picture_content' , 'f_picture_date' , 'f_picture_date_file' , 'f_picture_date_photo' , 'f_picture_mini_url' , 'f_picture_url' , 'f_picture_full_size_url' , 'f_edit_picture_submit_' . $picture_id)) )
	{
		# защита рефера
		mso_checkreferer();
    
    $par = array();
		$par['picture_id'] = $picture_id;	
		$par['picture_title'] = $post['f_picture_title'];	
		$par['picture_desc'] = $post['f_picture_desc'];		
			
		$par['picture_content'] = $post['f_picture_content'];
		$par['picture_date'] = $post['f_picture_date'];
		$par['picture_date_file'] = $post['f_picture_date_file'];
		$par['picture_date_photo'] = $post['f_picture_date_photo'];
		
		$par['picture_url'] = $post['f_picture_url'];
		$par['picture_mini_url'] = $post['f_picture_mini_url'];
		$par['picture_full_size_url'] = $post['f_picture_full_size_url'];

		$res = taggallery_add_picture($par);
		
    if (!$res['errors'])  echo '<div class="update">Данные картинки изменены.</div>';
    else echo '<div class="error">' .  $res['errors'] . '</div>';
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////


?>

<h1><?= t('Картинка') ?></h1>
<p class="info"><?= t('Редактирование атрибутов картинки') ?></p>

<?php
  
$picture_id = mso_segment(4);
$pag = false;   
$pictures = taggallery_get_pictures(array('picture_id' => $picture_id) , $pag);
if (isset($pictures[0]))  
{
  $picture = $pictures[0];

  // выводим форму редактирования атрибутов галереи
  echo '<form action="" method="post">' . mso_form_session('f_session_id');

	echo '<div class="admin_plugin_options">';
	echo '<table><tr><td>';
   echo '<H2>' . $picture['picture_title'] . '</H3>';
   echo '<H2>Id : ' . $picture['picture_id'] . '</H3>';
   echo '<H3>Слуг : ' . $picture['picture_slug'] . '</H3>';
   // ссылка на страницу директории этой картинки
   echo '<p>dir: <a href="' . $admin_url . 'taggallery/' . $picture['picture_dir'] . '" title="Каталог изображения" >' . '/uploads/' . $picture['picture_dir'] . '</a></p>';
   echo '<p>file: ' . $picture['picture_file'] . '</p>';
   echo '</td><td>';
   
   // миниатюра 
   if ($picture['picture_mini_url'])
   {
       $mini_url = $picture['picture_mini_url'];
       $url = $picture['picture_url'];
   }    
   else
   {
      $mini_url = $uploads_url . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
      $mini_dir = $uploads_dir . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
      $url = $uploads_url . $picture['picture_dir'] . $picture['picture_file'];
      if (!file_exists($mini_dir)) $mini_url = $url;
   }
   $prev = '<img src="' . $mini_url . '">'; 
  
   $prev = '<a class="lightbox" href="' . $url . '">' . $prev . '</a>';
  
   echo $prev; 
     
   echo '</td><td>';  
 
   echo '<a href="' . $siteurl . $options['picture_slug'] . '/' .  $options['picture_prefix'] .$picture['picture_slug'] . '" title="Просмотр страницы картинки" >Страница картинки</a><br/>'; 
   
   echo '<H3>Галереи картинки:</H3>';
   $gallerys = taggallery_get_gallerys(array('picture_id' => $picture['picture_id']));
   $out = '';
   require($plugin_dir . '/admin/out/gallerys.php');
   echo $out;
   
   echo '</td></tr></table>';    
	 echo '</div>';

	 echo '<div class="admin_plugin_options">';

         
   echo '<strong>' . t('Заголовок: ') . '</strong>' . '<input type="text" name="f_picture_title" value="' . $picture['picture_title'] . '">';
   echo '<strong>' . t('Дата добавления: ') . '</strong>' . '<input type="text" name="f_picture_date" value="' . $picture['picture_date'] . '">';
   echo '<strong>' . t('Дата файла: ') . '</strong>' . '<input type="text" name="f_picture_date_file" value="' . $picture['picture_date_file'] . '">';
   echo '<strong>' . t('Дата фото: ') . '</strong>' . '<input type="text" name="f_picture_date_photo" value="' . $picture['picture_date_photo'] . '">';
   echo '<strong>' . t('Описание: ') . '</strong>' . '<textarea rows="3" name="f_picture_desc">'  . $picture['picture_desc'] . '</textarea>';
   echo '<strong>' . t('Контент: ') . '</strong>' . '<textarea rows="10" name="f_picture_content">' . $picture['picture_content'] . '</textarea>'; 
   
   echo '<strong>' . t('mini url: ') . '</strong>' . '<input type="text" name="f_picture_mini_url" value="' . $picture['picture_mini_url'] . '">';
   echo '<strong>' . t('url: ') . '</strong>' . '<input type="text" name="f_picture_url" value="' . $picture['picture_url'] . '">';
   echo '<strong>' . t('full size url: ') . '</strong>' . '<input type="text" name="f_picture_full_size_url" value="' . $picture['picture_full_size_url'] . '">';
    
   echo '<p><input type="submit" name="f_edit_picture_submit_'. $picture_id . '" value="' . t('Именить атрибуты картинки', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';
	echo '</div></form>';

}
else
{
  echo '<div class="error">Нет картинки с таким id.</div>';
}
?>



    