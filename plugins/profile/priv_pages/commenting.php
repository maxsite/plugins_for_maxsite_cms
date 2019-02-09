<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // сраницы, на которых оставлены комментарии
  
  if (isset($options['pages'][mso_segment(2)])) $title = $options['pages'][mso_segment(2)];
  else $title = '';
  mso_head_meta('title', $options['title'] . ' » ' . $title); // meta title страницы

  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type type_users_form">' . NR;

  require (getinfo('plugins_dir') . 'profile/priv_pages/menu.php' );


 if ($commenting = get_commenting_pages($comusers_id))
 {
   echo NR . '<div class="profile_block">' . NR; 
   echo '<H3>Оставлено комментариев в статьях:</H3>';
   $link = '<a href="' . getinfo('siteurl') . 'page/';
   foreach ($commenting as $comm)
     echo '<p>' . $link . $comm['page_slug'] . '">' . $comm['page_title'] . '</a> (' . $comm['comments_count'] . ')</p>';
   echo '<H3><a href="' . getinfo('siteurl') . $options['profiles_slug'] . '/' . $comusers_id . '/comments">' . 'Смотреть комментарии' . '</a></H3>';
   echo '</div>';
 } 
 else echo '<p>Еще не прокомментировано ни одной статьи</p>';
  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;
	require(getinfo('shared_dir') . 'main/main-end.php');


// функция возвращает массив всех записей, которые комментировал комюзер
function get_commenting_pages($comuser_id=0)
{

	$cache_key = 'commenting_pages_' . $comuser_id;
	$k = mso_get_cache($cache_key);
	if ($k) return $k;

	$CI = & get_instance();
	$CI->db->select('page.page_id, page.page_slug, page.page_title, COUNT(comments_id) as comments_count');
	$CI->db->from('comments');
	$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	$CI->db->where('comments.comments_approved', '1');
	$CI->db->where('comments.comments_comusers_id', $comuser_id);
	$CI->db->group_by('page.page_id');
	$CI->db->order_by('comments_count' , 'desc');
	$query = $CI->db->get();
	if ($query->num_rows() > 0) 
	{
		$commenting_pages = $query->result_array(); 
		mso_add_cache($cache_key, $commenting_pages); // в кэш
		return $commenting_pages;
	}
  else return array();
}




?>