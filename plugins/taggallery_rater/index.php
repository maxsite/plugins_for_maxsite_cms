<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function taggallery_rater_autoload($args = array())
{

	mso_hook_add( 'head', 'taggallery_rater_head');
	mso_hook_add( 'picture_end', 'taggallery_rater_content_end');
	
	mso_register_widget('taggallery_rater_widget', t('Рейтинг страниц', 'plugins')); # регистрируем виджет
}

function taggallery_rater_head($args = array())
{
	mso_load_jquery();
	
	$path = getinfo('plugins_url') . 'taggallery_rater/';
	echo '<script type="text/javascript" src="' . $path . 'jquery.rater.js"></script>' . NR;
	echo '	<link rel="stylesheet" href="' . $path . 'rater.css" type="text/css" media="screen">' . NR;
}

function taggallery_rater_content_end($arg = array())
{
	global $page;

	// pr($page);
	
//	if ($page['page_type_name'] !== 'blog') return $arg;
	
  $picture_id = mso_segment(2);
  $picture = taggallery_get_picture($picture_id);
    
	if ($picture['rating']>0)
		$curvalue = round($picture['rating'] / $picture['rating_count'] );
	else
		$curvalue = 0;
	
	if ($curvalue > 10) $curvalue = 10;
	if ($curvalue < 0) $curvalue = 0;
	

	
	$path = getinfo('ajax') . base64_encode('plugins/taggallery_rater/ratings-post-ajax.php');
	
	
	echo '
	<div id="rater" title="' . t('Текущая оценка:', 'plugins') . ' ' . $curvalue . '. ' 
		. t('Голосов:', 'plugins') . ' ' . $picture['rating_count'] 
		. '"><script type="text/javascript">
		$(\'#rater\').rater(\'' . $path 
		. '\', {maxvalue:10, style:\'basic\', curvalue:' . $curvalue . ', slug:\''. $picture_id . '\'});
	</script></div>
	';

	return $arg;
}

# функция выполняется при деинсталяции плагина
function taggallery_rater_uninstall($args = array())
{	
	mso_delete_option_mask('taggallery_rater_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function taggallery_rater_widget($num = 1) 
{
	$widget = 'taggallery_rater_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return taggallery_rater_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function taggallery_rater_widget_form($num = 1) 
{
	$widget = 'taggallery_rater_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Рейтинг страниц', 'plugins');
	if ( !isset($options['count']) ) $options['count'] = 10;
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[BALL]</sup>';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div><strong>[TITLE]</strong> - ' . t('название записи', 'plugins');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[COUNT]</strong> - ' . t('всего голосов', 'plugins');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[BALL]</strong> -  ' . t('общий бал (деление общего рейтинга на кол-во голосов) - округлен до целого', 'plugins');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[REALBALL]</strong> -  ' . t('общий бал (дробный)', 'plugins');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[A]</strong>' . t('ссылка', 'plugins') . '<strong>[/A]</strong>';

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function taggallery_rater_widget_update($num = 1) 
{
	$widget = 'taggallery_rater_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function taggallery_rater_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[BALL]</sup>';
	if ( !isset($options['count']) )  $options['count'] = 10;
	
	// TITLE - название записи 
	// COUNT - всего голосов page_rating_count
	// BALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - округлен до целого
	// REALBALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - дробный
	// [A]ссылка[/A]
	/*
	$CI = & get_instance();
	$CI->db->select('page_slug, page_rating/page_rating_count AS page_ball, page_rating, page_rating_count, page_title', false);
	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->order_by('page_ball', 'desc');
	$CI->db->order_by('page_rating', 'desc');
	// $CI->db->order_by('page_rating_count', 'desc');
	$CI->db->limit($options['count']);
	
	$query = $CI->db->get('page');

	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		$link = '<a href="' . getinfo('siteurl') . 'page/';
		
		$out .= '<ul class="is_link rater">' . NR;

		foreach ($pages as $page)
		{
			$out1 = $options['format'];
			$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
			$out1 = str_replace('[COUNT]', $page['page_rating_count'], $out1);
			$out1 = str_replace('[REALBALL]', (real) $page['page_ball'], $out1);
			$out1 = str_replace('[BALL]', (round((real) $page['page_ball'])), $out1);
			
			$out1 = str_replace('[A]', $link . $page['page_slug'] 
					. '" title="' . t('Голосов:', 'plugins') . ' ' . $page['page_rating_count'] 
					. ' ' . t('Общий бал:', 'plugins') . ' ' . (real) $page['page_ball']
					. '">'
					, $out1);
			$out1 = str_replace('[/A]', '</a>', $out1);
			
			$out .= '<li>' . $out1 . '</li>' . NR;
		}
		$out .= '</ul>' . NR;
		
		if ($options['header']) $out = $options['header'] . $out;
	}
	*/
	return $out;	
}

?>