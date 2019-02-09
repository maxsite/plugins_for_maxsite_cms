<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// что вывести в head 
// этот файл подключается до main-start
   
   function dialog_head($a = array())
   {
 	    global $profile_css_furl , $js_furl;
        
	    echo '<link rel="stylesheet" type="text/css" href="' . $profile_css_furl . '">' . NR;
	    echo '<script type="text/javascript" src="' . $js_furl . '"></script>' . NR;
    
	    return $a;
   }   
    

  if (!isset($comuser['profile_css']) or !in_array($comuser['profile_css'] , $options['css'])) $comuser['profile_css'] = $options['css'][0];
        
  
  // создадим глобальные переменные чтобы подключать в head файлы из нужного шаблона
 	global $profile_css_furl , $js_furl;
 	
  $fn = $comuser['profile_css'];
  if ( ($template_default_url != $template_url) and (file_exists($template_dir . $fn)) )
    $profile_css_furl = $template_url . $fn;
  else 
    $profile_css_furl = $template_default_url . $fn;
 	
  $fn = 'comments.js';
  if ( ($template_default_url != $template_url) and (file_exists($template_dir . $fn)) )
    $js_furl = $template_url . $fn;
  else 
    $js_furl = $template_default_url . $fn;
     	
 	mso_hook_add('head', 'dialog_head'); 
 	 


?>



