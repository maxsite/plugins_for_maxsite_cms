<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * v.2.0
 */
  $version_page_views2 = '2.0';

# функция автоподключения плагина
function page_views2_autoload($args = array())
{
	mso_register_widget('page_views2_widget', t('Популярные публикации')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function page_views2_uninstall($args = array())
{
	mso_delete_option_mask('page_views2_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function page_views2_widget($num = 1)
{
	$widget = 'page_views2_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	return page_views2_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function page_views2_widget_form($num = 1)
{
	$widget = 'page_views2_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = 'Самое популярное';
	if ( !isset($options['limit']) ) $options['limit'] = 4;
	if ( !isset($options['page_type']) ) $options['page_type'] = 0;
	if ( !isset($options['content_word']) ) $options['content_word'] = '0';
	if ( !isset($options['img_prev_def']) ) $options['img_prev_def'] = '';
	if ( !isset($options['format']) ) $options['format'] = '[IMG_PREV][A][TITLE][/A] <sup>[ALLCOUNT]</sup>';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$CI->db->select('page_type_id, page_type_name');
	$query = $CI->db->get('page_type');
	$types = array(0 => t('Все типы'));
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $page)
		$types[$page['page_type_id']] = $page['page_type_name'];
	}

	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'] ) ), '');
	
	$form .= mso_widget_create_form(t('Тип записей'), form_dropdown( $widget . 'page_type', $types, array( 'value'=>$options['page_type'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество слов'), form_input( array( 'name'=>$widget . 'content_word', 'value'=>$options['content_word'] ) ), '0 - не выводить контент , иначе вывести количество слов контента');
	
	$form .= mso_widget_create_form(t('Миниатюра по-умолчанию'), form_input( array( 'name'=>$widget . 'img_prev_def', 'value'=>$options['img_prev_def'] ) ), 'указать миниатюру по умолчанию, в случае если в форматной строке используется [IMG_PREV], н-р, http://mysite.ru/uploads/default.jpg');
	
	//++ изменения в плагине
	$form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('<strong>[IMG_PREV]</strong> - выводить миниатюры из мета-поля "prev"<br /><strong>[TITLE]</strong> - название записи<br><strong>[COUNT]</strong> - просмотров в день<br><strong>[ALLCOUNT]</strong> - всего просмотров<br><strong>[A]</strong>ссылка<strong>[/A]</strong>'));
	//-- изменения в плагине

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function page_views2_widget_update($num = 1)
{
	$widget = 'page_views2_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = (int) mso_widget_get_post($widget . 'limit');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['content_word'] = (int) mso_widget_get_post($widget . 'content_word');
	$newoptions['img_prev_def'] = mso_widget_get_post($widget . 'img_prev_def');	
	$newoptions['format'] = mso_widget_get_post($widget . 'format');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


#  Вспомогательная функция для page_views2_widget_custom - сортировка массива
function page_views2_cmp($a, $b)
{
	if ( $a['sutki'] == $b['sutki'] ) return 0;
	return ( $a['sutki'] > $b['sutki'] ) ? -1 : 1;
}

# функции плагина
function page_views2_widget_custom($options = array(), $num = 1)
{
	// кэш
	$cache_key = 'page_views2_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 4;
	if ( !isset($options['page_type']) ) $options['page_type'] = 0;
	if ( !isset($options['format']) ) $options['format'] = '[IMG_PREV][A][TITLE][/A] <sup>[ALLCOUNT]</sup>';
	//++  изменения в плагине
	if ( !isset($options['img_prev_def']) ) $options['img_prev_def'] = '';
	if ( !isset($options['content_word']) ) $options['content_word'] = '0';
	$is_image = strpos($options['format'], '[IMG_PREV]');
	//--  изменения в плагине

	# получаем все записи как есть
	# в полученном массиве меняем общее кол-во прочтений на кол-во прочтений в сутки
	# сортируем массив по новомк значению

	$curdate = time();

	$CI = & get_instance();
	//++  изменения в плагине
	if ( isset($options['content_word'])  and ($options['content_word']) )
		$select_page_view2 = 'page_slug, page_title, page_id, page_view_count, page_date_publish, page_content'; // использовать контент при выводе
	else
		$select_page_view2 = 'page_slug, page_title, page_id, page_view_count, page_date_publish'; 			 	// не использовать контент
	if ($is_image !== false)
	{
		$CI->db->join('meta', 'page_id = meta_id_obj and meta_key="prev"', 'left');
		$select_page_view2.= ', meta_value';
		
	}	
	
	$CI->db->select($select_page_view2);
	$CI->db->limit($options['limit']);
	
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_view_count > ', '0');
	if ( $options['page_type'] ) $CI->db->where('page_type_id', $options['page_type']);
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	
	//if ($is_image !== false)
		//$CI->db->join('meta', 'page_id = meta_id_obj', 'left');
	//$CI->db->order_by('page_id', 'desc');
	$CI->db->order_by('page_view_count', 'desc');
	
	//echo _sql();
	$query = $CI->db->get('page');
	
	

	if ($query->num_rows() > 0)
	{
		$pages = $query->result_array();
		
		//pr($pages);
		foreach ($pages as $key=>$val)
		{
			// если еще сутки не прошли, то ставим общее колво прочтений
			//pr(round( $val['page_view_count'] / ($curdate - strtotime($val['page_date_publish'])) * 86400));
			if ( $curdate - strtotime($val['page_date_publish']) > 86400 )
				$pages[$key]['sutki'] = round( $val['page_view_count'] / (($curdate - strtotime($val['page_date_publish'])) / 86400));
			else
				$pages[$key]['sutki'] = $val['page_view_count'];
			
			//pr('$val[\'page_date_publish\'])='.$val['page_date_publish'].' ($curdate - strtotime($val[\'page_date_publish\'])) * 86400)='.(($curdate - strtotime($val['page_date_publish'])) / 86400).' $pages[$key][\'sutki\']='.$pages[$key]['sutki']);
		}

		//usort($pages, 'page_views2_cmp'); // отсортируем по ['sutki']

		// сам вывод
		$link = '<a href="' . getinfo('siteurl') . 'page/';

		//$i = 1;
		
		//++ изменения в плагине
		//$is_image = strpos($options['format'], '[IMG_PREV]');
		if ($is_image !== false)
				{	
				$img_prev_def = $options['img_prev_def'];	// URL к картинке по-умолчанию 
				$count_world  = $options['content_word']; 	// сколько слов контента выводить
				//require_once( getinfo('common_dir') . 'meta.php' ); // подключаем библиотеку для работы с meta-полями
				}
		//-- изменения в плагине
		
 		foreach ($pages as $page)
		{
			//if ($page['sutki'] > 0)
			//{
				//if ($i>$options['limit']) break; // лимит

				$out1 = $options['format'];

				$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
				$out1 = str_replace('[COUNT]', $page['sutki'], $out1);
				$out1 = str_replace('[ALLCOUNT]', $page['page_view_count'], $out1);

				$out1 = str_replace('[A]', $link . $page['page_slug']
						. '" title="' . t('Просмотров в сутки: ') . $page['sutki'] . '">'
						, $out1);

				$out1 = str_replace('[/A]', '</a>', $out1);

				//++ изменения в плагине
				// метаполе превьюшки
				if ($is_image !== false)
				{
					//if ($img_prev = mso_get_meta('prev', 'page', $page['page_id']))
					if ($img_prev = $page['meta_value'])
					{
						//pr('Есть картинка');
						//pr($img_prev);
						//if (isset($img_prev[0]['meta_value']) and $img_prev[0]['meta_value']) 
						if (isset($img_prev) and $img_prev) 
						{
							//$img_prev = '<img src="' . $img_prev[0]['meta_value'] . '" class="left"> ';
							$img_prev = '<img src="' . $img_prev . '" class="left"> ';
						}
						elseif ($img_prev_def) $img_prev = '<img src="' . $img_prev_def . '" class="left"> ';
						else $img_prev = '';
					}
					else
					{
						if ($img_prev_def) $img_prev = '<img src="' . $img_prev_def . '" class="left"> ';
						else $img_prev = '';
					}
					$out1 = str_replace('[IMG_PREV]', $img_prev, $out1);
					if (isset($page['page_content']) and ($page['page_content']))
					{	if (isset($count_world) and $count_world) $content_page_view = '<div class="content_page_view">'.trim(mso_str_word(strip_tags($page['page_content']),$count_world),',').' ...</div>'; 
							else  $content_page_view = '';
					}		
					else $content_page_view = '';
							
				}
				//else $img_prev = '';
				if ($is_image !== false)
					$out .= '<li>' . $out1 . $content_page_view .'<div class="clearfix"></div></li>' . NR;
				else $out .= '<li>' . $out1 . '<div class="clearfix"></div></li>' . NR;	
				//-- изменения в плагине
				

				//$i++;
			//}
			//else break; // всё

		}

		if ($out)
		{
			$out = '<ul class="is_link page_views2">' . NR . $out . '</ul>' . NR;
			if ($options['header']) $out = $options['header'] . $out;
		}
	}

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}

# end file