<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= getinfo('plugins_url') ?>editor_trumbowyg/ui/trumbowyg.css" type="text/css">
<script src="<?= getinfo('plugins_url') ?>editor_trumbowyg/trumbowyg.js"></script>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>

<textarea id="editor" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
<script>
$("#editor").trumbowyg();
</script>
