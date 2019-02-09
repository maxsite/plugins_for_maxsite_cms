<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 */

	$CI = & get_instance();
	
	$options_key = 'plugin_dignity_recommend';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['header'] = $post['f_header'];
		$options['textdo'] = $post['f_textdo'];
		$options['textposle'] = $post['f_textposle'];
		$options['slug'] = $post['f_slug'];
	
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
	}
	
?>
<h1><?= t('Рекомендовать', __FILE__) ?></h1>
<p class="info"><?= t('Панель управления плагином.', __FILE__) ?></p>

<?php
		// Проверяем опции
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['header']) ) $options['header'] = t('Рекомендовать', __FILE__); 
		if ( !isset($options['textdo']) ) $options['textdo'] = ''; 
		if ( !isset($options['textposle']) ) $options['textposle'] = ''; 
		if ( !isset($options['slug']) ) $options['slug'] = 'recommend';

		// Выводим форму
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		$form = '';

		$form .= '<h2>' . t('Настройки', __FILE__) . '</h2>';

		$form .= '<p><strong>' . t('Коротка ссылка:', __FILE__) . '</strong><br> ' . ' <input name="f_slug" type="text" value="' . $options['slug'] . '" style="width:60%"></p>';
		
		$form .= '<p><strong>' . t('Заголовок страницы:', __FILE__) . '</strong><br> ' . ' <input name="f_header" type="text" value="' . $options['header'] . '" style="width:60%" maxlength="70"></p>';

		$form .= '<p><strong>' . t('Текст вначале страницы:', __FILE__) . '</strong><br> ' . '<textarea name="f_textdo" cols="90" rows="10">' . $options['textdo'] . '</textarea></p>';

		$form .= '<p><strong>' . t('Текст в конце страницы:', __FILE__) . '</strong><br> ' . '<textarea name="f_textposle" cols="90" rows="10">' . $options['textposle'] . '</textarea></p>';

		echo $form;

		echo '<input type="submit" name="f_submit" value="' . t('Сохранить', __FILE__) . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>