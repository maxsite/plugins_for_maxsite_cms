<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

	$uploader_dir = getinfo('plugins_url') . 'file_manager/uploader/';
?>

<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(<?php echo $uploader_dir; ?>css/plupload.queue.css);</style>

<!-- Thirdparty intialization scripts, needed for the Google Gears and BrowserPlus runtimes 
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/gears_init.js"></script>
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> -->

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/plupload.full.min.js"></script>
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/jquery.plupload.queue.min.js"></script>
<script type="text/javascript" src="<?php echo $uploader_dir; ?>js/i18n.js"></script>


<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready
$(function() {
	$("#uploader").pluploadQueue({
		// General settings
		runtimes : 'html5,html4', //+ flash,silverlight,gears,browserplus
		url : '<?php echo getinfo('require-maxsite') . base64_encode('plugins/file_manager/uploader/upload-require-maxsite.php'); ?>',
		max_file_size : '<?php echo ini_get('post_max_size'); ?>',		

		// Specify what files to browse for
		filters : [
			{title : "Allowed files", extensions : "<?php echo $allowed_types; ?>"}
		],

		//headers: {REFERER : 'http://max-latest.xp/admin/file_manager'},
		
		// Flash settings
		//flash_swf_url : '<?php echo $uploader_dir; ?>js/plupload.flash.swf',

		// Silverlight settings
		//silverlight_xap_url : '<?php echo $uploader_dir; ?>js/plupload.silverlight.xap',
		
		// PreInit events, bound before any internal events
		preinit: {		
			Init: function(up, info) {
				if(info.runtime === 'html4') up.settings.filters = false;
			},
			UploadFile: function(up, file) {	
				up.settings.multipart_params = {
					f_userfile_title		: document.getElementById("f_userfile_title").value,				
					f_userfile_resize		: document.getElementById("f_userfile_resize").checked ? '1' : '',							
					f_userfile_resize_size	: document.getElementById("f_userfile_resize_size").value,							
					f_userfile_mini			: document.getElementById("f_userfile_mini").checked ? '1' : '',							
					f_userfile_mini_size	: document.getElementById("f_userfile_mini_size").value,							
					f_mini_type				: document.getElementById("f_mini_type").value,							
					f_userfile_water		: (document.getElementById("f_userfile_water").checked && document.getElementById("f_userfile_water").disabled == false) ? '1' : '',							
					f_water_type			: document.getElementById("f_water_type").value,						
					f_session2_id			: document.getElementById("f_session2_id").value,
					f_directory				: '<?php echo $current_dir; ?>'
				};
			}
		},
		
		init: {
			StateChanged: function(up) {
				// Called when the state of the queue is changed
				if(up.state == 1){
					$('#file_upload').submit();
				}
			},
			
			// Called when a file has finished uploading
			FileUploaded: function(up, file, info) {	
				if(info.response == '<?php echo FM_GOOD_RESP; ?>'){
					// Вывод результата
					document.getElementById("upl_message").value += '<div class="update"><?php echo t('Загрузка выполнена без ошибок. Файл "', 'admin')?>: ' + file.name + '"</div>';
				} else if(info.response.indexOf('<?php echo FM_GOOD_RESP; ?>') + 1){
					// Совместимость с HTML4
					if(info.response.indexOf('</div>') == -1) info.response = '<div class="error">' + info.response + '</div>'; 
					// Вывод результата
					document.getElementById("upl_message").value += '<div class="update"><?php echo t('Файл'); ?>  "' + file.name + '" загрузился, но в ходе обработки возникли некоторые ошибки: </div>' + info.response;			
				} else {
					// Совместимость с HTML4
					if(info.response.indexOf('</div>') == -1) info.response = '<div class="error">' + info.response + '</div>';
					// Вывод результата
					document.getElementById("upl_message").value += '<div class="error"><?php echo t('Файл'); ?>  "' + file.name + '" не загрузился из-за ошибок:</div>' + info.response;
				}					
			},
		}
	});

});
</script>


	<div id="uploader" style="height:272px;">
		<p>You browser doesn't have Silverlight, Gears, BrowserPlus or HTML5 support.</p>
		<input type="hidden" name="lolo" value="123">
	</div>