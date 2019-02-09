<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'cross_posting';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		
		$options['more_text'] = $post['f_more_text'];
		$options['cat_tag'] = isset( $post['f_cat_tag']) ? 1 : 0;
		
		// ya.ru
		$options['yaru_post'] = isset( $post['f_yaru_post']) ? 1 : 0;
		$options['yaru_username'] = $post['f_yaru_username'];
		$options['yaru_password'] = $post['f_yaru_password'];
		$options['yaru_post_comments'] = isset( $post['f_yaru_post_comments']) ? 1 : 0;
		$options['yaru_words'] = $post['f_yaru_words'];

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Кросс-постинг', __FILE__) ?></h1>
<p class="info"><?= t('Кросс-постинг в блоги Я.ру', __FILE__) ?></p>

<?php
		
	$options = mso_get_option($options_key, 'plugins', array());

	$form = '';
	$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

	// YaRu
	
	$form .= '<table cellspacing="5" cellpadding="10" cols="4">';
	
	if ( !isset($options['more_text']) ) $options['more_text'] = 'Ссылка на оригинал'; 
	if ( !isset($options['cat_tag']) ) $options['cat_tag'] = 0; 
	
	if ( !isset($options['yaru_post']) ) $options['yaru_post'] = 0; 
	if ( !isset($options['yaru_username']) ) $options['yaru_username'] = ''; 
	if ( !isset($options['yaru_password']) ) $options['yaru_password'] = ''; 
	if ( !isset($options['yaru_post_comments']) ) $options['yaru_post_comments'] = 1; 
	if ( !isset($options['yaru_words']) ) $options['yaru_words'] = 0; 

	$form .= '<colgroup style="width: 150px">';
	$form .= '<colgroup style="width: 150px">';
	$form .= '<colgroup style="width: 10px">';
	$form .= '<colgroup style="width: 500px">';
	
	$form .= '<tr><td><p><span><strong>Ссылка на оригинал:</strong></span></td>' . '<td><input name="f_more_text" type="text" value="' . $options['more_text'] . '"></td><td></td><td>Укажите текст ссылки на оригинал, которая будет проставлена в конце записи</td></tr>';
	$chk_cat_tag = $options['cat_tag'] ? ' checked="checked" ' : '';
	$form .= '<tr><td><p><span><strong>Рубрики как метки</strong></span></td>' . '<td><input name="f_cat_tag" type="checkbox" ' . $chk_cat_tag . '></td><td></td><td>Отметьте, если желаете использовать рубрики в качестве меток</td></tr>';
	
	$form .= '<tr><td colspan="4" align="center"><p><h2>Блог Я.ру</h2></td></tr>';
	
	$chk_yaru = $options['yaru_post'] ? ' checked="checked" ' : '';
	$form .= '<tr><td><p><span><strong>Постим в я.ру?</strong></span></td>' . '<td><input name="f_yaru_post" type="checkbox" ' . $chk_yaru . '></td></tr>';
	$form .= '<tr><td><p><span><strong>Логин:</strong></span></td>' . '<td><input name="f_yaru_username" type="text" value="' . $options['yaru_username'] . '"></td><td></td><td>Имя вашей учетной записи</td></tr>';
	$form .= '<tr><td><p><span><strong>Пароль:</strong></span></td>' . '<td><input name="f_yaru_password" type="password" value="' . $options['yaru_password'] . '"></td></tr>';
	$chk_yaru_comm = $options['yaru_post_comments'] ? ' checked="checked" ' : '';
	$form .= '<tr><td><p><span><strong>Комментирование</strong></span></td>' . '<td><input name="f_yaru_post_comments" type="checkbox" ' . $chk_yaru_comm . '></td><td></td><td>Если выключено, то комментирование запрещено всегда. Если включено, то комментирование задается настройкой поста.</td></tr>';
	$form .= '<tr><td><p><span><strong>Кол-во слов:</strong></span></td>' . '<td><input name="f_yaru_words" type="text" value="' . $options['yaru_words'] . '"></td><td></td><td>Обрезать пост до указанного количества слов. Укажите <b>0</b>, если не желаете обрезать по словам. В этом случае будет выведен текст до <b>[cut]/[xcut]</b>, если такой имеется. Если будет указано кол-во слов, то [cut]/[xcut] будет проигнорирован.</td></tr>';
	
	$form .= '</table>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

?>
