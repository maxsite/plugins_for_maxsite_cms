<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// главная страница публичого профиля комюзера с основной сводной информацией
// соронние плагины имеют возможность добавлять на эту страницу свои блоки

mso_cur_dir_lang('templates');


  if (isset($options['pages_profiles']['0'])) $title = $options['pages_profiles']['0'];
  else $title = '';
  mso_head_meta('title', $comusers_nik . ' » ' . $title); // meta title страницы


// теперь сам вывод

# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
echo NR . '<div class="type type_users">' . NR;

// меню страниц публичного профиля
require (getinfo('plugins_dir') . 'profile/pub_pages/menu-profiles.php' );

$comuser_info = profile_get_comuser(mso_segment(2)); // получим всю информацию о комюзере - номер в сегменте url

if ($comuser_info)
{
	extract($comuser_info[0]);
	
	/*
		if ($comusers_activate_string != $comusers_activate_key) // нет активации
			echo '<p><span style="color: red;" class="comusers-no-activate">'. t('Активация не завершена.'). '</span> <a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit">'. t('Завершить'). '</a></p>';
		*/
		// выводим все данные
		
   echo NR . '<div class="profile_block">' . NR;

  $avatar_url = profile_avatar(array('comusers_avatar_url'=>$comusers_avatar_url, 'users_avatar_url'=>'', 'users_email'=>'', 'comusers_email'=>$comusers_email));

  echo '<table><tr><td>';
  if ($avatar_url) echo $avatar_url;
  echo '</td><td>';

	
		if ($comusers_date_registr) echo '<p><strong>'. t('Дата регистрации'). ':</strong> ' . $comusers_date_registr . '</p>';
		if ($comusers_nik) echo '<p><strong>'. t('Ник'). ':</strong> ' . $comusers_nik . '</p>';
		if ($comusers_count_comments) echo '<p><strong>'. t('Комментариев'). ':</strong> ' . $comusers_count_comments . '</p>';
		
		if ($comusers_url) 
		{	
	       echo '<p><strong>'. t('Сайт'). ':</strong> ';
	       if ($comusers_count_comments)  echo '<a rel="nofollow" href="' . $comusers_url . '">' . $comusers_url . '</a>';
	       else echo $comusers_url;	
           echo '</p>';		   
		}
		
		if ($comusers_icq) echo '<p><strong>'. t('ICQ'). ':</strong> ' . $comusers_icq . '</p>';
		if ($comusers_msn) echo '<p><strong>'. t('Twitter'). ':</strong> <a rel="nofollow" href="http://twitter.com/' . $comusers_msn . '">@' . $comusers_msn . '</a></p>';
		if ($comusers_jaber) echo '<p><strong>'. t('Jabber'). ':</strong> ' . $comusers_jaber . '</p>';
		
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
		
		

		
		
		
		// теперь подключим дополнительные блоки, которые определяются сторонними плагинами
		
		$cache_key = 'profiles_blocks_' . $comusers_id;
	  $profiles_blocks = mso_get_cache($cache_key);
	  //$profiles_blocks = false;
	  
	  
	  if (!$profiles_blocks and $options['profile_plugins'])
	  {
		  foreach ($options['profile_plugins'] as $cur_plugin)
		    if ($cur_plugin != 'profile')
		    {
		      $profiles_blocks .= '<div class="profile_block">';
		      // у нас есть переменная $comusers_id, по которой и будем деловарить в следующем файле
		      require( getinfo('plugins_dir') . $cur_plugin . '/profile_block.php' );
		      $profiles_blocks .= '</div>';
		    }
		  mso_add_cache($cache_key, $profiles_blocks);
	  }
	  echo $profiles_blocks;
		
}
else
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . t('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users" -->' . NR;

# конечная часть шаблона
require(getinfo('shared_dir') . 'main/main-end.php');



function profile_get_comuser($id=0)
{
	if (!$id) return array(); // нет номера, выходим
	
	if (!is_numeric($id)) return array(); // если id указан не номером, выходим


	$CI = & get_instance();

	$CI->db->select('comusers.*');
	$CI->db->from('comusers');
	$CI->db->where('comusers_id', $id);
	$CI->db->limit(1);

	$query = $CI->db->get();

	if ($query->num_rows() > 0)
	{
		$comuser = $query->result_array(); // данные комюзера

		// в секцию meta добавим все метаполя данного юзера
		$CI->db->select('meta_key, meta_value');
		$CI->db->from('meta');
		$CI->db->where('meta_table', 'comusers');
		$CI->db->where('meta_id_obj', $id);
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
		{
			// переделаем полученный массив в key = value
			foreach ($query->result_array() as $val)
			{
				$comuser[0]['comusers_meta'][$val['meta_key']] = $val['meta_value'];
			}
		}
		else
		{
			$comuser[0]['comusers_meta'] = array();
		}
		
		// от вских гадостей
		$comuser[0]['comusers_nik'] =  mso_xss_clean($comuser[0]['comusers_nik']);
		$comuser[0]['comusers_icq'] =  mso_xss_clean($comuser[0]['comusers_icq']);
		$comuser[0]['comusers_url'] =  mso_xss_clean($comuser[0]['comusers_url']);
		
		if ($comuser[0]['comusers_url'] and strpos($comuser[0]['comusers_url'], 'http://') === false) 
			$comuser[0]['comusers_url'] = 'http://' . $comuser[0]['comusers_url'];
		
		$comuser[0]['comusers_msn'] =  mso_xss_clean($comuser[0]['comusers_msn']); // twitter
		$comuser[0]['comusers_msn'] = mso_slug(str_replace('@', '', $comuser[0]['comusers_msn']));
		
		$comuser[0]['comusers_jaber'] =  mso_xss_clean($comuser[0]['comusers_jaber']);
		$comuser[0]['comusers_skype'] =  mso_xss_clean($comuser[0]['comusers_skype']);
		$comuser[0]['comusers_description'] =  mso_xss_clean($comuser[0]['comusers_description']);

		return $comuser;
	}
	else return array();
}



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


?>