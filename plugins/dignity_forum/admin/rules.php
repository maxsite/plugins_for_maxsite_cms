<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */
 
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Введите здесь правила форума.', __FILE__) . '</p>';

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
    $options['rules'] = $post['f_rules'];
	
	// сохраняем опции
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

// проверяем опции
$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['rules']) ) $options['rules'] = '';
if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/rules/' . '" target="_blank">' . t('Перейти на форум (к правилам) →', __FILE__) . '</a></p>'; 

// начало фоормы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');

$form .= '<h3>' . t('Правила форума', __FILE__) . '</h3>';

$form .= '<p><textarea name="f_rules" cols="90" rows="15">' . $options['rules'] . '</textarea></p>';

// конец формы
$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';
	
// выводим форму
echo $form;
	
#end of file
