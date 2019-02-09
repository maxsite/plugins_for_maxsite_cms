<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * http://max-3000.com/
 */


# функция автоподключения плагина
function main_menu_mod_autoload()
{
	mso_create_allow('main_menu_mod_edit', t('Админ-доступ к редактированию MainMenu mod', __FILE__));
	mso_hook_add( 'main_menu', 'main_menu_mod_custom');
	mso_hook_add( 'head', 'main_menu_mod_head');
}


# функция выполняется при деинсталяции плагина
function main_menu_mod_uninstall($args = array())
{	
	mso_delete_option('plugin_main_menu_mod', 'plugins'); // удалим созданные опции
	mso_remove_allow('main_menu_mod_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function main_menu_mod_mso_options() 
{
	if ( !mso_check_allow('main_menu_mod_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_main_menu_mod', 'plugins', 
		array(
			'menu' => array(
							'type' => 'textarea', 
							'name' => 'Пункты меню', 
							'description' => 'Укажите полные адреса в меню и через | название ссылки. Каждый пункт в одной строчке.<br>Пример: http://maxsite.org/ | Блог Макса<br> Для группы меню используйте [ для открытия и ] для закрытия группы выпадающих пунктов. Например:<pre>[<br># | Медиа<br>audio | Аудио<br>video | Видео<br>photo | Фото<br>]</pre>', 
							'default' => ''
						),
			'menu_admin' => array(
							'type' => 'checkbox', 
							'name' => 'Пункт Admin', 
							'description' => 'Нужно ли добавлять пункт Admin в конце меню, если вы вошли в систему', 
							'default' => '1'
						),
			'show_arrows' => array(
							'type' => 'checkbox', 
							'name' => 'Показывать стрелки у пунктов, имеющих подпункты', 
							'description' => '', 
							'default' => '1'
						),
			),
		'Настройки плагина Main menu mod', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function main_menu_mod_head($arg = array())
{
	echo mso_load_jquery();
	
	//echo mso_load_jquery('ddsmoothmenu.js');
	
	if (file_exists(getinfo('template_dir') . 'main-menu-mod.css'))
		echo NR . '	<link rel="stylesheet" href="' . getinfo('template_url') . 'main-menu-mod.css' . '" type="text/css" media="screen">';
	elseif (file_exists(getinfo('template_dir') . 'css/main-menu-mod.css'))
		echo NR . '	<link rel="stylesheet" href="' . getinfo('template_url') . 'css/main-menu-mod.css' . '" type="text/css" media="screen">';
	else
		echo NR . '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'main_menu_mod/main-menu-mod.css' . '" type="text/css" media="screen">';
	
	// подключим JS и его настройки
	$options = mso_get_option('plugin_main_menu_mod', 'plugins', array());
	if (!isset($options['show_arrows'])) $options['show_arrows'] = true;
	
	echo NR . '	<!--[if lte IE 7]><style type="text/css">html .jqueryslidemenu{height:1%}</style><![endif]-->';	
	
	if ($options['show_arrows']) echo NR . '	<script type="text/javascript">var arrowimages={down:[\'downarrowclass\', \'' . getinfo('plugins_url') . 'main_menu_mod/down.gif\', 23], right:[\'rightarrowclass\', \'' . getinfo('plugins_url') . 'main_menu_mod/right.gif\']}</script>';	
	
	echo NR . '	<script type="text/javascript" src="' . getinfo('plugins_url') . 'main_menu_mod/jqueryslidemenu.js' . '"></script>';
	
	if ($options['show_arrows']) $ai = 'arrowimages';
	else $ai = 'false';
	echo NR . '	<script type="text/javascript">jqueryslidemenu.buildmenu("myslidemenu", ' . $ai . ')</script>' . NR;
	
}

# функции плагина
function main_menu_mod_custom($arg = array())
{

	$options = mso_get_option('plugin_main_menu_mod', 'plugins', array());
	
	if (!isset($options['menu'])) $options['menu'] = '';
	if (!isset($options['menu_admin'])) $options['menu_admin'] = true;
	
	if (!$options['menu']) return $arg;
	
	// для динамического изменения меню используем хук 
	$options['menu'] = mso_hook('main_menu_mod_custom', $options['menu']);
	
	$menu = mso_menu_build($options['menu'], 'current', (bool) $options['menu_admin']);
	
	if ($menu)
		echo '
		<div id="MainMenu">
			<div id="myslidemenu" class="jqueryslidemenu">
				<ul>
				' . $menu . '
				</ul>
			</div>
		</div>
	';
	
	return $arg;
}
?>