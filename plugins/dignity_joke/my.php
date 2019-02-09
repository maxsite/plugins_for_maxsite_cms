<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('template_dir') . 'main-start.php');

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_joke', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'joke';

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id)
{
	
	// загружаем данные из базы
	$CI->db->from('dignity_joke');
	$CI->db->where('dignity_joke_comuser_id', $id);
	$CI->db->order_by('dignity_joke_datecreate', 'desc');
	$CI->db->join('dignity_joke_category', 'dignity_joke_category.dignity_joke_category_id = dignity_joke.dignity_joke_category', 'left');
	$query = $CI->db->get();

	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$allpages = $query->result_array();
		
		// обьявляем переменую	
		$out = '';
		
		// цикл
                foreach ($allpages as $onepage) 
                {

                        $out .= '<div class="page_only">';
			
			$out .= '<div class="info info-top">';
			$out .= '<h1><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'] . '">' . '#' . $onepage['dignity_joke_id'] . '</a></h2>';
			$out .= '</div>';
		
                        // если вошел админ
                        if ($onepage['dignity_joke_comuser_id'] == getinfo('comusers_id'))
                        {
                                // выводим ссылку «редактировать»
                                $out .= '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_joke_id'] . '">' . t('Редактировать', __FILE__) . '</a></p>';
                        }
		
                        $out .= '<p>' . joke_cleantext($onepage['dignity_joke_cuttext']) . '</p>';
		
                        // если нет текста, скрываем ссылку «подробнее»
                        if (!$onepage['dignity_joke_text'])
                        {
                                // ничего не показываем...
                                $out .= '';
                        }
                        else
                        {
                                // показываем ссылку «подробнее»
                                $out .= '<p style="padding-bottom:10px;"><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'] . '">' .
                                	t('Подробнее»', __FILE__) . '</a></p>';
                        }
		
			$out .= '<div class="info info-bottom">';
			$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_joke_datecreate']) . ' | ';
			if ($onepage['dignity_joke_category_id'])
			{
				$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_joke_category_id'] . '">' . $onepage['dignity_joke_category_name'] . '</a>';	
			}
			else
			{
				$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все анекдоты', __FILE__) . '</a>';
			}
			$out .= '</div>';
			
			$out .= '<div class="break"></div></div><!--div class="page_only"-->';
		
                }
		
		joke_menu();
		
		// выводим всё
		echo $out;
	}
	else
	{
                
                joke_menu();
		
		// если запись не найдена
		echo '<p>' . t('Анекдотов нет.', __FILE__) . '</p>';
	}
}
else
{
    echo t('Анекдотов нет.', __FILE__);
}

require(getinfo('template_dir') . 'main-end.php');

// конец файла