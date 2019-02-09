<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function googleplusodin_autoload()
{
	if (!is_feed() and (is_type('page'))) 
		mso_hook_add('content_content', 'googleplusodin_content'); # хук на вывод контента
		mso_hook_add('head', 'googleplusodin_head');
}


# функция выполняется при деинсталяции плагина
function googleplusodin_uninstall($args = array())
{	
	mso_delete_option('plugin_googleplusodin', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function googleplusodin_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_googleplusodin', 'plugins', 
		array(
			'align' => array(
						'type' => 'select', 
						'name' => 'Выравнивание блока', 
						'description' => 'Укажите выравнивание блока. Он добавляется в начало каждой записи.',
						'values' => 'left||Влево # right||Вправо # none||Нет',
						'default' => 'right'
					),
			'page_type' => array(
						'type' => 'text', 
						'name' => 'Тип страниц', 
						'description' => 'Выводить блок только на указанных типах страниц (типы указывать через запятую).',
						'default' => 'blog, static'
					),										
			),
		'Настройки плагина Google +1', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
# Функции Заголовка
function googleplusodin_head($arg = array())
{
	echo '<script type="text/javascript" src="http://apis.google.com/js/plusone.js"> {lang: "ru"} </script>';
	return $arg;
}
# функции плагина
function googleplusodin_content($text = '')
{
	global $page;
	
	if (!is_type('page')) return $text;
	
	// если запись не опубликована, не отображаем блок
	if (is_type('page') and isset($page['page_status']) and $page['page_status'] != 'publish') return $text;
	
	$options = mso_get_option('plugin_googleplusodin', 'plugins', array() ); // получаем опции
	
	// отображать только на одиночной странице
//	if (!isset($options['show_only_page'])) $options['show_only_page'] = 0; 
//	if ($options['show_only_page'] and !is_type('page')) return $text;
	
	if (is_type('page') and isset($options['page_type']) and $options['page_type'])
	{
		$p_type_name = mso_explode($options['page_type'], false);
		
		// нет у указанных типах страниц
		if (!in_array($page['page_type_name'], $p_type_name)) return $text;
	}
	
	// стиль выравнивания
	if (!isset($options['align'])) $options['align'] = 'right';
	if ($options['align'] == 'left') $style = ' style="float: left; margin-right: 10px;"';
			elseif ($options['align'] == 'right') $style = ' style="float: right; margin-left: 10px; width: "';
			else $style = '';
	
	// блок выводится
			$url = mso_current_url(true);
		
		$text = '<span style="display: none"><![CDATA[<noindex>]]></span><div class="googleplusodin"' . $style . '>' 
		. '<g:plusone size="tall" href="'. $url .'"></g:plusone>'
		. '</div><span style="display: none"><![CDATA[</noindex>]]></span>' . $text;
		
	return $text;
}


# end file