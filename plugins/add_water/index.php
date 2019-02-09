<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 

 
 
# функция автоподключения плагина
function add_water_autoload($args = array())
{
    mso_create_allow('add_water_edit', t('Админ-доступ к add_water', __FILE__));
    mso_hook_add('admin_init', 'add_water_admin_init'); # хук на админку
}



# функция выполняется при активации (вкл) плагина
function add_water_activate($args = array())
{    
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function add_water_deactivate( $args = array() )
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function add_water_uninstall($args = array())
{
    mso_delete_option('add_water', 'plugins'); // удалим созданные опции
    return $args; 
}

function add_water_custom($response = '')
{    
     return $args;
}
  
  # при входе в админку
function add_water_admin_init($args = array()) 
 {
     if ( !mso_check_allow('add_water_edit') ) return $args;
  
     $this_plugin_url = 'add_water'; // url и hook 
     
     # добавляем свой пункт в меню админки
     # первый параметр - группа в меню
     # второй - это действие/адрес в url - http://сайт/admin/demo
     # Третий - название ссылки    
     mso_admin_menu_add('plugins', $this_plugin_url, 'add_water');
  
     # прописываем для указаного url
     # связанную функцию именно она будет вызываться, когда 
     # будет идти обращение по адресу http://сайт/admin/demo
     mso_admin_url_hook ($this_plugin_url, 'add_water_admin_page');
     
     return $args;
 }
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function add_water_admin_page($args = array()) 
 {
     global $MSO;
     if ( !mso_check_allow('add_water_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "add_water"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "add_water - " . $args; ' );
  
     # выносим админские функции отдельно в файл   
     $plugin_url = getinfo('siteurl') . 'admin/add_water/';
     require($MSO->config['plugins_dir'] . 'add_water/admin.php');
 }
 
  
?>