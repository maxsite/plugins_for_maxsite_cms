<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
echo '<script src="'.getinfo('plugins_url').'editor_dumb/editor_zero.js"></script>';
echo '<script src="'.getinfo('plugins_url').'editor_dumb/smiles.js"></script>';
?>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<?
  $image_url=getinfo('uploads_url').'smiles/';
  $CI = & get_instance();
  $CI->load->helper('smiley_helper');
  $smileys=_get_smiley_array();
   echo '<p style="padding-bottom:5px;">';
  $used = array();
  foreach ($smileys as $key => $val)
  {
    if (isset($used[$smileys[$key][0]]))
    {
      continue;
    }
    echo "<a href=\"javascript:void(0);\" onclick=\"addSmile('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" title=\"".$smileys[$key][3]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\"></a> ";
    $used[$smileys[$key][0]] = TRUE;
  }
echo '</p>';
?>
<p class="editor_button">
<!-- ВНИМАНИЕ: добавляем кнопки по следующему принципу:
<input type="button" value="название кнопки" title="<?= t('всплывающая подсказка') ?>" onClick="addText('текст до курсора', 'текст после курсора') ">
Вместо знака " ставим &nbsp; перенос строки \n -->
<input type="button" value="strong" title="<?= t('жирный важный') ?>" onClick="addText('[b]', '[/b]') ">
<input type="button" value="Абзац" title="<?= t('Абзац') ?>" onClick="addText('[p]', '[/p]') ">
<input type="button" value="b" title="<?= t('жирный обычный') ?>" onClick="addText('[bold]', '[/bold]') ">
<input type="button" value="Курсив" title="<?= t('курсив') ?>" onClick="addText('[i]', '[/i]') ">
<input type="button" value="s" title="<?= t('зачеркнутый') ?>" onClick="addText('[s]', '[/s]') ">
<input type="button" value="u" title="<?= t('подчеркнутый') ?>" onClick="addText('[u]', '[/u]') ">
<input type="button" value="small" title="<?= t('Мелкий') ?>" onClick="addText('[small]', '[/small]')">
<input type="button" value="<?= t('цитата') ?>" title="<?= t('цитата') ?>" onClick="addText('[quote]', '[/quote]') ">
<input type="button" value="Ссылка" title="<?= t('ссылка, формат вывода - [url=http://ссылка/]текст[/url]') ?>" onClick="addText('[url=http://]имя ссылки', '[/url]') ">
<input type="button" value="Cut" title="<?= t('Обрезать анонс текста') ?>" onClick="addText('[cut]\n', '')">
<br>Фото
<input type="button" value="Фото" title="<?= t('фото, формат вывода - [img онисание картинки]http://адрес[/img]') ?>" onClick="addText('[img онисание картинки]http://адрес', '[/img]') ">
<input type="button" value="Центр" title="<?= t('фото, центр ') ?>" onClick="addText('[img(center) онисание картинки]http://адрес', '[/img]') ">
<br>Заголовки
<input type="button" value="h2" title="<?= t('курсив') ?>" onClick="addText('[h2]', '[/h2]') ">
<input type="button" value="h3" title="<?= t('курсив') ?>" onClick="addText('[h3]', '[/h3]') ">
<input type="button" value="h4" title="<?= t('курсив') ?>" onClick="addText('[h4]', '[/h4]') ">
<input type="button" value="h5" title="<?= t('курсив') ?>" onClick="addText('[h5]', '[/h5]') ">
<input type="button" value="h6" title="<?= t('курсив') ?>" onClick="addText('[h6]', '[/h6]') ">

<br>Виджеты
<input type="button" value="youtube" title="<?= t('вставить video youtube') ?>" onClick="addText('[youtube=ссылка]','')">
<input type="button" value="faq" title="<?= t('организация FAQ на странице', 'plugins') ?>" onClick="addText('[faqs]\n[faq=', 'вопрос]\n ответ\n[/faq]\n[faq=вопрос2]\n ответ2\n[/faq]\n[/faqs]') ">
<input type="button" value="Подсчет" title="<?= t('подсчет количества переходов по ссылке, обрамите нужную ссылку в [dc]...[/dc]') ?>" onClick="addText('[dc]', '[/dc]') ">
<input type="button" value="code" title="<?= t('code') ?>" onClick="addText('[code]', '[/code]') ">
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
