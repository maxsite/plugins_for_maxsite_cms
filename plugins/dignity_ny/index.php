<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 *
 * Автор JS: Александр Музыченко
 * http://alexmuz.ru
 *
 */

# функция автоподключения плагина
function dignity_ny_autoload($args = array())
{
	mso_register_widget('dignity_ny_widget', t('Новый год', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_ny_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_ny_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_ny_widget($num = 1) 
{
	$widget = 'dignity_ny_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	if (isset($options['textdo']) ) $options['textdo'] = '<p>' . $options['textdo'] . '</p>';
	else $options['textdo'] = '';

	if (isset($options['textposle']) ) $options['textposle'] = '<p>' . $options['textposle'] . '</p>';
	else $options['textposle'] = '';
	
	if (isset($options['textgz']) ) $options['textgz'] = '<p>' . $options['textgz'] . '</p>';
	else $options['textgz'] = '';
	
	return dignity_ny_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_ny_widget_form($num = 1) 
{

	$widget = 'dignity_ny_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Новый год', 'plugins');
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	if ( !isset($options['textgz']) ) $options['textgz'] = 'Подзравляем с Новым годом!!!';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст до:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст после:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст поздравления:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textgz', 'value'=>$options['textgz'] ) ) ;

	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_ny_widget_update($num = 1) 
{
	$widget = 'dignity_ny_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	$newoptions['textgz'] = mso_widget_get_post($widget . 'textgz');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_ny_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$textdo = $options['textdo'];
	$textposle = $options['textposle'];
	$textgz = $options['textgz'];
	
	$code = "<script LANGUAGE=\"javascript\">
jQuery.fn.countdown = function (date, options) {
		options = jQuery.extend({
			lang: {
				years:   ['год', 'года', 'лет'],
				months:  ['месяц', 'месяца', 'месяцев'],
				days:    ['день', 'дня', 'дней'],
				hours:   ['час', 'часа', 'часов'],
				minutes: ['минута', 'минуты', 'минут'],
				seconds: ['секунда', 'секунды', 'секунд'],
				plurar:  function(n) {
					return (n % 10 == 1 && n % 100 != 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2);
				}
			}, 
			prefix: \"Осталось: \", 
			finish: \"Всё\"			
		}, options);
 
		var timeDifference = function(begin, end) {
		    if (end < begin) {
			    return false;
		    }
		    var diff = {
		    	seconds: [end.getSeconds() - begin.getSeconds(), 60],
		    	minutes: [end.getMinutes() - begin.getMinutes(), 60],
		    	hours: [end.getHours() - begin.getHours(), 24],
		    	days: [end.getDate()  - begin.getDate(), new Date(begin.getYear(), begin.getMonth() + 1, 0).getDate() - 0],
		    	months: [end.getMonth() - begin.getMonth(), 12],
		    	years: [end.getYear()  - begin.getYear(), 0]
		    };
		    var result = new Array();
		    var flag = false;
		    for (i in diff) {
		    	if (flag) {
		    		diff[i][0]--;
		    		flag = false;
		    	}    	
		    	if (diff[i][0] < 0) {
		    		flag = true;
		    		diff[i][0] += diff[i][1];
		    	}
		    	if (!diff[i][0]) continue;
			    result.push(diff[i][0] + ' ' + options.lang[i][options.lang.plurar(diff[i][0])]);
		    }
		    return result.reverse().join(' ');
		};
		var elem = $(this);
		var timeUpdate = function () {
		    var s = timeDifference(new Date(), date);
		    if (s.length) {
		    	elem.html(options.prefix + s);
		    } else {
		        clearInterval(timer);
		        elem.html(options.finish);
		    }		
		};
		timeUpdate();
		var timer = setInterval(timeUpdate, 1000);		
	};
</SCRIPT>";
	
	return $header . $code . "<div id=\"countdown\"><SCRIPT language=JavaScript>$(\"#countdown\").countdown(new Date(2013, 00, 01, 00, 00, 00), {prefix:'" . $textdo . "', finish: '" . $textgz . "'});</SCRIPT></div>" . $textposle;
}

#end of file
