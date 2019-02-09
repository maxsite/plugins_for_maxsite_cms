<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

echo '<script src="'. getinfo('plugins_url') . 'editor_minima/editor_zero.js"></script>';
echo '<link rel="stylesheet" href="'. getinfo('plugins_url') .'editor_minima/style.css">';

?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?php
global $MSO;
if (!in_array('bbcode', $MSO->active_plugins)) {
	echo '<span class="editor_warning">Для нормальной работы редактора необходимо включить плагин "bbcode"!</span><p>';
}
?>
<script>

</script> 
<span id="minima_button">Заголовок:<sup style="color: red">*</sup></span>
<?= $editor_config['do'] ?>
<span>Текст:<sup style="color: red">*</sup></span>

<div class="editor_button">
	<a title="Жирный" onclick="addText('[b]', '[/b]')" href="
	#b" tabindex="-1"><img width="16" height="16" alt="Ж" src="<?= getinfo('plugins_url') ?>editor_minima/img/bold.png"></a>
	<a title="Курсив" onclick="addText('[i]', '[/i]')" href="
	#i" tabindex="-1"><img width="16" height="16" alt="К" src="<?= getinfo('plugins_url') ?>editor_minima/img/italic.png"></a>
	<a title="Подчеркнутый" onclick="addText('[u]', '[/u]')" href="
	#u" tabindex="-1"><img width="16" height="16" alt="П" src="<?= getinfo('plugins_url') ?>editor_minima/img/underline.png"></a>
	<a title="Зачеркнутый" onclick="addText('[s]', '[/s]')" href="
	#s" tabindex="-1"><img width="16" height="16" alt="З" src="<?= getinfo('plugins_url') ?>editor_minima/img/strikethrough.png"></a>
	&nbsp;
	<a title="Ссылка" onclick="var src = prompt('Введите URL ссылки', 'http://'); if (src) {addText('[url='+src+']','[/url]')}" href="
	#s" tabindex="-1"><img width="16" height="16" alt="С" src="<?= getinfo('plugins_url') ?>editor_minima/img/link.png"></a>
	<a title="Изображение (left)" onclick="var src = prompt('Введите URL ссылки', 'http://'); if (src) {addText('[imgleft]'+src, '[/imgleft]')}" href="
	#s" tabindex="-1"><img width="16" height="16" alt="И" src="<?= getinfo('plugins_url') ?>editor_minima/img/image.png"></a>
	<a title="Цитата" onclick="addText('[quote]', '[/quote]')" href="
	#quote" tabindex="-1"><img width="16" height="16" alt="Ц" src="<?= getinfo('plugins_url') ?>editor_minima/img/quote.png"></a>
	<a title="Код" onclick="addText('[code]', '[/code]')" href="
	#code" tabindex="-1"><img width="16" height="16" alt="К" src="<?= getinfo('plugins_url') ?>editor_minima/img/code.png"></a>
	&nbsp;
	<a title="Кат" onclick="addText('[cut]\n', '')" href="
	#cut" tabindex="-1"><img width="16" height="16" alt="К" src="<?= getinfo('plugins_url') ?>editor_minima/img/cut.png"></a>
	&nbsp;
	<?php
	if (in_array('audioplayer', $MSO->active_plugins)) {
		echo '<a title="Аудио" onclick="var src = prompt(\'Введите адрес трека', 'http://\'); if (src) {addText(\'[audio=\'+src, \']\')}" href="#audio" tabindex="-1"><img width="16" height="16" alt="К" src="' . getinfo('plugins_url') . 'editor_minima/img/audio.png"></a>';
	}
	?>
	<?php
	if (in_array('spoiler', $MSO->active_plugins)) {
		echo '<a title="Спойлер" onclick="addText(\'[spoiler]\', \'[/spoiler]\')" href="#spoiler" tabindex="-1"><img width="16" height="16" alt="К" src="' . getinfo('plugins_url') . 'editor_minima/img/spoiler.png"></a>';
	}
	?>
	<?php
	if (in_array('auth_content', $MSO->active_plugins)) {
		echo '<a title="Auth" onclick="addText(\'[auth]\', \'[/auth]\')" href="
	#auth" tabindex="-1"><img width="16" height="16" alt="К" src="' . getinfo('plugins_url') . 'editor_minima/img/auth.png"></a>';
	}
	?>
	<?php
	if (in_array('down_count', $MSO->active_plugins)) {
		echo '<a title="Подсчет переходов по ссылке" onclick="addText(\'[dc]\', \'[/dc]\')" href="
	#dc" tabindex="-1"><img width="16" height="16" alt="К" src="' . ('plugins_url') . 'editor_minima/img/dc.png"></a>';
	}
	?>
	&nbsp;
	<select name="h" id="myH" onChange="addSelect()">
			<option value="" class="title">Заголовки:</option>
			<option value="h4">Заголовок</option>
			<option value="h5">Подзаголовок</option>
			<option value="h6">Подподзаголовок</option>
	</select>
	<select name="select" id="mySelect" onChange="addSelect()">
		<option value="" class="title">Списки:</option>
		<option value="ul">Маркированный</option>
		<option value="ol">Нумерованный</option>
		<option value="*">Элемент списка</option>
	</select>
	<select name="table" id="myTable" onChange="addSelect()">
		<option value="" class="title">Таблица:</option>
		<option value="table">Вставить таблицу</option>
		<option value="tr">Вставить строку</option>
		<option value="td">Вставить ячейку</option>
	</select>
	
	<a title="Теги для использования" onclick="$(this).parents('.new_or_edit').find('.bb_code_help').toggleClass('hidden');return false;" style="float: right; cursor: pointer;"><img width="16" height="16" alt="К" src="<?= getinfo('plugins_url') ?>editor_minima/img/tags.png"></a>
