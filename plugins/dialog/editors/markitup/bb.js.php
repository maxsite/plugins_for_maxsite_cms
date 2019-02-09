<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------

myBbcodeSettings = {
	nameSpace:	"bbcode", // Useful to prevent multi-instances CSS conflict
	
	previewParserPath: "<?= getinfo('ajax') . base64_encode('plugins/dialog/editors/markitup/preview-ajax.php') ?>",
	// previewInWindow: 'width=960, height=800, resizable=yes, scrollbars=yes',
	
	<?= $editor_config['previewposition'] ?>
	<?= $editor_config['preview'] ?>
	<?= $editor_config['previewautorefresh'] ?>
	
	
	markupSet:	[

		{name:'Шрифт', openWith:'[b]', closeWith:'[/b]', className:"fonts", dropMenu: [
			{name:'Полужирный (важный)', openWith:'[b]', closeWith:'[/b]', className:"bold", key:"B" },
			{name:'Курсив (важный)', openWith:'[i]', closeWith:'[/i]', className:"italic", key:"I" },
			{separator:'---------------' },
			{name:'Полужирный (простой)', openWith:'[bold]', closeWith:'[/bold]', className:"bold" },
			{name:'Курсив (простой)', openWith:'[italic]', closeWith:'[/italic]', className:"italic" },
			{separator:'---------------' },
			{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:"underline" },
			{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:"stroke" },
			{separator:'---------------' },
			{name:'Верхний индекс', openWith:'[sup]', closeWith:'[/sup]', className:"sup" },
			{name:'Нижний индекс', openWith:'[sub]', closeWith:'[/sub]', className:"sub" },
			{separator:'---------------' },
			{name:'Уменьшенный шрифт', openWith:'[small]', closeWith:'[/small]', className:"small" },
			{separator:'---------------' },
			{name:'Размер текста', openWith:'[size=[![Размер текста]!]%]', closeWith:'[/size]', className:"text-smallcaps"},
		]},
		
		{name:'Ссылка', key:'L', openWith:'[url=[![Адрес с http://]!]]', closeWith:'[/url]', className:"link", dropMenu: [
			{name:'Ссылка (адрес и текст)', openWith:'[url=[![Адрес с http://]!]][![Текст ссылки]!][/url]', closeWith:'', className:"link"}, 
			{name:'Youtube', openWith:'[youtube][![id видео]!]', closeWith:'[/youtube]', className:"flash"}, 
		]},
		      
 		{name:'Цитата', openWith:'[quote]\n', closeWith:'\n[/quote]', className:"quote", dropMenu: [
			{name:'Цитата (блок)', openWith:'\n[quote]\n', closeWith:'\n[/quote]', className:"quote"}, 
		//	{name:'Цитирование в строке', openWith:'[q]', closeWith:'[/q]', className:"quote"}, 
			{name:'Абревиатура', openWith:'[abbr [![Определение]!]]', closeWith:'[/abbr]', className:"abbr"}, 
	//		{name:'Сноска', openWith:'[cite]', closeWith:'[/cite]', className:"cite"}, 
			{name:'Адрес', openWith:'[address]', closeWith:'[/address]', className:"address"}, 
	//		{name:'Новый термин', openWith:'[dfn]', closeWith:'[/dfn]', className:"dfn"}, 
		]},
		     
  		{name:'Изображение', openWith:'[img [![Описание]!]][![Адрес]!][/img]', className:"picture", dropMenu: [
			{name:'Изображение', replaceWith:'[img][![Адрес]!][/img]', className:"picture"}, 
			{separator:'---------------' },
			{name:'[img]', openWith:'[img [![Описание]!]][![Адрес]!][/img]', className:"image_add"},
		//	{name:'[img(left)]', openWith:'[img(left) [![Описание]!]][![Адрес]!][/img]', className:"image_add"},
		//	{name:'[img(right)]', openWith:'[img(right) [![Описание]!]][![Адрес]!][/img]', className:"image_add"},
		//	{name:'[img(center)]', openWith:'[img(center) [![Описание]!]][![Адрес]!][/img]', className:"image_add"},
		]},
		    
		{name:'Цвет', openWith:'[color=[![Color]!]]', closeWith:'[/color]', className:"colors", dropMenu: [
			{name:'Желтый', openWith:'[color=yellow]', closeWith:'[/color]', className:"col-yellow" },
			{name:'Оранжевый', openWith:'[color=orange]', closeWith:'[/color]', className:"col-orange" },
			{name:'Красный', openWith:'[color=red]', closeWith:'[/color]', className:"col-red" },
			{name:'Синий', openWith:'[color=blue]', closeWith:'[/color]', className:"col-blue" },
			{name:'Фиолетовый', openWith:'[color=purple]', closeWith:'[/color]', className:"col-purple" },
			{name:'Зеленый', openWith:'[color=green]', closeWith:'[/color]', className:"col-green" },
			{name:'Белый', openWith:'[color=white]', closeWith:'[/color]', className:"col-white" },
			{name:'Серый', openWith:'[color=gray]', closeWith:'[/color]', className:"col-gray" },
			{name:'Черный', openWith:'[color=black]', closeWith:'[/color]', className:"col-black" },
			{name:'Ярко-голубой', openWith:'[color=cyan]', closeWith:'[/color]', className:"col-cyan" },
			{name:'Ярко-зеленый', openWith:'[color=lime]', closeWith:'[/color]', className:"col-lime" },
			
			{name:'Таблица цветов', className:'help', beforeInsert:function(){miu.select_colors();}, className:"col-select"},
			
		]},
		
		<?php if ($smiles) echo $smiles ?>
		
		
		{separator:'---------------' },
		
		{name:'Выравнивание', openWith:'[p]', closeWith:'[/p]', className:"left", dropMenu :[  
			{name:'Абзац', openWith:'[p]', closeWith:'[/p]', className:"left" },
			{name:'Абзац влево', openWith:'[pleft]', closeWith:'[/pleft]', className:"left" },
			{name:'Абзац по центру', openWith:'[pcenter]', closeWith:'[/pcenter]', className:"center" },
			{name:'Абзац вправо', openWith:'[pright]', closeWith:'[/pright]', className:"right" },
			{name:'Абзац по формату', openWith:'[pjustify]', closeWith:'[/pjustify]', className:"justify" },
			
			{separator:'---------------' },
			
			{name:'Блок влево', openWith:'[left]', closeWith:'[/left]', className:"text-padding-left"}, 
			{name:'Блок по центру', openWith:'[center]', closeWith:'[/center]', className:"text-padding-center"},       
			{name:'Блок вправо', openWith:'[right]', closeWith:'[/right]', className:"text-padding-right"}, 
			{name:'Блок по формату', openWith:'[justify]', closeWith:'[/justify]', className:"text-padding-justify"}, 
			
		//	{separator:'---------------' },
			
		//	{name:'p - абзац', openWith:'[p]', closeWith:'[/p]', className:"add"}, 
			
		//	{separator:'---------------' },
			
		//	{name:'div.class', openWith:'[div([![Css class]!])]', closeWith:'[/div]', className:"add"}, 
	//		{name:'span.class', openWith:'[span([![Css class]!])]', closeWith:'[/span]', className:"add"}, 
	//		{name:'&lt;div свойства&gt;', openWith:'[div [![Свойства]!]]', closeWith:'[/div]', className:"add"}, 
	//		{name:'&lt;span свойства&gt;', openWith:'[span [![Свойства]!]]', closeWith:'[/span]', className:"add"}, 
		]},

		{name:'Заголовок', openWith:'[h1]', closeWith:'[/h1]', className:"h1", dropMenu: [
			{name:'Заголовок 1', openWith:'[h1]', closeWith:'[/h1]', className:"h1"}, 
			{name:'Заголовок 2', openWith:'[h2]', closeWith:'[/h2]', className:"h2"}, 
			{name:'Заголовок 3', openWith:'[h3]', closeWith:'[/h3]', className:"h3"}, 	      
			{name:'Заголовок 4', openWith:'[h4]', closeWith:'[/h4]', className:"h4"}, 
			{name:'Заголовок 5', openWith:'[h5]', closeWith:'[/h5]', className:"h5"}, 
			{name:'Заголовок 6', openWith:'[h6]', closeWith:'[/h6]', className:"h6"}, 
		]},
		
		{name:'Списки', openWith:'\n[list]\n', closeWith:'\n[/list]', className:"list-bullet", dropMenu: [
			{name:'Обычный список', openWith:'\n[list]\n', closeWith:'\n[/list]', className:"list-bullet"}, 
			{name:'Номера', openWith:'\n[ol]\n', closeWith:'\n[/ol]', className:"list-numeric"}, 
			{name:'Элемент списка', openWith:'[*]', closeWith:'\n', className:"list-item"}, 	      
			{name:'3 элемента', openWith:'\n[list]\n[*]\n[*]\n[*]\n', closeWith:'[/list]', className:"list-bullet"}, 
			{name:'5 элементов', openWith:'\n[list]\n[*]\n[*]\n[*]\n[*]\n[*]\n', closeWith:'[/list]', className:"list-bullet"}, 
			
			{separator:'---------------' },
			
			{name:'Список определений', openWith:'\n[dl]\n', closeWith:'\n[/dl]', className:"dl"}, 
			{name:'Определение', openWith:'[dt]', closeWith:'[/dt]', className:"dl"}, 
			{name:'Описание', openWith:'[dd]', closeWith:'[/dd]', className:"dl"}, 
			{name:'Заготовка', openWith:'\n[dl]\n[dt]Определение[/dt]\n[dd]Описание[/dd]\n\n[dt]Определение[/dt]\n[dd]Описание[/dd]\n[/dl]', closeWith:'', className:"dl"}, 
		]},	  
		
		{name:'Таблица', openWith:'\n[table]\n', closeWith:'\n[/table]', className:"table", dropMenu: [
			{name:'Таблица', openWith:'\n[table]\n', closeWith:'\n[/table]\n', className:"table-add"}, 
			{name:'Строка', openWith:'[tr]\n', closeWith:'\n[/tr]', className:"table-row-insert"}, 
			{name:'Ячейка', openWith:'\n[td]', closeWith:'[/td]', className:"table-select"}, 
			{name:'Заготовка1', openWith:'[table]\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]\n[/table]', className:"table-go"}, 
			{name:'Заготовка2', openWith:'\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]', className:"table-go"}, 
		]},	

		{separator:'---------------' },

		{name:'Преформатированный текст с подсветкой синтаксиса', openWith:'[pre]', closeWith:'[/pre]', className:"code", dropMenu: [
			{name:'Код', openWith:'[pre]', closeWith:'[/pre]', className:"text" },
			{name:'Закрыть', openWith:'[auth]', closeWith:'[/auth]', className:"closed" },
			{name:'10 сообщений', openWith:'[count10]', closeWith:'[/count10]', className:"closed" },
			
		//	{name:'PHP-код', openWith:'[pre lang=php]', closeWith:'[/pre]', className:"php" },
		//	{name:'HTML-код', openWith:'[pre lang=html]', closeWith:'[/pre]', className:"html-pre" },
		//	{name:'CSS-код', openWith:'[pre lang=css]', closeWith:'[/pre]', className:"css" },
		//	{name:'JavaScript-код', openWith:'[pre lang=js]', closeWith:'[/pre]', className:"js" },
		//	{name:'Delphi/Pascal-код', openWith:'[pre lang=delphi]', closeWith:'[/pre]', className:"delphi" },
		//	{name:'SQL-код', openWith:'[pre lang=sql]', closeWith:'[/pre]', className:"sql" },
		//	{name:'C#-код', openWith:'[pre lang=csharp]', closeWith:'[/pre]', className:"csharp" },
		//	{name:'XML-код', openWith:'[pre lang=xml]', closeWith:'[/pre]', className:"xml" }
		]},

		{name:'Очистить текст от BB-кодов', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }, className:"clean", dropMenu: [
			
			{name:'Очистить текст от BB-кодов', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }, className:"clean"},
			
			{name:'Замена в тексте', className:'qrepl', beforeInsert:function(markItUp) { miu.repl(markItUp) }},
			
			{separator:'---------------' },
			
			{name:'Принудительный перенос', replaceWith:'[br]\n', className:"page-red"},
			{name:'Линия', openWith:'\n[hr]\n', className:"hr"}, 
			
			{separator:'---------------' },
			

			<?php if (function_exists('spoiler_custom')) { ?>
			{separator:'---------------' },
			{name:'Показать/спрятать (spoiler)', openWith:'[spoiler=[![Заголовок блока]!]]', closeWith:'[/spoiler]', className:"add"}, 
			<?php } ?>

			<?php if (function_exists('auth_content_parse')) { ?>
			{separator:'---------------' },
			{name:'Спрятать от незалогиненных', openWith:'[auth]', closeWith:'[/auth]', className:"add"}, 
			<?php } ?>

		]},
		
		
		{separator:'---------------' },
		
		{name:'Быстрое сохранение текста', className:'qsave', key:"S", beforeInsert:function(markItUp) { miu.save(markItUp) }},
		
		{separator:'---------------' },

		{name:'Предпросмотр (с ALT скрыть)', className:'preview', call:'preview' , key:"E"},
		
		{separator:'---------------' },
		
		{name:'Помощь по BB-кодам', className:'help', beforeInsert:function(){miu.help_bb();} },
		
		<?php mso_hook('editor_markitup_bbcode') ?>

	]
}

miu = {
	save: function(markItUp) 
	{
		data = markItUp.textarea.value;
		$.post(autosaveurl, {"text": data, "id": autosaveid}, 
			function(response) 
			{
				var dd = new Date();
				$('span.autosave-editor').html('<a target="_blank" title="Просмотр черновика" href="' + response + '">Сохранено как временный черновик в ' + dd.toLocaleTimeString() + '</a>');
				alert("Сохранено!");
				
			});
	},
	
	repl: function(markItUp) 
	{
		str = markItUp.textarea.value;
		
		var s_search = prompt('Что ищем?');
		var s_replace = prompt('На что меняем?');
		
		markItUp.textarea.value = str.replace(new RegExp(s_search,'g'), s_replace)
		
		alert("Выполнено!");
	},	
	
	help_bb: function()
	{
		window.open('<?= getinfo('siteurl') ?>application/maxsite/plugins/bbcode/bbcode-help.html');
	},

	select_colors: function()
	{
		window.open('<?= getinfo('siteurl') ?>application/maxsite/plugins/dialog/editors/markitup/color-table.html');
	},
	
}



