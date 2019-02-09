<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

function file_manager_comuser() 
{
	// Проверка доступа и залогинености
	if (!($comuser = is_login_comuser()) ) 
	{
		return '';
	}
	
	$comusers_id = $comuser['comusers_id'];

	DEFINE('FM_GOOD_RESP', '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	DEFINE('FM_ERROR_RESP', '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}');

 // обработчик получения картинок комюзера	
 $ajax_path = getinfo('ajax') . base64_encode('plugins/file_manager/comuser-ajax.php');

	//Получаем настройки
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );	
	$allowed_types = (isset($options['allowed_types_comuser'])) ? $options['allowed_types_comuser'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|xls|7z|gz|tgz';
	
	$allowed_types = str_replace ("|", ",", $allowed_types);

  $userfile_resize = 1;
	$userfile_resize_size = (int) mso_get_option('resize_images', 'general', 600);
	if ($userfile_resize_size < 1) $userfile_resize_size = 600;

  $userfile_mini = 1;
	$mini_type = mso_get_option('image_mini_type', 'general', 1);	
	$userfile_mini_size = (int) mso_get_option('size_image_mini', 'general', 150);
	if ($userfile_mini_size < 1) $userfile_mini_size = 150;

	$water_type = mso_get_option('watermark_type', 'general', 1);
	$userfile_water = mso_get_option('use_watermark', 'general', 0);
	
	
	$hide_options = (isset($options['hide_options'])) ? $options['hide_options'] : '1';
	$show_size = (isset($options['show_size'])) ? $options['show_size'] : '1';

  $subdir = (isset($options['user_subdir'])) ? $options['user_subdir'] : 'userfile';
  
  $out = '';


/* Thirdparty intialization scripts, needed for the Google Gears and BrowserPlus runtimes 
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/gears_init.js"></script>
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> -->
*/

	$uploader_dir = getinfo('plugins_url') . 'file_manager/uploader/';
	
  $out .= '
  	<link href="' . getinfo('plugins_url') . 'file_manager/comuser-style.css" rel="stylesheet" type="text/css">

    <style type="text/css">@import url(' . $uploader_dir . 'css/comuser-plupload.queue.css);</style>
    <script type="text/javascript" src="' . $uploader_dir . 'js/plupload.full.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/jquery.plupload.queue.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/i18n.js"></script>

    <script src="' . getinfo('plugins_url') . 'file_manager/comuser-fm.js"></script>
   ';

  echo mso_load_jquery('alerts/jquery.alerts.js');
	echo mso_load_jquery('cornerz.js');
	echo '	<link href="' . getinfo('common_url') . 'jquery/alerts/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen">';

	global $MSO;

  $out .=' <input type="hidden" id="f_ajax_path" value="' . $ajax_path . '">';
  
	// результат загрузки
	$out .= '<div id="results"><p><a href="javascript:void(0)" onclick="javascript:showresuts();" title="Показать/скрыть">Результаты загрузки.</a>
	<span id="upload_tips"> Нужно кликнуть, чтобы добавить в сообщение.</span></p>
	<div class="files_list" id="upl_message">
	</div></div>';	

 // картинки
	$out .= '<p id="show_dir"><a href="javascript:void(0)" onclick="javascript:showdir();" title="Показать/скрыть">Мои фото<b>+</b></a></p><p id="hide_dir"><a href="javascript:void(0)" onclick="javascript:hidedir();" title="Показать/скрыть">Мои фото<b>-</b></a> Кликните на миниатюре для добавления в сообщение</p>';
	
	$out .= '<div id="files_block" class="files_block">
	<div class="files_list" id="files_list"></div>
	<div id="files_tips" class="files_tips"></div>
	<div style="clear:both;"></div>
  <hr class="br"><br></div>';	



// загрузчик
$out .='<div><a href="javascript: void(0);" title="Показать/скрыть" onclick="javascript:showuploader();">Загрузить фото</a></div>
	<div id="uploader_block">';

// дерево
	$out .= '
	<div id="img_cat" style="width:200px; height:180px; background-color:#f5f5f5;border :1px solid Silver; overflow:auto; margin-top:5px; float:left">';

  $out .= getCatTree($comusers_id);
	
	$out .= '</div>';// дерево каталогов
		
	// поле для передачи текущего каталога
	$out .= '<input type="hidden" id="f_current_dir" class="current_dir" value="">';
	 
	 
	 // загрузчик файла
	$out .= '<div class="upload_file" style="margin-left:220px;">';

// var cod = \'<div class="gallery"><a href="\' + upl_url + file.name + \'" title="\' + title + \'"><img alt="\' + title + \'" src="\' + upl_url + \'mini/\' + file.name + \'"></a></div>\';

$out .= '
<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready

$(function() {
	$("#uploader").pluploadQueue({
		// General settings
		runtimes : \'html5,html4\', //+ flash,silverlight,gears,browserplus
		url : \'' . getinfo('require-maxsite') . base64_encode('plugins/file_manager/comuser-require-maxsite.php') . '\',
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
					f_userfile_resize		: \'' . $userfile_resize . '\',							
					f_userfile_resize_size	: \'' . $userfile_resize_size . '\',							
					f_userfile_mini			: \'' . $userfile_mini . '\',							
					f_userfile_mini_size	: \'' . $userfile_mini_size . '\',							
					f_mini_type				: \'' . $mini_type . '\',							
					f_userfile_water		: \'' . $userfile_water . '\',							
					f_water_type			: \'' . $water_type . '\',											
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
			  
			    var upl_url = \'' . getinfo('uploads_url') . $subdir . '/' . $comusers_id . '/' . '\' + document.getElementById("f_current_dir").value;
			    var title = document.getElementById("f_userfile_title").value;
			    fnid = \'img_\' + file.name.replace(".","_");
			    
			    var cod = \'[myfile]\' +  file.name + \'[/myfile]\';	
			    
			    var add = \'<div class="file_block"><a href="javascript:void(0)" onClick="addimg(\' + "\'" + fnid + "\'" + \') " title="Добавить в сообщение"><img alt="" src="\' + upl_url + \'_mso_i/\' + file.name + \'"></a><textarea style="display:none" id="\' + fnid + \'" rows="3" cols="80">\' + cod + \'</textarea>\</div>\';			  
			  
				if(info.response == \'' . FM_GOOD_RESP . '\')
				{
				  showfiles();
				
					// var files_list = document.getElementById("files_list");
					
					// files_list.innerHTML += add;
					// upl_message.innerHTML += add;

					
					$(".error").fadeOut();
					$(".update").fadeOut();
				} 
				else
				 if(info.response.indexOf(\'' . FM_GOOD_RESP . '\') + 1)
				 {
					upl_message.innerHTML += add;
					var files_list = document.getElementById("files_list");
					files_list.innerHTML += add;
					
					$(".error").fadeOut();
				  info.response = info.response.replace(\'' . FM_ERROR_RESP . '\',"");
					upl_message.innerHTML += \'<div class="update">Файл \' + file.name + \' загрузился, но в ходе обработки возникли некоторые ошибки: </div>\' + info.response;
				 } 
				 else
				 {
				  info.response = info.response.replace(\'' . FM_ERROR_RESP . '\',"");
					upl_message.innerHTML += \'<div class="error">Файл \' + file.name + \' не загрузился из-за ошибок:\' + info.response + \'</div>\';
				 }	
				
				$("#results").fadeIn() ;	
				$("#upload_tips").fadeIn() ;
				
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

';

$out .= '<input type="hidden" id="f_session2_id" value="'. $MSO->data['session']['session_id'] .'">

	</div>
    <div id="upload_settings" class="upload_settings">
		<p>' . t('Описание файла:', 'admin') . ' <input type="text" id="f_userfile_title" class="description_file" value=""></p>
    </div>
			<div id="load_tips" class="load_tips">' . t('Добавьте файлы в список и нажмите "Загрузить". Загрузка произведется в выбранный раздел. Максимальный размер файла ', 'admin') . ' ' . ini_get ('upload_max_filesize') . '.</div>
</div>	
	';	
	
 return $out;
}

// выведем разделы файлов
function getCatTree($comusers_id=0)
{
  if (!$comusers_id) return '';

	$cache_key = 'fmtree_' . $comusers_id;
	$k = mso_get_cache($cache_key, true);
//	if($k) return $k;
  /*
  $comusers_dir = getinfo('uploads_dir') . 'files/' . $comusers_id . '/';
  $result = '';
  
  // создаем каталог комюзера если нет
	if (!is_dir($comusers_dir)) @mkdir($comusers_dir); // нет каталога, пробуем создать
	else
	{
	  $fn_dir_names = $comusers_dir . '_dir_names.dat';
	  if (file_exists($fn_dir_names))
	  {
	    $dir_names = unserialize($fn_dir_names); // получим из файла все описания
	    foreach ($dir_names as $dir_name)
	    {
	    }
	  }
	  else
	  {
	    write_file($fn_dir_names, serialize(array()) );
	  }
	}
	*/
	$k = 'Все файлы';
  mso_add_cache($cache_key, $k, false, true);
	return $k;
}



?>