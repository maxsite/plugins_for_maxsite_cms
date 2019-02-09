<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 



# функция автоподключения плагина
function taggallery_autoload($args = array())
{
    mso_create_allow('taggallery_edit', t('Админ-доступ к taggallery', __FILE__));
    mso_hook_add('custom_page_404', 'taggallery_custom_page_404');
    mso_hook_add('admin_init', 'taggallery_admin_init'); # хук на админку
	  mso_hook_add( 'admin_head', 'taggallery_admin_head');
 //   mso_hook_add( 'content_end', 'taggallery_content_end');
   // mso_hook_add( 'init', 'taggallery_init');
  //  mso_register_widget('taggallery_widget_tags', t('Облако галерей', 'plugins')); # регистрируем виджет
 //   mso_register_widget('taggallery_widget_pictures', t('Вывод картинок', 'plugins')); # регистрируем виджет
 
}

function taggallery_admin_head($args = array()) 
{
	#echo mso_load_jquery('ui/ui.core.packed.js');
	#echo mso_load_jquery('ui/ui.draggable.packed.js');
	echo mso_load_jquery('alerts/jquery.alerts.js');
	echo mso_load_jquery('cornerz.js');
	echo '	<link href="' . getinfo('common_url') . 'jquery/alerts/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen">';
	return $args;
}

function taggallery_init($args=false) 
{

 return $args;
}


function taggallery_custom_page_404($args=false)
{
 $options = mso_get_option('taggallery', 'plugins', array());
 
 
 $plugin_dir = getinfo('plugins_dir') . 'taggallery/';
 require($plugin_dir . 'options_default.php');
 

 $segment1 = mso_segment(1);
 
 if ( in_array($segment1 , array($options['main_slug'] , $options['gallery_slug'] , $options['picture_slug'] , $options['album_slug'])) )
 {
   $siteurl = getinfo('siteurl');
   $uploads_url = $siteurl . 'uploads/';
   $plugin_url = getinfo('plugins_url') . 'taggallery/';
   $template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
   $template_url = $plugin_url . 'templates/' . $options['template'] . '/';
   $fn_template_index = $template_dir . '/index.php';
   if (file_exists($fn_template_index)) require($fn_template_index); 
   else return $args;
   
   return true;
 }
 
 return $args;
}



