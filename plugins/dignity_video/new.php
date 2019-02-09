<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'video';
if ( !isset($options['limit']) ) $options['limit'] = 10;

video_menu();

// добавляем заголовок «Категории»
echo '<h1><a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Новые видео', __FILE__) . '</a></h1>';
        
// получаем доступ к CI
$CI = & get_instance();

// готовим пагинацию
$pag = array();
$pag['limit'] = 20;
$CI->db->select('dignity_video_id');
$CI->db->from('dignity_video');
if (!is_login())
{
	$CI->db->where('dignity_video_approved', true);	
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
$CI->db->from('dignity_video');
if (!is_login())
{
	$CI->db->where('dignity_video_approved', true);	
}
$CI->db->order_by('dignity_video_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_video.dignity_video_comuser_id', 'left');
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
		$catout .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $entry['dignity_video_id'] . '">' . $entry['dignity_video_title'] . '</a>'
			. '<br>' . t(' от ', __FILE__) . '<a href="' . getinfo('siteurl') . 'users/' . $entry['dignity_video_comuser_id'] . '">' . $entry['comusers_nik'] . '</a>' . t(' в ', __FILE__) . mso_date_convert($format = 'H:i → d.m.Y', $entry['dignity_video_datecreate']) . '</p>';
		
		if (is_login())
		{	
			# удаление
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_delete')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_video_id'];
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				$CI->db->delete('dignity_video');
					
				mso_flush_cache();
				
				$catout .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
			}
			
			# одобрить
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_approved')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_video_id'];
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				
				$data = array (
					'dignity_video_approved' => 1,
				);
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				$CI->db->update('dignity_video', $data);
				echo '<script>location.replace(window.location); </script>';
					
				mso_flush_cache();
			}
			
			# на главную
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_ontop')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_video_id'];
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				
				$data = array (
					'dignity_video_ontop' => 1,
				);
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				$CI->db->update('dignity_video', $data);
				echo '<script>location.replace(window.location); </script>';
					
				mso_flush_cache();
			}
			
			# убрать с главной
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_drop_ontop')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $entry['dignity_video_id'];
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				
				$data = array (
					'dignity_video_ontop' => 0,
				);
				
				$CI->db->where('dignity_video_id', $post['f_video_id']);
				$CI->db->update('dignity_video', $data);
				echo '<script>location.replace(window.location); </script>';
					
				mso_flush_cache();
			}
		
			$form = '';
			$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
			$form .= '<input type="hidden" name="f_video_id" value="' . $entry['dignity_video_id'] . '" />';
			
			if (!$entry['dignity_video_approved'])
			{
				$form .= ' <input type="submit" name="f_submit_dignity_video_approved" onClick="if(confirm(\'' . t('Одобрить?', __FILE__) . ' ' . t('Комментарий №', __FILE__) . $entry['dignity_video_id'] . '\')) {return true;} else {return false;}" value="' . t('Одобрить', __FILE__) . '">';	
			}
			
			if (!$entry['dignity_video_ontop'])
			{
				$form .= ' <input type="submit" name="f_submit_dignity_video_ontop" onClick="if(confirm(\'' . t('На главную?', __FILE__) . ' ' . t('Запись №', __FILE__) . $entry['dignity_video_id'] . '\')) {return true;} else {return false;}" value="' . t('На главную', __FILE__) . '">';	
			}
			
			if ($entry['dignity_video_ontop'])
			{
				$form .= ' <input type="submit" name="f_submit_dignity_video_drop_ontop" onClick="if(confirm(\'' . t('На главную?', __FILE__) . ' ' . t('Запись №', __FILE__) . $entry['dignity_video_id'] . '\')) {return true;} else {return false;}" value="' . t('Убрать с главной', __FILE__) . '">';	
			}
			
			$form .= ' <input type="submit" name="f_submit_dignity_video_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Запись №', __FILE__) . $entry['dignity_video_id'] . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
			$form .= '</form>';
			
			$catout .= $form;	
		}

		$catout .= '</li>';
	}
		
	// начиаем новый список
	$out .= '<ul>';
		
	// выводим назавания категорий и количетсов записей
	$out .= $catout;
	
	// заканчиваем список
	$out .= '</ul>';
	
	echo $out;
	
	mso_hook('pagination', $pag);
}
else
{
	echo '<p>' . t('Нет новых видео записей.', __FILE__) . '</p>';
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла
