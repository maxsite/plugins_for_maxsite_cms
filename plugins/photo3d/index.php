<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# функция автоподключения плагина
function photo3d_autoload($args = array())
{
	mso_register_widget('photo3d_widget', 'Фото 3D'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function photo3d_uninstall($args = array())
{	
	mso_delete_option_mask('photo3d_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function photo3d_widget($num = 1) 
{
	$widget = 'photo3d_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return photo3d_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function photo3d_widget_form($num = 1) 
{
	$widget = 'photo3d_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['folder']) ) $options['folder'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="photo3d">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['width']) ) $options['width'] = 150;
		else $options['width'] = (int) $options['width'];
		
	if ( !isset($options['height']) ) $options['height'] = 150;
		else $options['height'] = (int) $options['height'];
		
	if ( !isset($options['clearcash']) ) $options['clearcash'] = '0';
		else $options['clearcash'] = (int) $options['clearcash'];
		
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
	$all_dirs 	= directory_map( getinfo( 'uploads_dir' ), TRUE ); 
	$out_folder = array(''=>'uploads');
	foreach( $all_dirs AS $dir ){
		
		if ( is_dir( getinfo('uploads_dir') . $dir ) && $dir != '_mso_float' && $dir != 'mini' && $dir != '_mso_i' && $dir != 'smiles') 
			$out_folder[$dir] = $dir;
	}
		
	$form = '<p><div class="t250">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) . '</p>';
	//$form .= '<p><div class="t250">Папка (uploads/<em>example</em>/):</div> '. form_input( array( 'name'=>$widget . 'folder', 'value'=>$options['folder'] ) ) . '</p>';
	
	$form .= '<p><div class="t250">Папка (uploads/<em>example</em>/):</div> '. form_dropdown( $widget . 'folder', $out_folder, $options['folder']) . '</p>';
	
	$form .= '<p><div class="t250">Ширина (px):</div> '. form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) ) . '</p>';
	$form .= '<p><div class="t250">Высота (px):</div> '. form_input( array( 'name'=>$widget . 'height', 'value'=>$options['height'] ) ) . '</p>';
			
	$form .= '<p><div class="t250">Начало блока:</div> '. form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ) . '</p>';
	$form .= '<p><div class="t250">Конец блока:</div> '. form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ) . '</p>';
	
	$form .= '<p><div class="t250">Сброс кэша плагина:</div> '. form_checkbox( $widget . 'clearcash', '1', ( $options['clearcash'] == 1 )? TRUE : FALSE ) . '</p>';
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function photo3d_widget_update($num = 1) 
{
	$widget = 'photo3d_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['folder'] = mso_widget_get_post($widget . 'folder');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');		
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['height'] = mso_widget_get_post($widget . 'height');
	$newoptions['clearcash'] = mso_widget_get_post($widget . 'clearcash');
	//print_r($_POST); exit(':-)');
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function photo3d_widget_custom($options = array(), $num = 1)
{	
	if ( !isset($options['clearcash']) ) $options['clearcash'] = 0;
	
	if( $options['clearcash'] != 1 ){
		$cache_key = 'photo3d_widget_custom' . serialize($options) . $num;
		$k = mso_get_cache($cache_key);
		if ($k) return $k; 
	}
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['folder']) ) $options['folder'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="photo3d">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['width']) ) $width = 150;
		else $width = (int) $options['width'];
		
	if ( !isset($options['height']) ) $height = 150;
		else $height = (int) $options['height'];
	
	// --------------------------------------------------------------------------------------- 	
	$_dir = opendir( getinfo('uploads_dir') . $options['folder'] . '/mini/' );
	$count_img	 = 0;
	$xml_content = '<images>';	
        while( $__file = readdir( $_dir ) ){		  
			if( $__file != '..' && $__file != '.' ){
                if( getimagesize( getinfo('uploads_dir') . $options['folder'] . '/mini/' . basename($__file) ) ){                
					$xml_content.='<image href="' . '/uploads/' . $options['folder'] . '/' . basename($__file). '" target="_blank">' . '/uploads/' . $options['folder'] . '/mini/' . basename($__file). '</image>';
					$count_img++;
                }				
			}
		} 
	$xml_content.='</images>';
	
	$filename = APPPATH . 'maxsite/plugins/photo3d/photowidget.xml';
	@chmod( $filename, 0744 );
	
	if ( is_writable( $filename ) ) {
	    $handle = fopen( $filename, 'w+' ); 
	    fwrite( $handle, $xml_content ); 	    
	    fclose( $handle );
	} 
		
	// ---------------------------------------------------------------------------------------
	if( $count_img > 0 ){
	
		$out='<object type="application/x-shockwave-flash" data="' . getinfo('plugins_url') . 'photo3d/photowidget.swf?t=' . time() . '" width="' . $width . '" height="' . $height . '">
					<param name="movie" value="' . getinfo('plugins_url') . 'photo3d/photowidget.swf" />
					<param name="bgcolor" value="#ffffff" />
					<param name="wmode" value="transparent" />
					<param name="AllowScriptAccess" value="always" />
					<param name="flashvars" value="feed=' . getinfo('plugins_url') . 'photo3d/photowidget.xml" />
					<p>This widget requires Flash Player 9 or better.</p>
			</object>';
	} else {
		$out='В папке <strong>' . '/uploads/' . $options['folder'] . '/mini/' . '</strong> нет изображений :(<br /> В разделе <em>Настройка виджетов</em> необходимо выбрать другую директорию.';
	}			
		
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	if( $options['clearcash'] != 1 ){
		mso_add_cache($cache_key, $out); // сразу в кэш добавим
	}
	
	return $out;
}

	