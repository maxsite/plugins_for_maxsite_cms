<?php
//echo getinfo('uploads_dir');
//echo '<br>'.getinfo('uploads_url');
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

?>

<style type="text/css">
	#close, #open, #dock, #undock {
		width: 100px;
		position:relative;
		display: -moz-inline-stack;
		display: inline-block;
		vertical-align: top;
		zoom: 1;
		*display: inline;
		margin:0 3px 3px 0;
		padding:1px 0;
		text-align:center;
		border:1px solid #ccc;
		background-color:#eee;
		margin:1em .5em;
		padding:.3em .7em;
		border-radius:5px; 
		-moz-border-radius:5px; 
		-webkit-border-radius:5px;
		cursor:pointer;
	}
</style>

<link rel="stylesheet" href="<?= $editor_config['url'] ?>elrte/css/elrte.min.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="<?= $editor_config['url'] ?>elfinder/css/smoothness/jquery-ui-1.8.13.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
<link rel="stylesheet" href="<?= $editor_config['url'] ?>elfinder/css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8">

<script src="<?= $editor_config['url'] ?>elrte/js/jquery-ui-1.8.13.custom.min.js" type="text/javascript" charset="utf-8"></script>

<script src="<?= $editor_config['url'] ?>elrte/js/elrte.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= $editor_config['url'] ?>elrte/js/i18n/elrte.ru.js" type="text/javascript" charset="utf-8"></script>

<script src="<?= $editor_config['url'] ?>elfinder/js/elfinder.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= $editor_config['url'] ?>elfinder/js/i18n/elfinder.ru.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
	$().ready(function() {
		elRTE.prototype.options.panels.myToolbar = ['bold', 'italic', 'underline', 'strikethrough','justifyleft','justifyright', 'justifycenter', 'justifyfull', 'docstructure','paste','removeformat','link','unlink', 'elfinder', 'image', 'fullscreen'];
		elRTE.prototype.options.toolbars.myToolbar = ['myToolbar'];

		var opts = {
			cssClass : 'el-rte',
			lang     : 'ru',
			height   : 450,
			fmAllow  : true,
			fmOpen   : function(callback) {
				$('<div id="myelfinder" />').elfinder({
					//url : '<?= $editor_config['url'] ?>elfinder/connectors/php/connector-ajax.php',
					//url  : '<?= getinfo('require-maxsite') . base64_encode('plugins/elrte/connectors/php/connector-require-maxsite.php'); ?>',
					//url  : '<?= getinfo('ajax') . base64_encode('plugins/elrte/connectors/php/connector-ajax.php'); ?>/',
					url  : 'http://dshi6.kharkov.ua/application/maxsite/plugins/elrte/elfinder/connectors/php/connector-ajax.php',
					lang : 'ru',
					dialog : { width : 900, modal : true, title : 'Files' }, // открываем в диалоговом окне
					closeOnEditorCallback : true, // закрываем после выбора файла
					editorCallback : callback // передаем callback файловому менеджеру
				})
			},
			toolbar  : 'maxi', //'myToolbar', //'complete',
			cssfiles : ['<?= $editor_config['url'] ?>elrte/css/elrte-inner.css']
		}

		$('#editor').elrte(opts);
	})
</script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="editor" name="f_content" style="width: 100%">
<?= $editor_config['content'] ?>
</textarea>
<?= $editor_config['posle'] ?>
</form>
