<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Dmitry Gushin
 * http://gushin.su/
 *
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function last_page_text_autoload($args = array())
{
	mso_register_widget('last_page_text_widget', 'Тескт последней статьи'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function last_page_text_uninstall($args = array())
{	
	mso_delete_option_mask('last_page_text_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function last_page_text_widget($num = 1) 
{
	$widget = 'last_page_text_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return last_page_text_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function last_page_text_widget_form($num = 1) 
{
	$widget = 'last_page_text_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['len']) ) $options['len'] = 30;
	if ( !isset($options['link_name']) ) $options['link_name'] = 'Далее...';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">Количество слов:</div> '. form_input( array( 'name'=>$widget . 'len', 'value'=>$options['len'] ) ) ;	
	$form .= '<p><div class="t150">Текст для ссылки на страницу:</div> '. form_input( array( 'name'=>$widget . 'link_name', 'value'=>$options['link_name'] ) ) ;	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_page_text_widget_update($num = 1) 
{
	$widget = 'last_page_text_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['len'] = mso_widget_get_post($widget . 'len');
	$newoptions['link_name'] = mso_widget_get_post($widget . 'link_name');
		
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function last_page_text_widget_custom($options = array(), $num = 1)
{
global $MSO;
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['len']) ) $options['len'] = 30;
	if ( !isset($options['link_name']) ) $options['link_name'] = 'Далее...';	
		
// параметры для получения страниц

$par = array( 'cut'=>false, 'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=>false, 'limit' => 1, 'content'=> 1, 'page_id'=> 0, 'custom_type'=> 'home', 'pagination'=>false ); 

$pages = mso_get_pages($par, $pagination); // получим все			

if ($pages) // есть страницы
{ 		
$page  = $pages["0"];  

  //Обрезаем пост
$out = '<h2 class="box"><span>' . $page["page_content"] . '</span></h2>';
  $out = trim(strip_tags($out));
 
// pr(mso_get_all_tags_page($page));
 //mso_page_title($page["page_slug"], $options['link_name'], '', '', true);
  $words = explode(" ",$out);
  $out = $options['header'];
  if($options['len'] <= count($words))
    {$j = $options['len'];}
  else
    {$j = count($words);}	
  for($i=0; $i < $j; $i++)
   {
    $out .= $words[$i] . " ";
   }

//формируем ссылку
$link = '<a href="' . $MSO->config['site_url'] . 'page/' . $page["page_slug"] . '" title="' . mso_strip($options['link_name']) . '">' . $options['link_name'] . '</a>';
$out .= "  " . $link;

}	
	return $out;	
}

?>