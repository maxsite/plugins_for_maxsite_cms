<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function goo_gl_autoload()
{
	mso_hook_add('comments_content_end', 'goo_gl_custom');
	mso_hook_add('head', 'goo_gl_head');

	mso_register_widget('goo_gl_widget', t('goo.gl') ); # регистрируем виджет
}

# функция выполняется при активации (вкл) плагина
function goo_gl_activate($args = array())
{	
	mso_create_allow('goo_gl_edit', t('Админ-доступ к настройкам goo_gl'));
	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function goo_gl_deactivate($args = array())
{	
	mso_delete_option('plugin_goo_gl', 'plugins' ); // удалим созданные опции
	mso_delete_option_mask('goo_gl_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function goo_gl_uninstall($args = array())
{	
	mso_delete_option('plugin_goo_gl', 'plugins' ); // удалим созданные опции
	mso_remove_allow('goo_gl_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function goo_gl_mso_options() 
{
	if ( !mso_check_allow('goo_gl_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_goo_gl', 'plugins', 
		array(
			'API_key' => array(
							'type' => 'text', 
							'name' => t('API key'), 
							'description' => t('Здесь необходимо указать ключ проекта (https://code.google.com/apis/console/)'), 
							'default' => ''
						),
			),
		t('Настройки плагина goo_gl'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

function goo_gl_head($args = array()) 
{
	$options = mso_get_option('plugin_goo_gl', 'plugins', array() ); // получаем опции
	if ( !isset($options['API_key']) ) $options['API_key'] = '';
	$url = $options['API_key'];
	
	echo mso_load_style(getinfo('plugins_url') .'goo_gl/goo_gl.css');
	echo <<<EOF
	
	<script type="text/javascript">
	  function shorten(e) {	   

	    var url = document.getElementById('txt').value;
		if (url == '') return;
	    var xhr = new XMLHttpRequest();
	    xhr.open('POST', 'https://www.googleapis.com/urlshortener/v1/url?key={$url}', true);
	    xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.onreadystatechange = function() {			
			if(xhr.readyState != 4 || xhr.status != 200) return;
			var res = JSON.parse(xhr.responseText);
			var div = document.getElementById("shorten_info").appendChild(document.createElement("div"));
			div.className = "shorten_url";
			var s1 = document.createElement("a");
			s1.href = res.id;
			s1.appendChild(document.createTextNode(res.id));
			
			var s2 = document.createElement("span");
			s2.className = "a_url";
			s2.appendChild(document.createTextNode(res.longUrl));
			div.appendChild(s2);
			div.appendChild(document.createElement("br"));
			div.appendChild(s1);	
		}		
		xhr.send(JSON.stringify({'longUrl': url}));
	  }	  
	</script>
	
EOF;
//if (url == '') return;
//if(xhr.readyState != 4 || xhr.status != 200) return;
}

# функции плагина
function goo_gl_custom($args = array())
{
	$options = mso_get_option('plugin_goo_gl', 'plugins', array() ); // получаем опции
	if ( !isset($options['API_key']) ) $options['API_key'] = '';

	// если введен API Key
	if ($options['API_key'])
	{
		echo '<input id="txt" type="text" style="width: 85%;" placeholder="http://">
		<span class="shorten_button" onclick="shorten()">Shorten</span>
		<div id="shorten_info"></div>';
	}
	
	//return $args;
}

# функция, которая берет настройки из опций виджетов
function goo_gl_widget($num = 1) 
{
	$widget = 'goo_gl_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	if (isset($options['text_before']) ) $options['text_before'] = '<p>' . $options['text_before'] . '</p>';
	else $options['text_before'] = '';

	if (isset($options['text_after']) ) $options['text_after'] = '<p>' . $options['text_after'] . '</p>';
	else $options['text_after'] = '';
	
	return goo_gl_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function goo_gl_widget_form($num = 1) 
{
	$widget = 'goo_gl_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Goo.gl Shorten');
	if ( !isset($options['text_before']) ) $options['text_before'] = '';
	if ( !isset($options['text_after']) ) $options['text_after'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header']), t('Подсказка')));

	$form .= mso_widget_create_form(t('Текст до', __FILE__), form_textarea( array( 'name'=>$widget . 'text_before', 'value'=>$options['text_before'] )), t('Можно использовать HTML', __FILE__));

	$form .= mso_widget_create_form(t('Текст после', __FILE__), form_textarea( array( 'name'=>$widget . 'text_after', 'value'=>$options['text_after'] )), t('Можно использовать HTML', __FILE__));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function goo_gl_widget_update($num = 1) 
{
	$widget = 'goo_gl_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text_before'] = mso_widget_get_post($widget . 'text_before');
	$newoptions['text_after'] = mso_widget_get_post($widget . 'text_after');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function goo_gl_widget_custom($options = array(), $num = 1)
{
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text_before']) ) $options['text_before'] = '';
	if ( !isset($options['text_after']) ) $options['text_after'] = '';

	// берем ключ из опции админки
	$options_admin = mso_get_option('plugin_goo_gl', 'plugins', array() ); // получаем опции
	if ( !isset($options_admin['API_key']) ) $options_admin['API_key'] = '';

	$out = '';

	// если введен API Key и page
	if ($options_admin['API_key'] && !is_type_slug('page'))
	{
		// выводим заголовок
		$out .= $options['header'];

		// выводим текст до
		$out .= $options['text_before'];

		// выводим форму
		$out .= '<input id="txt" type="text" style="width: 60%;" placeholder="http://">
		<span class="shorten_button" onclick="shorten()">Shorten</span>
		<div id="shorten_info"></div>';

		// выводим текст после
		$out .= $options['text_after'];
	}

	return $out;	
}

# end file