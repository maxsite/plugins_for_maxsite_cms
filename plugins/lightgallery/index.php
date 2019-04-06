<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*/


# функция автоподключения плагина
function lightgallery_autoload($args = array())
{
	mso_hook_add('head_css', 'lightgallery_css');
	mso_hook_add('body_end', 'lightgallery_js');
	mso_hook_add('content', 'lightgallery_content'); # хук на вывод контента после обработки всех тэгов
}

function lightgallery_css($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'lightgallery/css/lightgallery.min.css">';

	return $args;
}


function lightgallery_js($args = array())
{


?>
 <script type="text/javascript">
  $(document).ready(function(){
  $('.lightgallery').lightGallery();
  });
  </script>
<?php
 echo '<script src="' . getinfo('plugins_url') . 'lightgallery/js/lightgallery.js"></script>' . NR;
}


function lightgallery_content($text = '')
{
	$preg = array(

	   // удалим раставленные абзацы
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~<p>\[gallery\](\s)*~si' => '[gallery]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',


		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="lightgallery">$1</div>',
		'~\[gal=(.[^\s]*?)\](.*?)\[\/gal\]~si' => '<div class="gallery__item" data-src="$2"><img src="$1" alt="img" class="gallery__thumb gallery__thumb--hover"></div>',
        '~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<div class="gallery__item" data-src="$3"><img src="$1" alt="$2" class="gallery__thumb gallery__thumb--hover"></div>',
		'~\[galname\](.*?)\[\/galname\]~si' => '<div class="gallery__name">$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# end file