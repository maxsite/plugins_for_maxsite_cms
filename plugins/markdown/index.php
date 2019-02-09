<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function markdown_autoload($args = array())
{
	//mso_create_allow('markdown_edit', t('Админ-доступ к настройкам') . ' ' . t('markdown'));
	$options = mso_get_option('plugin_markdown', 'plugins', array());
	if (!array_key_exists('markdown_level', $options)) $options['markdown_level'] = 1;
	if ( ($options['markdown_level'] == 1) or ($options['markdown_level'] == 3) ) mso_hook_add( 'content', 'markdown_custom', 20); # хук на вывод контента
	if ( ($options['markdown_level'] == 2) or ($options['markdown_level'] == 3) ) mso_hook_add( 'comments_content', 'markdown_custom', 20);
	
	mso_hook_add( 'editor_content', 'markdown_editor_content'); // обработка текста для визуалього редактора
	
}

# функция выполняется при деинсталяции плагина
function markdown_uninstall($args = array())
{
	mso_delete_option('plugin_markdown', 'plugins' ); // удалим созданные опции
	return $args;
}


function markdown_mso_options()
{

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_markdown', 'plugins',
		array(
			'markdown_level' => array(
							'type' => 'select',
							'name' => t('Где использовать'),
							'description' => t('Укажите, где должен работать плагин'),
							'values' => t('1||На страницах #2||В комментариях #3||На страницах и в комментариях'),
							'default' => '1'
						),
			),
		t('Настройки плагина markdown'),
		t('Укажите необходимые опции.')
	);
}

# функции плагина
function markdown_custom($text = '')
{
  include_once  getinfo('plugins_dir') . 'markdown/markdown.php';
  $text = str_replace('<br>', "\n", $text);
  $text = Markdown($text);
  return $text;
}



# end file
