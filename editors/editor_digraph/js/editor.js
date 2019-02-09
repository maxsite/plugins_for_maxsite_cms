/*
 * DiGraph
 *
 * Copyright (c) 2009 Andrew Gromoff <andrew@gromoff.net>, http://gromoff.net/digraph
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 09.12.17 alfa
 */

/*
    * CTRL+A highlights the whole editing area
    * CTRL+B changes your font to bold.
    * CTRL+C copies the highlighted area to the clipboard.
    * CTRL+I changes your font to italic.
    * CTRL+L opens the Link window.
    * CTRL+SHIFT+S saves the document.
    * CTRL+U changes your font to underlined.
    * CTRL+V or SHIFT+INSERT pastes the data from the clipboard
    * CTRL+X or SHIFT+DELETE cuts the highlighted area.
    * CTRL+Y or CTRL+SHITF+Z starts the redo function.
    * CTRL+Z starts the undo function.
    * CTRL+ALT+ENTER fits the editor in the browsers window. 

Работа с текстом

Сочетание клавиш	Описание

Ctrl + A	Выделить всё
Ctrl + C
Ctrl + Insert	Копировать
Ctrl + X
Shift + Delete	Вырезать
Ctrl + V
Shift + Insert	Вставить
Ctrl + ←
Ctrl + →	Переход по словам в тексте. Работает не только в текстовых редакторах. Например, очень удобно использовать в адресной строке браузера
Shift + ←
Shift + →
Shift + ↑
Shift + ↓	Выделение текста
Ctrl + Shift + ←
Ctrl + Shift + →	Выделение текста по словам
Home
End
Ctrl + Home
Ctrl + End	Перемещение в начало-конец строки текста
Ctrl + Home
Ctrl + End	Перемещение в начало-конец документа
*/

var DiGraphDef = [
	[
	{id: 'remove-format', run: "getSelection", width: 172, menu: // width - ширина для меню, стандартная - 200 
		[
		{title: 'очистить', callback: "Clear"}
		,{title: 'удалить теги', callback: "removeAllTags"}
		,{title: 'удалить &amp;nbsp;', callback: "removeNBSP"}
		,{title: 'удалить &lt;br /&gt;', callback: "removeBR"}  
		,[]
		,{title: 'заменить &lt;br /&gt; на \\n', callback: "replaceBR"} 
		,{title: 'двойные переводы строк', callback: "doubleLine"}  
		]
	}	
//	,{id: 'redo', title: 'заглушка', disable: true}			// пример глушения кнопок
//	,{id: 'undo', title: 'заглушка', disable: true}	
	],[
	{id: 'bold', title: '<strong></strong>', run: "addText", tag: '<strong>', close: '</strong>'}				// strong b
	,{id: 'italic', tatle: '<em></em>', run: "addText", tag: '<em>', close: '</em>'}				// em
	,{id: 'underline', title: 'text-decoration:underline', run: "addText", tag: '<span style="text-decoration:underline;">', close: '</span>'}
//	,{id: 'strikethrough', tag: '<span style="text-decoration:line-through;">', close: '</span>'} // overline - черта над строкой
	],[
	{id: 'paragraph', run: "addText", width: 220, menu:
		[
		{title: '&lt;p&gt;&lt;/p&gt;', tag: '<p>', close: '</p>'}
		,[]
		,{title: 'абзац, выравнивание влево', tag: '<p style="text-align:left;">', close: '</p>'}
		,{title: 'абзац, выравнивание вправо', tag: '<p style="text-align:right;">', close: '</p>'}
		,{title: 'абзац, выравнивание по центру', tag: '<p style="text-align:center;">', close: '</p>'}
		,[]
		,{title: 'абзац, выравнивание по ширине', tag: '<p style="text-align:justify;">', close: '</p>'}
		]
	}
	,{id: 'header', run: "addText", width: 180, menu:
		[
		{title: '<h1>&lt;h1&gt;&lt;/h1&gt;</h1>', tag: '<h1>', close: '</h1>'}
		,{title: '<h2>&lt;h2&gt;&lt;/h2&gt;</h2>', tag: '<h2>', close: '</h2>'}
		,{title: '<h3>&lt;h3&gt;&lt;/h3&gt;</h3>', tag: '<h3>', close: '</h3>'}
		,{title: '<h4>&lt;h4&gt;&lt;/h4&gt;</h4>', tag: '<h4>', close: '</h4>'}
		,{title: '<h5>&lt;h5&gt;&lt;/h5&gt;</h5>', tag: '<h5>', close: '</h5>'}
		,{title: '<h6>&lt;h6&gt;&lt;/h6&gt;</h6>', tag: '<h6>', close: '</h6>'}
		]
	}
	,{id: 'list', run: "addText", title: 'вставить список',  width: 220, menu:
		[
		{title: 'вставить нумерованный список', tag: "<ol>\n\t<li></li>\n\t<li></li>\n\t<li></li>\n</ol>"}
		,{title: 'вставить ненумерованный список', tag: "<ul>\n\t<li></li>\n\t<li></li>\n\t<li></li>\n</ul>"}
		]
	}
	//,{id: 'decrease-indent', tag: '', close: '', disable: true}								// см. text-indent
	//,{id: 'increase-indent', tag: '<p style="margin-left:40px;">', close: '</p>', disable: true}
	],[
	// в XHTML роль якоря может выполнять любой элемент с установленным атрибутом id
	{id: 'link', title: 'создать ссылку', run: "makeLink"} // name (id), что бы быть якорем, link <a href="#111">111</a>
	,{id: 'image', title: 'добавить изображение', run: "makeImg"}
	,{id: 'character', title: 'добавить символ', run: "charMap"}
	],[
	{id: 'properties', title: 'свойства тега (атрибуты, класс, стиль...)', run: "Properties"}
	,{id: 'tags', title: 'выбрать тег из списка...', run: "addText", menu:
		[
		{title: 'мой любимый тег &lt;div&gt;', tag: '<div>', close: '</div>\n\n'}
		,{title: 'мой любимый тег &lt;span&gt;', tag: '<span>', close: '</span>'}
		,{title: 'частая комбинация', tag: '<div><span>', close: '</span></div>\n\n'}
		,{title: 'таблица 3x3', tag: '<table>\n<tr>\n\t<td>', close: '</td>\n\t<td></td>\n\t<td></td>\n</tr>\n<tr>\n\t<td></td>\n\t<td></td>\n\t<td></td>\n</tr>\n<tr>\n\t<td></td>\n\t<td></td>\n\t<td></td>\n</tr>\n</table>\n\n'}
		,[]
		,{title: 'Структурные элементы', submenu:
			[
			{title: '&lt;div&gt;', tag: '<div>', close: '</div>\n\n'}
			,{title: '&lt;p&gt;', tag: '<p>', close: '</p>\n\n'}
			,[]
			,{title: '&lt;h1&gt;', tag: '<h1>', close: '</h1>\n\n'}
			,{title: '&lt;h2&gt;', tag: '<h2>', close: '</h2>\n\n'}
			,{title: '&lt;h3&gt;', tag: '<h3>', close: '</h3>\n\n'}
			,{title: '&lt;h4&gt;', tag: '<h4>', close: '</h4>\n\n'}
			,{title: '&lt;h5&gt;', tag: '<h5>', close: '</h5>\n\n'}
			,{title: '&lt;h6&gt;', tag: '<h6>', close: '</h6>\n\n'}
			]
		}
		,{title: 'Другие блочные элементы', submenu:
			[
			{title: '&lt;address&gt;', tag: '<address>', close: '</address>\n\n'}
			,{title: '&lt;blockquote&gt;', tag: '<blockquote>', close: '</blockquote>\n\n'}
			,{title: '&lt;hr /&gt;', tag: '<hr />\n\n'}
			,{title: '&lt;pre&gt;', tag: '<pre>', close: '</pre>\n\n'}
			]
		}
		,{title: 'Списки', submenu:
			[
			{title: '&lt;ol&gt;', tag: '<ol>', close: '</ol>'}
			,{title: '&lt;ul&gt;', tag: '<ul>', close: '</ul>'}
			,{title: '&lt;li&gt;', tag: '<li>', close: '</li>'}
			,[]
			,{title: '&lt;dl&gt;', tag: '<dl>', close: '</dl>'}
			,{title: '&lt;dt&gt;', tag: '<dt>', close: '</dt>'}
			,{title: '&lt;dd&gt;', tag: '<dd>', close: '</dd>'}
			]
		}
		,{title: 'Таблицы', submenu:
			[
			{title: '&lt;table&gt;', tag: '<table>', close: '</table>'}
			,{title: '&lt;tr&gt;', tag: '<tr>', close: '</tr>'}
			,{title: '&lt;td&gt;', tag: '<td>', close: '</td>'}
			,{title: '&lt;th&gt;', tag: '<th>', close: '</th>'}
			,{title: '&lt;thead&gt;', tag: '<thead>', close: '</thead>'}
			,{title: '&lt;tbody&gt;', tag: '<tbody>', close: '</tbody>'}
			,{title: '&lt;tfoot&gt;', tag: '<tfoot>', close: '</tfoot>'}
			,{title: '&lt;colgroup&gt;', tag: '<colgroup span="">', close: '</colgroup>'}
			,{title: '&lt;col /&gt;', tag: '<col />'}
			,{title: '&lt;caption&gt;', tag: '<caption>', close: '</caption>'}
			]
		}
		,{title: 'Внутристрочные элементы', submenu:
			[
			{title: '&lt;br /&gt;', tag: '<br />'}
			,{title: '&lt;em&gt;', tag: '<em>', close: '</em>'}
			,{title: '&lt;span&gt;', tag: '<span>', close: '</span>'}
			,{title: '&lt;strong&gt;', tag: '<strong>', close: '</strong>'}
			,{title: '&lt;sub&gt;', tag: '<sub>', close: '</sub>'}
			,{title: '&lt;sup&gt;', tag: '<sup>', close: '</sup>'}
			]
		}
		,[]
		,{title: 'Редко употребляемые теги', submenu:
			[
			{title: '&lt;abbr&gt;', tag: '<abbr>', close: '</abbr>'}
			,{title: '&lt;acronym&gt;', tag: '<acronym>', close: '</acronym>'}
			,{title: '&lt;dfn&gt;', tag: '<dfn>', close: '</dfn>'}
			,{title: '&lt;cite&gt;', tag: '<cite>', close: '</cite>'}
			,{title: '&lt;code&gt;', tag: '<code>', close: '</code>'}
			,{title: '&lt;kbd&gt;', tag: '<kbd>', close: '</kbd>'}
			,{title: '&lt;samp&gt;', tag: '<samp>', close: '</samp>'}
			,{title: '&lt;q&gt;', tag: '<q>', close: '</q>'}
			,{title: '&lt;var&gt;', tag: '<var>', close: '</var>'}
			]
		}
		,{title: 'Теги, заменяемые CSS', submenu:
			[
			{title: '&lt;b&gt;', tag: '<b>', close: '</b>'}
			,{title: '&lt;big&gt;', tag: '<big>', close: '</big>'}
			,{title: '&lt;i&gt;', tag: '<i>', close: '</i>'}
			,{title: '&lt;small&gt;', tag: '<small>', close: '</small>'}
			,{title: '&lt;tt&gt;', tag: '<tt>', close: '</tt>'}
			]
		}
		]
	}
//	,{id: 'mystyle', title: 'пользовательские стили...', run: "addMyStyle", menu: []}
	],[
	{id: 'typograph', title: 'типографировать текст', run: "Typograph", width: 148, menu:	// width - ширина для меню, стандартная - 200 
		[
		{title: 'расставить теги', mode:"parsing"}
		,{title: 'типографировать', mode:"typograph"}
		,[]
		,{title: 'www.typograf.ru', mode:"typograf.ru"}
		,{title: 'www.artlebedev.ru', mode:"artlebedev.ru"}
		]
	}
	,{id: 'validator', title: 'пройти валидацию текста', run: "Validator"}
	,{id: 'preview',  title: 'режим просмотра', run: "Preview"}
	,{id: 'format', title: 'включить представление формата', run: "Format", disable: true}
	,{id: 'contentEditable', title: 'WYSIWYG: визуальное редактирование', run: "contentEditable"}
	]
//	,{id: 'help'}
];

