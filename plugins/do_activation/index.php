<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 * License GNU GPL 2+
 */


# функция автоподключения плагина
function do_activation_autoload()
{
	mso_register_widget('do_activation_widget', t('Проверка активации') ); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function do_activation_uninstall($args = array())
{	
	mso_delete_option_mask('do_activation_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function do_activation_widget($num = 1) 
{
	$widget = 'do_activation_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	if (isset($options['textdo']) ) $options['textdo'] = '<p>' . $options['textdo'] . '</p>';
	else $options['textdo'] = '';

	if (isset($options['textposle']) ) $options['textposle'] = '<p>' . $options['textposle'] . '</p>';
	else $options['textposle'] = '';
	
	return do_activation_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function do_activation_widget_form($num = 1) 
{
	$widget = 'do_activation_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Проверка активации', __FILE__);
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header']), t('Подсказка')));

	$form .= '<p><div class="t150">' . t('Текст вначале:', __FILE__) . '</div> '. form_textarea( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст в конце:', __FILE__) . '</div> '. form_textarea( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ) ;

	// $form .= mso_widget_create_form(t(''), , t(''));

	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function do_activation_widget_update($num = 1) 
{
	$widget = 'do_activation_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function do_activation_widget_custom($options = array(), $num = 1)
{
	/*
	// кэш 
	$cache_key = 'do_activation_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	*/

	$out = '';

	// если вошел коюмзер
	if (is_login_comuser())
	{

		if ( !isset($options['header']) ) $options['header'] = '';
		if ( !isset($options['textdo']) ) $options['textdo'] = '';
		if ( !isset($options['textposle']) ) $options['textposle'] = '';

		// проверяет статус акцивации
		function do_activation_comuser_activation_status($activate=false, $comuser_approved = '')
		{
			$CI = & get_instance();
	        
		    $CI->db->where('comusers_id', getinfo('comusers_id'));
		    $CI->db->limit(1);
		    $query = $CI->db->get('comusers');
		    $comuser_approved = $query->result_array();
		        
		    if ($comuser_approved)
		    {
		        extract($comuser_approved[0]);
		            
		        if($comusers_activate_string != $comusers_activate_key)
		        {
		            $activate = false;
		        }
		        else
		        {
		            $activate = true;
		        }
		    }
		        
		    return $activate;
		}

		// если активирован
		if (do_activation_comuser_activation_status() === true)
		{
			// ничего не выводим.
			$out .= '';
		}
		else
		{
			$out .= $options['header'];

			$out .= $options['textdo'];

			$out .= '<p style="text-align:center;"><img src="' . getinfo('plugins_url') . 'do_activation/img/attention.png" title="" alt=""></p>';

			$out .= '<p style="text-align:center;">' . t('Активация не завершена', __FILE__) . '</p>';
			$out .= '<p style="text-align:center;"><a href="' . getinfo('site_url') . 'users/' . getinfo('comusers_id') . '/edit/' . '">' . t('Завершить сейчас!', __FILE__) . '</a></p>';
		
			$out .= $options['textposle'];
		}

	}
	
	#mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

# end file