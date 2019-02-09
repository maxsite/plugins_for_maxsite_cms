<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// что вывести в head 

   
   function dialog_head2($a = array())
   {
      $template = 'default';
      $fn_css = getinfo('plugins_url') . 'dialog/templates/' . $template . '/' . 'css2.css';
	    echo '<link rel="stylesheet" type="text/css" href="'. $fn_css . '">';
	    $fn_js = getinfo('plugins_url') . 'dialog/templates/' . $template . '/' . 'comments.js';
	    echo '<script type="text/javascript" src="' . $fn_js . '"></script>' . NR;
    
	    return $a;
   }   
    
    
   function dialog_head1($a = array())
   {
      $template = 'default';
      $fn_css = getinfo('plugins_url') . 'dialog/templates/' . $template . '/' . 'css1.css';
	    echo '<link rel="stylesheet" type="text/css" href="'. $fn_css . '">';
	    $fn_js = getinfo('plugins_url') . 'dialog/templates/' . $template . '/' . 'comments.js';
	    echo '<script type="text/javascript" src="' . $fn_js . '"></script>' . NR;
  	    
	    return $a;
   }  
   
function dialog_comment_button_head($arg = array())
{
		echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;
	  return $arg;
}

function dialog_comment_smiles_head($arg = array())
{
		echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>' . NR;
	  return $arg;
}


  if ($comuser_id)
  {
    if ($post = mso_check_post(array('f_session_id', 'dialog_css_submit')))
    {
		    $css_id = mso_array_get_key($post['dialog_css_submit']);
			  
			     $upd_date = array('profile_css' => $options['css'][$css_id]);
			  
			     $CI = & get_instance();
			     $CI->db->where('profile_user_id', $comuser_id);
			     $res = ($CI->db->update('dprofiles', $upd_date )) ? '1' : '0';
			     
			     if ($res) $comuser['profile_css'] = $options['css'][$css_id];
			     
			     $CI->db->cache_delete_all();

    }
  }
   
  if (!isset($comuser['profile_css']) or !in_array($comuser['profile_css'] , $options['css'])) $comuser['profile_css'] = $options['css'][0];
        
     
  if ($comuser['profile_css'] == $options['css'][0])  $head_fn = 'dialog_head1';
  else $head_fn = 'dialog_head2';
  
 	mso_hook_add('head', $head_fn); 
 	 
  if ($options['comment_plugins'] and isset($flag_show_comments_js) and $flag_show_comments_js)
  {
     global $MSO;
     if (in_array('comment_button', $MSO->active_plugins) ) mso_hook_add('head', 'dialog_comment_button_head'); 
  }


?>