var DiGraphRunTime = {};
var DiGraphPlugin = [];

function DiGraph(path, idtextarea)
	{
	this.id = idtextarea;
	this.rid = '#'+ idtextarea;

	this.caretPos=0;
	this.scrollTop=0;

	this.path = path;	
	this.menu = [];

	this.timermenuShow	= undefined;
	this.timermenuHide		= undefined;

	this.activeMenu = undefined;
	this.activeSubMenu = undefined;
	this.hoverItem = undefined;
	this.hoverSubItem = undefined;

	// результат поиска
	this.search = {}; // start? end
	// данные от диалога по изменению атрибутов и инлайн стилей
	this.attr = {};
	this.tag = '';	
	this.end = '';	
	this.tagname = '';	
	this.tagcontent = '';	

	this.activetab =null; // ссылка на активную закладку

	// change
	this.change = [];

	this.selectedLang = 'дополнительные латинские'; // for charMap
	this.FcontentEditable = false;

	var _self = this;

	$(".f_header").addClass("ui-corner-all");
	
	function makeMenu(b, id)
		{
		var style='';

		if (b.width)
			{style=' style="width:'+b.width+'px;"'}

		var stm = '<ul class="digraph-menu ui-corner-bl ui-corner-br"'+style+'>';

		for (var m = 0; m < b.menu.length; m++)
			{
			var idm =  id + '-' + m;
			DiGraphRunTime[idm] =  b.menu[m];
					
			var submarker='';		
					
			if (typeof b.menu[m].submenu !== "undefined")
				{
				submarker = 'class="pointer"';

				var submenu = b.menu[m].submenu;
				var stsm = '<ul id="'+idm+':" class="digraph-menu ui-corner-all" style="z-index:101;width:110px;border-top:1px solid #5a6471;">'; // : !!! sub
				for (var sm = 0; sm < submenu.length; sm++)
					{
					var idsm =  idm + '-' + sm;
					DiGraphRunTime[idsm] =  submenu[sm];

					DiGraphRunTime[idsm].parent = idm;

					if (typeof submenu[sm].title === "undefined")
						{stsm +='<li class="separator"><hr /></li>';}
					else	{
						DiGraphRunTime[idsm].run = b.run;
						stsm+='<li id="' + idsm + '" class="digraph-menuitem"><span>' + submenu[sm].title + '</span></li>';
						}
					}

				_self.menu[idm] = $(stsm + '</ul>').appendTo("body");
				}

			if (typeof b.menu[m].title === "undefined")
				{stm +='<li class="separator"><hr /></li>';}
			else	{
				DiGraphRunTime[idm].run = b.run;
				stm+='<li id="' + idm + '" class="digraph-menuitem"><span ' + submarker + '>' + b.menu[m].title + '</span></li>';
				}
			}	// end submenu

		_self.menu[id] = $(stm + '</ul>').appendTo("body");
		}

	function makeButton(b)
		{
		var id =  _self.id + '-' + b.id;

		DiGraphRunTime[id] = b;

		var state	= (b.disable == true) ? ' ui-state-disabled' : '';
		var title	= (b.title) ? ' title="' + b.title + '"' : '';		

		if (typeof b.menu !== "undefined")
			{
			makeMenu(b, id);
			return '<div id="'+id+'" class="digraph-bmenu ui-button ui-state-default'+state+'"'+ title+'><span class="'+b.id+'"></span></div>'; // 
			}
		else	{
			return '<div id="'+id+'" class="digraph-button ui-button ui-state-default'+state+'"'+ title+'><span class="'+b.id+'"></span></div>';
			}
		}

	function MakePanels(c)
		{
		//c.push('<span class="divider">&nbsp;</span>');
		c.push('<span class="divider" style="width:2px"></span>');

		for(var p=0,L=DiGraphDef.length; p<L; p++)
			{
			if (DiGraphDef[p].length)	// панель ?
				{
				for (var i = 0; i < DiGraphDef[p].length; i++)
					{
					c.push(makeButton(DiGraphDef[p][i]));
					}
				if ((p+1) < L)	// последний divider не выводим
					{
					c.push('<span class="divider"></span>');
					}
				}
			else	{c.push(makeButton(DiGraphDef[p]))}	// отдельная кнопка	
			}
		}
		
	if (!$(this.rid))
		{
		alert('DiGraph: not found ID :' + idtextarea);	
		return false;
		}

	/* инициализация плагинов */
	for(var i=0,L=DiGraphPlugin.length;i<L;i++){this[DiGraphPlugin[i]]()}

	var c=[];
	c.push('<div class="digraph" id="' + this.id + '-digraph' + '"><div class="panels ui-corner-tl ui-corner-tr" id="' + this.id + '-panels">');	

	MakePanels(c);

	c.push('</div>');
	c.push('<div id="' + this.id + '-validatorbox" class="validatorbox"><div id="' + this.id + '-close" class="close"></div><div id="digraph-message" class="ui-corner-all" title="close message"></div></div>');
	c.push('<div id="' + this.id + '-previewbox" class="previewbox ui-corner-bl ui-corner-br"></div>');
	c.push('</div>');
	
	$(this.rid).before(c.join(''));
	this.e = $(this.rid).get(0);
	
	this.gotoCaretPos(0);
	this.e.focus();
	}

/*==========================================================================*/