# функция выполняется при активации (вкл) плагина
function taggallery_activate($args = array())
{    
    // создадим БД
    require (getinfo('plugins_dir') . 'taggallery/functions/create_db.php');
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function taggallery_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function taggallery_uninstall($args = array())
{
    mso_delete_option('taggallery', 'plugins'); // удалим созданные опции
    mso_delete_option('taggallery_admin', 'plugins'); // удалим созданные опции
    
    mso_delete_float_option('taggallery_picture_pages', 'taggallery'); // удалим флоат-опции
    mso_delete_option_mask('taggallery_widget_', 'plugins'); // удалим созданные опции виджетов

	// удалим таблицы
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('pictures');
	$CI->dbforge->drop_table('gallerys');
	$CI->dbforge->drop_table('albums');
	$CI->dbforge->drop_table('galalb');
	$CI->dbforge->drop_table('picgal');
	$CI->dbforge->drop_table('source');
	
    return $args; 
}

function taggallery_custom($response = '')
{    
     return $args;
}
  

// функция выводит после контента одиночной страницы ссылку на галереи по меткам страницы, если они есть
function taggallery_content_end($args = array())
{

 $options = mso_get_option('taggallery', 'plugins', array());
// require(getinfo('plugins_dir') . 'taggallery/data-access.php'); // подключим функции галереи

 if (is_type('page') 
 and isset($options['gallery_content_end'])
 and $options['gallery_content_end'] 
 and isset ($options['gallery_slug'])
 and isset ($taggallery_array))
 {

      global $page;
     //получим все существующие галереи, метки которых совпадают с метками этой страницы
     global $MSO;
      
      
     $par['vid'] = 'shot';
     $par['hash_tag'] = true;
     $tags_array =  taggallery_get_tags($par);
     $gallery_url = $MSO->config['site_url'] . $options['gallery_slug'] . '/';
     $out = '';
      foreach ($page['page_tags'] as $page_tag)
      {
        if (isset($tags_array[mso_slug($page_tag)]))
        {
           $out .=  '<a href="' . $gallery_url . mso_slug($page_tag)  . '" rel="tag">' . $page_tag  . '(' . $tags_array[mso_slug($page_tag)]['count'] . ') </a> ';
        } 
      }

     $bl_other_page_title = 'Галереи изображений по теме: ';
     if ($out) echo ('<div class="page_other_pages"><h3>' . $bl_other_page_title . $out . '</h3></div>');
    
	
 }	
return $args;
}
  
  # при входе в админку
function taggallery_admin_init($args = array()) 
 {
     if ( !mso_check_allow('taggallery_edit') ) return $args;
  
     $this_plugin_url = 'taggallery'; // url и hook 
     
     # добавляем свой пункт в меню админки
     # первый параметр - группа в меню
     # второй - это действие/адрес в url - http://сайт/admin/demo
     # Третий - название ссылки    
     mso_admin_menu_add('plugins', $this_plugin_url, 'taggallery');
  
     # прописываем для указаного url
     # связанную функцию именно она будет вызываться, когда 
     # будет идти обращение по адресу http://сайт/admin/demo
     mso_admin_url_hook ($this_plugin_url, 'taggallery_admin_page');
     
     return $args;
 }
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function taggallery_admin_page($args = array()) 
 {
  //   global $MSO;
     if ( !mso_check_allow('taggallery_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "taggallery"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "taggallery - " . $args; ' );
  
     # выносим админские функции отдельно в файл    
  //    require($MSO->config['plugins_dir'] . 'taggallery/admin.php');
    $plugin_dir = getinfo('plugins_dir') . 'taggallery/';
    require($plugin_dir . 'admin/index.php');
 }
 
 




 // Виджет облака меток галерей_______________________________________________________________________________________
 
# функция, которая берет настройки из опций виджетов
function taggallery_widget_tags($num = 1) 
{
	$widget = 'taggallery_widget_tags_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return taggallery_widget_tags_custom($options, $num);
}
 
 
 
 # форма настройки виджета 
# имя функции = виджет_form
function taggallery_widget_tags_form($num = 1) 
{
	$widget = 'taggallery_widget_tags_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
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
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
		
		
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	$form .= '<br><div class="t150">&nbsp;</div> %SIZE% %URL% %TAG% %COUNT%';
	
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
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function taggallery_widget_tags_update($num = 1) 
{
	$widget = 'taggallery_widget_tags_' . $num; // имя для опций = виджет + номер
	
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

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function taggallery_widget_tags_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'taggallery_widget_tags_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
//	if ($k) return $k; // да есть в кэше
	
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
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
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
		

  $par['vid'] = 'shot';
  $par['hash_tag'] = false;
  $tags_array =  taggallery_get_tags($par);
     
	
	$gallery_options = mso_get_option('taggallery', 'plugins', array());
	if ( !isset($gallery_options['gallery_slug']) ) $gallery_options['gallery_slug'] = 'pictures';	
	
	
  $url = getinfo('siteurl') . $gallery_options['gallery_slug'] . '/';
  $tagcloud = array();
	foreach ($tags_array as $tag => $gallery)  $tagcloud[$tag] = $gallery['count'];
	
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
    
 
    $out = '';
    $i = 0;
    foreach ($tagcloud as $tag => $count) 
    {
		if ($min_count) 
			if ($count < $min_count) continue;

		$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
		$af = str_replace(array('%SIZE%', '%URL%', '%TAG%', '%COUNT%'), 
							array($font_size, $url . urlencode($tag), $tags_array[$tag]['name'], $count), $options['format']);
		
		// альтернативный синтаксис с []
		$af = str_replace(array('[SIZE]', '[URL]', '[TAG]', '[COUNT]'), 
							array($font_size, $url . urlencode($tag), $tags_array[$tag]['name'], $count), $af);

		$out .= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}
 
// Виджет вывода изображений ------------------------------------------------------------------------ 
# функция, которая берет настройки из опций виджетов
function taggallery_widget_pictures($num = 1) 
{
	$widget = 'taggallery_widget_pictures_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return taggallery_widget_pictures_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function taggallery_widget_pictures_form($num = 1) 
{
	$widget = 'taggallery_widget_pictures_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['tag']) ) $options['tag'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['width']) ) $options['width'] = '180';
	if ( !isset($options['img_class']) ) $options['img_class'] = '';
	if ( !isset($options['sort_order']) ) $options['sort_order'] = '';
	if ( !isset($options['sort']) ) $options['sort'] = 'random';
	if ( !isset($options['gallery_keys']) ) $options['gallery_keys'] = '';
	if ( !isset($options['do']) ) $options['do'] = '';
	if ( !isset($options['posle']) ) $options['posle'] = '';
  if ( !isset($options['podpis']) ) $options['podpis'] = false;
  if ( !isset($options['type']) ) $options['type'] = 'custom';
//	if ( !isset($options['gallery_keys']) ) $options['gallery_keys'] = '';
	if ( !isset($options['tag']) ) $options['tag'] = '';
	
	// подготовим список меток для выпадающего списка
	 $par['hash_tag'] = true; // служебные метки включительно
   $par['vid'] = 'shot'; //короткий вид, 'long' - полный вид
   $tags = taggallery_get_tags($par);
   $tags_drop = array();
   foreach ($tags as $key => $tag) $tags_drop[$key] = $tag['name'];

//   $gallery_keys = taggallery_get_gallerys();
//   $gallery_keys[] = '';
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Метка альбома:', 'plugins') . '</div> '. form_dropdown( $widget . 'tag', $tags_drop, $options['tag']) . '</p>';

//	$form .= '<p><div class="t150">' . t('Галерея (если оставить пустым то все):', 'plugins') . '</div> '. form_dropdown( $widget . 'gallery_keys', $gallery_keys, $options['gallery_keys']) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('До картинки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'do', 'value'=>$options['do'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('После картинки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'posle', 'value'=>$options['posle'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('До блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'do_blok', 'value'=>$options['do_blok'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('После блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'posle_blok', 'value'=>$options['posle_blok'] ) ) . '</p>' ;

	$form .= '<p><div class="t150">' . t('Подпись:', 'plugins') . '</div> '. form_checkbox( array( 'name'=>$widget . 'podpis', 'value'=>$options['podpis'] ) ) . '</p>' ;	
	
	$form .= '<p><div class="t150">' . t('img_class:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'img_class', 'value'=>$options['img_class'] ) ) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Ширина картинки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) )  . '</p>';
	
	$form .= '<p><div class="t150">' . t('Сортировка по:', 'plugins') . '</div> '. form_dropdown( $widget . 'sort', 
		array(
			'random'=>'Случайно', 
			'file'=>'По именам файлов', 
			'desc'=>'По описанию',
			'date'=>'По времени создания файлов',
			'rating'=>'По рейтингу',
			'width' =>'По ширине',
			'height' =>'По высоте',
			'type' =>'По положению',
			'no'=>'Не сортировать',
			
			), $options['sort']) . '</p>';
			
	$form .= '<p><div class="t150">' . t('Порядок сортировки:', 'plugins') . '</div> '. form_dropdown( $widget . 'sort_order', 
		array(
			'asc'=>'Прямой порядок', 
			'desc'=>'Обратный порядок', 
			
			), $options['sort_order']) . '</p>';	
			
	
	$form .= '<p><div class="t150">' . t('Ссылка с миниатюры:', 'plugins') . '</div> '. form_dropdown( $widget . 'type', 
		array(
			'single'=>'На страницу одиночной картинки', 
			'custom'=>'На файл картинки', 
			
			), $options['type']) . '</p>';					
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function taggallery_widget_pictures_update($num = 1) 
{
	$widget = 'taggallery_widget_pictures_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['tag'] = mso_widget_get_post($widget . 'tag');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['img_class'] = mso_widget_get_post($widget . 'img_class');
	$newoptions['sort_order'] = mso_widget_get_post($widget . 'sort_order');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
//	$newoptions['gallery_keys'] = mso_widget_get_post($widget . 'gallery_keys');
	$newoptions['do'] = mso_widget_get_post($widget . 'do');
	$newoptions['posle'] = mso_widget_get_post($widget . 'posle');
	$newoptions['podpis'] = mso_widget_get_post($widget . 'podpis');
	$newoptions['type'] = mso_widget_get_post($widget . 'type');
	
	$newoptions['do_blok'] = mso_widget_get_post($widget . 'do_blok');
	$newoptions['posle_blok'] = mso_widget_get_post($widget . 'posle_blok');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


# функция плагина
function taggallery_widget_pictures_custom($options = array(), $num = 1)
{
//  $gallery_keys = mso_strip($options['gallery_keys']);
	$p = false;
  if (!isset($options['tag'])) $options['tag'] = '';
  if (!isset($options['do_blok'])) $options['do_blok'] = '';
  if (!isset($options['posle_blok'])) $options['posle_blok'] = '';
  if (isset($options['width']) and $options['width']) $options['html_adds'] = ' width="' . $options['width'] . '" '; //ширина миниатюры заносится в добавку к html картинки

  if (!isset($options['gallery_url']))
  {
    $plug_options = mso_get_option('taggallery', 'plugins', array());  
    global $MSO;
    $options['gallery_url'] = $MSO->config['site_url'] . $plug_options['gallery_slug'] . '/';
  }
    
  $gallery = taggallery_get_pictures_out($options['tag'] , $options , $p );
  if ( isset($gallery['pictures']) )
  {
     $out = $options['do_blok'] . $gallery['pictures'] . $options['posle_blok'];
     if ($options['header']) $out = $options['header'] . $out;
  }   
  else $out = '';
	return $out;	
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function taggallery_mso_options() 
{
	# для каждого шаблона галереи можно задать свои опции
	# настраиваем через стандартный  mso_admin_plugin_options
	# только опции передаем в зависисмоти от сегмента
	//сайт/admin/plugin_options/taggallery/template/template_name/
	
	if  (mso_segment(4) == 'templates') 
	  if ( $template = mso_segment(5) )
  	{
		// смотрим расположение опций - они находятся в файле options.php указанного шаблона
		$fn = getinfo('plugins_dir') . 'taggallery/templates/'. $template .'/options.php';
		if (file_exists($fn)) require($fn);
			else
			{
				# ошибочный файл
				mso_admin_plugin_options('modules', 'modules', 
					array(),
					'Ошибочный файл опций шаблона', // титул
					'Отстствует файл опций шаблона.' // инфо
				);
			}

	  }
	  else
	  {
	    	mso_admin_plugin_options('modules', 'modules', 
			  array(),
			  'Шаблон галереи', // титул
			  'Настройки шаблона отсутствуют' // инфо
		    );
		
	  }
	else 
	{
	
	}  
}

  
?>