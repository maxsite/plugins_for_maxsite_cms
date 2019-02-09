<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed'); 

/**
 * Syntax Highlighter plugin for MaxSite CMS
 * (c) Oleg Stadnik (aka lokee)
 * 			http://lokee.rv.ua
 */

function syntaxhighlighter_autoload($args = array())
{
	mso_hook_add( 'content_out', 'syntaxhighlighter_content' );
	mso_hook_add( 'head', 'syntaxhighlighter_template_head' );
}

function syntaxhighlighter_template_head( $arg = array() )
{
	$scripts_path = getinfo( 'plugins_url' ) . 'syntaxhighlighter/scripts/';
	$styles_path = getinfo( 'plugins_url' ) . 'syntaxhighlighter/styles/';
	echo '	<script type="text/javascript" src="' . $scripts_path . 'shCore.js"></script>';

	// получаем опции
	$options = mso_get_option( 'syntaxhighlighter', 'plugins', array() );
	// кисти
	$default_brushes = "shBrushPowerShell\nshBrushAS3\nshBrushPerl\nshBrushBash\nshBrushCpp\nshBrushCSharp\nshBrushCss\nshBrushDelphi\nshBrushDiff\nshBrushGroovy\nshBrushJava\nshBrushJScript\nshBrushPhp\nshBrushPlain\nshBrushPython\nshBrushRuby\nshBrushScala\nshBrushSql\nshBrushVb\nshBrushXml";
	$allbrushes = explode( "\n", $default_brushes );

	while( list( $key, $brush ) = each( $allbrushes ) ) {
		$js = "\n\t" . '<script type="text/javascript" src="' . $scripts_path . $brush . '.js"></script>';
		//$js_load = isset( $options[$brush] ) ? "suck\n" : $js; // w/o save opt
		//$js_load = empty( $options[$brush] ) ? "" : $js; // w/ save opt
		if ( !isset( $options[$brush] ) || ( isset( $options[$brush] ) && !empty( $options[$brush] ) ) ) {
			echo $js;
		}
	}
	// конфиг
	$default_shconfig = 'SyntaxHighlighter.config.clipboardSwf = "' . getinfo( 'plugins_url' ) . 'syntaxhighlighter/scripts/clipboard.swf";
SyntaxHighlighter.all();';
	$shconfig = isset( $options['shconfig'] ) ? $options['shconfig'] : $default_shconfig;
	$shconfig = !empty( $options['shconfig'] ) ? $options['shconfig'] : $default_shconfig;
	echo "\n\t<script type=\"text/javascript\">\n";
	echo "\t\t$shconfig\n";
	echo "\t</script>\n";
	// стили
	$style = isset( $options['shTheme'] ) ? $options['shTheme'] : 'shThemeDefault.css';
	//$style = !empty( $options['style'] ) ? $options['style'] : 'shThemeDefault.css';
	echo "\n\t" . '<link type="text/css" rel="stylesheet" href="' . $styles_path . 'shCore.css" />';
	echo "\n\t" . '<link type="text/css" rel="stylesheet" href="' . $styles_path . $style . '" />';
	
	return $arg;
}

function syntaxhighlighter_content_callback( $matches )
{
	if(sizeof($matches) === 2){ //Если без стиля, тогда подставляем текстовый стиль	
		$brush = 'text';
		$code = $matches[1];	
	} else{
		$brush = $matches[1];
		$code = $matches[2];
	}
	
	// hack (по-идее не нужен в MaxSite CMS 0.343+)
	$del = array( "<p>" => "", "</p>" => "", "<br />" => "", "&nbsp;" => " " );
	$code = strtr( $code, $del );
	// end hack
	$code = trim( htmlspecialchars( htmlspecialchars_decode( $code ) ) );
	
	return '<pre class="brush: ' . $brush . ';">' . $code . '</pre>';
}

