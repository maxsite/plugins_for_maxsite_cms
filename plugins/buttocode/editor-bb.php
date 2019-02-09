<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

# http://forum.max-3000.com/viewtopic.php?f=6&t=930
# Автор: Delete (http://www.finiks.ru/)

echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'buttocode/editor_zero.js"></script>';

include('palette.php');
?>
<script language="JavaScript">


   function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

  function showPalette() {
  	var posY = findPosY(document.getElementById("b2ccolor")) + 10;
  	var posX = findPosX(document.getElementById("b2ccolor")) + 10;
  	document.getElementById("b2cpaldiv").style.top =  posY+"px";
  	document.getElementById("b2cpaldiv").style.left =  posX+"px";
   	document.getElementById("b2cpaldiv").style.visibility = "visible";
   }
   function hidePalette() {   	document.getElementById("b2cpaldiv").style.visibility = "hidden";   }

</script>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<p class="editor_button b2c-buttons">
	<!--Шрифт и картинки | Блоки | Выравнивание / CSS | Разметка | LightBox / ColorBox | Палитра      -->
	<!-- ВНИМАНИЕ: добавляем кнопки по следующему принципу:
	<input type="button" value="название кнопки" title="<?= t('всплывающая подсказка', 'plugins') ?>" onClick="addText('текст до курсора', 'текст после курсора') ">
	Вместо знака " ставим &nbsp; перенос строки \n -->
	<div class="b2c-group"> Стили
