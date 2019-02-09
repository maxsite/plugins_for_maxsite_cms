<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

	global $MSO;
	$CI = & get_instance();
	
	$options_key = 'adscut';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_ushko')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['ushko'] = $post['f_ushko'];
		
		
		$options['start'] = isset($post['f_start']) ? 1 : 0;

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">Настройки обновлены!</div>';
	}
	
?>
<h1>Настройка AdsCUT</h1>
<p><strong>Создайте новое ушко и назовите его, к примеру, "googleads"</strong></p>
<p><strong>Разместите java-скрипт кода вызова рекламы <a href=https://www.google.com/adsense>Google-adsense</a> или <a href=http://profit-project.ru/?r=1213758012>Яндекс-директа</a> в этом ушке</strong></p>
<p><strong>Укажите в настройках плагина имя ушка с кодом который будет выводиться сразу после [cut] из текста статьи</strong></p>
<p><strong>Поставте галочку "включить плагин" и нажмите "Сохранить изменения"</strong></p>
<p><strong>Готово! Через несколько минут вы сможете увидеть рекламные объявления в тексте статьи</strong></p>

<p>&nbsp</p>

<p><strong>Вопросы? Пишите мне avishgreen@gmail.com</strong></p>


<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['ushko']) ) $options['ushko'] = 'googleads'; //имя ушка
		if ( !isset($options['start']) ) $options['start'] = true; 
		
		$checked_start = $options['start'] ? ' checked="checked" ' : '';
		
		$form = '';
		$form .= '<p><strong>Имя Ушка c кодом рекламы:</strong> ' . ' <input name="f_ushko" type="text" style="width: 300px;" value="' . $options['ushko'] . '"></p>';
		
		$form .= '<p><input name="f_start" type="checkbox"' . $checked_start . '> Включить плагин</p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>