<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Александр Шиллинг
 * (c) http://alexanderschilling.net/
 *
 * JS - twitter-text-js 1.4.10, Copyright 2011 Twitter, Inc.
 */

# функция автоподключения плагина
function dignity_twitter_autoload($args = array())
{
	mso_register_widget('dignity_twitter_widget', t('Твиттер', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_twitter_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_twitter_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_twitter_widget($num = 1) 
{
	$widget = 'dignity_twitter_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	$path = getinfo('plugins_url') . 'dignity_twitter/img/'; # путь к картинкам
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';

	if (isset($options['twitter']) and $options['twitter']) 
	$options['twitter'] = $options['twitter'];
	else $options['twitter'] = '';
	
	if (isset($options['twitter_rpp']) ) $options['twitter_rpp'] = $options['twitter_rpp'];
	else $options['twitter_rpp'] = '';

	if (isset($options['textend']) ) $options['textend'] = '<p>' . $options['textend'] . '</p>';
	else $options['textend'] = '';
	
	return dignity_twitter_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_twitter_widget_form($num = 1) 
{

	$widget = 'dignity_twitter_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Мой Твиттер';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['twitter']) ) $options['twitter'] = 'dignityinside';
	if ( !isset($options['twitter_rpp']) ) $options['twitter_rpp'] = 10;
	if ( !isset($options['textend']) ) $options['textend'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст вначале:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;

	$form .= '<p><div class="t150">' . t('Twitter:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'twitter', 'value'=>$options['twitter'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество твиттов:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'twitter_rpp', 'value'=>$options['twitter_rpp'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст в конце:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textend', 'value'=>$options['textend'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_twitter_widget_update($num = 1) 
{
	$widget = 'dignity_twitter_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['twitter'] = mso_widget_get_post($widget . 'twitter');
	$newoptions['twitter_rpp'] = mso_widget_get_post($widget . 'twitter_rpp');
	$newoptions['textend'] = mso_widget_get_post($widget . 'textend');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_twitter_widget_custom($options = array(), $num = 1)
{
	
	// кэш 
	$cache_key = 'dignity_twitter_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// Проверяем опции
	if ( !isset($options['twitter']) ) $options['twitter'] = 'dignityinside';
	if ( !isset($options['twitter_rpp']) ) $options['twitter_rpp'] = '';
	
	// Имя твиттера
	$twitter = $options['twitter'];
	
	// Количество твиттов
	$twitter_rpp = $options['twitter_rpp'];
	
	// Загружаем JS с сайта твиттера и выполняем JS
	$show = "<script src=\"http://widgets.twimg.com/j/2/widget.js\"></script>
	<script>
	new TWTR.Widget({
	  version: 2,
	  type: 'profile',
	  rpp: " . $twitter_rpp . ",
	  interval: 30000,
	  width: 'auto',
	  height: 300,
	  theme: {
	    shell: {
	      background: '#ffffff',
	      color: '#808080'
	    },
	    tweets: {
	      background: '#ffffff',
	      color: '#404040',
	      links: '#808080'
	    }
	  },
	  features: {
	    scrollbar: true,
	    loop: false,
	    live: true,
	    behavior: 'all'
	  }
	}).render().setUser('". $twitter . "').start();
	</script>";

	$out = '';
	
	// Проверяем опции
	if ( !isset($options['header']) ) $options['header'] = 'Мой Твиттер';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['textend']) ) $options['textend'] = '';
	
	// Выводим
	$out .= $options['header'];
	$out .= $options['text'];
	$out .= $show;
	$out .= $options['textend'];
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
		
	return $out;
}

#end of file