<input type="button" value="b" title="<?= t('полужирный', 'plugins') ?>" onClick="addText('[b]', '[/b]') ">
<input type="button" value="i" title="<?= t('курсив', 'plugins') ?>" onClick="addText('[i]', '[/i]') "/>
	<input type="button" value="u" title="<?= t('подчеркнутый', 'plugins') ?>" onClick="addText('[u]', '[/u]') "/>
	<input type="button" value="s" title="<?= t('зачеркнутый', 'plugins') ?>" onClick="addText('[s]', '[/s]') "/>
	&nbsp;
		<input type="button" id="b2ccolor" value="color" title="<?= t('SPAN color', 'plugins') ?>" onClick="showPalette();">
	<input type="button" value="size" title="<?= t('SPAN font-size', 'plugins') ?>" onClick="addText('[size=]', '[/size]') ">
	</div>
	  <div class="b2c-group">
	<input type="button" value="a" title="<?= t('ссылка, формат вывода - [url=http://ссылка/]текст[/url]', 'plugins') ?>" onClick="addText('[url=http://]', '[/url]') "/>
		<input type="button" value="dc" title="<?= t('подсчет количества переходов по ссылке, обрамите нужную ссылку в [dc]...[/dc]', 'plugins') ?>" onClick="addText('[dc]', '[/dc]') ">
		&nbsp;
		<input type="button" value="br" title="<?= t('<br clear=all>', 'plugins') ?>" onClick="addText('[br]', '') "> </div>
	 <div class="b2c-group">  Картинки
	<input type="button" value="img=" title="<?= t('картинка  [img=SIZExSIZE Название]адрес[/img]', 'plugins') ?>" onClick="addText('[img=]', '[/img]') "/>
	<input type="button" value="imgL" title="<?= t('картинка выравнивание по левому краю, формат вывода - [imgleft]адрес[/imgleft]', 'plugins') ?>" onClick="addText('[imgleft]', '[/imgleft]') "/>
	<input type="button" value="imgR" title="<?= t('картинка выравнивание по правому краю, формат вывода - [imgright]адрес[/imgright]', 'plugins') ?>" onClick="addText('[imgright]', '[/imgright]') "/>
	</div>
		   <div class="b2c-group">Заголовки
	<input type="button" value="h1" title="<?= t('H1, [h1]...[/h1]', 'plugins') ?>" onClick="addText('[h1]', '[/h1]') ">
	<input type="button" value="h2" title="<?= t('H2, [h2]...[/h2]', 'plugins') ?>" onClick="addText('[h2]', '[/h2]') ">
	<input type="button" value="h3" title="<?= t('H3, [h3]...[/h3]', 'plugins') ?>" onClick="addText('[h3]', '[/h3]') ">
	<input type="button" value="h4" title="<?= t('H4, [h4]...[/h4]', 'plugins') ?>" onClick="addText('[h4]', '[/h4]') ">
	<input type="button" value="h5" title="<?= t('H5, [h5]...[/h5]', 'plugins') ?>" onClick="addText('[h5]', '[/h5]') ">
	<input type="button" value="h6" title="<?= t('H6, [h6]...[/h6]', 'plugins') ?>" onClick="addText('[h6]', '[/h6]') ">
  </div>
		 <div class="b2c-group">Списки
	<input type="button" value="ul" title="<?= t('НЕнумерованный список, [ul][/ul]', 'plugins') ?>" onClick="addText('[ul]\n', '\n[/ul]') ">
	<input type="button" value="ol" title="<?= t('Нумерованный список, [ol]...[/ol]', 'plugins') ?>" onClick="addText('[ol]\n', '\n[/ol]') ">
	<input type="button" value="li" title="<?= t('элемент списка', 'plugins') ?>" onClick="addText('[*]', '[/*]\n') ">
	</div>
	<div class="b2c-group">Таблицы
	<input type="button" value="table" title="<?= t('вставить таблицу', 'plugins') ?>" onClick="addText('[table]\n', '\n[/table]') ">
	<input type="button" value="tr" title="<?= t('вставить строку таблицы', 'plugins') ?>" onClick="addText('[tr]\n', '\n[/tr]') ">
	<input type="button" value="td" title="<?= t('вставить ячейку таблицы', 'plugins') ?>" onClick="addText('[td]', '[/td]') ">
  </div>
    <div class="b2c-group">CSS
    <input type="button" value="p=" title="<?= t('P=стиль CSS, [p=стиль]...[/p]', 'plugins') ?>" onClick="addText('[p=]', '[/p]') ">
   <input type="button" value="div=" title="<?= t('DIV=стиль CSS, [div=стиль]...[/div]', 'plugins') ?>" onClick="addText('[div=]', '[/div]') ">
  	<input type="button" value="div()" title="<?= t('DIV(CSS класс), [div(class)]...[/div]', 'plugins') ?>" onClick="addText('[div()]', '[/div]') ">
  	<input type="button" value="span()" title="<?= t('SPAN(CSS класс), [span(class)]...[/span]', 'plugins') ?>" onClick="addText('[span()]', '[/span]') ">
    </div>
    <div class="b2c-group">Выравнивание &nbsp; [DIV]
  	 <input type="button" value="divJ" title="<?= t('DIV justify, [div]...[/div]', 'plugins') ?>" onClick="addText('[div=text-align:justify]', '[/div]') ">
	<input type="button" value="divL" title="<?= t('DIV left, [left]...[/left]', 'plugins') ?>" onClick="addText('[left]', '[/left]') ">
	<input type="button" value="divR" title="<?= t('DIV right, [right]...[/right]', 'plugins') ?>" onClick="addText('[right]', '[/right]') ">
	<input type="button" value="divC" title="<?= t('DIV center, [center]...[/center]', 'plugins') ?>" onClick="addText('[center]', '[/center]') ">
 &nbsp; [P]
     <input type="button" value="pJ" title="<?= t('P justify, [p]...[/p]', 'plugins') ?>" onClick="addText('[p=text-align:justify]', '[/p]') ">
	<input type="button" value="pL" title="<?= t('P left, [pleft]...[/pleft]', 'plugins') ?>" onClick="addText('[pleft]', '[/pleft]') ">
	<input type="button" value="pR" title="<?= t('P right, [pright]...[/pright]', 'plugins') ?>" onClick="addText('[pright]', '[/pright]') ">
	<input type="button" value="pC" title="<?= t('P center, [pcenter]...[/pcenter]', 'plugins') ?>" onClick="addText('[pcenter]', '[/pcenter]') ">
	   </div>
     	 <div class="b2c-group">Разметка
	<input type="button" value="xcut" title="<?= t('обрезать текст', 'plugins') ?>" onClick="addText('[xcut]\n', '') "/>
	<input type="button" value="cut" title="<?= t('обрезать текст', 'plugins') ?>" onClick="addText('[cut]\n', '') "/>
	&nbsp;
	<input type="button" value="ushka" title="<?= t('ушка', 'plugins') ?>" onClick="addText('[ushka=', '][/ushka]') ">
	<input type="button" value="audio" title="<?= t('вставить музыкальную композицию формат вывода [audio=http://site.com/my.mp3]', 'plugins') ?>" onClick="addText('[audio=http://', '.mp3]') ">
	<input type="button" value="spoiler" title="<?= t('скрыть текст под спойлер', 'plugins') ?>" onClick="addText('[spoiler]', '[/spoiler]') ">
	<input type="button" value="auth" title="<?= t('текст только для авторизованных', 'plugins') ?>" onClick="addText('[auth]', '[/auth]') ">
	&nbsp;
	<input type="button" value="faqs" title="<?= t('Блок из нескольких FAQ', 'plugins') ?>" onClick="addText('[faqs]\n[faq=', 'вопрос]\n ответ\n[/faq]\n[faq=вопрос2]\n ответ2\n[/faq]\n[/faqs]') ">
	<input type="button" value="faq" title="<?= t('Один FAQ, обрамлять ответ', 'plugins') ?>" onClick="addText('[faq=]\n', '\n[/faq]') ">
	&nbsp;
	 	<input type="button" value="<?= t('цитата', 'plugins') ?>" title="<?= t('цитата', 'plugins') ?>" onClick="addText('[quote]', '[/quote]') "/>
	<input type="button" value="<?= t('код', 'plugins') ?>" title="<?= t('код или преформатированный текст', 'plugins') ?>" onClick="addText('[code]', '[/code]') "/>
	<input type="button" value="pre" title="<?= t('преформатированный текст, [pre]...[/pre]', 'plugins') ?>" onClick="addText('[pre]', '\n[/pre]') ">
	  &nbsp;
	  <input type="button" value="HTML" title="<?= t('HTML-код [html]...[/html]', 'plugins') ?>" onClick="addText('[html]\n', '\n[/html]\n') ">
	  <input type="button" value="PHP" title="<?= t('PHP-код [php]...[/php]', 'plugins') ?>" onClick="addText('[php]\n', '\n[/php]\n') ">
	</div>
	  	 <div class="b2c-group">  LightBox
    <input type="button" value="image" title="<?= t('Картинка  [image(class)]адрес[/image]', 'plugins') ?>" onClick="addText('[image]', '[/image]') "/>
     <input type="button" value="gallery" title="<?= t('Галерея  с картинками', 'plugins') ?>" onClick="addText('[gallery]\n', '[gal=][/gal]\n[gal=][/gal]\n[gal=][/gal]\n[/gallery]') "/>
    <input type="button" value="galname" title="<?= t('Название галереи, [galname][/galname]', 'plugins') ?>" onClick="addText('[galname]', '[/galname]\n') "/>
     <input type="button" value="gal" title="<?= t('одно фото для галереи, [gal=mini.url Название]...[/gal]', 'plugins') ?>" onClick="addText('[gal=]', '[/gal]\n') "/>
      ColorBox
     <input type="button" value="slideshow" title="<?= t('Слайд-шоу', 'plugins') ?>" onClick="addText('[slideshow]\n', '[slide=][/slide]\n[slide=][/slide]\n[slide=][/slide]\n[/slideshow]') "/>
    <input type="button" value="slidename" title="<?= t('Название галереи, [slidename][/slidename]', 'plugins') ?>" onClick="addText('[slidename]', '[/slidename]\n') "/>
     <input type="button" value="slide" title="<?= t('одно фото для галереи, [slide=mini.url Название]...[/slide]', 'plugins') ?>" onClick="addText('[slide=]', '[/slide]\n') "/>
	 </div>

</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
