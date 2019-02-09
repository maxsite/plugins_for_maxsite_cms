<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<script>
</script>


	
<script language="javascript">
    function markdown_to_html(update) {
		var text = $('#f_content').val();
        var converter = new Showdown.converter();
        var preview_html = converter.makeHtml(text);
        $('#preview').html(preview_html);
    }

	$(document).ready(function() 
	{
        $('#f_content').wysiwym(Wysiwym.<?= $markup ?>, {});
<?
if (isset($options['autosave']) ) {
    if ($options['autosave'] == 'Включено') { ?>
        $('form').sisyphus({
            timeout: 60,
        });
<?  }
}
?>
        markdown_to_html();
	});

    $(function() {
                 $("#tabs-widget > ul").tabs();
    });

</script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<div id="tabs-widget" class="tabs-widget-all">
    <ul class="tabs-menu" id="tabs-menu">
        <li><a href="#tabs-widget-fragment-0"><span>Редактирование</span></a></li>
        <li><a href="#tabs-widget-fragment-1" onClick="markdown_to_html();"><span>Предпросмотр</span></a></li>
    </ul>
</div>
<div id="tabs-widget-fragment-0">
    <textarea id="f_content" class="markdown-editor" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;"><?= $editor_config['content'] ?></textarea>
</div>
<div id="tabs-widget-fragment-1">
    <style>
        code {
            border: 0px solid #CCC;
        }
    </style>
    <div id="preview">
    </div>
</div>
<?= $editor_config['posle'] ?>
</form>
<script language="javascript">
</script>
