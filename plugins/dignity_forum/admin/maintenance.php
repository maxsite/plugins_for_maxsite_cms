<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */
 
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Здесь вы можете закрыть сайт на технические работы. Форум будет доступен только админу.', __FILE__) . '</p>';

echo '<div class="admin-h-menu">';
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->admin_menu();
echo '</div>';

// получаем доступ к CI
$CI = & get_instance();

// ключ для опций
$options_key = 'plugin_dignity_forum';

// если был пост
if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
{
	// проверяем реферала
	mso_checkreferer();
	
	// создаём массив с опциями
	$options = array();
    $options['offline'] = isset($post['f_offline']) ? 1 : 0;
    $options['offline_text'] = $post['f_offline_text'];
	
	// сохраняем опции
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

// проверяем опции
$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['offline_text']) ) $options['offline_text'] = t('Форум закрыть на обслуживания! Зайдите пожалуйста позже!', __FILE__);
if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '" target="_blank">' . t('Перейти на форум →', __FILE__) . '</a></p>';

// начало фоормы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
    
// опубликовано?
$chckout = ''; 
if (!isset($options['offline']))  $options['offline'] = false;
if ( (bool)$options['offline'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Включить режим обслуживания?', __FILE__)
	. ' <input name="f_offline" type="checkbox" ' . $chckout . '></p>';
        
$form .= '<p><strong>' . t('Текст (если включён режим обслуживания):', 'plugins') . '</strong><br>'
    . '<textarea name="f_offline_text" cols="142" rows="5">' . $options['offline_text'] . '</textarea></p>';

// конец формы
$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';
	
// выводим форму
echo $form;
	
#end of file
