<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

// вставка загрузчика в редактор постов

function file_manager_form() 
{
   require_once (getinfo('plugins_dir') . 'file_manager/functions.php');


 // обработчик получения картинок в директории	
 $ajax_path = getinfo('ajax') . base64_encode('plugins/file_manager/ajax-ajax.php');

	//Получаем настройки
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );	
	$allowed_types = (isset($options['allowed_types'])) ? $options['allowed_types'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';
	$hide_options = (isset($options['hide_options'])) ? $options['hide_options'] : '1';
	$show_size = (isset($options['show_size'])) ? $options['show_size'] : '1';

  $sort_type = (isset($options['sort_type'])) ? $options['sort_type'] : '1';

  $out = '';


/* Thirdparty intialization scripts, needed for the Google Gears and BrowserPlus runtimes 
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/gears_init.js"></script>
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> -->
*/

	$uploader_dir = getinfo('plugins_url') . 'file_manager/uploader/';
	$tree_dir = getinfo('plugins_url') . 'file_manager/tree/';
	
  $out .= '
    <script src="' . getinfo('plugins_url') . 'file_manager/fm.js"></script>
   ';


	global $MSO;
	$allowed_types = str_replace ("|", ",", $allowed_types);

	$resize_images = (int) mso_get_option('resize_images', 'general', 600);
	if ($resize_images < 1) $resize_images = 600;
	
	$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 150);
	if ($size_image_mini < 1) $size_image_mini = 150;

	$watermark_type = mso_get_option('watermark_type', 'general', 1);
	
	$mini_type = mso_get_option('image_mini_type', 'general', 1);	

  $out .=' <input type="hidden" id="f_ajax_path" value="' . $ajax_path . '">';
  
	// результат загрузки
	// $out .= '<div id="results"><p><a href="javascript:void(0)" onclick="javascript:showresuts();" title="Показать/скрыть">Результаты загрузки</a></p><div class="upl_message" id="upl_message"></div></div>';	

 // файлы
	$out .= '<p><a href="javascript:void(0)" onclick="javascript:showdir();" title="Показать/скрыть">Загруженные</a></p>';
	$out .= '
	<div id="files_block" class="files_block">
	<div id="treeboxbox_tree" setImagePath="' . getinfo('plugins_url') . 'file_manager/tree/csh_dhx_skyblue/" style="width:220px; height:250px; background-color:#f5f5f5;border :1px solid Silver; overflow:auto; margin-top:5px; float:left">';

	$k = getTreeFromCache();
	if($k){
		$out .= $k;
	} else {
		$k = getFoldersTree();
		$cache_key = 'fmtree_' . mso_md5($_SERVER['REQUEST_URI']);
		mso_add_cache($cache_key, $k, false, true);
		$out .= $k;	}
	
	$out .= '</div>';// дерево каталогов
   $out .= '	
	<div class="files_list" id="files_list"></div>
	<div style="clear:both;"></div>
			<div class="sort_list"><p>' . t('Сортировка', 'admin') . ': <select id="f_sort_type">
		<option value="1"'.(($sort_type == 1)?(' selected="selected"'):('')).'>' . t('По дате загрузки', 'admin') . '</option>
		<option value="2"'.(($sort_type == 2)?(' selected="selected"'):('')).'>' . t('По дате загрузки обратно', 'admin') . '</option>
		<option value="3"'.(($sort_type == 3)?(' selected="selected"'):('')).'>' . t('По алфавиту', 'admin') . '</option>
		<option value="4"'.(($sort_type == 4)?(' selected="selected"'):('')).'>' . t('По алфавиту обратно', 'admin') . '</option>
		<option value="5"'.(($sort_type == 5)?(' selected="selected"'):('')).'>' . t('По ширине', 'admin') . '</option>
		<option value="6"'.(($sort_type == 6)?(' selected="selected"'):('')).'>' . t('По высоте', 'admin') . '</option>
		<option value="6"'.(($sort_type == 7)?(' selected="selected"'):('')).'>' . t('По положению', 'admin') . '</option>
		
		</select></p></div>	
	';
	
	// поле для передачи текущего директория
	$out .= '<input type="hidden" id="f_current_dir" class="current_dir" value="">';	
  $out .='	
	<div id="files_tips" class="files_tips">Для загрузки в выбранный каталог откройте секцию "Загрузить"</div>
  <hr class="br"><br>
  </div>';	

