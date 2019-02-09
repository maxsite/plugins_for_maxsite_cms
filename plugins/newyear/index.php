<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function newyear_autoload()
{
	mso_hook_add('head', 'ny_head');
	mso_register_widget('newyear_widget', t('newyear', __FILE__)); # регистрируем виджет

}

# функция выполняется при деинсталяции плагина
function newyear_uninstall($args = array())
{	
	mso_delete_option_mask('newyear_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function newyear_widget($num = 1) 
{
	$widget = 'newyear_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return newyear_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function newyear_widget_form($num = 1) 
{
	$widget = 'newyear_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['do'])) $options['do'] = '';
	if ( !isset($options['posle'])) $options['posle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Текст до виджета', 'plugins') . '</div>'.
			form_input( array( 'name'=>$widget . 'do', 'value'=>$options['do']));
	$form .= '<p><div class="t150">' . t('Текст после виджета', 'plugins') . '</div>'.
			form_input( array( 'name'=>$widget . 'posle', 'value'=>$options['posle']));
		
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function newyear_widget_update($num = 1) 
{
	$widget = 'newyear_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['do'] = mso_widget_get_post($widget . 'do');
	$newoptions['posle'] = mso_widget_get_post($widget . 'posle');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function newyear_widget_custom($options = array(), $num = 1)
{
	// кэш 
//	$cache_key = 'newyear_widget_custom' . serialize($options) . $num;
//	$k = mso_get_cache($cache_key);
//	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['do'])) $options['do'] = '';
        if ( !isset($options['posle'])) $options['posle'] = '';


    $date = getdate();
    $newyear = mktime(0, 0, 0, 1, 1, $date['year'] + 1);
 
    $now = time();
    $seconds = ($newyear - $now);
 
    $sec = $seconds;
    $secDesc;
    $ost1 = $sec % 10;
    $ost2  = $sec % 100;
    if (($ost1 == 1) && ($ost2 != 11))
        $secDesc = " секунда ";
    else if ((($ost1 == 2) && ($ost2 != 12)) || (($ost1 == 3) && ($ost2 != 13)) || (($ost1 == 4) && ($ost2 != 14)))
        $secDesc = " секунды ";
    else 
        $secDesc = " секунд ";
 
    $min = round($sec / 60);
    $minDesc;
    $ost1 = $min % 10;
    $ost2 = $min % 100;
    if (($ost1 == 1) && ($ost2 != 11))
        $minDesc = " минута ";
    else if ((($ost1 == 2) && ($ost2 != 12)) || (($ost1 == 3) && ($ost2 != 13)) || (($ost1 == 4) && ($ost2 != 14)))
        $minDesc = " минуты ";
    else 
        $minDesc = " минут ";
 
    $hour = round($sec / 3600);
    $hourDesc;
    $ost1 = $hour % 10;
    $ost2 = $hour % 100;
    if (($ost1 == 1) && ($ost2 != 11))
        $hourDesc = " час ";
    else if ((($ost1 == 2) && ($ost2 != 12)) || (($ost1 == 3) && ($ost2 != 13)) || (($ost1 == 4) && ($ost2 != 14)))
        $hourDesc = " часа ";
    else 
        $hourDesc = " часов ";
 
    $days = round($sec / (3600 * 24));
    $daysDesc;
    $ost1 = $days % 10;
    $ost2 = $days % 100;
    if (($ost1 == 1) && ($ost2 != 11))
        $daysDesc = " день ";
    else if ((($ost1 == 2) && ($ost2 != 12)) || (($ost1 == 3) && ($ost2 != 13)) || (($ost1 == 4) && ($ost2 != 14)))
        $daysDesc = " дня ";
    else 
        $daysDesc = " дней ";		
      

//	$out .='<center>';
	$out .='<span class="values" id="valDay">' . $days . '</span><span class="desc" id="descDay">' . $daysDesc . '</span>';
	$out .='<br>или<br>';
	$out .='<span class="values" id="valHour">' . $hour . '</span><span class="desc" id="descHour">' . $hourDesc . '</span>';
	$out .='<br>или<br>';
	$out .= '<span class="values" id="valMin">' . $min . '</span><span calss="desc" id="descMin">' . $minDesc . '</span>';
	$out .='<br>или<br>';
	$out .= '<span class="values" id="valSec">' . $sec .'</span><span class="desc" id="descSec">'. $secDesc . '</span>';
//	$out .='</center>';	

//	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
if ($out)
	{
	if (isset($options['do'])) $out = $options['do'] . $out;
	if (isset($options['posle'])) $out = $out . $options['posle'];
	}
if ($out)
	{		
	if (isset($options['header'])) $out = '<h3 class="box"><span>' . $options['header'] . '</span></h3>' . $out;
	}
	return $out;	
}


function ny_head($arg = array())
 {
  echo '<script type="text/javascript" src="'.getinfo('plugins_url').'newyear/schet.js"></script>';  

 }

?>