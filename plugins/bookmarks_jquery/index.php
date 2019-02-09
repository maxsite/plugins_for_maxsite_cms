<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Это расширение можно скачать на страничке http://askname.ru/page/bookmarks_jquery
// Ссылки для соц.сервисов и закладок на фреймворке jquery.
// Создатель плагина - Иван Александрович Чалый ( UmFal ) 
// Оригинальный jquery - Дмитрий Лялин ( dimox.name ), 
// Адаптирован к MaxSite на основе кода от Алексея Баринова ( driverlab.ru )

function bookmarks_jquery_autoload($args = array())
{
	mso_create_allow('bookmarks_jquery_edit', 'Админ-доступ к настройкам плагина Bookmarks');
	mso_hook_add('admin_init', 'bookmarks_jquery_admin_init');
	if (is_type('page')) mso_hook_add('content_end', 'bookmarks_jquery_content_end');
}

function bookmarks_jquery_uninstall($args = array())
{	
	mso_delete_option('plugin_bookmarks_jquery', 'plugins');
	return $args;
}

function bookmarks_jquery_admin_init($args = array()) 
{
	if ( !mso_check_allow('bookmarks_jquery_edit') ) 
	{
		return $args;
	}

	$this_plugin_url = 'plugin_bookmarks_jquery';
	mso_admin_menu_add('plugins', $this_plugin_url, 'Bookmarks_jquery');
	mso_admin_url_hook($this_plugin_url, 'bookmarks_jquery_admin_page');
	return $args;
}

