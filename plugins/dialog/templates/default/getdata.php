<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

 примем POST спасибо

*/


	global $_COOKIE, $_SESSION;
 
//	if ( $post = mso_check_post(array('comment_id', 'user_id', 'creator_id')) )
	   $CI = & get_instance();
	    $ins_data = array(
                'gud_comment_id' => 1,
                'gud_user_id' => 1,
                'gud_autor_id' => 1,
                'gud_date' => 1
	           );  		
			$res = ($CI->db->insert('dgud', $ins_data)) ? '1' : '0';
		 if (!$res) echo 'Ошибка' ; else echo 'Нормально';  	

 
	
 //require ($plugin_dir . 'functions/modify_db.php');

 ?>