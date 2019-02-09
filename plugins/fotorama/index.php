<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://max-3000.com/
 */


# функция автоподключения плагина
function fotorama_autoload($args = array())
{
	mso_hook_add( 'head', 'fotorama_head');
	mso_hook_add( 'admin_head', 'fotorama_head');
	mso_hook_add( 'content_out', 'fotorama_content'); # хук на вывод контента после обработки всех тэгов
}

function fotorama_uninstall($args = array())
{
    mso_delete_option('plugin_fotorama', 'plugins' ); // удалим созданные опции
    return $args;
}


function fotorama_mso_options()
{

    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_fotorama', 'plugins',
        array(
            'fotorama_width' => array(
                            'type' => 'text',
                            'name' => t('Ширина галереи'),
                            'description' => t('Укажите ширину Фоторамы (в пикселях или процентах)'),
                            'default' => '100%'
                        ),
            'fotorama_heigth' => array(
                            'type' => 'text',
                            'name' => t('Высота галереи'),
                            'description' => t('Укажите высоту Фоторамы (в пикселях или процентах)'),
                            'default' => '400'
                        ),
            'fotorama_style' => array(
                            'type' => 'select',
                            'name' => t('Навигация по галерее'),
                            'description' => t('Вместо миниатюр можно показывать точки'),
                            'values' => t('1||Точки #2||Миниатюры'),
                            'default' => '1'
                        ),
            'fotorama_background' => array(
                            'type' => 'text',
                            'name' => t('Цвет заднего фона галереи'),
                            'description' => t('Задавать в формате #XXYYZZ'),
                            'default' => '#252424'
                        ),
            'fotorama_preview_background' => array(
                            'type' => 'text',
                            'name' => t('Цвет заднего фона превью или точек'),
                            'description' => t('Задавать в формате #XXYYZZ'),
                            'default' => '#252424'
                        ),
            'fotorama_preload' => array(
                            'type' => 'text',
                            'name' => t('Сколько изображений предзагружать'),
                            'description' => t('Рекомендуется выбирать 0-2, экономьте трафик посетителей'),
                            'default' => "0"
                        ),
            ),
        t('Настройки плагина Фоторама'),
        t('Укажите необходимые опции.')
    );
}

function fotorama_head($args = array()) 
{
	echo mso_load_jquery();
    $options = mso_get_option('plugin_fotorama', 'plugins', array());	

	$url = getinfo('plugins_url') . 'fotorama/';

    if(!isset($options['fotorama_width'])) $options['fotorama_width'] = '100%';
    if(!isset($options['fotorama_heigth'])) $options['fotorama_heigth'] = '400';
    if(!isset($options['fotorama_style'])) $options['fotorama_style'] = '1';
    if(!isset($options['fotorama_background'])) $options['fotorama_background'] = '#252424';
    if(!isset($options['fotorama_preview_background'])) $options['fotorama_preview_background'] = '#252424';
    if(!isset($options['fotorama_preload'])) $options['fotorama_preload'] = '0';

	extract($options);

    if($fotorama_style == 1) { $style = "dots"; }
    else { $style = "thumbs"; } 
    
    	
	echo <<<EOF
	
	<link rel="stylesheet" href="{$url}static/fotorama.css" type="text/css" media="screen">
	<script src="${url}static/fotorama.js"></script>
	<script>
	$(function() {
            $('.gallery').fotorama({
                                    width: "${fotorama_width}",
                                    height: "${fotorama_heigth}",
                                    nav: "${style}",
                                    background: "${fotorama_background}",
                                    navBackground: "${fotorama_preview_background}",
                                    preload: "${fotorama_preload}",
                                });
        });
    </script>
	
EOF;

}

function fotorama_content($text = '')
{
//	$url = getinfo('plugins_url') . 'lightbox/images/';
	
	$preg = array(
	
		// удалим раставленные абзацы
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
		'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
//		'~\[gallery=(.*?)\](.*?)\[\/gallery\]~si' => '<div class="gallery$1">$2</div><script type="text/javascript">\$(function() { lburl = \'' . $url . '\'; \$(\'div.gallery$1 a\').lightBox({imageLoading: lburl+\'lightbox-ico-loading.gif\', imageBtnClose: lburl+\'lightbox-btn-close.gif\', imageBtnPrev: lburl+\'lightbox-btn-prev.gif\', imageBtnNext: lburl+\'lightbox-btn-next.gif\'});});</script>
//		',
		
		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',
		
		'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2"><img src="$1" alt=""></a>',
		
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox"><img src="$1" alt=""></a>',
	
		'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$1" alt=""></a>',
		
		# [image(left)=http://localhost/uploads/mini/2008-07-11-19-50-56.jpg Картинка]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^\s]*?)\)=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$4" class="lightbox" title="$3"><img src="$2" alt="$3" class="$1"></a>',
		
		# [image(left)=http://localhost/uploads/mini/2008-07-11-19-50-56.jpg]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^ ]*?)\)=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox"><img src="$2" alt="" class="$1"></a>',
		
		# [image(right)]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^ ]*?)\)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$2" alt="" class="$1"></a>',

		
	
	
		'~\[galname\](.*?)\[\/galname\]~si' => '<div>$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# end file
