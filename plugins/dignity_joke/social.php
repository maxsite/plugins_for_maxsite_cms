<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 * Фрагменты кода из плагина AddZakl
 * MaxSite CMS
 * http://max-3000.com
 *
 */

$show = "";
$sep = ' ';

$segment = mso_segment(2);

if ($segment === 'view') {
	$post_link = getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'];
	$post_title = $onepage['dignity_joke_cuttext'];
}
else
{
	$post_link = getinfo('site_url') . $options['slug'];
	$post_title = $options['header'];
}

$path = getinfo('plugins_url') . 'dignity_joke/img/'; # путь к картинкам

$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($post_title, 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
$show .= $link . '<img title="' . t('Опубликовать ссылку в Twitter', __FILE__) . '" alt="twitter.com" src="' . $path . '/social/twitter.png' . '" width="16px" height="16px"></a>';

$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
$show .= $sep . $link . '<img title="' . t('Опубликовать ссылку в Facebook', __FILE__) . '" alt="facebook.com" src="' . $path . '/social/facebook.png' . '" width="16px" height="16px"></a>';

$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&amp;title=' . $post_title  . '">';
$show .= $sep . $link . '<img title="Опубликовать ссылку В Контакте" alt="vkontakte.ru" src="' . $path . '/social/vkontakte.png' . '" width="16px" height="16px"></a>';

$show .= $sep . '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<div class="g-plusone" data-size="small" data-count="true"></div>
		<script type="text/javascript"> gapi.plusone.render("g-plusone", {"size": "small", "count": "true"}); </script>';

echo '<div class="addzakl">' . $show . '</div>';

#end of file