function bookmarks_jquery_admin_page($args = array()) 
{
	global $MSO;
	if ( !mso_check_allow('bookmarks_jquery_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}

	mso_hook_add_dinamic('mso_admin_header', ' return $args."Bookmarks_jquery"; ' );
	mso_hook_add_dinamic('admin_title', ' return "Bookmarks_jquery - ".$args; ' );
	require($MSO->config['plugins_dir'].'bookmarks_jquery/admin.php');
}

function bookmarks_jquery_content_end($args = array())
{
	global $page;

	$options = mso_get_option('plugin_bookmarks_jquery', 'plugins', array());

	// если нет в базе опций - устанавливаем значения здесь. умолчания все true.

	// сервисы закладок
	if ( !isset($options['100zakladok.ru']) ) $options['100zakladok.ru'] = true;
	if ( !isset($options['delicious.com']) ) $options['delicious.com'] = true;
	if ( !isset($options['google.com']) ) $options['google.com'] = true;
	if ( !isset($options['bobrdobr.ru']) ) $options['bobrdobr.ru'] = true;
	if ( !isset($options['links.i.ua']) ) $options['links.i.ua'] = true;
	if ( !isset($options['memori.ru']) ) $options['memori.ru'] = true;
	if ( !isset($options['moemesto.ru']) ) $options['moemesto.ru'] = true;
	if ( !isset($options['mister-wong.ru']) ) $options['mister-wong.ru'] = true;
	if ( !isset($options['linkstore.ru']) ) $options['linkstore.ru'] = true;
	if ( !isset($options['lopas.ru']) ) $options['lopas.ru'] = true;
	if ( !isset($options['myscoop.ru']) ) $options['myscoop.ru'] = true;
	if ( !isset($options['ruspace.ru']) ) $options['ruspace.ru'] = true;
	if ( !isset($options['vaau.ru']) ) $options['vaau.ru'] = true;

	// социальные сервисы
	if ( !isset($options['badabadu.com']) ) $options['badabadu.com'] = true;
	if ( !isset($options['chipp.ru']) ) $options['chipp.ru'] = true;
	if ( !isset($options['feedblog.ru']) ) $options['feedblog.ru'] = true;
	if ( !isset($options['korica.info']) ) $options['korica.info'] = true;
	if ( !isset($options['monrate.ru']) ) $options['monrate.ru'] = true;
	if ( !isset($options['news2.ru']) ) $options['news2.ru'] = true;
	if ( !isset($options['newsland.ru']) ) $options['newsland.ru'] = true;
	if ( !isset($options['sloger.net']) ) $options['sloger.net'] = true;

	// устанавливаем список тегов, заголовок и урл
	$tags = urlencode( implode(', ', $page['page_tags']) );
	$post_title = urlencode( stripslashes($page['page_title']).' — '.getinfo('name_site') );
	$post_link = getinfo('siteurl').mso_current_url();
	
	// пути до папки плагина и до папки иконок
	$patt = getinfo('plugins_url').'bookmarks_jquery/';
	$path = getinfo('plugins_url').'bookmarks_jquery/s/';

	$out = '';
	$out .= '<script type="text/javascript" src="' .  $patt . 'jqsocial.js"></script><script type="text/javascript">';

	// начинаем создавать список нужных сервисов. формирование переменных javascript. folder, s, s2. 
	// сделал их глобальными, иначе криво передаётся в функцию
	$out .="var folder='$patt';";
	$opt = 0;
	$out .= "var s=new Array(	";
	if ($options['100zakladok.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'save/?bmurl='.$post_link.'&amp;bmtitle='.utf8_encode($post_title).$options['delicious.com'];
		$out .= " '100zakladok.ru','$link' ";
		};
	if ($options['delicious.com'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'post?url='.$post_link.'&amp;title='.$post_title;
		$out .= " 'delicious.com','$link' ";
		};
	if ($options['google.com'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'bookmarks/mark?op=edit&amp;bkmk='.$post_link.'&amp;title='.$post_title.'&amp;labels='.$tags;	
		$out .= " 'google.com','$link' ";
		};
	if ($options['bobrdobr.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add.html?url='.$post_link.'&amp;title='.$post_title.'&amp;tags='.$tags;		
		$out .= " 'bobrdobr.ru','$link' ";
		};
	if ($options['links.i.ua'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'mark/?url='.$post_link.'&amp;ename='.$post_title;		
		$out .= " 'links.i.ua','$link' ";
		};
	if ($options['memori.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'link/?sm=1&amp;u_data[url]='.$post_link.'&amp;u_data[name]='.$post_title;		
		$out .= " 'memori.ru','$link' ";
		};
	if ($options['moemesto.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'post.php?url='.$post_link.'&amp;title='.$post_title.'&amp;tags='.$tags;		
		$out .= " 'moemesto.ru','$link' ";
		};
	if ($options['mister-wong.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add_url/?bm_url='.$post_link.'&amp;bm_description='.$post_title.'&amp;bm_tags='.urlencode(utf8_encode(implode('%20', $page['page_tags'])));		
		$out .= " 'mister-wong.ru','$link' ";
		};
	if ($options['linkstore.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'servlet/LinkStore?a=add&url='.$post_link.'&title='.$post_title;		
		$out .= " 'linkstore.ru','$link' ";
		};
	if ($options['lopas.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add_story.php?story_url='.$post_link;		
		$out .= " 'lopas.ru','$link' ";
		};
	if ($options['myscoop.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add/?title='.$post_title.'&amp;URL='.$post_link;
		$out .= "'myscoop.ru','$link' ";
		};
	if ($options['ruspace.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'index.php?link=bookmark&amp;action=bookmarkNew&amp;bm=1&amp;url='.$post_link.'&amp;title='.$post_title;
		$out .= " 'ruspace.ru','$link' ";
		};
	if ($options['vaau.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'submit/?action=step2&amp;url='.$post_link;
		$out .= " 'vaau.ru','$link' ";
		};
	$out .= ");";
	$opt = 0;
	$out .= "var s2=new Array( ";
	if ($options['badabadu.com'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = '?url='.$post_link.'&posttitle='.$post_title;
		$out .= " 'badabadu.com','$link' ";
		};
	if ($options['chipp.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'submit.php?url='.$post_link;
		$out .= " 'chipp.ru','$link' ";
		};
	if ($options['feedblog.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'submit.php?url='.$post_link;
		$out .= " 'feedblog.ru','$link' ";
		};
	if ($options['korica.info'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add_story.php?story_url='.$post_link.'&story_title='.$post_title;
		$out .= " 'korica.info','$link' ";
		};
	if ($options['monrate.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'submit.php?url='.$post_link;
		$out .= " 'monrate.ru','$link' ";
		};
	if ($options['news2.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'add_story.php?url='.$post_link;
		$out .= " 'news2.ru','$link' ";
		};
	if ($options['newsland.ru'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'News/Add/type/news/';
		$out .= " 'newsland.ru','$link' ";
		};
	if ($options['sloger.net'] == true) 
		{
		if ( $opt != 0 ) $out .= ',';
		$opt++;
		$link = 'submit.php?url='.$post_link;
		$out .= " 'sloger.net','$link' ";
		};
	$out .= "); ";
	$out .= 'jqsocial(encodeURIComponent(\'' . urldecode($post_link) . '\'),encodeURIComponent(\'' . urldecode($post_title) . '\'))</script>';

	// собственно вывод
	echo $out;

	return $args;
}

?>