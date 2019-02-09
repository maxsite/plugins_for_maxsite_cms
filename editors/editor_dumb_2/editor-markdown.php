<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

# http://forum.max-3000.com/viewtopic.php?f=6&t=930
# Автор: Delete (http://www.finiks.ru/)

echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_dumb/editor_zero.js"></script>'; 

?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<p class="editor_button">
	<!-- ВНИМАНИЕ: добавляем кнопки по следующему принципу:
	<input type="button" value="название кнопки" title="<?= t('всплывающая подсказка') ?>" onClick="addText('текст до курсора', 'текст после курсора') "> 
	Вместо знака " ставим &nbsp; перенос строки \n -->
	<input type="button" value="b" title="<?= t('b') ?>" onClick="addText('**', '**') "> 
	<input type="button" value="i" title="<?= t('i') ?>" onClick="addText('*', '*') "> 
	<input type="button" value="h1" title="<?= t('h1') ?>" onClick="addText('#', '') "> 
	<input type="button" value="h2" title="<?= t('h2') ?>" onClick="addText('##', '') "> 
	<input type="button" value="h3" title="<?= t('h3') ?>" onClick="addText('###', '') "> 
	<input type="button" value="h4" title="<?= t('h4') ?>" onClick="addText('####', '') "> 
	<input type="button" value="code" title="<?= t('code') ?>" onClick="addText('    ', '') "> 
	<input type="button" value="a" title="<?= t('a') ?>" onClick="addText('[', '](http://)') "> 
	<input type="button" value="audio" title="<?= t('audio') ?>" onClick="addText('[audio=', ']') "> 
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
