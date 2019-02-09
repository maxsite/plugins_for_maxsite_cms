<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

mso_remove_hook('admin_head');
//mso_hook_add( 'admin_head', 'specialbox_admin_head');

function specialbox_admin_head()
{
	echo '
	<script type="text/javascript" src="' .  getinfo('plugins_url') . 'specialbox/js/colorpicker.min.js" ></script>
	<link rel="stylesheet" href="' .  getinfo('plugins_url') . 'specialbox/css/colorpicker.css" type="text/css" media="screen" charset="utf-8"/>
	';
}
specialbox_admin_head();

	$id = mso_segment(4);
	
	global $MSO;
	$CI = & get_instance();
	$options_key = 'plugin_specialbox_boxes';

	if ( $post = mso_check_post(array('f_session_id','f_submit','f_name','f_color','f_ccolor','f_bcolor','f_bgcolor','f_cbgcolor','f_image','f_big')) )
	{
		mso_checkreferer();
	if($id != '')
	{
		$arr = mso_get_option($options_key, 'plugins', array());
		$count = count($arr) + 1;
		
		if(empty($id) && $id != 0) $id = $count;
		if(!isset($arr[$id])) $arr[$id] = array();
		
		$options = array();
		$options[$id]['name'] = $post['f_name'];
		$options[$id]['color'] = $post['f_color'];
		$options[$id]['ccolor'] = $post['f_ccolor'];
		$options[$id]['bcolor'] = $post['f_bcolor'];
		$options[$id]['bgcolor'] = $post['f_bgcolor'];
		$options[$id]['cbgcolor'] = $post['f_cbgcolor'];
		$options[$id]['image'] = $post['f_image'];
		$options[$id]['big'] = $post['f_big'];

		//
		$opt = mso_get_option('plugin_specialbox', 'plugins', array());
		if ( !isset($arr[$id]['name']) ) $name = '___'; else $name = $arr[$id]['name'];
		if ( !isset($options['boxes']) ) $options['boxes'] = '';
		if( strpos($opt['boxes'], $name) )
			$opt['boxes'] = str_replace($name, $post['f_name'], $opt['boxes']);
		else
			$opt['boxes'] .= '|' . $post['f_name'];
		mso_add_option('plugin_specialbox', $opt, 'plugins');
		//
		$arr[$id] = array_merge($arr[$id], $options[$id]);
		
		if ( mso_add_option($options_key, $arr, 'plugins') ) 
			echo '<div class="update">' . t('Добавлено!', 'admin') . '</div>';
		else
			echo '<div class="error">Ошибка добавления! ' . $result['description'] . ' </div>';
	} //end if
	}
	
	if ( $post = mso_check_post(array('f_session_id','f_submit_code','f_name','f_color','f_ccolor','f_bcolor','f_bgcolor','f_cbgcolor','f_image','f_big')) )
	{
		mso_checkreferer();
		
		$out = '
[sbox id="'. $post['f_name'].'" caption="Заголовок" color="'. $post['f_color'].'" ccolor="'. $post['f_ccolor'].'" bgcolor="'. $post['f_bgcolor'].'" cbgcolor="'. $post['f_cbgcolor'].'" bcolor="'. $post['f_bcolor'].'" image="'. $post['f_image'].'"]
[/sbox]';

		echo '<div class="update">'.$out.'</div>';
	}
	
?>
<h1>Редактор блоков</h1>
<p class="info">

</p>
<?php
	
	$CI->load->helper('form');

$def = array();
$options = array();

$def['name'] = 'custom';
$def['color'] = '000000';
$def['ccolor'] = 'ffffff';
$def['bcolor'] = 'f844ee';
$def['bgcolor'] = 'f7cdf5';
$def['cbgcolor'] = 'f844ee';
$def['image'] = '';
$def['big'] = 'true';
	
$options = mso_get_option($options_key, 'plugins', array());

//
if( is_numeric($id) )
if(isset($options[$id]))
{
	if ( isset($options[$id]['name']) ) $options['name'] = $options[$id]['name'];
	if ( isset($options[$id]['color']) ) $options['color'] = $options[$id]['color'];
	if ( isset($options[$id]['ccolor']) ) $options['ccolor'] = $options[$id]['ccolor'];
	if ( isset($options[$id]['bcolor']) ) $options['bcolor'] = $options[$id]['bcolor'];
	if ( isset($options[$id]['bgcolor']) ) $options['bgcolor'] = $options[$id]['bgcolor'];
	if ( isset($options[$id]['cbgcolor']) ) $options['cbgcolor'] = $options[$id]['cbgcolor'];
	if ( isset($options[$id]['image']) ) $options['image'] = $options[$id]['image'];
	if ( isset($options[$id]['big']) ) $options['big'] = $options[$id]['big'];	
}

$options = array_merge($def, $options);

echo "
<script type='text/javascript'>
$(document).ready(function(){

$('#colorpickerField1, #colorpickerField2, #colorpickerField3, #colorpickerField4, #colorpickerField5').ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		$(el).val(hex);
		$(el).ColorPickerHide();
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
})
.bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
});

});
</script>
";
		$form = '';		
//disabled="disabled"
		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Название блока: ') . '</strong>
			<input name="f_name" type="text" value="'.$options['name'].'"></p>';
			
		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Цвет шрифта блока #') . '</strong>
			<input id="colorpickerField1" name="f_color" type="text" value="'.$options['color'].'"></p>';

		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Цвет шрифта заголовка блока #') . '</strong>
			<input id="colorpickerField2" name="f_ccolor" type="text" value="'.$options['ccolor'].'"></p>';

		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Цвет окантовки блока #') . '</strong>
			<input id="colorpickerField3" name="f_bcolor" type="text" value="'.$options['bcolor'].'"></p>';

		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Цвет фона блока #') . '</strong>
			<input id="colorpickerField4" name="f_bgcolor" type="text" value="'.$options['bgcolor'].'"></p>';

		$form .= '<p style="padding-bottom:5px;"><strong>' . t('Цвет фона заголовка блока #') . '</strong>
			<input id="colorpickerField5" name="f_cbgcolor" type="text" value="'.$options['cbgcolor'].'"></p>';
			
		$form .= '<p style="padding-bottom:5px;"><strong>Полный URL иконки: </strong><input name="f_image" type="text" value="'.$options['image'].'"></p>';		
		
		$form .= '<p style="padding-bottom:5px">
			<strong>Выбор размера иконки (большая, малая): </strong>'. 
				form_dropdown('f_big', 
					array(  'true' => t('большая'),
							'false' => t('малая') ), 
					$options['big']).'</p>';
				
		echo '<form action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
		echo '<input type="submit" name="f_submit_code" value=" Показать код " style="margin:15px 0 5px" />';
		echo '</form>';
?>