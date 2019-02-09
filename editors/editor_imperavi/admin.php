<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$options_key = 'editor_imperavi';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_init')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['init'] = $post['f_init'];
		
		mso_add_option($options_key, $options, 'plugins' );
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
?>
<h1><?= t('Редиректы') ?></h1>
<p class="info"><?= t('Укажите настройки для вызова редактора.<a href="http://imperavi.com/redactor/docs/">Документация</a>') ?></p>
<p class="info"><?= t('По-умолчанию:') ?></p>
<pre>$(document).ready(function(){$('#f_content').redactor({imageUpload: '/editor_imperavi_uploader',imageGetJson: false });});</pre><br>



<?php

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['init']) ) $options['init'] = '$(document).ready(function(){$(\'#f_content\').redactor({imageUpload: \'/editor_imperavi_uploader\' });});';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<textarea name="f_init" style="width: 100%; height: 300px;">' .  $options['init'] . '</textarea>';
		
		echo '<br><br><input type="submit" name="f_submit" value="' . t('Сохранить изменения') . '">';
		echo '</form>';

?>