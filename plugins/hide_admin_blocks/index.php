<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function hide_admin_blocks_autoload()
{
	if ( mso_segment(1) == 'admin' and (mso_segment(2) == 'page_new' or mso_segment(2) == 'page_edit') ) 
	{
		mso_hook_add('admin_head', 'hide_admin_blocks_custom');	
	}
}

# функция выполняется при деинсталяции плагина
function hide_admin_blocks_uninstall($args = array())
{	
	mso_delete_option('plugin_hide_admin_blocks', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function hide_admin_blocks_mso_options() 
{
	mso_admin_plugin_options('plugin_hide_admin_blocks', 'plugins', 
		array(
			'page_status' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок статуса страницы', 
							'description' => '', 
							'default' => '1'
						),
			'page_files' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать ссылку на Загрузки', 
							'description' => '', 
							'default' => '1'
						),			
			'page_meta' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок мета', 
							'description' => '', 
							'default' => '1'
						),	
			'page_all_cat' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок рубрик', 
							'description' => '', 
							'default' => '1'
						),
			'page_tags' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок меток', 
							'description' => '', 
							'default' => '1'
						),
			'page_slug' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок короткой ссылки', 
							'description' => '', 
							'default' => '1'
						),
			'page_discus' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок обсуждения', 
							'description' => '', 
							'default' => '1'
						),
			'page_date' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок выбора даты', 
							'description' => '', 
							'default' => '1'
						),
			'page_post_type' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок типа страницы', 
							'description' => '', 
							'default' => '1'
						),
			'page_password' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок пароля', 
							'description' => '', 
							'default' => '1'
						),
			'page_menu_order' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок порядка страницы', 
							'description' => '', 
							'default' => '1'
						),
			'page_all_parent' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок родительской страницы', 
							'description' => '', 
							'default' => '1'
						),
			'page_all_users' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок выбора автора', 
							'description' => '', 
							'default' => '1'
						),
			),
		'Настройки блоков', // титул
		'Отметьте тем блоки, которые следует выводить на странице создания/редактирования записей.'   // инфо
	);

}

# функции плагина
function hide_admin_blocks_custom($arg = array())
{
	$options = mso_get_option('plugin_hide_admin_blocks', 'plugins', array());
	
	$css = '';
	if ( isset($options['page_status']) and !$options['page_status']) $css .= 'p.page_status {display: none !important;}' .NR ;
	if ( isset($options['page_files']) and !$options['page_files']) $css .= 'a.page_files {display: none !important;}' .NR ;
	
	if ( isset($options['page_meta']) and !$options['page_meta']) $css .= 'div.page_meta {display: none !important;}' .NR ;
	if ( isset($options['page_all_cat']) and !$options['page_all_cat']) $css .= 'div.page_all_cat {display: none !important;}' .NR ;
	if ( isset($options['page_tags']) and !$options['page_tags']) $css .= 'div.page_tags {display: none !important;}' .NR ;
	if ( isset($options['page_slug']) and !$options['page_slug']) $css .= 'div.page_slug {display: none !important;}' .NR ;
	if ( isset($options['page_discus']) and !$options['page_discus']) $css .= 'div.page_discus {display: none !important;}' .NR ;
	if ( isset($options['page_date']) and !$options['page_date']) $css .= 'div.page_date {display: none !important;}' .NR ;
	if ( isset($options['page_post_type']) and !$options['page_post_type']) $css .= 'div.page_post_type {display: none !important;}' .NR ;
	if ( isset($options['page_password']) and !$options['page_password']) $css .= 'div.page_password {display: none !important;}' .NR ;
	if ( isset($options['page_menu_order']) and !$options['page_menu_order']) $css .= 'div.page_menu_order {display: none !important;}' .NR ;
	if ( isset($options['page_all_parent']) and !$options['page_all_parent']) $css .= 'div.page_all_parent {display: none !important;}' .NR ;
	if ( isset($options['page_all_users']) and !$options['page_all_users']) $css .= 'div.page_all_users {display: none !important;}' .NR ;
	
	
	if ($css)
	{
		echo NR . '<style>' . NR . $css . '</style>' . NR;
	}
}

# end file