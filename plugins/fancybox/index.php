<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# функция автоподключения плагина
function fancybox_autoload($args = array())
{
	mso_hook_add( 'head', 'fancybox_head');
	mso_hook_add( 'admin_head', 'fancybox_head');
	mso_hook_add( 'content_out', 'fancybox_content'); # хук на вывод контента после обработки всех тэгов
}

function fancybox_head($args = array())
{
	echo mso_load_jquery();
	
	$url = getinfo('plugins_url') . 'fancybox/';
	
	echo <<<EOF
  <script type="text/javascript" src="{$url}jquery.fancybox.pack.js"></script>
  <script type="text/javascript" src="{$url}jquery.mousewheel-3.0.6.pack.js"></script>
  <link rel="stylesheet" type="text/css" href="{$url}jquery.fancybox.css" media="screen" />

  <script type="text/javascript">
  $(document).ready(function() {
    $("a.lightbox, .fancybox").fancybox({
	  'transitionIn'  :'none',
	  'transitionOut' :'none',
      'titlePosition' :'inside'});
    $("div.gallery a").fancybox({
	  'transitionIn'  :'none',
	  'transitionOut' :'none',
	  'titlePosition' :'over',
	  'titleFormat'	  :function(title, currentArray, currentIndex, currentOpts) {
	  return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? '   ' + title : '') + '</span>';
	  }
    });
  });
  </script>

EOF;
}

function fancybox_content($text = '')
{
   $preg = array(
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',
		'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" rel="group" title="$2"><img src="$1" alt="$2" /></a>',
		'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2" rel="group"><img src="$1" alt="" /></a>',
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox"><img src="$1" alt="" /></a>',
		'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox" title="$2"><img src="$1" alt="$2" /></a>',
		'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$1" alt="" /></a>',
		'~\[image\((.[^\s]*?)\)=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$4" class="lightbox" title="$3"><img src="$2" alt="$3" class="$1" /></a>',
		'~\[image\((.[^ ]*?)\)=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox"><img src="$2" alt="" class="$1" /></a>',
		'~\[image\((.[^ ]*?)\)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$2" alt="" class="$1" /></a>',
		'~\[galname\](.*?)\[\/galname\]~si' => '<div>$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

?>
