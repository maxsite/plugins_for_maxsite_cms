<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	mso_cur_dir_lang(__FILE__);

	$CI = & get_instance();

	$options_key = 'perelinks';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$options = array();
		$options['linkcount'] = isset( $post['f_linkcount']) ? $post['f_linkcount'] : 0;
		$options['wordcount'] = isset( $post['f_wordcount']) ? $post['f_wordcount'] : 0;
		$options['allowlate'] = isset( $post['f_allowlate']) ? 1 : 0;
		$options['only_page'] = isset( $post['f_only_page']) ? 1 : 0;
		$options['pagetypes'] = isset( $post['f_pagetypes']) ? $post['f_pagetypes'] : 1;
		$options['stopwords'] = isset( $post['f_stopwords']) ? $post['f_stopwords'] : 'будет нужно';

		mso_add_option($options_key, $options, 'plugins');

		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}


	echo '<h1>'. t('Плагин perelinks'). '</h1><p class="info">'. t('С помощью этого плагина вы можете сделать настраиваемую внутреннюю перелинковку.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	$options['linkcount'] = isset($options['linkcount']) ? (int)$options['linkcount'] : 0;
	$options['wordcount'] = isset($options['wordcount']) ? (int)$options['wordcount'] : 0;
	$options['allowlate'] = isset($options['allowlate']) ? (int)$options['allowlate'] : 1;
	$options['only_page'] = isset($options['only_page']) ? (int)$options['only_page'] : 1;
	$options['pagetypes'] = isset($options['pagetypes']) ? (int)$options['pagetypes'] : 1;
	$options['stopwords'] = isset($options['stopwords']) ? $options['stopwords'] : 'будет нужно';
	$type = $options['pagetypes'];

	$pagetypes = NR .
			'<option value="1"' . ( ($type == 1)?(' selected="selected"'):('') ) . '>' . t('Все типы') . '</option>' . NR .
			'<option value="2"' . ( ($type == 2)?(' selected="selected"'):('') ) . '>' . t('Блоговые страницы') . '</option>' . NR .
			'<option value="3"' . ( ($type == 3)?(' selected="selected"'):('') ) . '>' . t('Статические страницы') . '</option>' . NR .
			'<option value="4"' . ( ($type == 4)?(' selected="selected"'):('') ) . '>' . t('Совпадающие страницы') . '</option>' . NR;

	$form = '';

	$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

	$chk = $options['allowlate'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_allowlate" type="checkbox" ' . $chk . '> <strong>' . t('Ссылаться ли на более поздние записи') . '</strong></label><br>' . NR;
	$form .= t('Если отмечено, ссылаемся на любые записи кроме как из будущего. Иначе только на записи с более ранней датой, чем текущая запись.'). '</p>' . NR;

	$chk = $options['only_page'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_only_page" type="checkbox" ' . $chk . '> <strong>' . t('Работать только на страницах') . '</strong></label><br>' . NR;
	$form .= t('Если отмечено, плагин не будет работать на главной, в категориях, тегах и т.п. Рекомендую отметить.'). '</p>' . NR;

	$form .= '<p>&nbsp;</p><p><label><input name="f_linkcount" type="text" value="' . $options['linkcount'] . '"> <strong>' . t('Количество внутренних ссылок') . '</strong></label><br>' . NR;
	$form .= t('Количество внутренних ссылок на одной странице (ссылаться не больше чем на х страниц. 0 — без ограничений).'). '</p>' . NR;

	$form .= '<p>&nbsp;</p><p><label><input name="f_wordcount" type="text" value="' . $options['wordcount'] . '"> <strong>' . t('Ограничение вхождений слов') . '</strong></label><br>' . NR;
	$form .= t('0 — без ограничений. 1 — только первое одинаковое слово делать ссылкой. Дальнейшее не реализовано.'). '</p>' . NR;

	$form .= '<p>&nbsp;</p><p><label><select name="f_pagetypes">' . $pagetypes . '</select> <strong>' . t('Типы страниц для ссылок') . '</strong></label><br>' . NR;
	$form .= t('Делать ли ссылки на страницы с совпадающим типом, на блоговые, статические или все'). '</p>' . NR;

	$form .= '<br><br><h2>' . t('Стоп-слова') . '</h2>';
	$form .= '<p>' . t('Список слов через пробел, которые не будут становиться ссылками.') . '</p>';
	$form .= '<textarea name="f_stopwords" rows="7" style="width: 99%;">';
	$form .= htmlspecialchars($options['stopwords']);
	$form .= '</textarea>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

?>