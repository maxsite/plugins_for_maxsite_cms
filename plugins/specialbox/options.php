<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	global $MSO;
	$CI = & get_instance();
	$options_key = 'plugin_specialbox';
	
	if ( $post = mso_check_post(array('f_session_id','f_submit','f_rounded_corners','f_text_shadow','f_box_shadow','f_border_style','f_margin','f_bigImg','f_showImg')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['rounded_corners'] = $post['f_rounded_corners'];
		$options['text_shadow'] = $post['f_text_shadow'];
		$options['box_shadow'] = $post['f_box_shadow'];
		$options['border_style'] = $post['f_border_style'];
		$options['margin'] = $post['f_margin'];
		$options['bigImg'] = $post['f_bigImg'];
		$options['showImg'] = $post['f_showImg'];
		$options['corner'] = $post['f_corner'];
		$options['boxes'] = $post['f_boxes'];
	
		if ( mso_add_option($options_key, $options, 'plugins') ) 
		{
			echo '<div class="update">' . t('Добавлено!', 'admin') . '</div>';
		}
		else
			echo '<div class="error">Ошибка добавления! ' . $result['description'] . ' </div>';
	}
?>
<h1>Настройка Special Box</h1>
<p class="info">
Плагин помещает текст блок и подсвечивает определенным цветом.<br />
Для подсветки текста, используйте следующий короткий код:<br />
[sbox id="НУЖНЫЙ_ID"]Подсвечиваемый текст и/или короткие коды[/sbox]<br /><br />
НУЖНЫЙ_ID = alert – красный блок. download – синий блок. info – зелёный блок. warning – жёлтый блок.<br />
black – чёрный блок. grey – серый блок. custom – настраиваемый в админке блок.<br />
<a href="<?= getinfo('plugins_url') ?>specialbox/text.txt">Дополнительно</a>
<p>Укажите необходимые опции.</p>
</p>
<?php
	$CI = & get_instance();
	$CI->load->helper('form');

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['rounded_corners']) ) $options['rounded_corners'] = 'true';
		if ( !isset($options['text_shadow']) ) $options['text_shadow'] = 'false';
		if ( !isset($options['box_shadow']) ) $options['box_shadow'] = 'false';
		if ( !isset($options['border_style']) ) $options['border_style'] = 'solid';
		if ( !isset($options['margin']) ) $options['margin'] = '10px 10px 10px 10px';
		if ( !isset($options['bigImg']) ) $options['bigImg'] = 'false';
		if ( !isset($options['showImg']) ) $options['showImg'] = 'true';
		if ( !isset($options['corner']) ) $options['corner'] = 'false';
		if ( !isset($options['boxes']) ) $options['boxes'] = 'нету';
		
		$form = '';		
		$form .= '<p style="padding-bottom:5px"><strong>скругленные края рамки: </strong>'. 
				form_dropdown('f_rounded_corners', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['rounded_corners']).'</p>';

		$form .= '<p style="padding-bottom:5px"><strong>тень от текста: </strong>'. 
				form_dropdown('f_text_shadow', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['text_shadow']).'</p>';

		$form .= '<p style="padding-bottom:5px"><strong>тень от блока: </strong>'. 
				form_dropdown('f_box_shadow', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['box_shadow']).'</p>';

		$form .= '<p style="padding-bottom:5px;"><strong>стиль рамки: </strong><input name="f_border_style" type="text" value="'.$options['border_style'].'"></p>';
		
		$form .= '<p style="padding-bottom:15px;border-bottom:1px #CCC solid"><strong>отступы (margin): </strong><input name="f_margin" type="text" value="'.$options['margin'].'"></p>';		
		
		$form .= '<p style="padding-bottom:5px; padding-top:5px"><strong>отображать большие картинки: </strong>'. 
				form_dropdown('f_bigImg', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['bigImg']).'</p>';

		$form .= '<p style="padding-bottom:5px"><strong>показывать картинки: </strong>'. 
				form_dropdown('f_showImg', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['showImg']).'</p>';
		$form .= '<p style="padding-bottom:5px"><strong>закругленные края(доп.): </strong>'. 
				form_dropdown('f_corner', 
					array(  'true' => t('да'),
							'false' => t('нет') ), 
					$options['corner']).'<br />для некоторых браузеров, которые не поддерживают webkit или css3)</p>';
//disabled="disabled"
		$form .= '<p style="padding-bottom:5px;"><strong>используемые блоки: </strong>
				<input style="width: 300px;" name="f_boxes" type="text" value="'.$options['boxes'].'">
				<br />для блоков, созданных в редакторе(Формат:|custom|my_box)</p>';
				
		echo '<form action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
		echo '</form>';
?>