<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * q_rss_import
 * http://maxsitecms.ru/q_rss_import
 */

  if (!defined('MAGPIE_CACHE_AGE'))  define('MAGPIE_CACHE_AGE', 1); // время кэширования MAGPIE
  require_once($MSO->config['common_dir'] . 'magpierss/rss_fetch.inc');


  # новая страница (взята из common/functions-edit.php, вырезана проверка логина и пароля)
  function mso_new_page2($data)
  {
    global $MSO;

    $CI = & get_instance();


    $page_title = $data['page_title'];
    $page_content = $data['page_content'];

    // короткая ссылка
    $page_slug = isset($data['page_slug']) ? mso_slug($data['page_slug']) : false;
    if (!$page_slug)
    {
      if ($page_title) $page_slug = mso_slug($page_title);
        else $page_slug = 'no-title';
    }

    // нужно проверить есть ли уже такая запись
    // проверяем по заголовку + тексту
    $CI->db->select('page_slug, page_title, page_content');
    $CI->db->where(array('page_title'=>$page_title, 'page_content'=>$page_content ));
    // $CI->db->where(array('page_slug'=>$page_slug, 'page_title'=>$page_title ));
    $query = $CI->db->get('page');
    if ($query->num_rows()) // что-то есть
    {
      $response = array(
              'result' => 0,
              'description' => 'Existing page'
              );
      // pr($response);
      return $response;
    }

    // $page_slug нужно проверить на существование
    // если есть, то нужно добавить скажем их кол-во+1
    $CI->db->select('page_slug');
    $query = $CI->db->get('page'); // получили все slug

    if ($query->num_rows()>0)
    {
      $all = array(); // сделаем массив всех слаг
      foreach ($query->result_array() as $row)
        $all[] = $row['page_slug'];

      $count = 0; // начальное приращения слага
      $in = in_array($page_slug, $all); // признак вхождения -
      while ($in)
      {
        $count++;
        $in = in_array($page_slug . '-' . $count, $all);
      }
      if ($count) $page_slug = $page_slug . '-' . $count;
    }

    $page_password = isset($data['page_password']) ? mso_strip($data['page_password']) : '';


    // дата публикации если нет даты, от ставим текущую
    $page_date_publish = isset($data['page_date_publish']) ? $data['page_date_publish'] : date('Y-m-d H:i:s');

    $page_type_id = isset($data['page_type_id']) ? $data['page_type_id'] : '1';
    $page_id_parent = isset($data['page_id_parent']) ? $data['page_id_parent'] : '0';

    $page_status = isset($data['page_status']) ? $data['page_status'] : 'publish';
    if ( ($page_status != 'publish') and ($page_status != 'draft') and ($page_status != 'private') )
        $page_status = 'publish';


    $page_comment_allow = isset($data['page_comment_allow']) ? (int) $data['page_comment_allow'] : '1';
    $page_ping_allow = isset($data['page_ping_allow']) ? (int) $data['page_ping_allow'] : '1';
    $page_feed_allow = isset($data['page_feed_allow']) ? (int) $data['page_feed_allow'] : '1';


    $page_id_autor = isset($data['page_id_autor']) ? (int) $data['page_id_autor'] : -1;

    // нужно проверить вообще есть ли такой юзер $page_id_autor
    $CI->db->select('users_id');
    $CI->db->from('users');
    $CI->db->where(array('users_id'=>$page_id_autor));
    $query = $CI->db->get();
    if (!$query->num_rows()) // нет
      $page_id_autor = '-1';

    $ins_data = array (
      'page_type_id' => $page_type_id,
      'page_id_parent' => $page_id_parent,
      'page_id_autor' => $page_id_autor,
      'page_title' => $page_title,
      'page_content' => $page_content,
      'page_status' => $page_status,
      'page_slug' => $page_slug,
      'page_password' => $page_password,
      'page_comment_allow' => $page_comment_allow,
      'page_ping_allow' => $page_ping_allow,
      'page_feed_allow' => $page_feed_allow,
      'page_date_publish' => $page_date_publish,
      'page_last_modified' => $page_date_publish,

      // 'page_date_dead' => $,
      // 'page_lang' => $,

      );

    // pr($ins_data);

    $res = ($CI->db->insert('page', $ins_data)) ? '1' : '0';

    if ($res)
    {
      $id = $CI->db->insert_id(); // номер добавленной записи

      // добавим теперь рубрики
      // рубрики указаны в виде номеров через запятую
      $page_id_cat = isset($data['page_id_cat']) ? $data['page_id_cat'] : '';
      $page_id_cat = mso_explode($page_id_cat); // в массив

      foreach ($page_id_cat as $key=>$val)
      {
        $ins_data = array (
          'page_id' => $id,
          'category_id' => $val
          );
        $CI->db->insert('cat2obj', $ins_data);
      }

      // $page_tags = метка
      // метки - это мета данные
      // вначале получим существующие метки
      $CI->db->select('meta_id');

      // дефолтные данные
      $def_data = array (
          'meta_key' => 'tags',
          'meta_id_obj' => $id,
          'meta_table' => 'page'
          );

      $CI->db->where($def_data);
      $query = $CI->db->get('meta');

      if (!$query->num_rows()) // нет меток для этой страницы
      {  // значит инсерт
        $page_tags = isset($data['page_tags']) ? $data['page_tags'] : '';
        $tags = mso_explode($page_tags, false, false); // в массив - не только числа

        foreach ($tags as $key=>$val)
        {
          $ins_data = $def_data;
          $ins_data['meta_value'] = $val;
          $CI->db->insert('meta', $ins_data);
        }
      }

      // опции - мета
      $page_meta_options = isset($data['page_meta_options']) ? $data['page_meta_options'] : '';


      //title##VALUE##титул##METAFIELD##description##VALUE##описание##METAFIELD##keywords##VALUE##ключи##METAFIELD##

      $page_meta_options = explode('##METAFIELD##', $page_meta_options);

      // добавляем через insert
      foreach ($page_meta_options as $key=>$val)
      {
        if (trim($val))
        {
          $meta_temp = explode('##VALUE##', $val);
          $meta_key = trim($meta_temp[0]);
          $meta_value = trim($meta_temp[1]);

          $CI->db->insert('meta', array('meta_key'=>$meta_key, 'meta_value'=>$meta_value,
                          'meta_table' => 'page', 'meta_id_obj' => $id) );
        }
      }

      // результат возвращается в виде массива
      $res = array($id, $page_slug, $page_status, $page_password, $page_date_publish);
      $response = array(
              'result' => $res,
              'description' => 'Inserting new page'
              );
    }
    else
    {
      $response = array(
            'result' => 0,
            'description' => 'Error inserting page'
            );
    }

    if ($response['result']) mso_hook('new_page');

    return $response;
  }







