<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Lighter
 * Author: (c) Bugo
 * Plugin URL: http://dragomano.ru/page/maxsite-cms-plugins
 */

function lighter_autoload()
{
	mso_hook_add('head', 'lighter_head', 0);
	mso_hook_add('head', 'lighter_noconflict', 100);
	$options = mso_get_option('plugin_lighter', 'plugins', array());
	if (isset($options['default_lang']) and $options['default_lang']) mso_hook_add('content', 'lighter_content');
}

function lighter_uninstall($args = array())
{	
	mso_delete_option('plugin_lighter', 'plugins');
	return $args;
}

function lighter_scan_files($cat = 'css')
{
	$CI = & get_instance();
	$CI->load->helper('directory'); 

	$path = getinfo('plugins_dir') . '/lighter/' . $cat;
	$files = directory_map($path, true);
	
	if (!$files) return '';
	
	$all_files = array();
	
	foreach ($files as $file)
	{
		if (@is_dir($path . $file)) continue;
		$file = str_replace('.css', '', $file);
		$file = str_replace('Flame.', '', $file);
		$all_files[] = $file;
	}
	
	sort($all_files);
	
	return implode($all_files, '#');
}

function lighter_mso_options()
{
	$all_css = lighter_scan_files('css');
	$all_lang = '||' . t('Нет', __FILE__) . ' #css#html#js#md#php#ruby#shell#sql';
	$all_highlight = '||' . t('Нет', __FILE__) . ' #hover||' . t('Только при наведении указателя мыши', __FILE__) . ' #odd||' . t('Только нечётных строк', __FILE__);

	mso_admin_plugin_options('plugin_lighter', 'plugins', 
		array(
			'css' => array(
				'type' => 'select', 
				'name' => t('Стиль оформления', __FILE__), 
				'description' => t('Выберите схему подсветки кода.', __FILE__), 
				'values' => $all_css,
				'default' => 'standard'
			),
			'default_lang' => array(
				'type' => 'select', 
				'name' => t('Язык программирования по умолчанию', __FILE__), 
				'description' => t('Выберите язык, который будет применяться к &lt;pre&gt; и [pre] без указанного class (или lang).', __FILE__), 
				'values' => $all_lang,
				'default' => 'php'
			),	
            'alt_lines' => array(
				'type' => 'select', 
				'name' => t('Подсветка отдельных строк', __FILE__), 
				'description' => '', 
				'values' => $all_highlight,
				'default' => '1'
			),
		),
		'Настройки плагина Lighter',
		'
	Плагин делает код более привлекательным и наглядным. Для использования следует указать его в виде: </p>
<pre>
	&lt;pre class="php"&gt; тут PHP-код &lt;/pre&gt;
	&lt;pre class="css"&gt; тут CSS-код &lt;/pre&gt;
	&lt;pre class="html"&gt; тут HTML-код &lt;/pre&gt;
	&lt;pre class="js"&gt; тут JavaScript-код &lt;/pre&gt;
	&lt;pre class="sql"&gt; тут SQL-код &lt;/pre&gt;
	&lt;pre class="ruby"&gt; тут Ruby-код &lt;/pre&gt;
	&lt;pre class="shell"&gt; тут Shell-код &lt;/pre&gt;
	&lt;pre class="md"&gt; тут Markdown-код &lt;/pre&gt;
</pre>
	<br>
	<p class="info">Если у вас включён плагин <strong>BBCode</strong>, то теги можно указывать так:</p>
<pre>
	[pre class="php"] тут PHP-код [/pre]
</pre>
	<br>
	<p class="info">Если указать язык по умолчанию, то можно не указывать язык:</p>
<pre>
	&lt;pre&gt; тут код &lt;/pre&gt;
	[pre] тут код [/pre]
	[code] и тут код [/code]
</pre>
	<br>
	<p class="info">Кроме того, реализована совместимость с плагином Syntax Highlighter. Если раньше вы пользовались им, то привыкли указывать код так:</p>
<pre>
	&lt;pre lang="язык"&gt; тут код &lt;/pre&gt;
	[pre lang="язык"] тут код [/pre]
</pre><br>'
	);

}

