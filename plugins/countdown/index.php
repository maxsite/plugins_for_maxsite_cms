<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
#счетчик

# функция автоподключения плагина
function countdown_autoload()
{
	mso_hook_add('content_content', 'countdown_custom'); # хук на вывод контента
}

# функция выполняется при активации (вкл) плагина
function countdown_activate($args = array())
{	
	mso_create_allow('countdown_edit', t('Админ-доступ к настройкам countdown'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function countdown_deactivate($args = array())
{	
	// mso_delete_option('plugin_countdown', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function countdown_uninstall($args = array())
{	
	mso_delete_option('plugin_countdown', 'plugins' ); // удалим созданные опции
	mso_remove_allow('countdown_edit'); // удалим созданные разрешения
	return $args;
}

function countdown_head($args = array()) 
{
	echo '<script src="'.getinfo('plugins_url').'countdown/countdown.js"></script>';
	
}



# callback-функция 
function countdown_callback($matches)
{
	/*
	# принимаем данные
	# формат принимаемых данных
	ГГГГ/ММ/ДД/ЗАГОЛОВОК/ПОДСКАЗАКА ПО ОКОНЧАНИИ ОТСЧЕТА
	
	*/
	$text_info = $matches[1];
	$arr1 = array('<p>', '</p>', '<br />', '<br>', '&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;');
	$arr2 = array('',    '',     '',     '',   ' ',      '&',     '<',    '>',    '"');
	$text_info = trim( str_replace($arr1, $arr2, $text_info) );
	
	$arr_text_info = explode('/',$text_info);
	if(count($arr_text_info) != 5) {
		return 'Ошибка в параметрах';
	}
	
	
	$text = '';
	$text .= '<div id="cntdwntitle">'.$arr_text_info[3].'</div>';
	$text .= '<div align="center" id="countdown"></div>';
	$text .= '<script type="text/javascript" language="javascript">';
	$text .= "
		var DateCntY = ".$arr_text_info[0].";
		var DateCntM = ".$arr_text_info[1].";
		var DateCntD = ".$arr_text_info[2].";
		var EventStopTime = '".$arr_text_info[4]."';
		
		function actionifend() {
			//alert('Время вышло');
		}
		";
	
	$text .= '</script>';
	
	$text .= '<script src="'.getinfo('plugins_url').'countdown/countdown.js" type="text/javascript" language="javascript"></script>';
	
	
	
	return $text;
}


# функции плагина
function countdown_custom($text = '')
{
	if (strpos($text, '[countdown]') !== false) // есть вхождения [countdown]
	{
		$pattern = '~\[countdown\](.*?)\[/countdown\]~si';
		$text = preg_replace_callback($pattern, 'countdown_callback', $text);
	}
	
	return $text;
}

# end file