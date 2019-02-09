<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

	<script type="text/javascript" src="<?= $editor_config['url'] ?>fckeditor/fckeditor.js"></script>
  <script type="text/javascript">
  window.onload = function()
  {
  	var cFCKeditor = new FCKeditor('f_content') ;
  	cFCKeditor.BasePath	= '<?=$editor_config['url'];?>fckeditor/';
  	cFCKeditor.Config['CustomConfigurationsPath'] = '<?=$editor_config['url'];?>fckeditor/fckconfig_maxsite.js';
  	cFCKeditor.Height = <?= $editor_config['height'] ?>;
  	cFCKeditor.ToolbarSet = 'MaxSiteDefault';
  	cFCKeditor.ReplaceTextarea();
  }
  </script>
<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

