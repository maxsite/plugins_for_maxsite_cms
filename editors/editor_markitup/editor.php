<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/html.js"></script>'; ?>
<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.js"></script>'; ?>
<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.pack.js"></script>'; ?>

<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'editor_markitup/style.css" />'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'editor_markitup/html.style.css" />'; ?>


<script language="javascript">
$(document).ready(function()	{
   $('#f_content').markItUp(myHtmlSettings);
});
</script>


<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

