<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
  $cur_kind = mso_segment(4);
  if ($cur_kind == 'next') $cur_kind = '';
	
	
	# удалить 
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_elements')) )
	{
		mso_checkreferer();
		// pr($post);
		
		$f_check_elements = $post['f_check_elements']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_elements as $id_el=>$val)
			if ($val) $arr_ids[] = $id_el;
		
		// сперва удалим комментарии
		$CI->db->where_in('comments_element_id', $arr_ids);
		if ( $CI->db->delete('other_comments') )
		{
			mso_flush_cache();
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			
			echo '<div class="update">' . t('Удалены комментарии сущности!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка удаления комментариев сущности') . '</div>';
			
	// проверим наличие комментариев у удаляемых сущностей
  $CI->db->select('comments_id');
  $CI->db->where_in('comments_element_id', $arr_ids);
	$query = $CI->db->get('other_comments');
	if ($query->num_rows() == 0)
	{
	  // удаляем сущность
		$CI->db->where_in('element_id', $arr_ids);
		if ( $CI->db->delete('elements') )
		{
			mso_flush_cache();
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			echo '<div class="update">' . t('Удалены выбранные сущности!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка удаления сущностей') . '</div>';	  
	}
	else 
			echo '<div class="error">' . t('Невозможно удалить сущности у которых есть комментарии') . '</div>';
						
	}	
	
	
?>
<h1><?= t('Комментируемые эллементы') ?></h1>
<p class="info"><?= t('Для каждой страницы сайта siteurl/seg1/seg2, где размещаются комментарии создается запись в этой таблице') ?></p>
<?php
if (!$cur_kind) echo '<p><strong>' . t('Без фильтра: ') . '</strong>' . t('Все') . '</a></p>';
else echo '<p><strong>' . t('Без фильтра: ') . '</strong><a href="' . getinfo('site_admin_url') . 'other_comments/elements/">' . t('Все') . '</a></p>';
echo '<p><strong>' . t('Фильтр по видам: ') . '</strong>';
  // выведем виды сущностей
  $CI->db->select('kind_id , kind_slug , kind_title');
	$query = $CI->db->get('kinds');
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
		{
			$slug = $row['kind_slug'];
			$title = $row['kind_title'];
			if ($slug == $cur_kind) 
			  echo $slug . ' (' . $title . ') | ';
			else
			  echo '<a href="' . getinfo('site_admin_url') . 'other_comments/elements/' . $slug . '">' . $slug . ' (' . $title . ')</a> | ';  
		}
  } 
  echo '</p>';
  
  // проверим есть ли фильтр
  
	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	if ($cur_kind) $CI->table->set_heading('ID', 'title', 'slug' , 'table' , 'id');
	else $CI->table->set_heading('ID', 'title', 'slug' , 'kind' , 'table' , 'id');
	
	// получим комментируемые сущности
	
	if ($cur_kind) 	
	  $CI->db->select('SQL_CALC_FOUND_ROWS element_id , element_slug , element_title , element_id_in_table , element_table_name , kinds.kind_slug', false);
	else
	  $CI->db->select('SQL_CALC_FOUND_ROWS element_id , element_slug , element_title , element_id_in_table , element_table_name' , false);
	
	$CI->db->from('elements');
	
	if ($cur_kind) $CI->db->join('kinds', 'kinds.kind_id = elements.element_kind_id');
	
	if ($cur_kind) 	$CI->db->where('kinds.kind_slug' , $cur_kind);
	
	$CI->db->order_by('element_title', 'asc');
	
	$limit = 50;

	$CI->db->limit($limit, mso_current_paged() * $limit - $limit ); // не более $limit	
	$query = $CI->db->get();

	$pagination = mso_sql_found_rows($limit); // определим общее кол-во записей для пагинации
	mso_hook('pagination', $pagination);
	
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
		{
			$id = $row['element_id'];
			$slug = $row['element_slug'];
			$title = $row['element_title'];
			if ($cur_kind) $kind_slug = $row['kind_slug']; else $kind_slug = '';
			$id_in_table = $row['element_id_in_table'];
			$table_name = $row['element_table_name'];			
			
			
			// для вывода делаем чекбокс + hidden всех комментов для того, чтобы проверить тех,
			// которые окажутся не отмечены - их POST не передает
			$id_out = $id . ' <input type="checkbox" name="f_check_elements[' . $id . ']">' . NR;
			
			if ($cur_kind) $CI->table->add_row($id_out, $title, $slug , $table_name , $id_in_table);
			else $CI->table->add_row($id_out, $title, $slug , $kind_slug , $table_name , $id_in_table);
		}
	}

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	
	echo $CI->table->generate();

	echo '<input type="submit" name="f_delete_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p>';
	
	echo '</form>';
	
	mso_hook('pagination', $pagination);

	
?>