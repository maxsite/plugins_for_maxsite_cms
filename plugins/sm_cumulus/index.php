<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * 3d flash tag/cat plugin
 * Author: (c) Sam, Roy Tanck
 * Plugin URL: http://6log.ru/sm_cumulus 
 */


# функция автоподключения плагина
function sm_cumulus_autoload($args = array())
{
	mso_cur_dir_lang(__FILE__);
	mso_register_widget('sm_cumulus_widget', t('Облако тэгов/рубкик в 3D')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function sm_cumulus_uninstall($args = array())
{	
	mso_delete_option_mask('sm_cumulus_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function sm_cumulus_widget($num = 1) 
{
	$widget = 'sm_cumulus_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' .
	  $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return sm_cumulus_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function sm_cumulus_widget_form($num = 1) 
{
	mso_cur_dir_lang(__FILE__);
	
	$widget = 'sm_cumulus_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	if ( !isset($options['min_size']) ) $options['min_size'] = 30;
	if ( !isset($options['max_size']) ) $options['max_size'] = 100; //230
	if ( !isset($options['max_num']) ) $options['max_num'] = 50;
	if ( !isset($options['min_count']) ) $options['min_count'] = 0;
	if ( !isset($options['width']) ) $options['width'] = 150;
	if ( !isset($options['height']) ) $options['height'] = 150;
	if ( !isset($options['speed']) ) $options['speed'] = 180; //220
	if ( !isset($options['trans']) ) $options['trans'] = 'true';

	if ( !isset($options['showtags']) ) $options['showtags'] = 'true';
	if ( !isset($options['distr']) ) $options['distr'] = 'true';
	if ( !isset($options['mode']) ) $options['mode'] = 'tags';
	
	if ( !isset($options['bgcolor']) ) $options['bgcolor'] = 'FFFFFF'; //ffffff
	if ( !isset($options['tcolor']) ) $options['tcolor'] = '660000'; //000000
	if ( !isset($options['tcolor2']) ) $options['tcolor2'] = '000000'; //cccccc
	if ( !isset($options['hover_color']) ) $options['hover_color'] = 'CC0000'; //999999
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок') . ':</div> '.
		 form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Скорость вращения') . ':</div> '.
		 form_input( array( 'name'=>$widget . 'speed', 'value'=>$options['speed'] ) ) ;
	$form .= '<p><div class="t150">' . t('Ширина') . '(px):</div> '.
		 form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) ) ;
	$form .= '<p><div class="t150">' . t('Высота') . '(px):</div> '.
		 form_input( array( 'name'=>$widget . 'height', 'value'=>$options['height'] ) ) ;
	$form .= '<p><div class="t150">' . t('Цвет фона') . ':#</div> '.
		 form_input( array( 'name'=>$widget . 'bgcolor', 'value'=>$options['bgcolor'] ) ) ;
	$form .= '<p><div class="t150">' . t('Цвет текста') . ':#</div> '.
		 form_input( array( 'name'=>$widget . 'tcolor', 'value'=>$options['tcolor'] ) ) ;
	$form .= '<p><div class="t150">' . t('Цвет текста') . '2:#</div> '.
		 form_input( array( 'name'=>$widget . 'tcolor2', 'value'=>$options['tcolor2'] ) ) ;
	$form .= '<p><div class="t150">' . t('Ц. при наведении') . ':#</div>'.
		 form_input( array( 'name'=>$widget . 'hover_color', 'value'=>$options['hover_color'] ) ) ;
	$form .= '<p><div class="t150">' . t('Мин. размер') . '(%):</div> '.
		 form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ) ;
	$form .= '<p><div class="t150">' . t('Макс. размер') . '(%):</div> '.
		 form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ) ;
	$form .= '<p><div class="t150">' . t('Макс. меток') . ':</div> '.
		 form_input( array( 'name'=>$widget . 'max_num', 'value'=>$options['max_num'] ) ) ;
	$form .= '<p><div class="t150">' . t('Миним. меток') . '*:</div> '.
		 form_input( array( 'name'=>$widget . 'min_count', 'value'=>$options['min_count'] ) ) ;
	$form .= '<p><div class="t150">&nbsp;</div>*' . t('Отображать только метки, которых более указанного количества. (0 - без ограничений)');
	$form .= '<p><div class="t150">' . t('Начало блока') . ':</div> '.
		 form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ) ;
	$form .= '<p><div class="t150">' . t('Конец блока') . ':</div> '.
		 form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ) ;

	$form .= '<p><div class="t150">' . t('Прозрачность') . ':</div> '.
		 form_dropdown( $widget . 'trans', array( 'false'=>'false', 'true'=>'true'), $options['trans'] ) ;
	$form .= '<p><div class="t150">' . t('Расположение') . ':</div> '.
		 form_dropdown( $widget . 'distr', array( 'false'=>'false', 'true'=>'true'), $options['distr'] ) ;
	$form .= '<p><div class="t150">&nbsp;</div>' . t('Расположить метки на равных друг от друга расстояниях вместо случайного расположения.');
	$form .= '<p><div class="t150">' . t('Отображать') . ':</div> '.
		 form_dropdown( $widget . 'mode', array( 'tags'=>t('теги'), 'cats2'=>t('рубрики'), 'both'=>t('всё')), $options['mode'] ) ;
	$form .= '<p><div class="t150">&nbsp;</div>' . t('Можно выбрать что отображать: теги/рубрики(всё - вместе).');
	$form .= '<p><div class="t150">' . t('No javascript support') . ':</div> '.
		 form_dropdown( $widget . 'showtags', array( 'false'=>'false', 'true'=>'true'), $options['showtags'] ) ;
	$form .= '<p><div class="t150">&nbsp;</div>' . t('Отображать ли облако с отключенным javascript.');
										   
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function sm_cumulus_widget_update($num = 1) 
{
	$widget = 'sm_cumulus_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['min_size'] = mso_widget_get_post($widget . 'min_size');
	$newoptions['max_size'] = mso_widget_get_post($widget . 'max_size');
	$newoptions['max_num'] = mso_widget_get_post($widget . 'max_num');
	$newoptions['min_count'] = mso_widget_get_post($widget . 'min_count');	
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['height'] = mso_widget_get_post($widget . 'height');
	$newoptions['speed'] = mso_widget_get_post($widget . 'speed');
	
	$newoptions['bgcolor'] = mso_widget_get_post($widget . 'bgcolor');
	$newoptions['tcolor'] = mso_widget_get_post($widget . 'tcolor');
	$newoptions['tcolor2'] = mso_widget_get_post($widget . 'tcolor2');
	$newoptions['hover_color'] = mso_widget_get_post($widget . 'hover_color');

	$newoptions['showtags'] = mso_widget_get_post($widget . 'showtags');
	$newoptions['trans'] = mso_widget_get_post($widget . 'trans');
	$newoptions['distr'] = mso_widget_get_post($widget . 'distr');
	$newoptions['mode'] = mso_widget_get_post($widget . 'mode');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function sm_cumulus_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'sm_cumulus_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="sm_cumulus">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';

	if ( !isset($options['min_size']) ) $min_size = 30;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 100;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $max_num = 50;
		else $max_num = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $min_count = 0;
		else $min_count = (int) $options['min_count'];
	
	if ( !isset($options['width']) ) $options['width'] = 150;
	if ( !isset($options['height']) ) $options['height'] = 150;
	if ( !isset($options['speed']) ) $options['speed'] = 220;
	if ( !isset($options['bgcolor']) ) $options['bgcolor'] = 'FFFFFF';
	if ( !isset($options['tcolor']) ) $options['tcolor'] = '000000';
	if ( !isset($options['tcolor2']) ) $options['tcolor2'] = 'CCCCCC';
	if ( !isset($options['hover_color']) ) $options['hover_color'] = '999999';

	if ( !isset($options['showtags']) ) $options['showtags'] = 'true';
	if ( !isset($options['trans']) ) $options['trans'] = 'true';
	if ( !isset($options['distr']) ) $options['distr'] = 'true';
	if ( !isset($options['mode']) ) $options['mode'] = 'tags';
		
	$format = '<a href=\'%URL%\' style=\'font-size:%SIZE%px;\'>%TAG%<\/a>';
	$format2 = '<a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub>';
	$tags = '';
	$tags2 = '';
	$cats = '';
	$cats2 = '';
//==================================================
// использован кусок кода из plugin tagclouds Максима //

	if ($options['mode'] != 'cats2')
	{
		require_once( getinfo('common_dir') . 'meta.php' ); // функции мета
		$tagcloud = mso_get_all_tags_page();
	
		asort($tagcloud);
		$min = reset($tagcloud);
	    $max = end($tagcloud);
    
    	if ($max == $min) $max++;
		
		$url = getinfo('siteurl') . 'tag/';
		$i = 0;

		foreach ($tagcloud as $tag => $count)
    	{
			if ($min_count) 
			if ($tcount < $min_count) continue;

			$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			$tag_url = urlencode($tag);

			$af = str_replace(array('%SIZE%', '%URL%', '%TAG%'), 
							array($font_size, $url . $tag_url, $tag), $format );
			$af2 = str_replace(array('%URL%', '%TAG%', '%COUNT%'), 
							array($url . $tag_url, $tag, $count), $format2 );
		
			$tags .= $af . ' ';
			$tags2 .= $af2 . '  ';
			$i++;
			if ( $max_num != 0 and $i == $max_num ) break;
		}
	}
	
////////////////	
	
	if ($options['mode'] != 'tags')
	{
		require_once( getinfo('common_dir') . 'category.php' ); // функции мета

		$all_cat = mso_cat_array_single('page', 'category_name', 'ASC', 'blog');
		$catcloud = array();
		foreach ($all_cat as $key => $val)
		{
			if ( count($val['pages'])>0 ) // кол-во страниц в этой рубрике > 0
				$catcloud[$val['category_name']] = array( 'count'=>count($val['pages']), 'slug' => $val['category_slug'] );
		}
	
		asort($catcloud);
		$min2 = reset($catcloud);
		$min2 = $min2['count'];
		$max2 = end($catcloud);
		$max2 = $max2['count'];
	  
		if ($max2 == $min2) $max2++;

		if (isset($max) && isset($min))
		{
			$max = round(($max + $max2)/2);
			$min = round(($min + $min2)/2);
		}
		else
		{
			$max = $max2;
			$min = $min2;
		}
	
		$url = getinfo('siteurl') . 'category/';
		$i = 0;
		
		foreach ($catcloud as $cat => $ar) 
		{
			$count = $ar['count'];
			$slug = $ar['slug'];
			
			if ($min_count) 
			if ($tcount < $min_count) continue;

			$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
			$af = str_replace(array('%SIZE%', '%URL%', '%TAG%'), 
							array($font_size, $url . $slug, $cat), $format );
			$af2 = str_replace(array('%URL%', '%TAG%', '%COUNT%'), 
							array($url . $slug, $cat, $count), $format2 );
		
			$cats .= $af . ' ';
			$cats2 .= $af2 . '  ';
			$i++;
			if ( $max_num != 0 and $i == $max_num ) break;			
		}
	}	 
//==================================================

	$path = getinfo('plugins_url') . 'sm_cumulus/';
	$out = '';	

	$out = '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';
    $out .= '<script type="text/javascript" src="' . $path . 'swfobject.js"></script>
			<div id="cumuluscontent">';
	
	if( $options['showtags'] == 'true' ){ $out .= '<p>'; } else { $out .= '<p style="display:none;">'; };
	// alternate content
	if( $options['mode'] != "cats2" ){ $out .= urldecode($tags2); }
	if( $options['mode'] != "tags" ){ $out .= urldecode($cats2); }

	$out .= '</p><p><a href="http://get.adobe.com/flashplayer/">Requires Flash Player 9 or better.</a></p></div>';

    $out .= '<script type="text/javascript">
				//<![CDATA[
				var rnumber = Math.floor(Math.random()*9999999);
				var widget_so = new SWFObject("' . $path . 'tagcloud.swf?r="+rnumber, "tagcloudflash", "' . $options['width'] .
						 '", "' . $options['height'] . '", "9", "#' . $options['bgcolor'] . '");';
	if ($options['trans'] == true)
	{
		$out .= '	
				widget_so.addParam("wmode", "transparent")';
	}	
	$out .= '	
				widget_so.addParam("allowScriptAccess", "always");
				widget_so.addVariable("tcolor", "0x' . $options['tcolor'] . '");
				widget_so.addVariable("tcolor2", "0x' . $options['tcolor2'] . '");
				widget_so.addVariable("hicolor", "0x' . $options['hover_color'] . '");
				widget_so.addVariable("tspeed", "' . $options['speed'] . '");
				widget_so.addVariable("distr", "' .$options['distr']. '");
				widget_so.addVariable("mode", "' .$options['mode']. '");
				widget_so.addVariable("tagcloud", "<span>';

	$out .= $tags . $cats;

	$out .= '<\/span>");
				widget_so.write("cumuluscontent");
				//]]>	
				</script>
			</div>';
				
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

?>