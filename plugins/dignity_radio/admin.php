<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

echo '<h1>' . t('Онлайн радио', __FILE__) . '</h1>';

// получаем доступ к CI
$CI = & get_instance();

// ключ для опций
$options_key = 'plugin_dignity_radio';

// если был пост
if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
{
	// проверяем реферала
	mso_checkreferer();
	
	// создаём массив с опциями
	$options = array();
	$options['header'] = $post['f_header'];
	$options['textdo'] = $post['f_textdo'];
	$options['textposle'] = $post['f_textposle'];
	
	// сохраняем опции
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

// проверяем опции
$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['header']) ) $options['header'] = t('Онлайн радио', __FILE__); 
if ( !isset($options['textdo']) ) $options['textdo'] = ''; 
if ( !isset($options['textposle']) ) $options['textposle'] = '';

// начало фоормы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');

$form .= '<p><strong>' . t('Заголовок страницы:', __FILE__) . '</strong><br>'
    . ' <input name="f_header" type="text" value="' . $options['header'] . '" style="width:100%"></p>';

$form .= '<p><strong>' . t('Текст вначале страницы:', __FILE__) . '</strong><br>'
    . '<textarea name="f_textdo" cols="142" rows="5">' . $options['textdo'] . '</textarea></p>';

$form .= '<p><strong>' . t('Текст в конце страницы:', __FILE__) . '</strong><br>'
    . '<textarea name="f_textposle" cols="142" rows="5">' . $options['textposle'] . '</textarea></p>';

// конец формы
$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';
	
// выводим форму
echo $form;
	
#end of file
