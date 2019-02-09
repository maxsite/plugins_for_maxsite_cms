<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

function unit_add1_form ($args)
	{
	global $MSO;
	global $grgll;
	$CI = & get_instance();
	$CI->load->helper('form');	// подгружаем хелпер форм
	$CI->load->helper('file'); // хелпер для работы с файлами
	require_once( getinfo('common_dir') . 'meta.php' ); 	
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку
	
	$id = '';
	if (mso_segment(3))	$id = mso_segment(3);	// получаем id страницы
	$idpg_dir = $grgll['uploads_pict_dir'];
	if (is_numeric($id)) $idpg_dir =$idpg_dir.'/'.$grgll['prefix'].$id;
	$new_dir = getinfo('uploads_dir').$idpg_dir;
	$out = '<div class="block_page page_add">';
	$out .= '<h3>'.t('Услуги и цены', 'admin').'</h3>';

	$f_tags = '';
	$f_all_tags = '';
	
	$alltags = mso_get_all_tags_page();
	$grouptags = get_group_tag();
	
		$CI = & get_instance();
		if ($id)
		{
			$qdata = array ('meta_id_obj' => $id, 'meta_table' => 'page');
			$CI->db->select('meta_value, meta_desc, meta_table, meta_menu_order, meta_id_obj, meta_key, meta_id');
			$CI->db->where(array ('meta_id_obj' => $id, 'meta_table' => 'page'));
			$CI->db->group_by('meta_value');
			$query = $CI->db->get('meta');
			
			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
					{
					$taga[$row['meta_value']] = $row['meta_value'];
					if ($row['meta_key'] == 'price') $taga['price_tags_page'] = $row['meta_value'];
					}
			}
			
			$price = array();			
			$price = @unserialize($taga['price_tags_page']);
			$i = 0;
			foreach ($alltags as $key => $val)
				{
				$display = '';
				if (isset($grouptags[$key])) $display = 'style="display: none;"';
				$price[$key] = (isset($price[$key])) ? $price[$key] : '';
				$check_use_meta = (isset($taga[$key])) ? 1:0;
				$out.= '<div class="check_unit_add1" '.$display.'>';
				$out.= form_checkbox('use_tag['.$i.']', $key, $check_use_meta).'  '.$key.'  ';
				$out .= '</div>';
				$out.= '<div class="input_unit_add1" '.$display.'>';
				
				$out.= form_textarea(array('name'=>'price_tag['.$i.']', 'id'=>'', 'value'=>$price[$key], 'rows'=>'1','cols'=>'7'));
				//$out .= '<input type="text" name="price_tag['.$i.']" id="" value="'.$price[$key].'" class="inputprice" style="width: 45px"/>';
				
				$out .= '</div>';
				if ($display == '')$out .= '<br/>';
				++$i;
				}
		}
		else	// если нету id страницы, значит это новая страница
		{
			$i = 0;		
			foreach ($alltags as $key => $val)
			{
			$display = '';
			if (isset($grouptags[$key])) $display = 'style="display: none;"';
			$out.= '<div class="check_unit_add1" '.$display.'>';
			$out.= form_checkbox('use_tag['.$i.']', $key, '').'  '.$key.'  ';
			$out .= '</div>';
			$out.= '<div class="input_unit_add1" '.$display.'>';
			$out.= form_textarea(array('name'=>'price_tag['.$i.']', 'id'=>'', 'value'=>'', 'rows'=>'1','cols'=>'6'));
			$out .= '</div>';
			if ($display == '')$out .= '<br/>';
			++$i;
			}
		}
		
	$out .= '<hr class="br">';
	$data = array('name'=> 'new_tag','id'=> 'new_tag','value'=> '','maxlength'=> '100','size'=> '10','style'=>'width:30%');
	$out.= ' новая услуга '.form_input($data).'<br>';
	
	$data = array('name'=> 'new_tag_price','id'=> 'new_tag_price','value'=> '','maxlength'=> '50','size'=> '10','style'=>'width:30%');	
	$out.= 'тариф новой услуги '.form_input($data).'<br>';
	
	$plugin_url = $MSO->config['site_admin_url'] . 'grgallery/services';
		
	$out .= '<hr class="br"><a href="'.$plugin_url.'">Редактировать группы услуг</a>';
	
	$out .= '</div>';
	return $out;
	}
?>