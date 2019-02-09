<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.ru
 * License GNU GPL 2+
 */
 
echo '<h1>' . t('Статистика', __FILE__) . '</h1>';

// получаем доступ к CI
$CI = & get_instance();

// ключ для опций
$options_key = 'plugin_statistic';

// если был пост
if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
{
	// проверяем реферала
	mso_checkreferer();
	
	// создаём массив с опциями
	$options = array();
    $options['hide_users'] = isset($post['f_hide_users']) ? 1 : 0;
    $options['hide_comusers'] = isset($post['f_hide_comusers']) ? 1 : 0;
    $options['hide_active_comusers'] = isset($post['f_hide_active_comusers']) ? 1 : 0;
    $options['hide_no_active_comusers'] = isset($post['f_hide_no_active_comusers']) ? 1 : 0;
    $options['hide_article_dignity_blogs'] = isset($post['f_hide_article_dignity_blogs']) ? 1 : 0;
    $options['hide_comments_dignity_blogs'] = isset($post['f_hide_comments_dignity_blogs']) ? 1 : 0;
    $options['hide_topic_dignity_forum'] = isset($post['f_hide_topic_dignity_forum']) ? 1 : 0;
    $options['hide_reply_dignity_forum'] = isset($post['f_hide_reply_dignity_forum']) ? 1 : 0;
    $options['hide_video_dignity_video'] = isset($post['f_hide_video_dignity_video']) ? 1 : 0;
    $options['hide_comments_dignity_video'] = isset($post['f_hide_comments_dignity_video']) ? 1 : 0;
    $options['hide_joke_dignity_joke'] = isset($post['f_hide_joke_dignity_joke']) ? 1 : 0;
    $options['hide_comments_dignity_joke'] = isset($post['f_hide_comments_dignity_joke']) ? 1 : 0;
    $options['hide_soft_dignity_soft'] = isset($post['f_hide_soft_dignity_soft']) ? 1 : 0;
    $options['hide_comments_dignity_soft'] = isset($post['f_hide_comments_dignity_soft']) ? 1 : 0;
	
	// сохраняем опции
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

$options = mso_get_option($options_key, 'plugins', array());

// начало фоормы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
    
// показвать юзеров?
if (!isset($options['hide_users']))  $options['hide_users'] = true;
$chckout = ''; 
if ( (bool)$options['hide_users'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "администраторов"?', __FILE__)
	. ' <input name="f_hide_users" type="checkbox" ' . $chckout . '></p>';

// показывать комюзеров?
if (!isset($options['hide_comusers']))  $options['hide_comusers'] = false;
$chckout = ''; 
if ( (bool)$options['hide_comusers'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "Комюзеров (пользователей)"?', __FILE__)
	. ' <input name="f_hide_comusers" type="checkbox" ' . $chckout . '></p>';

// показывать активных комюзеров?
if (!isset($options['hide_active_comusers']))  $options['hide_active_comusers'] = false;
$chckout = ''; 
if ( (bool)$options['hide_active_comusers'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "Активных"?', __FILE__)
	. ' <input name="f_hide_active_comusers" type="checkbox" ' . $chckout . '></p>';

// показывать не активных комюзеров?
if (!isset($options['hide_no_active_comusers']))  $options['hide_no_active_comusers'] = false;
$chckout = ''; 
if ( (bool)$options['hide_no_active_comusers'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "Заблудившихся"?', __FILE__)
	. ' <input name="f_hide_no_active_comusers" type="checkbox" ' . $chckout . '></p>';

// показывать количество тем в блогах (dignity_blogs)?
if (!isset($options['hide_article_dignity_blogs']))  $options['hide_article_dignity_blogs'] = false;
$chckout = ''; 
if ( (bool)$options['hide_article_dignity_blogs'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество тем в блогах (dignity_blogs)"?', __FILE__)
	. ' <input name="f_hide_article_dignity_blogs" type="checkbox" ' . $chckout . '></p>';

// показывать количество комментарий в блогах (dignity_blogs)?
if (!isset($options['hide_comments_dignity_blogs']))  $options['hide_comments_dignity_blogs'] = false;
$chckout = ''; 
if ( (bool)$options['hide_comments_dignity_blogs'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество комментариев в блогах (dignity_blogs)"?', __FILE__)
	. ' <input name="f_hide_comments_dignity_blogs" type="checkbox" ' . $chckout . '></p>';

// показывать количество тем на форуме (dignity_forum)?
if (!isset($options['hide_topic_dignity_forum']))  $options['hide_topic_dignity_forum'] = false;
$chckout = ''; 
if ( (bool)$options['hide_topic_dignity_forum'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество тем на форуме (dignity_forum)"?', __FILE__)
	. ' <input name="f_hide_topic_dignity_forum" type="checkbox" ' . $chckout . '></p>';

// показывать количество ответов на форуме (dignity_forum)?
if (!isset($options['hide_reply_dignity_forum']))  $options['hide_reply_dignity_forum'] = false;
$chckout = ''; 
if ( (bool)$options['hide_reply_dignity_forum'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество ответов на форуме (dignity_forum)"?', __FILE__)
	. ' <input name="f_hide_reply_dignity_forum" type="checkbox" ' . $chckout . '></p>';

// показывать количество видео записей (dignity_video)?
if (!isset($options['hide_video_dignity_video']))  $options['hide_video_dignity_video'] = false;
$chckout = ''; 
if ( (bool)$options['hide_video_dignity_video'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество видео записей (dignity_video)"?', __FILE__)
	. ' <input name="f_hide_video_dignity_video" type="checkbox" ' . $chckout . '></p>';

// показывать количество комментарий к видео записям (dignity_video)?
if (!isset($options['hide_comments_dignity_video']))  $options['hide_comments_dignity_video'] = false;
$chckout = ''; 
if ( (bool)$options['hide_comments_dignity_video'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество комментариев к видео записям (dignity_video)"?', __FILE__)
	. ' <input name="f_hide_comments_dignity_video" type="checkbox" ' . $chckout . '></p>';

// показывать количество анекдотов (dignity_joke)?
if (!isset($options['hide_joke_dignity_joke']))  $options['hide_joke_dignity_joke'] = false;
$chckout = ''; 
if ( (bool)$options['hide_joke_dignity_joke'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество анекдотов (dignity_joke)"?', __FILE__)
	. ' <input name="f_hide_joke_dignity_joke" type="checkbox" ' . $chckout . '></p>';

// показывать количество комментарий к анекдотам (dignity_joke)?
if (!isset($options['hide_comments_dignity_joke']))  $options['hide_comments_dignity_joke'] = false;
$chckout = ''; 
if ( (bool)$options['hide_comments_dignity_joke'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество комментариев к анекдотам (dignity_joke)"?', __FILE__)
	. ' <input name="f_hide_comments_dignity_joke" type="checkbox" ' . $chckout . '></p>';

// показывать количество приложений (dignity_soft)?
if (!isset($options['hide_soft_dignity_soft']))  $options['hide_soft_dignity_soft'] = false;
$chckout = ''; 
if ( (bool)$options['hide_soft_dignity_soft'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество приложений (dignity_soft)"?', __FILE__)
	. ' <input name="f_hide_soft_dignity_soft" type="checkbox" ' . $chckout . '></p>';

// показывать количество комментарий к приложениям (dignity_soft)?
if (!isset($options['hide_comments_dignity_soft']))  $options['hide_comments_dignity_soft'] = false;
$chckout = ''; 
if ( (bool)$options['hide_comments_dignity_soft'] )
{
	$chckout = 'checked="false"';
} 
$form .= '<p>' . t('Скрыть "количество комментариев к приложениям (dignity_soft)"?', __FILE__)
	. ' <input name="f_hide_comments_dignity_soft" type="checkbox" ' . $chckout . '></p>';

// конец формы
$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить', __FILE__) . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';
	
// выводим форму
echo $form;
	
#end of file
