<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 

	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_delete')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$page_id = (int) $post['f_page_delete'];
		if (!is_numeric($page_id)) $page_id = false; // не число
			else $page_id = (int) $page_id;

		if (!$page_id) // ошибка! 
		{
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
		}
		else 
		{
			$data = array(
				'user_login' => $MSO->data['session']['users_login'],
				'password' => $MSO->data['session']['users_password'],
				'page_id' => $page_id,
			);
			
			require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
			
			$result = mso_delete_page($data);
			
			if (isset($result['result']) and $result['result'])
			{
				if ( $result['result'] ) 
				{
					# mso_flush_cache(); // сбросим кэш перенес в mso_delete_page
					echo '<div class="update">' . t('Страница удалена') . '</div>';
				}
				else
				{
					echo '<div class="error">' . t('Ошибка при удалении') . ' ('. $result['description'] . ')</div>';
				}
			}
			else
			{
				echo '<div class="error">' . t('Ошибка при удалении') . ' ('. $result['description'] . ')</div>';
			}

		}
	}
	

?>
<h1><?= t('Записи') ?></h1>
<p class="info"><?= t('Все записи сайта. Нажмите "+" или "-" для раскрытия или сворачивания списка.') ?></p>

<?php
/*
	$CI->load->library('table');
	$CI->load->helper('form');
	
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', t('Заголовок'), t('Дата'), t('Тип'), t('Статус'), t('Автор'));
	
	
	if ( !mso_check_allow('admin_page_edit_other') )
	{
		# echo 'запрещено редактировать чужие страницы';
		$current_users_id = getinfo('session');
		$current_users_id = $current_users_id['users_id'];
	}
	else $current_users_id = false;
	*/
	
	/*$par = array( 
			'limit' => 30, // колво записей на страницу
			'type' => false, // любой тип страниц
			'custom_type' => 'home', // запрос как в home
			'order' => 'page_date_publish', // запрос как в home
			'order_asc' => 'desc', // в обратном порядке
			'page_status' => false, // статус любой
			'date_now' => false, // любая дата
			//'content'=> false, // без содержания
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
		# pr($all_cats);
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любая') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
			
			$selected = ($cat_segment_id != $nav['category_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/category/' . $nav['category_id'] .'"' . $selected . '>' . $nav['category_name'] . ' ('. count($all_cats[$nav['category_id']]['pages']) . ')</option>';
		

		}

		echo '</select>';
	}
*/
	
	/*
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
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
		
			$selected = ($type_segment_id != $nav['page_type_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/type/' . $nav['page_type_id'] .'"' . $selected . '>' . $nav['page_type_name'] . '</option>';
			
			if ($selected) $type_segment_name = $nav['page_type_name'];
			
		}
		
		echo '</select>';
	}
	*/
	/*
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
			. t('Статус')
			. ':</strong> ';
	
	$all_status = array('publish', 'draft', 'private');
	
	echo '<select class="admin_page_filtr">';
	
	$selected = (!mso_segment(4) and mso_segment(3) != 'status') ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
	
	foreach($all_status as $status)
	{
		$selected = (mso_segment(4) == $status) ? ' selected' : '';
			
		echo '<option value="' . getinfo('site_admin_url'). 'page/status/' . $status .'"' . $selected . '>' . t($status) . '</option>';
		
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
	
	mso_remove_hook('content'); // удаляем все хуки по content*/
/*=======================================начало таблицы===========================================*/	
	//$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	
	$all_pages = array(); // сразу список всех страниц для формы удаления
	
	$this_url = getinfo('site_admin_url') . 'page_edit/';
	$view_url = getinfo('siteurl') . 'page/';
	$view_url_cat = getinfo('siteurl') . 'category/';
	$view_url_tag = getinfo('siteurl') . 'tag/';
/*===============================скрипты=============================*/		


/*echo '<link rel="stylesheet" href="http://novice2ninja.ru/jq-src/chapter_08/04_expandable_tree/style.css" type="text/css" media="screen" charset="utf-8"/>';*/


/*echo '
<script type="text/javascript" >
$(function(){
	$("ul.my-li").eq(0).removeClass("my-li").attr("id","celebTree");
	$("ul.my-li").removeClass("my-li");
	});


</script>';
;*/

echo '
<script type="text/javascript" >
$(function(){
	$("ul.treeview-black").eq(0).attr("id","tree");
	
	});
</script>';
/*
echo '
<script>
  $(document).ready(function(){
  $("#black").treeview();
  });
  </script>';
*/
echo '		
<script type="text/javascript">
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "cookie"
			});
		})
		
	</script>
';
/*
$("#red").treeview({
		animated: "fast",
		collapsed: true,
		unique: true,
		persist: "cookie",
		toggle: function() {
			window.console && console.log("%o was toggled", this);
		}

echo '
<script type="text/javascript" >
$("#tree").treeview({
		control: "#sidetreecontrol",
		persist: "cookie",
		cookieId: "treeview-black"

</script>';
;*/
/*
echo "
<script type=\"text/javascript\" >
$(document).ready(function() {
  $('#celebTree ul')
    .hide()
    .prev('span')
    .before('<span></span>')
    .prev()
    .addClass('handle closed')
    .click(function() {
      // plus/minus handle click
      $(this)
        .toggleClass('closed opened')
        .nextAll('ul')
        .toggle();
    });
});
</script>";*/

