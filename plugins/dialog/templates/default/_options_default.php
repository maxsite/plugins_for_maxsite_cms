<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле инициализируются дефолтные опции шаблона 

     
  $default_options_array = array(

    'breadcrumbs_razd' => ' >> ' ,

		)   ; 

foreach ($default_options_array as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;

     
?>