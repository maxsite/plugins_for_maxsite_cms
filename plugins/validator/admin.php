<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	global $MSO;
	$CI = & get_instance();
	$optionb_key = 'plugin_validator';
	
	if ( $post = mso_check_post(array('f_session_id','f_submit','f_class')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['content_at'] = isset($post['v_content_auto_tag']) ? 1 : 0;
		$options['content_bt'] = isset($post['v_content_balance_tags']) ? 1 : 0;
		$options['noindex'] = isset($post['v_noindex']) ? 1 : 0;
		$options['noindex_class'] = $post['f_class'];

		mso_add_option($optionb_key, $options, 'plugins');
		echo '<div class="update">Обновлено!</div>';
	}
?>
<h1 style="margin-bottom:10px">Настройки Валидатора</h1>
<?php
	$options = mso_get_option($optionb_key, 'plugins', array());
	if ( !isset($options['content_at']) ) $options['content_at'] = false;
	if ( !isset($options['content_bt']) ) $options['content_bt'] = false;
	if ( !isset($options['noindex']) ) $options['noindex'] = false;
	if ( !isset($options['noindex_class']) ) $options['noindex_class'] = '';


	$checked_content_at = $options['content_at'] ? ' checked="checked" ' : '';
	$checked_content_bt = $options['content_bt'] ? ' checked="checked" ' : '';
	$checked_noindex = $options['noindex'] ? ' checked="checked" ' : '';

	$form = '<h2>Авторасстановка тегов</h2>';
	$form .= '<p><label><input name="v_content_auto_tag" type="checkbox"'.$checked_content_at.'> отключить mso_auto_tag()</label></p>';
	$form .= '<p style="padding-bottom:10px;border-bottom:1px #CCC solid"><label><input name="v_content_balance_tags" type="checkbox"'.$checked_content_bt.'> отключить mso_balance_tags()</label></p>';
	$form .= '<h2 style="margin-top:10px">Валидация noindex</h2>
	<p><label><input name="v_noindex" type="checkbox"'.$checked_noindex.'> включить</label></p>';
	$form .= '<p><label><input name="f_class" type="text" value="'.$options['noindex_class'].'"> CSS class содержащий display:none</label></p>';
	$form .= '<p style="margin-top:-1px;color:#AAA">если пусто — то будет использоваться display:none</label></p>';

	echo '<form action="" method="post">'.mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
	echo '</form>';
?>