<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// главная страница публичого профиля комюзера с основной сводной информацией

  extract($edit_profile);
	
  echo NR . '<div class="profile_block">' . NR;

  $avatar_url = dialog_avatar(array('comusers_avatar_url'=>$comusers_avatar_url, 'users_avatar_url'=>'', 'users_email'=>'', 'comusers_email'=>$comusers_email));

  echo '<table><tr><td>';
  if ($avatar_url) echo $avatar_url;
  echo '</td><td>';

	
	if ($comusers_date_registr) echo '<p><strong>'. t('Дата регистрации'). ':</strong> ' . $comusers_date_registr . '</p>';
	if ($comusers_nik) echo '<p><strong>'. t('Ник'). ':</strong> ' . $comusers_nik . '</p>';
	if ($comusers_count_comments) echo '<p><strong>'. t('Комментариев'). ':</strong> ' . $comusers_count_comments . '</p>';
	if ($comusers_url) echo '<p><strong>'. t('Сайт'). ':</strong> <a rel="nofollow" href="' . $comusers_url . '">' . $comusers_url . '</a></p>';
	if ($comusers_icq) echo '<p><strong>'. t('ICQ'). ':</strong> ' . $comusers_icq . '</p>';
	if ($comusers_msn) echo '<p><strong>'. t('Twitter'). ':</strong> <a rel="nofollow" href="http://twitter.com/' . $comusers_msn . '">@' . $comusers_msn . '</a></p>';
	if ($comusers_jaber) echo '<p><strong>'. t('Jabber'). ':</strong> ' . $comusers_jaber . '</p>';
		
/*  
  // блок подключенных видимых социальных аккаунтов
    if (isset($comusers_meta['providers_visible']))
    {
      $ulogin_plugin_img_url = getinfo('plugins_url') . 'profile/img/';
      // получим ссылки на разрешенных для показа провайдеров
      $providers_visible = explode(',' , $comusers_meta['providers_visible']);
      foreach ($providers_visible as $provider_key)
      {
         // выводим присоединенный профиль
         $img = '<img src="'.$ulogin_plugin_img_url . $provider_key . '.png"> ';
         echo (isset($comusers_meta[$provider_key . '_profile']) and $comusers_meta[$provider_key . '_profile']) ? '<a href="'.$comusers_meta[$provider_key . '_profile'].'" alt="'.$comusers_meta[$provider_key . '_profile'].'" tite="'.$comusers_meta[$provider_key . '_profile'].'">' . $img . '</a>' : $img;         
      }
    }		
*/		
		if ($comusers_date_birth and $comusers_date_birth!='1970-01-01 00:00:00' and $comusers_date_birth!='0000-00-00 00:00:00'   ) 
				echo '<p><strong>'. t('Дата рождения'). ':</strong> ' . $comusers_date_birth . '</p>';
		echo '</td></tr></table>';

		if ($comusers_description) 
		{
			$comusers_description = strip_tags($comusers_description);
			$comusers_description = str_replace("\n", '<br>', $comusers_description);
			$comusers_description = str_replace('<br><br>', '<br>', $comusers_description);
			
			echo '<p><strong>'. t('О себе'). ':</strong> ' . $comusers_description . '</p>';
		}
    
    echo NR . '</div><!-- class="profile_block" -->' . NR;
		

	echo '<div class="profile_block">';
	$profiles_blocks = '';
	require( $plugin_dir . 'profile_block.php' );
	echo $profiles_blocks;
    echo '</div>';

/*

function profile_avatar($comment, $img_add = 'style="float: left; margin: 5px 10px 10px 0;" class="gravatar"', $echo = false)
{
	extract($comment);

	$avatar_url = '';
	if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
	elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
	
	$avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
	if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;
	
	if (!$avatar_url) 
	{ 
		// аватарки нет, попробуем получить из gravatara
		if ($users_email) $grav_email = $users_email;
		elseif ($comusers_email) $grav_email = $comusers_email;
		else $grav_email = '';
		
		if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
			$d = '&amp;d=' . urlencode($gravatar_type);
		else 
			$d = '';
		
		if (!empty($_SERVER['HTTPS'])) 
		{
		   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		} 
		else 
		{
		   $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		}
	}
	
	if ($avatar_url) 
		$avatar_url =  '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" '. $img_add . '>';
	
	if ($echo) echo $avatar_url;	
		else return $avatar_url;
}
*/

?>