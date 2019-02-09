<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

?>

<h1><?= t('Загрузка картинок и фото на сайт', __FILE__) ?></h1>
<p class="info"><?= t('Панель управления плагином.', __FILE__) ?></p>

<div class="admin-h-menu">
<?php
	$plugin_url = getinfo('site_admin_url') . 'dignity_storage';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select');
	echo $a;
?>
</div>

<?php

$CI = & get_instance();
	
$options_key = 'plugin_dignity_storage';
	
if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
{
	mso_checkreferer();
	
	$options = array();
	$options['header'] = $post['f_header'];
	$options['slug'] = $post['f_slug'];
	$options['allowed_types'] = $post['f_allowed_types'];
	
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['header']) ) $options['header'] = t('Хранилище', __FILE__);
if ( !isset($options['slug']) ) $options['slug'] = 'storage';
if ( !isset($options['allowed_types']) ) $options['allowed_types'] = 'gif|jpg|jpeg|png'; 

$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
$form .= '<h2>' . t('Настройки', __FILE__) . '</h2>';
$form .= '<p>' . t('Заголовок страницы:', 'plugins') . ' <input name="f_header" type="text" value="' . $options['header'] . '" style="width:50%"></p>';
$form .= '<p>' . t('Коротка ссылка:', 'plugins') . ' <input name="f_slug" type="text" value="' . $options['slug'] . '"></p>';
$form .= '<p>' . t('Разрешенные типы файлов:', 'plugins') . ' <input name="f_allowed_types" type="text" value="' . $options['allowed_types'] . '"></p>';

$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить', 'plugins') . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';

echo $form;

#end of file