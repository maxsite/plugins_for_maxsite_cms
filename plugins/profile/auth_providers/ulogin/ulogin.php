<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Залогиневание ulogin_auth 
 */

	   if (!isset($options['widget_type'])) $options['widget_type'] = 'small'; 
       $widget_type =  $options['widget_type'];
	
	   if (!isset($options['auth_title']) or empty($options['auth_title'])) $options['auth_title'] = 'ulogin';  
	   $auth_title = $options['auth_title'];
	
	   if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,mailru,yandex,twitter';
	   $providers_set = $options['providers_set'];
	
	   if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,facebook,livejournal,youtube';
	   $providers_hidden = $options['providers_hidden'];	

	   if (!isset($options['fields'])) $options['fields'] = '';  
        if ($options['fields']) $fields = 'fields=' . $options['fields'] . ';';
        else $fields = '';

  	    $curpage = getinfo('siteurl') . mso_current_url();
	    $current_url = getinfo('siteurl') . 'maxsite-ulogin-auth?' . $curpage;
/*	    if ( ($widget_type == 'small') or ($widget_type == 'panel'))
        $out1 = '
          <div id="uLogin2"x-ulogin-params="display='
          . $widget_type . ';'. $fields .'optional=first_name,last_name,photo,nickname,email;providers='.$providers_set.';hidden='.$providers_hidden.';redirect_uri='
          . urlencode( $current_url ) . '"></div>';
        else
          $out1 = '
           <a href="#" id="uLogin2" x-ulogin-params="display=window;'. $fields .'optional=first_name,last_name,photo,nickname,email;redirect_uri='
           . urlencode( $current_url ) . '"><img src="http://ulogin.ru/img/button.png" width=187 height=30 alt="МультиВход"/></a>'; 
*/	
	    $out1 = '
<div data-ulogin="display='
 . $widget_type . ';theme=classic;'.$fields.'optional=first_name,last_name,photo,nickname,email;providers='.$providers_set.';hidden='.$providers_hidden.';redirect_uri='.urlencode( $current_url).';mobilebuttons=0;"></div>';

# end file
