<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# функция автоподключения плагина
function tiny_mce_autoload($args = array())
{
	define('TINY_MCE_PLUGIN_URL',getinfo('plugins_url'));
	define('TINY_MCE_PLUGIN_DIR',getinfo('plugins_dir'));

	mso_hook_add( 'admin_init', 'tiny_mce_admin_init'); # хук на админку
	mso_hook_add( 'editor_custom', 'tiny_mce'); # хук на подключение своего редактора
	mso_hook_add( 'content_replace_chr10_br', 'tiny_mce_content_replace_chr10_br'); # хук на подключение своего редактора
}

# функция выполняется при активации (вкл) плагина
function tiny_mce_activate($args = array())
{
	$tadv_options = array( 'advlink' => 1, 'advimage' => 1, 'importcss' => 0, 'contextmenu' => 0, 'tadvreplace' => 0 );
	$tadv_toolbars = array(
		'toolbar_1' => array( 'bold', 'italic', 'strikethrough', 'underline', 'separator1', 'bullist', 'numlist', 'outdent',  'indent', 'separator2', 'justifyleft', 'justifycenter', 'justifyright', 'separator3', 'link', 'unlink', 'separator4', 'image', 'styleprops', 'separator12', 'wp_more', 'wp_page', 'separator5', 'spellchecker', 'search', 'separator6', 'fullscreen', 'wp_adv' ),
		'toolbar_2' => array( 'fontsizeselect', 'formatselect', 'pastetext', 'pasteword', 'removeformat', 'separator8', 'charmap', 'print', 'separator9', 'forecolor', 'backcolor', 'emotions', 'separator10', 'sup', 'sub', 'media', 'separator11', 'undo', 'redo', 'attribs', 'wp_help' ),
		'toolbar_3' => array(),
		'toolbar_4' => array()
	);


	mso_add_option( 'tadv_toolbars', $tadv_toolbars, 'plugins');
	mso_add_option( 'tadv_options', $tadv_options, 'plugins' );
	mso_add_option( 'tadv_plugins', $tadv_plugins, 'plugins' );
	mso_add_option( 'tadv_btns1', $tadv_btns1, 'plugins' );
	mso_add_option( 'tadv_btns2', $tadv_btns2, 'plugins' );
	mso_add_option( 'tadv_btns3', $tadv_btns3, 'plugins' );
	mso_add_option( 'tadv_btns4', $tadv_btns4, 'plugins' );
	mso_add_option( 'tadv_allbtns', $tadv_allbtns, 'plugins' );

	return $args;
}

function tiny_mce_deactivate($args = array())
{
	mso_remove_allow('tiny_mce_edit'); // старое ошибочное разрешение
	return $args;
}

# функция выполняется при деинсталяции плагина
function tiny_mce_uninstall($args = array())
{
	mso_remove_allow('plugin_tiny_mce');
	mso_delete_option_mask('tadv_', 'plugins'); // удалим созданные опции
	return $args;
}


