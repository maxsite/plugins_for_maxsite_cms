<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>
	<script type="text/javascript" src="<?= $editor_config['url'] ?>tinymce/tinymce.min.js"></script>
	
<script type="text/javascript">
tinymce.init({
    selector: "textarea#elm1",    
		language: "ru",    
		theme: "modern",    
		height: 350,
    plugins: [        
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager"  
		],
   
	image_advtab: true,
    relative_urls: false,
   
	toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | pagebreak | bullist numlist outdent indent | link | image",
    toolbar2: "print preview media | forecolor backcolor emoticons",
    
	content_css:"<?= getinfo('plugins_url') ?>editor_tinymce/editor-style.css",
	<?php $path_tpl = getinfo('require-maxsite') 	. base64_encode	(		'plugins/editor_tinymce/template_list-require-maxsite.php'	);?>		template_external_list_url : "<?= $path_tpl ?>",
   
    
    style_formats: [       
		{title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}    
	]	
	});
	
	</script>	
	<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data"><?= $editor_config['do'] ?><textarea id="elm1" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea><?= $editor_config['posle'] ?></form>
