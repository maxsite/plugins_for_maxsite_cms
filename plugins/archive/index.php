<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function archive_autoload($args = array())
{
	mso_register_widget('archive_widget', 'Архив'); 
}

function archive_uninstall($args = array())
{	
	mso_delete_option_mask('archive_widget_', 'plugins');
	return $args;
}

function archive_widget($num = 1)
{
	$widget = 'archive_widget_' . $num;
	$options = mso_get_option($widget, 'plugins', array() );

	return archive_widget_custom($options, $num);
}


function archive_widget_form($num = 1) 
{
	$widget = 'archive_widget_' . $num;
	
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Архив';
	if ( !isset($options['count']) ) $options['count'] = '5';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<div class="t150">' . t('Заголовок:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . '_header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<div class="t150">' . t('Количество записей:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . '_count', 'value'=>$options['count'] ) ) ;
	
	return $form;
}


function archive_widget_update($num = 1) 
{
	$widget = 'archive_widget_' . $num;
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . '_header');
	
	$newoptions['count'] = (int) mso_widget_get_post($widget . '_count');
	if ($newoptions['count'] < 1) $newoptions['count'] = 5;
	
	if ( $options != $newoptions ) mso_add_option($widget, $newoptions, 'plugins');
}

function archive_date_convert($date)
{
	if (!$date) return '';
	$df = 'F Y';
	$dd = t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье');
	$dm = t('Январь Февраль Март Апрель Май Июнь Июль Август Сентябрь Октябрь Ноябрь Декабрь');

	$out = mso_date_convert($df, $date, true, $dd, $dm);
	return $out;
}

function archive_widget_custom($options, $num)
{
	global $MSO;
	
	# оформление виджета
	if ( !isset($options['header']) ) $options['header'] = 'Архив';
	if ( !isset($options['count']) ) $options['count'] = 5;
	if ( !isset($options['block_start']) ) $options['block_start'] = '<ul class="widget_archive">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</ul>';

	$archive_widget = @archive_go($options['count']);
	if ($archive_widget) 
	{	
		return NR.'<h2 class="box"><span>'.$options['header'].'</span></h2>'.NR.$options['block_start'].NR.$archive_widget.$options['block_end'].NR;
	}
}


function archive_go($count = 5)
{	
	global $MSO;
	
	$cache_key = 'widget_archive_'.$count;
	$k = mso_get_cache($cache_key, true);
	if ($k) return $k;

	$out = '';
	$par = array( 
			'no_limit' => true,
			'custom_type' => 'home', 
			'content' => false
			); 
	$pages = mso_get_pages($par, $pagination);

	if ($pages)
	{
		$url = getinfo('siteurl').'archive/';
		$first = true;
		$i=1;
		foreach ($pages as $page)
		{
			$url_date = mso_date_convert('Y/m', $page['page_date_publish']);
			$date = archive_date_convert($page['page_date_publish']);
			if ($first && $i <= $count) 
			{
				$out .= '<li><a href="'.$url.$url_date.'" title="Записи за '.$date.' года">'.$date.'</a></li>'.NR;
				$first = false;
				$i++;
			}
			elseif ($url_date1 != $url_date && $i <= $count)
			{
				$out .= '<li><a href="'.$url.$url_date.'" title="Записи за '.$date.' года">'.$date.'</a></li>'.NR;
				$i++;
			}
			else {
				$out .='';
			}
			$url_date1 = $url_date;
		}
	}
	
	mso_add_cache($cache_key, $out, 600, true);

	return $out;
}

?>