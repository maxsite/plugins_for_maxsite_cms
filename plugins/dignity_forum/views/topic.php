<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// если есть свой custom
if (file_exists(getinfo('plugins_dir') . 'dignity_forum/custom/forum_topic.php'))
{
	// подключаем custom/forum_topic.php
	require(getinfo('plugins_dir') . 'dignity_forum/custom/forum_topic.php'); 
}
else
{
	// Подключаем default/forum_topic.php
	require(getinfo('plugins_dir') . 'dignity_forum/default/forum_topic.php'); 
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
