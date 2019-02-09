<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// форма профайла


  if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3) )
  {
     // форма для хозяина
     
        echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
     
				$CI = & get_instance();
				$CI->load->helper('form');
				
				echo '<input type="hidden" value="' . $comuser['comusers_email'] . '" name="f_comusers_email">';
				echo '<input type="hidden" value="' . $comuser['comusers_password'] . '" name="f_comusers_password">';
				
        echo '<div class="profile_block">';
				echo '<p><strong>'. t('Ник для форума'). ':</strong> <input type="text" name="f_psevdonim" value="' . $edit_profile['profile_psevdonim'] . '"></p>';
				echo '<p><strong>'. t('Подпись/девиз'). ':</strong> <input type="text" name="f_podpis" value="' . $edit_profile['profile_podpis'] . '"></p>';
				echo '<p><strong>'. t('Сообщений на странице'). ':</strong> <input type="text" name="f_comments_on_page" value="' . $edit_profile['profile_comments_on_page'] . '"></p>';
       echo '</div>';
        
        echo '<div class="profile_block">';

        $chckout = ''; 
        if ($edit_profile['profile_allow_msg']=='1') 
        {
            $chckout = 'checked="true"';
        } 	
	      echo '<H3>' . t('Разрешить отправку сообщений пользователями', 'plugins') . '</H3>';
	      echo '<p class="center"><input name="f_profile_allow_msg" type="checkbox" ' . $chckout . '></p>';
	      echo '<p>' . t('Зарегистрированные посетители форума могут отправлять сообщения наваш email (email показан не будет).', 'plugins') . '</p>';
        
        $chckout = ''; 
        if ($edit_profile['profile_allow_info']=='1') 
        {
            $chckout = 'checked="true"';
        } 	
	      echo '<H3>' . t('Разрешить отправку информационных сообщений', 'plugins') . '</H3>';
	      echo '<p class="center"><input name="f_profile_allow_info" type="checkbox" ' . $chckout . '></p>';
	      echo '<p>' . t('Разрешить письма о том, что Ваше сообщение процитировано, на него поступил ответ или оно послужило причиной новой искуссии.', 'plugins') . '</p>';
	              
        $chckout = ''; 
        if ($edit_profile['profile_allow_subscribe']=='1') 
        {
            $chckout = 'checked="true"'; 
        } 
	      echo '<H3>' . t('Получать подписки на темы', 'plugins') . '</H3>';
	      echo '<p class="center"><input name="f_profile_allow_subscribe" type="checkbox" ' . $chckout . '></p>';
	      echo '<p>' . t('Будут приходить письма при появлении новых сообщений во всех подписанных дискуссиях.', 'plugins') . ' </p>';		    
        
        echo '</div>';
		    
		    
				echo '<p><input type="submit" name="f_user_submit" class="submit" value="' .  t('Отправить') . '"></p></form>';  
  }
  else
  {
     // просто выводим инфу для всех
    $fn = 'profile_info.php';
    if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
    else 
       require($template_default_dir . $fn);    
  }

?>