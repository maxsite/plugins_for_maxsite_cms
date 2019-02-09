<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Seozavr
 * (c) http://maxsitecms.ru/
 */


# функция автоподключения плагина
function seozavr_autoload($args = array())
{
  mso_create_allow('seozavr_edit', 'Админ-доступ к редактированию seozavr');
  mso_hook_add( 'init', 'seozavr_init'); # хук на инициализацию
  mso_hook_add( 'admin_init', 'seozavr_admin_init'); # хук на админку
}


# функция выполняется при деинсталяции плагина
function seozavr_uninstall($args = array())
{  
  //mso_delete_option_mask('seozavr_widget_', 'plugins'); // удалим созданные опции
  return $args;
}

# функция выполняется при указаном хуке admin_init
function seozavr_admin_init($args = array())
{
  if ( mso_check_allow('plugin_seozavr') )
  {
    $this_plugin_url = 'plugin_seozavr'; // url и hook
    mso_admin_menu_add('plugins', $this_plugin_url, 'Seozavr.ru');
    mso_admin_url_hook ($this_plugin_url, 'seozavr_admin_page');
  }
  
  return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function seozavr_admin_page($args = array())
{
  global $MSO;
  
  # выносим админские функции отдельно в файл
  if ( !mso_check_allow('plugin_seozavr') )
  {
    echo 'Доступ запрещен';
    return $args;
  }
  # выносим админские функции отдельно в файл
  mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка seozavr.ru"; ' );
  mso_hook_add_dinamic( 'admin_title', ' return "Настройка seozavr.ru - " . $args; ' );
  require($MSO->config['plugins_dir'] . 'seozavr/admin.php');
}


# подключаем функции сеозавра
function seozavr_init($args = array())
{
  global $seozavr, $MSO;
  
  $options = mso_get_option('seozavr', 'plugins', array() ); // получаем опции
  
  if (isset($options['kod']) 
    and isset($options['go']) and $options['go'] 
    and isset($options['start']) and $options['start']) // можно подключать
  {

    if ( !defined('_seozavr_USER') ) define('_seozavr_USER', $options['kod']);
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/' . _seozavr_USER . '/seozavr.php');
    
    $seozavr = new seozavr();
    $seozavr->encoding = 'utf-8';
    
    if (isset($options['cat_link']) and $options['cat_link']) $seozavr->catalogURL = $options['cat_link'];
    
    if (isset($options['no_part_link']) and $options['no_part_link']) $seozavr->enableAffiliateLink = false;
    
    if (isset($options['own_title']) and $options['own_title']) $seozavr->catalogTitle = iconv('utf-8', 'windows-1251', $options['own_title']);

    if (isset($options['leave_del_art']) and $options['leave_del_art']) $seozavr->leaveDeletedArticles = false;


    mso_hook_add( 'content_content', 'seozavr_content'); # хук на конечный текст для вывода
    mso_hook_add( 'head_meta', 'seozavr_head_meta'); # хук на мета-тэги

  }
  
  return $args;
}


# функция вывода блока ссылок на статьи
function seozavr_out()
{
  global $seozavr;
  
  $out = '';
  
  if (isset($seozavr) and $seozavr)
  {
    $out = $seozavr->getList();
  }
  
  return $out;
}

# функция вывода контента
function seozavr_content($text = '')
{
  global $seozavr;
  
  if(strpos($text,'[seozavr]')!==false)
  {
    $text = str_replace('[seozavr]',$seozavr->getBody(),$text);

  }
  $text = str_replace('[seozavr_list]',$seozavr->getList(),$text);

  return $text;
}

# функция обработки мета-тэгов
function seozavr_head_meta($arg = array())
{
  global $seozavr, $MSO;
  
  $options = mso_get_option('seozavr', 'plugins', array() ); // получаем опции
  
  if( mso_segment(2)==$options['cat_link'] )  // только каталог
  {
    if (isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'])
    {
      parse_str($_SERVER['QUERY_STRING'] , $in);
      $_GET=$in;
    }

    if(isset($arg['info'])&&$arg['info']=='title')$arg=$seozavr->getTitle();
    if(isset($arg['info'])&&$arg['info']=='description')$arg=$seozavr->getDescription();
    if(isset($arg['info'])&&$arg['info']=='keywords')$arg=$seozavr->getKeywords();
  }
  else //Еси не наше, выводим как обычно, копия mso_head_meta
  {


    $info=$arg['info'];
    $args=$arg['args'];
    $format=$arg['format'];
    $sep=$arg['sep'];
    $only_meta =$arg['only_meta'];
    
    if (!$args) // нет аргумента - выводим что есть
    {
      if ( !$MSO->$info )  $arg = $MSO->$info = getinfo($info);
      else $arg = $MSO->$info;
    }
    else // есть аргументы
    {
      if (is_scalar($args)) $arg = $args; // какая-то явная строка - отдаем её как есть
      else // входной массив - скорее всего это страница
      {
        $category_name = '';
        $page_title = '';
        $users_nik = '';
        $title = getinfo($info);

        if ( $info!='title') $format = '%title%';

        if ( isset($args[0]['category_name']) ) $category_name = $args[0]['category_name'];
        if ( isset($args[0]['page_title']) ) $page_title = $args[0]['page_title'];
        if ( isset($args[0]['users_nik']) ) $users_nik = $args[0]['users_nik'];

        // если есть мета, то берем её
        if ( isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0] )
        {
          if ( $only_meta ) $category_name = $title = $sep = '';
          $page_title = $args[0]['page_meta'][$info][0];

          if ( $info!='title') $title = $page_title;
        }
        else
        {

        }

        $arr_key = array( '%title%', '%page_title%',  '%category_name%', '%users_nik%', '|' );
        $arr_val = array( $title ,  $page_title, $category_name, $users_nik, $sep );

        $arg = str_replace($arr_key, $arr_val, $format);
      }
    }

    // отдаем результат, сразу же указывая измененный $info в $MSO->
    $arg = $MSO->$info = trim($arg);
    
    
  }
  
  return $arg;
}

?>