function syntaxhighlighter_content( $text )
{
	// code tag
	$pattern = '|[<\[]code lang=["]*(.*?)["]*[>\]](.*?)[<\[]/code[>\]]|si';
	$text = preg_replace_callback($pattern, 'syntaxhighlighter_content_callback', $text);
	// pre tag
	$pattern = '|[<\[]pre lang=["]*(.*?)["]*[>\]](.*?)[<\[]/pre[>\]]|si';
	$text = preg_replace_callback($pattern, 'syntaxhighlighter_content_callback', $text);

	// pre tag
	$pattern = '|[<\[]pre[>\]](.*?)[<\[]/pre[>\]]|si';
	$text = preg_replace_callback($pattern, 'syntaxhighlighter_content_callback', $text);	
	
	return $text;
}

// функция выполняется при деинсталяции плагина
function syntaxhighlighter_uninstall( $args = array() )
{
	// удалим созданные опции
	mso_delete_option( 'syntaxhighlighter', 'plugins' );
	return $args;
}

// функция отрабатывающая миниопции плагина (function плагин_mso_options)
function syntaxhighlighter_mso_options() 
{
	//$shversion = '2.0.320';

	mso_cur_dir_lang(__FILE__);

	// ключ, тип, ключи массива
	mso_admin_plugin_options('syntaxhighlighter', 'plugins', 
		array(
			/*
			* выпадающий список стилей
			*/
			'shTheme' => array(
							'type' => 'select', 
							'name' => t( 'Стиль подсветки' ), 
							'description' => t( 'Какой стиль использовать для подсветки кода (<a href="http://alexgorbatchev.com/wiki/SyntaxHighlighter:Themes">подробнее</a>)' ),
							'values' => 'shThemeDefault.css||Default # shThemeDjango.css||Django # shThemeEmacs.css||Emacs # shThemeFadeToGrey.css||FadeToGrey # shThemeMidnight.css||Midnight # shThemeRDark.css||RDark # shThemeEclipse.css||Eclipse # shThemeMDUltra.css||MDUltra',
							'default' => 'shThemeDefault.css'
						),
			/*
			* чекбоксы "кистей"
			*/
			'shBrushAS3' => array(
							'type' => 'checkbox', 
							'name' => t( 'ActionScript3' ), 
							'description' => t( 'Алиасы: ' ) . 'as3, actionscript3', 
							'default' => '1'
						),
			'shBrushBash' => array(
							'type' => 'checkbox', 
							'name' => t( 'Bash/shell' ), 
							'description' => t( 'Алиасы: ' ) . 'bash, shell', 
							'default' => '1'
						),
			'shBrushCSharp' => array(
							'type' => 'checkbox', 
							'name' => t( 'C#' ), 
							'description' => t( 'Алиасы: ' ) . 'c-sharp, csharp', 
							'default' => '1'
						),
			'shBrushCpp' => array(
							'type' => 'checkbox', 
							'name' => t( 'C++' ), 
							'description' => t( 'Алиасы: ' ) . 'cpp, c', 
							'default' => '1'
						),
			'shBrushCss' => array(
							'type' => 'checkbox', 
							'name' => t( 'CSS' ), 
							'description' => t( 'Алиасы: ' ) . 'css', 
							'default' => '1'
						),
			'shBrushDelphi' => array(
							'type' => 'checkbox', 
							'name' => t( 'Delphi' ), 
							'description' => t( 'Алиасы: ' ) . 'delphi, pas, pascal', 
							'default' => '1'
						),
			'shBrushDiff' => array(
							'type' => 'checkbox', 
							'name' => t( 'Diff' ), 
							'description' => t( 'Алиасы: ' ) . 'diff, patch', 
							'default' => '1'
						),
			'shBrushGroovy' => array(
							'type' => 'checkbox', 
							'name' => t( 'Groovy' ), 
							'description' => t( 'Алиасы: ' ) . 'groovy', 
							'default' => '1'
						),
			'shBrushJScript' => array(
							'type' => 'checkbox', 
							'name' => t( 'JavaScript' ), 
							'description' => t( 'Алиасы: ' ) . 'js, jscript, javascript', 
							'default' => '1'
						),
			'shBrushJava' => array(
							'type' => 'checkbox', 
							'name' => t( 'Java' ), 
							'description' => t( 'Алиасы: ' ) . 'java', 
							'default' => '1'
						),
			'shBrushJavaFX' => array(
							'type' => 'checkbox', 
							'name' => t( 'JavaFX' ), 
							'description' => t( 'Алиасы: ' ) . 'jfx, javafx', 
							'default' => '1'
						),
			'shBrushPerl' => array(
							'type' => 'checkbox', 
							'name' => t( 'Perl' ), 
							'description' => t( 'Алиасы: ' ) . 'perl, pl', 
							'default' => '1'
						),
			'shBrushPhp' => array(
							'type' => 'checkbox', 
							'name' => t( 'PHP' ), 
							'description' => t( 'Алиасы: ' ) . 'php', 
							'default' => '1'
						),
			'shBrushPlain' => array(
							'type' => 'checkbox', 
							'name' => t( 'Plain Text' ), 
							'description' => t( 'Алиасы: ' ) . 'plain, text', 
							'default' => '1'
						),
			'shBrushPowerShell' => array(
							'type' => 'checkbox', 
							'name' => t( 'PowerShell' ), 
							'description' => t( 'Алиасы: ' ) . 'ps, powershell', 
							'default' => '1'
						),
			'shBrushPython' => array(
							'type' => 'checkbox', 
							'name' => t( 'Python' ), 
							'description' => t( 'Алиасы: ' ) . 'py, python', 
							'default' => '1'
						),
			'shBrushRuby' => array(
							'type' => 'checkbox', 
							'name' => t( 'Ruby' ), 
							'description' => t( 'Алиасы: ' ) . 'rails, ror, ruby', 
							'default' => '1'
						),
			'shBrushScala' => array(
							'type' => 'checkbox', 
							'name' => t( 'Scala' ), 
							'description' => t( 'Алиасы: ' ) . 'scala', 
							'default' => '1'
						),
			'shBrushSql' => array(
							'type' => 'checkbox', 
							'name' => t( 'SQL' ), 
							'description' => t( 'Алиасы: ' ) . 'sql', 
							'default' => '1'
						),
			'shBrushVb' => array(
							'type' => 'checkbox', 
							'name' => t( 'Visual Basic' ), 
							'description' => t( 'Алиасы: ' ) . 'vb, vbnet', 
							'default' => '1'
						),
			'shBrushXml' => array(
							'type' => 'checkbox', 
							'name' => t( 'XML' ), 
							'description' => t( 'Алиасы: ' ) . 'xml, xhtml, xslt, html, xhtml', 
							'default' => '1'
						),
			'shconfig' => array(
							'type' => 'textarea',
							'name' => t( 'Настройки' ),
							'description' => t( 'Дополнительная конфигурация (<a href="http://alexgorbatchev.com/wiki/SyntaxHighlighter:Configuration">подробнее</a>)' ),
							'default' => 'SyntaxHighlighter.config.clipboardSwf = "' . getinfo( 'plugins_url' ) . 'syntaxhighlighter/scripts/clipboard.swf";
SyntaxHighlighter.all();'
						)
			),
		// титул
		t( 'Настройки плагина SyntaxHighlighter' ),
		// инфо
		t( 'Этот плагин сделает ваш код визуально более наглядным.<br /><br /><strong>Использование:</strong><br />Заключите код в один из следующих тэгов:<ul><li>&lt;pre lang=алиас_языка&gt; &lt;/pre&gt;</li><li>&lt;code lang=алиас_языка&gt; &lt;/code&gt;</li><li>[pre lang=алиас_языка] [/pre]</li><li>[code lang=алиас_языка] [/code]</li></ul><br />' )
	);
}

?>