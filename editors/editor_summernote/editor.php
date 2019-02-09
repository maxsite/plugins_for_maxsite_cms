<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<link rel="stylesheet" href="<?= getinfo('plugins_url') ?>editor_summernote/dist/bootstrap/3.3.5/css/bootstrap.css" type="text/css">

<script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_summernote/dist/ajax/libs/jquery/3.2.1/jquery.js"></script> 
 <script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_summernote/dist/bootstrap/3.3.5/js/bootstrap.js"></script> 
  
<link rel="stylesheet" href="<?= getinfo('plugins_url') ?>editor_summernote/dist/summernote.css" type="text/css">
 <script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_summernote/dist/summernote.min.js"></script>


<script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_summernote/dist/lang/summernote-ru-RU.js"></script>

<script type="text/javascript" src="<?= getinfo('plugins_url') ?>editor_summernote/dist/plugin/summernote-ext-elfinder/summernote-ext-elfinder.js"></script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor"><?= $editor_config['do'] ?><textarea id="summernote" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea><?= $editor_config['posle'] ?><?= $editor_config['do_script'] ?></form>

<script>

  
$(function() {
  $('#summernote').summernote({
    lang: 'ru-RU',
    height: 300,
  toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough', 'superscript', 'subscript']],
    ['fontsize', ['fontsize']],
    ['color', ['color']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['insert', ['picture', 'link', 'video', 'table']],
    ['misc', ['undo', 'redo', 'codeview']],
    ['height', ['height']],
    //['elfinder', ['elfinder']]

]

  })
});

</script>



