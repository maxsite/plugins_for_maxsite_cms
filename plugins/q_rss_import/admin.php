<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * q_rss_import
 * http://maxsitecms.ru/q_rss_import
 */

  global $MSO;
  $CI = & get_instance();
  
  if (!defined('MAGPIE_CACHE_AGE'))  define('MAGPIE_CACHE_AGE', 1); // время кэширования MAGPIE
  require_once($MSO->config['common_dir'] . 'magpierss/rss_fetch.inc');
  
  
  $options_key = 'plugin_q_rss_import';

  //сохранение количества фидов
  if ( $post = mso_check_post(array('f_session_id', 'f_save_feed_count', 'f_feed_count')) )
  {
    mso_checkreferer();
    $options = mso_get_option($options_key, 'plugins', array());
    $options['feed_count']=max(1,min(25,$post['f_feed_count']));
    $options['curr_feed']=1;
    $options['enabled'] = isset( $post['f_enabled']) ? 1 : 0;
    
    for ($i=1;$i<=$options['feed_count'];$i++)
    {
      if ( !isset($options[$i]['interval']) ) $options[$i]['interval'] = 60*60*24;
      if ( !isset($options[$i]['rssurl']) ) $options[$i]['rssurl'] = 'http://maxsitecms.ru/feed';
      if ( !isset($options[$i]['timestamp']) ) $options[$i]['timestamp'] = 0;
      if ( !isset($options[$i]['cntitem4import']) ) $options[$i]['cntitem4import'] = 2;
      
    }
    
    mso_add_option($options_key, $options, 'plugins');
    echo '<div class="update">Обновлено!</div>';
  }

  //сохранение опции
  if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_interval', 'f_rssurl', 'f_cntitem4import')) )
  {
    mso_checkreferer();
    $options = mso_get_option($options_key, 'plugins', array());

    $options[$post['f_id_feed']]['interval'] = $post['f_interval'];
    $options[$post['f_id_feed']]['rssurl'] = $post['f_rssurl'];
    $options[$post['f_id_feed']]['timesamp'] = 0;
    $options[$post['f_id_feed']]['cntitem4import'] = $post['f_cntitem4import'];

    mso_add_option($options_key, $options, 'plugins');
    echo '<div class="update">Обновлено!</div>';
  }
  


?>
<h1>Плагин q_rss_import</h1>
<p class="info"></p>

<?php
    $options = mso_get_option($options_key, 'plugins', array());
    if ( !isset($options['feed_count']) ) $options['feed_count'] = 1;

    $form_feed_count = '<form action="" method="post">' . mso_form_session('f_session_id');
    $form_feed_count .= '<h2>Настройки</h2><br />';
    $form_feed_count .= '<p>Количество rss-лент для импорта:<br />' . ' <input size="60" name="f_feed_count" type="text" value="' . $options['feed_count'] . '"><br />';

    $chk = $options['enabled'] ? ' checked="checked"  ' : '';
    $form_feed_count .= '<input name="f_enabled" type="checkbox" ' . $chk . '> <strong>Включить плагин</strong><br />';

    $form_feed_count .= '<input type="submit" name="f_save_feed_count" value=" Сохранить " /></p>';
    $form_feed_count .= '</form><br />';

    echo $form_feed_count;


    $form_feeds='';
    for($i=1;$i<=$options['feed_count'];$i++)
    {

      $form_feeds .= '<form action="" method="post">' . mso_form_session('f_session_id');
      $form_feeds .= '<input type="hidden" name="f_id_feed" value="'.$i.'">';
      $form_feeds .= '<h3>Настройки фида '.$i.'</h3>';
      $form_feeds .= '<p>Интервал обработки (в секундах):<br />' . ' <input size="60" name="f_interval" type="text" value="' . $options[$i]['interval'] . '"></p>';
      $form_feeds .= '<p>URL rss-ленты:<br />' . ' <input size="60" name="f_rssurl" type="text" value="' . $options[$i]['rssurl'] . '"></p>';
      $form_feeds .= '<p>Количество новостей, импортируемых за раз:<br />' . ' <input size="60" name="f_cntitem4import" type="text" value="' . $options[$i]['cntitem4import'] . '"></p>';
      $form_feeds .= '<input type="submit" name="f_submit" value=" Сохранить " style="margin: 5px 5px 25px 0;" />';
      $form_feeds .= '</form>';

    };

    echo $form_feeds;

?>
