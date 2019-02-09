<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<script type="text/javascript">
<?php require('bb.js.php') ?>
</script>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'dialog/editors/markitup/jquery.markitup.js"></script>'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'dialog/editors/markitup/style.css">'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'dialog/editors/markitup/bb.style.css">'; ?>

<?php
	$auto_id = mso_segment(3); // номер страницы по сегменту url
	// проверим, чтобы это было число
	if (!is_numeric($auto_id)) $auto_id = 0; // ошибочный id
?>
	
<script language="javascript">
	autosaveurl = '<?= getinfo('ajax') . base64_encode('dialog/editors/markitup/autosave-post-ajax.php') ?>';
	autosaveid = '<?= $auto_id ?>';

	$(document).ready(function() 
	{
		$('#f_content').markItUp(myBbcodeSettings);
	});
</script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
