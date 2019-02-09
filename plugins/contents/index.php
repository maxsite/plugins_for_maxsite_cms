<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://max-3000.com/
 */

# функция автоподключения плагина
function contents_autoload($args = array())
{
	mso_hook_add('head', 'contents_head');
	mso_hook_add('content_content', 'contents_content'); # хук на вывод контента после обработки всех тэгов
	mso_register_widget('contents_widget', t('contents') ); # регистрируем виджет
}

function contents_uninstall($args = array())
{
    mso_delete_option('plugin_contents', 'plugins' ); # удалим созданные опции
    return $args;
}

function contents_mso_options()
{
	mso_admin_plugin_options('plugin_contents', 'plugins',
		array(
			'contents_class_page' => array(
				'type' => 'text',
				'name' => t('Класс блока на странице, содержание которого будет анализироваться для составления «содержания»'),
				'description' => t('Укажите название класса (без точки в начале)'),
				'default' => 'page_content'
			),
			'contents_class_contents' => array(
				'type' => 'text',
				'name' => t('Класс блока, в который будет записано содержимое'),
				'description' => t('Укажите название класса (без точки в начале)'),
				'default' => 'contents'
			),
			'contents_header' => array(
				'type' => 'text',
				'name' => t('Заголовок'),
				'description' => t('Укажите заголовок у блока, в котором будет размещено «содержание». Заголовок будет обёрнут в тэг параграфа <b>&lt;p></b>'),
				'default' => 'Содержание',
			)
		),
		t('Настройки плагина «Содержание»'),
		t('Укажите необходимые опции. Стили оформления можно задать через файл <b>custom.css</b> в папке плагина.')
	);
}

function contents_head($args = array()) 
{
	if( is_type('page') )
	{
		echo mso_load_jquery();
		
		$options = mso_get_option('plugin_contents', 'plugins', array());	
		if(!isset($options['contents_class_contents'])) $options['contents_class_contents'] = 'contents';
		if(!isset($options['contents_class_page'])) $options['contents_class_page'] = 'page_content';
		if(!isset($options['contents_header'])) $options['contents_header'] = t('Содержание');

		# стили пользователя
		if( file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/custom.css') )
		{
			echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/custom.css" type="text/css" media="screen">'.NR;
		}

		# скрипт
		echo "
			<script type=\"text/javascript\">
				var contents_opts = { 
					'header':'".$options['contents_header']."',
					'сlass_page':'".$options['contents_class_page']."',
					'class_contents':'".$options['contents_class_contents']."',
				}
			</script>
		";
		echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/script.js"></script>';
	}
}

function contents_content($text = '')
{
	global $page;
		
	$options = mso_get_option('plugin_contents', 'plugins', array());	
	if( !isset($options['contents_class_contents']) ) $options['contents_class_contents'] = 'contents';
		
	if( preg_match('~\[contents\]~si', $text) )
	{
		$page['contents'] = true;
	}

	$preg = array(
		'~\[contents\]~si' => '<div class="'.$options['contents_class_contents'].'">$1</div>',
	);
		
	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# функция, которая берет настройки из опций виджетов
function contents_widget( $num = 1 )
{
	$widget = 'contents_widget_' . $num; # имя для опций = виджет + номер
		
	$options = mso_get_option('plugin_contents', 'plugins', array());
	if( !isset($options['contents_class_contents']) ) $options['contents_class_contents'] = 'contents';
	if( !isset($options['contents_header']) ) $options['contents_header'] = t('Содержание');
	# заменим заголовок, чтобы был в  h2 class="box"
	if( isset($options['contents_header']) && $options['contents_header'] )
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['contents_header'] . mso_get_val('widget_header_end', '</span></h2>');
	else
		$options['header'] = '';
	
	return contents_widget_custom($options, $num);
}

# функции плагина
function contents_widget_custom($options = array(), $num = 1)
{
	global $page;
		
	if( is_type('page') && isset($page) && isset($page['contents']) )
	{
		# кэш 
		$cache_key = 'contents_widget_custom' . serialize($options) . $num;
		$k = mso_get_cache($cache_key);
		if( $k ) return $k; # да есть в кэше
		
		$out = $options['header'] . '<div class="'.$options['contents_class_contents'] . '"></div>';
		
		mso_add_cache($cache_key, $out); # сразу в кэш добавим
		
		return $out;
	}
	else
	{
		return '';
	}
}

# end file