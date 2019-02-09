<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function random_pages_img_autoload($args = array())
{
	mso_register_widget('random_pages_img_widget', t('Случайные статьи с картинками', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function random_pages_img_uninstall($args = array())
{	
	mso_delete_option_mask('random_pages_img_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function random_pages_img_widget($num = 1) 
{
	$widget = 'random_pages_img_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return random_pages_img_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function random_pages_img_widget_form($num = 1) 
{
	$widget = 'random_pages_img_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Тип страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ) ;

	$form .= '<p><div class="t150">' . t('Ширина превьюшки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'src_width', 'value'=>$options['src_width'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function random_pages_img_widget_update($num = 1) 
{
	$widget = 'random_pages_img_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['src_width'] = mso_widget_get_post($widget . 'src_width');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function random_pages_img_widget_custom($options = array(), $num = 1)
{
  require_once( getinfo('common_dir') . 'meta.php' );
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 1;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['src_width']) ) $options['src_width'] = '150';
	
	$CI = & get_instance();
	
	$CI->db->select('page_id, page_slug, page_title , page_content');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('page_status', 'publish');
	if ($options['page_type']) $CI->db->where('page_type_name', $options['page_type']);
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->from('page');
	$CI->db->order_by('page_id', 'random');
	$CI->db->limit($options['count']);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		$link = '<a href="' . getinfo('siteurl') . 'page/' . NR;
		if ($options['count'] > 1) $out .= '<ul class="is_link random_pages">' . NR; //Если выводим более 1-й случайной статьи - формируем их в виде списка
		foreach ($pages as $page) 
		{
		
      if ($options['count'] > 1) $out .= '<li>' . $link . $page['page_slug'] . '">' . $page['page_title'] . '</a>';	
			                      else $out .= $link . $page['page_slug'] . '">' . $page['page_title'] . '</a>' . NR;//Если выводим  1 статью - список не нужен
  		$meta = 'prev';
  	  $page_meta = mso_get_meta($meta,'page', $page['page_id']);
 	    if (isset($page_meta) and $page_meta) //если превьюшка присвоена в метаполе, используем ее
		  {
		    foreach ($page_meta as $src_meta) $prev = $src_meta['meta_value'];
		    $image = "<img src = " . $prev . " width =" . $options['src_width'] . " >";
		  }
      else //если превьюшка не присвоена в метаполе, пытаемся найти картинку в контенте
      {
       $content=$page['page_content'];
       $prev = stristr($content, "img");
	     if ($prev)
       {
	       $prev2 = stristr($prev, "http");
	       if ($prev) 
	       {
	         $num = explode('"', $prev2);
           if ($num[0])
           {
            $prev = $num[0];
           }
        }
       } 
      } 
  	  if (isset($prev) and $prev)
  	  {
  	    $image = "<img src = " . $prev . " width =" . $options['src_width'] . " >";
  	    $out .= $image;
  	  }  
		}
  	if ($options['count'] > 1) 	
  	{
      $out .= '</li>' . NR;
  	  $out .= '</ul>' . NR;
  	}  
		if ($options['header']) $out = $options['header'] . $out;

	}
	return $out;	
}

?>