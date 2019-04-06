<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* MaxSite CMS
* (c) http://max-3000.com/
*
*
* Создатель плагина:
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*
* Responsive-Lightbox
* https://github.com/duncanmcdougall/Responsive-Lightbox
* Copyright 2013 Duncan McDougall and other contributors; @license Creative Commons Attribution 2.5
*/


# функция автоподключения плагина
function responsive_lightbox_autoload($args = array())
{
     mso_hook_add( 'head_css', 'responsive_lightbox_css');
	 mso_hook_add( 'body_end', 'responsive_lightbox_js');
	 mso_hook_add( 'content', 'responsive_lightbox_content'); # хук на вывод контента после обработки всех тэгов
}

function responsive_lightbox_css($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'responsive_lightbox/css/lightbox.css">';

	return $args;
}


function responsive_lightbox_js($args = array())
{
	echo '<script src="' . getinfo('plugins_url') . 'responsive_lightbox/js/jquery.lightbox.min.js"></script>' . NR;
}


function responsive_lightbox_content($text = '')
{

	$preg = array(

		// удалим раставленные абзацы
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~<p>\[gallery\](\s)*~si' => '[gallery]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',


		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',

		'~\[gal=(.[^\s]*?)\](.*?)\[\/gal\]~si' => '<div class="gallery__item"><a href="$2"><img src="$1" alt="img" class="gallery__thumb gallery__thumb--hover"></a></div>',
        '~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<div class="gallery__item"><a href="$3" title="$2"><img src="$1" alt="$2" class="gallery__thumb gallery__thumb--hover"></a></div>',

		// MaxSite CMS
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox__img"><img src="$1" alt="img" class="gallery__thumb--center gallery__thumb--hover"></a>',
        '~\[img (.*?)\](.*?)\[\/img\]~si'=> '<img src="$2" title="$1" alt="$1" class="center">',
		'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox__img"><img src="$1" alt="$2" title="$2" class="gallery__thumb--center gallery__thumb--hover"></a>',
		'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox__img"><img src="$1" alt="img" title="img" class="gallery__thumb--center gallery__thumb--hover"></a>',

        //NEW
        '~\[image(.*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox__img"><img src="$2" alt="$1" title="$1" class="gallery__thumb--center gallery__thumb--hover"></a>',
		'~\[image_left\](.*?)\[\/image_left\]~si' => '<a href="$1" class="lightbox__img"><img src="$1" alt="img" class="gallery__thumb--left gallery__thumb--hover"></a>',
		'~\[image_left(.*?)\](.*?)\[\/image_left\]~si' => '<a href="$2" class="lightbox__img"><img src="$2" alt="$1" title="$1" class="gallery__thumb--left gallery__thumb--hover"></a>',

		'~\[image_right\](.*?)\[\/image_right\]~si' => '<a href="$1" class="lightbox__img"><img src="$1" alt="img" class="gallery__thumb--right gallery__thumb--hover"></a>',
        '~\[image_right(.*?)\](.*?)\[\/image_right\]~si' => '<a href="$2" class="lightbox__img"><img src="$2" alt="$1" title="$1" class="gallery__thumb--right gallery__thumb--hover"></a>',

		'~\[galname\](.*?)\[\/galname\]~si' => '<div class="gallery__name">$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# end file