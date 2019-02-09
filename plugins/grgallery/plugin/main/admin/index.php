<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

if ( !mso_check_allow('grgallery_edit') ) 
	{
	 echo t('Доступ запрещен', 'plugins/grgallery');
	 return;
	}


global $MSO;
require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
$CI = & get_instance();	
$CI->load->helper('form');	// подгружаем хелпер форм	

$out = '';
$email_notice = '';

if ($post = mso_check_post(array('f_session_id', 'ch_opt')))
	{
	mso_checkreferer();
	
	//--- сохранение измененных опций---------------------------
	$newoptions['use_upload_id_dir'] = $grgll['use_upload_id_dir'];
	$newoptions['q_col_list'] = $grgll['q_col_list'];
	$newoptions['view_all_tags'] = $grgll['view_all_tags'];
	$newoptions['view_groups_page'] = $grgll['view_groups_page'];
	
	if (!isset($post['use_upload_id_dir']))	$newoptions['use_upload_id_dir'] = 0;
	if (isset($post['view_all_tags']))	$newoptions['view_all_tags'] = 1;
	if (isset($post['q_col_list']))	$newoptions['q_col_list'] = $post['q_col_list'];
	if (!isset($post['view_groups_page']))	$newoptions['view_groups_page'] = 0;
	
	$grgll_options = mso_add_option('default_quantity_col', $newoptions['q_col_list'], 'templates'); //записываем измененные опции
	$grgll_options = mso_add_option($grgll['main_key_options'], $newoptions, 'plugins'); //записываем измененные опции
	};

$grgll_options = mso_get_option($grgll['main_key_options'], 'plugins', array()); // получение опций
$setupdir = FALSE;
if ($grgll_options['use_upload_id_dir'] == '1') {$setupdir = TRUE;};
#$out.= '<h1 class="content">'.t('Общие настройки плагина GrGallery', 'plugins/grshop').'</h1><br/>';
$out.= form_open($plugin_url.'/main/').mso_form_session('f_session_id');
//$out.= form_checkbox('use_upload_id_dir', '1', $setupdir).'  '.t('Использовать для каждой записи отдельную папку загрузки', 'plugins/grgallery').'  '.NR.'<br/><br/>';

$q_col_list = mso_get_option('default_quantity_col', 'templates', '3');
$data = array(
              'name'        => 'q_col_list',
              'id'          => 'q_col_list',
              'value'       => $q_col_list,
              'maxlength'   => '3',
              'size'        => '3',
            );

$out.= form_input($data).'  '.t('количество колонок картинок на главной и в разделах', 'plugins/grshop').'  '.NR.'<br/><br/>';

$viewalltags = FALSE;
if ($grgll_options['view_all_tags'] == '1') {$viewalltags = TRUE;};
$out.= form_checkbox('view_all_tags', '1', $viewalltags).'  '.t('Выводить в прайсе на странице все услуги (включая непредоставляемые)', 'plugins/grgallery').'  '.NR.'<br/><br/>';

$viewgroupspage = FALSE;
if ($grgll_options['view_groups_page'] == '1') {$viewgroupspage = TRUE;};
$out.= form_checkbox('view_groups_page', '1', $viewgroupspage).'  '.t('выводить услуги с разбивкой по группам', 'plugins/grgallery').'  '.NR.'<br/><br/>';

$out .=	form_submit('ch_opt', t('Сохранить', 'plugins/grshop') );
$out .= form_close( );
mso_flush_cache();
echo $out;
?>