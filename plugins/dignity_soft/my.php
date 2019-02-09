<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'soft';

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id)
{
	
	// загружаем данные из базы
	$CI->db->from('dignity_soft');
	$CI->db->where('dignity_soft_comuser_id', $id);
	$CI->db->order_by('dignity_soft_datecreate', 'desc');
	$CI->db->join('dignity_soft_category', 'dignity_soft_category.dignity_soft_category_id = dignity_soft.dignity_soft_category', 'left');
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
			
			$no_approved = '';
			if ($onepage['dignity_soft_comuser_id'] == getinfo('comusers_id'))
			{
				if (!$onepage['dignity_soft_approved'])
				{
					$no_approved .= '<span style="color:red;">?</span>';
				}
			}
		
                        $out .= '<div class="info info-top"><h1>' . $no_approved;
			
			if($onepage['dignity_soft_approved'])
			{
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_soft_id'] . '">';
			}
			else
			{
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_soft_id'] . '">';
			}
			
			$out .= $onepage['dignity_soft_title'] . '</a> ';
                        
                        $out .= '</h1></div>';
		
                        // если вошел админ
                        if ($onepage['dignity_soft_comuser_id'] == getinfo('comusers_id'))
                        {
                                // выводим ссылку «редактировать»
                                $out .= '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_soft_id'] . '">' . t('Редактировать', __FILE__) . '</a></p>';
                        }
		
                        $out .= '<p>' . soft_cleantext($onepage['dignity_soft_cuttext']) . '</p>';
		
                        // если нет текста, скрываем ссылку «подробнее»
                        if (!$onepage['dignity_soft_text'])
                        {
                                // ничего не показываем...
                                $out .= '';
                        }
                        else
                        {
                                // показываем ссылку «подробнее»
                                $out .= '<p style="padding-bottom:10px;"><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_soft_id'] . '">' .
                                	t('Подробнее»', __FILE__) . '</a></p>';
                        }
		
			$out .= '<div class="info info-bottom"></div>';
			
			$out .= '<div class="break"></div></div><!--div class="page_only"-->';
		
                }
		
		soft_menu();
		
		// выводим всё
		echo $out;
	}
	else
	{
                
                soft_menu();
		
		// если запись не найдена
		echo '<p>' . t('Приложений нет.', __FILE__) . '</p>';
	}
}
else
{
    echo t('Приложений нет.', __FILE__);
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла