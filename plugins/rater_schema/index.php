<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* MaxSite CMS
* (c) http://max-3000.com/
*
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*
*/


# функция автоподключения плагина
function rater_schema_autoload($args = array())
{


	if(is_type('page'))//только на страницах
	{
	    mso_hook_add('head', 'rater_schema_head');
		mso_hook_add('content_end', 'rater_content_end');
	}

	mso_register_widget('rater_schema_widget', t('Рейтинг страниц')); # регистрируем виджет
}

function rater_schema_head($args = array())
{
    $path = getinfo('plugins_url') . 'rater_schema/';

	//mso_load_jquery();
   echo '<script src="' . $path . 'js/jquery.rater.js"></script>' . NR;
   echo'<link rel="stylesheet" href="' . $path . 'css/rater.css">' . NR;
}

function rater_content_end($arg = array())
{
	global $page;


	if ($page['page_type_name'] !== 'blog') return $arg;
	if ($page['page_rating_count']>0)
		$curvalue = round($page['page_rating'] / $page['page_rating_count'] );
	else
		$curvalue = 0;

	if ($curvalue > 10) $curvalue = 10;
	if ($curvalue < 0) $curvalue = 0;

	$page_id = $page['page_id'];

	$path = getinfo('ajax') . base64_encode('plugins/rater_schema/ratings-post-ajax.php');
	$path_img = getinfo('plugins_url') . 'rater_schema/img/';

echo'<div class="rater_body" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">'.NR;
echo'<meta itemprop="bestRating" content="10">';
echo'<p><i class="i-star"></i>Общий бал: <span itemprop="ratingValue">' . $curvalue . '</span> <i class="i-users"></i>Проголосовало: <span itemprop="ratingCount">' . $page['page_rating_count']. '</span></p>';
echo'<div id="rater"><script>$(\'#rater\').rater(\'' . $path. '\', {maxvalue:10, style:\'basic\', curvalue:' . $curvalue . ', slug:\''. $page_id . '\'});</script></div><div class="break"></div>'.NR;
echo'</div>'.NR;

	return $arg;
}


# функция выполняется при деинсталяции плагина
function rater_schema_uninstall($args = array())
{
	mso_delete_option_mask('rater_schema_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function rater_schema_widget($num = 1)
{
	$widget = 'rater_schema_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header">') . $options['header'] . mso_get_val('widget_header_end', '</div>');
	else $options['header'] = '';

	return rater_schema_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function rater_schema_widget_form($num = 1)
{
	$widget = 'rater_schema_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = '<i class="i-star"></i> Рейтинг страниц';
	if ( !isset($options['count']) ) $options['count'] = 10;
	if ( !isset($options['format']) ) $options['format'] = '[A][IMG_RATER][TITLE] <sup>[BALL]</sup>[/A]';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div class="t150">' . t('Заголовок:') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Количество:') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;

	$form .= '<p><div class="t150">' . t('Формат:') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
    $form .= '<p><div class="t150">&nbsp;</div><strong>[IMG_RATER]</strong> - ' . t('выводить миниатюры');
	$form .= '<p><div class="t150">&nbsp;</div><strong>[TITLE]</strong> - ' . t('название записи');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[COUNT]</strong> - ' . t('всего голосов');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[BALL]</strong> -  ' . t('общий бал (деление общего рейтинга на кол-во голосов) - округлен до целого');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[REALBALL]</strong> -  ' . t('общий бал (дробный)');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[A]</strong>' . t('ссылка') . '<strong>[/A]</strong>';

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function rater_schema_widget_update($num = 1)
{
	$widget = 'rater_schema_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function rater_schema_widget_custom($options = array(), $num = 1)
{
     //require_once( getinfo('common_dir') . 'meta.php' );
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '<i class="i-star"></i> Рейтинг страниц';
	if ( !isset($options['format']) ) $options['format'] = '[A][IMG_RATER][TITLE]<sup>[BALL]</sup>[/A] ';
	if ( !isset($options['count']) )  $options['count'] = 5;

	// TITLE - название записи
	// COUNT - всего голосов page_rating_count
	// BALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - округлен до целого
	// REALBALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - дробный
	// [A]ссылка[/A]

	$CI = & get_instance();
	//$CI->db->select('page_slug, page_rating/page_rating_count AS page_ball, page_rating, page_rating_count, page_title', false);
	$CI->db->select('page_slug, page_id,page_content, page_rating/page_rating_count AS page_ball, page_rating, page_rating_count, page_title', false);

	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->order_by('page_ball', 'desc');
	$CI->db->order_by('page_rating', 'desc');
	// $CI->db->order_by('page_rating_count', 'desc');
	$CI->db->limit($options['count']);
    $CI->db->order_by('page_id', 'desc');
	$query = $CI->db->get('page');

	if ($query->num_rows() > 0)
	{
		$pages = $query->result_array();

		$link = '<a href="' . getinfo('siteurl') . 'page/';

		$out .= '<ul class="mso-widget-list">' . NR;


		foreach ($pages as $page)
		{
		      //new

             if( function_exists('mso_get_first_image_url') && isset($page['page_content']) && $page['page_content'] != '' )
              {
               $rater_img_url = mso_get_first_image_url($page['page_content']); # ищем первую картинку в записи

              }else
              {
                 $rater_img_url = '';
              }

              if($rater_img_url != '')
  	          {
  	             $rater_img_url = '<img src="' . $rater_img_url  . '" alt="' . $page['page_title']. '" class="rater_widget_img">';
  	             // pr($rater_schema_img);
  	          }

			$out1 = $options['format'];
			$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
			$out1 = str_replace('[COUNT]', $page['page_rating_count'], $out1);
			$out1 = str_replace('[REALBALL]', (real) $page['page_ball'], $out1);
			$out1 = str_replace('[BALL]', (round((real) $page['page_ball'])), $out1);
			$out1 = str_replace('[IMG_RATER]', $rater_img_url, $out1);



			$out1 = str_replace('[A]', $link . $page['page_slug']
					. '" title="' . t('Голосов:') . ' ' . $page['page_rating_count']
					. ' ' . t('Общий бал:') . ' ' . (real) $page['page_ball']
					. '">'
					, $out1);
			$out1 = str_replace('[/A]', '</a>', $out1);
            $path_img = getinfo('plugins_url') . 'rater_schema/';
		    $out .= '<li>' . $out1 . '</li>' . NR;
		}
		$out .= '</ul>' . NR;

		if ($options['header']) $out = $options['header'] . $out;
	}

	return $out;
}

# end file