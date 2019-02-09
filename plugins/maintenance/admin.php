<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * (c) http://uncleeugene.net
 * Плагин написан на обломках родного Maxsite'овского "Redirect". 
 * Большая часть кода принадлежит автору оного Redirect'а :)
 */

	$options_key = 'maintenance';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_maint_url', 'f_redirect_url')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['maint_url'] = $post['f_maint_url'];
		$options['redirect_url'] = $post['f_redirect_url'];
		
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Обслуживание') ?></h1>
<p class="info"><?= t('С помощью этого плагина можно временно закрывать любой доступ к сайту, а также можно настроить автоматический редирект главной страницы.') ?></p>
<p class="info"><?= t('Укажите адрес страницы "Закрыто на обслуживание". Если адрес указан, то любое обращение к сайту, кроме админки будет редиректиться туда.') ?></p>

<?php

		$options = mso_get_option($options_key, 'plugins', array());

		if ( !isset($options['maint_url']) ) $options['maint_url'] = '';
		if ( !isset($options['redirect_url']) ) $options['redirect_url'] = '';

		echo '<form method="post">' . mso_form_session('f_session_id');		
		echo '<input name="f_maint_url" value="' .  $options['maint_url'] . '">';		
		echo '<p class="info">' . t('Укажите адрес для автоматического редиректа с главной страницы.') . '</p>';

		echo '<input name="f_redirect_url" value="' .  $options['redirect_url'] . '"><br /><br />';
		
		echo '<button type="submit" name="f_submit" class="i save">' . t('Сохранить') . '</button>';
		echo '</form>';

?>