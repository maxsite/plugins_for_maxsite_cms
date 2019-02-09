<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

	if ( !mso_check_allow('grshop_edit') ) 
	{
	 echo 'Доступ запрещен';
	 return;
	}

	$CI = & get_instance();	 // получаем доступ к CodeIgniter
	$CI->load->helper('form');	// подгружаем хелпер форм
	require_once ($MSO->config['plugins_dir'].'grshop/common/admcom.php');	// подгружаем библиотеку для админки
	require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grshop/common/csv.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные

	$out='';



// Обработка пост-запроса из этой формы
if ($post = mso_check_post(array('f_session_id', 'predexport')))
	{
	mso_checkreferer();
	$f_userfile = ''; if (isset($post['f_userfile'])) $f_userfile = $post['f_userfile'];
	$qfadd = ''; if (isset($post['qfadd'])) $qfadd = $post['qfadd'];	// это если надо больше характеристик, чем CSV-полей

/*	if ($post['data_tip'] == 'pictzip')
		{
		echo 'экспортируем архив';
		require_once ($MSO->config['plugins_dir'].'grshop/common/pclzip.lib.php');	// подгружаем библиотеку
		
		$upload_path = getinfo('uploads_dir').$grsh['uploads_pict_dir'].'/arh';
		$unzip_puth = getinfo('uploads_dir').$grsh['uploads_pict_dir'];

		$config['allowed_types'] = 'zip|txt';				// разрешенные типы файлов
		$config['upload_path'] = $upload_path;
		$CI->load->library('upload', $config);

		if (isset($_FILES['f_userfile']['name'])) 
		{
			$f_temp = $_FILES['f_userfile']['name'];
			// оставим только точку
			$f_temp = str_replace('.', '__mso_t__', $f_temp);
			$f_temp = mso_slug($f_temp); // остальное как обычно mso_slug
			$f_temp = str_replace('__mso_t__', '.', $f_temp);
			$_FILES['f_userfile']['name'] = $f_temp;
			$f_userfile = $_FILES['f_userfile']['name'];
		}

		$res = $CI->upload->do_upload('f_userfile');
		echo $zipfile = $upload_path.'/'.$f_userfile;
		//echo $zipfile = $MSO->config['uploads_dir'].'/'.$f_userfile;

		//if ( !file_exists('pclzip.lib.php') ) die('Not found pclzip.lib.php');
		if ( !file_exists($zipfile) ) die('Not found '.$zipfile);
	
		$archive = new PclZip($zipfile);
		if ($archive->extract($unzip_puth) == 0) die("Error : " . $archive->errorInfo(true));
		else echo t('Куда-то разархивировалось');

		return;
		}
*/



	$data=array(	'path_form_action'=>$plugin_url .'/export', 
			'name_form_sub'=>'export', 
			'name_tabl_db'=>'grsh_prod', 
			'delimiter'=>$post['delimiter'],
			'data_tip'=>'prod',
			'name_title'=>'товаров ',
			'f_userfile' => $f_userfile,
			'qfadd' => $qfadd );

	if ($post['data_tip'] == 'cat')
		{
		$data=array(	'path_form_action'=>$plugin_url .'/export', 
				'name_form_sub'=>'export', 
				'name_tabl_db'=>'grsh_cat', 
				'delimiter'=>$post['delimiter'],
				'data_tip'=>'cat',
				'name_title'=>'категорий ',
				'f_userfile' => $f_userfile,
				'qfadd' => $qfadd);
		};
	if ($post['data_tip'] == 'act')
		{
		$data=array(	'path_form_action'=>$plugin_url .'/export', 
				'name_form_sub'=>'export', 
				'name_tabl_db'=>'grsh_act', 
				'delimiter'=>$post['delimiter'],
				'data_tip'=>'act',
				'name_title'=>'акций ',
				'f_userfile' => $f_userfile,
				'qfadd' => $qfadd);
		};

	$res = form_conform_field($data);	// вывод страницы сопоставления полей
	if ($res == 'nofile')  mso_redirect('admin/grshop/export/err/nofile');
	$out .= $res;
	echo  $out;
	return;
	}

// собственно экспорт данных из файла в базу данных
if ($post = mso_check_post(array('f_session_id', 'export')))
	{
	mso_checkreferer();
	require_once ($MSO->config['plugins_dir'].'grshop/common/csv.php');		// подгружаем библиотеку работы с csv
	$res=export_csv_db($post);	// экспорт
	if ($res == 'nocat') mso_redirect('admin/grshop/export/err/nocat');
	}

// отмена экспорта из файла в базу данных
if ($post = mso_check_post(array('f_session_id', 'canselcsv')))
	{
	mso_checkreferer();
	$csvfile = $MSO->config['uploads_dir'] .'/'.$post['f_userfile'];  	// получаем путь к временному файлу, загруженному для импорта
	if (file_exists($csvfile)) unlink($csvfile); 	// если файл существует, удаляем его
	}

// экспорт архива изображений
//if ($post = mso_check_post(array('f_session_id', 'canselcsv')))
//	{
//	mso_checkreferer();
//	$csvfile = $MSO->config['uploads_dir'] .'/'.$post['f_userfile'];  	// получаем путь к временному файлу, загруженному для импорта
//	if (file_exists($csvfile)) unlink($csvfile); 	// если файл существует, удаляем его
//	}

//if ($post = mso_check_post(array('f_session_id', 'toexportcsv')))	//-- потом будут другие способы экспорта....
//	{


//-------------------форма выбора файла для экспорта--------------------

	mso_checkreferer();
	if (mso_segment(5) == 'nocat')	$out .= '<p class="info">'.t('Вы не выбрали КАТЕГОРИЮ для загрузки данных о товарах ', 'admin').'</p>';
	if (mso_segment(5) == 'nofile')	$out .= '<p class="info">'.t('Вы не указали CSV-ФАЙЛ для загрузки, или у вас нет прав на его чтение ', 'admin').'</p>';
	$out .= form_upload_file($data=array('path_form_action'=>$plugin_url .'/export', 'descr_head'=>t('Выбор CSV-файла для экспорта', 'admin'), 'name_form_sub' => 'predexport'));
	echo $out;
	return;


//	}

	echo $out;

?>