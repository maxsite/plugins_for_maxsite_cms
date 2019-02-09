<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

# оболочечная функция, которая берет настройки из опций виджетов
# эта ф-ция будет выполняться в сайдбаре !!!.
function basket_widget($num = 1) 
{

	global $MSO;
	$widget = 'grshop_basket_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	return basket_widget_custom($options, $num);   # вызов ф-ции, выводящей корзину - суть виджета
}

# форма настройки виджета 
# имя функции = виджет_form
function basket_widget_form($num = 1) 
{


	global $MSO;
	$widget = 'grshop_basket_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('КОРЗИНА', 'plugins/grshop');

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins/grshop') . '</div> '.form_input( array( 'name'=>$widget .'header', 'value'=>$options['header'] ) ) ;
	return $form;
}


# сюда приходят POST из формы настройки виджета
# в этой ф-ции обновление опций
# имя функции = виджет_update
function basket_widget_update($num = 1) 
{

	global $MSO;
	$widget = 'grshop_basket_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции сохраненные раньше
	$options = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST из предыдущей формы
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	# если есть изменения обновляем опции
	if ( $options != $newoptions ) mso_add_option($widget, $newoptions, 'plugins');
}



# собственно функция действия виджета, т.е. что там в нем выводится.
# то есть ф-ция должна выводить содержимое корзины, с учетом параметров
function basket_widget_custom($options = array(), $num = 1)
	{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные

	global $grsh;
	$out = '';

	if ( !isset($options['header']) ) $options['header'] = t('КОРЗИНА', 'plugins/grshop');	//-- это из настроек виджета

	$grsh_options = mso_get_option($grsh['main_key_options'], 'plugins', array()); // получение опций
	if ( !isset($grsh_options['main_slug']) ) $grsh_options['main_slug'] = 'catalog';
	$link = getinfo('siteurl').$grsh_options['main_slug'].'/bas/';	// формирования линка

	$CI = & get_instance();
	$arr_basket_prod = $CI->session->userdata('prod');

	$out .=  $options['header'];
	$sum_main = 0;
	if ($arr_basket_prod) 
		{
		foreach ($arr_basket_prod as $id_prod => $idp)
			{
			$sum_prod = $idp['cur_cost']*$idp['qp'];
			$out.= $idp['name'].' : '.$idp['qp'].' '.t('шт.', 'plugins/grshop').'<br>';
			$out.= $idp['cur_cost'].' X '.$idp['qp'].' = '.$sum_prod.' '.$grsh_options['money'].'<br><br>';
			$sum_main = $sum_main + $sum_prod;
			}
		$out .= t('ИТОГО:', 'plugins/grshop').' '.$sum_main.' '.$grsh_options['money'].'<br>';
		$out .= '<br/>'.'<a href="'.$link.'">'.t('оформить заявку', 'plugins/grshop').'</a><br><br>';
		}
	else
		{
		$out .= t('пока пусто.:(', 'plugins/grshop');
		}
	return $out;
	};
	
?>