<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function parallax_autoload()
{
	mso_hook_add( 'head', 'parallax_head');
	mso_hook_add( 'content', 'parallax_content');
}

# функция выполняется при активации (вкл) плагина
function parallax_activate($args = array())
{	
	mso_create_allow('parallax_edit', t('Админ-доступ к настройкам parallax'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function parallax_deactivate($args = array())
{	
	// mso_delete_option('plugin_parallax', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function parallax_uninstall($args = array())
{	
	mso_delete_option('plugin_parallax', 'plugins' ); // удалим созданные опции
	mso_remove_allow('parallax_edit'); // удалим созданные разрешения
	return $args;
}

function parallax_head($arg = array())
{

	echo '	<script src="' . getinfo('plugins_url') . 'parallax/js/modernizr.custom.04022.js"></script>';
	//echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'parallax/css/demo.css" type="text/css" media="screen">' . NR;
	//echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'parallax/css/normalize.css" type="text/css" media="screen">' . NR;
	echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'parallax/css/style.css" type="text/css" media="screen">' . NR;
	
	return $arg;
}

# функции плагина
function parallax_custom($arg = array())
{

	
}

# callback функция 
function parallax_content_callback($matches)
{	
	
	$out = '<div class="container">
			<div class="sp-slideshow">
			
				<input id="button-1" type="radio" name="radio-set" class="sp-selector-1" checked="checked" />
				<label for="button-1" class="button-label-1"></label>
				
				<input id="button-2" type="radio" name="radio-set" class="sp-selector-2" />
				<label for="button-2" class="button-label-2"></label>
				
				<input id="button-3" type="radio" name="radio-set" class="sp-selector-3" />
				<label for="button-3" class="button-label-3"></label>
				
				<input id="button-4" type="radio" name="radio-set" class="sp-selector-4" />
				<label for="button-4" class="button-label-4"></label>
				
				<input id="button-5" type="radio" name="radio-set" class="sp-selector-5" />
				<label for="button-5" class="button-label-5"></label>
				
				<label for="button-1" class="sp-arrow sp-a1"></label>
				<label for="button-2" class="sp-arrow sp-a2"></label>
				<label for="button-3" class="sp-arrow sp-a3"></label>
				<label for="button-4" class="sp-arrow sp-a4"></label>
				<label for="button-5" class="sp-arrow sp-a5"></label>
				
				<div class="sp-content">
					<div class="sp-parallax-bg"></div>
					<ul class="sp-slider clearfix">
						<li><img src="'. getinfo('plugins_url') .'parallax/images/image1.png" alt="image01" /></li>
						<li><img src="'. getinfo('plugins_url') .'parallax/images/image2.png" alt="image02" /></li>
						<li><img src="'. getinfo('plugins_url') .'parallax/images/image3.png" alt="image03" /></li>
						<li><img src="'. getinfo('plugins_url') .'parallax/images/image4.png" alt="image04" /></li>
						<li><img src="'. getinfo('plugins_url') .'parallax/images/image5.png" alt="image05" /></li>
					</ul>
				</div>
			</div>
		</div>';

	return $out;
}


# функции плагина
function parallax_content($text = '')
{
	$text = preg_replace_callback('~\[parallax(.*?)\]~si', 'parallax_content_callback', $text);

	return $text;
}


# end file