</div>
<div class="bb_code_help hidden"><b>Теги для использования:</b><hr>
<table class="editor_minima_table">
<tr>
	<td width="50%">
	<b>[h1][/h1]</b><br>
	...<br>
	<b>[h6][/h6]</b><br>
	<span class="bb_code_description">Заголовки разного уровня</span><br>
	<b>[sup][/sup]</b><br>
	<b>[sub][/sub]</b><br>
	<span class="bb_code_description">Текст, заключенный в тег [sup] отображается в виде верхнего индекса, [sub] - в виде нижнего</span><br>
	<b>[hr]</b><br>
	<b>[line]</b><br>
	<span class="bb_code_description">Тег для вставки горизонтальной линии</span><br>
</td>
	<td>
	<b>[cut Читать далее]</b><br>
	<span class="bb_code_description">Таким образом можно менять текст ссылки на полную запись.</span><br>
	<b>[abbr title=""][/abbr]</b><br>
	<span class="bb_code_description">Таким тегом выделяется аббревиатура, в атрибуте title указывайте её расшифровку</span><br>
	<b>[color=][/color]</b><br>
	<span class="bb_code_description">Этот тег позволяет задать цвет для текста</span><br>
	<b>[pre][/pre]</b><br>
	<span class="bb_code_description">Преформатированный текст (код)</span><br>
	</td>
</tr>
</table>

</div>

<?
if(isset($_POST['m_submit']) & !empty($_POST['f_content']))
    {
	$output_content = $_POST['f_content'];

	$output = trim($output_content);
	$output = str_replace(chr(10), "<br>", $output);
	$output = str_replace(chr(13), "", $output);
				
	$output = mso_hook('content', $output);
	$output = mso_hook('content_auto_tag', $output);
	$output = mso_hook('content_balance_tags', $output);
	$output = mso_hook('content_out', $output);
	$output = mso_hook('content_content', $output);
	
	echo <<<EOF
	
	<textarea id="f_content" name="f_content" rows="25" cols="82" style="height: {$editor_config['height']} px;" >{$output_content} </textarea>
	<div class="preview_minima">
	{$output}	</div>
EOF;
	
} 
else {
?>
<textarea autofocus id="f_content" name="f_content" rows="25" cols="82" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?
}
?>
<span style="color: red">Поле отмеченное * обязательно для заполнения</span>
<br>

<script type="text/javascript">
	window.onload = function(){
	var preview = document.getElementsByClassName('autosave-editor');
	var newItem = document.createElement("span");
	newItem.innerHTML = '<button type="submit" name="m_submit" class="i preview">Предпросмотр</button>';
	preview[0].appendChild(newItem);
    }
</script>

<?= $editor_config['posle'] ?>
</form>




