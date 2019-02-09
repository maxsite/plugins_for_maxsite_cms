<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS plugin
 * (c) http://max-3000.com/
 * (c) http://filimonov.com.ua
*/

  $options = mso_get_option('custom_tagclouds' , 'plugins', array());
  if (!isset($options['tags'])) $options['tags'] = array();
	require_once( getinfo('common_dir') . 'meta.php' ); // функции мета
	$all_tags = mso_get_all_tags_page(); // все метки в систее

	
	// сохранить изменения
	if ( $post = mso_check_post(array('f_session_id', 'f_check_tags', 'f_confirm_submit')) )
	{
		mso_checkreferer();
    $tag_array = array();
		foreach ($post['f_check_tags'] as $tag)
		{
      $tag_array[$tag] = $all_tags[$tag];
		}
		$options['tags'] = $tag_array;
    mso_add_option('custom_tagclouds', $options , 'plugins');
		echo '<div class="update">' . t('Выполнено', 'admin') . '</div>';
	}

?>
<h1><?= t('Настраиваемое облако меток', 'plugins')?></h1>

<p class="info"><?= t('Можно выбрать метки, которые будут затем учавствовать в облвке меток', 'plugins') ?></p>

<?php

	$CI = & get_instance();
	$CI->load->library('table');
	$CI->load->helper('form');
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="110">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );
			  
	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_heading(t('Метка', 'admin'), t('Кол-во', 'admin'));
			
	foreach ($all_tags as $tag => $count)
	{
	  $tag_key = mso_slug($tag);
	  if (array_key_exists($tag , $options['tags'])) $checkid = true;
	  else $checkid = false;
		$check_tag = form_checkbox('f_check_tags[]', $tag, $checkid,
			'title="' . $tag . '" id="' . $tag_key . '" class="f_check_files"')
			. '<label for="' . $tag_key
			. '"> '
			. $tag . '</label>';					  
		$CI->table->add_row($check_tag, $count);
  }			
  
    // Форма ввода
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $CI->table->generate(); // вывод подготовленной таблицы
		
		echo '<p class="br"><input type="submit" name="f_confirm_submit" value="' . t('Подтвердить', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';

		echo '</form>';		

?>