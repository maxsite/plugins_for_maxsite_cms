<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 */


# функция автоподключения плагина
function ecwid_autoload()
{
	mso_hook_add( 'admin_init', 'ecwid_admin_init'); # хук на админку
	mso_hook_add('custom_page_404', 'ecwid_custom_page_404');
}

# функция выполняется при активации (вкл) плагина
function ecwid_activate($args = array())
{	
	mso_create_allow('ecwid_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('Магазин Ecwid', __FILE__));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function ecwid_deactivate($args = array())
{	
	mso_delete_option('plugin_ecwid', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function ecwid_uninstall($args = array())
{	
	mso_delete_option('plugin_ecwid', 'plugins'); // удалим созданные опции
	mso_remove_allow('ecwid_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function ecwid_admin_init($args = array()) 
{
	if ( !mso_check_allow('ecwid_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'ecwid';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Магазин Ecwid', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'ecwid_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function ecwid_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл

	if ( !mso_check_allow('ecwid_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('ecwid', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('ecwid', __FILE__) . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'ecwid/admin.php');
}

function ecwid_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_ecwid', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'shop'; 
	
	if ( mso_segment(1)==$options['slug'] ) 
	{
		require( getinfo('plugins_dir') . 'ecwid/shop.php' );
		return true;
	}

	return $args;
}

function ecwid()
{
	$CI = & get_instance();

	$options = mso_get_option('plugin_ecwid', 'plugins', array());

	if ( !isset($options['header'])) $options['header'] = t('Интернет-Магазин', __FILE__);
	if ( !isset($options['textdo'])) $options['textdo'] = ''; 
	if ( !isset($options['textposle'])) $options['textposle'] = '';
	if ( !isset($options['slug'])) $options['slug'] = 'shop';
	if ( !isset($options['ecwid_storeid'])) $options['ecwid_storeid'] = 1003; 
	if ( !isset($options['ecwid_catperrow'])) $options['ecwid_catperrow'] = 3; 
	if ( !isset($options['ecwid_productpercolumn'])) $options['ecwid_productpercolumn'] = 3;
	if ( !isset($options['ecwid_productsperrow'])) $options['ecwid_productsperrow'] = 3;
	if ( !isset($options['ecwid_productsperpage'])) $options['ecwid_productsperpage'] = 10;
	if ( !isset($options['ecwid_productsperpagetable'])) $options['ecwid_productsperpagetable'] = 20;
	if (!isset($options['ecwid_show_search']))  $options['ecwid_show_search'] = true;
	if (!isset($options['ecwid_show_minicart']))  $options['ecwid_show_minicart'] = true;
	if (!isset($options['ecwid_show_category']))  $options['ecwid_show_category'] = true;
	if (!isset($options['ecwid_show_sidebar']))  $options['ecwid_show_sidebar'] = true;

	$out = '';
	
	$out .= '<h1><a href="' . $options['slug'] . '">' . $options['header'] . '</a></h1>';

	$out .= '<p>' . $options['textdo'] . '</p><br>';

	$ssl = '';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
	{
		$ssl = 'https://';
	}
	else
	{
		$ssl = 'http://';
	}

	$out .= '<script type="text/javascript" src="' . $ssl . 'app.ecwid.com/script.js?' . $options['ecwid_storeid'] . '" charset="utf-8"></script>';
	
	if ($options['ecwid_show_search'])
	{
		$out .= '<div><script type="text/javascript"> xSearchPanel("style="); </script></div>';	
	}

	if ($options['ecwid_show_minicart'])
	{
		$out .= '<div><script type="text/javascript"> xMinicart("style=","layout=attachToCategories"); </script></div>';
	}
	
	if ($options['ecwid_show_category'])
	{
		$out .= '<div><script type="text/javascript"> xCategories("style="); </script></div>';
	}
	
	// нужно будет вынести в сайтбар
	// $out .= '<div><script type="text/javascript"> xVCategories("style="); </script></div>';

	$out .= '<div>
	<script type="text/javascript"> xProductBrowser("categoriesPerRow=' . $options['ecwid_catperrow'] . '","views=grid(' . $options['ecwid_productpercolumn'] . ',' . $options['ecwid_productsperrow'] . ') list(' . $options['ecwid_productsperpage'] . ') table(' . $options['ecwid_productsperpagetable'] . ')","categoryView=grid","searchView=list","style="); </script>
	<noscript>Ваш браузер не поддерживает JavaScript. Пожалуйста, перейдите на <a href="http://app.ecwid.com/jsp/' . $options['ecwid_storeid'] . '/catalog">HTML версию магазина.</a></noscript>
	</div>';

	$out .= '<div><p>' . $options['textposle'] . '</p><br></div>';
	
	if ($options['ecwid_show_sidebar'])
	{
		$out .= '<style type="text/css">
			div.content {margin: 0; width: 100%; float: none;}
			div.content-wrap {margin: 0px 20px 10px 20px; padding-top:20px;}
			div.sidebar, div.sidebar1, div.sidebar2 {display: none;}
			</style>';
	}
	
	echo $out;
	
	return $out;
	
}

# end file