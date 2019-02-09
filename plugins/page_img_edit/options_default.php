<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле задаются дефолтные опции  

  $default_options_array = array(
    'prev_field' => 'image_for_page' ,
    'limit' => 20,
    'out_tags' => false,
		)   ; 

  $options = mso_get_option('page_img_edit', 'plugins', array());

  foreach ($default_options_array as $key => $val)
     if (!isset($options[$key])) $options[$key] = $val;

     
?>