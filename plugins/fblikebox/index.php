<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
** Olann.Org.Ru
 */

# функция автоподключения плагина
function fblikebox_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_fblikebox', 'plugins', array());
	
		if (!isset($options['priory'])) $options['priory'] = 20;
		mso_hook_add('content_end', 'fblikebox_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function fblikebox_uninstall($args = array())
{	
	mso_delete_option('plugin_fblikebox', 'plugins'); // удалим созданные опции
	return $args;
}

function fblikebox_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_fblikebox', 'plugins', 
		array(
			'type-box' => array(
							'type' => 'select', 
							'name' => 'Тип Кода', 
							'description' => 'Выберите Тип Кода',
							'values' => 'iframe # xfbml',  // правила для select как в ini-файлах
							'default' => 'xfbml'
						),
			'pgeurl'  => array(
							'type' => 'text',
							'name' => 'URL',
							'description' => 'Адрес FaceBook Страницы с HTTP://',
							'default' => 'http://www.facebook.com/pages/olannorgru/235381456477325',
						),	
			'width'  => array(
							'type' => 'text',
							'name' => 'Ширина',
							'description' => 'Ширина блока',
							'default' => '300',
						),	
			'show_faces'  => array(
							'type' => 'select',
							'name' => 'Аватары',
							'description' => 'Отображение аватаров',
							'values' => 'true # false',  // правила для select как в ini-файлах
							'default' => 'true',
						),
			'border_color'  => array(
							'type' => 'text',
							'name' => 'Цвет рамки',
							'description' => 'Позволяет изменить цвет рамки',
							'default' => '',
						),
			'stream'  => array(
							'type' => 'select',
							'name' => 'Полоса прокрутки',
							'description' => 'Отображение полосы прокрутки',
							'values' => 'true # false',  // правила для select как в ini-файлах
							'default' => 'true',
						),
			'header'  => array(
							'type' => 'select',
							'name' => 'Заголовок',
							'description' => 'Отображение заголовка',
							'values' => 'true # false',  // правила для select как в ini-файлах
							'default' => 'true',
						),
			'priory' => array(
							'type' => 'text', 
							'name' => 'Приоритет блока', 
							'description' => 'Позволяет расположить блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 20.', 
							'default' => '20'
						),					
			),
		'FaceBook Like Box', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function fblikebox_content_end($args = array())
{
	global $page;
	
	$options = mso_get_option('plugin_fblikebox', 'plugins', array());
	$out = '';
	if($options['type-box'] == 'xfbml') 
		{
		$out .= '<div id="fb-root"></div>'
		. '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>'
		. '<fb:like-box href="'
		. $options['pgeurl']
		. '" width="'
		. $options['width']
		. '" show_faces="'
		. $options['show_faces']
		. '" border_color="'
		. $options['border_color']
		. '" stream="'
		. $options['stream']
		. '" header="'
		. $options['header']
		. '">'
		. '</fb:like-box>';
		}
	else
		{
		$out .= '<iframe src="http://www.facebook.com/plugins/likebox.php?href='
		. urlencode($options['pgeurl'])
		. '&amp;width='
		. $options['width']
		. '&amp;colorscheme=light&amp;show_faces='
		. $options['show_faces']
		. '&amp;border_color';
		if($options['border_color']!='')
			{
			$out .= '=' . $options['border_color'];
			}
		$out .= '&amp;stream='
		. $options['stream']
		. '&amp;header='
		. $options['header']
		. '&amp;height=200" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'
		. $options['width']
		. 'px; height:200px;" allowTransparency="true"></iframe>';
		}
	echo NR . '<div class="fblikebox">' . $out . '</div>' . NR;
return $args;
}

# end file
