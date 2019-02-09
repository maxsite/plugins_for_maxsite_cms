<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
// Функция по переданным параметрам страницы возвращает адрес файла картинки, привязанной к странице
// функция последовательно пытается обнаружить картинку в нескольких источниках
// если не получается, то устанавливается картинка по умолчанию
// в качестве аргумента можно передать такие необязательные параметры 'meta_name', 'page_id', 'dir', 'page_slug', 'page_content'
// функция сперва попробует взять картинку в метаполе, затем найти по id или slug в каталоге а затем найти первую попавшуюся в контенте
function pages_images_get_pictures ($arg = array() ) 
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


  // если задано 'page_id' или 'page_slug' и указан каталог где искать
	if ( (isset($arg['page_id']) or isset($arg['page_slug'])) and isset($arg['dir']) and trim($arg['dir']))
  {
     $CI = & get_instance();
     $CI->load->helper('directory');
     
     $allowed_ext = array('gif', 'jpg', 'jpeg', 'png');
     $all_dirs = directory_map($arg['dir'], true);
     if ($all_dirs)
     foreach ($all_dirs as $d)
     {
       if (!is_dir($d))
       {
		    	if ( (strpos($d , $arg['page_id']) !== false) or (strpos($d , $arg['page_slug']) !== false))// если находится файл по ключу
		    	{
		    	  $ext = strtolower(str_replace('.', '', strrchr($d, '.'))); // расширение файла
		    	  $picture = $arg['dir'] . $d;
		    	  if ( in_array($ext, $allowed_ext) )  // не запрещенный тип файла
				       return  $picture;     
          }
       }
     }   
  }
  
	
	
	// если передан 'page_content'
	if (isset ($arg['page_content']))
	{	    
         $prev = stristr($arg['page_content'], "img");
	       if ($prev)
         {
	         $prev2 = stristr($prev, "http");
	         if ($prev) 
	         {
	           $num = explode('"', $prev2);
             if (trim($num[0]))
             {
              return trim($num[0]);
             }
           }
          } 
   }
 
 return (getinfo('plugins_dir') . 'pages_images/custom.jpg' . NR);
  
}

function pages_images_get_pages ($arg = array() ) 
{
 // у нас будет два типа запросов - для случайных страниц и для последних

}

 // нам нужно получить аннотацию записи, тоесть запись до cut с уделенными картинками
function pages_images_clear_content ($arg = array() ) 
{


}

# функция автоподключения плагина
function pages_images_autoload($args = array())
{
	mso_register_widget('pages_images_widget', t('Случайные статьи с поиском превьюшек', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function pages_images_uninstall($args = array())
{	
	mso_delete_option_mask('pages_images_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function pages_images_widget($num = 1) 
{
	$widget = 'pages_images_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return pages_images_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function pages_images_widget_form($num = 1) 
{
	$widget = 'pages_images_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['src_width']) ) $options['src_width'] = '150';
	if ( !isset($options['src_dir']) ) $options['src_dir'] = getinfo('uploads_dir');
	if ( !isset($options['meta_name']) ) $options['meta_name'] = 'prev';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Тип страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ) ;

	$form .= '<p><div class="t150">' . t('Ширина превьюшки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'src_width', 'value'=>$options['src_width'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Директорий превьюшки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'src_dir', 'value'=>$options['src_dir'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Мета-поле превьюшки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'meta_name', 'value'=>$options['meta_name'] ) ) ;		
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function pages_images_widget_update($num = 1) 
{
	$widget = 'pages_images_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['src_width'] = mso_widget_get_post($widget . 'src_width');
	$newoptions['src_dir'] = mso_widget_get_post($widget . 'src_dir');
	$newoptions['meta_name'] = mso_widget_get_post($widget . 'meta_name');
	
	
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function pages_images_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 1;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['src_width']) ) $options['src_width'] = '150';
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

		//Если выводим более 1-й случайной статьи и без картинок - формируем их в виде списка
		if ( ($options['count']>1) and !($options['src_width']>0) ) $out .= '<ul class="is_link random_pages">' . NR; 
		foreach ($pages as $page) 
		{
		  //Если выводим  1 статью или статьи с картинками - список не нужен
      if ( ($options['count']>1) and !($options['src_width']>0) ) $out .= '<li>' . $link . $page['page_slug'] . '">' . $page['page_title'] . '</a>';	
			                      else $out .= $link . $page['page_slug'] . '">' . $page['page_title'] . '</a>' . NR;
      if ($options['src_width']>0) // если задана ненулевая ширина картинки то картинка нужна
      {
  	    	$arg = array(
  	    	'meta_name' => $options['meta_name'],
  	    	'page_id'=> $page['page_id'],
  	    	'page_content' =>  $page['page_content'],
  	    	'page_slug'	=> $page['page_slug'],
  	    	'dir'	=> $options['src_dir']);
          $prev = pages_images_get_pictures($arg);
   	      $out .= "<img src = " . $prev . " width =" . $options['src_width'] . " >";
  	   } 
		}
		
		
  	if ( ($options['count']>1) and !($options['src_width']>0) )	
  	{
      $out .= '</li>' . NR;
  	  $out .= '</ul>' . NR;
  	}  
		if ($options['header']) $out = $options['header'] . $out;

	}
	return $out;	
}

?>