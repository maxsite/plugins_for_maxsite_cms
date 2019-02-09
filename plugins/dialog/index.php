<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function dialog_autoload($args = array())
{
    mso_create_allow('dialog_edit', t('Админ-доступ к dialog', __FILE__));
    mso_hook_add('custom_page_404', 'dialog_custom_page_404');
    mso_hook_add('admin_init', 'dialog_admin_init'); # хук на админку
	  mso_hook_add( 'admin_head', 'dialog_admin_head');

	  mso_register_widget('dialog_disc_widget', t('Дискуссии форума', 'plugins')); # регистрируем виджет
	  mso_register_widget('dialog_tagclouds_widget', t('Облако меток форума', 'plugins')); # регистрируем виджет
}

function dialog_admin_head($args = array()) 
{
	return $args;
}

function dialog_init($args=false) 
{
 return $args;
}


function dialog_custom_page_404($args=false)
{
 $options = mso_get_option('dialog', 'plugins', array());
 $plugin_dir = getinfo('plugins_dir') . 'dialog/';
 require($plugin_dir . 'plugin_options_default.php');
 

 $segment1 = mso_segment(1);
 
 // массив - какие сущности (1-е сегменты) есть в плагине Диалог
 $elements_array = array($options['main_slug'] , $options['discussion_slug'] , $options['comment_slug'] , $options['edit_discussion_slug'] , $options['goto_slug'] , $options['unsubscribe_slug']); 
 
 //если есть сущности , заданные сегментом, в плагине диалог
 if ( in_array($segment1 , $elements_array) )
 {
   $segment2 = trim(mso_segment(2));
   $options['siteurl'] = $siteurl = getinfo('siteurl');
   
   $uploads_url = $siteurl . 'uploads/';
   $plugin_url = getinfo('plugins_url') . 'dialog/';
   $template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
   
   // определим флаг дефолтного/пользовательского шаблона
 
   if ($options['template'] != 'default') $default_template_dir = $plugin_dir . 'templates/default/';
   else $default_template_dir = false; // означает, что шаблон и так дефолтный
   $template_url = $plugin_url . 'templates/' . $options['template'] . '/';
 
 
   if ($options['template'] != 'default') $flag_default_template = false;
   else $flag_default_template = true; // означает, что шаблон дефолтный  
   
   $template_default_dir = $plugin_dir . 'templates/default/';
   $template_default_url = $plugin_url . 'templates/default/';   
   
   // передадим управление индексному файлу шаблона - он разберется что к чему
   if (file_exists($template_dir . 'index.php')) require($template_dir . 'index.php'); // передаем в шаблонный index
   elseif (file_exists($template_default_dir . 'index.php')) require($template_default_dir . 'index.php'); // передаем в index дефолтного шаблона
   else return $args;
   
   return true;
 }

 // для профайлов отдельно
 if ($segment1 == $options['profile_slug'])
 {
   $slugs = array($options['comments_slug'] , $options['subscribe_slug'] , $options['guds_slug'] , $options['send_email_slug'] , $options['log_slug']);
   $segment3 = trim(mso_segment(3));
   $segment2 = trim(mso_segment(2));

  // посмотрим, установлен ли плагин profile, который управляет страницами профиля
  $profile_plugin_optoins = mso_get_option('profile', 'plugins', array());
 
  if (isset($profile_plugin_optoins['profiles_title'])) 
    $profile_plugin = true;
  else $profile_plugin = false;
     
  
   if ( ( (in_array($segment3, $slugs) or (!$segment3 and !$profile_plugin) ) and is_numeric($segment2)) or ($segment2 == $options['main_slug']))
   {
     $options['siteurl'] = $siteurl = getinfo('siteurl');
   
     $uploads_url = $siteurl . 'uploads/';
     $plugin_url = getinfo('plugins_url') . 'dialog/';
     $template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
     $template_url = $plugin_url . 'templates/' . $options['template'] . '/';
     $template_default_dir = $plugin_dir . 'templates/default/';
     $template_default_url = $plugin_url . 'templates/default/';   
   
     // передадим управление индексному файлу шаблона - он разберется что к чему
     if (file_exists($template_dir . 'index.php')) require($template_dir . 'index.php'); 
     elseif (file_exists($$template_default_dir . 'index.php')) require($template_default_dir); 
     else return $args;
   
     return true;     
   }
   else return $args;   
 }

 // Если у нас страница личного кабинета и кто-то залогинен
 // и пришел код подтверждения если требуется приход этого кода
 
 if ($segment1 == $options['comuser_profile_slug'])
 {
   $segment2 = mso_segment(2);
   $slugs = array($options['settings_slug'] , $options['settings_subscribe_slug'] , $options['log_slug']);

   if ( is_login_comuser() and in_array($segment2 , $slugs) )
   {
     $options['siteurl'] = $siteurl = getinfo('siteurl');
   
     $uploads_url = $siteurl . 'uploads/';
     $plugin_url = getinfo('plugins_url') . 'dialog/';
     $template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
     $template_url = $plugin_url . 'templates/' . $options['template'] . '/';
     $template_default_dir = $plugin_dir . 'templates/default/';
     $template_default_url = $plugin_url . 'templates/default/';   
   
     // передадим управление индексному файлу шаблона - он разберется что к чему
     if (file_exists($template_dir . 'index.php')) require($template_dir . 'index.php'); 
     elseif (file_exists($template_default_dir . 'index.php')) require($template_default_dir . 'index.php'); 
     else return $args;
   
     return true;          
   }
   else return $args; // это нужно для возмоности вывода личного кабинета другими плагинами
 } 
 
 return $args;
}



