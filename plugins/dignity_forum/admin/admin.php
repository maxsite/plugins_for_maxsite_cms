<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */
 
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Рекомендую включить плагины: pagination, captcha, xml_sitemap.', __FILE__) . '</p>';

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
	$options['header'] = $post['f_header'];
	$options['textdo'] = $post['f_textdo'];
	$options['textposle'] = $post['f_textposle'];
	$options['slug'] = $post['f_slug'];
	$options['limit'] = $post['f_limit'];
    $options['reply_limit'] = $post['f_reply_limit'];
    $options['stats'] = isset($post['f_stats']) ? 1 : 0;
    $options['permanent_link'] = isset($post['f_permanent_link']) ? 1 : 0;
    $options['show_social'] = isset($post['f_show_social']) ? 1 : 0;
    $options['hide_sidebar'] = isset($post['f_hide_sidebar']) ? 1 : 0;
   	$options['reply_edit_time'] = $post['f_reply_edit_time'];
    $options['hide_view_avatar'] = isset($post['f_hide_view_avatar']) ? 1 : 0;
    $options['hide_view_author'] = isset($post['f_hide_view_author']) ? 1 : 0;
    $options['google_search_id'] = $post['f_google_search_id'];
    $options['use_admin_note_as_status'] = isset($post['f_use_admin_note_as_status']) ? 1 : 0;
    $options['hide_elapsed_time'] = isset($post['f_hide_elapsed_time']) ? 1 : 0;
    $options['hide_pathway'] = isset($post['f_hide_pathway']) ? 1 : 0;
    $options['hide_date'] = isset($post['f_hide_date']) ? 1 : 0;
    $options['forum_news_title'] = $post['f_forum_news_title'];
	
	// сохраняем опции
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

// проверяем опции
$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['header']) ) $options['header'] = t('Форум', __FILE__); 
if ( !isset($options['textdo']) ) $options['textdo'] = ''; 
if ( !isset($options['textposle']) ) $options['textposle'] = ''; 
if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['reply_limit']) ) $options['reply_limit'] = 10;
if ( !isset($options['reply_edit_time']) ) $options['reply_edit_time'] = 300;
if ( !isset($options['google_search_id']) ) $options['google_search_id'] = ''; 
if ( !isset($options['forum_news_title']) ) $options['forum_news_title'] = t('Недавно обновленные темы на форуме', __FILE__);

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '" target="_blank">' . t('Перейти на форум →', __FILE__) . '</a></p>';

// начало фоормы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');

$form .= '<h3>' . t('Основные настройки', __FILE__) . '</h3>';

$form .= '<p><strong>' . t('Коротка ссылка:', __FILE__) . '</strong><br>'
    . ' <input name="f_slug" type="text" value="' . $options['slug'] . '"></p>';

$form .= '<p><strong>' . t('Тем на страницу:', __FILE__) . '</strong><br>'
    . ' <input name="f_limit" type="text" value="' . $options['limit'] . '"></p>';

$form .= '<p><strong>' . t('Ответов на страницу:', __FILE__) . '</strong><br>'
    . ' <input name="f_reply_limit" type="text" value="' . $options['reply_limit'] . '"></p>';
    
$form .= '<p><strong>' . t('Время для редактирование ответа (в секундах):', __FILE__) . '</strong><br>'
    . ' <input name="f_reply_edit_time" type="text" value="' . $options['reply_edit_time'] . '"></p>';

$form .= '<p><strong>' . t('Заголовок страницы:', __FILE__) . '</strong><br>'
    . ' <input name="f_header" type="text" value="' . $options['header'] . '" style="width:70%"></p>';

$form .= '<p><strong>' . t('Заголовок виджета:', __FILE__) . '</strong><br>'
    . ' <input name="f_forum_news_title" type="text" value="' . $options['forum_news_title'] . '" style="width:70%"></p>';

