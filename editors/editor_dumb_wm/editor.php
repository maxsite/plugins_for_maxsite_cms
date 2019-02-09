<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
echo '<script src="'. getinfo('plugins_url') . 'editor_dumb_wm/editor_zero.js"></script>';
echo '<script src="'.getinfo('plugins_url').'editor_dumb_wm/smiles.js"></script>';
echo '<link rel="stylesheet" href="'.getinfo('plugins_url').'editor_dumb_wm/btn.css">';
?>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<button type="button" class="btn btn-inverse" type="button"  onClick="open_close('smiles')">Смайлы</button>
<div id="smiles" style="display:none;">
<?php
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
?>
</div>
<button type="button" class="btn btn-inverse" onClick="open_close('itinfo')">Справка</button>
<div id="itinfo" style="display:none;">
<p>Можно поместить полезные кусочки кода</p>
</div>
<button type="button" class="btn btn-inverse" onClick="open_close('itclass')">Справка по css</button>
<div id="itclass" style="display:none;">
<p>Можно поместить полезные кусочки кода</p>
</div>
<div class="btn-group">
<button class="btn" type="button" onClick="addText('[cut]\n', '') " title="Отрезать текст анонса">[cut]</button>
<button class="btn" type="button" onClick="addText('<p>','</p>') " title="Абзац <p> </p>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/p.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<br>','') " title="перенос строки <br>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/br.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<strong>','</strong>') " title="Жирный важный <strong></strong>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/strong.png" alt=""></button>
<button class="btn" type="button" onClick="addText('<b>','</b>') " title="Жирный обычный <b></b>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/b.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<em>','</em>')" title="Наклонный шрифт <em></em>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/em.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<u>','</u>')" title="Подчеркнутый <u></u>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/underline.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<del>','</del>')" title="Зачеркнутый шрифт <del></del>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/del.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<small>','</small>')" title="Маленький шрифт <small></small>"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/small.png" alt=""></button>
<button class="btn" type="button" onClick="addText('<div class=&quot;center&quot;>','</div>') " title="Центрировать текст или елемент center"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/center.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<h2>','</h2>')" title="Заголовок H2"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/h2.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<h3>','</h3>')" title="Заголовок H3"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/h3.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<h4>','</h4>')" title="Заголовок H4"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/h4.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<h5>','</h5>')" title="Заголовок H5"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/h5.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<h6>','</h6>')" title="Заголовок H6"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/h6.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<blockquote>','</blockquote>') " title="Цитата"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/blockquote.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<pre>','</pre>') " title="Код или преформатированный текст"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/code.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<div class=&quot;info-blok&quot;>','</div>') " title="полезная информация"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/info.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<div class=&quot;idea&quot;>','</div>') " title="Идея"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/idea.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<div class=&quot;blok-system&quot;>','</div>') " title="ошибка blok-system"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/error.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<div class=&quot;blok-ok&quot;>','</div>') " title="ошибка blok-ok"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/ok.png" alt="!"></button>
</div>
<p class="btn-group">
<button class="btn" type="button" onClick="addText('<img itemprop=&quot;image&quot; src=&quot;http://адрес&quot; alt=&quot; &quot; class=&quot;img-content&quot;>','')" title="картинка"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/images.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<a href=&quot;http://адрес&quot;>','имя ссылки</a>')" title="Ссылка обычная"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/link.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<a href=&quot;http://адрес&quot;>','имя ссылки</a>')" title="Ссылка с rel=&quot;nofollow&quot; не индексируется поисковиками"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/link_nofollow.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('<a href=&quot;http://адрес&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>','имя ссылки</a>')" title="В новом окне и не индексируется"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/link_target.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('[youtube=ссылка]','')" title="вставить video youtube"><img src="<?=getinfo('plugins_url') ?>editor_dumb_wm/teg/youtube.png" alt="!"></button>
<button class="btn" type="button" onClick="addText('[audio=http://site.com/my.mp3]','')" title="вставить музыки">mp3</button>
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

