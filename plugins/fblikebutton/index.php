<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * @author Ruslan Brest, http://rb.labtodo.com/
 *
 **/

# функция автоподключения плагина
function fblikebutton_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_fblikebutton', 'plugins', array());

		if (!isset($options['priory'])) $options['priory'] = 20;
		mso_hook_add('content_end', 'fblikebutton_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function fblikebutton_uninstall($args = array())
{
	mso_delete_option('plugin_fblikebutton', 'plugins'); // удалим созданные опции
	return $args;
}

function fblikebutton_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_fblikebutton', 'plugins', 
		array(
			'type-box' => array(
				'type' => 'select', 
				'name' => 'Тип Кода', 
				'description' => 'Выберите Тип Кода',
				// 'values' => 'iframe # xfbml',  // правила для select как в ini-файлах
				'values' => 'iframe # xfbml',  // правила для select как в ini-файлах
				'default' => 'xfbml'
				),
			/*
			'appid'  => array(
				'type' => 'text',
				'name' => 'Application ID',
				'description' => 'AppID FaceBook-приложения',
				'default' => '123456789012345',
				),
			*/
			'width'  => array(
				'type' => 'text',
				'name' => 'Ширина',
				'description' => 'Ширина блока',
				'default' => '450',
				),
			'show_faces'  => array(
				'type' => 'select',
				'name' => 'Аватары',
				'description' => 'Отображение аватаров',
				'values' => 'true # false',  // правила для select как в ini-файлах
				'default' => 'true',
				),
			'font'  => array(
				'type' => 'select',
				'name' => 'Шрифт',
				'description' => 'Шрифт для текстов',
				'values' => ' # Ubuntu # Verdana # Tahoma',  // правила для select как в ini-файлах
				'default' => '',
				),
			'priory' => array(
				'type' => 'text', 
				'name' => 'Приоритет блока', 
				'description' => 'Позволяет расположить блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 20.', 
				'default' => '20'
				)
			),
		'FaceBook Like Button', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function fblikebutton_content_end($args = array())
{
	global $page;
	
	$options = mso_get_option('plugin_fblikebutton', 'plugins', array());
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';
	if($options['type-box'] == 'xfbml') 
	{
		/*
		<div id="fb-root"></div>
		<script src="http://connect.facebook.net/en_US/all.js#appId=128243287260050&amp;xfbml=1"></script>
		<fb:like href="" send="true" width="450" show_faces="true" font="verdana"></fb:like>
		*/
		$out .= '<div id="fb-root"></div>'
		. '<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js'
		. '#'
		//. 'appId=' . $options['appid']
		//. '&amp;'
		. 'xfbml=1'
		. '"></script>'
		. '<fb:like href="'
		. $post_link
		. '" send="true" layout="button_count" width="'
		. $options['width']
		. '" show_faces="'
		. $options['show_faces']
		. '" font="'
		. $options['font']
		. '"></fb:like>';
	}
	/*
	 * <iframe src="http://www.facebook.com/plugins/like.php?href=...&layout=standard
	 * &show_faces=false&width=450&action=like&colorscheme=light&height=35"
	 * scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px;
	 * height:35px;" allowTransparency="true"></iframe>
	 */
	else
	{
		$out .= '<iframe src="http://www.facebook.com/plugins/like.php?href='
		. urlencode($post_link)
		. '&amp;layout=standard&amp;show_faces='
		. $options['show_faces']
		. '&amp;width='
		. $options['width']
		. '&amp;action=like&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'
		. $options['width']
		. 'px; height:35px;" allowTransparency="true"></iframe>';
	}
	echo NR . '<div class="fblikebutton">' . $out . '</div>' . NR;
	return $args;
}

# end of file
