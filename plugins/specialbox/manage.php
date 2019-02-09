<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	global $MSO;
	$CI = & get_instance();
	$options_key = 'plugin_specialbox_boxes';
?>
<h1>Управление блоками</h1>
<p class="info">

</p>
<?php
	$mode = mso_segment(4);
	$id = mso_segment(5);
	
	// Удаляем
	if( 'delete' == $mode && is_numeric($id) )
	{
		$arr = mso_get_option($options_key, 'plugins', array());
		//
		$opt = mso_get_option('plugin_specialbox', 'plugins', array());
		$name = $arr[$id]['name'];
		$opt['boxes'] = str_replace('|' . $name, '', $opt['boxes']);
		mso_add_option('plugin_specialbox', $opt, 'plugins');
		//
		unset($arr[$id]);
		mso_add_option($options_key, $arr, 'plugins');
		mso_redirect(getinfo('site_url').'admin/specialbox/manage/');
	}

	# добавление нового блока
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_name', 'f_descr',)) )
	{
		mso_checkreferer();
		
		$arr = mso_get_option($options_key, 'plugins', array());
		$count = count($arr) + 1;
		$post['f_name'] = mso_slug($post['f_name']);
		$post['f_name'] = str_replace(' ','-',$post['f_name']);
		
		// подготавливаем данные
		$data[$count] = array(
//			'id' => (int) $count,
			'name' => $post['f_name'],
			'descr' => $post['f_descr'],
			);
		
		// выполняем запрос и получаем результат
		$data = array_merge($arr, $data);
		
		if ( mso_add_option($options_key, $data, 'plugins') ) 
		{
			echo '<div class="update">' . t('Добавлено!', 'admin') . '</div>';
		}
		else
			echo '<div class="error">Ошибка добавления! ' . $result['description'] . ' </div>';
	}
	
$arr = mso_get_option($options_key, 'plugins', array());
	
if(count($arr) > 0)
{
	$CI->load->library('table');
	$CI->table->set_template(array(
		'table_open'  => '<table border="0" cellpadding="0" cellspacing="0" class="samborsky_polls_table">',
		'heading_cell_start'  => '<th valign="top">',
	)); 
	$CI->table->set_heading('ID','Название','Описание','Изменить','Удалить');
	
	foreach( $arr as $key => $row )
	{
		$edit_url = $MSO->config['site_url'] . 'admin/specialbox/editor/' . $key;
		$delete_url = $MSO->config['site_url'] . 'admin/specialbox/manage/delete/' . $key;
		
		if(!isset($row['name'])) $row['name'] = 'undefine';
		if(!isset($row['descr'])) $row['descr'] = ' --- ';
		
		$CI->table->add_row(
			$key,
			stripslashes($row['name']),
			stripslashes($row['descr']),
			"<a href='{$edit_url}'>Изменить</a>",
			"<a href='".$delete_url."' onclick=\"return confirm('Удалить? Уверены?');\">Удалить</a>"
		);
	}
	
	echo $CI->table->generate(); 
}
		$form = '<br /><br />';		

		$form .= '<p style="padding:5px;border-top:1px #CCC solid"><strong>Название блока: </strong>
				<input name="f_name" type="text" value=""></p>';
		
		$form .= '<p style="padding:15px;border-bottom:1px #CCC solid"><strong>Описание: </strong>
				<input name="f_descr" type="text" value=""></p>';		
				
		echo '<form action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Добавить " style="margin:15px 0 5px" />';
		echo '</form>';
?>