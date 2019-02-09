/*
 * Copyright (c) 2009 Andrew Gromoff <andrew@gromoff.net>, http://gromoff.net/digraph
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * HTML Parser By John Resig (ejohn.org)
 * Original code by Erik Arvidsson, Mozilla Public License
 * http://erik.eae.net/simplehtmlparser/simplehtmlparser.js
 *
 * $Version: 09.11.19 alfa
 */

(function(){

	var	startTag	= /^<(\w+)((?:\s+[\w:-]+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,
		endTag	= /^<\/(\w+)[^>]*>/,
		attr		= /(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g,
		bb		= /\[(\w+)((?:\s+[\w:-]+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)\]/;
		

	/* XHTML 1.0 Strict		http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd */

	/* =================== Text Elements ==================================== */

	var special_pre	= "br,span,bdo,map";
	var special		= special_pre + ",object,img";
	var fontstyle		= "tt,i,b,big,small";
	var phrase		= "em,strong,dfn,code,q,samp,kbd,var,cite,abbr,acronym,sub,sup";
	var inline_forms	= "input,select,textarea,label,button";
	// these can occur at block or inline level 
	var misc_inline	= "ins,del,script";
	// these can only occur at block level
	var misc			= "noscript,"+misc_inline;
	var inline			= "a,"+special+','+fontstyle+','+phrase+','+inline_forms;
	// %Inline; covers inline or "text-level" elements
	var Inline			= "#PCDATA,"+','+inline+','+misc_inline;

	/* ================== Block level elements ============================== */

	var heading		= "h1,h2,h3,h4,h5,h6";
	var lists			= "ul,ol,dl";
	var blocktext		= "pre,hr,blockquote,address";
	var block			= "p,"+heading+",div,"+lists+','+blocktext+",fieldset,table";
	var Block			= block+",form,"+misc;
	// %Flow; mixes block and inline and is used for list items etc.
	var Flow			= "#PCDATA,"+block+",form,"+inline+','+misc;

	/* ================== Content models for exclusions ===================== */

	// a elements use %Inline; excluding a
	//var a_content		= "#PCDATA,"+special+','+fontstyle+','+phrase+','+inline_forms+','+misc_inline;

	// pre uses %Inline excluding big, small, sup or sup
	//var pre_content	= "#PCDATA,a,"+fontstyle+','+phrase+','+special_pre+','+misc_inline+','+inline_forms;

	// form uses %Block; excluding form
	//var form_content	= block+','+misc;

	// button uses %Flow; but excludes a, form and form controls
	//var button_content	= "#PCDATA,p,"+heading+",div,"+lists+','+blocktext+",table,"+special+','+fontstyle+','+phrase+','+misc;

	var head_misc	= "script,style,meta,link,object";

	inline		= makeMap(inline);
	Inline		= makeMap(Inline);
	Block		= makeMap(Block);
	Flow			= makeMap(Flow);
	//a_content	= makeMap(a_content);

	/* ================ Document Structure XHTML 1.0 ================================== */

	var stru = {
		html: makeMap("head,body")
		,head: makeMap("base,title,"+head_misc)
		,title: '#PCDATA'
		,base: ''
		,meta: ''
		,link: ''
		,style: '#PCDATA'
		,script: '#PCDATA'
		,noscript: Block
		,body: Block
		,div: Flow
		,p: Inline
		,h1: Inline
		,h2: Inline
		,h3: Inline
		,h4: Inline
		,h5: Inline
		,h6: Inline
		,ul: makeMap("li")
		,ol: makeMap("li")
		,li: Flow
		,dl: makeMap("dt,dd")
		,dt: Inline
		,dd: Flow
		,address: Inline
		,hr: ''
		,pre: '#PCDATA'	// (#PCDATA | a | %fontstyle; | %phrase; | %special.pre; | %misc.inline;)
		,blockquote: Block
		,ins: Flow
		,del: Flow
		,a: makeMap(special+','+fontstyle+','+phrase+','+inline_forms+','+misc_inline)	// (#PCDATA | %special; | %fontstyle; | %phrase; | %inline.forms; | %misc.inline;)
		,span: Inline
		,bdo: Inline
		,br: ''
		,em: Inline
		,strong: Inline
		,dfn: Inline
		,code: '#PCDATA'	// Inlinemix
		,samp: Inline
		,kbd: Inline
		,'var': Inline
		,cite: Inline
		,abbr: Inline
		,acronym: Inline
		,q: Inline
		,sub: Inline
		,sup: Inline
		,tt: Inline
		,i: Inline
		,b: Inline
		,big: Inline
		,small: Inline
		,object: '#PCDATA'
		,param: ''
		,img: ''
		,map: '#PCDATA'	// ((%block; | form | %misc;)+ | area+)>
		,area: ''
		,form: '#PCDATA'	// (%block; | %misc;)
		,label: Inline
		,input: ''
		,select: makeMap("optgroup,option")
		,optgroup: makeMap("option")
		,option: '#PCDATA'
		,textarea: '#PCDATA'
		,object: '#PCDATA'
		,fieldset: '#PCDATA' // (#PCDATA | legend | %block; | form | %inline; | %misc;)
		,legend: Inline
		,button: '#PCDATA'  // "(#PCDATA | p | %heading; | div | %lists; | %blocktext; | table | %special; | %fontstyle; | %phrase; | %misc;)*">
		,table: makeMap("caption,col,colgroup,thead,tfoot,tbody,tr")
		,caption: Inline
		,thead: makeMap("tr")
		,tfoot: makeMap("tr")
		,tbody: makeMap("tr")
		,colgroup: makeMap("col")
		,col: ''
		,tr: makeMap("th,td")
		,th: Flow
		,td: Flow
	}

	var empty	= [];
	var special	= [];

	for(var i in stru)
		{
		if(stru[i] === '#PCDATA')
			special[i] = true;
		if(stru[i] === '')
			empty[i] = true;
		}

	
	var replace = {
		'<nobr>':'<span style="white-space:nowrap">'
		,'<\\/nobr>':'</span>'
	};
		
	// теги, которые могут быть открыты (в HTML) и которые будут закрыты (XHTML)
	var closeSelf = makeMap("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr");

	// Attributes that have their values filled in disabled="disabled"
	//var fillAttrs = makeMap("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected");

	var tblock = makeMap("address,caption,dd,dt,h1,h2,h3,h3,h5,h6,div,li,p,td,th"); // blockquote p !!!
	var tsp = makeMap("html,head");

	//HTMLtree = {};
	ParserTags = [];
	ParserIndex = [];
	bbCode = [];	
	bbIndex = 57344; // E000

	var HTMLParser = this.HTMLParser = function(html, _self)
		{
		var exit = false;
		var v = [''];
		var vi=[];	

		var stack = [];
		
		stack.last = function()
			{
			return this[this.length-1];
			};

		// очистка
		ParserTags = [];
		ParserIndex = [];
		bbCodeIndex = 57344; // E000
		bbCode = [];	

		html = html.replace(/\r/gm,'');

		for(var i in replace)	// меняем теги
			{
			html = html.replace(new RegExp(i, "gm"),replace[i]);
			}

		html = parseBB(html); /* bbCode */

		var index, chars, match, last = html;

		while(html)
			{
			chars = true;

			// Make sure we're not in a script or style element
			if(!stack.last() || !special[stack.last()])
				{
				// Comment
				if(html.indexOf("<!--") == 0)
					{
					index = html.indexOf("-->");
	
					if(index >= 0)
						{
						v[v.length-1]+=html.substring( 0, index+3 );
						html = html.substring( index + 3 );
						chars = false;
						}
					}
				// end tag
				else	if(html.indexOf("</") == 0)
						{
						match = html.match(endTag);
	
						if ( match )
							{
							html = html.substring( match[0].length );
							match[0].replace(endTag, parseEndTag);
							chars = false;
							}
	
						}
					// start tag
					else	if(html.indexOf("<") == 0)
							{
							match = html.match(startTag);
							chars = false;
							if(match)
								{
								html = html.substring(match[0].length);
								match[0].replace(startTag, parseStartTag);

								if(exit){return}
								}
							else	{
								// конструкции похожие на теги...
								match = html.match(/(<[^>]*>)/);
								if(match==null){match=[]; match[0] = '<'}
								html = html.substring(match[0].length);

								v[v.length-1]+=match[0]; //.replace(/</gm,'&lt;').replace(/>/gm,'&gt;');
								}
							}

				if(chars)
					{
					index = html.indexOf("<");
					
					var text = index < 0 ? html : html.substring(0,index);
					html = index < 0 ? "" : html.substring(index);

					//text = parsebbCode(text);

					text.replace(/(.*)(\n+)?/gm, function(s,s1,s2)
						{
						if (!s){return '';}
//M2.push(ti);
						if(!stack.last())
							{
							if(s1.replace(/\s+/gm,''))
								{
								if (!$.trim(s1).replace(/[\uE001-\uF8FF]/gm,''))
									{v[v.length-1]+= s}
								else	{
									parseStartTag('','p');
									v[v.length-1]=s;
									}
								}
							else	{v[v.length-1]+=s}
							}
						else	{v[v.length-1]+=s}

						if (s2)
							{
							if (stack.last() && (stack.last() == 'p'))
								{
								if(v[v.length-1].replace(/\n+/gm,''))
									{parseEndTag('', stack.last())}
								else	{v[v.length-1]=''}
								}
							}

						return '';
						});
					}

				}
			/* special ... */
			else {
				var search = "</" + stack.last() + ">";
				index = html.indexOf(search);
	
				if(index >= 0)
					{
					text = html.substring(0, index);
					if(text.length)
						v[v.length-1]+=text;
												
					html = html.substring(index + search.length);
					}

				parseEndTag('', stack.last());
				}

			if ( html == last )
				{
				alert('ERROR PARSER (обнаружен незакрытый тег): '+last);	
				throw "Parse Error: " + html;
				}
			last = html;

			}	// end while
		
		parseEndTag('', stack.last());
		// Clean up any remaining tags
		parseEndTag();				

		ParserTags = v;
		ParserIndex = vi;

		/*==============================*/
		function parseStartTag(tag, tagName, rest, unary)
			{
			function checkRules()
				{
				var rules = stru[stack.last()];

				if(!rules[tagName]) //  tagName - не может здесь находится
					{

					if (Block[stack.last()] && Block[tagName]) // если блок не может содержать блок => закрываем блок
						{
						parseEndTag('', stack.last());
						return;
						}

					if (inline[tagName])	// если блок inline, а предок не может его содержать, делаем новый блок
						{
						parseStartTag('','p');	
						return;
						}

					_self.TERRORS.push('<ul><li><div><span class="error e1"><strong>Error parser: </strong></span>тег <strong>'+stack.last()+'</strong> содержит <strong>'+tagName+'</strong>, что не соответствует правилам.</div></li></ul>');
					}
				}

			if(typeof stru[tagName] === "undefined")
				{
				_self.TERRORS.push('<ul><li><div><span class="error e1"><strong>Error parser: </strong></span>тег <strong>'+tagName+'</strong> не отределен для XHTML 1.0 Strict.</div></li></ul>');
				exit = true;
				return;
				}

			if (closeSelf[ tagName ] && stack.last() == tagName ) // colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr
				{
				parseEndTag('', tagName);
				}

			if(stack.last())	// если есть parent
				{checkRules()}
			else	{
				if (inline[tagName])
					{
					parseStartTag('','p');	
					}
				}

			unary = empty[tagName] || !!unary;

			if(!unary)
				stack.push(tagName);

			// запись
			if (unary === true)
				{
				v[v.length-1]+='<'+tagName+(rest ? rest : '')+' />';
				}
			else	{
				if(Block[tagName] || special[tagName])
					{
					v.push('<'+tagName+(rest ? rest : '')+'>');
					}	
				else	{
					v[v.length-1]+='<'+tagName+(rest ? rest : '')+'>';
					}

				if(tblock[tagName])	// блоки для типографирования
					{
					vi.push(v.length);
					v.push('');
					}
				}
			}
		/*=========================*/
		function parseEndTag( tag, tagName)
			{
			// If no tag name is provided, clean shop
			if(!tagName)
				var pos = 0;
				
			// Find the closest opened tag of the same type
			else	for(var pos = stack.length-1; pos >= 0; pos--)
					if(stack[ pos ] == tagName)
						break;
			
			if ( pos >= 0 )
				{
				// Close all the open elements, up the stack
				for(var i = stack.length - 1; i >= pos; i--)
					{
					if(tblock[stack[i]])
						{
						var end = '';
						v[v.length-1] = v[v.length-1].replace(/^\s+/,'').replace(/(\s+)$/,function(s){end=s;return ''}); // TRIM
						v.push("</"+stack[i]+">"+end); // переводы строк выносим за тег
						//v.push("</"+stack[i]+">");
						}

					else	v[v.length-1]+= "</"+stack[i]+">";
					}
				
				// Remove the open elements from the stack
				stack.length = pos;
				}
			}

		function parseBB(t)
			{
			var f = 0, i = 0;

			while((i = t.indexOf("[", f)) != -1)
				{
				f = i+1;

				t.replace(bb, function(all, tagName, s1, s2, pos)
					{
					f = 0;

					var search = "[/" + tagName + "]";
					var i2 = t.indexOf(search);

					if(i2 >= 0)
						{
						bbCode[bbIndex += 1] =  t.substring(pos, i2 + search.length);
						t = t.substring(0, pos) + String.fromCharCode(bbIndex) + t.substring(i2 + search.length);
						}
					else	{
						bbCode[bbIndex += 1] = all;
						t = t.substring(0, pos) + String.fromCharCode(bbIndex) + t.substring(pos + all.length);
						}
					});
				}
			return t;
			}
		};

	function makeMap(str){
		var obj = {}, items = str.split(",");
		for ( var i = 0; i < items.length; i++ )
			obj[ items[i] ] = true;
		return obj;
	}
})();