/*==============================////скрипты=========================*/

/*========================================*/


$CI->db->select('page_id,page_id_parent,page_title,page_slug,page_status');
//$CI->db->where('page_id_parent','0');
	if ($query = $CI->db->get('page')) {
			$result = $query->result_array();
		}
//print_r($result);

$cats = array();
//В цикле формируем массив разделов, ключом будет id родительской категории

	foreach ($result as $cat) {
      $cats[$cat['page_id_parent']][] =  $cat;
}
//print_r($cats);
/*
function build_tree($cats,$parent_id){
  global $this_url;
  
  if(is_array($cats) and isset($cats[$parent_id])){
    $tree = '<ul class="my-li">';
    foreach($cats[$parent_id] as $cat){
       $tree .= '<li><span><a href="page_edit/'.$cat['page_id'].'" title="'.$cat['page_slug'].'">'.$cat['page_title'].'</a></span>
	   <span class="li-red"><a href="page_new/'.$parent_id.'" target="_blank">создать</a></span>
	   <span class="li-red-2"><a href="page_new/'.$cat['page_id'].'" target="_blank">внутри</a></span>';
       
	   
	   $tree .=  build_tree($cats,$cat['page_id']);
       $tree .= '</li>';
    }
    $tree .= '</ul>';
  }
  else return null;
  return $tree;
}
*/
//$img_add = '<img src="'.getinfo('plugins_url').'admin_struct/img/add_new.png" />';

/*
function build_tree($cats,$parent_id){
	$img_add = '<img src="'.getinfo('plugins_url').'admin_struct/img/add_new.png" />';
	$img_add_in = '<img src="'.getinfo('plugins_url').'admin_struct/img/add_new_in.png">';
	$img_del = '<img src="'.getinfo('plugins_url').'admin_struct/img/delete.png">';
  	$img_skrut = '<img src="'.getinfo('plugins_url').'admin_struct/img/skrut.png">';
  
  $tit_add = 'Создать запись в текущей директории';
  $tit_add_in = 'Создать дочернюю страницу';
  $tit_del = 'Удалить страницу';
  $tit_skrut = 'Публикация';
  
  global $this_url;
  
  if(is_array($cats) and isset($cats[$parent_id])){
    $tree = '<ul class="treeview-black">';
    foreach($cats[$parent_id] as $cat){
       if ($cat['page_status'] == 'draft') {
		   $stl = 'style="color:#CCC"';
	   }else{
		   $stl = '';
	   }
	   
	   $tree .= '<li><div class="in-hov"><a '.$stl.' href="page_edit/'.$cat['page_id'].'" title="'.$cat['page_slug'].'">'.$cat['page_title'].'</a>
	   <span><a href="/admin/page_new/'.$parent_id.'" title="'.$tit_add.'">'.$img_add.'</a>&nbsp;&nbsp;
	   <a href="/admin/page_new/'.$cat['page_id'].'" title="'.$tit_add_in.'">'.$img_add_in.'</a>&nbsp;&nbsp;
	   <a class="skrut-page" href="'.$cat['page_id'].'" title="'.$tit_skrut.'">'.$img_skrut.'</a>&nbsp;&nbsp;
	   <a class="skrut-page" href="'.$cat['page_id'].'" title="'.$tit_del.'">'.$img_del.'</a>
	   </span>
	   </div>';
	   
	   
	   $tree .=  build_tree($cats,$cat['page_id']);
       $tree .= '</li>';
    }
    $tree .= '</ul>';
  }
  else return null;
  return $tree;
}


echo '<div id="sidetreecontrol"><a href="?#">Свернуть все</a> | <a href="?#">Раскрыть все</a></div>';


echo build_tree($cats,0);
*/
/*================================================================*/
require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик 
/*================================================================*/
/*================================================================*/
/*================================================================*/
//$all = mso_cat_array_single('page', 'category_name', 'DESC', '', true, true);

$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_menu_order', 'asc', false, false, false, false, false, false, true);
//print_r($all);


$format = '
	<div class="in-hov">[TITLE_HTML](<a class="skrut-page" href="[ID]">[COUNT]</a>)</div>';	
//echo '<ul class="treeview-black" id="tree">';/*----------------------------------------*/
$out = mso_create_list($all, 
		array(
			'childs'=>'childs', 
			'format'=>$format, 
			'format_current'=>$format, 
			'class_ul'=>'treeview-black', 
			
			//'class_ul_style'=>'list-style-type: none; margin: 0 0 0 -30px;', 
			//'class_child_style'=>'list-style-type: none;', 
			//'class_li_style'=>'margin: 0 0 10px 30px;',
			
			'title'=>'category_name', 
			'link'=>'category_slug', 
			'current_id'=>false, 
			'prefix'=>'category/', 
			'count'=>'pages_count', 
			'id'=>'category_id', 
			'slug'=>'page_slug',
			'pag' => 'pages',
			'menu_order'=>'category_menu_order', 
			'id_parent'=>'category_id_parent'
			) );
	
	// добавляем форму, а также текущую сессию
echo '<div class="tree-div">';	
echo $out;
echo '<div class="list-art"></div>';
echo '</div>';
//echo '</ul>';/*----------------------------------------*/
?>