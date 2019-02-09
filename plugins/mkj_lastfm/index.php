<?php

if(!defined('BASEPATH'))
	exit('No direct script access allowed'); 

/*
 * MKJ last.fm для MaxSite CMS
 * © http://moringotto.ru/
 */

// Автоподключение плагина.
function mkj_lastfm_autoload()
{
	mso_register_widget('mkj_lastfm_widget', 'last.fm');
	mso_hook_add('head', 'mkj_lastfm_head');
}

// CSS.
function mkj_lastfm_head()
{
	echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'mkj_lastfm/mkj_lastfm.css" type="text/css" media="screen">', NR;
}

// Удаление плагина.
function mkj_lastfm_uninstall($args = array())
{	
	mso_delete_option_mask('mkj_lastfm_widget_', 'plugins');
	return $args;
}

// Получение настроек виджета.
function mkj_lastfm_widget($num = 1) 
{
	$widget = 'mkj_lastfm_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array() );

	if(isset($options['header']) and $options['header']) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';

	return mkj_lastfm_widget_custom($options, $num);
}


// Форма настройки виджета.
function mkj_lastfm_widget_form($num = 1) 
{
	// Получение настроек виджета.
	$widget = 'mkj_lastfm_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array());
	if(!isset($options['header'])) $options['header'] = '';
	if(!isset($options['username'])) $options['username'] = '';
	if(!isset($options['profilelink'])) $options['profilelink'] = 0;
	if(!isset($options['tracklink'])) $options['tracklink'] = 1;
	if(!isset($options['cover'])) $options['cover'] = 0;
	if(!isset($options['now'])) $options['now'] = 'Слушаю сейчас';
	if(!isset($options['count'])) $options['count'] = 5; else $options['count'] = (int) $options['count'];
	if(!isset($options['cache'])) $options['cache'] = 1;
	
	// Вывод формы.
	$CI = & get_instance();
	$CI->load->helper('form');
	// Поля формы.
	$form = '<p><div class="t150">Заголовок:</div> ' . form_input(array(
				'name' => $widget . 'header',
				'value' => $options['header']));
	$form .= '<p><div class="t150">Имя пользователя:</div> ' . form_input(array(
				'name' => $widget . 'username',
				'value' => $options['username']));
	$form .= '<p><div class="t150">Ссылка на профиль:</div> ' . form_dropdown($widget . 'profilelink', array(
				'0' => 'Показывать',
				'1' => 'Показывать (noindex и nofollow)',
				'2' => 'Скрыть'), $options['profilelink']);
	$form .= '<p><div class="t150">Ссылки на песни:</div> ' . form_dropdown($widget . 'tracklink', array(
				'0' => 'Показывать',
				'1' => 'Показывать (noindex и nofollow)',
				'2' => 'Скрыть'), $options['tracklink']);
	$form .= '<p><div class="t150">Обложка альбома:</div> ' . form_dropdown($widget . 'cover', array(
				'0' => 'Показывать',
				'1' => 'Скрыть'), $options['cover']);
	$form .= '<p><div class="t150">Текущая песня:</div> ' . form_input(array(
				'name' => $widget . 'now',
				'value' => $options['now']));
	$form .= '<p><div class="t150">Количество песен:</div> ' . form_input(array(
				'name' => $widget . 'count',
				'value' => $options['count']));
	$form .= '<p><div class="t150">Кеш:</div> ' . form_dropdown($widget . 'cache', array(
				'0' => 'Не использовать',
				'1' => '5 минут',
				'2' => '10 минут',
				'3' => '15 минут',
				'4' => '20 минут',
				'5' => '25 минут',
				'6' => '30 минут'), $options['cache']);

	return $form;
}


// Сохранение настроек.
function mkj_lastfm_widget_update($num = 1) 
{
	$widget = 'mkj_lastfm_widget_' . $num;

	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['username'] = mso_widget_get_post($widget . 'username');
	$newoptions['profilelink'] = mso_widget_get_post($widget . 'profilelink');
	$newoptions['tracklink'] = mso_widget_get_post($widget . 'tracklink');
	$newoptions['cover'] = mso_widget_get_post($widget . 'cover');
	$newoptions['now'] = mso_widget_get_post($widget . 'now');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['cache'] = mso_widget_get_post($widget . 'cache');

	if($options != $newoptions) 
		mso_add_option($widget, $newoptions, 'plugins');
}

