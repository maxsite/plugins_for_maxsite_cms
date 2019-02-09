<style type="text/css"><!--
#table-dc {
	width: 100%
}
#table-dc tr td:first-child {
	width: 350px;
}
//--></style>

<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	mso_cur_dir_lang(__FILE__);
	require_once(getinfo('common_dir') . 'meta.php');
	$CI = & get_instance();
	$CI->load->library('table');
	$tmpl = array (
			'table_open'		  => '<table class="page tablesorter" id="table-dc">',
			'row_alt_start'		  => '<tr class="alt">',
			'cell_alt_start'	  => '<td class="alt">',
			'heading_row_start' 	=> NR . '<thead><tr>',
			'heading_row_end' 		=> '</tr></thead>' . NR,
			'heading_cell_start'	=> '<th style="cursor: pointer;">',
			'heading_cell_end'		=> '</th>',
				);
	$CI->table->set_template($tmpl);
	$CI->table->set_heading('Категория', 'Метатеги');

	$CI->db->select('category_id, category_name');
	$CI->db->where('category_type', 'page');
	$query = $CI->db->get('category');
	if ($query->num_rows() > 0)
	{
		$all_meta = array();
		$all_categories = array();
		foreach ($query->result_array() as $key => $category)
		{
			$m = mso_get_meta('category_title', 'category', $category['category_id']);
			if (!$m) $m = ' | | | '; else $m = $m[0]['meta_value'];;
			$m = explode('|', $m);
			$meta['ct_title'] = trim($m[0]);
			$meta['ct_keywords'] = trim($m[1]);
			$meta['ct_description'] = trim($m[2]);
			$meta['ct_description'] = str_replace("_NR_", "\n", $meta['ct_description']);
			$meta['ct_template'] = trim($m[3]);
			$all_meta[$category['category_id']] = $meta;
			$all_categories[$category['category_id']] = $category;
		}
	}

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		//pr($post);
		$meta = array();
		foreach ($post['f_category_title'] as $key => $title)
		{
			$all_meta[$key]['ct_title'] = $post['f_category_title'][$key];
			$all_meta[$key]['ct_keywords'] = $post['f_category_keywords'][$key];
			$all_meta[$key]['ct_description'] = $post['f_category_description'][$key];
			$all_meta[$key]['ct_template'] = $post['f_category_template'][$key];
			
			if (trim($post['f_category_title'][$key]) or trim($post['f_category_keywords'][$key]) or trim($post['f_category_description'][$key]) or trim($post['f_category_template'][$key]))
			{
				$post['f_category_description'][$key] = str_replace("\n", "_NR_", $post['f_category_description'][$key]);
				mso_add_meta('category_title', $key, 'category', $post['f_category_title'][$key] . '|' . $post['f_category_keywords'][$key] . '|' .$post['f_category_description'][$key] . '|' .$post['f_category_template'][$key]);
			}
			else
			{
				$CI->db->delete('meta', array('meta_key' => 'category_title', 'meta_id_obj' => $key));
			}
		}
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}


	echo '<h1>'. t('Category title'). '</h1><p class="info">'. t('С помощью этого плагина вы можете сделать настраиваемую мета-теги категорий.'). '</p>';
	$form = '<h2>' . t('Категории', 'plugins') . '</h2>';

	foreach ($all_categories as $key => $category)
	{
		$CI->table->add_row(
							'<h3 style="background: silver;">' . $category['category_name'] . '</h3>',
							''
							);
		$CI->table->add_row(
							'Title',
							'<input type="text" name="f_category_title['.$category['category_id'].']" value="'.$all_meta[$key]['ct_title'].'">'
							);
		$CI->table->add_row(
							'Description',
							'<textarea name="f_category_description['.$category['category_id'].']">'.$all_meta[$key]['ct_description'].'</textarea>'
							);
		$CI->table->add_row(
							'Keywords',
							'<input type="text" name="f_category_keywords['.$category['category_id'].']" value="'.$all_meta[$key]['ct_keywords'].'">'
							);
		$CI->table->add_row(
							'Template',
							'<input type="text" name="f_category_template['.$category['category_id'].']" value="'.$all_meta[$key]['ct_template'].'">'
							);
	}
	$form .= $CI->table->generate();

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

