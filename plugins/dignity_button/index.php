<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Александр Шиллинг
 * (c) http://dignityinside.org/
 */

# функция автоподключения плагина
function dignity_button_autoload()
{
	mso_register_widget('dignity_button_widget', t('Наша кнопка', __FILE__)); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_button_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_button_widget_', 'plugins'); // удалим созданные опции
	
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_button_widget($num = 1) 
{
	$widget = 'dignity_button_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	if (isset($options['img']) ) $options['img'] = $options['img'];
	else $options['img'] = '';
	
	if (isset($options['link']) ) $options['link'] = $options['link'];
	else $options['link'] = '';
	
	if (isset($options['title']) ) $options['title'] = $options['title'];
	else $options['title'] = '';
	
	return dignity_button_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_button_widget_form($num = 1) 
{
	$widget = 'dignity_button_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	# Путь к картинкам
	$path = getinfo('plugins_url') . 'dignity_button/img/';
	
	if ( !isset($options['header']) ) $options['header'] = 'Наша кнопка';
	if ( !isset($options['img']) ) $options['img'] = $path . 'banner.jpg';
	if ( !isset($options['link']) ) $options['link'] = getinfo('siteurl');
	if ( !isset($options['title']) ) $options['title'] = 'Название сайта';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '';
	
	$form .= '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) );
			
	$form .= '<p><div class="t150">' . t('Ссылка на картинку:', 'plugins') . '</div> '.
			form_input( array( 'name'=>$widget . 'img', 'value'=>$options['img'] ) ) ;
			
	$form .= '<p><div class="t150">' . t('Ссылка на сайт:', 'plugins') . '</div> '.
			form_input( array( 'name'=>$widget . 'link', 'value'=>$options['link'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Название сайта:', 'plugins') . '</div> '.
			form_input( array( 'name'=>$widget . 'title', 'value'=>$options['title'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_button_widget_update($num = 1) 
{
	$widget = 'dignity_button_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['img'] = mso_widget_get_post($widget . 'img');
	$newoptions['link'] = mso_widget_get_post($widget . 'link');
	$newoptions['title'] = mso_widget_get_post($widget . 'title');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function dignity_button_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	$out .= $options['header'];
	
	# JavaScript для раскрытия и закрытия
	$out .= "
	<script language=\"JavaScript\" type=\"text/javascript\">
	<!--
	function OpenComment(where){
		if(document.getElementById(where).style.display == 'inline'){
			document.getElementById(where).style.display = 'none';
			}
		else{
			document.getElementById(where).style.display = 'inline';
		}
	}
	// -->
	</script>";
  
	# Вывод
	$out .= '<p><img src="' . $options['img'] . '"></p>';
	$out .= "<p><a class=sub href=\"javascript:OpenComment('divbanner')\">Получить код</a></p>";
	$out .= "<div id=divbanner style=\"display: none; margin: 0px\">";
	$out .= '<textarea style="border-style: dotted" rows=5 cols=23><a href="' . $options['link'] . '" target="_blank" title="' . $options['title'] . '"><img src="' . $options['img'] . '"></textarea></div>';
	
	return $out;	
}

# end file