# функция автоподключения плагина
function q_rss_import_autoload($args = array())
{
  mso_create_allow('q_rss_import_edit', 'Админ-доступ к q_rss_import');
  mso_hook_add( 'admin_init', 'q_rss_import_admin_init'); # хук на админку
  mso_hook_add( 'init', 'q_rss_import_doit'); # хук на инициализацию
}

#само импортирование
#При инициализации выбирается очередной фид. Если пришло время его импортировать - импортируем
function q_rss_import_doit()
{
  global $MSO;
  $options = mso_get_option('plugin_q_rss_import', 'plugins', array());
  if ( !isset($options['enabled'])||!$options['enabled'] ) return;

  $links=@file_get_contents($MSO->config['uploads_dir'].'rss_import_links.txt');
  if($links)
  {
    $links=unserialize($links);
  }
  else
  {
    $links=array();
  }

  //Если пришло время его импортировать - импортируем
  if(time()>$options[$options['curr_feed']]['timestamp']+$options[$options['curr_feed']]['interval'])
  {
    //парсим фид
    $all = @fetch_rss($options[$options['curr_feed']]['rssurl']);
    if ($all)
    {
      $ti=0;
      foreach($all->items as $item)
      {
        //еще данный итем не импортировали
        if(!in_array($item['link'],$links))
        {
          if(!isset($item['description']))$item['description']='';
          if(!isset($item['title']))$item['title']='';
          $data = array(
          'page_title' => $item['title'],
          'page_content' => $item['description'],
          'page_type_id' => '1',
          'page_id_cat' => '',
          'page_id_parent' => '',
          'page_id_autor' => 1,
          'page_status' => 'publish',
          'page_slug' => '',
          'page_password' => '',
          'page_comment_allow' => 1,
          'page_ping_allow' => 0,
          'page_feed_allow' => 1,
          'page_tags' => '',
          'page_meta_options' => '',

          );

          $result = mso_new_page2($data);

          $links[]=$item['link'];
          if(++$ti>=$options[$options['curr_feed']]['cntitem4import'])break;
        }
      }
      $options[$options['curr_feed']]['timestamp']=time();
    }
  }
  else
  {

  }
  
  $options['curr_feed']=$options['curr_feed']%$options['feed_count']+1;
  mso_add_option('plugin_q_rss_import', $options, 'plugins');

  $fp=fopen($MSO->config['uploads_dir'].'rss_import_links.txt','w');
  fwrite($fp,serialize($links));
  fclose($fp);
  
}

