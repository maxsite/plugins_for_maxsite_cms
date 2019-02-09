<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// ф-я возвращает файлы пользователя с инфой об их использовании, отсортированные заданным образом
function get_userfiles($comusers_id=0 , $subdir='userfile' , $sort=1 , $only_use=false , $count=0)
// sort: 1-дата, 2-дата обратно, 3-использован, 4-использован обратно, 5-имя, 6-имя обратно
{
  $key_cache = 'get_userfiles_' . $comusers_id . '_' . $sort;
  if ($only_use) $key_cache .= '-use';
  if ($count) $key_cache .= $count;
  
  if ( $k = mso_get_cache($key_cache) ) return $k; // да есть в кэше
 
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
	$CI->load->helper('form');

	$source_pach = false; // здесь МОЖЕТ будет что-то, если юзаем не uploads/
	$uploads_url = getinfo('uploads_url');
	$subpath = $subdir . '/' . $comusers_id . '/';
	$path = getinfo('uploads_dir') . $subpath;
    $fn_mso_descriptions = $path . '_mso_i/_mso_descriptions.dat';
 
 
    $pag = false; // задел
 
  
	// все файлы в массиве $dirs
	$dirs = directory_map($path, true); // только в текущем каталоге
	if (!$dirs) $dirs = array();

    // создадим массив файлов в текущей директории
    $result = array();


	foreach ($dirs as $file)
	{
	  $file_full_path = $path . $file;
	  
		if (@is_dir($file_full_path)) continue; // это каталог
		 if ($file == 'avatar.jpg') continue; // это служебный файл
    
        $user_file = array('file'=>$file , 'comuser_id'=>$comusers_id);
    
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		// if ( !( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png')  ) continue; // запрещенный тип файла

		$file_arr = explode("." , $file);
		
		
		    // определим где использовалось в форуме
       $user_file['use'] = array();
       $user_file['use_date'] = '';
       
		    
    if ($CI->db->table_exists('dcomments'))
    {
	   $CI->db->select('comment_id , comment_date_create , ddiscussions.discussion_id , ddiscussions.discussion_title');
	   $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	   $CI->db->where('ddiscussions.discussion_approved', '1');
       $CI->db->where('comment_approved', '1');	
       $CI->db->where('comment_deleted', '0');   	   
   	   $CI->db->where('ddiscussions.discussion_private', '0');   
       $CI->db->where('comment_creator_id', $comusers_id);   	   
	   $CI->db->where(
		'(`comment_content` LIKE \'%' . $CI->db->escape_str($file) . '%\')', '', false);
	   $CI->db->order_by('comment_id', 'desc');

	   $query = $CI->db->get('dcomments');
       
  	   if ($query->num_rows() > 0)
	   {
		  $comments = $query->result_array();
		  $user_file['use'] = $comments;
          $user_file['use_date'] = $comments[0]['comment_date_create'];
	   }
    }
		
		if ($only_use and !$user_file['use']) continue;
 
        // дата файла
        $user_file['date_file'] = date ("Y-m-d" , filemtime($file_full_path));

		// выберем превьюшку
		if (file_exists( $path . '_mso_i/' . $file  ))
		    $prev = '_mso_i/';
		elseif (file_exists( $path . 'mini/' . $file  ))
	    	$prev = 'mini/';
	  else	
	    	$prev = '';

    $user_file['prev'] = $prev;

    $result[] = $user_file;
  }  

  $sort_fn = 'uploads_sort_' . $sort;    
  if (function_exists($sort_fn)) uasort($result , $sort_fn);

	mso_add_cache($key_cache, $result);
	return $result;
}


function uploads_sort_1($a, $b) //date
{
	if ( $a['date_file'] == $b['date_file'] ) return 0;
	return ( $a['date_file'] < $b['date_file'] ) ? 1 : -1;
}

function uploads_sort_2($a, $b) //date обратно
{
	if ( $a['date_file'] == $b['date_file'] ) return 0;
	return ( $a['date_file'] > $b['date_file'] ) ? 1 : -1;
}

function uploads_sort_3($a, $b) //date
{
	if ( count($a['use']) == count($b['use']) ) return 0;
	return ( count($a['use']) < count($b['use']) ) ? 1 : -1;
}

function uploads_sort_4($a, $b) //date
{
	if ( count($a['use']) == count($b['use']) ) return 0;
	return ( count($a['use']) > count($b['use']) ) ? 1 : -1;
}

function uploads_sort_5($a, $b) //date
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] < $b['file'] ) ? 1 : -1;
}

function uploads_sort_6($a, $b) //date
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] > $b['file'] ) ? 1 : -1;
}


// функция возвращает кол-во файлов в uploads каждого пользователя
function get_userfile_count()
{
	$key_cache = 'get_userfiles_count';
	if ( $k = mso_get_cache($key_cache) ) return $k; // да есть в кэше
	
  require_once( getinfo('common_dir') . 'comments.php' ); 
  $comusers = mso_get_comusers_all(); // получим всех комюзеров	
  $res = array();
  
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');

	$path = getinfo('uploads_dir') . 'userfile/';
		  
  if ($comusers)
	 foreach ($comusers as $comuser)
	 {
	    	$count = 0;    	   
	    	if (@is_dir($path . $comuser['comusers_id'])) // это каталог	  
	    	{
              $dirs = directory_map($path . $comuser['comusers_id'] , true);	
      	      foreach ($dirs as $file)
	          {
	             $file_full_path = $path . $file;
		         if (@is_dir($file_full_path)) continue; // это каталог
		         elseif ($file == 'avatar.jpg') continue; // это служебный файл	   
		         else $count++;
		      }   	
	    	}
	    	
	    	if ($count) 
	    	{
	    	   $comuser['filecount'] = $count;
	    	   $res[$comuser['comusers_id']] = $comuser;
	    	}   
	 }
	
	mso_add_cache($key_cache, $res);
	return $res;
}




?>