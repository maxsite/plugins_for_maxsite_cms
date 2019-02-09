<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor"><?= $editor_config['do'] ?><textarea id="f_content" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea><?= $editor_config['posle'] ?><?= $editor_config['do_script'] ?></form>

<script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_ckeditor/ckeditor/ckeditor.js"></script>




			<script type="text/javascript">
				CKEDITOR.replace( 'f_content',{
filebrowserBrowseUrl : '../application/maxsite/plugins/editor_ckeditor/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
filebrowserUploadUrl : '../application/maxsite/plugins/editor_ckeditor/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
filebrowserImageBrowseUrl : '../application/maxsite/plugins/editor_ckeditor/filemanager/dialog.php?type=1&editor=ckeditor&fldr='

});
			
	</script>
		
			

			

			
			




