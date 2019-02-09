<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function embuttadd_autoload($args = array())
{
	mso_hook_add('head', 'embuttadd_head');
	mso_hook_add('admin_head', 'embuttadd_head');
	mso_hook_add('content', 'embuttadd_go'); # хук на вывод контента
	mso_hook_add('editor_controls_extra_css', 'embuttadd_editor_controls_extra_css');
	mso_hook_add('editor_controls_extra', 'embuttadd_editor_controls_extra');
	mso_hook_add('editor_markitup_bbcode', 'embuttadd_editor_markitup_bbcode');}

function embuttadd_head($args = array()) 
{

	echo mso_load_jquery();
	
	$url = getinfo('plugins_url') . 'embuttadd/';	


	echo <<<EOF

		<link rel="stylesheet" href="{$url}css/embuttadd.css">


EOF;


}

# функция выполняется при активации (вкл) плагина
function embuttadd_activate($args = array())
{	
	mso_create_allow('embuttadd_edit', t('Админ-доступ к настройкам псевдокода'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function embuttadd_uninstall($args = array())
{
	mso_delete_option('plugin_embuttadd', 'plugins' ); // удалим созданные опции
	mso_remove_allow('embuttadd_edit'); // удалим созданные разрешения 
	return $args;
}

# опции
function embuttadd_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_embuttadd', 'plugins',
		array(
			'replace' => array(
							'type' => 'textarea',
							'name' => t('Укажите замены через || '),
							'description' => t('Замены следует указывать через || по одной в строчке, например<br>[подзаголовок] || &lt;h2&gt;<br>[/подзаголовок] || &lt;/h2&gt;'),
							'default' => '[пробел] || &nbsp; 
[кр. строка] || <span class="redrow">кр. стр.</span>
[dwllinks] || <div class="emdwl-title"><h4>Скачать</h4></div><div class="emdwl">
[/dwllinks] || </div>'
						),

		),
		t('Настройки доп. клавиш editor_markitup'),
		t('Плагин позволяет создавать псевдокод, который используется для создания дополнительных клавиш в editor-markitup.')

	);
				
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function embuttadd_go($text) 
{
	
	$options = mso_get_option('plugin_embuttadd', 'plugins', array());
	if (!isset($options['replace']))
	{		
		$embuttadds = array(
			'[пробел]' => '&nbsp', 
			'[кр. строка]' => '<span class="redrow">кр. стр.</span>',
			'[dwllinks]' => '<div class="emdwl-title"><h4>Скачать</h4></div><div class="emdwl">',
			'[/dwllinks]' => '</div>');
	}
	else
	{
		$embuttadds_all = explode("\n", $options['replace']); // строки в массив
		
		$embuttadds = array();
		
		foreach($embuttadds_all as $line) // проходим каждую строчку
		{
			if (trim($line))
			{
				$kv = explode('||', $line); // строку, разделенную || в массив
				
				if (count($kv) > 1) // должно быть два элемента
				{
					$embuttadds[trim($kv[0])] = trim($kv[1]);
				}
			}
		}
	}
	
	$text = strtr($text, $embuttadds);
	return $text;
}
# интеграция в editor_markitup
function embuttadd_editor_markitup_bbcode($args = array()){


	echo <<<EOF
			{separator:'---------------' },	
		
			{name:'Дополнительные клавиши', openWith:'', className:"emadd", dropMenu: [
				{name:'пробел', openWith:'[пробел]', className:""},
				{name:'красная строка', openWith:'[кр. строка]', className:""},
				{name:'Для ссылок на скачивание', openWith:'[dwllinks]', closeWith:'[/dwllinks]', className:""},
		]},
EOF;





	return $args;
}

# end file
