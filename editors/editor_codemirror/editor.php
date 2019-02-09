<? if (!defined('BASEPATH')) exit(':-)'); ?>

<form <?= $editor_config['action'] ?> method="post">
<?= $editor_config['do'] ?>
<div style="border: 1px solid #aaa;">
<textarea id="f_content" name="f_content" cols="75" rows="25" style="width: 100%; height: <?= $editor_config['height'] ?>px;"><?= $editor_config['content'] ?></textarea>
</div>
<?= $editor_config['posle'] ?>
</form>

<?
echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'textarea/codemirror/codemirror.js"></script>';
$codemirror = getinfo('plugins_url') . 'textarea/codemirror/';
?>

<script type="text/javascript">
var editor = CodeMirror.fromTextArea('f_content', {
	parserfile : ['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js'],
	stylesheet : ['<?= $codemirror ?>xmlcolors.css', '<?= $codemirror ?>jscolors.css', '<?= $codemirror ?>csscolors.css'],
	path : '<?= $codemirror ?>',
	tabMode : 'shift'
});
</script>