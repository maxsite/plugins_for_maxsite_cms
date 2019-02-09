<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

?>

<script type="text/javascript" src="<?= $editor_config['url'] ?>fckeditor/fckeditor.js"></script>
<script type="text/javascript">
window.onload = function()
{
	var oFCKeditor = new FCKeditor( 'fcktextarea' );
	oFCKeditor.Config['CustomConfigurationsPath'] = "<?= $editor_config['url'] ?>fckeditor/maxsite_config.js";
	oFCKeditor.BasePath = "<?= $editor_config['url'] ?>fckeditor/";
	oFCKeditor.Height = <?= $editor_config['height'] ?>;
	oFCKeditor.ReplaceTextarea();
}
</script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="fcktextarea" name="f_content" style="width: 100%">
<?= $editor_config['content'] ?>
</textarea>
<?= $editor_config['posle'] ?>
</form>