# функция выполняется при активации (вкл) плагина
function q_rss_import_activate($args = array())
{  
  $options=array();
  $options['feed_count']=1;
  $options['curr_feed']=1;
  $options['enabled']=0;

  $options[1]['interval'] = 60*60*24;
  $options[1]['rssurl'] = 'http://maxsitecms.ru/feed';
  $options[1]['timestamp'] = 0;
  $options[1]['cntitem4import'] = 2;

  mso_add_option('plugin_q_rss_import', $options, 'plugins');

  return $args;
}

# функция выполняется при деактивации (выкл) плагина
function q_rss_import_deactivate($args = array())
{  
  // mso_delete_option('plugin_q_rss_import', 'plugins'); // удалим созданные опции
  return $args;
}

# функция выполняется при деинстяляции плагина
function q_rss_import_uninstall($args = array())
{  
  mso_delete_option('plugin_q_rss_import', 'plugins'); // удалим созданные опции
  return $args;
}

# функция выполняется при указаном хуке admin_init
function q_rss_import_admin_init($args = array())
{
  if ( !mso_check_allow('q_rss_import_edit') )
  {
    return $args;
  }
  
  $this_plugin_url = 'plugin_q_rss_import'; // url и hook
  
  # добавляем свой пункт в меню админки
  # первый параметр - группа в меню
  # второй - это действие/адрес в url - http://сайт/admin/demo
  #      можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
  # Третий - название ссылки  
  
  mso_admin_menu_add('plugins', $this_plugin_url, 'Плагин q_rss_import');

  # прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
  # связанную функцию именно она будет вызываться, когда 
  # будет идти обращение по адресу http://сайт/admin/_null
  mso_admin_url_hook ($this_plugin_url, 'q_rss_import_admin_page');
  
  return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function q_rss_import_admin_page($args = array())
{
  # выносим админские функции отдельно в файл
  global $MSO;
  if ( !mso_check_allow('q_rss_import_admin_page') )
  {
    echo 'Доступ запрещен';
    return $args;
  }
  
  mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Плагин q_rss_import"; ' );
  mso_hook_add_dinamic( 'admin_title', ' return "q_rss_import - " . $args; ' );
  
  require($MSO->config['plugins_dir'] . 'q_rss_import/admin.php');
}


# функции плагина
function q_rss_import_custom($arg = array(), $num = 1)
{

  
}



?>
