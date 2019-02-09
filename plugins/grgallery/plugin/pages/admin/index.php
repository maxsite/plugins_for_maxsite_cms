<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	mso_cur_dir_lang('admin');
	
	if ( !mso_check_allow('grgallery_edit') ) 
	{
	 echo t('Доступ запрещен', 'plugins/grgallery');
	 return;
	}


	$CI = & get_instance();	 // получаем доступ к CodeIgniter
	$CI->load->helper('form');	// подгружаем хелпер форм
	//require_once ($MSO->config['plugins_dir'].'grgallery/common/admcom.php');	// подгружаем библиотеку для админки
	require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	$out='';
	
	$new_dir = getinfo('uploads_dir').$grgll['uploads_pict_dir'].'/'.$grgll['prefix'];
	
	// удаление выбранных страниц
	if ($post = mso_check_post(array('f_session_id', 'postdell')))
		{
			mso_checkreferer();
			delete_pages($post['postdell']);
			
		}
		
	// удаление всех страниц текущей категории
	if ($post = mso_check_post(array('f_session_id', 'delall')))
		{
			mso_checkreferer();
			$all_page_id = @unserialize($post['all_page_id']);
			delete_pages($all_page_id);
			mso_flush_cache();
			mso_redirect('admin/grgallery/pages/'.$post['slug']);
		}

	// редирект на создание новой страницы
	if ($post = mso_check_post(array('f_session_id', 'toadd')))
		{
			mso_checkreferer();
			mso_redirect('admin/page_new');
		}		
	
	// вывод списка страниц
	mso_flush_cache();
	require_once($MSO->config['plugins_dir'] . 'grgallery/plugin/pages/admin/list.php');
	echo $out;
	
	if (mso_segment(3) != 'pages') $pagination['next_url'] = 'pages/next';
	mso_hook('pagination', $pagination);
?>