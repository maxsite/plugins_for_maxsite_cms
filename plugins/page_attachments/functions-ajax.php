<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	mso_checkreferer(); // защищаем реферер

	require_once( getinfo('common_dir') . 'meta.php' );
	
	$CI = & get_instance();

	if ( $post = mso_check_post(array('del')) )
	{
		$ar = explode('-', $post['del']);

		$file = mso_get_meta($ar[0], 'page', $ar[1]);//array('field' => $ar[0], 'id' => $ar[1]);
		//TODO: check if exists
	
		$site_url = getinfo('site_url');
		$fcpath = getinfo('FCPATH');
		$file_ref = $fcpath.str_replace($site_url, '', $file[0]['meta_value']);

		if(@unlink($file_ref)){
			//remove db record
			$CI->db->delete('mso_meta', array('meta_id_obj' => $ar[1], 'meta_key' => $ar[0]));
			$result = array('status' => 'deleteOk', 'message' => t('Файл удален!', 'plugins'));
			//return deleted - ok
		} else {
			$result = array('status' => 'deleteErr', 'message' => t('Не удалось удалить файл!', 'plugins'), 'dd' => $file_ref);
			//return error
		}
		
		echo json_encode($result);
	} else die('Error AJAX');
?>