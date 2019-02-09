<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_dumb/editor_zero.js"></script>'; ?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<p class="editor_button">
	<input type="button" value="<b>" title="<?= t('Полужирный', 'plugins') ?>" onClick="addText('<b>', '</b>') ">
	<input type="button" value="<i>" title="<?= t('Курсив', 'plugins') ?>" onClick="addText('<i>', '</i>') ">
	<input type="button" value="<u>" title="<?= t('Подчеркнутый', 'plugins') ?>" onClick="addText('<u>', '</u>') ">
	<input type="button" value="<s>" title="<?= t('Зачеркнутый', 'plugins') ?>" onClick="addText('<s>', '</s>') ">
	&nbsp; &nbsp;
	<input type="button" value="<br>" title="<?= t('С новой строки', 'plugins') ?>" onClick="addText('<br>\n', '') ">
	&nbsp; &nbsp;
	<input type="button" value="<p>" title="<?= t('Абзац', 'plugins') ?>" onClick="addText('<p>', '</p>') ">
	<input type="button" value="<p.left>" title="<?= t('Абзац с форматированием слева', 'plugins') ?>" onClick="addText('<p class=&quot;text-left&quot;>', '</p>') ">
	<input type="button" value="<p.center>" title="<?= t('Абзац с форматированием по центру', 'plugins') ?>" onClick="addText('<p class=&quot;text-center&quot;>', '</p>') ">
	<input type="button" value="<p.right>" title="<?= t('Абзац с форматированием справа', 'plugins') ?>" onClick="addText('<p class=&quot;text-right&quot;>', '</p>') ">
	&nbsp; &nbsp;
	<input type="button" value="<a>" title="<?= t('Ссылка', 'plugins') ?>" onClick="addText('<a href=&quot;&quot;>', '</a>') ">
	&nbsp; &nbsp;
	<input type="button" value=".lightbox" title="<?= t('Всплывающий просмотр для картинок', 'plugins') ?>" onClick="addText('class=&quot;lightbox&quot;', '') ">
	<input type="button" value=".left" title="<?= t('Класс слева', 'plugins') ?>" onClick="addText('class=&quot;left&quot;', '') ">
	<input type="button" value=".center" title="<?= t('Класс центр', 'plugins') ?>" onClick="addText('сlass=&quot;center&quot;', '') ">
	<input type="button" value=".right" title="<?= t('Класс справа', 'plugins') ?>" onClick="addText('class=&quot;right&quot;', '') ">
	&nbsp; &nbsp;
	<input type="button" value="#about" title="<?= t('Блок about', 'plugins') ?>" onClick="addText('<div class=&quot;about&quot;>', '</div>') ">
	<input type="button" value="#alert" title="<?= t('Блок alert', 'plugins') ?>" onClick="addText('<div class=&quot;alert&quot;>', '</div>') ">
	<input type="button" value="#message" title="<?= t('Блок message', 'plugins') ?>" onClick="addText('<div class=&quot;message&quot;>', '</div>') ">
	<input type="button" value="#error" title="<?= t('Блок error', 'plugins') ?>" onClick="addText('<div class=&quot;error&quot;>', '</div>') ">
	<input type="button" value="#ok" title="<?= t('Блок ok', 'plugins') ?>" onClick="addText('<div class=&quot;ok&quot;>', '</div>') ">
	&nbsp; &nbsp;
	<input type="button" value="<img>" title="<?= t('Картинка', 'plugins') ?>" onClick="addText('<img src=&quot;&quot; alt=&quot;&quot;>', '') ">
	<input type="button" value="<img.left>" title="<?= t('Изображение слева', 'plugins') ?>" onClick="addText('<img class=&quot;left&quot; src=&quot;&quot; alt=&quot;&quot;>', '>') ">
	<input type="button" value="<img.center>" title="<?= t('Изображение по центру', 'plugins') ?>" onClick="addText('<img class=&quot;center&quot; src=&quot;&quot; alt=&quot;&quot;>', '') ">
	<input type="button" value="<img.right>" title="<?= t('Изображение справа', 'plugins') ?>" onClick="addText('<img class=&quot;right&quot; src=&quot;&quot; alt=&quot;&quot;>', '') ">
	&nbsp; &nbsp;
	<input type="button" value="<?= t('Цитата', 'plugins') ?>" title="<?= t('Цитата', 'plugins') ?>" onClick="addText('<blockquote>', '</blockquote>') ">
	<input type="button" value="<?= t('Код', 'plugins') ?>" title="<?= t('Код или преформатированный текст', 'plugins') ?>" onClick="addText('<code>', '</code>') ">
	<input type="button" value="<?= t('Анонс', 'plugins') ?>" title="<?= t('Отрезать текст', 'plugins') ?>" onClick="addText('[cut]\n', '') ">
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