# выводим сразу после подключения jQuery, для урегулирования конфликтов с Mootools
function lighter_noconflict($arg = array())
{
	echo '<script type="text/javascript">jQuery.noConflict();</script>';
	
	return $arg;
}

function lighter_head($arg = array())
{
	$options = mso_get_option('plugin_lighter', 'plugins', array());
	if (!isset($options['css']) or !$options['css']) $options['css'] = 'standard';
	if (!array_key_exists('alt_lines', $options)) $options['alt_lines'] = '';

	echo '
	<style type="text/css">
		.a_copy {
			background:url("' . getinfo('plugins_url') . 'lighter/img/clipboard.png") no-repeat #eee;
			border:1px solid #ccc;
			display:block;
			float:right;
			height:26px;
			text-decoration:none;
			text-indent:-9999px;
			width:26px;
		}
	</style>
	<script type="text/javascript">!window.addEvent && document.write(unescape(\'%3Cscript src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools-yui-compressed.js"%3E%3C/script%3E\'))</script>
	<script type="text/javascript" src="' . getinfo('plugins_url') . 'lighter/Lighter.js"></script>
	<script type="text/javascript">
		ZeroClipboard.setMoviePath(\'' . getinfo('plugins_url') . 'lighter/ZeroClipboard.swf\');
		String.implement({
			sourcify: function() {
				return this.trim().replace(/&lt;/g,\'<\').replace(/&gt;/g,\'>\').replace(/&amp;/g,\'&\');
			}
		});
		window.addEvent(\'domready\',function(){
			var pres = $$(\'pre\');
			pres.light({
				altLines: \'' . $options['alt_lines'] . '\',
				mode: \'ol\',
				indent: 4,
				flame: \'' . $options['css'] . '\',
				path: \'' . getinfo('plugins_url') . 'lighter/css/\'
			});
			(function() {
				$$(\'ol.' . $options['css'] . 'Lighter\').each(function(pre, j) {
					var a_copy_id = \'copy\' + j;
					new Element(\'a\',{
					href: \'#\',
					title: \'' . t('Копировать', __FILE__) . '\',
					\'class\': \'a_copy\',
					opacity: 0,
					events: {
						click: function(e) {
							e && e.stop();
						}
					},
					id: a_copy_id
				}).inject(pre,\'top\').fade(\'in\');
				var clip = new ZeroClipboard.Client();
				clip.setHandCursor(true);
				clip.addEventListener(\'onMouseDown\', function() {
					clip.setText(pres[j].get(\'html\').sourcify());
				});
				clip.addEventListener(\'onComplete\', function() {
					alert(\'' . t('Исходный код скопирован в буфер обмена', __FILE__) . '\');
				});
				clip.glue(a_copy_id);
			});
		}).delay(1000);
		});
	</script>' . NR;
	
	return $arg;
}

# замены pre в тексте
function lighter_content($text = '')
{
	$options = mso_get_option('plugin_lighter', 'plugins', array());
	if (!isset($options['default_lang']) or !$options['default_lang']) 
	{
		return $text;
	}
	else
	{
		$text = str_replace('<pre>', '<pre class="' . $options['default_lang'] . '">', $text);
		$text = str_replace('[pre]', '[pre class="' . $options['default_lang'] . '"]', $text);
		$text = str_replace('<code>', '<pre class="' . $options['default_lang'] . '">', $text);
		$text = str_replace('[code]', '[pre class="' . $options['default_lang'] . '"]', $text);
		
		// замены для совместимости с syntaxhighlighter и chili
		$text = str_replace('[pre lang=', '[pre class=', $text);
		$text = str_replace('<pre lang=', '<pre class=', $text);
		$text = str_replace('[code lang=', '[pre class=', $text);
		$text = str_replace('<code lang=', '<pre class=', $text);
		$text = str_replace('[/code]', '[/pre]', $text);
		$text = str_replace('</code>', '</pre>', $text);
		
		return $text;
	}

}

# end file