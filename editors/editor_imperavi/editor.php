<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<link rel="stylesheet" href="<?= getinfo('plugins_url') ?>editor_imperavi/redactor/redactor.css" type="text/css" />
<script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_imperavi/redactor/redactor.min.js"></script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="f_content" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;"><?= $editor_config['content'] ?></textarea>

<?= $editor_config['posle'] ?>
			<script type="text/javascript">
<?= $options['init'] ?>
			</script>
</form>



