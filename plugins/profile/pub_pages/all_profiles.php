<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод списка всех комюзеров

// $options['pages_main'] = isset($options['pages_main']) ? $options['pages_main'] : array('0'=>'Все пользователи' , 'all' => 'Все действия');	  


mso_cur_dir_lang('templates');


 require_once( getinfo('common_dir') . 'comments.php' ); 
 // require (getinfo('plugins_dir') . 'profile/functions_avatar.php' );

 $comusers = get_all_profiles(); // получим всех комюзеров

 mso_head_meta('title', t('Пользователи') . ' ' . getinfo('title')); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');

echo NR . '<div class="type type_users_all">' . NR;

// главное меню главной страницы профилей
require (getinfo('plugins_dir') . 'profile/menu-main.php' );
	// pr($comusers);
	
if ($comusers)
{
	echo '<table class="profiles_table">';
 	echo '<tr><th>Пользователь</th><th>Регистрация/<br>Последняя активность</th><th>Действия</th><th>Ссылки</th></tr>';
 	echo '<tbody>';          	
 	foreach ($comusers as $profile)
 	{
 	  extract ($profile);
	
  	  if (!$comusers_nik) $comusers_nik = t('Пользователь'). ' ' . $comusers_id;
	
	  $profile_page_url = getinfo('siteurl') . $options['profiles_slug'] . '/' . $comusers_id;
		
	  $activ = '';
	  if ($comusers_count_comments) $activ .= '<a href="' . $profile_page_url . '/comments" title="Перейти к просмотру комментариев"><img src="' . getinfo('plugins_url') . 'profile/img/comment.png"> ' . $comusers_count_comments . '</a><br>';
 	  if (isset($meta['uplcount']) and ($meta['uplcount'])) $activ .= '<a href="' . $profile_page_url . '/files" title="Перейти к просмотру загрузок"><img src="' . getinfo('plugins_url') . 'profile/img/img.png"> ' . $meta['uplcount'] . '</a>';
 	  
 	  $links = '';  
	  if ($comusers_url) $links .= '<a href="' . $comusers_url . '">' . $comusers_url . '</a><br>';

      if (isset($meta['providers_visible']))
      {
        $ulogin_plugin_img_url = getinfo('plugins_url') . 'profile/img/';
        // получим ссылки на разрешенных для показа провайдеров
        $providers_visible = explode(',' , $meta['providers_visible']);
        foreach ($providers_visible as $provider_key)
        {
           // выводим присоединенный профиль
           $img = '<img src="'.$ulogin_plugin_img_url . $provider_key . '.png"> ';
           $links .= (isset($meta[$provider_key . '_profile']) and $meta[$provider_key . '_profile']) ? '<a href="'.$meta[$provider_key . '_profile'].'" alt="'.$meta[$provider_key . '_profile'].'" tite="'.$meta[$provider_key . '_profile'].'">' . $img . '</a>' : $img;         
        }
      }		 
 
 	  $date = explode(" ",$comusers_date_registr);
 	  $comusers_date_registr = $date[0];
 	  
 	  $date = explode(" ",$comusers_last_visit);
 	  $comusers_last_visit = $date[0]; 	  
 	  
 	  echo 
 	  '<tr><td><a href="' . $profile_page_url . '">' . mso_avatar(array('comusers_avatar_url'=>$comusers_avatar_url , 'users_avatar_url'=>'', 'comusers_email'=>$comusers_email, 'users_email'=>'')) . '</a>' .
 	  '</td><td>' . $comusers_date_registr . '<br>' . $comusers_last_visit . 
 	  
 	  '</td><td><p><a href="' . $profile_page_url . '">' . $comusers_nik  . '</a></p>' . $activ . 
 	  '</td><td>' . $links . '</td></tr>';
 	  
 	}
 	
 	echo '</tbody></table>';
	
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
		echo '<p>' . t('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users_all" -->' . NR;

# конечная часть шаблона
require(getinfo('shared_dir') . 'main/main-end.php');



# список всех комюзеров с пагинацией и сортировкой
function get_all_profiles($args = array())
{

	$cache_key = 'get_all_profiles';
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$comusers = array();
	$CI = & get_instance();

	$CI->db->select('*');
	$CI->db->from('comusers');
	$CI->db->order_by('comusers_count_comments' , 'desc');
	$CI->db->order_by('comusers_avatar_url' , 'desc');
	
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)
	{
		$comusers = $query->result_array();
		mso_add_cache($cache_key, $comusers);
	}
	
	// получим все мета одним запросом
	$CI->db->select('meta_id_obj, meta_key, meta_value');
	$CI->db->where('meta_table', 'comusers');
	$CI->db->order_by('meta_id_obj');
	
	$query = $CI->db->get('meta');
	
	if ($query->num_rows() > 0)
		$all_meta = $query->result_array();
	else 
		$all_meta = array();
	
	// переделываем формат массива, чтобы индекс был равен номеру комюзера
	$r_array = array();
	
	foreach ($all_meta as $val)
	{
		$r_array[$val['meta_id_obj']][$val['meta_key']] = $val['meta_value'];
	}
	
	$all_meta = $r_array;
	
	
	// добавляем в каждого комюзера элемент массива meta, comments и comments_pages_id
	$r_array = array();
	foreach ($comusers as $key=>$val)
	{
		$r_array[$key] = $val;
		
		if (isset($all_meta[$val['comusers_id']])) 
			$r_array[$key]['meta'] = $all_meta[$val['comusers_id']];
		else 
			$r_array[$key]['meta'] = array();
		
	}
	
	$comusers = $r_array;	
	
	mso_add_cache($cache_key, $comusers);
	
	return $comusers;
}

	
?>