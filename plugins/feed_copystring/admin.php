<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Feed Copyright String (http://maxsitecms.ru/feed_copystring)
 */

  global $MSO;
  $CI = & get_instance();
  
  $options_key = 'plugin_feed_copystring';
  
  if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
  {
    mso_checkreferer();
    
    $options = array();
    $options['comments'] = isset( $post['f_comments'] ) ? $post['f_comments'] : '' ;
    $options['category'] = isset( $post['f_category'] ) ? $post['f_category'] : '' ;
    $options['page'] =     isset( $post['f_page'] )     ? $post['f_page']     : '' ;
  
    mso_add_option($options_key, $options, 'plugins');
    echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
  }
  
?>
<h1><?= t('Плагин Feed Copyright String', __FILE__) ?></h1>
<p class="info"><?= t('Плагин добавляет указанные строки к rss-лентам сайта', __FILE__) ?></p>

<?php

    $options = mso_get_option($options_key, 'plugins', array());
    if ( !isset($options['comments']) ) $options['comments'] = '';
    if ( !isset($options['category']) ) $options['category'] = '';
    if ( !isset($options['page']) )     $options['page']     = '';

    $form = '<p><strong>' . t('Строка, добавляющаяся к записям rss-ленты комментариев:', __FILE__) . '</strong></p>
        <p><input style="width: 550px" name="f_comments" type="text" value="' . htmlspecialchars($options['comments']) . '"></p>';
        
    $form .= '<p><strong>' . t('Строка, добавляющаяся к записям rss-ленты категорий:', __FILE__) . '</strong></p>
        <p><input style="width: 550px" name="f_category" type="text" value="' . htmlspecialchars($options['category']) . '"></p>';
        
    $form .= '<p><strong>' . t('Строка, добавляющаяся к записям главной rss-ленты сайта:', __FILE__) . '</strong></p>
        <p><input style="width: 550px" name="f_page" type="text" value="' . htmlspecialchars($options['page']) . '"></p>';

    $form .= '<p>' . t('Возможные подстановки:',__FILE__) . ' [SITE_URL] [SITE_NAME] [SITE_DESCRIPTION]</p>';
    $form .= '<p>' . t('Например:',__FILE__) . htmlspecialchars(' <p>© <a href="[SITE_URL]">[SITE_NAME]</a> - [SITE_DESCRIPTION]</p>') . '</p>';
    
    echo '<form action="" method="post">' . mso_form_session('f_session_id');
    echo $form;
    echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;" />';
    echo '</form>';

?>
