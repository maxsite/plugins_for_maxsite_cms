<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
# функция автоподключения плагина
function abc_catalog_autoload($args = array())
{
    mso_create_allow('abc_catalog_edit', t('Админ-доступ к abc_catalog', __FILE__));
    mso_hook_add('custom_page_404', 'abc_catalog_custom_page_404');
    mso_hook_add('admin_init', 'abc_catalog_admin_init'); # хук на админку

}

function abc_catalog_custom_page_404($args=false)
{
 $options = mso_get_option('abc_catalog', 'plugins', array());
 if ( !isset($options['catalog_slug']) ) $options['catalog_slug'] = 'catalog'; 
 if ( !isset($options['full_text']) ) $options['full_text'] = 'checked="true"'; 
 if ( !isset($options['categories']) ) $options['categories'] = ''; 
 if ( !isset($options['exclude_page_id']) ) $options['exclude_page_id'] = ''; 
 if ( !isset($options['type']) ) $options['type'] = 'blog'; 

 if ( !isset($options['catalog_name']) ) $options['catalog_name'] = 'Статьи';
 if ( !isset($options['category_id']) ) $options['category_id'] = '';
 $catalog_slug = $options['catalog_slug'];
 $segment = mso_segment(1);
 if ($segment == $catalog_slug)
 {
   require(getinfo('plugins_dir').'abc_catalog/catalog.php');
   return true;
 }
 return $args;
}

# функция выполняется при активации (вкл) плагина
function abc_catalog_activate($args = array())
{    
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function abc_catalog_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function abc_catalog_uninstall($args = array())
{
    mso_delete_option('abc_catalog', 'plugins'); // удалим созданные опции
    return $args; 
}

function abc_catalog_custom($response = '')
{    
     return $args;
 }
  
  # при входе в админку
function abc_catalog_admin_init($args = array()) 
 {
     if ( !mso_check_allow('abc_catalog_edit') ) return $args;
  
     $this_plugin_url = 'abc_catalog'; // url и hook 
     
     # добавляем свой пункт в меню админки
     # первый параметр - группа в меню
     # второй - это действие/адрес в url - http://сайт/admin/demo
     # Третий - название ссылки    
     mso_admin_menu_add('plugins', $this_plugin_url, 'ABC_catalog');
  
     # прописываем для указаного url
     # связанную функцию именно она будет вызываться, когда 
     # будет идти обращение по адресу http://сайт/admin/demo
     mso_admin_url_hook ($this_plugin_url, 'abc_catalog_admin_page');
     
     return $args;
 }
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function abc_catalog_admin_page($args = array()) 
 {
     global $MSO;
     if ( !mso_check_allow('abc_catalog_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "abc_catalog"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "abc_catalog - " . $args; ' );
  
     # выносим админские функции отдельно в файл    
     require($MSO->config['plugins_dir'] . 'abc_catalog/admin.php');
 }
 
 
 
  
  
?>