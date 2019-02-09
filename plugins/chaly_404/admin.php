<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/* 

Файл является неотъемлемой частью плагина chaly_404
Создано для использования в Maxsite CMS http://max-3000.com
Разработано авторским коллективом разработчиков студии "Чалый со товарищи" 
http://ЧалыйСоТоварищи.РФ http://ChalyComrades.com

*/

	$options_key = 'chaly_404';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_all')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['all'] = $post['f_all'];
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Заголовки HTTP', 'plugins') ?></h1>
<p class="info"><?= t('Плагин задаёт верные заголовки по указанному ниже шаблону. Например так:', 'plugins') ?></p>
<pre>category/*/next/*</pre><br>
<pre>page/*</pre><br>
<p class="info"><?= t('Последнюю строку следует сделать пустой, чтобы отдавать правильный заголовок на главной.', 'plugins') ?></p>

<?php

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['all']) ) $options['all'] = '';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<textarea name="f_all" style="width: 100%; height: 300px;">' .  $options['all'] . '</textarea>';
		echo '<br><br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '">';
		echo '</form>';

?>