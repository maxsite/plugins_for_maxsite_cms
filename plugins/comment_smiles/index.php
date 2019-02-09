<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 */

# функция автоподключения плагина
function comment_smiles_autoload($args = array())
{
  mso_hook_add( 'head', 'comment_smiles_head'); # хук на head шаблона - для JS
  mso_hook_add( 'comments_content_start', 'comment_smiles_custom',1); # хук на форму
}

# подключаем JS в head
function comment_smiles_head($arg = array())
{
  if (is_type('page'))
    echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>' . NR;
}


# функции плагина
function comment_smiles_custom($arg = array())
{
  $image_url=getinfo('uploads_url').'smiles/';
  $CI = & get_instance();
  $CI->load->helper('smiley_helper');
  $smileys=_get_smiley_array();
  
  echo '<p style="padding-bottom:5px;">';
  
  //кусок кода из smiley_helper
  $used = array();
  foreach ($smileys as $key => $val)
  {
    // Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
    // показывалась только одна кнопка
    if (isset($used[$smileys[$key][0]]))
    {
      continue;
    }
    echo "<a href=\"javascript:void(0);\" onClick=\"addSmile('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" title=\"".$smileys[$key][3]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\" /></a> ";
    $used[$smileys[$key][0]] = TRUE;
  }
  
  echo '</p>';
        
}

?>