DiGraph.prototype.init = function()
	{
	var _self = this;

	// close validatorbox
	$("#"+_self.id + "-close").click(function()
		{
		$(_self.rid+"-validatorbox").hide(0);
		_self.e.focus();	
		});
			
	// автоубирание меню...
	// очистка события автоубирания меню

	function tMenuHide()
		{
		if (!_self.timermenuHide)
			{
			if (_self.timermenuShow)	// если запущен SHOW
				{
				clearTimeout(_self.timermenuShow);
				_self.timermenuShow=undefined;
				}
			
			if (_self.activeMenu)		// если активно
				{
				_self.timermenuHide = window.setTimeout(function()
					{
					_self.hideMenu();
					}, 200); 
				}
			}
		}

	function tMenuShow(bmenu, idm)
		{
		if (!_self.timermenuShow)
			{
			if (_self.timermenuHide)
				{
				clearTimeout(_self.timermenuHide);
				_self.timermenuHide=undefined;
				}

			_self.timermenuShow = window.setTimeout(function(){_self.showMenu(bmenu, idm)},100);
			}
		}

	$(".digraph-menu")
		.mouseover(function()
			{
			if (_self.timermenuHide){clearTimeout(_self.timermenuHide);_self.timermenuHide=undefined}
			})
		.mouseout(function()
			{
			tMenuHide()
			});


	// обработчик кнопки меню
	$(".digraph-bmenu")
		.mouseover(function(event)
			{
			if($(this).hasClass(".ui-state-disabled")===true){return}
			$(this).addClass("ui-state-hover");

			tMenuShow(this, $(this).attr("id"));
			})
		.mouseout(function(event)
			{
			if($(this).hasClass(".ui-state-disabled")===true){return}
			$(this).removeClass("ui-state-hover");

			tMenuHide();
			});

	// обработка пунктов меню
	$(".digraph-menuitem")
		.mouseover(function()
			{
			if (!DiGraphRunTime[this.id].parent)
				{
				if (_self.hoverItem){$(_self.hoverItem).removeClass("hover")}

				if (DiGraphRunTime[this.id].submenu)
					{_self.showSubMenu(this.id)}
				else	{_self.hideSubMenu()}

				_self.hoverItem = this;
				}
			else	{
				if (_self.hoverSubItem){$(_self.hoverSubItem).removeClass("hover")}

				_self.hoverSubItem = this;
				}

			$(this).addClass("hover");
			})
		.mousedown(function()
			{
			_self.hideMenu();
			if (DiGraphRunTime[this.id].run){_self[DiGraphRunTime[this.id].run](this)}
			window.setTimeout(function(){_self.e.focus()}); 
			});

	// обработчик кнопок

	// обработчик кнопок
	$(".digraph-button")
		.mouseover(function()
			{
			if($(this).hasClass(".ui-state-disabled")===true){return}
			$(this).addClass("ui-state-hover");
			_self.hideMenu();
			})
		.mouseout(function()
			{
			if($(this).hasClass(".ui-state-disabled")===true){return}
			$(this).removeClass("ui-state-hover");
			})
		.mousedown(function()
			{
			if($(this).hasClass(".ui-state-disabled")===true){return}
			$(this).addClass("ui-state-active");

			if (DiGraphRunTime[this.id].run){_self[DiGraphRunTime[this.id].run](this)}
			
			//var d = $(_self.rid).css('display');

			//if ((d === 'inline') || (d === 'inline-block'))		// inline-block for IE7 & IE8
			//	{
				//window.setTimeout(function(){_self.LoadState(true);},10);
			//	}
			});


	if ($.browser.msie){this.forIE();}

	// click & [ALT]

	$(this.rid).click(function(e)
		{
		if(e.altKey)
			{
			if (_self.searchTag() === true)
				{
				_self.e.setSelectionRange(_self.search.start, _self.search.end+1);
				return false;
				}
			}
		else	{
			if ($.browser.msie)	
				{_self.e.storeSelection()}
			}
		return false; // !!!
		});

		
	// разрешаем табуляцию	
	$(this.rid).keydown(function(event)
		{

		switch (event.keyCode)
			{
			case 9:
				if (event.currentTarget.nodeName)
					{
					if (event.currentTarget.nodeName.toLowerCase() == "textarea"){event.currentTarget.focus()}
					}
				if(document.selection)
					{
					var iesel = document.selection.createRange().duplicate();
					iesel.text = "\t";
					}
				else	{
					var start = event.currentTarget.selectionStart;
					var end = event.currentTarget.selectionEnd;
					var left = event.currentTarget.value.substring(0, start);
					var right = event.currentTarget.value.substring(end);
					var scroll = event.currentTarget.scrollTop;
					event.currentTarget.value = left + "\t" + right;
					event.currentTarget.selectionStart = event.currentTarget.selectionEnd = start + 1;
					event.currentTarget.scrollTop = scroll;
					event.currentTarget.focus();
					}
				if (event.isDefaultPrevented())
					{
					event.preventDefault();
					}
					
				event.returnValue = false;
				return false;
			}	
		});


	$("#digraph-properties")
		.overlay(
			{
			closeOnClick: false
			,fadeInSpeed:0
			,speed:0
			,onLoad:function()
				{
				if (!$("#digraph-properties").attr('saveTop'))
					{
					var pos = $(_self.rid).offset();
					$("#digraph-properties").css({'top':pos.top+8});
					}
				else	{
					$("#digraph-properties").css({'top':$("#digraph-properties").attr('saveTop'), 'left':$("#digraph-properties").attr('saveLeft')});
					}
				}
			,onBeforeClose:function()
				{
				var pos = $("#digraph-properties").offset();
				$("#digraph-properties").attr('saveTop', pos.top+'px');
				$("#digraph-properties").attr('saveLeft', pos.left+'px');
				}
			,onClose: function()
				{ 
				$("#digraph-attr-" + _self.tagname).hide();

				_self.modalButtons('', false);

				window.setTimeout(function(){_self.LoadState();},10);  
				} 
			})
		.jqDrag('.digraph-header');

	$("ul.digraph-tabs").tabs("fieldset.digraph-panes > div");
	// свойства
	$("#digraph-tab-properties-box :input").change(function(){_self.changeProperties("#digraph-tab-properties-box :input")});
	$("#digraph-tab-style-box :input").change(function(){_self.changeProperties("#digraph-tab-style-box :input")});
	$("#digraph-tab-font-box :input").change(function(){_self.changeProperties("#digraph-tab-font-box :input")});
	$("#digraph-tab-text-box :input").change(function(){_self.changeProperties("#digraph-tab-text-box :input")});
	// кнопки
	$("#digraph-prop-cancel").mousedown(function(){$("#digraph-properties").overlay().close()});
	$("#digraph-prop-ok").mousedown(function()
		{
		_self.saveProperties();
		$("#digraph-properties").overlay().close();
		});

	$("#digraph-wait")
		.overlay(
			{
			closeOnClick: false
			,fadeInSpeed:0
			,speed:0
			,expose:
				{
				color: '#888888'
				,opacity: 0.25
				,loadSpeed:0
				,closeSpeed:0
				,closeOnClick: false
				,closeOnEsc: false
				}
			,onLoad:function()
				{
				var pos = $(_self.rid).offset();
				$("#digraph-wait").css({'top':pos.top+32});
				}
			});

	$("#digraph-charmap")
		.overlay(
			{
			closeOnClick: false
			,fadeInSpeed:0
			,speed:0
			,onLoad:function()
				{
				if (!$("#digraph-charmap").attr('saveTop'))
					{
					var pos = $(_self.rid).offset();
					$("#digraph-charmap").css({'top':pos.top+8});
					}
				else	{
					$("#digraph-charmap").css({'top':$("#digraph-charmap").attr('saveTop'), 'left':$("#digraph-charmap").attr('saveLeft')});
					}
				}
			,onBeforeClose:function()
				{
				var pos = $("#digraph-charmap").offset();
				$("#digraph-charmap").attr('saveTop', pos.top+'px');
				$("#digraph-charmap").attr('saveLeft', pos.left+'px');
				}
			,onClose: function()
				{ 
				_self.modalButtons('', false);
				_self.SaveState();
				window.setTimeout(function(){_self.LoadState();},10);  
				} 
			})
		.jqDrag('.digraph-header');

	$("#digraph-select-range").change(function(){_self.renderCharMapHTML()});
	$("#digraph-char-font").change(function(){_self.renderCharFont(this)});

	this.renderCharFont($("#digraph-char-font").get(0));
	this.mapLoad();

	// ОТКЛЮЧЕНИЕ КНОПКИ CE
	if (($.browser.msie) || ($.browser.opera))
		{
		$(this.rid+"-contentEditable").addClass("ui-state-disabled");	
		}


	$("#digraph-contentEditable *").live('click',function()
			{
			//alert(1);
			//$(this).attr('contentEditable',"true");
			$(this).css('background',"#f0f0f0");

			});


	}

/* FUNCTION =================================== */

DiGraph.prototype.externalLink = function()
	{
	$("a[href][rel *= 'external']").each(function()
		{
		$(this).attr('target',"_blank");
		$(this).attr('title',$(this).attr('title') + ' (откроется в новом окне)');
		});
	}

DiGraph.prototype.showMenu = function(bmenu, id)
	{
	this.timermenuShow = undefined;

	//if($("#"+id).hasClass(".ui-state-disabled")===true){return}
	//if($("#"+id).hasClass(".ui-state-active")!==true){return;}

	if (this.activeMenu != id)
		{
		$(bmenu).addClass("ui-state-active");

		if (this.menu[this.activeMenu]){this.hideMenu()}

		this.activeMenu = id;
		var pos = $("#"+id).offset();
		//var posta = $(this.rid).offset(); // textarea
		$(this.menu[id]).css({'top': pos.top + $("#"+id).height(), 'left': pos.left+1});
		//$("#"+id).addClass("ui-state-active");
		$(this.menu[id]).slideDown(200);
		}
	}
DiGraph.prototype.showSubMenu = function(id)
	{
	if (!this.activeMenu){return false} 

	if (this.activeSubMenu != id)
		{
		this.hideSubMenu();

		if (this.menu[id])
			{
			this.activeSubMenu = id;
			var pos = $("#"+id).offset();
			$(this.menu[id]).css({'top': pos.top-1, 'left': pos.left + $("#"+id).width() - 6});
			$(this.menu[id]).slideDown(0);
			}
		}
	}
DiGraph.prototype.hideSubMenu = function(id)
	{
	if (this.activeSubMenu)
		{
		if ((id) && (this.activeSubMenu == id))
			{return false}
		
		$(this.menu[this.activeSubMenu]).slideUp(0);
		//$("#"+this.activeSubMenu).removeClass("hover");
		this.activeSubMenu = undefined;

		if (this.hoverSubItem)
			{
			$(this.hoverSubItem).removeClass("hover");
			this.hoverSubItem = undefined;
			}
		}
	}
DiGraph.prototype.hideMenu = function(id)
	{
	if (this.timermenuHide){clearTimeout(this.timermenuHide);this.timermenuHide=undefined}

	if (this.activeMenu)
		{
		if ((id) && (this.activeMenu == id)){return false}
	
		this.hideSubMenu();

		$("#"+this.activeMenu).removeClass("ui-state-active");
		$(this.menu[this.activeMenu]).slideUp(200);
		this.activeMenu = undefined;

		if (this.hoverItem)
			{
			$(this.hoverItem).removeClass("hover");
			this.hoverItem = undefined;
			}
		}
	}

