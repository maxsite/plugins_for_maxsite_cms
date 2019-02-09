<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$options_key = 'plugin_wm_control';
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) ){
		$options = array();
		if (!isset($post['enable_check'] )){
			$options['enable_check'] = null;
		}else{
			$options['enable_check'] = $post['enable_check'];
		}
		if (!isset($post['enable_merchant'])){
			$options['enable_merchant'] = null;
		}else{
			$options['enable_merchant'] = $post['enable_merchant'];
		}

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

	$options = mso_get_option($options_key, 'plugins', array());

	if (!isset($options['enable_check'] )){
		$options['enable_check'] = null;
	}else{
		$options['enable_check'] = 'checked="checked"';
	}
	if (!isset($options['enable_merchant'] )){
		$options['enable_merchant'] = null;
	}else{
		$options['enable_merchant'] = 'checked="checked"';
	}

	$form  = '<p><strong><u><div class="t250">'.t('Включить выписку счетов', __FILE__).'</div></u></strong> ';
	$form .= '<input name="enable_check" type="checkbox" '.$options['enable_check'].'></p><br>';

	$form .= '<p><strong><u><div class="t250">'.t('Включить Мерчант', __FILE__).'</div></u></strong> ';
	$form .= '<input name="enable_merchant" type="checkbox" '.$options['enable_merchant'].'></p>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';
?>