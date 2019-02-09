<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 */

 ?>
 
 <h1><?= t('Плагин Ecwid', __FILE__) ?></h1>

<p class="info"><?= t('Для того чтобы использовать плагин, вам нужно пройти бесплатную регистрацию на сайте <a href="https://my.ecwid.com/cp?lang=ru#register" target="_blank">Ecwid</a>.', __FILE__) ?></p>

<p class="info"><?= t('<a href="https://my.ecwid.com/cp/?lang=ru/" target="_blank">Перейти в панель управления Ecwid</a>', __FILE__) ?> | <?= t('<a href="http://ecwidru.pbworks.com/w/page/38368750/FrontPage/" target="_blank">База знаний Ecwid</a>', __FILE__) ?> | <?= t('<a href="http://www.ecwid.com/forums/forumdisplay.php?f=10" target="_blank">Форум Ecwid</a>', __FILE__) ?></p>

<?php

	$CI = & get_instance();
	
	$options_key = 'plugin_ecwid';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['header'] = $post['f_header'];
		$options['slug'] = $post['f_slug'];
		$options['textdo'] = $post['f_textdo'];
		$options['textposle'] = $post['f_textposle'];
		$options['ecwid_storeid'] = $post['f_ecwid_storeid'];
		$options['ecwid_catperrow'] = $post['f_ecwid_catperrow'];
		$options['ecwid_productpercolumn'] = $post['f_ecwid_productpercolumn'];
		$options['ecwid_productsperrow'] = $post['f_ecwid_productsperrow'];
		$options['ecwid_productsperpage'] = $post['f_ecwid_productsperpage'];
		$options['ecwid_productsperpagetable'] = $post['f_ecwid_productsperpagetable'];
		$options['ecwid_show_search'] = isset($post['f_ecwid_show_search']) ? 1 : 0;
		$options['ecwid_show_category'] = isset($post['f_ecwid_show_category']) ? 1 : 0;
		$options['ecwid_show_minicart'] = isset($post['f_ecwid_show_minicart']) ? 1 : 0;
		$options['ecwid_show_sidebar'] = isset($post['f_ecwid_show_sidebar']) ? 1 : 0;
	
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['header']) ) $options['header'] = t('Интернет-Магазин', __FILE__); 
	if ( !isset($options['slug']) ) $options['slug'] = 'shop'; 
	if ( !isset($options['textdo']) ) $options['textdo'] = ''; 
	if ( !isset($options['textposle']) ) $options['textposle'] = ''; 
	if ( !isset($options['ecwid_storeid']) ) $options['ecwid_storeid'] = '1003'; 
	if ( !isset($options['ecwid_catperrow']) ) $options['ecwid_catperrow'] = '3';
	if ( !isset($options['ecwid_productpercolumn']) ) $options['ecwid_productpercolumn'] = '3';
	if ( !isset($options['ecwid_productsperrow']) ) $options['ecwid_productsperrow'] = '3';
	if ( !isset($options['ecwid_productsperpage']) ) $options['ecwid_productsperpage'] = '10';
	if ( !isset($options['ecwid_productsperpagetable']) ) $options['ecwid_productsperpagetable'] = '20';
	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	$form = '';

	$form .= '<h2>' . t('Основные настройки плагина', 'plugins') . '</h2>';

	$form .= '<p><strong>' . t('Заголовок страницы:', 'plugins') . '</strong><br>' . ' <input name="f_header" type="text" value="' . $options['header'] . '" style="width:50%"></p>';

	$form .= '<p><strong>' . t('ID Магазина:', 'plugins') . '</strong><br>' . t('Можно узнать в панели управления Ecwid (вкладка "Консоль").', 'plugins') . '</p><p>' . ' <input name="f_ecwid_storeid" type="text" value="' . $options['ecwid_storeid'] . '"></p>';

	$form .= '<p><strong>' . t('Коротка ссылка:', 'plugins') . '</strong><br>' . getinfo('siteurl') . $options['slug'] . '</p><p>' . ' <input name="f_slug" type="text" value="' . $options['slug'] . '"></p>';

	$form .= '<p><strong>' . t('Текст вначале страницы:', 'plugins') . '</strong><br> ' . '<textarea name="f_textdo" style="width:100%">' . $options['textdo'] . '</textarea></p>';

	$form .= '<p><strong>' . t('Текст в конце страницы:', 'plugins') . '</strong><br> ' . '<textarea name="f_textposle" style="width:100%">' . $options['textposle'] . '</textarea></p>';

	$form .= '<h2>' . t('Внешний вид', 'plugins') . '</h2>';

	$chckout = ''; 
	if (!isset($options['ecwid_show_search']))  $options['ecwid_show_search'] = true;
	if ( (bool)$options['ecwid_show_search'] )
	{
		$chckout = 'checked="true"';
	} 
	$form .= '<p>' . t('Включить поиск?', __FILE__)
	. ' <input name="f_ecwid_show_search" type="checkbox" ' . $chckout . '></p>';
	
	$chckout = ''; 
	if (!isset($options['ecwid_show_category']))  $options['ecwid_show_category'] = true;
	if ( (bool)$options['ecwid_show_category'] )
	{
		$chckout = 'checked="true"';
	} 
	$form .= '<p>' . t('Включить горизонтальное меню категорий?', __FILE__)
	. ' <input name="f_ecwid_show_category" type="checkbox" ' . $chckout . '></p>';

	$chckout = ''; 
	if (!isset($options['ecwid_show_minicart']))  $options['ecwid_show_minicart'] = true;
	if ( (bool)$options['ecwid_show_minicart'] )
	{
		$chckout = 'checked="true"';
	} 
	$form .= '<p>' . t('Прекрепить корзину к горизонтальному меню категорий? (необходимо включить опцию выше)', __FILE__)
	. ' <input name="f_ecwid_show_minicart" type="checkbox" ' . $chckout . '></p>';
	
	$chckout = ''; 
	if (!isset($options['ecwid_show_sidebar']))  $options['ecwid_show_sidebar'] = true;
	if ( (bool)$options['ecwid_show_sidebar'] )
	{
		$chckout = 'checked="true"';
	} 
	$form .= '<p>' . t('Скрыть сайтбары?', __FILE__)
	. ' <input name="f_ecwid_show_sidebar" type="checkbox" ' . $chckout . '></p>';

	$form .= '<p><strong>' . t('Количество категорий горизонтально:', 'plugins') . '</strong><br>' . ' <input name="f_ecwid_catperrow" type="text" value="' . $options['ecwid_catperrow'] . '"></p>';

	$form .= '<p><strong>' . t('Количество товаров вертикально (режим сетки) - max. 3:', 'plugins') . '</strong><br>' . ' <input name="f_ecwid_productpercolumn" type="text" value="' . $options['ecwid_productpercolumn'] . '"></p>';

	$form .= '<p><strong>' . t('Количество товаров горизонтально (режим сетки) - max. 3:', 'plugins') . '</strong><br>' . ' <input name="f_ecwid_productsperrow" type="text" value="' . $options['ecwid_productsperrow'] . '"></p>';

	$form .= '<p><strong>' . t('Количество товаров на странице (режим списка):', 'plugins') . '</strong><br>' . ' <input name="f_ecwid_productsperpage" type="text" value="' . $options['ecwid_productsperpage'] . '"></p>';

	$form .= '<p><strong>' . t('Количество товаров на странице (режим таблица):', 'plugins') . '</strong><br>' . ' <input name="f_ecwid_productsperpagetable" type="text" value="' . $options['ecwid_productsperpagetable'] . '"></p>';

	echo $form;

	echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

#end of file