if ($.browser.msie)
	{
	DiGraph.prototype.getCaretPos = function()
		{
		this.e.focus();
		var sel = document.selection.createRange();
		var clone = sel.duplicate();
		sel.collapse(true);
		clone.moveToElementText(this.e);
		clone.setEndPoint('EndToEnd', sel);
		return clone.text.length;
		}

	DiGraph.prototype.gotoCaretPos = function(pos)
		{
		var correct = this.e.value.substr(0, pos).match(/\r/g);
		correct = correct ? correct.length : 0;

		var sel = this.e.createTextRange();      
		sel.collapse(true);
		sel.moveStart("character", pos - correct);
		sel.moveEnd("character", 0);
		sel.select();
		}

	DiGraph.prototype.getSelection = function(b)
		{
		this.hideMenu();
		this.SaveState();

		var button	= DiGraphRunTime[b.id];
	
		this.e.focus();
		
		var sel =  document.selection.createRange();

		if (sel.text)
			{
			sel.text = this[button.callback](sel.text);
			sel.select();
			}
		else	{
			this.e.value = this[button.callback](this.e.value);
			}

		var _self=this;
		window.setTimeout(function(){_self.LoadState();},10);
		$(b).removeClass("ui-state-active");
		}
	}	
else	{
	DiGraph.prototype.getCaretPos = function()
		{
		if (this.e.selectionStart!==false)
			{return this.e.selectionStart}
		else {return 0}
		}

	DiGraph.prototype.gotoCaretPos = function(pos)
		{
		var end = this.e.value.length;  
		this.e.setSelectionRange(pos,pos);  
		this.e.focus();  
		}

	DiGraph.prototype.getSelection = function(b)
		{
		this.hideMenu();
		this.SaveState();

		var button	= DiGraphRunTime[b.id];
	
		if (this.e.selectionStart || this.e.selectionStart == '0')
			{
			var startPos	= this.e.selectionStart;
			var endPos	= this.e.selectionEnd;
			var cursorPos	= endPos;
			var scrollTop	= this.e.scrollTop;
			
			if (startPos != endPos)
				{
				var v = this[button.callback](this.e.value.substring(startPos, endPos));
				this.e.value = this.e.value.substring(0, startPos) + v + this.e.value.substring(endPos, this.e.value.length);
				cursorPos = startPos + v.length
				}
			else	{
				this.e.value = this[button.callback](this.e.value);
				cursorPos = 0;
				}
				
			this.e.selectionStart	= cursorPos;
			this.e.selectionEnd		= cursorPos;
			this.e.scrollTop		= scrollTop;
			}
		else	{alert('Видимо, Ваш браузер что-то не поддерживает :) Обратитесь пож. к разработчику...');}

		var _self=this;
		window.setTimeout(function(){_self.LoadState();},10);
		$(b).removeClass("ui-state-active");
		}
	}		

DiGraph.prototype.SaveState = function()
	{
	this.scrollTop = this.e.scrollTop;
	this.caretPos = this.getCaretPos();
	}
DiGraph.prototype.LoadState = function()
	{
	this.e.scrollTop = this.scrollTop;
	this.gotoCaretPos(this.caretPos);
	this.e.focus();
	}
DiGraph.prototype.modalButtons = function(b, state)
	{
	if (state===true)
		{
		$(".panels > .ui-button:not(.ui-state-disabled)").addClass("ui-state-disabled digraph-disabled");
		$(b).removeClass("ui-state-disabled digraph-disabled");	
		$(b).addClass("ui-state-active");
		}
	else	{
		$(".digraph-disabled").removeClass("ui-state-disabled digraph-disabled");
		$(".ui-state-active").removeClass("ui-state-active");
		}
	
	}

/* CLEAR FUNCTIONS */	
	
DiGraph.prototype.Clear = function(v)
	{
	return '';
	}

// arguments.callee

DiGraph.prototype.removeAllTags = function(v)
	{
	this.scrollTop=0;
	this.caretPos=0;
	return v.replace(/<([^>]*)>/g,'');
	}	
DiGraph.prototype.removeNBSP = function(v)
	{
	this.scrollTop=0;
	this.caretPos=0;
	return v.replace(/(&nbsp;)/g,' ');
	}
DiGraph.prototype.replaceBR = function(v)
	{
	this.scrollTop=0;
	this.caretPos=0;
	v = v.replace(/(&lt;br\s?\/?&gt;)/gim,"\n");
	return v.replace(/(<br\s?\/?>)/gim,"\n");
	}	
DiGraph.prototype.removeBR = function(v)
	{
	this.scrollTop=0;
	this.caretPos=0;
	v = v.replace(/(&lt;br\s?\/?&gt;)/gim,'');
	return v.replace(/(<br\s?\/?>)/gim,'');
	}	
DiGraph.prototype.doubleLine= function(v)
	{
	this.scrollTop=0;
	this.caretPos=0;
	return v.replace(/\r/gm,'').replace(/(\n+)/g,'\n\n'); // \r - for IE & Opera
	}	

/*================================================================*/

DiGraph.prototype.addText = function(b)
	{
	var button = DiGraphRunTime[b.id]

	if (typeof button.submenu !== "undefined")
		{return false;}

	var t1 = button.tag;
	var t2 = (button.close) ? button.close : '';

	if (document.selection) // MSIE & Opera
		{
		this.e.focus();
		var sel = document.selection.createRange();
		sel.text = t1 + sel.text + t2;
		sel.select();
		}
	else	{
		if (this.e.selectionStart || this.e.selectionStart == '0')
			{
			var startPos	= this.e.selectionStart;
			var endPos	= this.e.selectionEnd;
			var cursorPos	= endPos;
			var scrollTop	= this.e.scrollTop;
			
			if (startPos != endPos)
				{
				this.e.value = this.e.value.substring(0, startPos)
						+ t1
						+ this.e.value.substring(startPos, endPos)
						+ t2
						+ this.e.value.substring(endPos, this.e.value.length);
				cursorPos = startPos + t1.length
				}
			else	{
				this.e.value = this.e.value.substring(0, startPos)
						+ t1
						+ t2
						+ this.e.value.substring(endPos, this.e.value.length);
				cursorPos = startPos + t1.length;
				}
						
			this.e.selectionStart	= cursorPos;
			this.e.selectionEnd		= cursorPos;
			this.e.scrollTop		= scrollTop;
			}
		else	{
			alert('Видимо, Ваш браузер что-то не поддерживает :) Обратитесь пож. к разработчику...');
			}
		}

	var _self=this; window.setTimeout(function(){_self.e.focus();},10);
	$(b).removeClass("ui-state-active");
	}

DiGraph.prototype.searchTag	= function()
	{
	// MSIE, Opera - \n - 2 символа
	// FF, Safari - \n - 1 символ

	this.search.start = 0;
	this.search.end = 0; 

	var p = this.getCaretPos();	// e.value.charAt(p) - работает корректно везде
	var p = this.getCaretPos();	// e.value.charAt(p) - работает корректно везде

	var v = this.e.value;

	if (v.charCodeAt(p) < 14) // попали в разрыв строки
		{
		var f,r,l = v.length;
		var ff = p;
		var rr = p;

		for (f = p; f<l; f++)
			{
			if (v.charCodeAt(f) > 14){ff=f; break}
			}
		for (r = p; r>-1; r--)
			{
			if (v.charCodeAt(r) > 14){rr=r; break}
			}

		if ((p-rr) < (ff-p))
			{p=rr}
		else	{p=ff}
		}

	var start = 0, end = 0;
	
	var L = v.length;
		
	var check = undefined;	
	var close = []; // закрывающий тег
	var tag = []; // открывающий тег
	var incount = 0;
	var direction = undefined; 

	var debug = false;

	function getName(pos,type)
		{
		var name = '';	
			
		//alert('n='+v.charAt(pos));	
		for (var j = pos; j<L; j++)	
			{
			if ((v.charAt(j) == '>') || (v.charAt(j) == ' '))
				{
				if ((type=='end') && (!close.length))
					{end = j;}
				return name;
				}
			name+=v.charAt(j);
			}
		}
			
		//alert('1='+v.charAt(p));	

	for (var i = p; i >= 0; i--)
		{
		if (v.charAt(i) == '<') 
			{
			if (v.charAt(i+1) == '/')	// close teg => continue	
				{
				if (close.length === 0)	
					{close[0] = getName(i+2,'end')}

				continue;
				}
					
			var newtag = getName(i+1,''); 

			if(debug){alert('tt='+close[close.length-1]+' '+newtag+' '+close.length+' dir='+direction);}

			if ((close.length === 0) || (close[close.length-1] == newtag) || (close[close.length-1] === '/')) // if ((tag.length == 0) || (tag[tag.length-1] == newtag) ||)
				{
				if ((tag.length == 0) && (typeof direction === "undefined"))
					{direction = 'forward';}

				if (direction === 'back')
					{start = i}
				else {
					if (tag.length == 0)
						{start = i}
					}

				tag[tag.length] = newtag;

				if(debug){alert('tag='+tag);}

				if (close.length)
					{
					if (close[close.length-1] == '/')
						{
						if(debug){alert('5= simple close');}
						break;
						}
					if (tag[tag.length-1] == close[close.length-1])
						{
					

						if (close.length > 1)
							{
							if(debug){alert(close.length);}
							if(debug){alert('5='+tag+' c='+close+' pop');}
							close.pop(); // удалили последний
							}
						else {
							if(debug){alert('5='+tag+' c='+close+' break');}
							break;
							}
						}
					else	{}		// ищем дальше
					}
				else	{
					// теперь надо найти конечный тег
					// если не было ctag, значит кликнули или по тегу или по content тега, => искать дальше от pos
					if(debug){alert('2='+v.charAt(p+1)+' '+tag);}

					for (var i2 = p+1; i2<L; i2++)
						{

						//if(debug){alert('i2='+v.charAt(i2));}

						if ((v.charAt(i2) == '/') && (v.charAt(i2+1) == '>'))
							{
							if(debug){alert('good='+tag);}

							if ((tag[tag.length-1] == 'img') || (tag[tag.length-1] == 'hr') || (tag[tag.length-1] == 'br') || (tag[tag.length-1] == 'embed')  || (tag[tag.length-1] == 'input'))
								{
								if (close.length === 0)
									{
									close[close.length] = '/';
									if(debug){alert('close-5=/');}
									end=i2+1;
									if(debug){alert('4');}
									break;
									}
								}
							}	
						if (v.charAt(i2) == '<')
							{
							if (v.charAt(i2+1) == '/')
								{
								var newclose = getName(i2+2,'end');

								if(debug){alert('close-4='+newclose+' '+close+' open='+tag);}

								if (tag[tag.length-1] == newclose)
									{
									//close[close.length] = newclose;
								

									if (tag.length > 1)
										{
										if(debug){alert('3 pop');}
										tag.pop();
										}
									else	{
										if(debug){alert('3 break');}
									
										break;
										}
									}
								else	{
									//close = '';
									//close.pop();
									//alert('close-3='+close);
									// продолжаем поиск
									}	
								}
							else	{ // close teg
								var newtag = getName(i2+1,'');
								if ((tag.length == 0) || (tag[tag.length-1] == newtag))
									{
									tag[tag.length] = newtag;

									if(debug){alert('open-1='+tag);}
									}
								}
							}	
						}

					if ((close.length) || (tag.length))
						{
						if(debug){alert('2');}
						break;
						}
					}
				// ищем дальше	
				//alert('ищем дальше');
				}
			}
							
		//if ((v.charAt(i) == '>') && (!close)) {check = '>';}

		if ((v.charAt(i) == '/')) // if ((v.charAt(i) == '/') && (!close))
			{
			if (v.charAt(i+1) == '>') // simple tag
				{
				if (close.length === 0)
					{
					close[0] = '/';

					if(debug){alert('close-2=/');}

					end = i+1;
					}
				}	
			if (v.charAt(i-1) == '<') // click on close tag
				{
				var newclose = getName(i+1,'end');

				if(debug){alert(newclose+' '+close.length+' '+close[close.length-1]);}

				if ((close.length === 0) || (close[close.length-1] === newclose))
					{
					if ((close.length === 0) && (typeof direction === "undefined"))
						{direction = 'back';}

					close[close.length] = newclose;

					if(debug){alert('close-1='+close);}

					}
				}

			if ((close.length) && (check == '>'))
				{alert('пусто'); return false;}
			}	
		}
			
	//alert(close);

	if (!tag.length){return false}
	

	this.search.start = start;
	this.search.end = end; 

	return true;
	}

