<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Евгений Мирошниченко
 *
 * (c) https://modern-templates.com
 *
 */

# функция автоподключения плагина
function person_information_autoload($args = array())
{
	mso_register_widget('person_information_widget', t('Персольнаяная информация', 'plugins')); # регистрируем виджет
    mso_hook_add('head_css', 'person_information_add_css');# хук на подключит стили виджета
}

# функция выполняется при деинсталяции плагина
function person_information_uninstall($args = array())
{
	mso_delete_option_mask('person_information_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция подключит стили плагина к шаблону
function person_information_add_css($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'person_information/css/style.css">';

	return $args;
}

# функция, которая берет настройки из опций виджетов
function person_information_widget($num = 1)
{
	$widget = 'person_information_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	$path = getinfo('plugins_url') . 'person_information/img/'; # путь к картинкам

	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';


    if (isset($options['person_photo']) and $options['person_photo'])
	$options['person_photo'] = '<img src="' . $options['person_photo'] . '" alt="" class="person-info__photo">';
	else $options['person_photo'] = '';



    if (isset($options['person_name']) and $options['person_name'])
	$options['person_name'] = '<div class="person-info__name">' . $options['person_name'] . '</div>';
	else $options['person_name'] = '';

	if (isset($options['twitter']) and $options['twitter'])
	$options['twitter'] = '<a href="' . $options['twitter'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-twitter"></i></a> ';
	else $options['twitter'] = '';

    if (isset($options['vkontakte']) and $options['vkontakte'])
	$options['vkontakte'] = '<a href="' . $options['vkontakte'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-vk"></i></a>';
	else $options['vkontakte'] = '';

	if (isset($options['facebook']) and $options['facebook'])
	$options['facebook'] = '<a href="' . $options['facebook'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-facebook"></i></a>';
	else $options['facebook'] = '';

    if (isset($options['instagram']) and $options['instagram'])
	$options['instagram'] = '<a href="' . $options['instagram'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-instagram"></i></a>';
	else $options['instagram'] = '';

	if (isset($options['youtube']) and $options['youtube'])
	$options['youtube'] = '<a href="' . $options['youtube'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-youtube"></i></a>';
	else $options['youtube'] = '';

	if (isset($options['twitch']) and $options['twitch'])
	$options['twitch'] = '<a href="' . $options['twitch'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-twitch"></i></a>';
	else $options['twitch'] = '';

	if (isset($options['linkedin']) and $options['linkedin'])
	$options['linkedin'] = '<a href="' . $options['linkedin'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-linkedin"></i></a>';
	else $options['linkedin'] = '';

	if (isset($options['rss']) and $options['rss'])
	$options['rss'] = '<a href="' . $options['rss'] . '" target="_blank" rel="nofollow" class="person-info-btn__item"><i class="i-rss"></i></a>';
	else $options['rss'] = '';

    if (isset($options['email']) and $options['email'])
	$options['email'] = '<a href="mailto:' . $options['email'] . '" class="person-info-btn__item"><i class="i-envelope"></i></a> ';
	else $options['email'] = '';

	if (isset($options['text']) ) $options['text'] = '<div class="person-info__text">' . $options['text'] . '</div>';
	else $options['text'] = '';


	return person_information_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function person_information_widget_form($num = 1)
{

	$widget = 'person_information_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = 'Персональная информация';
	if ( !isset($options['person_photo']) ) $options['person_photo'] = '';
	if ( !isset($options['person_name']) ) $options['person_name'] = '';
	if ( !isset($options['twitter']) ) $options['twitter'] = '';
	if ( !isset($options['vkontakte']) ) $options['vkontakte'] = '';
	if ( !isset($options['facebook']) ) $options['facebook'] = '';
	if ( !isset($options['instagram']) ) $options['instagram'] = '';
	if ( !isset($options['youtube']) ) $options['youtube'] = '';
	if ( !isset($options['twitch']) ) $options['twitch'] = '';
	if ( !isset($options['linkedin']) ) $options['linkedin'] = '';
	if ( !isset($options['rss']) ) $options['rss'] = '';
	if ( !isset($options['email']) ) $options['email'] = '';
	if ( !isset($options['text']) ) $options['text'] = '';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

    $form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');

	$form .= mso_widget_create_form(t('Ссылка на фото'), form_input( array( 'name'=>$widget . 'person_photo', 'value'=>$options['person_photo'] ) ), '');

    $form .= mso_widget_create_form(t('Имя/Псевдоним'), form_input( array( 'name'=>$widget . 'person_name', 'value'=>$options['person_name'] ) ), '');

	$form .= mso_widget_create_form(t('Twitter:'), form_input( array( 'name'=>$widget . 'twitter', 'value'=>$options['twitter'] ) ), t('Полная ссылка с http://'));

    $form .= mso_widget_create_form(t('Вконтакте'), form_input( array( 'name'=>$widget . 'vkontakte', 'value'=>$options['vkontakte'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('Facebook'), form_input( array( 'name'=>$widget . 'facebook', 'value'=>$options['facebook'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('instagram'), form_input( array( 'name'=>$widget . 'instagram', 'value'=>$options['instagram'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('Youtube'), form_input( array( 'name'=>$widget . 'youtube', 'value'=>$options['youtube'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('Twitch:'), form_input( array( 'name'=>$widget . 'twitch', 'value'=>$options['twitch'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('Linkedin'), form_input( array( 'name'=>$widget . 'linkedin', 'value'=>$options['linkedin'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('RSS-лента'), form_input( array( 'name'=>$widget . 'rss', 'value'=>$options['rss'] ) ), t('Полная ссылка с http://'));

	$form .= mso_widget_create_form(t('E-Mail'), form_input( array( 'name'=>$widget . 'email', 'value'=>$options['email'] ) ), '');

	$form .= mso_widget_create_form(t('Текст:'), form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ),  t('Можно использовать html-код'));


	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function person_information_widget_update($num = 1)
{
	$widget = 'person_information_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['person_photo'] = mso_widget_get_post($widget . 'person_photo');
	$newoptions['person_name'] = mso_widget_get_post($widget . 'person_name');
	$newoptions['twitter'] = mso_widget_get_post($widget . 'twitter');
	$newoptions['facebook'] = mso_widget_get_post($widget . 'facebook');
	$newoptions['vkontakte'] = mso_widget_get_post($widget . 'vkontakte');
	$newoptions['instagram'] = mso_widget_get_post($widget . 'instagram');
	$newoptions['youtube'] = mso_widget_get_post($widget . 'youtube');
	$newoptions['twitch'] = mso_widget_get_post($widget . 'twitch');
	$newoptions['linkedin'] = mso_widget_get_post($widget . 'linkedin');
	$newoptions['rss'] = mso_widget_get_post($widget . 'rss');
	$newoptions['email'] = mso_widget_get_post($widget . 'email');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function person_information_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$person_photo = $options['person_photo'];
	$person_name = $options['person_name'];
	$twitter = $options['twitter'];
	$vkontakte = $options['vkontakte'];
	$facebook = $options['facebook'];
	$instagram = $options['instagram'];
	$youtube = $options['youtube'];
	$twitch = $options['twitch'];
	$linkedin = $options['linkedin'];
	$rss = $options['rss'];
	$email = $options['email'];
	$text = $options['text'];

	return $header . $person_photo . $person_name . '<div class="person-info-btn">'. $vkontakte . $twitter . $facebook . $instagram . $youtube . $twitch . $linkedin . $rss . $email. '</div>' . $text;
}
