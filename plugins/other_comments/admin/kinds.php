<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	
	// проверяем входящие данные если было добавление нового вида
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 'f_new_slug', 'f_new_title')) )
	{
		# защита рефера
		mso_checkreferer();

		// полученное новое значение
		$f_new_slug = trim($post['f_new_slug']);
		$f_new_title = trim($post['f_new_title']);
		if ($f_new_slug)
		{
			 $ins_data = array (
					'kind_slug' => $f_new_slug,
					'kind_title' => $f_new_title
					);

				$res = ($CI->db->insert('kinds', $ins_data)) ? '1' : '0';		
		}
		else echo '<div class="error">' . t('Вы не ввели слуг') . '</div>';
	}
	
?>
<h1><?= t('Виды комментируемых сущностей') ?></h1>
<p class="info"><?= t('Определяют первый сегмент адреса страницы сайта') ?></p>

<?php

	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', 'slug', 'title');
	
	
	$CI->db->select('kind_id , kind_slug , kind_title');
	$query = $CI->db->get('kinds');
	
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
		{
			$id = $row['kind_id'];
			$slug = $row['kind_slug'];
			$title = $row['kind_title'];
			$CI->table->add_row($id, $slug, $title);
		}
	}
	

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	

	echo $CI->table->generate();
	echo '<strong>slug: </strong><input type="text" name="f_new_slug" value="">';
	echo '<strong>title: </strong><input type="text" name="f_new_title" value="">';
	echo '<input type="submit" name="f_new_submit" value="' . t('Добавить') . '">';
	echo '</form>';
	

	
?>