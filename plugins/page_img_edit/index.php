<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function page_img_edit_autoload($args = array())
{
    mso_create_allow('page_img_edit_edit', t('Админ-доступ к page_img_edit', __FILE__));
    mso_hook_add('admin_init', 'page_img_edit_admin_init'); # хук на админку
	mso_hook_add( 'admin_head', 'page_img_edit_admin_head');
}

function page_img_edit_admin_head($args = array()) 
{
	echo '<link rel="STYLESHEET" type="text/css" href="' . getinfo('plugins_url') . 'page_img_edit/style.css">';
	return $args;
}

function page_img_edit_init($args=false) 
{
 return $args;
}


# функция выполняется при активации (вкл) плагина
function page_img_edit_activate($args = array())
{    
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function page_img_edit_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function page_img_edit_uninstall($args = array())
{
    mso_delete_option('page_img_edit', 'plugins'); // удалим созданные опции
    mso_delete_option('page_img_edit_admin', 'plugins'); // удалим созданные опции
}


  
  # при входе в админку
function page_img_edit_admin_init($args = array()) 
 {
     if ( !mso_check_allow('page_img_edit_edit') ) return $args;
     $this_plugin_url = 'page_img_edit'; // url и hook 
     mso_admin_menu_add('plugins', $this_plugin_url, 'page_img_edit');
     mso_admin_url_hook ($this_plugin_url, 'page_img_edit_admin_page');
     return $args;
 }
  
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function page_img_edit_admin_page($args = array()) 
 {
    require(getinfo('plugins_dir') . 'page_img_edit/options_default.php');
 
  //   global $MSO;
     if ( !mso_check_allow('page_img_edit_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "page_img_edit"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "page_img_edit - " . $args; ' );
  
    $plugin_dir = getinfo('plugins_dir') . 'page_img_edit/';
    require($plugin_dir . 'admin/index.php');
 }
 

?>