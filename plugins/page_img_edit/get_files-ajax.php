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

if ( ($post = mso_check_post(array('type' , 'dir'))))
{
 	mso_checkreferer(); // защищаем реферер
	
	$current_dir = $post['dir'];
	$out = '';
	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;
	
	$CI = & get_instance();
	$CI->load->helper('directory');
	$CI->load->helper('file');
	$dirs = directory_map($uploads_dir, 2); // только в текущем каталоге
	if (!$dirs) $dirs = array();

	asort($dirs);
	
	foreach ($dirs as $file)
	{
		if (is_array($file)) continue; // каталог — это массив — нам здесь не нужен
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		
		$this_img = ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png');
		
		$url = $uploads_url . $file;
		
		if (file_exists($uploads_dir . 'mini/' . $file)) 
			$url_mini = $uploads_url . 'mini/' . $file;
        else
            $url_mini = $url;
       
        $url_mini = str_replace('//' , '/' , $url_mini );
        $url = str_replace('//' , '/' , $url );
        
        $img_out = '<img class="file_img" alt="" src="' . $url_mini . '">';    
		$out .= '<div class="cornerz"><div class="wrap"><p><a title="' . $url . '" href="javascript: void(0);" onclick="addImgPage(\'' . $url . '\');">'. $img_out.'</a></p></div></div>';	
    }		
	$return['goto_files']  =  '<a id="goto_files" href="' . getinfo('site_admin_url') . 'files/' . $current_dir . '" target="_blank" class="goto-files"><img src="' . getinfo('plugins_url') . 'page_img_edit/images/goto.png' . '"></a></a></p>';

    $return['error_code'] = 0;
    $return['resp'] = $out;	 
}

echo json_encode($return);	



# end file