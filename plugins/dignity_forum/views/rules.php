<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// загружаем начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать сайтбар
$forum->hide_sidebar();

// меню
$forum->menu();

// загружаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
if ( !isset($options['rules']) ) $options['rules'] = ''; 

// выводим заголовок
echo '<div class="forum_header_topic">';
echo '<h1>' . t('Правила', __FILE__) . '</h1>';
echo '</div>';

if ($options['rules'])
{
    echo '<p>' . $options['rules'] . '</p>';
}
else
{
    echo '<p>' . t('Правила не указаны.', __FILE__) . '</p>';
}

// выводим конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
