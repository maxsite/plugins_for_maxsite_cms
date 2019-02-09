<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

// используется в плагине profile
// вставляем файловый загрузчик для комюзера $comusers_id
// 

function file_manager_profile($comusers_id=0) 
{
	DEFINE('FM_GOOD_RESP', '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	DEFINE('FM_ERROR_RESP', '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}');

	//Получаем настройки
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );	
	$allowed_types = (isset($options['allowed_types_comuser'])) ? $options['allowed_types_comuser'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|xls|7z|gz|tgz';
	
	$allowed_types = str_replace ("|", ",", $allowed_types);
	
	$hide_options = (isset($options['hide_options'])) ? $options['hide_options'] : '1';
	$show_size = (isset($options['show_size'])) ? $options['show_size'] : '1';

  $subdir = (isset($options['user_subdir'])) ? $options['user_subdir'] : 'userfile';
  $curdir = $subdir . '/' . $comusers_id;
  
  $out = '';


/* Thirdparty intialization scripts, needed for the Google Gears and BrowserPlus runtimes 
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/gears_init.js"></script>
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> -->
*/

  // подключим скрипты загрузчика
	$uploader_dir = getinfo('plugins_url') . 'file_manager/uploader/';
  $out .= '
  	<link href="' . getinfo('plugins_url') . 'file_manager/comuser-profile.css" rel="stylesheet" type="text/css">

    <style type="text/css">@import url(' . $uploader_dir . 'css/comuser-plupload.queue.css);</style>
    <script type="text/javascript" src="' . $uploader_dir . 'js/plupload.full.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/jquery.plupload.queue.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/i18n.js"></script>
   ';

  echo mso_load_jquery('alerts/jquery.alerts.js');
	echo mso_load_jquery('cornerz.js');
	echo '	<link href="' . getinfo('common_url') . 'jquery/alerts/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen">';

	global $MSO;

  $out .= '<div id="load_tips" class="load_tips">' . t('Добавьте файлы в список. Добавьте описание файлов. Нажмите "Загрузить". Максимальный размер файла', '_FILE_') . ' ' . ini_get ('upload_max_filesize') . '.</div>';	
  
	$out .= '<div class="upl_message" id="upl_message"></div>';
	 
	 // загрузчик файла
	$out .= '<div class="upload_file">';

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
					f_session2_id			: document.getElementById("f_session2_id").value,
					f_directory				: \'' . getinfo('upoads_dir') . $curdir . '\'
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
			  
			    var upl_url = \'' . getinfo('uploads_url') . $curdir . '/' . '\';
			    
			    var title = document.getElementById("f_userfile_title").value;
			    var add = \'<div class="uploads_picture"><a class="lightbox" href="\' + upl_url + file.name + \'"><img class="uploads_img" src="\' + upl_url + \'_mso_i/\' + file.name + \'"></a></div>\';
			    
			  
				if(info.response == \'' . FM_GOOD_RESP . '\')
				{
					var files_list = document.getElementById("files_list");
					
					showfiles();
					
					$(".error").fadeOut();
					$(".update").fadeOut();
				} 
				else
				 if(info.response.indexOf(\'' . FM_GOOD_RESP . '\') + 1)
				 {
					//var files_list = document.getElementById("files_list");
					showfiles();
					
					$(".error").fadeOut();
				  info.response = info.response.replace(\'' . FM_ERROR_RESP . '\',"");
					upl_message.innerHTML += \'<div class="update">Файл \' + file.name + \' загрузился, но в ходе обработки возникли некоторые ошибки: </div>\' + info.response;
				 } 
				 else
				 {
				  info.response = info.response.replace(\'' . FM_ERROR_RESP . '\',"");
					upl_message.innerHTML += \'<div class="error">Файл \' + file.name + \' не загрузился из-за ошибок:\' + info.response + \'</div>\';
				 }	
				
				$(".plupload_buttons").fadeIn();
        $(".plupload_upload_status").fadeOut();		
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

	
  <div id="upload_settings" class="upload_settings">
	<p>' . t('Описание загружаемых файлов', '_FILE_') . ': <input type="text" id="f_userfile_title" class="description_file" value=""></p>
  </div></div>';

	
 return $out;
}


?>