<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function group_autoload()
{
	mso_register_widget('group_widget', t('Социальные группы') ); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function group_uninstall($args = array())
{	
	mso_delete_option_mask('group_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function group_widget($num = 1)
{
	$widget = 'group_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

    if ( !isset($options['vkontakte']) ) $options['vkontakte'] = '';
    if ( !isset($options['facebook']) ) $options['facebook'] = '';
	if ( !isset($options['both']) ) $options['both'] = '';
	
	return group_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function group_widget_form($num = 1)
{
	$widget = 'group_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
    if ( !isset($options['vkontakte']) ) $options['vkontakte'] = '';
    if ( !isset($options['facebook']) ) $options['facebook'] = '';
	if ( !isset($options['both']) ) $options['both'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), t(''));

	$form .= mso_widget_create_form(t('ВКонтакте'), form_textarea( array( 'name'=>$widget . 'vkontakte', 'value'=>$options['vkontakte'])), t('Настройте код виджета перейдя по <a href="http://vk.com/developers.php?oid=-1&p=Groups" target="blanck">ссылке</a> и вставте код в этот блок. '));

    $form .= mso_widget_create_form(t('Facebook'), form_textarea( array( 'name'=>$widget . 'facebook', 'value'=>$options['facebook'])), t('Настройте код виджета перейдя по <a href="http://developers.facebook.com/docs/reference/plugins/like-box/" target="blanck">ссылке</a> и вставте код в этот блок. '));

    $form .= mso_widget_create_form(t('Вывод'), form_dropdown( $widget . 'both', array( 'vkontakte'=>t('ВКонтакте'), 'facebook'=>t('Facebook'), 'both'=>t('Вместе')), $options['both']), t('Укажите как будут отображаться блоки.'));

	// $form .= mso_widget_create_form(t(''), , t(''));

	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function group_widget_update($num = 1)
{
	$widget = 'group_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
    $newoptions['vkontakte'] = mso_widget_get_post($widget . 'vkontakte');
    $newoptions['facebook'] = mso_widget_get_post($widget . 'facebook');
    $newoptions['both'] = mso_widget_get_post($widget . 'both');

	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function group_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$vkontakte = $options['vkontakte'];
    $facebook = $options['facebook'];
    $both = $options['both'];

    if($both == 'vkontakte'){

       $data = $vkontakte;

    }else if($both == 'facebook'){

       $data = $facebook;
       //echo $data;

    }else{
      // заносим необходимые элементы в массив:
      $data = array(
      $vkontakte,
      $facebook
      );

      shuffle($data); // перемешиваем
      return $header . $data[0];
    }

    return $header . $data;

}

# end file