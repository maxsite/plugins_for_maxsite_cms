<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'buttocode/editor_zero.js"></script>'; ?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<p class="editor_button b2c-buttons">
	<input type="button" value="B" title="<?= t('Полужирный', 'plugins') ?>" onClick="addText('<b>', '</b>') ">
	<input type="button" value="I" title="<?= t('Курсив', 'plugins') ?>" onClick="addText('<i>', '</i>') ">
	<input type="button" value="U" title="<?= t('Подчеркнутый', 'plugins') ?>" onClick="addText('<u>', '</u>') ">
	<input type="button" value="S" title="<?= t('Зачеркнутый', 'plugins') ?>" onClick="addText('<s>', '</s>') "> &nbsp;
	<input type="button" value="A" title="<?= t('Ссылка', 'plugins') ?>" onClick="addText('<a href=&quot;&quot;>', '</a>') ">
	<input type="button" value="IMG" title="<?= t('Картинка', 'plugins') ?>" onClick="addText('<img src=&quot;&quot; alt=&quot;&quot;>', '') ">
	<input type="button" value="<?= t('Цитата', 'plugins') ?>" title="<?= t('Цитата', 'plugins') ?>" onClick="addText('<blockquote>', '</blockquote>') ">
	<input type="button" value="<?= t('Код', 'plugins') ?>" title="<?= t('Код или преформатированный текст', 'plugins') ?>" onClick="addText('<code>', '</code>') ">
	<input type="button" value="cut" title="<?= t('Отрезать текст', 'plugins') ?>" onClick="addText('[cut]\n', '') ">
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