$form .= '<p><strong>' . t('Текст вначале страницы:', __FILE__) . '</strong><br>'
    . '<textarea name="f_textdo" cols="90" rows="5">' . $options['textdo'] . '</textarea></p>';

$form .= '<p><strong>' . t('Текст в конце страницы:', __FILE__) . '</strong><br>'
    . '<textarea name="f_textposle" cols="90" rows="5">' . $options['textposle'] . '</textarea></p>';
    
$form .= '<p><strong>' . t('Код Google поиска:', __FILE__) . ' <a href="http://www.google.com/cse/manage/create">(' . t('Получить', __FILE__) . ')</a> </strong><br>'
    . ' <textarea name="f_google_search_id" cols="90" rows="5">' . $options['google_search_id'] . '</textarea></p>';

$form .= '<h3>' . t('Дополнительные настройки', __FILE__) . '</h3>';
    
// показывать постоянную ссылку в теме?
$chckout = ''; 
if (!isset($options['permanent_link']))  $options['permanent_link'] = false;
if ( (bool)$options['permanent_link'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать постоянную ссылку в теме?', __FILE__)
	. ' <input name="f_permanent_link" type="checkbox" ' . $chckout . '></p>';
        
// показывать социальные кнопки?
$chckout = ''; 
if (!isset($options['show_social']))  $options['show_social'] = true;
if ( (bool)$options['show_social'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать социальные кнопки в теме?', __FILE__)
	. ' <input name="f_show_social" type="checkbox" ' . $chckout . '></p>';
        
// скрывать сайтбар
$chckout = ''; 
if (!isset($options['hide_sidebar']))  $options['hide_sidebar'] = false;
if ( (bool)$options['hide_sidebar'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Скрывать сайтбар?', __FILE__)
	. ' <input name="f_hide_sidebar" type="checkbox" ' . $chckout . '></p>';
        
// скрывать автора на странице view
$chckout = ''; 
if (!isset($options['hide_view_author']))  $options['hide_view_author'] = true;
if ( (bool)$options['hide_view_author'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать автора темы на странице /view/?', __FILE__)
	. ' <input name="f_hide_view_author" type="checkbox" ' . $chckout . '></p>';

// 
$chckout = ''; 
if (!isset($options['use_admin_note_as_status']))  $options['use_admin_note_as_status'] = false;
if ( (bool)$options['use_admin_note_as_status'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Использовать поле "Примечание админа" (комюзера) как статус на форуме?', __FILE__)
	. ' <input name="f_use_admin_note_as_status" type="checkbox" ' . $chckout . '></p>';

// 
$chckout = ''; 
if (!isset($options['hide_elapsed_time']))  $options['hide_elapsed_time'] = false;
if ( (bool)$options['hide_elapsed_time'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать надпись "Cтраница сгенерирована за..."?', __FILE__)
	. ' <input name="f_hide_elapsed_time" type="checkbox" ' . $chckout . '></p>';
    
// скрывать pathway?
$chckout = ''; 
if (!isset($options['stats']))  $options['stats'] = true;
if ( (bool)$options['stats'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать статистику?', __FILE__)
	. ' <input name="f_stats" type="checkbox" ' . $chckout . '></p>';

// показывать постоянную ссылку в теме?
$chckout = ''; 
if (!isset($options['hide_pathway']))  $options['hide_pathway'] = false;
if ( (bool)$options['hide_pathway'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Скрывать pathway?', __FILE__)
	. ' <input name="f_hide_pathway" type="checkbox" ' . $chckout . '></p>';

// показывать дату публикации?
$chckout = ''; 
if (!isset($options['hide_date']))  $options['hide_date'] = true;
if ( (bool)$options['hide_date'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Показывать дату публикации?', __FILE__)
	. ' <input name="f_hide_date" type="checkbox" ' . $chckout . '></p>';

// конец формы
$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';
	
// выводим форму
echo $form;
	
#end of file
