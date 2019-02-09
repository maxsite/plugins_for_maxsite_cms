<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function my_page_parent_autoload($args = array())
{
	mso_register_widget('my_page_parent_widget', t('Родительские страницы', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function my_page_parent_uninstall($args = array())
{	
	mso_delete_option_mask('my_page_parent_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function my_page_parent_widget($num = 1) 
{
	$widget = 'my_page_parent_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return my_page_parent_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function my_page_parent_widget_form($num = 1) 
{
	$widget = 'my_page_parent_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['page_id']) ) $options['page_id'] = '';
	if ( !isset($options['out']) ) $options['out'] = 'static';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Номер страницы:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'page_id', 'value'=>$options['page_id'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Типы страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'out', 'value'=>$options['out'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function my_page_parent_widget_update($num = 1) 
{
	$widget = 'my_page_parent_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['page_id'] = mso_widget_get_post($widget . 'page_id');
	$newoptions['out'] = mso_widget_get_post($widget . 'out');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}




# функции плагина
function my_page_parent_widget_custom($options = array(), $num = 1)
{
	// кэш не нужен, потому что mso_page_map сама всё кэширует
        global $page;
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['page_id']) ) $options['page_id'] = 0;
	if ( !isset($options['out']) ) $options['out'] = 'static';
	$page_id = $page['page_id'];
	
	$out = mso_explode($options['out'], false, false);//сформируем массив типов для выдачи

	$r = mso_page_from($options['page_id'], $page_id, $out); // построение карты страниц
	
	// создание ul-списка со своими опциями
	if ($r) $out = mso_create_list($r, array('format_current'=>'[LINK][TITLE][/LINK]',
									'class_ul'=>'is_link page_parent', 
									'class_child'=>'is_link page_parent_child', 'current_id'=>true ) ); 
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	return $out;	
}

# вспомогательная рекурсивная функция для mso_page_from
function mso_page_from_child( $page_id_parent = 0, $cur_id = 0, $out = array() )
{       
	$CI = & get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug');
	$CI->db->where('page_id_parent', $page_id_parent);
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));

	foreach ($out as $type_out)
	{
	  $CI->db->where('page_type_name', $type_out);
	}
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->order_by('page_menu_order');
	
	$query = $CI->db->get('page');

	$result = $query->result_array(); // здесь все страницы указанного типа

	$r = array();
	if ($result)
	{
	  foreach ($result as $key=>$row)
	  {
	    $k = $row['page_id'];
	    $ch = mso_page_from_child($row['page_id'], $cur_id, $out);
             
	    $r[$k] = $row;
	    if ($k == $cur_id) $r[$k]['current'] = 1;
	    if ($ch) $r[$k]['childs'] = $ch;  
	    else 
	    { 	    
  	     $way = way_to($cur_id);
  	     if (in_array($k , $way))  $r[$k]['current'] = 1;
  	    }     
	  }
		
	}

	return $r;
}




# вывод карты страниц по паренту от конкретной страницы, содержащей только страницы указанного типа
# функция ресурсоемкая!
function mso_page_from($page_id_parent = 0, $cur_id = 0, $out = array() )
{
	$cache_key = 'mso_page_from' . $cur_id . '-' . $page_id_parent;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
        $r = array();
        
	$CI = & get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug', 'page_type_id');
	$CI->db->where('page_id_parent', $page_id_parent);
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	
	foreach ($out as $type_out)
	{
	  $CI->db->where('page_type_name', $type_out); //заносим типы страниц, присутствующие в результате
	}
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->order_by('page_menu_order');
	$query = $CI->db->get('page');
	$result = $query->result_array(); 
        
        $r = array();
        if ($result)
	  foreach ($result as $key=>$row)
	  {
	    $k = $row['page_id'];
	    $ch = mso_page_from_child($row['page_id'], $cur_id, $out);
	    if ($k == $cur_id) $r[$k]['current'] = 1;
	    $r[$k] = $row;
	    if ($ch) $r[$k]['childs'] = $ch;  
	    else // иначе определим, есть ли текущая среди неотображаемых дочерних
	    { 	    
  	     $way = way_to($cur_id); //определяем массив страниц, через которые попадаем к текущей странице
  	     if (in_array($k , $way))  $r[$k]['current'] = 1; 
  	     //если добавляемая сейчас страница не имеет добавляемых дочерних 
  	     //и является путем к текущей странице
  	     //то отмечаем ее, как содержащую текущую
  	    }      
		   
	  }
	

	// pr($k);
	// pr($r);


	mso_add_cache($cache_key, $r); // в кэш

	return $r;
}

function way_to ($page_id = 0) //возращает путь к странице через связи дочерние-родительские страницы
{
  
  $cache_key = 'way_to' . $page_id;
  $k = mso_get_cache($cache_key);
  if ($k) return $k; // да есть в кэше
 
  $r = array();
  $r[] = $page_id;
  $CI = & get_instance();
  $CI->db->select('page_id, page_id_parent');
  $CI->db->where('page_id', $page_id);
  $CI->db->order_by('page_menu_order');
  $query = $CI->db->get('page');
  $result = $query->result_array(); 

  if ($result)
  {
    foreach ($result as $key=>$no)
    {
      $parents = way_parents($no['page_id_parent']);
      foreach ($parents as $parent)
         $r[] = $parent;
    }  
  }
  mso_add_cache($cache_key, $r); // в кэш
  return $r;
}

function way_parents ($page_id = 0) //рекурентная для way_to
{
  $r = array();
  $r[] = $page_id;
  $CI = & get_instance();
  $CI->db->select('page_id, page_id_parent');
  $CI->db->where('page_id', $page_id);
  $CI->db->order_by('page_menu_order');
  $query = $CI->db->get('page');
  $result = $query->result_array(); 

  if ($result)
  {
    foreach ($result as $key=>$no)
    {
      if ($no['page_id_parent']>0)
        { 
         $parents = way_parents($no['page_id_parent']);
         foreach ($parents as $parent)
         $r[] = $parent;
        } 
    }  
  }
  return $r;
}


?>