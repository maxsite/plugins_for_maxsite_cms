<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* (c) http://rgblog.ru/
*/
  
function extlinks_autoload($args = array())
{
    mso_hook_add( 'custom_page_404', 'extlinks_custom_page_404');
    mso_hook_add( 'content', 'extlinks_content');
    mso_hook_add( 'comments_content', 'extlinks_content');
}

function extlinks_uninstall($args = array())
{
	mso_delete_option('plugin_extlinks', 'plugins'); // удалим созданные опции
	return $args;
}

function extlinks_mso_options()
{
	mso_admin_plugin_options('plugin_extlinks', 'plugins',
		array(
			'prefix' => array(
							'type' => 'text',
							'name' => t('Префикс редиректа', 'plugins'),
							'description' => t('http://mysite/prefix/url', 'plugins'),
							'default' => 'go'
						),
			'message' => array(
							'type' => 'text',
							'name' => t('Сообщение редиректа', 'plugins'),
							'description' => t('Обязательное сообщение о редиректе. Используйте [TIME] для вывода интервала редиректа.', 'plugins'),
							'default' => 'Внимание! Через [TIME] секунды Вы перейдете по внешней ссылке'
						),
			'time' => array(
							'type' => 'text',
							'name' => t('Время редиректа', 'plugins'),
							'description' => t('Выводить сообщение о переходе на другой сайт', 'plugins'),
							'default' => '0'
						),
			'nofol' => array(
						'type' => 'checkbox',
						'name' => t('Добавление тегов noindex и nofollow'),
						'description' => 'Автоматическое добавление тегов на внешние ссылки.',
						'default' => '1',
					),
			'targ' => array(
						'type' => 'checkbox',
						'name' => t('В новой вкладке'),
						'description' => 'Открывать ссылки в новой вкладке.',
						'default' => '1',
					),
			'real' => array(
						'type' => 'checkbox',
						'name' => t('Адрес в теге TITLE'),
						'description' => 'Выводить в подсказке ссылки реальный адрес.',
						'default' => '1',
					),
			'referer' => array(
						'type' => 'checkbox',
						'name' => t('Запрет с чужих сайтов'),
						'description' => 'Запретить переходы с чужих сайтов по созданным ссылкам.',
						'default' => '1',
					),
			'addtext' => array(
							'type' => 'textarea',
							'name' => t('Дополнительный текст', 'plugins'),
							'description' => t('Добавить текст на страницу редиректа', 'plugins'),
							'default' => ''
						),
			'except' => array(
							'type' => 'textarea',
							'name' => t('Исключения', 'plugins'),
							'description' => t('Ссылки, которые необходимо исключить из обработки.', 'plugins'),
							'default' => ''
						),
        ),
		'Настройки плагина ExtLinks', // титул
		'Редирект внешних ссылок.'   // инфо
	);
}

function replaceurl($mlink)
{
	$options = mso_get_option('plugin_extlinks', 'plugins', array());
	if ( !isset($options['nofol']) ) $options['nofol'] = true;
	if ( !isset($options['targ']) ) $options['targ'] = true;
	if ( !isset($options['prefix']) ) $options['prefix'] = 'go';
	if ( !isset($options['real']) ) $options['real'] = true;
	if ( !isset($options['except']) ) $options['except'] = '';

	if ($options['except'])
	{
		if (strpos($options['except'],$mlink[2]) !== false) { return $mlink[0]; }
	}
    $url=getinfo('siteurl');
    $link=base64_encode($mlink[2]);
	$title = ($options['real'])?(' title="' . $mlink[2] . '" '):(' ');
    if (strpos($mlink[2],$url)!==0)
	{
	        $str = '<a'.$mlink[1].'href="'.$url.$options['prefix'].'/'.$link.'"'.$title;
            if ($options['nofol']) $str .= ' rel="nofollow"';
            if ($options['targ']) $str .= ' target="_blank"';
			$str .= $mlink[3].'>'.$mlink[4].'</a>';
	    	return $str;
    }
    return $mlink[0];
}

