<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// если есть custom
if (file_exists(getinfo('plugins_dir') . 'dignity_forum/custom/forums.php'))
{
	// подключаем custom/forums.php
	require(getinfo('plugins_dir') . 'dignity_forum/custom/forums.php'); 
}
else
{
	// подключаем default/forums.php
	require(getinfo('plugins_dir') . 'dignity_forum/default/forums.php');
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
