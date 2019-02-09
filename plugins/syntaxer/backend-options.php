<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Syntaxer» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-syntaxer
 */

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_syntaxer', 'plugins', 
		array(
			'group_1' => array(
				'type' => 'info',
				'title' => 'Основные параметры',
				'text' => '', 
			),
			'bb_script' => array(
				'type' => 'text', 
				'name' => 'ББ-код для реализации &lt;script /&gt; метода', 
				'description' => 'Название bb-кода для оформления кода с помощью тэга &lt;script&gt;. Подробнее про &lt;script /&gt; метод читайте <a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/installation.html">здесь</a>. Например, если указать <b>src</b>, то вставку python-кода можно будет осуществлять с помощью bb-кода так: <pre><b>[src=python]...[/src]</b><br> или так<br><b>[src=py collapse: true; light:true ]...[/src]</b> (с параметрами)</pre>Если поле оставить пустым, то &lt;script /&gt; метод будет недоступен через bb-код (но всё также будет доступен через html вариант, если <b><i>SyntaxHighlighter.config.useScriptTags</i></b> равен true).',
				'default' => 'src'
			),
			'css' => array(
				'type' => 'textarea', 
				'name' => 'CSS стили',
				'description' => 'Вы можете указать произвольные css-стили, которые нужны для кастомизации оформления.',
				'default' => ''
			),
			'group_2' => array(
				'type' => 'info',
				'title' => 'Настройки SyntaxHighlighter',
				'text' => '', 
			),
			'defaults' => array(
				'type' => 'textarea', 
				'name' => 'SyntaxHighlighter.defaults',
				'description' => 'Настройки SyntaxHighlighter «по-умолчанию». Подробности см. на странице «<a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/">Configuration</a>». Поддерживаются: <b>class-name</b>, <b>pad-line-numbers</b>, <b>highlight</b>, <b>title</b>, <b>smart-tabs</b>, <b>tab-size</b>, <b>gutter</b>, <b>toolbar</b>, <b>quick-code</b>, <b>collapse</b>, <b>auto-links</b>, <b>light</b>, <b>unindent</b>, <b>html-script</b>. Каждый параметр размещать на отдельной строке. Значение параметра указывать через символ «|». Например:<br><pre><b>highlight | [23, 24, 25]</b><br><b>class-name | \'darkstyle\'</b><br><b>toolbar | false</b></pre>', 
				'default' => 'toolbar | false'
			),			
			'config' => array(
				'type' => 'textarea', 
				'name' => 'SyntaxHighlighter.config',
				'description' => 'Общие настройки SyntaxHighlighter. Подробности см. на странице «<a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/">Сonfiguration</a>». Поддерживаются: <b>space</b>, <b>useScriptTags</b>, <b>bloggerMode</b>, <b>stripBrs</b>, <b>tagName</b>. Каждый параметр размещать на отдельной строке. Значение параметра указывать через символ «|». Например:<br><pre><b>space | "&amp;nbsp;"</b><br><b>bloggerMode | false</b><br><b>stripBrs | false</b></pre>', 
				'default' => 
					'useScriptTags | true'."\n".
					'tagName | "pre"'
			),			
			'cfgstrings' => array(
				'type' => 'textarea', 
				'name' => 'SyntaxHighlighter.config.strings',
				'description' => 'Настройки строк SyntaxHighlighter. Подробности см. на странице «<a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/strings.html">Strings configuration</a>». Поддерживаются: <b>aboutDialog</b>, <b>brushNotHtmlScript</b>, <b>noBrush</b>, <b>alert</b>, <b>help</b>, <b>expandSource</b>. Каждый параметр размещать на отдельной строке. Значение параметра указывать через символ «|». Например:<br><pre><b>expandSource | "развернуть код"</b><br><b>noBrush | "Невозможно найти стиль для:"</b><br><b>help | "помощь"</b></pre>', 
				'default' => ''
			),			
			'brushes' => array(
				'type' => 'textarea', 
				'name' => 'SyntaxHighlighter.brushes - типы подсвечиваемого синтаксиса',
				'description' => 'Укажите только те стили (синтаксисы) оформления, информация о которых должна передаваться автоматическому загрузчику (SyntaxHighlighter.autoloader) на вашем сайте. Подробности см. на странице «<a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/api/autoloader.html">Dynamic Brush Loading</a>». Список всех доступных стилей можно посмотреть здесь - «<a href="http://alexgorbatchev.com/SyntaxHighlighter/manual/brushes/">Bundled Brushes</a>». В поле нужно указывать алиас (или несколько алиасов, через запятую) и через символ «|» связанный с ним js-файл (который расположен в подкаталоге «<b>'.getinfo('plugins_dir').'syntaxer/scripts</b>»). Например:<br><pre><b>js,jscript,javascript | shBrushJScript.js</b><br><b>xml,xhtml,xslt,html | shBrushXml.js</b><br><b>text,plain | shBrushPlain.js</b></pre>', 
				'default' => 
					'applescript            | shBrushAppleScript.js'."\n".
					'actionscript3,as3      | shBrushAS3.js'."\n".
					'bash,shell             | shBrushBash.js'."\n".
					'coldfusion,cf          | shBrushColdFusion.js'."\n".
					'cpp,c                  | shBrushCpp.js'."\n".
					'c#,c-sharp,csharp      | shBrushCSharp.js'."\n".
					'css                    | shBrushCss.js'."\n".
					'delphi,pascal          | shBrushDelphi.js'."\n".
					'diff,patch,pas         | shBrushDiff.js'."\n".
					'erl,erlang             | shBrushErlang.js'."\n".
					'groovy                 | shBrushGroovy.js'."\n".
					'haxe,hx                | shBrushHaxe.js'."\n".
					'java                   | shBrushJava.js'."\n".
					'jfx,javafx             | shBrushJavaFX.js'."\n".
					'js,jscript,javascript  | shBrushJScript.js'."\n".
					'perl,pl                | shBrushPerl.js'."\n".
					'php                    | shBrushPhp.js'."\n".
					'text,plain             | shBrushPlain.js'."\n".
					'powershell,ps,posh     | shBrushPowerShell.js'."\n".
					'py,python              | shBrushPython.js'."\n".
					'ruby,rails,ror,rb      | shBrushRuby.js'."\n".
					'sass,scss              | shBrushSass.js'."\n".
					'scala                  | shBrushScala.js'."\n".
					'sql                    | shBrushSql.js'."\n".
					'vb,vbnet               | shBrushVb.js'."\n".
					'xml,xhtml,xslt,html    | shBrushXml.js'
			),			
		),
		'Настройки плагина «Syntaxer»', # титул
		'Задайте необходимые значения опциям настройки плагина. Cкачать последнюю версию плагина «<b>Syntaxer</b>» или сообщить о багах можно на <a href="http://vizr.ru/page/plugin-syntaxer">официальной странице плагина</a>. Узнать подробнее про «<b>SyntaxHighlighter</b>» можно <a href="http://alexgorbatchev.com/SyntaxHighlighter/">здесь</a>.'   # инфо
	);
	
	echo '<br>Разработка плагина - <a href="http://vizr.ru/">Илья Земсков</a>';
?>