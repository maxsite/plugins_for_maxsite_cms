<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
   function bookmaker_head($a = array())
   {
      $plugin_url = getinfo('plugins_url') . 'bookmaker/';
	    echo '<link rel="stylesheet" type="text/css" href="'. $plugin_url . 'bookmaker.css">';
      echo '<script type="text/javascript" src="' . $plugin_url . 'functions.js"></script>' . NR;	    
	    return $a;
   } 

# функция автоподключения плагина
function bookmaker_autoload($args = array())
{
	mso_hook_add( 'head', 'bookmaker_head'); # хук на head шаблона
    mso_hook_add('custom_page_404', 'bookmaker_custom_page_404');

    mso_create_allow('bookmaker_edit', t('Админ-доступ к bookmaker', __FILE__));
    mso_hook_add('admin_init', 'bookmaker_admin_init'); # хук на админку
	  mso_hook_add( 'admin_head', 'bookmaker_admin_head');

 
}

function bookmaker_admin_head($args = array()) 
{
	return $args;
}

function bookmaker_init($args=false) 
{
 return $args;
}





# функция выполняется при активации (вкл) плагина
function bookmaker_activate($args = array())
{    

}
  
# функция выполняется при деактивации (выкл) плагина
function bookmaker_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function bookmaker_uninstall($args = array())
{
    return $args; 
}

function bookmaker_custom($response = '')
{    
     return $args;
}
  

  
  # при входе в админку
function bookmaker_admin_init($args = array()) 
 {
     if ( !mso_check_allow('bookmaker_edit') ) return $args;
     $this_plugin_url = 'bookmaker'; // url и hook 
     mso_admin_menu_add('plugins', $this_plugin_url, 'bookmaker');
     mso_admin_url_hook ($this_plugin_url, 'bookmaker_admin_page');
     return $args;
 }
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function bookmaker_admin_page($args = array()) 
 {
  //   global $MSO;
     if ( !mso_check_allow('bookmaker_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "bookmaker"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "bookmaker - " . $args; ' );
  
    $plugin_dir = getinfo('plugins_dir') . 'bookmaker/';
    require($plugin_dir . 'admin/index.php');
 }
 

function bookmaker_custom_page_404($args=false)
{
 $options = mso_get_option('bookmaker', 'plugins', array());
 $plugin_dir = getinfo('plugins_dir') . 'bookmaker/';

 if (!isset($options['comuser_profile_slug'])) $options['comuser_profile_slug'] = 'profile';
 if (!isset($options['bookmaker_slug'])) $options['bookmaker_slug'] = 'bookmaker';
 
 if (!isset($options['elements'])) $options['elements'] = array('pages'=>'Страницы' , 'comments'=>'Комментарии' );
 $element_slugs = array_keys($options['elements']);

 $segment1 = mso_segment(1);
 $segment2 = mso_segment(2);
 

 // Если у нас страница личного кабинета и кто-то залогинен
 if ( ($segment1 == $options['comuser_profile_slug']) and is_login_comuser() and ($segment2 == $options['bookmaker_slug']) )
 {
   $segment3 = mso_segment(3);
   if ( !$segment3 or in_array($segment3 , $element_slugs) )  // если есть что выводить по 2-му сегменту
   {
     // передадим управление файлу вывода
     require($plugin_dir . 'out/profile_main.php'); 
   
     return true;          
   }
   else return $args; // это нужно для возмоности вывода личного кабинета другими плагинами
 } 
 
 return $args;
} 

?>