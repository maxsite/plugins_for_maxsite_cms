<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

# http://forum.max-3000.com/viewtopic.php?f=6&t=930
# Автор: Delete (http://www.finiks.ru/)


?>
<script src="<?=getinfo('plugins_url')?>editor_wysibb/jquery.wysibb.min.js"></script>
<link rel="stylesheet" href="<?=getinfo('plugins_url')?>editor_wysibb/theme/default/wbbtheme.css" type="text/css" />
<script>
$(document).ready(function() {
var wbbOpt = {
imgupload : false
}
$("#f_content").wysibb(wbbOpt);
});
</script>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>

<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
