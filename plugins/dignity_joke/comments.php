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

joke_menu();

// добавляем заголовок «Категории»
echo '<h1><a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Новые комментарии', __FILE__) . '</a></h1>';
        
// получаем доступ к CI
$CI = & get_instance();

// готовим пагинацию
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_joke_comments_id');
$CI->db->from('dignity_joke_comments');
if (!is_login())
{
	$CI->db->where('dignity_joke_comments_approved', true);	
}
$query = $CI->db->get();
$pag_row = $query->num_rows();

if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']);

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}
	
// берём данные из базы
$CI->db->from('dignity_joke_comments');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_joke_comments.dignity_joke_comments_comuser_id', 'left');
$CI->db->order_by('dignity_joke_comments_datecreate', 'desc');
if (!is_login())
{
	$CI->db->where('dignity_joke_comments_approved', true);	
}
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();
	
// если есть что выводить
if ($query->num_rows() > 0)	
{	
	$entrys = $query->result_array();
	
	// обьявлем переменую
	$out = '';
	$catout = '';
	
	foreach ($entrys as $entry) 
	{	
		// выводим названия категории и количество записей в ней
		$catout .= '<li>';
		$catout .= '<p>';
		$catout .= '<a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $entry['dignity_joke_comments_thema_id'] . '">' . blogs_cleantext($entry['dignity_joke_comments_text']) . '</a>'
			. '<br>' . t(' от ', __FILE__) . '<a href="' . getinfo('siteurl') . 'users/' . $entry['dignity_joke_comments_comuser_id'] . '">' . $entry['comusers_nik'] . '</a>' . t(' в ', __FILE__) . mso_date_convert($format = 'H:i → d.m.Y', $entry['dignity_joke_comments_datecreate']);
		if (is_login())
		{	
			# удаление
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_comments_delete')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_joke_comments_id'];
				
				$CI->db->where('dignity_joke_comments_id', $post['f_comments_id']);
				$CI->db->delete('dignity_joke_comments');
					
				mso_flush_cache();
				
				$catout .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
			}
			
			# одобрить
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_comments_approved')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_joke_comments_id'];
				
				$CI->db->where('dignity_joke_comments_id', $post['f_comments_id']);
				
				$data = array (
					'dignity_joke_comments_approved' => 1,
				);
				
				$CI->db->where('dignity_joke_comments_id', $post['f_comments_id']);
				$CI->db->update('dignity_joke_comments', $data);
				echo '<script>location.replace(window.location); </script>';
					
				mso_flush_cache();
			}
		
			$form = '';
			$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
			$form .= '<input type="hidden" name="f_comments_id" value="' . $entry['dignity_joke_comments_id'] . '" />';
			
			if (!$entry['dignity_joke_comments_approved'])
			{
				$form .= '<input type="submit" name="f_submit_dignity_joke_comments_approved" onClick="if(confirm(\'' . t('Одобрить?', __FILE__) . ' ' . t('Комментарий №', __FILE__) . $entry['dignity_joke_comments_id'] . '\')) {return true;} else {return false;}" value="' . t('Одобрить', __FILE__) . '">';	
			}
			
			$form .= '<input type="submit" name="f_submit_dignity_joke_comments_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Комментарий №', __FILE__) . $entry['dignity_joke_comments_id'] . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
			$form .= '</form>';
			
			$catout .= $form;	
		}
		
		$catout .= '</p>';
		$catout .= '</li>';
	}
		
	// начиаем новый список
	$out .= '<ul>';
		
	// выводим назавания категорий и количетсов записей
	$out .= $catout;
	
	// заканчиваем список
	$out .= '</ul>';
	
	echo $out;
}
else
{
	echo t('Нет новых комментарий.', __FILE__);
}

require(getinfo('template_dir') . 'main-end.php');

// конец файла