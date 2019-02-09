<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<!-- TinyMCE -->
<script language="JavaScript" src="<?= $editor_config['url'] ?>tiny_mce.js" type="text/javascript"></script>
<script type="text/javascript">
	tinyMCE.init(
	{
		// General options
		mode : "exact",
		elements : "wysiwyg",
		theme : "advanced",
		skin : "o2k7",
		language : "ru",
		//disk_cache : true,
		<?php
		$mce_plugins='safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,more';/*contextmenu,*/
		$mce_plugins=mso_hook('mce_plugins',$mce_plugins);
		?>
		plugins : "<?php echo $mce_plugins ?>",

		<?php
		$mce_buttons='bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect';
		$mce_buttons=mso_hook('mce_buttons',$mce_buttons);
		if($mce_buttons):
		?>
		theme_advanced_buttons1 : "<?php echo $mce_buttons ?>",
		<?php
		endif;
		$mce_buttons_2='cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,code,|,insertdate,inserttime,preview,|,forecolor,backcolor';
		$mce_buttons_2=mso_hook('mce_buttons_2',$mce_buttons_2);
		if($mce_buttons_2):
		?>
		theme_advanced_buttons2 : "<?php echo $mce_buttons_2 ?>",
		<?php
		endif;
		$mce_buttons_3='tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,advhr,|,print,|,fullscreen';
		$mce_buttons_3=mso_hook('mce_buttons_3',$mce_buttons_3);
		if($mce_buttons_3):
		?>
		theme_advanced_buttons3 : "<?php echo $mce_buttons_3 ?>",
		<?php
		endif;
		$mce_buttons_4='';
		$mce_buttons_4=mso_hook('mce_buttons_4',$mce_buttons_4);
		if($mce_buttons_4): ?>
		theme_advanced_buttons4 : "<?php echo $mce_buttons_4 ?>",
		<?php endif; ?>
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		dialog_type : "modal",
		relative_urls : false,
		accessibility_focus : true,
		paste_remove_styles : true,
		paste_remove_spans : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?php echo $editor_config['url'] ?>css/content.css",
	});
</script>
<!-- /TinyMCE -->

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

