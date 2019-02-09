<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле инициализируются дефолтные опции  

  // опции обавления нового коммента   
  $default_options_array = array(
    'tags' => '<p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code>' ,
    'xss_clean' => true ,
    'xss_clean_die' => false ,
    'noword' => '',
    'tags' => '<p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code>' ,
    'moderate' => 0

		)   ; 

foreach ($default_options_array as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;

     
?>