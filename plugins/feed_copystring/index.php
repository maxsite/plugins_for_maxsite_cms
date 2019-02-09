<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Feed Copyright String (http://maxsitecms.ru/feed_copystring)
 */


# функция автоподключения плагина
function feed_copystring_autoload($args = array())
{
  mso_create_allow('feed_copystring_edit', t('Админ-доступ к Feed Copyright String', __FILE__));
  mso_hook_add( 'admin_init', 'feed_copystring_admin_init'); # хук на админку
  mso_hook_add( 'content', 'feed_copystring_custom' ); # хук на тексты
  mso_hook_add( 'comments_content', 'feed_copystring_custom');
}


# функция выполняется при деинстяляции плагина
function feed_copystring_uninstall($args = array())
{
  mso_delete_option('plugin_feed_copystring', 'plugins'); // удалим созданные опции
  return $args;
}

# функция выполняется при указаном хуке admin_init
function feed_copystring_admin_init($args = array())
{
  if ( !mso_check_allow('feed_copystring_edit') )
  {
    return $args;
  }

  $this_plugin_url = 'plugin_feed_copystring'; // url и hook

  # добавляем свой пункт в меню админки
  mso_admin_menu_add('plugins', $this_plugin_url, 'Feed Copyright String');

  # указываем функцию, которая обрабатывает админку плагина
  mso_admin_url_hook ($this_plugin_url, 'feed_copystring_admin_page');

  return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function feed_copystring_admin_page($args = array())
{
  # выносим админские функции отдельно в файл
  global $MSO;
  if ( !mso_check_allow('feed_copystring_admin_page') )
  {
    echo t('Доступ запрещен',__FILE__);
    return $args;
  }

  mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Feed Copyright String"; ' );
  mso_hook_add_dinamic( 'admin_title', ' return "Feed Copyright String - " . $args; ' );

  require($MSO->config['plugins_dir'] . 'feed_copystring/admin.php');
}


# функции плагина
function feed_copystring_custom($content)
{

  $dobavka='';
  if ( is_feed() )
  {
    $options = mso_get_option('plugin_feed_copystring', 'plugins', array());
    if ( !isset($options['comments']) ) $options['comments'] = '';
    if ( !isset($options['category']) ) $options['category'] = '';
    if ( !isset($options['page']) )     $options['page']     = '';
  
    if ( is_type('page') || is_type('comments') ) $dobavka = $options['comments'];       // комментарии
    elseif ( is_type('category') ) $dobavka = $options['category'];                      // по рубрикам
    else $dobavka = $options['page'];                                                     // записи

    $dobavka=str_replace( array( '[SITE_URL]', '[SITE_NAME]', '[SITE_DESCRIPTION]' ),
                          array( getinfo('siteurl'), getinfo('name_site'), getinfo('description_site') ),
                          $dobavka);
  }

  return $content.$dobavka;
}
?>
