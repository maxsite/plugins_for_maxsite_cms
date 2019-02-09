<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!is_login()) die('no login');
if (!mso_check_allow('admin_page_edit')) die('no allow');

$return = array(
		'error_code' => 1,
		'end' => 0,
		'error_description' => 'Неверные данные',
		'resp' => '0',
		'pag_no' => 0,
		'pag_c' => 0
);


if ( ($post = mso_check_post(array('type' , 'p_id'))))
{
	 mso_checkreferer(); // защищаем реферер

     if (is_numeric($post['p_id'])) $page_id = $post['p_id']; else $page_id = 0;

     $out ='';
      
     $uploads_dir = getinfo('uploads_dir');
	 $uploads_url = getinfo('uploads_url');
	
      // получим page_content
	  $CI = & get_instance();	  
	  $CI->db->select('page_content');
	  $CI->db->where('page_id', $page_id);
	  $query = $CI->db->get('page');
	  if ($query->num_rows() > 0)
	  {
	     $pages = $query->result_array();
	     $content = $pages[0]['page_content'];
	     
		 $content = mso_hook('content_init', $content);
		 $content = mso_hook('content_in', $content);
		 $content = mso_hook('content', $content);
		 $content = mso_hook('content_auto_tag', $content);
		 $content = mso_hook('content_balance_tags', $content);
		 $content = mso_hook('content_out', $content);
		 $content = mso_hook('content_complete', $content);	  
		 $content =  mso_hook('content_content', $content);
		    
         preg_match_all('/(href|src)=("|\')[^"\'>]+/i', $content, $images);
         $data = preg_replace('/(href|src)("|\'|="|=\')(.*)/i', "$3", $images[0]);
 
         if($data)
         {
         foreach ($data as $url)
         {
            $info = pathinfo($url);
            if (!isset($info['extension']) or !isset($info['filename']) or !isset($info['dirname'])) continue;
            else $ext = $info['extension'];
        
            if ( !in_array($ext, array('jpg', 'jpeg', 'gif', 'png') ))  continue;

            $pos = strripos($url, '/mini/');
            if ($pos !== false) continue;

            $pos = strripos($url, '/_mso_i/');
            if ($pos !== false) continue;    

            $dirs = explode("/uploads", $info['dirname']);
           
            if (!isset($dirs[1])) continue ;
            else $info['dirname'] =  $dirs[1]; 
            
            $path = $dirs[1] . '/mini/' . $info['filename'] . '.' .  $info['extension'];    
                  
            $fn_mini =   $uploads_dir . $path;
            $fn_mini = str_replace('//' , '/' , $fn_mini );
            
			if (!file_exists($fn_mini)) 
			   $url_mini = $url;   
			else 
			   $url_mini = $uploads_url . $path;
            
            $url_mini = str_replace('//uploads' , '/uploads' , $url_mini );
            $url_mini = str_replace('uploads//' , 'uploads/' , $url_mini );
			
            $img_out = '<img class="file_img" alt="" src="' . $url_mini . '">';    
			$out .= '<div class="cornerz"><div class="wrap"><p><a title="' . $url . '" href="javascript: void(0);" onclick="addImgPage(\'' .$url . '\');">'. $img_out.'</a></p></div></div>';	
         }	 
         }
         
	  }
               
      // все, связанное с директориями, в отдельный файл
      $segments = '_pages/' . $page_id;
      require(getinfo('plugins_dir') . 'page_img_edit/admin/files.php');
      $return['dirs'] = $out_dirs; // список директорий
      $return['cur_dir'] = $found; // текущая
             	  
      $return['error_code'] = 0;
      $return['resp'] = $out;	 
}

	echo json_encode($return);	

# end file


