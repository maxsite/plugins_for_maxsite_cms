<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

 	echo '<h1>'. t('Плагин profile'). '</h1><p class="info">'. t('Статистика привязанных социальных аккаунтов на сайте.'). '</p>';
 	
 	// получим все аккаунты
 	$comusers = profile_get_comusers();
 	
 	echo '<table>';
 	foreach ($comusers as $comuser)
 	{
 	  echo '<tr><td>';
 	  if (isset($comuser['comusers_avatar_url'])) echo '<img src="' . $comuser['comusers_avatar_url'] . '">';
 	  echo $comuser['comusers_nik'];
 	  echo '</td><td>' . $comuser['comusers_date_registr'];
 	  echo '</td><td>' . $comuser['comusers_last_visit'];
 	  echo '</td><td>';
	  
	  
 	  foreach ($comuser['meta'] as $provider)
 	    echo '<p><a href="' . $provider . '">' . $provider . '</a></p>';
 	  echo '</td></tr>';
 	}
 	echo '</table>';
 	

# список всех комюзеров
function profile_get_comusers($args = array())
{
	$cache_key = mso_md5('profile_get_comusers');
	$k = mso_get_cache($cache_key);
//	if ($k) return $k; // да есть в кэше

	$comusers = array();
	$CI = & get_instance();

	$CI->db->select('*');
	$CI->db->from('comusers');
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
		$comusers = $query->result_array();
		
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
	  // если это метаполе провайдера
	  if (strpos($val['meta_key'], '_profile'))
	  {
	    if (!isset($r_array[$val['meta_id_obj']])) $r_array[$val['meta_id_obj']] = array();
		  $r_array[$val['meta_id_obj']][] = $val['meta_value'];
		}  
	}
	
	$all_meta = $r_array;
	
	// добавляем в каждого комюзера элемент массива meta
	$r_array = array();
	foreach ($comusers as $key=>$val)
	{
		if (isset($all_meta[$val['comusers_id']])) 
		{
		  $val['meta'] = $all_meta[$val['comusers_id']];
		  $r_array[] = $val;
    }
	}
	
	$comusers = $r_array;	

	mso_add_cache($cache_key, $comusers);
	
	return $comusers;
}

?>