<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

 // редактирование разделов: форумов и категорий
 
require($plugin_dir . 'functions/access_db.php');
require($plugin_dir . 'functions/modify_db.php');

 // форумы ____________________________________________________________________

	// проверяем входящие данные если было обновление
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_forum_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_forum_submit']);
		
		$par['forum_title'] = $post['f_title'][$f_id];	
		$par['forum_desc'] = $post['f_desc'][$f_id];
		$par['forum_order'] = $post['f_order'][$f_id];
		
		$par['forum_id'] = $f_id;
		$err = dialog_edit_forum($par);
		
    if (!$err)  echo '<div class="update">Данные форума номер ' . $par['forum_id'] . ' изменены.</div>';
    else echo '<div class="error">' .  $err . '</div>';
	}


	// проверяем входящие данные если было удаление форума
	if ( $post = mso_check_post(array('f_session_id' , 'f_delete_forum_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_forum_submit']);
		
    $err = dialog_delete_forum($f_id);
   
    if (!$err)  echo '<div class="update">Форум удален.</div>';
    else echo '<div class="error">' .  $err . '</div>';	
  }


	// проверяем входящие данные если было добавление нового форума
	if ( $post = mso_check_post(array('f_session_id', 'f_new_forum_submit', 'f_new_order', 'f_new_title', 'f_new_desc')) )
	{
		# защита рефера
		mso_checkreferer();

		$par['forum_title'] = $post['f_new_title'];		
		$par['forum_desc'] = $post['f_new_desc'];
		$par['forum_order'] = $post['f_new_order'];
		$err = dialog_add_forum($par);
		
    if (!$err)  echo '<div class="update">Форум создан.</div>';
    else echo '<div class="error">' .  $err . '</div>';
	}

 // категории ____________________________________________________________________

	// проверяем входящие данные если было обновление категории
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_cat_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем forum_id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_cat_submit']);
		
		$par['category_title'] = $post['f_cat_title'][$f_id];	
		$par['category_desc'] = $post['f_cat_desc'][$f_id];
		$par['category_order'] = $post['f_cat_order'][$f_id];
		$par['category_slug'] = $post['f_cat_slug'][$f_id];
		$par['category_forum_id'] = $post['f_cat_forum_id'][$f_id];
		
		$par['category_id'] = $f_id;
		$err = dialog_edit_category($par);
		
    if (!$err)  echo '<div class="update">Данные категории номер ' . $par['category_id'] . ' изменены.</div>';
    else echo '<div class="error">' .  $err . '</div>';
	}

/*
	// проверяем входящие данные если было удаление категории
	if ( $post = mso_check_post(array('f_session_id' , 'f_delete_cat_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_cat_submit']);
		
    $err = dialog_delete_forum($f_id);
   
    if (!$err)  echo '<div class="update"Категория удалена.</div>';
    else echo '<div class="error">' .  $err . '</div>';	
  }
*/


	// проверяем входящие данные если было добавление категории
	if ( $post = mso_check_post(array('f_session_id', 'f_new_cat_submit', 'f_new_cat_order', 'f_new_cat_title', 'f_new_cat_desc', 'f_new_cat_slug')) )
	{
		# защита рефера
		mso_checkreferer();
		$par['category_title'] = $post['f_new_cat_title'];	
		$par['category_desc'] = $post['f_new_cat_desc'];
		$par['category_order'] = $post['f_new_cat_order'];
		$par['category_slug'] = $post['f_new_cat_slug'];
		$par['category_forum_id'] = $post['f_new_cat_forum_id'];
		$err = dialog_add_category($par);
		
    if (!$err)  echo '<div class="update">Категория созданна.</div>';
    else echo '<div class="error">' .  $err . '</div>';
	}
	
///////////////////////////////////////////////////////////////////////////////////////////////////////


?>

<h1><?= t('Форумы и категории') ?></h1>
<?php


$forums = dialog_get_forums();
 
$form = '';