# функция выполняется при активации (вкл) плагина
function dialog_activate($args = array())
{    
    // создадим БД
    require (getinfo('plugins_dir') . 'dialog/functions/create_db.php');
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function dialog_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function dialog_uninstall($args = array())
{
    mso_delete_option('dialog', 'plugins'); // удалим созданные опции
    mso_delete_option('dialog_messages', 'plugins'); // удалим созданные опции
    mso_delete_option_mask('dialog_', 'plugins'); // удалим созданные опции
    mso_delete_option('dialog_admin', 'plugins'); // удалим созданные опции
    
 //   mso_delete_float_option('dialog_', 'dialog'); // удалим флоат-опции
	mso_delete_option_mask('dialog_disc_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option_mask('dialog_tagclouds_widget_', 'plugins'); // удалим созданные опции

	// удалим таблицы
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('dforums');
	$CI->dbforge->drop_table('dcategorys');
	$CI->dbforge->drop_table('ddiscussions');
	$CI->dbforge->drop_table('dcomments');
	$CI->dbforge->drop_table('dprofiles');
	$CI->dbforge->drop_table('dwatch');
	$CI->dbforge->drop_table('dvotes');
	$CI->dbforge->drop_table('drooms');
	$CI->dbforge->drop_table('dlog');
	$CI->dbforge->drop_table('dgud');
	$CI->dbforge->drop_table('dbad');
	$CI->dbforge->drop_table('dperelinks');
	$CI->dbforge->drop_table('dmeta');
    return $args; 
}

function dialog_custom($response = '')
{    
     return $args;
}
  

// функция выводит после контента одиночной страницы ссылку на галереи по меткам страницы, если они есть
function dialog_content_end($args = array())
{
   return $args;
}
  
  # при входе в админку
function dialog_admin_init($args = array()) 
 {
     if ( !mso_check_allow('dialog_edit') ) return $args;
     $this_plugin_url = 'dialog'; // url и hook 
     mso_admin_menu_add('plugins', $this_plugin_url, 'dialog');
     mso_admin_url_hook ($this_plugin_url, 'dialog_admin_page');
     return $args;
 }
  
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function dialog_admin_page($args = array()) 
 {
  //   global $MSO;
     if ( !mso_check_allow('dialog_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "dialog"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "dialog - " . $args; ' );
  
    $plugin_dir = getinfo('plugins_dir') . 'dialog/';
    require($plugin_dir . 'admin/index.php');
 }
 
  // подключим виджеты
  require(getinfo('plugins_dir') . 'dialog/widgets.php');

  
// функция подключенияфайлов шаблона
function dialog_shared($fn='')
{
 $plugin_dir = getinfo('plugins_dir') . 'dialog/';
 if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
    require($template_dir . $fn);
 else 
    require($template_default_dir . $fn);    
}  
?>