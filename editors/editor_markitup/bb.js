// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
myBbcodeSettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "~/sets/bbcode/preview.php",
  markupSet: [
  
  
		{name:'Шрифт', openWith:'[b]', closeWith:'[/b]', className:"fonts", dropMenu: [
			{name:'Полужирный', openWith:'[b]', closeWith:'[/b]', className:"bold" },
			{name:'Курсив', openWith:'[i]', closeWith:'[/i]', className:"italic" },
			{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:"underline" },
			{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:"stroke" },
		]},
      
		{name:'Цвет', openWith:'[color=[![Color]!]]', closeWith:'[/color]', className:"colors", dropMenu: [
			{name:'Желтый', openWith:'[color=yellow]', closeWith:'[/color]', className:"col1-1" },
			{name:'Оранжевый', openWith:'[color=orange]', closeWith:'[/color]', className:"col1-2" },
			{name:'Красный', openWith:'[color=red]', closeWith:'[/color]', className:"col1-3" },
			{name:'Синий', openWith:'[color=blue]', closeWith:'[/color]', className:"col2-1" },
			{name:'Фиолетовый', openWith:'[color=purple]', closeWith:'[/color]', className:"col2-2" },
			{name:'Зеленый', openWith:'[color=green]', closeWith:'[/color]', className:"col2-3" },
			{name:'Белый', openWith:'[color=white]', closeWith:'[/color]', className:"col3-1" },
			{name:'Серый', openWith:'[color=gray]', closeWith:'[/color]', className:"col3-2" },
			{name:'Черный', openWith:'[color=black]', closeWith:'[/color]', className:"col3-3" }
		]},

		{name:'Размер', openWith:'[size=[![Text size]!]%]', closeWith:'[/size]', className:"text-smallcaps", dropMenu :[  
			{name:'Большой', openWith:'[size=200%]', closeWith:'[/size]', className:"big" },
			{name:'Нормальный', openWith:'[size=100%]', closeWith:'[/size]', className:"normal" },
			{name:'Маленький', openWith:'[size=50%]', closeWith:'[/size]', className:"small" }  
		]},
		
  
		{name:'Выравнивание', openWith:'[pleft]', closeWith:'[/pleft]', className:"left", dropMenu :[  
			{name:'Абзац влево', openWith:'[pleft]', closeWith:'[/pleft]', className:"left" },
			{name:'Абзац по центру', openWith:'[pcenter]', closeWith:'[/pcenter]', className:"center" },
			{name:'Абзац вправо', openWith:'[pright]', closeWith:'[/pright]', className:"right" },
			{name:'Абзац по формату', openWith:'[pjustify]', closeWith:'[/pjustify]', className:"justify" },
			{separator:'---------------' },
			{name:'Блок влево', openWith:'[left]', closeWith:'[/left]', className:"text-padding-left"}, 
			{name:'Блок по центру', openWith:'[center]', closeWith:'[/center]', className:"text-padding-center"},       
			{name:'Блок вправо', openWith:'[right]', closeWith:'[/right]', className:"text-padding-right"}, 
			{name:'Блок по формату', openWith:'[justify]', closeWith:'[/justify]', className:"text-padding-justify"}, 
		]},
  
		{separator:'---------------' },
	  
		{name:'Ссылка', key:'L', openWith:'[url=[![Адрес]!]]', closeWith:'[/url]', placeHolder:'текст ссылки', className:"link"},
		{name:'Отрезать', replaceWith:'\n[cut]\n', className:"separator"}, 
		{name:'Принудительный перенос', replaceWith:'\n[br]\n', className:"page-red"},
		
		{name:'Цитата', openWith:'[quote]\n', closeWith:'\n[/quote]', className:"quote"}, 
		
		{name:'Линия', openWith:'\n[hr]\n', className:"hr"}, 
		
		{separator:'---------------' },
		
		{name:'Выполнить PHP-код', openWith:'[php]', closeWith:'[/php]', className:"php"}, 
		{name:'Выполнить HTML-код', openWith:'[html]', closeWith:'[/html]', className:"html-code"}, 
		
		{separator:'---------------' },
		
		{name:'Код', openWith:'[pre]', closeWith:'[/pre]', className:"code", dropMenu: [
			{name:'Текст', openWith:'[pre]', closeWith:'[/pre]', className:"text" },
			{name:'PHP', openWith:'[pre lang=php]', closeWith:'[/pre]', className:"php" },
			{name:'CSS', openWith:'[pre lang=css]', closeWith:'[/pre]', className:"css" },
			{name:'JavaScript', openWith:'[pre lang=js]', closeWith:'[/pre]', className:"js" },
			{name:'Delphi', openWith:'[pre lang=delphi]', closeWith:'[/pre]', className:"delphi" },
			{name:'SQL', openWith:'[pre lang=sql]', closeWith:'[/pre]', className:"sql" },
			{name:'C#', openWith:'[pre lang=csharp]', closeWith:'[/pre]', className:"csharp" },
			{name:'XML', openWith:'[pre lang=xml]', closeWith:'[/pre]', className:"xml" }
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
		]},	  
		
		{name:'Таблица', openWith:'\n[table]\n', closeWith:'\n[/table]', className:"table", dropMenu: [
			{name:'Таблица', openWith:'\n[table]\n', closeWith:'\n[/table]\n', className:"table-add"}, 
			{name:'Строка', openWith:'[tr]\n', closeWith:'\n[/tr]', className:"table-row-insert"}, 
			{name:'Ячейка', openWith:'\n[td]', closeWith:'[/td]', className:"table-select"}, 
			{name:'Заготовка1', openWith:'[table]\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]\n[/table]', className:"table-go"}, 
			{name:'Заготовка2', openWith:'\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]', className:"table-go"}, 
		]},	
		
		{name:'Изображение', replaceWith:'[img][![Адрес]!][/img]', className:"picture", dropMenu: [
			{name:'Изображение', openWith:'[img [![Описание]!]][![Адрес]!][/img]', className:"picture"}, 
			{name:'Влево', openWith:'[imgleft [![Описание]!]][![Адрес]!][/imgleft]', className:"picture"}, 
			{name:'Вправо', openWith:'[imgright [![Описание]!]][![Адрес]!][/imgright]', className:"picture"},
			{name:'Центр', openWith:'[imgcenter [![Описание]!]][![Адрес]!][/imgcenter]', className:"picture"},
		]},
		
		{name:'Медиа', className:"audio", dropMenu: [
			{name:'Аудиоплеер', replaceWith:'[audio=[![Адрес]!]]', className:"audio"}, 
			{name:'Flash (flv, mp4)', replaceWith:'[flash(640,480)][![Адрес]!][/flash]', className:"movies"}, 
			{name:'Flowplayer (flv)', replaceWith:'[flowplayer=[![Адрес]!]', className:"movies"}, 
		]},
		
		{name:'Прочее', className:"script-edit", dropMenu: [
			{name:'div.class', openWith:'[div([![Css class]!])]', closeWith:'[/div]', className:"add"}, 
			{name:'span.class', openWith:'[span([![Css class]!])]', closeWith:'[/span]', className:"add"}, 
			{name:'div arrt', openWith:'[div [![Attribute]!]]', closeWith:'[/div]', className:"add"}, 
			{name:'span arrt', openWith:'[span [![Attribute]!]]', closeWith:'[/span]', className:"add"}, 
		]},
		
		{separator:'---------------' },

		{name:'Очистить выделенное от BB кодов', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }, className:"clean"},
		
		/*{name:'Предпросмотр', className:"preview", call:'preview', className:"preview"}*/
   ]
}