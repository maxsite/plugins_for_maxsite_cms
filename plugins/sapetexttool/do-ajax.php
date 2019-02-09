<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «SapeTextTool» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-sapetexttool
 */

	# Проверка прав доступа
	if( !mso_check_allow('sapetexttool_edit') )
	{
		echo false;
		die();
	}
		
	$CI = & get_instance();
	mso_checkreferer();
	global $MSO, $num_counter;
	$out = ''; $num_counter = 0;
		
	# показать мета, сменить статус публикации или удалить
	if( $post = mso_check_post(array('do', 'templ', 'page_author', 'page_type', 'page_status', 'page_category', 'page_tag', 'get_range', 'page_id_begin', 'page_id_end', 'get_list', 'pages_id')))
	{
		# генерация по шаблону
		if( $post['do'] == 'getlist' )
		{
			$res = false;
				
			$CI->db->from('page pa');
			$CI->db->select('pa.page_id pid, pa.page_type_id type, pa.page_id_autor autor, pa.page_title name, pa.page_slug slug, pa.page_status status, mm1.meta_value title, mm2.meta_value description, mm3.meta_value keywords');
			$CI->db->join('mso_meta mm1', 'mm1.meta_id_obj = pa.page_id AND mm1.meta_key = "title"', 'left'); 
			$CI->db->join('mso_meta mm2', 'mm2.meta_id_obj = pa.page_id AND mm2.meta_key = "description"', 'left'); 
			$CI->db->join('mso_meta mm3', 'mm3.meta_id_obj = pa.page_id AND mm3.meta_key = "keywords"', 'left'); 
				
			#  page_id_autor
			if( isset($post['page_author']) && is_numeric($post['page_author']) && $post['page_author'] != 0 )
			{
		        $CI->db->where('pa.page_id_autor', $post['page_author']);
			}
				
			# page_type_id
			if( isset($post['page_type']) && is_numeric($post['page_type']) && $post['page_type'] != 0 )
			{
		        $CI->db->where('pa.page_type_id', $post['page_type']);
			}
				
			# page_status
			if( isset($post['page_type']) && $post['page_status'] != '' )
			{
		        $CI->db->where('pa.page_status', $post['page_status']);
			}
				
			# page_category
			if( isset($post['page_category']) && is_numeric($post['page_category']) && $post['page_category'] != 0 )
			{
				$CI->db->from('cat2obj ca');
				$CI->db->where('ca.page_id = pa.page_id AND ca.category_id = '.$post['page_category']);
			}
				
			# page_tag
			if( isset($post['page_tag']) && $post['page_tag'] != '' )
			{
				$page_tags = array_map('trim', explode(',', trim($post['page_tag'])));
				$CI->db->from('meta me');
				$CI->db->where('me.meta_key = "tags"');
				$CI->db->where('me.meta_id_obj = pa.page_id');
		        $CI->db->where_in('me.meta_value', $page_tags);
			}
				
			# get_range
			if( isset($post['get_range']) && is_numeric($post['get_range']) && $post['get_range'] == 1 )
			{
				if( isset($post['page_id_begin']) && is_numeric($post['page_id_begin']) && $post['page_id_begin'] >= 0 )
				{
					$CI->db->where('pa.page_id >=', $post['page_id_begin']);
				}
					
				if( isset($post['page_id_end']) && is_numeric($post['page_id_end']) && $post['page_id_end'] > 0 )
				{
					$CI->db->where('pa.page_id <=', $post['page_id_end']);
				}
			}
				
			# get_list
			if( isset($post['get_list']) && is_numeric($post['get_list']) && $post['get_list'] == 1 )
			{
				if( isset($post['pages_id']) && $post['pages_id'] != '' && !preg_match('/[^\d\s,]{1}/', $post['pages_id']) )
				{
					$pages_id = array_map('trim', explode(',', trim($post['pages_id'])));
					$CI->db->where_in('pa.page_id', $pages_id);
				}
			}
				
			$qry = $CI->db->get();
				
			if( $qry->num_rows() > 0 )
			{
				$res = true;
				$rows = $qry->result_array();
					
				foreach( $rows as $row )
				{
					
					$templ = $post['templ'];
						
					$templ = $row['pid'] == '' ? $templ : preg_replace('/\[ID\]/msi', $row['pid'], $templ);
					$templ = $row['slug'] == '' ? $templ : preg_replace('/\[SLUG\]/msi', $row['slug'], $templ);
					$templ = $_SERVER['HTTP_HOST'] == '' ? $templ : preg_replace('/\[DOMAIN\]/msi', $_SERVER['HTTP_HOST'], $templ);
					$templ = $row['slug'] == '' ? $templ : preg_replace('/\[URL\]/msi', getinfo('siteurl').'page/'.$row['slug'], $templ);
					$templ = $row['title'] == '' ? $templ : preg_replace('/\[TITLE\]/msi', $row['title'], $templ);
					$templ = $row['name'] == '' ? $templ : preg_replace('/\[NAME\]/msi', $row['name'], $templ);
					$templ = $row['description'] == '' ? $templ : preg_replace('/\[DESC\]/msi', $row['description'], $templ);
					$templ = $row['keywords'] == '' ? $templ : preg_replace('/\[KEYWORDS\]/msi', $row['keywords'], $templ);
						
					if( $row['title'] != '' )
					{
						$templ = preg_replace('/\[TOPIC\]/msi', $row['title'], $templ);
					}
					elseif( $row['title'] == '' && $row['name'] != '')
					{
						$templ = preg_replace('/\[TOPIC\]/msi', $row['name'], $templ);
					}
						
					if( $row['keywords'] != '' && preg_match('/\[KEYWORD\]/', $templ ) )
					{
						$keywords = array_map('trim', explode(',', trim($row['keywords'])));;
						if( $keywords && count($keywords) > 0 )
						{
							$kw_templ = '';
							foreach( $keywords as $keyword )
							{
								$kw_templ .= preg_replace('/\[KEYWORD\]/msi', $keyword, $templ);
							}
							$templ = $kw_templ;
						}
					}
						
					if( preg_match('/\[RUBRICS\]|\[RUBRIC\]/', $templ ) )
					{
						$rubrics = get_categories( $row['pid'] );
						if( $rubrics && count($rubrics) > 0 )
						{
							$tmp_rubrics = array();
							foreach( $rubrics as $rubr )
							{
								$tmp_rubrics[] = $rubr['category_name'];
							}
								
							if( preg_match('/\[RUBRICS\]/', $templ ) )
							{
								$templ = preg_replace('/\[RUBRICS\]/msi', implode(', ', $tmp_rubrics), $templ);
							}
								
							if( preg_match('/\[RUBRIC\]/', $templ ) )
							{
								$rub_templ = '';
								foreach( $tmp_rubrics as $rubric )
								{
									$rub_templ .= preg_replace('/\[RUBRIC\]/msi', $rubric, $templ);
								}
								$templ = $rub_templ;
							}
						}
					}
						
					if( preg_match('/\[TAGS\]|\[TAG\]/', $templ ) )
					{
						$tags = get_tags( $row['pid'] );
						if( $tags && count($tags) > 0 )
						{
							$tmp_tags = array();
							foreach( $tags as $tag )
							{
								$tmp_tags[] = $tag['tag'];
							}
								
							if( preg_match('/\[TAGS\]/', $templ ) )
							{
								$templ = preg_replace('/\[TAGS\]/msi', implode(', ', $tmp_tags), $templ);
							}
								
							if( preg_match('/\[TAG\]/', $templ ) )
							{
								$tag_templ = '';
								foreach( $tmp_tags as $tag )
								{
									$tag_templ .= preg_replace('/\[TAG\]/msi', $tag, $templ);
								}
								$templ = $tag_templ;
							}
						}
					}
						
					$out .= $templ;
				}
				$out = preg_replace_callback('/\[NUM\]/msi', 'parse_num', $out);
			}
			else
			{
				$out = 'Для заданных условий поиска записей не обнаружено!';
			}
				
			echo json_encode(array(
				'msg' => $out,
				'res' => $res,
			));
			die();
		}
	}
		
	die();
	
###
function get_categories( $pid ) # Получаем список рубрик поста
{
	$CI = & get_instance();
		
	# нужно выбрать все рубрики
	$CI->db->select('ca.category_name');
	$CI->db->from('category ca');
	$CI->db->from('cat2obj cao');
	$CI->db->where('cao.page_id', $pid);
	$CI->db->where('cao.category_id = ca.category_id');
	if( $qry = $CI->db->get() )
	{
		return $qry->result_array();
	}
		
	return false;
}

function get_tags( $pid ) # Получаем список меток поста
{
	$CI = & get_instance();
		
	# нужно выбрать все рубрики
	$CI->db->select('me.meta_value tag');
	$CI->db->from('meta me');
	$CI->db->where('me.meta_id_obj', $pid);
	$CI->db->where('me.meta_key = "tags"');
	if( $qry = $CI->db->get() )
	{
		return $qry->result_array();
	}
		
	return false;
}

function parse_num( $match ) #  Заменяем [NUM]
{
	global $num_counter;
		
	return ++$num_counter;
	#pr($match);
}
?>