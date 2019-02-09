<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Page attachments plugin for MaxSite CMS
 * (c) http://www.moneymaker.ru/
 */

function page_attachments_autoload($args = array())
{
	mso_create_allow('page_attachments_edit', t('Админ-доступ к page_attachments', __FILE__));
	mso_hook_add('admin_init', 'page_attachments_admin_init'); # хук на админку

	mso_hook_add('admin_page_form_add_all_meta', 'page_attachments_page_form_add_all_meta');
	mso_hook_add('new_page', 'page_attachments_custom_new');
	mso_hook_add('edit_page', 'page_attachments_custom_edit');
	
}

function page_attachments_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function page_attachments_deactivate($args = array())
{	
	//mso_delete_option('plugin_page_attachments', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function page_attachments_uninstall($args = array())
{	
	mso_delete_option('plugin_page_attachments', 'plugins'); // удалим созданные опции
	mso_remove_allow('page_attachments_edit'); // удалим созданные разрешения
	return $args;
}

function page_attachments_page_form_add_all_meta($args = array()){
	require_once( getinfo('common_dir') . 'meta.php' );

	$id = mso_segment(3);
	if (!is_numeric($id) || empty($id)) $id = false; // не число
	else $id = (int) $id;

	$plugin_path = getinfo('plugins_url') . 'page_attachments/';
	$path_pics = $plugin_path . 'i/';
	
	$path_ajax = getinfo('ajax').base64_encode('plugins/page_attachments/functions-ajax.php' );

	$out = '';
	if($id) $image_small = mso_get_meta('image_small', 'page', $id);
	$image_small = '<div id="div_image_small">'.(!empty($image_small) 
	? '<input type="text" name="f_options[image_small]" value="'.$image_small[0]['meta_value'].'" style="width:80%;" readonly="readonly" /> <a href="'.$image_small[0]['meta_value'].'" class="lightbox" target="_blank"><img src="'.$path_pics.'icon_view.gif" style="width:12px; height:13px; border:0; vertical-align:middle;" alt="посмотреть" /></a> <a href="#" class="delete_button" id="image_small-'.$image_small[0]['meta_id_obj'].'"><img src="'.$path_pics.'icon_delete.gif" style="width:12px; height:13px; border:0; vertical-align:middle;" alt="удалить" /></a>' 
	: 
	'<input type="file" name="f_options[image_small]" />').'</div>';

	if($id) $image_big =  mso_get_meta('image_big', 'page', $id);	
	$image_big = '<div id="div_image_big">'.(!empty($image_big) 
	? '<input type="text" name="f_options[image_big]" value="'.$image_big[0]['meta_value'].'" style="width:80%;" readonly="readonly" /> <a href="'.$image_big[0]['meta_value'].'" class="lightbox" target="_blank"><img src="'.$path_pics.'icon_view.gif" style="width:12px; height:13px; border:0; vertical-align:middle;" alt="посмотреть" /></a> <a href="#" class="delete_button" id="image_big-'.$image_big[0]['meta_id_obj'].'"><img src="'.$path_pics.'icon_delete.gif" style="width:12px; height:13px; border:0; vertical-align:middle;" alt="удалить" /></a>' 
	: 
	'<input type="file" name="f_options[image_big]" />').'</div>';

	$out .= '<div>
	<h3>Превью</h3>
	' . $image_small . '
	<p>Загрузка превью</p>
	</div>';
	
	$out .= '<div>
	<h3>Фото</h3>
	' . $image_big . '
	<p>Загрузка фото</p>
	</div>';
	$out .= '<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />';
	
	$out .= '<script type="text/javascript">
	var path_ajax="'.$path_ajax.'";
	$(document).ready(function(){
		$(".delete_button").click(function() {
			if (window.confirm("'.t('Вы действительно хотите удалить файл?', 'plugins').'")) {
				var elem = $(this);
				$.ajax({
					type: "POST",
					url:path_ajax,
					data: "del=" + elem.attr("id"),
					dataType: "json",
					cache:false,
					success: function(result)
					{
						if(result.status == "deleteOk")
						{
							var key = elem.parent().find("input").attr("name");
							elem.parent().html("<input type=\"file\" name=\"" + key + "\" />");
							alert(result.message);
						}
						else if(result.status == "deleteErr")
						{
							alert(result.message);
						}
					}
				});
			}
			return false;
		});
	}); 
	</script>';
	
	return $out;
}

function page_attachments_custom_new($args = array()){
	$id = $args[0];
	
	$uploads_dir = getinfo('uploads_dir');
	$uploads_url = getinfo('uploads_url');

	$CI = & get_instance();

	$config['upload_path'] = $uploads_dir;
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']	= '100';
	$config['max_width']  = '1024';
	$config['max_height']  = '768';

	$CI->load->library('upload', $config);

	if(!empty($_FILES)){
		$_FILES = page_attachments_format_files_array($_FILES);
		//print_r($_FILES);
		foreach($_FILES AS $key => $param){
			$CI->upload->initialize($config);
			if (!$CI->upload->do_upload($key))
			{
				$error = array('error' => $CI->upload->display_errors());
				//print_r($error);
			}	
			else
			{
				$data = array('upload_data' => $CI->upload->data());
				$CI->db->insert('mso_meta', 
				array(
					'meta_key' => $key,
					'meta_id_obj' => $id,
					'meta_table' => 'page',
					'meta_value' => $uploads_url.$data['upload_data']['file_name']
				));
				//print_r($data);
			}
		}
	}	
	return $args;
}

function page_attachments_custom_edit($args = array()){
	$id = mso_segment(3);
	if (!is_numeric($id)) $id = false; // не число
	else $id = (int) $id;

	$uploads_dir = getinfo('uploads_dir');
	$uploads_url = getinfo('uploads_url');

	$CI = & get_instance();

	$config['upload_path'] = $uploads_dir;
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']	= '100';
	$config['max_width']  = '1024';
	$config['max_height']  = '768';

	$CI->load->library('upload', $config);

	if(!empty($_FILES)){
		$_FILES = page_attachments_format_files_array($_FILES);
		//print_r($_FILES);
		foreach($_FILES AS $key => $param){
			$CI->upload->initialize($config);
			if (!$CI->upload->do_upload($key))
			{
				$error = array('error' => $CI->upload->display_errors());
				//print_r($error);
			}	
			else
			{
				$data = array('upload_data' => $CI->upload->data());
				$CI->db->insert('mso_meta', 
				array(
					'meta_key' => $key,
					'meta_id_obj' => $id,
					'meta_table' => 'page',
					'meta_value' => $uploads_url.$data['upload_data']['file_name']
				));
				//print_r($data);
			}
		}
	}
	
	return $args;
}

/** helper fuctions */

// for normal use multi-dimensional $_FILES array with CI uploader library
function page_attachments_format_files_array($files, $first_key = 'f_options'){
	$new = array();
	$names = array( 'name','type','type', 'tmp_name', 'error', 'size');

	foreach($files[$first_key]['name'] AS $key => $val){
		foreach($names AS $name){
			$new[$key][$name] = $files[$first_key][$name][$key];
		}
	}
    return $new;
}
?>