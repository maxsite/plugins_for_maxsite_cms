<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
	<script type="text/javascript" src="<?= $editor_config['url'] ?>tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		mode : "exact",
        elements : "wysiwyg",
		language: "ru",
		theme : "advanced",
		plugins : "safari,table,advimage,advlink,iespell,inlinepopups,paste,media,autosave,template",
		theme_advanced_buttons1 : "bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,sub,sup,|,link,unlink,anchor,|,code",
		theme_advanced_buttons2 : "formatselect,forecolor,|,pastetext,removeformat,|,image,code_add,hr,template,|,visualaid,charmap,|,undo,redo",
		theme_advanced_buttons3 : "table,cell_props,col_after,col_before,row_after,row_before,split_cells,merge_cells,delete_table,delete_col,delete_row",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : "1",
		theme_advanced_resize_horizontal : false,
		theme_advanced_blockformats : "div,p,h2,h3,h4,h5,blockquote",
		theme_advanced_more_colors : false,
		gecko_spellcheck : true,
		convert_urls : false,
		accessibility_warnings : false,
		content_css:"<?= getinfo('plugins_url') ?>editor_tinymce/editor-style.css",
<?php 

//http://test.site/application/maxsite/plugins/editor_tinymce/template_list-require-maxsite.php

$path_tpl = getinfo('require-maxsite') 
	. base64_encode
	(
		'plugins/editor_tinymce/template_list-require-maxsite.php'
	);
?>
		template_external_list_url : "<?= $path_tpl ?>",
	});
	</script>

	
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

