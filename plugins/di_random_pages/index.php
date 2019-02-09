<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
// функция попробует взять картинку из  метаполе и все.
function di_random_pages_get_pictures ($arg = array() )
{
  $picture = '';


  // если задано 'page_id' и указано 'meta_name'
  if ( isset($arg['page_id']) and isset($arg['meta_name']) )
  {
        require_once( getinfo('common_dir') . 'meta.php' );
  	    $page_meta = mso_get_meta($arg['meta_name'] ,'page', $arg['page_id']);
 	      if (isset($page_meta) and $page_meta) //если превьюшка присвоена в метаполе, используем ее
		   {
		      foreach ($page_meta as $src_meta) $picture = $src_meta['meta_value'];
		      if ($picture) return $picture;
		   }

  }


  return;

}



# функция автоподключения плагина
function di_random_pages_autoload($args = array())
{
  mso_register_widget('di_random_pages_widget', t('Случайные статьи с картинками', 'plugins')); # регистрируем виджет
  mso_hook_add('head', 'di_random_pages_head');
}

function di_random_pages_head($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'di_random_pages/css/style.css">';
	return $args;
}

# функция выполняется при деинсталяции плагина
function di_random_pages_uninstall($args = array())
{
	mso_delete_option_mask('di_random_pages_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function di_random_pages_widget($num = 1)
{
	$widget = 'di_random_pages_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';

	return di_random_pages_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function di_random_pages_widget_form($num = 1)
{
	$widget = 'di_random_pages_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['meta_name']) ) $options['meta_name'] = 'prev';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;

	$form .= '<p><div class="t150">' . t('Тип страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ) ;

	$form .= '<p><div class="t150">' . t('Мета-поле превьюшки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'meta_name', 'value'=>$options['meta_name'] ) ) ;


	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function di_random_pages_widget_update($num = 1)
{
	$widget = 'di_random_pages_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['meta_name'] = mso_widget_get_post($widget . 'meta_name');



	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function di_random_pages_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 1;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['src_dir']) ) $options['src_dir'] = getinfo('uploads_dir');
	if ( !isset($options['meta_name']) ) $options['meta_name'] = 'prev';


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

		$link = '<a href="' . getinfo('siteurl') . 'page/';


		$out .= '<ul class="di_random_pages">' . NR;

		foreach ($pages as $page)
		{
	        $arg = array(
  	    	'meta_name' => $options['meta_name'],
  	    	'page_id'=> $page['page_id'],
  	    	'page_slug'	=> $page['page_slug'],);

            $prev = di_random_pages_get_pictures($arg);

            //если пусто
            if($prev !='')
  	        {
              $prev = '<img src="' . $prev  . '" alt="' . $page['page_title']. '" class="sidebar_img">';
  	        }
  	        else
  	        {
  	           $prev = '<img src="' . getinfo('plugins_url') . 'di_random_pages/img/nophoto.jpg" alt="' . $page['page_title']. '" class="sidebar_img">';
  	        }

            // pr($prev);
		   	$out .= '<li>' . $link . $page['page_slug'] . '">' . $prev . ' ' . $page['page_title'] . '</a></li>';
		}

	    $out .= '</ul>' . NR;


		if ($options['header']) $out = $options['header'] . $out;

	}
	return $out;

}