/* NEW A ========================================================================= */

DiGraph.prototype.makeLink = function(b)
	{
	this.modalButtons(b, true);

	this.attr = {};
	this.tag = '<a>';	
	this.end = '</a>';	
	this.tagname = 'a';	
	this.tagcontent = '';	

	this.SaveState(); // this.scrollTop = this.e.scrollTop;	this.caretPos = this.getCaretPos();

	this.search.start	= this.caretPos;
	this.search.end	= this.caretPos-1;

	//this.scrollTop		= this.e.scrollTop;

	$("#digraph-properties-title").html('New: <strong>' + this.tagname+'</strong>');
	$("#digraph-properties :input").val('');
	$("#digraph-properties :select").removeAttr('selected');
	
	$("#digraph-prop-preview").hide('');
	$("#digraph-attr-" + this.tagname).show();
	$("#digraph-properties").overlay().load();
	}

/* NEW IMG  ========================================================================= */

DiGraph.prototype.makeImg = function(b)
	{
	this.modalButtons(b, true);

	this.attr = {};
	this.tag = '<img';	
	this.end = '';	
	this.tagname = 'img';	
	this.tagcontent = '';	

	//this.caretPos		= this.getCaretPos();

	this.SaveState();

	this.search.start	= this.caretPos;
	this.search.end	= this.caretPos-1;

	//this.scrollTop		= this.e.scrollTop;

	$("#digraph-properties-title").html('New: <strong>' + this.tagname+'</strong>');
	$("#digraph-properties :input").val('');
	$("#digraph-properties :select").removeAttr('selected');
	
	$("#digraph-prop-preview").hide('');
	$("#digraph-attr-" + this.tagname).show();
	$("#digraph-properties").overlay().load();
	
	}


/* PROPERTIES ================================================================= */