function replaceurl2($mlink)
{
	die;
	$m[1] = ' ';
	$m[2] = $mlink[1];
	$m[3] = '';
	$m[4] = $mlink[2];

	return replaceurl($m);
}

function extlinks_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_extlinks', 'plugins', array());
	if ( !isset($options['prefix']) ) $options['prefix'] = 'go';
    if ( !isset($options['time']) ) $options['time'] = 0;
    if ( !isset($options['addtext']) ) $options['addtext'] = '';
	if ( !isset($options['message']) ) $options['message'] = 'Внимание! Через [TIME] секунды Вы перейдете по внешней ссылке';
	if ( !isset($options['referer']) ) $options['referer'] = true;
	
	if (mso_segment(1) == $options['prefix'] and mso_segment(2))
	{
		if ($options['referer'])
		{
			// если нет реферера, то рубим
			if (!isset($_SERVER['HTTP_REFERER']))
			{
				header("Content-Type: text/html;charset=utf8");
				die( sprintf('<b><font color="red">' . t('Данная ссылка доступна только со <a href="%s">страниц сайта</a>') . '</font></b>', getinfo('siteurl')) );
			}
			
			// проверяем реферер - откуда пришел
			$p = parse_url($_SERVER['HTTP_REFERER']);
			if (isset($p['host'])) $p = $p['host'];
				else $p = '';
			if ( $p != $_SERVER['HTTP_HOST'] ) // чужой сайт
			{
				header("Content-Type: text/html;charset=utf8");
				die('<b><font color="red">' . t('Запрещен переход по этой ссылке с чужого сайта') . '</font></b>');
			}
		}
		
        $url = base64_decode(mso_segment(2)); // декодируем
		if (mso_segment(3)) $url .= '?'.base64_decode(mso_segment(3));
		
		$url = strip_tags($url);
		$url = str_replace( array('%0d', '%0a'), '', $url );	
		$url = mso_xss_clean($url);

		if (strpos($url,'://')==0) { $url = 'http://'.$url; }
		header('HTTP/1.1 302 Found');
		
        if ($options['time']>0)
        {
            mso_head_meta('title', t('Редирект', __FILE__) );         
			// загружаем начало шаблона
            if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
			$options['message'] = str_replace('[TIME]',$options['time'],$options['message']);
            echo '<h1>Переход по внешней ссылке</h1><p>'.$options['message'].'</p><p style="font-style: italic;"> '.$url.' </p>';
			//Выводим дополнительный текст из опций
            if (isset($options['addtext'])) echo $options['addtext'];
			// редирект
			header("Refresh: ".$options['time'].";$url");
			// загружаем конец шаблона
			if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
        }
		else header("Location: $url");
 		exit();
    }
    return $args;

}

function replacechars($matches)
{
	$matches[1] = htmlspecialchars($matches[1]);
	return '<pre>'.$matches[1].'</pre>';
}

function extlinks_content($text)
{
	$options = mso_get_option('plugin_extlinks', 'plugins', array());
	if (!isset($options['nofol']) ) $options['nofol'] = true;

	$pattern = '|<pre>(.*)</pre>|Usi';
	$text = preg_replace_callback($pattern,'replacechars',$text);  		
	
	// <a href="http://localhost/codeigniter/">ссылка</a>
	$pattern = '|<a(.*?)href="(.*?)"(.*?)>(.*?)</a>|ui';
	$text = preg_replace_callback($pattern, 'replaceurl', $text);
	 
	// [url=урл]сайт[/url]
	$pattern = '|\[url=(.*?)\](.*?)\[/url\]|ui';
	$text = preg_replace_callback($pattern, 'replaceurl2', $text);

    if ($options['nofol'])
    {
		$text = str_ireplace('<a ', '<noindex><a ', $text);
		$text = str_ireplace('</a>', '</a></noindex>', $text);
    }
    return $text;
}
?>
