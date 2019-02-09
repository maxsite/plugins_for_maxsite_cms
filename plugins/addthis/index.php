<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function addthis_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_addthis', 'plugins', array());
	
		if (!isset($options['priory'])) 
			$options['priory'] = 10;
		mso_hook_add('content_end', 'addthis_content_end', $options['priory']);
	}
}

function addthis_uninstall($args = array())
{	
	mso_delete_option('plugin_addthis', 'plugins');
	return $args;
}

function addthis_mso_options() 
{
	mso_admin_plugin_options('plugin_addthis', 'plugins', 
		array(
			'size' => array(
							'type' => 'radio', 
							'name' => 'Размер иконок', 
							'description' => '',
							'values' => '0||Кнопки рамером 16х16 # 1||Кнопки рамером 32х32', 
							'default' => '0',
							'delimer' => '&nbsp;&nbsp;&nbsp;&nbsp;',
						),	
			'services' => array(
							'type' => 'text', 
							'name' => 'Всегда показывать иконки', 
							'description' => 'Укажите список <a href="//addthis.com/services">сервис-кодов</a> через запятую, котрые нужно всегда показывать.', 
							'default' => ''
						),					
			'preferred' => array(
							'type' => 'select', 
							'name' => 'Количество дополнительных иконок', 
							'description' => 'Укажите количество видимых иконок. По умолчанию 4.', 
							'values' => '0 # 1 # 2 # 3 # 4 # 5 # 6 # 7 # 8 # 9 # 10', 
							'default' => '4'
						),					
			'more' => array(
							'type' => 'checkbox', 
							'name' => 'Показывать иконку меню AddThis', 
							'description' => '', 
							'default' => '1'
						),
			'counter' => array(
							'type' => 'checkbox', 
							'name' => 'Показывать счетчик добавлений', 
							'description' => '', 
							'default' => '1'
						),
			'profile' => array(
							'type' => 'text', 
							'name' => 'Индентификатор профиля AddThis', 
							'description' => 'Укажите свой ID профиля  AddThis, посмотреть его можно на <a href="https://www.addthis.com/settings/publisher">Вашей странице</a>.', 
							'default' => 'xa-4de61c782ff7002f'
						),					
			'priory' => array(
							'type' => 'text', 
							'name' => 'Приоритет блока', 
							'description' => 'Располагает блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию 10.', 
							'default' => '10'
						),					
			),
		'Сервис закладок AddThis',
		'Укажите необходимые внешний вид и основные сервисы для вывода в блог.'
	);
}

function addthis_content_end($args = array())
{
	global $page;
	
	$options = mso_get_option('plugin_addthis', 'plugins', array());
	if (!isset($options['size'])) $options['size'] = '';
	if (!isset($options['profile'])) $options['profile'] = 'xa-4de61c782ff7002f';
	if (!isset($options['priory'])) $options['priory'] = '10';

	echo NR . '<!-- AddThis Button BEGIN -->';

	$size = ($options['size'])?(' addthis_32x32_style'):('');
	echo '<div class="addthis_toolbox addthis_default_style' . $size . '">';

	if (isset($options['services']) )
	{
		$services = explode(',', $options['services']);
			foreach ($services as $service)
			{
				$service = trim($service);
				if ($service == 'more')
					echo '<a class="addthis_button_compact"></a>';
				else
					echo '<a class="addthis_button_'.strtolower($service).'"></a>';
			}
	}

	if (isset($options['preferred']) && is_numeric($options['preferred']))
	{
		for ($a = 1; $a <= $options['preferred']; $a++)
			echo '<a class="addthis_button_preferred_'.$a.'"></a>';
	}

	if (isset($options['more']) && $options['more'] == true)
		echo '<a class="addthis_button_compact"></a>';

	if (isset($options['counter']) && $options['counter'] == true)
		echo '<a class="addthis_counter addthis_bubble_style"></a>';

	echo '</div>';
	echo '<script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>';
	echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=' . $options['profile'] . '"></script>';
	echo '<!-- AddThis Button END -->' . NR;
	return $args;
}