DiGraph.prototype.Properties = function(b)
	{
	//this.caretPos = this.getCaretPos();
	
	if (this.searchTag() === false)
		{
		$(b).removeClass("ui-state-active");
		return false;
		}

	var v = this.e.value.substring(this.search.start, this.search.end+1);

	if (!v)
		{
		$(b).removeClass("ui-state-active");
		return false;
		}

	this.modalButtons(b, true);

	this.SaveState();
	
	//this.scrollTop = this.e.scrollTop;
	
	//AR = v.split('/(<[\/!]*?[^<>]*?>)/');
	//var rtags = new XRegExp("(<[^>]*>)", "g");
	//v = ' ' + v + ' ';
	//var rtags = new RegExp("(<[^>]*>)", "g");
	//var items = v.split(rtags);
	
	var items = v.split(/(<[^>]*>)/g);
	//alert(items.length+' 0='+items[0]+' 1='+items[1]);
	this.tag	=  items[1];
	this.end	= '';
	this.tagcontent = '';

	if (items.length > 3)
		{
		this.end = items[items.length-2];
		for (var i = 2; i < items.length-2; i++)
			this.tagcontent += items[i];
		}
	
	var a = this.tag.split(/( |=|")/);

	this.tagname = a[0].replace(/[>|<]/g,'');

	if ((this.tagname == "!--") || (this.tagname == "script") || (this.tagname == "style")) {return false}
		
	$("#digraph-properties-title").html(this.tagname);
	$("#digraph-properties :input").val('');
	$("#digraph-properties :select").removeAttr('selected');

	var topen = false;
	var content = '';
	var w = '';
	var name = '';

	//var def=['id','class','title','style','color','background'];
	attr={};
	var style={};

	//for (var i = 0; i < def.length; i++){attr[def[i]] = ''}

	if (a.length > 0)
		{
		for (var i = 2; i < a.length-1; i++)
			{
			if (!a[i]){continue}

			if (a[i] == '"')
				{
				topen = (!topen);

				if (topen == true)
					{}	
				else	{
					attr[name] = {};
					attr[name].value = content;
					}

				content = '';

				continue;
				}
				
			if ((a[i] == '=') && (topen === false))
				{
				name = w;
				w = '';
				continue;
				}

			if (topen == false)
				{
				if (a[i] != ' '){w = a[i]}

				continue;
				}

			content += a[i];
			}

		for(i in attr)
			{
			if ($("#digraph-attr-"+i))
				{
				attr[i].edit = true;
				$("#digraph-attr-"+i).val(attr[i].value);
				}
			else	{
				attr[i].edit = false;
				}
			}
		}


	if (attr['style'])
		{
		var s = attr['style'].value.split(/(:|;)/);

		topen = true;
		w ='';
		for (var i = 0; i < s.length; i++)
			{
			if (topen === true)
				{
				w = $.trim(s[i]);
				topen = false;
				}
			if  (s[i] == ';')
				{
				topen = true;
				}
			if  (s[i] == ':')
				{
				style[w] = {};
				style[w].value = $.trim(s[i+1]);
				}
			}

		for(i in style)
			{
			if ($("#digraph-"+i))
				{
				style[i].edit = true;

				//alert("#digraph-"+i+' = '+style[i].value);

				$("#digraph-"+i).val(style[i].value);
				}
			else	{
				style[i].edit = false;
				}
			}
		}

	this.attr = attr;

	$("#digraph-prop-preview").html('<div class="post"><div id="' + this.id+'-previewbox-entry" class="entry">' + v + '</div></div>');
	$("#digraph-prop-preview").show('');


	/* определить, какие дополнительные атрибуты включить для тега
	a
		href		%URI;			#IMPLIED
		rel		%LinkTypes;		#IMPLIED
		rev		%LinkTypes;		#IMPLIED
	img
		src		%URI;		#REQUIRED	Путь к графическому файлу. 
		alt		%Text;		#REQUIRED	Альтернативный текст для изображения. 
		height	%Length;	#IMPLIED	Высота изображения.
		width	%Length;	#IMPLIED	Ширина изображения.
	*/

	$("#digraph-attr-" + this.tagname).show();
	$("#digraph-properties").overlay().load();
	}

DiGraph.prototype.changeProperties = function(selector)
	{
	var style = '';
	var css = {};
	$(selector).each(function(i, obj)
		{
		var id = obj.id.split(/digraph-/);

		var v = $.trim(obj.value);

		if (v)
			{
			style += id[1] + ':'+obj.value + ';';
			}

		css[id[1]] = obj.value;
			
		}
	);
	//alert(style);

	$(DiGraph.rid+"-previewbox-entry > "+DiGraph.tagname+":first").css(css);

	return style;
	}


DiGraph.prototype.saveProperties = function()
	{
	var attr = '';
	var _self = this;

	var check_src = false;
	var check_alt = false;

	function makeStyle()
		{
		var style='';

		style +=_self.changeProperties("#digraph-tab-style-box :input");
		style +=_self.changeProperties("#digraph-tab-font-box :input");
		style +=_self.changeProperties("#digraph-tab-text-box :input");

		if (style)
			{style = ' style="'+style+'"'}
		
		return style;	
		}

	$("#digraph-tab-properties-box :input").each(function(i, obj)
		{
		var id = obj.id.split(/digraph-attr-/);

		if (typeof _self.attr[id[1]] !== "undefined")
			{
			delete _self.attr[id[1]];
			}

		var v = $.trim(obj.value);

		if (id[1] == 'style')
			{
			attr +=makeStyle();
			}
		else	{	
			if (v)
				{
				attr +=' ' + id[1] + '="'+obj.value + '"';
				}
			}
		}
		);

	// возврат необрабатываемых атрибутов
	for(i in this.attr)
		{
		attr +=' '+i + '="'+ this.attr[i].value + '"';
		}

	var st = '<' + this.tagname+attr;
	if (this.end)
		{
		this.caretPos = this.search.start + st.length + 1;		
		st +='>' + this.tagcontent + this.end;
		}
	else	{
		this.caretPos = this.search.start + st.length + 3;
		st +=' />';
		}
	this.e.value = this.e.value.substring(0,this.search.start)+st+this.e.value.substring(this.search.end+1);
	}

/* VALIDATOR ==================================================================== */

DiGraph.prototype.responseValidator = function(_self, error, text)
	{
	$("#digraph-wait").overlay().close();

	if (error)
		{
		$("#digraph-message").html(error);
		$(_self.rid+"-validatorbox").show(0);		
		}

	if (text)
		{_self.e.value = text}

	window.setTimeout(function(){_self.LoadState();},10);
	$(".ui-state-active").removeClass("ui-state-active");
	}

DiGraph.prototype.Validator = function(b) // el - нажатая кнопка
	{
	if (!this.e.value)
		{
		this.e.value = 'Здесь надо написать текст, прежде чем вызывать валидатор.\nВот теперь жмите кнопку.\n';
		return false;
		}

	this.SaveState();
		
	$(this.rid+"-validatorbox").hide(0);
		
	$("#digraph-wait-title").html('ВАЛИДАЦИЯ...');
	$("#digraph-wait").overlay().load();

	var _self = this;
	
	$.ajax(
		{
		url: this.path+'php/HTMLPurifier.php'
		,type: "POST"
		,data: {text: _self.e.value, skey: _self.skey}
		,dataType: "json"
		,error: function (xhr, desc, exceptionobj){_self.responseValidator(_self, xhr.responseText)}
		,success : function (json){_self.responseValidator(_self, json.error, json.text)}
		});	
	}

/* PREVIEW ==================================================================== */

DiGraph.prototype.postPreviewBox = function(v)
	{
	// взять заголовок
	var h = $(".f_header").val();
	// а был ли мальчик?
	if (typeof h !== "undefined")
		{h='<h1>' + h + '</h1>'}
	else	{h = '';}	

	$(this.rid+"-previewbox").html('<div class="post">' + h + '<div class="entry">'+ v.replace(/\[(cut|xcut)\]/gm,'')+'</div></div>');
	//$(this.rid+"-previewbox").html('<div class="post">' + h + '<div id ="digraph-contentEditable" contentEditable="true" class="entry">'+v+'</div></div>');

	// привести в порядок ссылки
	this.externalLink();
		
	$(this.rid).hide();
	$(this.rid+"-previewbox").show();
	}

DiGraph.prototype.makePreviewBox = function()	
	{
	var v = this.e.value;
	if($(this.rid+"-format").hasClass("ui-state-on")===true)
		{
		$("#digraph-wait-title").html('ФОРМАТИРОВАНИЕ...');
		$("#digraph-wait").overlay().load();

		var _self = this;
		window.setTimeout(function()
			{
			//$(document).get(0).designMode = 'off';
			_self.postPreviewBox(v.replace(/([\u00A0\u2009\u2011-\u2014\u2212])/gm,function(str){return '<span class="u'+str.charCodeAt()+'">'+str+'</span>';}));
			//$(document).get(0).designMode = 'on';

			$("#digraph-wait").overlay().close();
			}, 0);

		//v=this.formatON(v);
		}
	else	this.postPreviewBox(this.e.value);
	}

DiGraph.prototype.Preview = function(b)
	{
	if ($(this.rid+"-previewbox").css("display") == "none")
		{
		$(b).attr('title','переключиться в режим редактирования');
		this.modalButtons(b, true);
		// сохраним куда вернуться	
		this.SaveState();	
		
		$(this.rid+"-format").removeClass("ui-state-disabled");	

		// в размер textarea
		$(this.rid+"-previewbox").height($(this.rid).height());
		
		this.makePreviewBox();

		//$(document).get(0).designMode = 'on';
		}
	else	{
		//$(document).get(0).designMode = 'off';

		$(this.rid+"-previewbox").hide();
		$(this.rid).show();
		
		$(b).attr('title','переключиться в режим просмотра');	
		this.modalButtons(b, false);
		$(this.rid+"-format").addClass("ui-state-disabled");

		var _self=this; window.setTimeout(function(){_self.LoadState();},10);
		}
	}

/* FORMAT */
/*
DiGraph.prototype.formatON = function(v)
	{

	v = v.replace(/\u00A0/gm,'<span class="nbsp">\u00A0</span>');	// неразрывный пробел
	v = v.replace(/\u2009/gm,'<span class="thinsp">\u2009</span>');	// тонкая шпанация
	v = v.replace(/\u2011/gm,'<span class="u2011">\u2011</span>');	// неразрывный
	v = v.replace(/\u2012/gm,'<span class="u2012">\u2012</span>');	// цифровой 	
	v = v.replace(/\u2013/gm,'<span class="u2013">\u2013</span>');	// среднее тире 	
	v = v.replace(/\u2014/gm,'<span class="u2014">\u2014</span>');	// прямая речь
	v = v.replace(/\u2212/gm,'<span class="u2212">\u2212</span>');	// унарный минус
	
	v = v.replace(/([\u00A0\u2009\u2011-\u2014\u2212])/gm,function(str){return '<span class="u'+str.charCodeAt()+'">'+str+'</span>';});

	return v;
	}
*/

DiGraph.prototype.Format = function(b)
	{
	if ($(b).hasClass("ui-state-disabled")===true){return}

	if($(b).hasClass("ui-state-on")===true)
		{
		$(b).removeClass("ui-state-on");	
		$(b).removeClass("ui-state-active");	
		$(b).attr('title','включить представление формата');
		this.makePreviewBox();
		}
	else	{
		$(b).addClass("ui-state-on");
		$(b).attr('title','выключить представление формата');
		this.makePreviewBox();
		}
	}

/* CONTENT EDITABLE =========================================================== */

DiGraph.prototype.contentEditable = function(b)
	{
	if (this.FcontentEditable === false)
		{
		this.FcontentEditable = true;
		this.modalButtons(b, true);
		// сохраним куда вернуться	
		this.SaveState();	

		var v = this.e.value;

		v = v.replace(/\[cut\]/gm,'\uEF00');
		v = v.replace(/\[xcut\]/gm,'\uEF01');

		v=v.replace(/([\u00A0\u2009\u2011-\u2014\u2212])/gm,function(str){return '<span class="u'+str.charCodeAt()+'">'+str+'</span>';});

		// в размер textarea
		$(this.rid+"-previewbox").height($(this.rid).height());
		$(this.rid+"-previewbox").html('<div class="post"><div id ="digraph-contentEditable" contentEditable="true" class="entry">'+v+'</div></div>');
		
		// привести в порядок ссылки
		// this.externalLink();	// не делаем, иначе к нам _black вернётся
		
		$(this.rid).hide();
		$(this.rid+"-previewbox").show();
		}
	else	{
		this.FcontentEditable = false;

		v = $("#digraph-contentEditable").html();
		//v=v.replace(/([\u00A0\u2009\u2011-\u2014\u2212])/gm,function(str){return '<span class="u'+str.charCodeAt()+'">'+str+'</span>';});	
		v=v.replace(/<span class="u(160|8201|8209|8210|8211|8212|8722)">([^<]+)<\/span>/gm,function(str,s1,s2)
			{
			if (s2 == '&nbsp;')
				{
				return '\u00A0';
				}
			else	{
				return s2;
				}
			});	

		v = v.replace(/\uEF00/gm,'[cut]');
		v = v.replace(/\uEF01/gm,'[xcut]');

		this.e.value = v;
		
		$(this.rid+"-previewbox").hide();
		$(this.rid).show();

		this.modalButtons(b, false);
		$(b).removeClass("ui-state-active");	

		var _self=this; window.setTimeout(function(){_self.LoadState();},10);
		}
	}

/* CHARMAP ================================================================= */

DiGraph.prototype.charMap = function(b)
	{
	this.modalButtons(b, true);
	this.SaveState(); 
	$("#digraph-charmap").overlay().load();
	}
DiGraph.prototype.previewChar = function(e)
	{
	var code = e.id.substr(1);
	$("#digraph-preview-char").html('&#'+ code +';');
	$("#digraph-preview-code").html('&amp;#'+ code +';');
	}

DiGraph.prototype.insertChar = function(i)
	{
	var id = this.id+'-character';

	DiGraphRunTime[id].tag = String.fromCharCode(i.substr(1));
	var b={'id':id}
	this.addText(b);
	}

DiGraph.prototype.renderCharMapHTML = function()
	{
	var range= $("#digraph-select-range").get(0).value.split(",");

	var start= parseInt(range[0],16);
	var end= parseInt(range[1],16);
	var html=[];
		
	html.push('<table id="digraph-char-data"><tbody>');
	for (var i=start,j=0; i<=end; i++,j++)
		{
		if(j==0){html.push('<tr>')}
		html.push('<td valign="middle" class="char ui-corner-all" id="c'+i+'">'+ String.fromCharCode(i) +'</td>');
		if(j==15){html.push('</tr>');j=-1}
		}
	html.push('</tbody></table>');

	$("#digraph-preview-char").html('');
	$("#digraph-preview-code").html('');
	$("#digraph-char-list").html(html.join(''));

	$(".char").css('font-family', $("#digraph-char-font").val());
	$("#digraph-preview-box").css('font-family', $("#digraph-char-font").val());
	}

DiGraph.prototype.mapLoad = function()
	{
	var selected=0;
	
	var select= $("#digraph-select-range").get(0);
	for(var i in char_range_list)
		{
		if(i.toLowerCase() == this.selectedLang)
			selected=select.options.length;

		select.options[select.options.length]=new Option(i, char_range_list[i]);
		}
	select.options[selected].selected=true;

	this.renderCharMapHTML();

	var _self = this;

	$(".char").live('mouseover',function()
			{
			_self.previewChar(this);
			});
	$(".char").live('click',function()
			{
			_self.insertChar(this.id);
			});
	}
DiGraph.prototype.renderCharFont = function(e)
	{
	//$(".char")
	$(".char").css('font-family',e.value);
	$("#digraph-preview-box").css('font-family',e.value);
	}

/* ISO/IEC 10646 Collections - http://www.evertype.com/standards/iso10646/ucs-collections.html */

var char_range_list={
"Основные латинские (ASCII) ":"0020,007F"
,"Дополнительные латинские":"0080,00FF"
,"Латинские расширенные-A ":"0100,017F"
,"Латинские расширенные-B":"0180,024F"
,"Расширения МФА":"0250,02AF"
,"Символы пробелов":"02B0,02FF"

,"Комбинирующие диакритические знаки":"0300,036F"
,"Греческие и коптские":"0370,03FF"
,"Кириллические":"0400,04FF"
,"Кириллические дополнительные":"0500,052F"
,"Армянские":"0530,058F"
,"Еврейские":"0590,05FF"
,"Арабские":"0600,06FF"
,"Сирийские":"0700,074F"

,"Thaana":"0780,07BF"
,"Деванагари":"0900,097F"

,"Гурмукхи":"0A00,0A7F"
,"Гуджарати":"0A80,0AFF"

,"Тамильские":"0B80,0BFF"
,"Телугу":"0C00,0C7F"
,"Каннада":"0C80,0CFF"

,"Тайские":"0E00,0E7F"

,"Грузинские":"10A0,10FF"

,"Латинские расширенные дополнительные":"1E00,1EFF"
,"Греческие расширенные":"1F00,1FFF"
,"Общая пунктуация":"2000,206F"
,"Степени и индексы":"2070,209F"
,"Символы валют":"20A0,20CF"
,"Буквоподобные символы":"2100,214F"
,"Числовые формы":"2150,218F"

,"Стрелки":"2190,21FF"
,"Математические операторы":"2200,22FF"
,"Разные технические":"2300,23FF"
,"Control Pictures":"2400,243F"

,"Псевдографика":"2500,257F"
,"Элементы блоков":"2580,259F"
,"Геометрические фигуры":"25A0,25FF"

,"Разные символы":"2600,26FF"
,"Osmanya":"10480,104AF"
,"Кхароштхи":"10A00,10A5F"
};



// end DiGraph

/*
 * jqDnR - Minimalistic Drag'n'Resize for jQuery.
 *
 * Copyright (c) 2007 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 2007.08.19 +r2
 */

(function($){
$.fn.jqDrag=function(h){return i(this,h,'d');};
$.fn.jqResize=function(h){return i(this,h,'r');};
$.jqDnR={dnr:{},e:0,
drag:function(v){
 if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
 else E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
  return false;},
stop:function(){E.css('opacity',M.o);$().unbind('mousemove',J.drag).unbind('mouseup',J.stop);}
};
var J=$.jqDnR,M=J.dnr,E=J.e,
i=function(e,h,k){return e.each(function(){h=(h)?$(h,e):e;
 h.bind('mousedown',{e:e,k:k},function(v){var d=v.data,p={};E=d.e;
 // attempt utilization of dimensions plugin to fix IE issues
 if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
 M={X:p.left||f('left')||0,Y:p.top||f('top')||0,W:f('width')||E[0].scrollWidth||0,H:f('height')||E[0].scrollHeight||0,pX:v.pageX,pY:v.pageY,k:d.k,o:E.css('opacity')};
 E.css({opacity:0.8});$().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
 return false;
 });
});},
f=function(k){return parseInt(E.css(k))||false;};
})(jQuery);

/*
 * jquery.tools 1.0.2 - The missing UI library
 * 
 * [tools.tabs-1.0.1, tools.tooltip-1.0.2, tools.overlay-1.0.4, tools.expose-1.0.3]
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 * 
 * -----
 * 
 * Build: Fri Jun 12 22:32:02 GMT+00:00 2009
 */
(function(c){c.tools=c.tools||{version:{}};c.tools.version.tabs="1.0.1";c.tools.addTabEffect=function(d,e){b[d]=e};var b={"default":function(d){this.getPanes().hide().eq(d).show()},fade:function(d){this.getPanes().hide().eq(d).fadeIn(this.getConf().fadeInSpeed)},slide:function(d){this.getCurrentPane().slideUp("fast");this.getPanes().eq(d).slideDown()},horizontal:function(d){if(!c._hW){c._hW=this.getPanes().eq(0).width()}this.getCurrentPane().animate({width:0},function(){c(this).hide()});this.getPanes().eq(d).animate({width:c._hW},function(){c(this).show()})}};function a(e,f,g){var d=this;var h;function i(j,k){c(d).bind(j,function(m,l){if(k&&k.call(this,l.index)===false&&l){l.proceed=false}});return d}c.each(g,function(j,k){if(c.isFunction(k)){i(j,k)}});c.extend(this,{click:function(k){if(k===h){return d}var m=d.getCurrentPane();var l=e.eq(k);if(typeof k=="string"){l=e.filter("[href="+k+"]");k=e.index(l)}if(!l.length){if(h>=0){return d}k=g.initialIndex;l=e.eq(k)}var j={index:k,proceed:true};c(d).triggerHandler("onBeforeClick",j);if(!j.proceed){return d}l.addClass(g.current);b[g.effect].call(d,k);c(d).triggerHandler("onClick",j);e.removeClass(g.current);l.addClass(g.current);h=k;return d},getConf:function(){return g},getTabs:function(){return e},getPanes:function(){return f},getCurrentPane:function(){return f.eq(h)},getCurrentTab:function(){return e.eq(h)},getIndex:function(){return h},next:function(){return d.click(h+1)},prev:function(){return d.click(h-1)},onBeforeClick:function(j){return i("onBeforeClick",j)},onClick:function(j){return i("onClick",j)}});e.each(function(j){c(this).bind(g.event,function(k){d.click(j);if(!g.history){return k.preventDefault()}})});if(g.history){e.history(function(j,k){d.click(k||0)})}if(location.hash){d.click(location.hash)}else{d.click(g.initialIndex)}f.find("a[href^=#]").click(function(){d.click(c(this).attr("href"))})}c.fn.tabs=function(g,d){var e=this.eq(typeof conf=="number"?conf:0).data("tabs");if(e){return e}var f={tabs:"a",current:"current",onBeforeClick:null,onClick:null,effect:"default",history:false,initialIndex:0,event:"click",api:false};if(c.isFunction(d)){d={onBeforeClick:d}}c.extend(f,d);this.each(function(){var h=c(this).find(f.tabs);if(!h.length){h=c(this).children()}var i=g.jquery?g:c(g);e=new a(h,i,f);c(this).data("tabs",e)});return f.api?e:this}})(jQuery);(function(b){var c,a;b.prototype.history=function(e){var d=this;if(b.browser.msie){if(!a){a=b("<iframe />").hide().get(0);b("body").append(a);setInterval(function(){var f=a.contentWindow.document;var g=f.location.hash;if(c!==g){b.event.trigger("hash",g);c=g}},100)}d.bind("click.hash",function(g){var f=a.contentWindow.document;f.open().close();f.location.hash=b(this).attr("href")});d.eq(0).triggerHandler("click.hash")}else{setInterval(function(){var f=location.hash;if(d.filter("[href*="+f+"]").length&&f!==c){c=f;b.event.trigger("hash",f)}},100)}b(window).bind("hash",e);return this}})(jQuery);
(function(c){c.tools=c.tools||{version:{}};c.tools.version.tooltip="1.0.2";var b={toggle:[function(){this.getTip().show()},function(){this.getTip().hide()}],fade:[function(){this.getTip().fadeIn(this.getConf().fadeInSpeed)},function(){this.getTip().fadeOut(this.getConf().fadeOutSpeed)}]};c.tools.addTipEffect=function(d,f,e){b[d]=[f,e]};c.tools.addTipEffect("slideup",function(){var d=this.getConf();var e=d.slideOffset||10;this.getTip().css({opacity:0}).animate({top:"-="+e,opacity:d.opacity},d.slideInSpeed||200).show()},function(){var d=this.getConf();var e=d.slideOffset||10;this.getTip().animate({top:"-="+e,opacity:0},d.slideOutSpeed||200,function(){c(this).hide().animate({top:"+="+(e*2)},0)})});function a(f,e){var d=this;var h=f.next();if(e.tip){if(e.tip.indexOf("#")!=-1){h=c(e.tip)}else{h=f.nextAll(e.tip).eq(0);if(!h.length){h=f.parent().nextAll(e.tip).eq(0)}}}function j(k,l){c(d).bind(k,function(n,m){if(l&&l.call(this)===false&&m){m.proceed=false}});return d}c.each(e,function(k,l){if(c.isFunction(l)){j(k,l)}});var g=f.is("input, textarea");f.bind(g?"focus":"mouseover",function(k){k.target=this;d.show(k);h.hover(function(){d.show()},function(){d.hide()})});f.bind(g?"blur":"mouseout",function(){d.hide()});h.css("opacity",e.opacity);var i=0;c.extend(d,{show:function(q){if(q){f=c(q.target)}clearTimeout(i);if(h.is(":animated")||h.is(":visible")){return d}var o={proceed:true};c(d).trigger("onBeforeShow",o);if(!o.proceed){return d}var n=f.position().top-h.outerHeight();var k=h.outerHeight()+f.outerHeight();var r=e.position[0];if(r=="center"){n+=k/2}if(r=="bottom"){n+=k}var l=f.outerWidth()+h.outerWidth();var m=f.position().left+f.outerWidth();r=e.position[1];if(r=="center"){m-=l/2}if(r=="left"){m-=l}n+=e.offset[0];m+=e.offset[1];h.css({position:"absolute",top:n,left:m});b[e.effect][0].call(d);c(d).trigger("onShow");return d},hide:function(){clearTimeout(i);i=setTimeout(function(){if(!h.is(":visible")){return d}var k={proceed:true};c(d).trigger("onBeforeHide",k);if(!k.proceed){return d}b[e.effect][1].call(d);c(d).trigger("onHide")},e.delay||1);return d},isShown:function(){return h.is(":visible, :animated")},getConf:function(){return e},getTip:function(){return h},getTrigger:function(){return f},onBeforeShow:function(k){return j("onBeforeShow",k)},onShow:function(k){return j("onShow",k)},onBeforeHide:function(k){return j("onBeforeHide",k)},onHide:function(k){return j("onHide",k)}})}c.prototype.tooltip=function(d){var e=this.eq(typeof d=="number"?d:0).data("tooltip");if(e){return e}var f={tip:null,effect:"slideup",delay:30,opacity:1,position:["top","center"],offset:[0,0],api:false};if(c.isFunction(d)){d={onBeforeShow:d}}c.extend(f,d);this.each(function(){e=new a(c(this),f);c(this).data("tooltip",e)});return f.api?e:this}})(jQuery);
(function(b){b.tools=b.tools||{version:{}};b.tools.version.overlay="1.0.4";var c=[];function a(h,d){var r=this,q=b(window),f,n,s,i,k,m,l;var e=d.expose&&b.tools.version.expose;function p(o,t){b(r).bind(o,function(v,u){if(t&&t.call(this)===false&&u){u.proceed=false}});return r}b.each(d,function(o,t){if(b.isFunction(t)){p(o,t)}});var j=d.target||h.attr("rel");var g=j?b(j):null;if(!g){g=h}else{k=h}q.load(function(){m=g.attr("overlay");if(!m){m=g.css("backgroundImage");if(!m){throw"background-image CSS property not set for overlay element: "+j}m=m.substring(m.indexOf("(")+1,m.indexOf(")")).replace(/\"/g,"");g.css("backgroundImage","none");g.attr("overlay",m)}s=g.outerWidth({margin:true});i=g.outerHeight({margin:true});n=b('<img src="'+m+'"/>');n.css({border:0,position:"absolute",display:"none"}).width(s).attr("overlay",true);b("body").append(n);if(k){k.bind("click.overlay",function(o){r.load(o.pageY-q.scrollTop(),o.pageX-q.scrollLeft());return o.preventDefault()})}d.close=d.close||".close";if(!g.find(d.close).length){g.prepend('<div class="close"></div>')}f=g.find(d.close);f.bind("click.overlay",function(){r.close()});if(d.preload){setTimeout(function(){var o=new Image();o.src=m},2000)}});b.extend(r,{load:function(w,v){if(!n){q.load(function(){r.load(w,v)});return r}if(r.isOpened()){return r}if(d.oneInstance){b.each(c,function(){this.close()})}var u={proceed:true};b(r).trigger("onBeforeLoad",u);if(!u.proceed){return r}if(e){n.expose(d.expose);l=n.expose().load()}w=w||d.start.top;v=v||d.start.left;var o=d.finish.top;var t=d.finish.left;if(o=="center"){o=Math.max((q.height()-i)/2,0)}if(t=="center"){t=Math.max((q.width()-s)/2,0)}if(!d.start.absolute){w+=q.scrollTop();v+=q.scrollLeft()}if(!d.finish.absolute){o+=q.scrollTop();t+=q.scrollLeft()}n.css({top:w,left:v,width:d.start.width,zIndex:d.zIndex}).show();n.animate({top:o,left:t,width:s},d.speed,function(){g.css({position:"absolute",top:o,left:t});var x=n.css("zIndex");f.add(g).css("zIndex",++x);g.fadeIn(d.fadeInSpeed,function(){b(r).trigger("onLoad")})});return r},close:function(){if(!r.isOpened()){return r}var u={proceed:true};b(r).trigger("onBeforeClose",u);if(!u.proceed){return r}if(l){l.close()}if(n.is(":visible")){g.hide();var t=d.start.top;var o=d.start.left;if(k){u=k.offset();t=u.top+k.height()/2;o=u.left+k.width()/2}n.animate({top:t,left:o,width:0},d.closeSpeed,function(){b(r).trigger("onClose",u)})}return r},getBackgroundImage:function(){return n},getContent:function(){return g},getTrigger:function(){return k},isOpened:function(){return g.is(":visible")},getConf:function(){return d},onBeforeLoad:function(o){return p("onBeforeLoad",o)},onLoad:function(o){return p("onLoad",o)},onBeforeClose:function(o){return p("onBeforeClose",o)},onClose:function(o){return p("onClose",o)}});b(document).keydown(function(o){if(o.keyCode==27){var rez = !r.isOpened(); r.close(); return rez;}});if(d.closeOnClick){b(document).bind("click.overlay",function(o){if(!g.is(":visible, :animated")){return}var t=b(o.target);if(t.attr("overlay")){return}if(t.parents("[overlay]").length){return}r.close()})}}b.fn.overlay=function(e){var f=this.eq(typeof e=="number"?e:0).data("overlay");if(f){return f}var d=b(window);var g={start:{top:Math.round(d.height()/2),left:Math.round(d.width()/2),width:0,absolute:false},finish:{top:80,left:"center",absolute:false},speed:"normal",fadeInSpeed:"fast",closeSpeed:"fast",close:null,oneInstance:true,closeOnClick:true,preload:true,zIndex:9999,api:false,expose:null,target:null};if(b.isFunction(e)){e={onBeforeLoad:e}}b.extend(true,g,e);this.each(function(){f=new a(b(this),g);c.push(f);b(this).data("overlay",f)});return g.api?f:this}})(jQuery);
(function(b){b.tools=b.tools||{version:{}};b.tools.version.expose="1.0.3";function a(){var e=b(window).width();if(b.browser.mozilla){return e}var d;if(window.innerHeight&&window.scrollMaxY){d=window.innerWidth+window.scrollMaxX}else{if(document.body.scrollHeight>document.body.offsetHeight){d=document.body.scrollWidth}else{d=document.body.offsetWidth}}return d<e?d+20:e}function c(g,h){var e=this,d=null,f=false,i=0;function j(k,l){b(e).bind(k,function(n,m){if(l&&l.call(this)===false&&m){m.proceed=false}});return e}b.each(h,function(k,l){if(b.isFunction(l)){j(k,l)}});b(window).bind("resize.expose",function(){if(d){d.css({width:a(),height:b(document).height()})}});b.extend(this,{getMask:function(){return d},getExposed:function(){return g},getConf:function(){return h},isLoaded:function(){return f},load:function(){if(f){return e}i=g.eq(0).css("zIndex");if(h.maskId){d=b("#"+h.maskId)}if(!d||!d.length){d=b("<div/>").css({position:"absolute",top:0,left:0,width:a(),height:b(document).height(),display:"none",opacity:0,zIndex:h.zIndex});if(h.maskId){d.attr("id",h.maskId)}b("body").append(d);var k=d.css("backgroundColor");if(!k||k=="transparent"||k=="rgba(0, 0, 0, 0)"){d.css("backgroundColor",h.color)}if(h.closeOnEsc){b(document).bind("keydown.unexpose",function(n){if(n.keyCode==27){e.close()}})}if(h.closeOnClick){d.bind("click.unexpose",function(){e.close()})}}var m={proceed:true};b(e).trigger("onBeforeLoad",m);if(!m.proceed){return e}b.each(g,function(){var n=b(this);if(!/relative|absolute|fixed/i.test(n.css("position"))){n.css("position","relative")}});g.css({zIndex:h.zIndex+1});var l=d.height();if(!this.isLoaded()){d.css({opacity:0,display:"block"}).fadeTo(h.loadSpeed,h.opacity,function(){if(d.height()!=l){d.css("height",l)}b(e).trigger("onLoad")})}f=true;return e},close:function(){if(!f){return e}var k={proceed:true};b(e).trigger("onBeforeClose",k);if(k.proceed===false){return e}d.fadeOut(h.closeSpeed,function(){b(e).trigger("onClose");g.css({zIndex:b.browser.msie?i:null})});f=false;return e},onBeforeLoad:function(k){return j("onBeforeLoad",k)},onLoad:function(k){return j("onLoad",k)},onBeforeClose:function(k){return j("onBeforeClose",k)},onClose:function(k){return j("onClose",k)}})}b.fn.expose=function(d){var e=this.eq(typeof d=="number"?d:0).data("expose");if(e){return e}var f={maskId:null,loadSpeed:"slow",closeSpeed:"fast",closeOnClick:true,closeOnEsc:true,zIndex:9998,opacity:0.8,color:"#456",api:false};if(typeof d=="string"){d={color:d}}b.extend(f,d);this.each(function(){e=new c(b(this),f);b(this).data("expose",e)});return f.api?e:this}})(jQuery);
