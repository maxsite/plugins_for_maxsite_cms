<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
	
 //опции 
$options = mso_get_option('dialog' , 'plugins', array());
require($plugin_dir . 'plugin_options_default.php');
  
require($plugin_dir . 'templates/' . $options['template'] . '/info_messages.php');




	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	  
	  foreach ($messages as $key=>$message)
	  {
		   if ( isset($post[$key]) ) $options_messages[$key] = $post[$key];
	  }
		mso_add_option('dialog_messages', $options_messages , 'plugins');
	  
  	$info = 'Обновлено' ;
		echo '<div class="update">' . t($info, 'plugins') . '!</div>';
	}

	
        
		$form = '';
		$form .= '<H3>' . t('Настройки выводимых сообщений.', 'plugins') . '</H3>';
		$form .= '<p>' . t('Ключ опции текста (дефолтное значение).', 'plugins') . '</p>';
		
    foreach ($messages as $key=>$message)
    {
          if (!isset($options_messages[$key])) $options_messages[$key] = $message;
		  $form .= '<div class="admin_plugin_options">';
		  $form .= '<strong>' . $key . '</strong>';
		  $form .= '<input name="' . $key . '" type="text" value="' . htmlspecialchars($options_messages[$key]) . '">';		
		  $form .= '<em>' . htmlspecialchars($messages[$key]) . '</em>';
	    $form .= '</div>';
    }
	
   $form .= '<tr><td><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" /></td></tr>';
   
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
        echo $form;
		echo '</form>';

?>