<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Yandex.Money: Give me the Ruble (Widget)
 * Author: (c) Bugo
 * Plugin URL: http://dragomano.ru/maxsite-cms-plugins
 */

function collector_autoload()
{
	mso_register_widget('collector_widget', t('Дай рубль', __FILE__));
}

function collector_uninstall($args = array())
{	
	mso_delete_option_mask('collector_widget_', 'plugins');
	return $args;
}

function collector_widget($num = 1) 
{
	$widget = 'collector_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array());
	
	if (isset($options['header']) and $options['header']) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return collector_widget_custom($options, $num);
}

function collector_widget_form($num = 1) 
{
	$widget = 'collector_widget_' . $num;
	
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['number']) ) $options['number'] = '';
	if ( !isset($options['label']) ) $options['label'] = t('Поделись', __FILE__);
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['bg']) ) $options['bg'] = 'default';
	if ( !isset($options['size']) ) $options['size'] = 2;
	
	$CI = &get_instance();
	$CI -> load -> helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input(array('name'=>$widget . 'header', 'value'=>$options['header']));
	$form .= '<p><div class="t150">' . t('Номер кошелька:', __FILE__) . '</div> '. form_input(array('name'=>$widget . 'number', 'value'=>$options['number']));
	$form .= '<p><div class="t150">' . t('Текст кнопки:', __FILE__) . '</div> '. form_input(array('name'=>$widget . 'label', 'value'=>$options['label']));
	$form .= '<p><div class="t150">' . t('Текст под кнопкой:', __FILE__) . '</div> '. form_input(array('name'=>$widget . 'text', 'value'=>$options['text']));
	$form .= '<p><div class="t150">' . t('Цвет фона:', __FILE__) . '</div> '. 
		form_dropdown(
			$widget . 'bg',
			array( 
				'default' => t('Оранжевый', __FILE__),
				'aqua' => t('Морская волна', __FILE__),
				'blue' => t('Голубой', __FILE__),
				'green' => t('Зелёный', __FILE__),
				'orange' => t('Янтарный', __FILE__),
				'pink' => t('Розовый', __FILE__),
				'purple' => t('Фиолетовый', __FILE__),
				'red' => t('Малиновый', __FILE__),
				'yellow' => t('Жёлтый', __FILE__),
				'nobg' => t('Без фона', __FILE__)
			), 
			$options['bg']
		);
	$form .= '<p><div class="t150">' . t('Сколько просим:', __FILE__) . '</div> '. 
		form_dropdown(
			$widget . 'size',
			array( 
				'0' => t('Рубль', __FILE__), 
				'1' => t('Три рубля', __FILE__), 
				'2' => t('Пять рублей', __FILE__)
			), 
			$options['size']
		);
		
	return $form;
}

function collector_widget_update($num = 1) 
{
	$widget = 'collector_widget_' . $num;
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['number'] = mso_widget_get_post($widget . 'number');
	$newoptions['label'] = mso_widget_get_post($widget . 'label');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['bg'] = mso_widget_get_post($widget . 'bg');
	$newoptions['size'] = mso_widget_get_post($widget . 'size');
	
	if ($options != $newoptions) 
		mso_add_option($widget, $newoptions, 'plugins');
}

function collector_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'collector_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // есть в кэше
	
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['number']) ) $options['number'] = '';
	if ( !isset($options['label']) ) $options['label'] = t('Поделись', __FILE__);
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['bg']) ) $options['bg'] = 'default';
	if ( !isset($options['size']) ) $options['size'] = 2;
	
	switch ($options['size'])
	{
		case 0:
			$size = 1;
			$pos = 79;
		break;
		case 1:
			$size = 3;
			$pos = 262;
		break;
		default:
			$size = 5;
			$pos = 445;
	}
	
	if (!empty($options['number'])) {
		$out .= '
	<div style="font: 12px Arial, sans-serif; color: #000; width: 116px; text-align: center; margin: 0 auto">';
	
		if ($options['bg'] == 'nobg')
			$out .= '
		<div>';
		else
			$out .= '
		<div style="background: url(' . getinfo('plugins_url') . 'collector/img/' . $options['bg'] . '.png) no-repeat 0 -' . $pos . 'px">';
		
		$out .= '
			<form action="https://money.yandex.ru/donate.xml" method="post">
				<input type="hidden" name="to" value="' . $options['number'] . '">
				<input type="hidden" name="s5" value="' . $size . 'rub">
				<input class="submit" type="submit" value="' . (!empty($options['label']) ? $options['label'] : t('Поделись', __FILE__)) . '" style="margin-top:54px">
			</form>
			<div style="padding-top: 4px"><b>' . $options['text'] . '</b></div>
		</div>
		<div style="background: url(' . getinfo('plugins_url') . 'collector/img/' . $options['bg'] . '.png) no-repeat 0 0; height: 29px"></div>
	</div>';
	}
	
	$out = $options['header'] . NR . $out;
	
	mso_add_cache($cache_key, $out); // добавляем в кэш
	
	return $out;
}

?>