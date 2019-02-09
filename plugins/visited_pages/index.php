<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function visited_pages_autoload($args = array())
{
	mso_register_widget('visited_pages_widget', t('Посещенные страницы')); # регистрируем виджет
	mso_hook_add( 'body_end', 'visited_pages_listing');
}

# функция выполняется при активации (вкл) плагина
function visited_pages_activate($args = array())
{
	//$path_to_image=getinfo('plugins_dir').'/visited_pages/default_pict.jpg';
	copy(getinfo('plugins_dir').'/visited_pages/default_pict.jpg',getinfo('uploads_dir').'default_pict_for_visited_pages.jpg');
	return $args;
}

# функция выполняется при деинсталяции плагина
function visited_pages_uninstall($args = array())
{
	mso_delete_option_mask('visited_pages_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# формирование спика посещенных страниц
function visited_pages_listing($arg = array()) 
{
	
	$stat = mso_get_float_option('last_pages', 'last_pages', array());
    
	$sess=getinfo('session');
    $sess_id=$sess['session_id'];
	
	$type='';
	$picture=NULL;
	$url = mso_current_url(true);
	
	//удаляем все устаревшие данные по посещенным страницам, период  - час
	foreach ($stat as $key=>$sess_info)
		{
			$max_sess_time=0;
			foreach ($sess_info as $page_info)
			{
				if ( strtotime($page_info['time'])>$max_sess_time )
				$max_sess_time=strtotime($page_info['time']);
			}
			
			if ($max_sess_time+3600<strtotime(date('Y-m-d H:i:s')))
			{
				unset($stat[$key]);
				//echo ($max_sess_time+60-strtotime(date('Y-m-d H:i:s')));
			}
			
		}
	
	//Ограничиваем список посещенных страниц  - пока category и page, ну и главная	
	if ((!is_type('page')) and (!is_type('category')) and (!is_type('home')))
	return $arg;
	
	
	
	//тип - страинца//
	if (is_type('page'))
	{
		$CI = & get_instance();
		
		$CI->db->from('mso_page'); 
		$CI->db->select('page_title');
		$CI->db->where( array('page_slug'=>mso_segment(2)) );
		$query = $CI->db->get();

		if (!$query or $query->num_rows() == 0) 
		{
			$title='не найдено';
		}
		else
		{
			// есть что-то
			$row = $query->row();
			$title= $row->page_title;
		}
		$type='page';
		
		
		// получаем картинку для статьи если она есть
		$CI->db->select('meta_value');
		$CI->db->where( array (	'meta_table' => 'page', 'meta_key' => 'image_for_page'  ) );
		$CI->db->from('meta');
	    $CI->db->join('page', 'meta.meta_id_obj = page.page_id','right');
	    $CI->db->where(array ('page.page_slug' => mso_segment(2)));
		
		$query = $CI->db->get();

		if (!$query or $query->num_rows() == 0) 
		{
			$picture=NULL;
		}
		else
		{
			$row = $query->row();
			$picture= $row->meta_value;
		}
			
		
	}
	//тип - категория//
	elseif (is_type('category'))
	{
		$CI = & get_instance();
		
		$CI->db->from('mso_category'); 
		$CI->db->select('category_name');
		$CI->db->where( array('category_slug'=>mso_segment(2)) );
		$query = $CI->db->get();

		if (!$query or $query->num_rows() == 0) 
		{
			$title='не найдено';
		}
		else
		{
			// есть что-то
			$row = $query->row();
			$title= $row->category_name;
		}
		$type='category';
	}
	//тип - домашняя страница//
	elseif (is_type('home'))
	{
		$title='Главная страница';
		$type='home';
	}
	
	$stat[$sess_id][] = 
	array (
	'url'=>$url,
	'title'=>$title,
	'type'=>$type,
	'pict'=>$picture,
	'time'=>date('Y-m-d H:i:s')
	); 
	 
	mso_add_float_option('last_pages', $stat, 'last_pages'); 

	return $arg;
			
}

# форма настройки виджета
# имя функции = виджет_form
function visited_pages_widget_form($num = 1)
{
	$widget = 'visited_pages_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = 'Вы недавно смотрели:';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
		else $options['limit'] = (int) $options['limit'];
	if ( !isset($options['item_width']) ) $options['item_width'] = 50;
		else $options['item_width'] = (int) $options['item_width'];
	if ( !isset($options['item_height']) ) $options['item_height'] = 50;
		else $options['item_height'] = (int) $options['item_height'];
	//if ( !isset($options['exp_time']) ) $options['exp_time'] = 3600;
	//	else $options['exp_time'] = (int) $options['exp_time'];	
	if ( !isset($options['flat_list']) ) $options['flat_list'] = false;	
	//if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A]';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'] ) ), '');
	
	$form .= mso_widget_create_form(t('Ширина значка посещенной страницы'), form_input( array( 'name'=>$widget . 'item_width', 'value'=>$options['item_width'] ) ), '');
	
	$form .= mso_widget_create_form(t('Высота значка посещенной страницы'), form_input( array( 'name'=>$widget . 'item_height', 'value'=>$options['item_height'] ) ), '');
	
	$form.= mso_widget_create_form('', form_checkbox( array( 'name'=>$widget . 'flat_list', 'value' => 'flat_list', 'checked' =>  $options['flat_list'] ) ) . ' ' . t('Отображение простым списком'));
	
	//$form .= mso_widget_create_form(t('Период "жизни" сохраненных данных сессии в секундах'), form_input( array( 'name'=>$widget . //'exp_time', 'value'=>$options['exp_time'] ) ), '');
	
	

	return $form;
}

# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function visited_pages_widget_update($num = 1)
{
	$widget = 'visited_pages_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = (int) mso_widget_get_post($widget . 'limit');
	$newoptions['item_width'] =(int) mso_widget_get_post($widget . 'item_width');
	$newoptions['item_height'] =(int) mso_widget_get_post($widget . 'item_height');
	//$newoptions['exp_time'] = (int) mso_widget_get_post($widget . 'exp_time');	
	$newoptions['flat_list'] = mso_widget_get_post($widget . 'flat_list');
	//$newoptions['format'] = mso_widget_get_post($widget . 'format');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


# функция, которая берет настройки из опций виджетов
function visited_pages_widget($num = 1) 
{
	$widget = 'visited_pages_widget_' . $num; // имя для формы и опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	return visited_pages_widget_custom($options);
}

function visited_pages_widget_custom($options = array())
{

	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 7;
		else $options['limit'] = (int) $options['limit'];
	if ( !isset($options['item_width']) ) $options['item_width'] = 50;
		else $options['item_width'] = (int) $options['item_width'];
	if ( !isset($options['item_height']) ) $options['item_height'] = 50;
		else $options['item_height'] = (int) $options['item_height'];
	//if ( !isset($options['exp_time']) ) $options['exp_time'] = 3600;
	//	else $options['exp_time'] = (int) $options['exp_time'];
	if ( !isset($options['flat_list']) ) $options['flat_list'] = false;	
	//if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A]>';
	
	
	//global $MSO;
	$stat = mso_get_float_option('last_pages', 'last_pages', array());
	
	$sess=getinfo('session');
	$sess_id=$sess['session_id'];
	
	//pr($previously_visited_pages);
	
	if (isset($stat[$sess_id]) and sizeof($stat[$sess_id])>0)
	{
		
		$stat[$sess_id]=array_reverse($stat[$sess_id]);
		//array_shift($stat[$sess_id]);
		$i = 1;
		
		foreach ($stat[$sess_id] as $page_info)
		{
			
				if ($i>$options['limit']) break; // лимит

				// $out1 = $options['format'];
				$out1 = '[A][TITLE][/A]';
				
				 $out1 = str_replace('[A]', '<a href="'.$page_info['url'].'">', $out1);
				 $out1 = str_replace('[/A]', '</a>', $out1);
				
			if ($options['flat_list'])
			{
				$out.= NR .'<li>'. str_replace('[TITLE]', $page_info['title'], $out1). '</li>'.NR;
			}
			else
			{
				if (isset($page_info['pict']))
				{
					$path_to_image=$page_info['pict'];
				}
				else
				{
					$path_to_image=getinfo('uploads_url').'default_pict_for_visited_pages.jpg';
					$out1.='<div style="position:absolute; width:100%; height:20px; top:'.($options['item_height']-20).'px;  overflow:hidden;"><span>'.$page_info['title'].'</span></div>';
				}
				
				
				$title = ' title="' . htmlspecialchars($page_info['title']) . '"';
				$alt = ' alt="' . htmlspecialchars($page_info['title']) . '"';
				$width = ' width="'.$options['item_width'].'"';
				$height = ' height="'.$options['item_height'].'"';
				
				if ($image_for_page = thumb_generate(
									$path_to_image, 
									$options['item_width'],
									$options['item_height']
								))
				{
				
					$out1 = str_replace('[TITLE]', '<img src="' . $image_for_page . '"' . $title . $alt . $width . $height . '>', $out1);					
				}
				else
				{
					$out1 = str_replace('[TITLE]', '<img src="' . $path_to_image . '"' . $title . $alt . $width . $height . '>', $out1);
				}
			
			
				$out .= '<div class="'.$page_info['type'].'_visited_pages" style="width:'.$options['item_width'].'px; height:'.$options['item_height'].'px;float:left; position:relative;">' . $out1 . '</div>' . NR;
			}	
				$i++;
		}
	}
	else 
		$out='';
	
	//pr($sess);
	
	

		if ($out)
		{
			$out= ($options['flat_list']) ? ('<ul class="is link visited_pages">'.$out.'</ul>') : ($out);
			if ($options['header']) $out = $options['header'] .NR . $out. NR;
			
		}
	
	//$url = mso_current_url(true);
	return ($options['flat_list']) ? ($out) : ('<div style="width:100%;background:none;"><div class="wrap">'.$out.'<div class="clearfix"></div></div></div>');
	
}



?>