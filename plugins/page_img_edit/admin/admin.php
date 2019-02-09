<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	$CI->load->helper('form');
	
	require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
	
	# обновление
	if ( $post = mso_check_post(array('f_session_id', 'f_img', 'f_ok_submit')) )
	{
		mso_checkreferer();
        require_once( getinfo('common_dir') . 'meta.php' ); 

		foreach ($post['f_img'] as $key => $img)
              mso_add_meta($meta_key = $options['prev_field'], $meta_id_obj = $key, $meta_table = 'page', $meta_value = $img);
		
		echo '<div class="update">' . t('Выполнено') . '</div>';
	}

?>
<p class="info"><?= t('Все записи сайта. Используйте фильтр по рубрикам, типу или статусу для быстрого поиска нужной записи.') ?></p>
<?php
	if ( !mso_check_allow('admin_page_edit_other') )
	{
		# echo 'запрещено редактировать чужие страницы';
		$current_users_id = getinfo('session');
		$current_users_id = $current_users_id['users_id'];
	}
	else $current_users_id = false;
	
	$par = array( 
			'limit' => $options['limit'], // колво записей на страницу
			'type' => false, // любой тип страниц
			'custom_type' => 'home', // запрос как в home
			'order' => 'page_id', // запрос как в home
			'order_asc' => 'asc', // в обратном порядке
			'page_status' => false, // статус любой
			'date_now' => false, // любая дата
			'content'=> false, // без содержания
			'page_id_autor'=> $current_users_id, // только указанного автора
			'cut' => ' ',
			);
	
	$CI->db->select('category_id, category_name');
	$CI->db->order_by('category_name');
	$CI->db->where('category_type', 'page');
	
	$query = $CI->db->get('category');

	if ($query and $query->num_rows() > 0) 
	{
		//echo '<h1>Страницы по рубрикам</h1>';
		$cat_segment_id = 0;
		
		if (mso_segment(3) == 'category') $cat_segment_id = (int) mso_segment(4);
		
		echo '<p class="admin_page_filtr"><strong>'
				. t('Рубрика')
				. ':</strong> ';
		
		require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
		
		$all_cats = mso_cat_array_single('page', 'category_id', 'ASC', ''); // все рубрики для вывода кол-ва записей
		echo '<select class="admin_page_filtr">';
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		echo '<option value="' . getinfo('site_admin_url') . 'page_img_edit"' . $selected . '>' . t('Любой') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
			$selected = ($cat_segment_id != $nav['category_id']) ? '' : ' selected';
			echo '<option value="' . getinfo('site_admin_url'). 'page_img_edit/category/' . $nav['category_id'] .'"' . $selected . '>' . $nav['category_name'] . ' ('. count($all_cats[$nav['category_id']]['pages']) . ')</option>';
		}
		echo '</select>';
	}

	
	$CI->db->select('page_type_id, page_type_name');
	$CI->db->order_by('page_type_name');
	
	$query = $CI->db->get('page_type');
	
	if ($query->num_rows() > 0) 
	{
		$type_segment_id = 0;

		if (mso_segment(3) == 'type') 
		{
			$type_segment_id = (int) mso_segment(4); 
			$type_segment_name = '';
		}
		
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
				. t('Тип')
				. ':</strong> ';
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page_img_edit"' . $selected . '>' . t('Любой') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
			$selected = ($type_segment_id != $nav['page_type_id']) ? '' : ' selected';
			echo '<option value="' . getinfo('site_admin_url'). 'page_img_edit/type/' . $nav['page_type_id'] .'"' . $selected . '>' . $nav['page_type_name'] . '</option>';
			if ($selected) $type_segment_name = $nav['page_type_name'];
		}
		
		echo '</select>';
	}
	
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
			. t('Статус')
			. ':</strong> ';
	
	$all_status = array('publish', 'draft', 'private');
	
	echo '<select class="admin_page_filtr">';
	
	$selected = (!mso_segment(4) and mso_segment(3) != 'status') ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'page_img_edit"' . $selected . '>' . t('Любой') . '</option>';
	
	foreach($all_status as $status)
	{
		$selected = (mso_segment(4) == $status) ? ' selected' : '';
		echo '<option value="' . getinfo('site_admin_url'). 'page_img_edit/status/' . $status .'"' . $selected . '>' . t($status) . '</option>';
	}
	echo '</select>';
	
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' 
	. t('Превьюшка')	
	. ':</strong> ';
	
	$all_status = array('no_prev' , 'yes_prev');
	
	echo '<select class="admin_page_filtr">';
	
	$selected = (!mso_segment(4) and mso_segment(3) != 'prev') ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'page_img_edit"' . $selected . '>' . t('Любой') . '</option>';
	
	foreach($all_status as $status)
	{
		$selected = (mso_segment(4) == $status) ? ' selected' : '';
		echo '<option value="' . getinfo('site_admin_url'). 'page_img_edit/prev/' . $status .'"' . $selected . '>' . t($status) . '</option>';
	}
	echo '</select>';
	echo '</p>';
		
	//  переход на указанный url
	echo '<script>
	$("select.admin_page_filtr").change(function(){
		window.location = $(this).val();
	});
	</script>';

	if (mso_segment(3) == 'category') 
	{
		if (mso_segment(4) != '') 
		{
			$par['cat_id'] = abs(intval(mso_segment(4)));
		}
	}
	elseif (mso_segment(3) == 'type') 
	{
		if (mso_segment(4) != '') 
		{
			$par['type'] = $type_segment_name;
		}
	}
	elseif (mso_segment(3) == 'status') 
	{
		if (in_array(mso_segment(4), $all_status)) 
		{
			$par['page_status'] = mso_segment(4);
		}
	}
	elseif (mso_segment(3) == 'prev') 
	{
		if (in_array(mso_segment(4), $all_status)) 
		{
		    // получим id всех записей, у которых есть поле.
	        $CI = & get_instance();	  
	        $CI->db->select('page_id');
		    $CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
		    $CI->db->where('meta_key', $options['prev_field']);
		    $CI->db->where('meta_table', 'page');
	        $query = $CI->db->get('page');
	        
	        $pages_id = '';  
	        if ($query->num_rows() > 0)
	        {
	           $pages = $query->result_array();	
	           foreach ($pages as $page)  $pages_id .= $page['page_id'] . ',';
	        }   	

		    if (mso_segment(4) == 'no_prev') $par['exclude_page_id'] = mso_explode($pages_id);
		    else $par['page_id'] = $pages_id;
		}
	}	
	
	mso_remove_hook('content'); // удаляем все хуки по content
	
	$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	
	$this_url = getinfo('site_admin_url') . 'page_edit/';
	$view_url = getinfo('siteurl') . 'page/';
	$view_url_cat = getinfo('siteurl') . 'category/';
	$view_url_tag = getinfo('siteurl') . 'tag/';
	
		$pagination['type'] = '';
		$pagination['range'] = 10;
		mso_hook('pagination', $pagination);	
		
	if ($pages) // есть страницы
	{ 	
	
	echo '<div class="table_pages">';
	$CI->load->library('table');
	$tmpl = array (
			    	'table_open'		  => '<table class="tablesorter">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );
	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_heading('Страница' , '['.$options['prev_field'] . ']');	
		
	foreach ($pages as $page) // выводим в цикле
	{
		if (!$page['page_title']) $page['page_title'] = 'no-title';
			
		//  возможно кому-то надо
		if ($options['out_tags'])
		{
			$cats = '';
			$tags = '';
			$tagcat = '';
			
			foreach ($page['page_categories_detail'] as $key => $val)
			{
				$cats .= '<a href="' . $view_url_cat . $page['page_categories_detail'][$key]['category_slug'] . '">'
					. $page['page_categories_detail'][$key]['category_name'] . '</a>  ';
			}
			$cats = str_replace('  ', ', ', trim($cats));
			
			foreach ($page['page_tags'] as $val)
			{
				$tags .= '<a href="' . $view_url_tag . $val . '">' . $val . '</a>  ';
			}
			$tags = str_replace('  ', ', ', trim($tags));
			
			if ($cats) $tagcat .= '<br><img src="' . $plugins_url . 'page_img_edit/images/category.png' . '" alt="' . t('Рубрика:') . '"> ' . $cats;
			if ($tags) $tagcat .= '<br><img src="' . $plugins_url . 'page_img_edit/images/tag.png' . '" alt="' . t('Метки:') . '"> ' . $tags;
			
			// $tagcat .= '<p class="admin_page_qhint">' . $qhint . '</p>';
			// $date_p = '<span title="Дата и время сохранения записи">' . $page['page_date_publish'] . '</span>'; // это время публикации как установлено на сервере
			if ($tagcat) $tagcat = $tagcat;
		}
		else $tagcat = '';
			
			if(isset( $page['page_meta'][$options['prev_field']][0] ))
			{
			    $page_img = $page['page_meta'][$options['prev_field']][0];
			    $img_out = '<img class="post_img" alt="" src="' . $page_img . '">';    
			}    
			else {$page_img = ''; $img_out = '';}		
				
			// $date_p = '<span title="' . t('Дата отображения на блоге с учетом временной поправки') . '">' . mso_date_convert('Y-m-d H:i:s', $page['page_date_publish']) . '</span>';
			
			$title = '<a id="pg' . $page['page_id'] . '" class = "page_link" href="javascript: void(0);" onclick="javascript:getPI(' . $page['page_id'] . ');">' . $page['page_title'] . '</a>';
			$links = '<span class="right"><a href="' . $view_url . $page['page_slug'] . '" title="' . t('Просмотр') . '" target="_blank"><img src="' . $plugins_url . 'page_img_edit/images/goto.png' . '"></a> <a href="' . $this_url . $page['page_id']. '" title="' . t('Edit') . '"target="_blank"><img src="' . $plugins_url . 'page_img_edit/images/edit.png' . '"></a></span>';
			$input = '<input class="img_url" name="f_img[' . $page['page_id'] . ']" type="text" value="' . $page_img . '">';			
		   
		   
			$CI->table->add_row($title . ' ' .$links . $tagcat . '<br>' . $input, $img_out);

	 }
		
		echo '<form method="post">' . mso_form_session('f_session_id');

	    echo $CI->table->generate(); // вывод подготовленной таблицы
	
		echo '<button type="submit" name="f_ok_submit" onClick="if(confirm(\'' . t('Сохранить все изменения?') . '\')) {return true;} else {return false;}">' . t('Сохранить') . '</button>';
	    
	    echo '</form>';
        echo '</div>';	

	    // выбор картинок
	    echo '<div class="pics">';
	   
	    // картинки в статье 
	    echo '<H3>Установите превьюшку для выбранной страницы из контента:</H3>';
	            
	    echo '<div id="content_img" class="content_pics"></div>';
	
	    echo '<div id="dirs" class="dirs">';
	    echo '<p class="admin_files_nav"><b>' . t('dir:') . '</b><span id="select_dir"></span> <span id="goto_files"></span></p>';
	    echo '</div>';
	    
	    echo '<div id="file_img" class="content_pics"></div>';
	    
	    
        echo '<script>$(document).ready(function(){getPI('. $pages[0]['page_id'] . ');})</script>';
        echo '<div class="loader" id="loader"><img src="' .  $plugins_url . 'page_img_edit/loader.gif' . '" alt="Идет загрузка…"></div>';
        echo '<div id="result" class="result"></div>'; 	
      
      
        echo '</div>';	
			
      		
	}
	else
	{
		echo '<h2>' . t('Страниц не найдено') . '</h2>';
		
	}


	
		echo mso_load_jquery('jquery.tablesorter.js') . '
			<script>
			$(function() {
				$("table.tablesorter").tablesorter();
			});
			</script>';
			
  echo '<script type="text/javascript" src="' . $plugins_url . 'page_img_edit/gimg.js" ></script>';
  echo '<script type="text/javascript">
			var ajax_path_content = "' . getinfo('ajax') . base64_encode('plugins/page_img_edit/get_img-ajax.php') . '";
			var ajax_path_files = "' . getinfo('ajax') . base64_encode('plugins/page_img_edit/get_files-ajax.php') . '";
			var page_id = 0;
			
	function addImgPage(img) {
		var e = $("input[name=\'f_img[" + page_id +"]\']");
		if ( e.length > 0 ) 
		{
			e.val(img);
			alert("' . t('Установлено:') . ' " + img);
		}
	}			
  </script>';		
?>