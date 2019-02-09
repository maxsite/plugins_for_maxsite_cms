<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 *
 */

# функция автоподключения плагина
function dignity_quotations_autoload($args = array())
{
	mso_register_widget('dignity_quotations_widget', t('Котировки ЦБ', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_quotations_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_quotations_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_quotations_widget($num = 1) 
{
	$widget = 'dignity_quotations_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	if (isset($options['textdo']) ) $options['textdo'] = '<p>' . $options['textdo'] . '</p>';
	else $options['textdo'] = '';

	if (isset($options['textposle']) ) $options['textposle'] = '<p>' . $options['textposle'] . '</p>';
	else $options['textposle'] = '';
	
	return dignity_quotations_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_quotations_widget_form($num = 1) 
{

	$widget = 'dignity_quotations_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Котировки ЦБ', 'plugins');
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст до:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст после:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_quotations_widget_update($num = 1) 
{
	$widget = 'dignity_quotations_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_quotations_widget_custom($options = array(), $num = 1)
{
	
	function get_content() 
	{
		
		// формируем сегодняшнюю дату
		$date = date("d/m/Y");
		
		// формируем ссылку 
		$link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$date;
		
		// загружаем HTML-страницу 
		$fd = @fopen($link, "r");
		
		$text = "";
		
		if (!$fd)
		{
			$text = '';
		} 
		else
		{
			// чтение содержимого файла в переменную $text
			while (!feof ($fd)) $text .= fgets($fd, 4096);
			
			// закрыть открытый файловый дескриптор
			fclose ($fd);
		}
		
		return $text;
	}
	
	// кэш
	$cache_key = 'dignity_quotations_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// получаем текущие курсы валют с сайта www.cbr.ru 
  	$content = get_content();
	
  	// разбираем содержимое, при помощи регулярных выражений 
  	$pattern = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i";
  	preg_match_all($pattern, $content, $out, PREG_SET_ORDER);
	
 	$dollar = ""; 
  	$euro = ""; 
  	$uah = "";
	
	foreach($out as $cur) 
	{  
		if($cur[2] == 978) $euro = str_replace(",",".",$cur[4]);
		if($cur[2] == 840) $dollar = str_replace(",",".",$cur[4]);
		if($cur[2] == 980) $uah   = str_replace(",",".",$cur[4]);
	}
	
	$out = '';
	
	// выводим названия виджета
	$out .= $options['header'];
	
	// выводим текст до
	$out .= $options['textdo'];
	
	// если euro, dollar, uah существуют, то...
	if ($euro && $dollar && $uah)
	{
		// выводим котировки валют
		$out .= '<p>' . t('EUR: ', __FILE__) . $euro . t('р.', __FILE__) .  '</p>';
		$out .= '<p>' . t('USD: ', __FILE__) . $dollar . t('р.', __FILE__) . '</p>';
		$out .= '<p>' . t('UAH: ', __FILE__) . $uah . t('р.', __FILE__) . '</p>';
	}
	else
	{
		// если сервер не отвечает, выдаём ошибку
		$out .= '<p>Сервер ЦБ не отвечает</p>';
	}

	// выводим текст после
	$out .= $options['textposle'];
	
	// добавляем в кеш, сохраняем сутки
	mso_add_cache($cache_key, $out, 86400, true); // сразу в кэш добавим
	
	return  $out;
}

#end of file
