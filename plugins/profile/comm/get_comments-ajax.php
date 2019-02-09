<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов
// получает и возвращает события

$return = array(
		'error_code' => 1,
		'end' => 0,
		'error_description' => 'Неверные данные',
		'resp' => '0',
		'pag_no' => 0,
		'pag_c' => 0
);

/*	должно быть передано 
    type=getevents
     p_id=
     u_id=
     c_id=
     sort=
     pag_no=
     limit=
     pag_c= 
*/

if ( ($post = mso_check_post(array('type' , 'p_id' , 'u_id' , 'c_id' , 'sort' , 'pag_no' , 'limit' , 'pag_c'))))
{

  //  подготовим данные для получения массива комментариев
  
  /* нужны такие переменные
   $pag_no
   $comuser_id
   $user_id
   $page_id
   $pag_count
   $limit
   $sort
   $comments_array
  */
  
  $site_url = getinfo('siteurl');
	 
 // $options = mso_get_option('profile', 'plugins', array());
 
  if (is_numeric($post['p_id'])) $page_id = $post['p_id']; else $page_id = 0;
  if (is_numeric($post['u_id'])) $user_id = $post['u_id']; else $user_id = 0;
  if (is_numeric($post['c_id'])) $comuser_id = $post['c_id']; else $comuser_id = 0;
  if (is_numeric($post['pag_c'])) $pag_count = $post['pag_c']; else $pag_count = 0;
  if (is_numeric($post['pag_no'])) $pag_no = $post['pag_no']; else $pag_no = 1;
  if (is_numeric($post['limit'])) $limit = $post['limit']; else $limit = 20;
  if ($post['sort']) $sort = 'asc'; else $sort = 'desc';
  
  // подключим получатель комментов
 // $comments_array = array();
  $cache_key = serialize($post);
  $comments_array = mso_get_cache($cache_key);
  $pag_count = mso_get_cache('pagcommcount');
  if (!$comments_array) 
  {
     $comments_array = array();
     require( getinfo('plugins_dir') . 'profile/comm/' . 'all-get-comments.php' );
     mso_add_cache($cache_key, $comments_array);
     mso_add_cache('pagcommcount', $pag_count);
  }	    
   
    // из массива комментариев сконструируем переменную для вывода
    $out = '';
    if ($comments_array)
    {
        // подключим файл с циклом
        require(getinfo('plugins_dir') . 'profile/comm/all_foreach.php');
    }
	   if($pag_count == $pag_no) $return['end'] = 1;
       $return['error_code'] = 0;
       $return['resp'] = $out;	   
       $return['pag_c'] = $pag_count;
       
}

	echo json_encode($return);	





?>


