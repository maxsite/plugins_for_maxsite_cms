<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 




# функция автоподключения плагина
function weather_adv_autoload($optionss = array())
{
	# регистрируем виджет
	mso_register_widget('weather_adv_widget', t('Погода Плюс', 'plugins')); 
}

# функция выполняется при деинсталяции плагина
function weather_adv_uninstall($optionss = array())
{	
	mso_delete_option_mask('weather_adv_widget_', 'plugins'); // удалим созданные опции
	return $optionss;
}

# функция, которая берет настройки из опций виджетов
function weather_adv_widget($num = 1) 
{
	$widget = 'weather_adv_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	if ( isset($options['id_town']) ) $options['id_town'] = (int)$options['id_town'];
		else $options['id_town'] = 27612; // Москва
	
	return weather_adv_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function weather_adv_widget_form($num = 1) 
{

	$widget = 'weather_adv_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['id_town']) ) $options['id_town'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Индекс города:', 'plugins') . '<br /><a href="http://zmei.name/page/plagin-dlja-maxsite-pogoda" target="_blank">Пример</a></div> '. form_input( array( 'name'=>$widget . 'id_town', 'value'=>$options['id_town'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function weather_adv_widget_update($num = 1) 
{

	$widget = 'weather_adv_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['id_town'] = mso_widget_get_post($widget . 'id_town');
	
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


function weather_adv_widget_custom( $options = array(), $num = 1 )
{	
	if ( !isset($options['header']) )      $options['header'] = '<h2 class="box"><span>' . t('Погода', 'plugins') . '</span></h2>';
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="weather">';
	if ( !isset($options['block_end']) )   $options['block_end'] = '</div>';
	if ( !isset($options['id_town']) )     $options['id_town'] = 27612;
	
    $day              = date( "j" );  
    $options['date']  = date( "Y_m_d" );    
	$cache_key 		  = 'weather_adv_widget' . serialize( $options ) . $num;
	$cache_weather	  = mso_get_cache( $cache_key );
	
	 
	if( $cache_weather ){
		
		$out = $cache_weather;	
	
	} else {
    		
		mso_flush_cache(); 
        
        $xmlfile	= 'http://informer.gismeteo.ru/xml/' . (int)$options['id_town'] . '_1.xml';
		$out        = '';
		$atributes  = array();	
			
		$simple = implode( file( $xmlfile ) );
		$xml    = xml_parser_create();
		xml_parser_set_option( $xml, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $xml, XML_OPTION_SKIP_WHITE, 1 );                  
		xml_parse_into_struct( $xml, $simple, $values, $indexes, $atributes );
		xml_parser_free( $xml );
		
		if ( $values ){
			
			$town = urldecode( $values['2']['attributes']['sname'] );
			$town = iconv( "windows-1251", "UTF-8", $town ); 
			
            $month = mso_page_date( date( "Y-m-d H:i:s" ), 
					   array(	'format' => 'M', 
							'month' => t('января февраля марта апреля мая июня июля августа сенября октября ноября декабря') ), '', '', FALSE );
            	
			$out.='<strong class="town">' . $town .'</strong> - ' . date( "d" ) .' '. $month . '<br />' . NR;	
			
			foreach( $values AS $key => $items ){ 
				
				if( $items['tag'] == 'FORECAST' && $items['type'] == 'open' ){ 
					
                    if( $items['attributes']['day'] != $day ){
                        continue;
                    } 
                    
					$temperatureMin = $values[$key + 3]['attributes']['min'];
					$temperatureMax = $values[$key + 3]['attributes']['max'];
					
					$sky_img  = '';
					$sky_word = '';
							
					$cloudiness = $values[$key + 1]['attributes']['cloudiness'];
					switch( $cloudiness ){
							case '0': 
								$sky_word 	= 'ясно';
								$sky_img    = '<img src="' . getinfo('plugins_url') . 'weather_adv/images/sol.png" alt="ясно" />'; 		break;
							case '1': $sky_img    = '<img src="' . getinfo('plugins_url') . 'weather_adv/images/clouds.png" alt="малооблачно" />'; 	break;
							case '2': $sky_img    = '<img src="' . getinfo('plugins_url') . 'weather_adv/images/clouds_more.png" alt="облачно" />'; 		break;
							case '3': $sky_img    = '<img src="' . getinfo('plugins_url') . 'weather_adv/images/sol_more_clouds.png" alt="пасмурно" />'; 	break;
					}
					
					$precipitation = $values[$key + 1]['attributes']['precipitation'];
					switch( $precipitation ){
							case '4':  $sky_img    ='<img src="' . getinfo('plugins_url') . 'weather_adv/images/sol_rain.png" alt="дождь" />'; 		break;
							case '5':  $sky_img    ='<img src="' . getinfo('plugins_url') . 'weather_adv/images/sol_more_clouds.png" alt="ливень" />'; 		break;
							case '6':  $sky_img    ='<img src="' . getinfo('plugins_url') . 'weather_adv/images/cluds_snow.png" alt="снег" />'; 			break;
							case '7':  $sky_img    ='<img src="' . getinfo('plugins_url') . 'weather_adv/images/cluds_snow.png" alt="снег" />'; 			break;
							case '8':  $sky_img    ='<img src="' . getinfo('plugins_url') . 'weather_adv/images/groza.png" alt="гроза" />'; 		break;
					}
					if ( !$sky_img ){
						  $sky_img    = '<img src="' . getinfo('plugins_url') . 'weather_adv/images/sol.png" alt="ясно" />';
					}
					
					$tod = $values[$key]['attributes']['tod'];
					switch( $tod ){
							case '0': $time    = 'Ночь';  break;
							case '1': $time    = 'Утро';  break;
							case '2': $time    = 'День';  break;
							case '3': $time    = 'Вечер'; break;
					}
									
					
					$out.='<span class="time">' . $time .'</span>: <span class="temperature">' . $temperatureMin .' - ' . $temperatureMax . '&deg;C</span><br />' . NR;					
					$out.=$sky_img . NR;					
					$out.='<br class="end" /><a rel="nofollow" href="www.gismeteo.ru" class="gismeteo-info">Предоставлено Gismeteo.Ru</a>' . NR;
				}
				
			}

			mso_add_cache($cache_key, $out);	
			
		} else {    
			$out = 'Нет данных :(';
		}
	}  
        	
	$out = $options['header'] . $options['block_start'] . NR . $out . $options['block_end'];
		
	return $out;
}