function tiny_mce_admin_init($args = array())
{	if ( mso_check_allow('plugin_tiny_mce') )
	{
		$this_plugin_url = 'tiny_mce'; // url и hook

		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки

		mso_admin_menu_add('plugins', $this_plugin_url, t('TinyMCE', 'plugins'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
		# связанную функцию именно она будет вызываться, когда
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'tiny_mce_admin_page');
	}
	mso_hook_add( 'mce_buttons', 'tiny_mce_btns', 999 );
	mso_hook_add( 'mce_buttons_2', 'tiny_mce_btns2', 999 );
	mso_hook_add( 'mce_buttons_3', 'tiny_mce_btns3', 999 );
	mso_hook_add( 'mce_buttons_4', 'tiny_mce_btns4', 999 );
	mso_hook_add( 'mce_plugins', 'tiny_mce_plugins', 999 );
	return $args;
}

function tiny_mce_btns($orig)
{
	global $tadv_allbtns, $tadv_hidden_row;
	$tadv_btns1 = (array) mso_get_option('tadv_btns1','plugins');
	$tadv_allbtns = (array) mso_get_option('tadv_allbtns','plugins');

	if ( in_array( 'wp_adv', $tadv_btns1 ) )
		$tadv_hidden_row = 2;

	$orig=explode(',',$orig);
	if ( is_array($orig) && ! empty($orig) )
	{
		$orig = array_diff( $orig, $tadv_allbtns );
		$tadv_btns1 = array_merge( $tadv_btns1, $orig );
	}
	return implode(',',$tadv_btns1);
}

function tiny_mce_btns2($orig)
{
	global $tadv_allbtns, $tadv_hidden_row;
	$tadv_btns2 = (array) mso_get_option('tadv_btns2','plugins');

	if ( in_array( 'wp_adv', $tadv_btns2 ) )
		$tadv_hidden_row = 3;

	$orig=explode(',',$orig);
	if ( is_array($orig) && ! empty($orig) ) {
		$orig = array_diff( $orig, $tadv_allbtns );
		$tadv_btns2 = array_merge( $tadv_btns2, $orig );
	}
	return implode(',',$tadv_btns2);
}

function tiny_mce_btns3($orig)
{
	global $tadv_allbtns, $tadv_hidden_row;
	$tadv_btns3 = (array) mso_get_option('tadv_btns3','plugins');

	if ( in_array( 'wp_adv', $tadv_btns3 ) )
		$tadv_hidden_row = 4;

	$orig=explode(',',$orig);
	if ( is_array($orig) && ! empty($orig) ) {
		$orig = array_diff( $orig, $tadv_allbtns );
		$tadv_btns3 = array_merge( $tadv_btns3, $orig );
	}
	return implode(',',$tadv_btns3);
}

function tiny_mce_btns4($orig)
{
	global $tadv_allbtns;
	$tadv_btns4 = (array) mso_get_option('tadv_btns4','plugins');

	$orig=explode(',',$orig);
	if(is_array($orig) && ! empty($orig))
	{
		$orig = array_diff( $orig, $tadv_allbtns );
		$tadv_btns4 = array_merge( $tadv_btns4, $orig );
	}
	return implode(',',$tadv_btns4);
}

function tiny_mce_plugins($orig)
{
	$tadv_plugins = (array) mso_get_option('tadv_plugins','plugins');

	$orig=explode(',',$orig);
	if(is_array($orig) && ! empty($orig))
	{
		$tadv_plugins = array_merge( $tadv_plugins, $orig );
	}
	return implode(',',$tadv_plugins);
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function tiny_mce_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_tiny_mce') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	mso_hook_add('admin_head','tiny_mce_admin_head');

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Настройки TinyMCE", "plugins"); ' );
	mso_hook_add_dinamic( 'admin_title', ' return t("Настройки TinyMCE", "plugins") . " - " . $args; ' );

	require(getinfo('plugins_dir') . 'tiny_mce/admin.php');
}

function tiny_mce_admin_head($args=array())
{	echo mso_load_jquery('ui/ui.core.min.js');
	echo mso_load_jquery('ui/ui.draggable.min.js');
	echo mso_load_jquery('ui/ui.sortable.min.js');	?>	<script type="text/javascript" src="<?php echo TINY_MCE_PLUGIN_URL; ?>/tiny_mce/js/tadv.js"></script>
	<link rel="stylesheet" href="<?php echo TINY_MCE_PLUGIN_URL; ?>/tiny_mce/css/tadv-styles.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo TINY_MCE_PLUGIN_URL; ?>/tiny_mce/css/tadv-mce.css" type="text/css" />
	<?php
	return $args;
}

function tiny_mce_content_replace_chr10_br($f_content)
{
	$f_content = trim($f_content);
	$f_content = str_replace(chr(13), "", $f_content);
	return $f_content;
}

function tiny_mce($args = array())
{
	$editor_config['url'] = getinfo('plugins_url') . 'tiny_mce/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'tiny_mce/';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	//$editor_config['content'] = preg_replace("/&lt;br[^&gt;]*&gt;/Ui","\n",$args['content']);

	if (isset($args['do'])) $editor_config['do'] = $args['do'];
	else $editor_config['do'] = '';

	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
	else $editor_config['posle'] = '';

	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
	else $editor_config['action'] = '';

	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else
	{
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}

	require($editor_config['dir'] . 'editor.php');
}


?>