<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//	global $MSO;
	$CI = & get_instance();
	
	$options_key = 'abc_catalog';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$options = array();
		$options['catalog_slug'] = $post['f_catalog_slug'];
    $options['full_text']  = isset($post['f_full_text']) ? TRUE : FALSE;
    $options['categories'] = $post['f_categories'];
    $options['exclude_page_id'] = $post['f_exclude_page_id'];
    $options['type'] = $post['f_type'];

    $options['catalog_name'] = $post['f_catalog_name'];
    $options['category_id'] = $post['f_category_id'];
	
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('ABC_catalog', 'plugins') ?></h1>
<p class="info"><?= t('Плагин для группировки страниц выбранного типа в алфавитный каталог по заданному адресу', 'plugins') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['catalog_slug']) ) $options['catalog_slug'] = 'catalog'; 
	
		if ( !isset($options['categories']) ) $options['categories'] = ''; 
		if ( !isset($options['exclude_page_id']) ) $options['exclude_page_id'] = ''; 
		if ( !isset($options['type']) ) $options['type'] = 'blog'; 

    if ( !isset($options['catalog_name']) ) $options['catalog_name'] = 'Статьи';
    if ( !isset($options['category_id']) ) $options['category_id'] = ''; 
     $chckout = '';     
     if ( !isset($options['full_text']) OR (bool)$options['full_text'] )
        {
            $chckout = 'checked="true"';
        } 
        
		$form = '';
		$form .= '<th>' . t('Настройки', 'plugins') . '</th>';
		$form .= '<tr><td><strong>' . t('Slug для каталога', 'plugins') . '</strong> </td>' . '<td> <input name="f_catalog_slug" type="text" value="' . $options['catalog_slug'] . '"></td></tr>';
    $form .= '<tr><td><strong>' . t('Выводить полные страницы', 'plugins') . '</strong> </td>' . '<td> <input name="f_full_text" type="checkbox" ' . $chckout . '"></td></tr>';
    $form .= '<tr><td><strong>' . t('Выводимые категории (если оставить пустым - все)', 'plugins') . '</strong> </td>' . '<td> <input name="f_categories" type="text" value="' . $options['categories'] . '"></td></tr>';
    $form .= '<tr><td><strong>' . t('Исключаемые страницы', 'plugins') . '</strong> </td>' . '<td> <input name="f_exclude_page_id" type="text" value="' . $options['exclude_page_id'] . '"></td></tr>';    
    $form .= '<tr><td><strong>' . t('Тип страниц для каталога', 'plugins') . '</strong> </td>' . '<td> <input name="f_type" type="text" value="' . $options['type'] . '"></td></tr>';

    $form .= '<tr><td><strong>' . t('Имя сущности для заголовка каталога', 'plugins') . '</strong> </td>' . '<td> <input name="f_catalog_name" type="text" value="' . $options['catalog_name'] . '"></td></tr>';        
    $form .= '<tr><td><strong>' . t('Номер рубрики для навигации', 'plugins') . '</strong> </td>' . '<td> <input name="f_category_id" type="text" value="' . $options['category_id'] . '"></td></tr>'; 		echo '<form action="" method="post">' . mso_form_session('f_session_id');
    
		echo '<table>';
        echo $form;
        echo '</table>';
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>