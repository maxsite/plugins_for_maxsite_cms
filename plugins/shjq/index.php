<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# функция автоподключения плагина
function shjq_autoload()
{
    mso_hook_add( 'head', 'shjq_head');
    $options = mso_get_option('plugin_shjq', 'plugins', array());
    if (isset($options['default_lang']) and $options['default_lang']) mso_hook_add('content', 'shjq_content');
}


# функция выполняется при деинсталяции плагина
function shjq_uninstall($args = array())
{
    mso_delete_option('plugin_shjq', 'plugins' ); // удалим созданные опции
    return $args;
}
# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function shjq_mso_options()
{

    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_shjq', 'plugins',
        array(
            'default_lang' => array(
                'type' => 'select',
                'name' => t('Язык программирования по-умолчанию'),
                'description' => t('Выберите язык, который будет применяться к &lt;pre&gt; и [pre] без указанного class.'),
                'values' => 'Нет # JS # PHP # CSS # HTML',
                'default' => 'PHP'
            ),
            'indent' => array(
                'type' => 'select',
                'name' => t('Отступы'),
                'description' => '',
                'values' => 'Табуляция # Пробелы',
                'default' => 'Табуляция'
            ),
            'source' => array(
                'type' => 'checkbox',
                'name' => t('Показывать вкладку "Исходный код"'),
                'description' => '',
                'default' => TRUE
            ),
            'list' => array(
                'type' => 'checkbox',
                'name' => t('Нумерация строк кода'),
                'description' => '',
                'default' => TRUE
            ),


        ),
        'Настройки плагина SHJQ - jQuery Syntax Highlighting', // титул
        '
	Плагин делает код более привлекательным и наглядным. Для использования следует указать его в виде: </p>
<pre>
	&lt;pre class="sh_php"&gt; тут PHP-код &lt;/pre&gt;
	&lt;pre class="sh_css"&gt; тут CSS-код &lt;/pre&gt;
	&lt;pre class="sh_html"&gt; тут HTML-код &lt;/pre&gt;
	&lt;pre class="sh_javascript"&gt; тут JavaScript-код &lt;/pre&gt;
</pre>
	<br>
	<p class="info">Если у вас включён плагин <strong>BBCode</strong>, то можно использовать так:</p>
<pre>
	[pre class="sh_php"] тут PHP-код [/pre]
	[pre class="sh_css"] тут CSS-код [/pre]
	[pre class="sh_html"] тут HTML-код [/pre]
	[pre class="sh_javascript"] тут JavaScript-код [/pre]
</pre>
	<br>
	<p class="info">Если указать язык по-умолчанию, то можно не указывать class:</p>
<pre>
	&lt;pre&gt; тут код &lt;/pre&gt;
	[pre] тут код [/pre]
</pre><br>

        '
    );



}

# подключение плагина в head
function shjq_head($arg = array())
{
    $options = mso_get_option('plugin_shjq', 'plugins', array());

    $source = (isset($options['source'])) ? $options['source'] : 1;
    $indent = (isset($options['indent'])) ? $options['indent'] : 'tabs';
    $list   = (!isset($options['list']))   ? 'ol' : ($options['list']) ? 'ol' : 'ul';


    echo '
	<script src="' . getinfo('plugins_url') . 'shjq/highlight.js"></script>
	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'shjq/highlight.css">
	<script>
	$(document).ready(function(){
	        $("pre").addClass("code");
			$("pre").highlight({source:' . $source . ', zebra:1, indent:"' . $indent . '", list:"' . $list . '"});
		});
    </script>
	';

    return $arg;
}

# замены pre в тексте
function shjq_content($text = '')
{
    $options = mso_get_option('plugin_shjq', 'plugins', array());
    if (!isset($options['default_lang']) or !$options['default_lang'])
    {
        return $text;
    }
    else
    {
        $text = str_replace('<pre>', '<pre class="' . $options['default_lang'] . '">', $text);
        $text = str_replace('[pre]', '[pre class="' . $options['default_lang'] . '"]', $text);

        # замены для совместимости с syntaxhighlighter
        $text = str_replace('[pre lang=php]', '[pre class="sh_php"]', $text);
        $text = str_replace('<pre lang=php>', '<pre class="sh_php">', $text);

        $text = str_replace('[pre lang=css]', '[pre class="sh_css"]', $text);
        $text = str_replace('<pre lang=css>', '<pre class="sh_css">', $text);

        $text = str_replace('[pre lang=js]', '[pre class="sh_javascript"]', $text);
        $text = str_replace('<pre lang=js>', '<pre class="sh_javascript">', $text);

        $text = str_replace('[pre lang=javascript]', '[pre class="sh_javascript"]', $text);
        $text = str_replace('<pre lang=javascript>', '<pre class="sh_javascript">', $text);

        $text = str_replace('[pre lang=html]', '[pre class="sh_html"]', $text);
        $text = str_replace('<pre lang=html>', '<pre class="sh_html">', $text);


        return $text;
    }

}

# end file