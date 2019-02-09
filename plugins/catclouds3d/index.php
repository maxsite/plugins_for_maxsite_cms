<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function catclouds3d_autoload($args = array())
{
	mso_register_widget('catclouds3d_widget', 'Облако рубрик 3D'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function catclouds3d_uninstall($args = array())
{	
	mso_delete_option_mask('catclouds3d_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function catclouds3d_widget($num = 1) 
{
	$widget = 'catclouds3d_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return catclouds3d_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function catclouds3d_widget_form($num = 1) 
{
	$widget = 'catclouds3d_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="catclouds3d">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $options['min_size'] = 40;
		else $options['min_size'] = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $options['max_size'] = 130;
		else $options['max_size'] = (int) $options['max_size'];
		
	if ( !isset($options['width']) ) $options['width'] = 150;
		else $options['width'] = (int) $options['width'];
		
	if ( !isset($options['height']) ) $options['height'] = 150;
		else $options['height'] = (int) $options['height'];
			
	if ( !isset($options['speed']) ) $options['speed'] = 220;
		else $options['speed'] = (int) $options['speed'];		
		
	//if ( !isset($options['format']) ) 
//		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%CAT%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	//$form .= '<p><div class="t150">Формат:</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
//	$form .= '<br /><div class="t150">&nbsp;</div> %SIZE% %URL% %CAT% %COUNT%';
	
	$form .= '<p><div class="t150">Мин. размер (%):</div> '. form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ) ;
	$form .= '<p><div class="t150">Макс. размер (%):</div> '. form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ) ;
	$form .= '<p><div class="t150">Скорость вращения :</div> '. form_input( array( 'name'=>$widget . 'speed', 'value'=>$options['speed'] ) ) ;
	$form .= '<p><div class="t150">Ширина (px):</div> '. form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) ) ;
	$form .= '<p><div class="t150">Высота (px):</div> '. form_input( array( 'name'=>$widget . 'height', 'value'=>$options['height'] ) ) ;
	$form .= '<p><div class="t150">Начало блока:</div> '. form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ) ;
	$form .= '<p><div class="t150">Конец блока:</div> '. form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ) ;
	
	$form .= '<p><div class="t150">Сортировка:</div> '. form_dropdown($widget . 'sort', 
								array( '0'=>'По количеству записей (обратно)', '1'=>'По количеству записей', 
									   '2'=>'По алфавиту', '3'=>'По алфавиту (обратно)'), $options['sort'] ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function catclouds3d_widget_update($num = 1) 
{
	$widget = 'catclouds3d_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['min_size'] = mso_widget_get_post($widget . 'min_size');
	$newoptions['max_size'] = mso_widget_get_post($widget . 'max_size');
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['height'] = mso_widget_get_post($widget . 'height');
	$newoptions['speed'] = mso_widget_get_post($widget . 'speed');
	//$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function catclouds3d_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'catclouds3d_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="catclouds3d">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $min_size = 40;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 130;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['width']) ) $width = 150;
		else $width = (int) $options['width'];
		
	if ( !isset($options['height']) ) $height = 150;
		else $height = (int) $options['height'];
		
	if ( !isset($options['speed']) ) $speed = 220;
		else $speed = (int) $options['speed'];			
		
	//if ( !isset($options['format']) ) 
//		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%CAT%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
    
    $url = getinfo('siteurl') . 'category/';
		
	require_once( getinfo('common_dir') . 'category.php' ); // функции мета
	$all_cat = mso_cat_array_single('page', 'category_name', 'ASC', 'blog');
	

	$catcloud = array();
	foreach ($all_cat as $key => $val)
	{
		if ( count($val['pages'])>0 ) // кол-во страниц в этой рубрике > 0
		{
			$catcloud[$val['category_name']] = array( 'count'=>count($val['pages']), 'slug' => $val['category_slug'] );
		}
	}
	
	asort($catcloud);
	$min = reset($catcloud);
	$min = $min['count'];
    $max = end($catcloud);
	$max = $max['count'];
  
    if ($max == $min) $max++;
    
    // сортировка перед выводом
    if ($sort == 0) arsort($catcloud); // по количеству обратно
    elseif ($sort == 1) asort($catcloud); // по количеству 
    elseif ($sort == 2) ksort($catcloud); // по алфавиту
    elseif ($sort == 3) krsort($catcloud); // обратно по алфавиту
    else arsort($catcloud); // по умолчанию
    
	/* ============ */
    $out .= '<script type="text/javascript" src="' . getinfo('stylesheet_url').'js/jvclouds.js"></script>
	<script type="text/javascript" src="' . getinfo('stylesheet_url') . 'js/swfobject.js"></script>';
	
    $out .= '
			<div id="cumulus3dcontent">
			
				<script type="text/javascript">
				//<![CDATA[
						var rnumber = Math.floor(Math.random()*9999999);
						var widget_so = new SWFObject("' . getinfo('stylesheet_url') . 'js/tagcloud.swf?r="+rnumber, "tagcloudflash", "'.$width.'", "'.$height.'", "9", "#FFFFFF");
						widget_so.addParam("wmode", "transparent");								
						widget_so.addParam("allowScriptAccess", "always");
						widget_so.addVariable("tcolor", "#000000");
						widget_so.addVariable("tspeed", "'.$speed.'");
						widget_so.addVariable("distr", "true");
						widget_so.addVariable("mode", "tags");
						widget_so.addVariable("tagcloud", "<span>';
    
    $format = '<a href=\'%URL%\' style=\'font-size:%SIZE%px;\'>%CAT%<\/a>';
    
    foreach( $catcloud as $cat => $ar ) 
    {
		$count = $ar['count'];
		$slug = $ar['slug'];
	
        $font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
        
        $af = str_replace(array('%SIZE%', '%URL%', '%CAT%'), 
						  array($font_size, $url . $slug, $cat), $format);

		$out .= $af . ' '; 	
    }
	
	$out.='<\/span>");
				widget_so.write("cumulus3dcontent");
				//]]>	
				</script>
			</div>';
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