if ($forums)
{
  foreach ($forums as $forum)
  {
		$form .= '<div>';
    
    if ($forum['forum_id'])
		{ 
		  $form .= '<div class="admin_plugin_options">';
		
		  $form .= '<H2>' . t('Форум') . ' Id: <strong>' . $forum['forum_id'] . '</strong></H2>';
		  $title = '<input type="text" name="f_title[' . $forum['forum_id'] . ']" value="' . $forum['forum_title'] . '">';
		  $desc = '<input type="text" name="f_desc[' . $forum['forum_id'] . ']" value="' . $forum['forum_desc'] . '">';
		  $order = '<input type="text" name="f_order[' . $forum['forum_id'] . ']" value="' . $forum['forum_order'] . '">';
		
		  $form .= '<input type="submit" name="f_edit_forum_submit[' . $forum['forum_id'] . ']" value="' . t('Изменить форум') . '">';
		 
		  $form .= '<strong>' . t('Название') . ' :</strong>' . $title;
		  $form .= '<strong>' . t('Описание') . ' :</strong>' . $desc;
		  $form .= '<strong>' . t('Порядок') . ' :</strong>' . $order;
		  $form .= '<input type="submit" name="f_delete_forum_submit[' . $forum['forum_id'] . ']" value="' . t('Удалить форум') . '">';
		  
		}
		else
		{
		  if (!$forum['categorys']) continue;
		  $form .= '<H2>' . $forum['forum_title'] . '</H2>';
		  $form .= '<p>' . $forum['forum_desc'] . '</p>';
		}
		
		$form .= '</div>';
		
		$form .= '<H3>' . t('Категории форума') . '</H3>';
		
		if ($forum['categorys'])
		  foreach ($forum['categorys'] as $category)
		  {
		    $form .= '<p><strong>' . $category['category_id'] . ' :</strong>';
		    $form .= ' title<input type="text" name="f_cat_title[' . $category['category_id'] . ']" value="' . $category['category_title'] . '">';
		    $form .= ' desc<input type="text" name="f_cat_desc[' . $category['category_id'] . ']" value="' . $category['category_desc'] . '">';
		    $form .= ' slug<input type="text" name="f_cat_slug[' . $category['category_id'] . ']" value="' . $category['category_slug'] . '">';
		    $form .= ' forum_id<input type="text" name="f_cat_forum_id[' . $category['category_id'] . ']" value="' . $category['category_forum_id'] . '">';
		    $form .= ' order<input type="text" name="f_cat_order[' . $category['category_id'] . ']" value="' . $category['category_order'] . '">';
		    $form .= '<input type="submit" name="f_edit_cat_submit[' . $category['category_id'] . ']" value="' . t('Изменить') . '">';
		    $form .= '<input type="submit" name="f_delete_cat_submit[' . $category['category_id'] . ']" value="' . t('Удалить') . '"></p>';
		  }
		
		$form .= '</div>';
  }




	# форма добавления категории
	$form .= '<div class="admin_plugin_options">';

	$form .= '<H1>' . t('Добавление новой категории') . ':</H1>';
		
	$form .= '<strong>' . t('Название') . ' :</strong><input type="text" name="f_new_cat_title" value="">';
	$form .= '<strong>' . t('Описание') . ' :</strong><input type="text" name="f_new_cat_desc" value="">';
	$form .= '<strong>' . t('Слуг') . ' :</strong><input type="text" name="f_new_cat_slug" value="">';
	$form .= '<strong>' . t('Порядок') . ' :</strong><input type="text" name="f_new_cat_order" value="">';
	$form .= '<strong>' . t('Id форума') . ' :</strong><input type="text" name="f_new_cat_forum_id" value="">';
	
	$form .= '<input type="submit" name="f_new_cat_submit" value="' . t('Добавить новую категорию') . '">';
	$form .= '</div>';

}
	# форма добавления форума
	$form .= '<div class="admin_plugin_options">';

	$form .= '<H1>' . t('Добавление новго форума') . ':</H1>';
		
	$form .= '<strong>' . t('Название') . ' :</strong><input type="text" name="f_new_title" value="">';
	$form .= '<strong>' . t('Описание') . ' :</strong><input type="text" name="f_new_desc" value="">';
	$form .= '<strong>' . t('Порядок') . ' :</strong><input type="text" name="f_new_order" value="">';

	$form .= '<input type="submit" name="f_new_forum_submit" value="' . t('Добавить новый форум') . '">';
	$form .= '</div>';

	
	

	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form; // вывод подготовленной формы
	echo '</form>'; 

  


?>