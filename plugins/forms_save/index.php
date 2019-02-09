<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Дополнения: Колесников А.А.
 */


# функция выполняется при активации (вкл) плагина
/*function forms_save_activate($args = array())
{	
	$CI = & get_instance();	

	if ( !$CI->db->table_exists('forms')) // нет таблицы forms
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "forms (
		form_id bigint(20) NOT NULL auto_increment,
		form_ip varchar(255) NOT NULL default '',
		form_browser varchar(255) NOT NULL default '',
		form_date datetime default NULL,
		form_name varchar(255) NOT NULL default '',
		form_email varchar(255) NOT NULL default '',
		form_text longtext,
		form_refer varchar(255) NOT NULL default '',
		form_json_data longtext,
		PRIMARY KEY (form_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
		
	return $args;
}
*/

# функция выполняется при деинстяляции плагина
/*
function forms_save_uninstall($args = array())
{	
	$CI = & get_instance();	

	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('forms');

	return $args;
}
*/


# функция автоподключения плагина
function forms_save_autoload($args = array())
{
	mso_hook_add( 'forms_send', 'forms_save'); 
	return $args;
}


function forms_save( $arg = array() )
{
	$post = $arg["post"];
	$fields = $ar["fields"];

	$CI = & get_instance();	

	$json_data = '{ "' . t('Имя') .'": "'. addcslashes($post['forms_name'], "\"\\" ) . '", ';
	$json_data .= '"' . t('Email') .'": "'.  addcslashes($post['forms_email'], "\"\\" ) . '", ';

	foreach ($post['forms_fields'] as $key=>$val)
	{
		$json_data .= '"' . $fields[$key]['description'].'": "'. addcslashes( $val, "\"\\" ) . '", ';
	}
	$json_data .= '}';

	$CI = &get_instance();
	// данные для новой записи
	$ins_data = array (
		'form_date' => date('Y-m-d H:i:s'),
		'form_ip' => $_SERVER['REMOTE_ADDR'] ,
		'form_browser' => $_SERVER['HTTP_USER_AGENT'],
		'form_name' => $post['forms_name'] ,
		'form_email' => $post['forms_email'],
		//'form_text' => $message,
		'form_refer' =>  $_SERVER['HTTP_REFERER'],
		'form_json_data' =>  $json_data,
	);
	// pr($ins_data);
	$res = ($CI->db->insert('forms', $ins_data)) ? '1' : '0';
}
?>
