<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	//********************************************************************
	//********** страница отображения альбома с фотографиями *************
	//********************************************************************
	$options_key = 'admin_fotki';
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['upload_path']) ) $options['upload_path'] = 'fotki';
	$foto_dir = $options['upload_path'];
	
	global $foto_gallery;
?>


<?php
	$pagintaion_url = mso_segment(2);
	$pagination_page = mso_segment(3);
	# начальная часть шаблона
	require(getinfo('template_dir') . 'main-start.php');

	$foto_album_id = false;
	
	require_once( getinfo('plugins_dir') . $plug_url . '/gallery.php' );
	
	# конечная часть шаблона
	require(getinfo('template_dir') . 'main-end.php');		
?>