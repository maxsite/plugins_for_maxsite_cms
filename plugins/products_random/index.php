<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function products_random_autoload($args = array())
{
	mso_register_widget('products_random_widget', t('Случайные товары и услуги', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function products_random_uninstall($args = array())
{	
	mso_delete_option_mask('products_random_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function products_random_widget($num = 1) 
{
	$widget = 'products_random_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return products_random_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function products_random_widget_form($num = 1) 
{
	$widget = 'products_random_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['product_type']) ) $options['product_type'] = 'product';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Тип страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'product_type', 'value'=>$options['product_type'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function products_random_widget_update($num = 1) 
{
	$widget = 'products_random_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['product_type'] = mso_widget_get_post($widget . 'product_type');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function products_random_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['product_type']) ) $options['product_type'] = 'product';
	
	$CI = & get_instance();
	
	$CI->db->select('product_slug, product_title');
	$CI->db->where('product_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('product_status', 'publish');
	if ($options['product_type']) $CI->db->where('product_type_name', $options['product_type']);
	$CI->db->join('product_type', 'product_type.product_type_id = product.product_type_id');
	$CI->db->from('product');
	$CI->db->order_by('product_id', 'random');
	$CI->db->limit($options['count']);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$products = $query->result_array();
		
		$link = '<a href="' . getinfo('siteurl') . 'product/';
		$out .= '<ul class="is_link products_random">' . NR;
		foreach ($products as $product) 
		{
			$out .= '<li>' . $link . $product['product_slug'] . '">' . $product['product_title'] . '</a>' . '</li>' . NR;
		}
		
		$out .= '</ul>' . NR;
		if ($options['header']) $out = $options['header'] . $out;
	}
	
	return $out;	
}

?>