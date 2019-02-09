<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS plugin
 * (c) http://max-3000.com/
 * (c) http://filimonov.com.ua
*/


# функция автоподключения плагина
function custom_tagclouds_autoload($args = array())
{
	mso_create_allow('plugin_custom_tagclouds', t('Админ-доступ к custom_tagclouds', 'plugins'));
	mso_hook_add( 'admin_init', 'custom_tagclouds_admin_init'); # хук на админку
	mso_register_widget('custom_tagclouds_widget', t('Настраиваемое облако меток', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function custom_tagclouds_uninstall($args = array())
{	
	mso_delete_option_mask('custom_tagclouds_widget_', 'plugins'); // удалим созданные опции
	mso_remove_allow('plugin_custom_tagclouds');
	return $args;
}

# функция, которая берет настройки из опций виджетов
function custom_tagclouds_widget($num = 1) 
{
	$widget = 'custom_tagclouds_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return custom_tagclouds_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function custom_tagclouds_widget_form($num = 1) 
{
	$widget = 'custom_tagclouds_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="custom_tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $options['min_size'] = 90;
		else $options['min_size'] = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $options['max_size'] = 230;
		else $options['max_size'] = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $options['max_num'] = 50;
		else $options['max_num'] = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $options['min_count'] = 0;
		else $options['min_count'] = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a style="color: %COLOR%;" href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
	
	if ( !isset($options['color']) ) $options['color'] = 'Green, Brown , Olive';

	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	$form .= '<br><div class="t150">&nbsp;</div> %SIZE% %URL% %TAG% %COUNT% %COLOR%';
	
	$form .= '<p><div class="t150">' . t('Мин. размер (%):', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Макс. размер (%):', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ) ;

	$form .= '<p><div class="t150">' . t('Макс. меток:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'max_num', 'value'=>$options['max_num'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Миним. меток:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'min_count', 'value'=>$options['min_count'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div>' . t('Отображать только метки, которых более указанного количества. (0 - без ограничений)', 'plugins');

	$form .= '<p><div class="t150">' . t('Начало блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Конец блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Сортировка:', 'plugins') . '</div> '. form_dropdown($widget . 'sort', 
								array( '0'=>t('По количеству записей (обратно)', 'plugins'), 
										'1'=>t('По количеству записей', 'plugins'), 
										'2'=>t('По алфавиту', 'plugins'), 
										'3'=>t('По алфавиту (обратно)', 'plugins')), 
								$options['sort'] ) ;
	$form .= '<p><div class="t150">' . t('Список цветов:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'color', 'value'=>$options['color'] ) ) ;
	$form .= '<br><div class="t150">&nbsp;</div> Green, Brown , Olive';
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function custom_tagclouds_widget_update($num = 1) 
{
	$widget = 'custom_tagclouds_widget_' . $num; // имя для опций = виджет + номер
	
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
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	$newoptions['color'] = mso_widget_get_post($widget . 'color');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function custom_tagclouds_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'custom_tagclouds_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="custom_tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $min_size = 90;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 230;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $max_num = 50;
		else $max_num = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $min_count = 0;
		else $min_count = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a style="color: %COLOR%;" href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
	
		if ( !isset($options['color']) ) $options['color'] = 'Green, Brown , Olive';
		$colours = mso_explode($options['color'] , false);
		
  $plugin_options = mso_get_option('custom_tagclouds' , 'plugins', array()); 
	$tagcloud = $plugin_options['tags'];
	
	asort($tagcloud);
	$min = reset($tagcloud);
    $max = end($tagcloud);
    
    if ($max == $min) $max++;
    
    // сортировка перед выводом
    if ($sort == 0) arsort($tagcloud); // по количеству обратно
    elseif ($sort == 1) asort($tagcloud); // по количеству 
    elseif ($sort == 2) ksort($tagcloud); // по алфавиту
    elseif ($sort == 3) krsort($tagcloud); // обратно по алфавиту
    else arsort($tagcloud); // по умолчанию
    
    $url = getinfo('siteurl') . 'tag/';
    $out = '';
    $i = 0;
    foreach ($tagcloud as $tag => $count) 
    {
		if ($min_count) 
			if ($count < $min_count) continue;

		$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
		if (!isset($rand_key)) $rand_key = false;
		if ($colours)
		{
		  $old_key = $rand_key;
		  $i = 0;
		  do
		  {
		    $rand_key = array_rand($colours);
		    $i++;
		  }  while (($old_key == $rand_key) and ($i<10));
      $color = $colours[$rand_key]; 
		}
		else $color = '';		
		
		$af = str_replace(array('%SIZE%', '%URL%', '%TAG%', '%COUNT%' , '%COLOR%'), 
							array($font_size, $url . urlencode($tag), $tag, $count, $color), $options['format']);
		
		// альтернативный синтаксис с []
		$af = str_replace(array('[SIZE]', '[URL]', '[TAG]', '[COUNT]' , '[COLOR]'), 
							array($font_size, $url . urlencode($tag), $tag, $count, $color), $af);

		$out .= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

# функция выполняется при указаном хуке admin_init
function custom_tagclouds_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_custom_tagclouds') ) 
	{
		$this_plugin_url = 'plugin_custom_tagclouds'; // url и hook
	
		mso_admin_menu_add('plugins', $this_plugin_url, t('custom_tagclouds', 'plugins'));
		mso_admin_url_hook ($this_plugin_url, 'custom_tagclouds_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function custom_tagclouds_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_custom_tagclouds') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Настройки custom_tagclouds", "plugins"); ' );
	mso_hook_add_dinamic( 'admin_title', ' return t("custom_tagclouds", "plugins") . " - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'custom_tagclouds/admin.php');
}


?>