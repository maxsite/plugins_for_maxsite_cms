<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

                                  

mso_checkreferer(); // защищаем реферер

if ( $post = mso_check_post(array('seotext_down')) )
{

    $options = $newoptions = mso_get_option('seotext', 'plugins', array());
    
    # обрабатываем POST
    $newoptions['down'] = ($post['seotext_down']=='true');     
    
    if ( $options != $newoptions ) 
        mso_add_option('seotext', $newoptions, 'plugins');   
    die($post['seotext_down']);        
}
die();

?>