// Сам виджет.
function mkj_lastfm_widget_custom($options = array(), $num = 1)
{
	// Обрабатываем настройки.
	if(!isset($options['header'])) $options['header'] = '';
	if(!isset($options['username'])) $options['username'] = '';
	if(!isset($options['profilelink'])) $options['profilelink'] = 0;
	if(!isset($options['tracklink'])) $options['tracklink'] = 1;
	if(!isset($options['cover'])) $options['cover'] = 0;
	if(!isset($options['now']) or !$options['now']) $options['now'] = 'Слушаю сейчас';
	if(!isset($options['cache'])) $options['cache'] = 1;
	// Количество композиций. (Не меньше 1, не больше 20.)
	if(!isset($options['count']) or !$options['count']) 
		{ $options['count'] = 5; }
	elseif($options['count'] < 1)
		{ $options['count'] = 1; }
	elseif($options['count'] > 20)
		{ $options['count'] = 20; }
	else
		{ $options['count'] = (int) $options['count']; }

	// Кеш.
	$cache_key = 'mkj_lastfm_' . $options['username'] . $options['cache'];
	$cache = mso_get_cache($cache_key);
	// Кеш имеется, возвращаем его.
	if($cache)
		return $cache;

	// Если имя пользователя не указано.
	if($options['username'] == '')
		return '<div style="border: 2px solid red; padding: 10px;">Имя пользователя last.fm не указано. Невозможно вывести список композиций.</div>';

	// Нет? Получаем список композиций.
	$recent = mkj_lastfm_connect('user.getrecenttracks&user=' . $options['username'], $options['count']);
	$tracks = '';
	// Задаём заголовок виджета.
	$out = $options['header'];
	// Обрабатываем композиции и заносим в переменную out.
	if(is_array($recent['recenttracks']['track'])) {
		$i = 0;
		while($i < $options['count']) {
			// Получаем данные.
			$track =  $recent['recenttracks']['track'][$i];
			if(isset($track['date']['uts'])) {
				$nowplaying = false;
				$time = mkj_lastfm_time($track['date']['uts']);
			}
			else
				$nowplaying = true;

			if($track['image'][0]['#text'] != "")
				{ $image = $track['image'][0]['#text']; }
			else {
				$artist_images = mkj_lastfm_connect('artist.getimages&artist=' . urlencode($track['artist']['#text']), 1);
				if($artist_images['images']['image']['sizes']['size'][0]['#text'] != '')
					{ $image = $artist_images['images']['image']['sizes']['size'][0]['#text']; } 
				else
					{ $image = 'http://cdn.last.fm/flatness/catalogue/noimage/2/default_artist_small.png'; }
			}

			// Блок композиции.
			$out .= '<div class="mkj_lastfm_track">';
			// Обложка альбома.
			if($options['cover'] == 0)
				{ $out .= '<img src="' . $image . '" class="cover">'; }
			// Информация об исполнителе и песне.
			if($options['tracklink'] == 0)
				{ $out .= '<p class="title"><a href="http://www.last.fm/music/' . urlencode($track['artist']['#text']) . '">' . $track['artist']['#text'] . '</a> &mdash; <a href="' . $track['url'] . '">' . $track['name'] . '</a></p>'; }
			elseif($options['tracklink'] == 1)
				{ $out .= '<p class="title"><noindex><a href="http://www.last.fm/music/' . urlencode($track['artist']['#text']) . '" rel="nofollow">' . $track['artist']['#text'] . '</a></noindex> &mdash; <noindex><a href="' . $track['url'] . '" rel="nofollow">' . $track['name'] . '</a></noindex></p>'; }
			else
				{ $out .= '<p class="title">' . $track['artist']['#text'] . ' &mdash; ' . $track['name'] . '</p>'; }
			// Время.
			if($nowplaying)
				{ $out .= '<p>' . $options['now'] . '<img src="' . getinfo('plugins_url') . 'mkj_lastfm/nowplaying.gif" class="nowplaying"></p>'; }
			else
				{ $out .= '<p>' . $time . '</p>'; }
			$out .= '</div>';

			// Поехали дальше.
			$i++;
		}
	}
	// Ссылка на профиль.
	if($options['profilelink'] == 0)
		{ $out .= '<p class="mkj_lastfm_link">Я на <a href="http://www.last.fm/user/' . $options['username'] . '" class="mkj_lastfm_link">last.fm</a>.</p>'; }
	elseif($options['profilelink'] == 1)
		{ $out .= '<p class="mkj_lastfm_link">Я на <noindex><a href="http://www.last.fm/user/' . $options['username'] . '" class="mkj_lastfm_link" rel="nofollow">last.fm</a></noindex>.</p>'; }

	// Добавление в кеш.
	if($options['cache'] != 0)
		{ mso_add_cache($cache_key, $out, $options['cache'] * 300); }

	// Выводим.
	return $out;
}

// Функция подключения к API last.fm и получения настроек.
function mkj_lastfm_connect($method, $count = 5)
{
	$http_options = array(
		'http' => array(
			'method' => "GET",
			'header' => "User-Agent: MaxSite CMS Plugin: MKJ last.fm\r\n"
			)
		);
	$context = stream_context_create($http_options);
	$data = file_get_contents('http://ws.audioscrobbler.com/2.0/?method=' . $method . '&limit=' . $count . '&format=json&api_key=ab56575f312e3ed82281984edd2f1f1e', false, $context);
	if($data)
		{ return json_decode($data, true); }
	else 
		{ return $data; }
}

// Функция обработки времени для вывода.
function mkj_lastfm_time($datefrom)
{
	$d = time() - $datefrom;

	// Секунды.
	if($d < 60)
		{ $time = mkj_lastfm_normaltime($d, 'секунд', 'секунда', 'секунды'); }
	// Минуты.
	elseif($d < 3600) {
		$p = floor($d / 60);
		$time = mkj_lastfm_normaltime($p, 'минут', 'минута', 'минуты');
	}
	// Часы.
	elseif($d < 86400) {
		$p = floor($d / 3600);
		$time = mkj_lastfm_normaltime($p, 'часов', 'час', 'часа');
	}
	// Дни.
	else {
		$p = floor($d / 86400);
		$time = mkj_lastfm_normaltime($p, 'дней', 'день', 'дня');
	}

	return "$time назад";
}

// Функция вывода склонённого текста к числу.
function mkj_lastfm_normaltime($t, $t1, $t2, $t3)
{
	$p = $t % 10;
	if($t > 10 && $t < 20)
		{ $time = $t . ' ' . $t1; }
	elseif($p == 1)
		{ $time = $t . ' ' . $t2; }
	elseif($p == 2 || $p == 3 || $p == 4)
		{ $time = $t . ' ' . $t3; }
	else
		{ $time = $t . ' ' . $t1; }

	return $time;
}

// Функция «обрезки» строк.

?>