// загрузчик

$out .='<div><a href="javascript: void(0);" title="Показать/скрыть" onclick="javascript:showuploader();">Загрузить</a></div>
  <div id="uploader_block">
	<div id="load_tips" class="load_tips">' . t('Добавьте файлы в список и нажмите "Загрузить". Максимальный размер файла ', 'admin') . ' ' . ini_get ('upload_max_filesize') . '.</div>  
	<div class="load_tips">Выбор каталога для загрузки в секции загруженных.</div>
	<div class="upload_file">';


$out .= '
<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready

$(function() {
	$("#uploader").pluploadQueue({
		// General settings
		runtimes : \'html5,html4\', //+ flash,silverlight,gears,browserplus
		url : \'' . getinfo('require-maxsite') . base64_encode('plugins/file_manager/uploader/upload-require-maxsite.php') . '\',
		max_file_size : \'' . ini_get('upload_max_filesize') .'\',		

		// Specify what files to browse for
		filters : [
			{title : "Allowed files", extensions : "' . $allowed_types . '"}
		],

		//headers: {REFERER : \'http://max-latest.xp/admin/file_manager\'},
		
		// Flash settings
		//flash_swf_url : \'' . $uploader_dir . 'js/plupload.flash.swf\',

		// Silverlight settings
		//silverlight_xap_url : \'' . $uploader_dir . 'js/plupload.silverlight.xap\',
		
		// PreInit events, bound before any internal events
		preinit: {		
			Init: function(up, info) {
				if(info.runtime === \'html4\') up.settings.filters = false;
			},
			UploadFile: function(up, file) {	
				up.settings.multipart_params = {
					f_userfile_title		: document.getElementById("f_userfile_title").value,				
					f_userfile_resize		: document.getElementById("f_userfile_resize").checked ? \'1\' : \'\',							
					f_userfile_resize_size	: document.getElementById("f_userfile_resize_size").value,							
					f_userfile_mini			: document.getElementById("f_userfile_mini").checked ? \'1\' : \'\',							
					f_userfile_mini_size	: document.getElementById("f_userfile_mini_size").value,							
					f_mini_type				: document.getElementById("f_mini_type").value,							
					f_userfile_water		: (document.getElementById("f_userfile_water").checked && document.getElementById("f_userfile_water").disabled == false) ? \'1\' : \'\',							
					f_water_type			: document.getElementById("f_water_type").value,						
					f_session2_id			: document.getElementById("f_session2_id").value,
					f_directory				: \'' . getinfo('upoads_dir') . '\' + document.getElementById("f_current_dir").value
				};
			}
		},
		
		init: {
			StateChanged: function(up) {
				// Called when the state of the queue is changed
				if(up.state == 1){
					$(\'#file_upload\').submit();
				}
			},
			
			// Called when a file has finished uploading
			FileUploaded: function(up, file, info) {	
			
			  var upl_message = document.getElementById("upl_message");
			  var upl_url = \'' . getinfo('uploads_url') . '\' + document.getElementById("f_current_dir").value;
			  var title = document.getElementById("f_userfile_title");
			  
				if(info.response == \'' . FM_GOOD_RESP . '\')
				{
				 	 // upl_message.innerHTML += \'<p>Загружено</p>\';
			     showfiles(document.getElementById("f_current_dir").value);
				} 
				else
				 if(info.response.indexOf(\'' . FM_GOOD_RESP . '\') + 1)
				 {
					upl_message.innerHTML += \'<div class="update">Файл \' + file.name + \' загрузился, но в ходе обработки возникли некоторые ошибки: </div>\' + info.response;			
				 } 
				 else
				 {
					upl_message.innerHTML += \'<div class="error">Файл \' + file.name + \' не загрузился из-за ошибок:\' + info.response + \'</div>\';
				 }	
				
				$("#results").fadeIn() ;	
				$(".plupload_buttons").fadeIn();
        $(".plupload_upload_status").fadeOut();		
				var load_tips = document.getElementById("load_tips");
			},
		}
	});

});

</script>


	<div id="uploader" style="height:190px;">
		<p>You browser doesn\'t have Silverlight, Gears, BrowserPlus or HTML5 support.</p>
		<input type="hidden" name="lolo" value="123">
	</div>
</div>
';

// настройки	
$out .= '		
    <input type="hidden" id="f_session2_id" value="'. $MSO->data['session']['session_id'] .'">
		<p>' . t('Описание файла:', 'admin') . ' <input type="text" id="f_userfile_title" class="description_file" value=""></p>
    <p><a href="javascript:void(0)" onclick="javascript:showsettings();" title="Показать/скрыть">Настройки обработки рисунков</a></p>
 </div>
	';	

$out .= '		
    <div id="upload_settings" class="upload_settings">

		<p><label><input type="checkbox" id="f_userfile_resize" checked="checked" value=""> ' . t('Для изображений изменить размер до', 'admin') . '</label>
			<input type="text" id="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).', 'admin') . '</p>

		<p><label><input type="checkbox" id="f_userfile_mini" checked="checked" value=""> ' . t('Для изображений сделать миниатюру размером', 'admin') . '</label>
			<input type="text" id="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '"> ' . t('px (по максимальной стороне).', 'admin') . ' <br><em>' . t('Примечание: миниатюра будет создана в подкаталоге', 'admin') . ' <strong>mini</strong></em></p>


		<p>' . t('Миниатюру делать путем:', 'admin') . ' <select id="f_mini_type">
		<option value="1"'.(($mini_type == 1)?(' selected="selected"'):('')).'>' . t('Пропорционального уменьшения', 'admin') . '</option>
		<option value="2"'.(($mini_type == 2)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) по центру', 'admin') . '</option>
		<option value="3"'.(($mini_type == 3)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого верхнего края', 'admin') . '</option>
		<option value="4"'.(($mini_type == 4)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого нижнего края', 'admin') . '</option>
		<option value="5"'.(($mini_type == 5)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого верхнего края', 'admin') . '</option>
		<option value="6"'.(($mini_type == 6)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого нижнего края', 'admin') . '</option>
		<option value="7"'.(($mini_type == 7)?(' selected="selected"'):('')).'>' . t('Уменьшения и обрезки (crop) в квадрат', 'admin') . '</option>
		</select></p>

		<p><label><input type="checkbox" id="f_userfile_water" value="" '.
			((file_exists(getinfo('uploads_dir') . 'watermark.png')) ? '' : ' disabled="disabled"') . 
			((mso_get_option('use_watermark', 'general', 0)) ? (' checked="checked"') : ('')) .
			'> ' . t('Для изображений установить водяной знак', 'admin') . '
			<select id="f_water_type">
			<option value="1"'.(($watermark_type == 1)?(' selected="selected"'):('')).'>' . t('По центру', 'admin') . '</option>
			<option value="2"'.(($watermark_type == 2)?(' selected="selected"'):('')).'>' . t('В левом верхнем углу', 'admin') . '</option>
			<option value="3"'.(($watermark_type == 3)?(' selected="selected"'):('')).'>' . t('В правом верхнем углу', 'admin') . '</option>
			<option value="4"'.(($watermark_type == 4)?(' selected="selected"'):('')).'>' . t('В левом нижнем углу', 'admin') . '</option>
			<option value="5"'.(($watermark_type == 5)?(' selected="selected"'):('')).'>' . t('В правом нижнем углу', 'admin') . '</option>
			</select>
			</label>
			<br><em>' . t('Примечание: водяной знак должен быть файлом <strong>watermark.png</strong> и находиться в каталоге', 'admin') . ' <strong>uploads</strong></em></p>
		</div>';

 return '<div class = "block_page page_file">' . $out . '</div>';
}


?>