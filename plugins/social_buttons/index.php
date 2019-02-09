<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 **/

# функция автоподключения плагина
function social_buttons_autoload()
{
	mso_hook_add('admin_head', 'social_buttons_admin_head');
	mso_hook_add('admin_body_start', 'social_buttons_admin_body_start');
	mso_hook_add('head', 'social_buttons_head');
	mso_hook_add('content_end', 'social_buttons_content_end');
}


# функция выполняется при деинсталяции плагина
function social_buttons_uninstall($args = array())
{
	mso_delete_option('plugin_social_buttons', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function social_buttons_mso_options() 
{
	$url = getinfo('plugins_url') . 'social_buttons';

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_social_buttons', 'plugins', 
		array(
			'buttons_title' => array(
						'type' => 'info',
						'title' => t('Общие настройки')
					),
			'block_style' => array(
						'type' => 'textarea',
						'name' => t('Стили блока с кнопками'),
						'description' => t('Обратите внимание, каждая кнопка выводится в своем контейнере, который имеет уникальный класс. <a href="' . $url . '/help/help.html" target="_blank">HTML-структура плагина</a>.'),
						'default' => '#vkshare0 {margin: 0;}
#vkshare0 td {padding: 0; border: 0;}
.social-buttons_grid {display: table; width: 100%; margin: 1em 0;}
.social-buttons_cell {display: table-cell;}'
					),
			'buttons_on' => array(
						'type' => 'text',
						'itype' => 'hidden', // скрытый input hidden;
						'name' => t('Порядок кнопок'), 
						'description' => t('Перетащите мышкой нужные кнопки на активную область.'), 
						'default' => '',
						'group_end' => '
								<ul id="sb-list" class="sb-list connectedSortable">
									<li id="btn_twitter" title="Кнопка «Твитнуть» Twitter"><img src="' . $url . '/images/tw_icon.png" alt=""></li>
									<li id="btn_vkontakte" title="Кнопка «Опубликовать» ВКонтакте"><img src="' . $url . '/images/vk_icon.png" alt=""></li>
									<li id="btn_vkontakte_wg" class="vk_wg" title="Виджет «Мне нравится» ВКонтакте"><img src="' . $url . '/images/vk_icon_wg.png" alt=""></li>
									<li id="btn_facebook" title="Кнопка «Нравится» Facebook"><img src="' . $url . '/images/fb_icon.png" alt=""></li>
									<li id="btn_google" title="Кнопка «+1» Google"><img src="' . $url . '/images/ggl_icon.png" alt=""></li>
									<li id="btn_mm" title="Кнопка «Нравится» Мой мир"><img src="' . $url . '/images/mm_icon.png" alt=""></li>
									<li id="btn_ok" title="Кнопка «Класс!» Одноклассники"><img src="' . $url . '/images/ok_icon.png" alt=""></li>
									<li id="btn_pocket" title="Кнопка Pocket"><img src="' . $url . '/images/pkt_icon.png" alt=""></li>
								</ul>
								<ul id="sb-list-active" class="sb-list-active connectedSortable">
								</ul>'
					),
			'block_is_type' => array(
						'type' => 'select', 
						'name' => t('Отображение на сайте'), 
						'description' => t('Показывать кнопки только на одиночной странице (странице записи) или на всех страницах, где возможен вывод записей (главная, страница рубрики, страница метки и т. д.).<br> Обратите внимание, даже при выбранном значении «Везде» виджет «Мне нравится» ВКонтакте и кнопка «Класс!» Одноклассники отображаются только на странице записи.'),
						'values' => t('0||Везде # 1||Только на одиночной странице'),
						'default' => '1'
					),
			'block_page_type' => array(
						'type' => 'text', 
						'name' => t('Типы страниц'), 
						'description' => t('Укажите через запятую типы страниц, на которых должны выводиться кнопки.'),
						'default' => 'blog, static'
					),
			'block_page_exclude' => array(
						'type' => 'text', 
						'name' => t('Исключить страницы'), 
						'description' => t('Укажите через запятую номера страниц (записей), на которых кнопки выводиться не будут.'),
						'default' => ''
					),
			'text_before' => array(
						'type' => 'textarea', 
						'name' => t('Текст перед кнопками'), 
						'description' => t('Текст выводится в блоке с классом social-buttons_before.'),
						'default' => ''
					),
			'text_after' => array(
						'type' => 'textarea', 
						'name' => t('Текст после кнопок'), 
						'description' => t('Текст выводится в блоке с классом social-buttons_after.'),
						'default' => ''
					),
			'tw_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «Твитнуть» Twitter'),
						'id' => 'plugin_options_tw'
					),
			'tw_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('horizontal||Счетчик справа <br><img src="' . $url . '/images/tw_horisontal.png"> # vertical||Счетчик вверху <br><img src="' . $url . '/images/tw_vertical.png"> # none||Без счетчика <br><img src="' . $url . '/images/tw_nocount.png">'),
						'default' => 'horizontal'
					),
			'tw_size' => array(
						'type' => 'radio', 
						'name' => t('Размер'),
						'description' => 'Если выбран большой размер кнопки, вариант внешнего вида «Счетчик вверху» не поддерживается.',
						'values' => t('medium||Стандартная <br><img src="' . $url . '/images/tw_horisontal.png"> # large||Большая <br><img src="' . $url . '/images/tw_large.png">'),
						'default' => 'medium'
					),
			'tw_lang' => array(
						'type' => 'select', 
						'name' => t('Язык'), 
						'description' => t('Обратите внимание, выбранный язык влияет на ширину кнопки.'),
						'values' => t('ru||Русский # en||Английский'),
						'default' => 'ru'
						
					),
			'tw_username' => array(
						'type' => 'text',
						'name' => t('Логин в Твиттере (username)'),
						'description' => t('Если указан, то по клику на кнопке в твит будет добавляться приписка «с помощью @username».'),
						'default' => ''
					),
			'vk_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «Опубликовать» ВКонтакте'),
						'id' => 'plugin_options_vk'
					),
			'vk_view' => array(
						'type' => 'radio', 
						'name' => 'Внешний вид', 
						'description' => '',
						'values' => 'round||Со счетчиком <br><img src="' . $url . '/images/vk_round.png"> # round_nocount||Без счетчика <br><img src="' . $url . '/images/vk_round_nocount.png"> # button||Прямые углы <br><img src="' . $url . '/images/vk_button.png"> # button_nocount||Прямые углы, без счетчика <br><img src="' . $url . '/images/vk_button_nocount.png"> # link||Ссылка с иконкой <br><img src="' . $url . '/images/vk_link.png">  # custom||Иконка <br><img src="' . $url . '/images/vk_custom.png">',
						'default' => 'round'
					),
			'vk_text' => array(
						'type' => 'text', 
						'name' => 'Текст', 
						'description' => 'На кнопке можно указать свой текст.',
						'default' => 'Сохранить'
					),
			'vk_wg_title' => array(
						'type' => 'info',
						'title' => t('Настройки виджета «Мне нравится» ВКонтакте'),
						'id' => 'plugin_options_vk_wg'
					),
			'vk_wg_apiid' => array(
						'type' => 'text', 
						'name' => 'API_ID', 
						'description' =>'',
						'default' => '',
						'group_end' => '<p class="nop"><span class="fhint">При пустом поле кнопка не выводится. Получить API_ID можно на <a href="http://vk.com/developers.php?oid=-1&p=Like" target="_blank">соответствующей странице</a>. <a href="' . $url . '/help/help.html" target="_blank">Пояснения</a>.</span></p>'
					),
			'vk_wg_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('button||Числовой счетчик <br><img src="' . $url . '/images/vk_wg_button.png"> # full||Текстовый счетчик <br><img src="' . $url . '/images/vk_wg_full.png"> # mini||Мини-кнопка <br><img src="' . $url . '/images/vk_wg_mini.png"> # vertical||Мини-кнопка, счетчик вверху <br><img src="' . $url . '/images/vk_wg_vertical.png">'),
						'default' => 'button'
					),
			'vk_wg_text' => array(
						'type' => 'radio', 
						'name' => t('Текст'),
						'description' => '',
						'values' => t('like||Мне нравится <br><img src="' . $url . '/images/vk_wg_button.png"> # interest||Это интересно <br><img src="' . $url . '/images/vk_wg_interest.png">'),
						'default' => 'like'
					),
			'vk_wg_height' => array(
						'type' => 'select', 
						'name' => t('Высота кнопки в пикселях (px)'),
						'description' => '',
						'values' => '18||18 # 20||20 # 22||22 # 24||24',
						'default' => '22'
					),
			'vk_wg_width' => array(
						'type' => 'text', 
						'name' => t('Ширина блока кнопки в пикселях (px)'),
						'description' => t('Только для кнопки с текстовым счетчиком.'),
						'default' => '350'
					),
					
			'fb_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «Нравится» Facebook'),
						'id' => 'plugin_options_fb'
					),
			'fb_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('button_count||Счетчик справа <br><img src="' . $url . '/images/fb_button_count.png"> # box_count||Счетчик вверху <br><img src="' . $url . '/images/fb_box_count.png"> # standard||Текстовый счетчик <br><img src="' . $url . '/images/fb_standard.png">'),
						'default' => 'button_count'
					),
			'fb_color' => array(
						'type' => 'radio', 
						'name' => t('Цвет'), 
						'description' => '',
						'values' => t('light||Светлый <br><img src="' . $url . '/images/fb_button_count.png"> # dark||Темный <br><img src="' . $url . '/images/fb_color_dark.png">'),
						'default' => 'light'
					),
			'fb_text' => array(
						'type' => 'radio', 
						'name' => t('Текст'), 
						'description' => '',
						'values' => t('like||Нравится <br><img src="' . $url . '/images/fb_button_count.png"> # recommend||Я рекомендую <br><img src="' . $url . '/images/fb_recommend.png">'),
						'default' => 'like'
					),
			'fb_lang' => array(
						'type' => 'select', 
						'name' => t('Язык'), 
						'description' => t('Обратите внимание, выбранный язык влияет на ширину кнопки.'),
						'values' => t('ru||Русский # en||Английский'),
						'default' => 'ru'
					),
			'fb_width' => array(
						'type' => 'text', 
						'name' => t('Ширина блока кнопки в пикселях (px)'),
						'description' => t('Только для кнопки с текстовым счетчиком.'),
						'default' => '225'
					),
			'ggl_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «+1» Google'),
						'id' => 'plugin_options_ggl'
					),
			'ggl_count' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'), 
						'description' => '',
						'values' => t('bubble||Числовой счетчик <br><img src="' . $url . '/images/ggl_standard_count.png"> # inline||Текстовый счетчик <br><img src="' . $url . '/images/ggl_count_inline.png"> # nocount||Без счетчика <br><img src="' . $url . '/images/ggl_nocount.png">'),
						'default' => 'bubble'
					),
			'ggl_size' => array(
						'type' => 'radio', 
						'name' => t('Размер'), 
						'description' => '',
						'values' => t('standard||Стандартная <br><img src="' . $url . '/images/ggl_standard_count.png"> # small||Маленькая <br><img src="' . $url . '/images/ggl_small.png"> # middle||Средняя <br><img src="' . $url . '/images/ggl_middle.png"> # big||Большая <br><img src="' . $url . '/images/ggl_big.png">'),
						'default' => 'standard'
					),
			'ggl_lang' => array(
						'type' => 'select', 
						'name' => t('Язык'), 
						'description' => t('Применяется к всплывающей подсказке кнопки и текстовому счетчику.'),
						'values' => t('ru||Русский # en||Английский'),
						'default' => 'ru'
					),
			'ggl_align' => array(
						'type' => 'select', 
						'name' => t('Выравнивание кнопки в блоке'), 
						'description' => '',
						'values' => t('left||По левому краю # right||По правому краю'),
						'default' => 'left'
					),
			'ggl_width' => array(
						'type' => 'text', 
						'name' => t('Ширина блока кнопки в пикселях (px)'), 
						'description' => '',
						'default' => '250',
						'group_end' => '<p class="nop"><span class="fhint">Только для кнопки с текстовым счетчиком. <a href="https://developers.google.com/+/plugins/+1button/?hl=ru#inline-annotation" target="_blank">Подробнее</a>.</span></p>'
					),
			'pkt_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки Pocket'),
						'id' => 'plugin_options_pkt'
					),
			'pkt_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('horizontal||Счетчик справа <br><img src="' . $url . '/images/pkt_horisontal.png"> # vertical||Счетчик вверху <br><img src="' . $url . '/images/pkt_vertical.png"> # none||Без счетчика <br><img src="' . $url . '/images/pkt_none.png">'),
						'default' => 'horizontal'
					),
			'pkt_align' => array(
						'type' => 'select', 
						'name' => t('Выравнивание кнопки в блоке'), 
						'description' => t('Если у кнопки Pocket выбран внешний вид «Счетчик справа» и она размещена крайней справа, используйте выравнивание «По правому краю».'),
						'values' => t('left||По левому краю # right||По правому краю'),
						'default' => 'left'
					),
			'mm_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «Нравится» Мой мир'),
						'id' => 'plugin_options_mm'
					),
			'mm_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('horizontal||Счетчик справа <br><img src="' . $url . '/images/mm_horisontal.png"> # vertical||Счетчик вверху <br><img src="' . $url . '/images/mm_vertical.png"> # none||Без счетчика <br><img src="' . $url . '/images/mm_nocount.png">'),
						'default' => 'horizontal'
					),
			'mm_size' => array(
						'type' => 'radio', 
						'name' => t('Размер'),
						'description' => '',
						'values' => t('medium||Стандартная <br><img src="' . $url . '/images/mm_horisontal.png"> # large||Большая <br><img src="' . $url . '/images/mm_horisontal_big.png">'),
						'default' => 'medium'
					),
			'mm_text' => array(
						'type' => 'radio', 
						'name' => t('Текст'), 
						'description' => '',
						'values' => t('like||Нравится <br><img src="' . $url . '/images/mm_horisontal.png"> # share||Поделиться <br><img src="' . $url . '/images/mm_horisontal_share.png"> # recommend||Рекомендую <br><img src="' . $url . '/images/mm_horisontal_recommend.png"> # notext||Без текста <br><img src="' . $url . '/images/mm_horisontal_notext.png">'),
						'default' => 'like'
					),
			'mm_rounding' => array(
						'type' => 'radio', 
						'name' => t('Скругление'), 
						'description' => '',
						'values' => t('strong||Сильное <br><img src="' . $url . '/images/mm_horisontal.png"> # weak||Слабое <br><img src="' . $url . '/images/mm_horisontal_round.png"> # right||Прямые углы <br><img src="' . $url . '/images/mm_horisontal_right.png">'),
						'default' => 'strong'
					),
			'ok_title' => array(
						'type' => 'info',
						'title' => t('Настройки кнопки «Класс!» Одноклассники'),
						'id' => 'plugin_options_ok'
					),
			'ok_view' => array(
						'type' => 'radio', 
						'name' => t('Внешний вид'),
						'description' => '',
						'values' => t('horizontal||Счетчик справа <br><img src="' . $url . '/images/ok_horisontal.png"> # vertical||Счетчик вверху <br><img src="' . $url . '/images/ok_vertical.png"> # none||Без счетчика <br><img src="' . $url . '/images/ok_nocount.png">'),
						'default' => 'horizontal'
					),
			'ok_size' => array(
						'type' => 'radio', 
						'name' => t('Размер'),
						'description' => '',
						'values' => t('medium||Стандартная <br><img src="' . $url . '/images/ok_horisontal.png"> # large||Большая <br><img src="' . $url . '/images/ok_horisontal_big.png">'),
						'default' => 'medium'
					),
			'ok_text' => array(
						'type' => 'radio', 
						'name' => t('Текст'), 
						'description' => '',
						'values' => t('cool||Класс! <br><img src="' . $url . '/images/ok_horisontal.png"> # share||Поделиться <br><img src="' . $url . '/images/ok_horisontal_share.png"> # like||Нравится <br><img src="' . $url . '/images/ok_horisontal_like.png"> # notext||Без текста <br><img src="' . $url . '/images/ok_horisontal_notext.png">'),
						'default' => 'cool'
					),
			'ok_rounding' => array(
						'type' => 'radio', 
						'name' => t('Скругление'), 
						'description' => '',
						'values' => t('strong||Сильное <br><img src="' . $url . '/images/ok_horisontal.png"> # weak||Слабое <br><img src="' . $url . '/images/ok_horisontal_round.png"> # right||Прямые углы <br><img src="' . $url . '/images/ok_horisontal_right.png">'),
						'default' => 'strong'
					),
			),
		t('Настройки плагина "Социальные кнопки"'), // титул
		t('Укажите необходимые опции.') // инфо
	);
}

# функции плагина
function social_buttons_admin_head() // Только в админке
{
	$url = getinfo('plugins_url') . 'social_buttons';
	if ( mso_segment(1) == 'admin' and mso_segment(2) == 'plugin_options' and mso_segment(3) == 'social_buttons')
	{
		echo '<link rel="stylesheet" href="' . $url . '/css/admin.css">';
		echo '<script src="' . $url . '/js/jquery-ui-1.10.3.custom.min.js"></script>';
		echo '<script src="' . $url . '/js/admin.js"></script>';
	}
	
}

function social_buttons_admin_body_start() // Только в админке
{
	$url = getinfo('plugins_url') . 'social_buttons';
	if ( mso_segment(1) == 'admin' and mso_segment(2) == 'plugin_options' and mso_segment(3) == 'social_buttons')
	{
		echo '<ul class="sb-nav"><li><a href="#" id="sb-plugin-nav_top"><img src="' . $url . '/images/arrow_top.png"></a></li><li><a href="#plugin_options_tw"><img src="' . $url . '/images/tw_icon.png"></a></li><li><a href="#plugin_options_vk" ><img src="' . $url . '/images/vk_icon.png"></a></li><li><a href="#plugin_options_vk_wg"><img src="' . $url . '/images/vk_icon_wg.png"></a></li><li><a href="#plugin_options_fb"><img src="' . $url . '/images/fb_icon.png"></a></li><li><a href="#plugin_options_ggl"><img src="' . $url . '/images/ggl_icon.png"></a></li><li><a href="#plugin_options_pkt"><img src="' . $url . '/images/pkt_icon.png"></a></li><li><a href="#plugin_options_mm"><img src="' . $url . '/images/mm_icon.png"></a></li><li><a href="#plugin_options_ok"><img src="' . $url . '/images/ok_icon.png"></a></li><li><button id="sb-nav-btn" class="i save" title="Сохранить"></button></li></ul>';
	}
}

function social_buttons_head() // Стили и скрипт для блока с кнопками
{
	global $page;

	$options = mso_get_option('plugin_social_buttons', 'plugins', array());

	if (!isset($options['block_page_type'])) $options['block_page_type'] = 'blog, static';
	if (!isset($options['block_is_type'])) $options['block_is_type'] = 1;
	if (!isset($options['block_page_exclude'])) $options['block_page_exclude'] = '';
	if (!isset($options['buttons_on'])) $options['buttons_on'] = '';
	if (!isset($options['block_style']) ) $options['block_style'] = '.social-buttons > table {width: 100%; margin: 1em 0; text-align: left;} .social-buttons table td {margin: 0; padding: 0; border: 0;} #vkshare0 {margin: 0;}';
	if (!isset($options['ggl_lang'])) $options['ggl_lang'] = 'ru';
	if (!isset($options['fb_lang'])) $options['fb_lang'] = 'ru';

	$style = '<style>' . $options['block_style'] . '</style>';

	if ($options['ggl_lang'] == 'ru') $ggl_lang = 'ru';
	elseif ($options['ggl_lang'] == 'en') $ggl_lang = 'en';

	if ($options['fb_lang'] == 'ru') $fb_lang = 'ru_RU';
	elseif ($options['fb_lang'] == 'en') $fb_lang = 'en_US';

	// Google
	if (preg_match('/btn_google/i',$options['buttons_on']))
	{
		
		$ggl_url = '"https://apis.google.com/js/platform.js",';
		$gcfg_ggl_lang = 'window.___gcfg = {lang: "' . $ggl_lang. '"};';
	}
	else
	{
		$ggl_url = '';
		$gcfg_ggl_lang = '';
	}

	// Twitter
	if (preg_match('/btn_twitter/i',$options['buttons_on'])) $tw_url = '"//platform.twitter.com/widgets.js",';
	else $tw_url = '';

	// ВК виджет
	if (preg_match('/btn_vkontakte_wg/i',$options['buttons_on'])) $vk_wg_url = '"//userapi.com/js/api/openapi.js?34",';
	else $vk_wg_url = '';

	// Facebook
	if (preg_match('/btn_facebook/i',$options['buttons_on'])) $fb_url = '"//connect.facebook.net/'. $fb_lang .'/all.js#xfbml=1",'; 
	else $fb_url = '';

	// Pocket
	if (preg_match('/btn_pocket/i',$options['buttons_on'])) $pkt_url = '"https://widgets.getpocket.com/v1/j/btn.js?v=1"';
	else $pkt_url = '';


	$script = '<script>
	(function (window, document) {' . $gcfg_ggl_lang . '

	var apis = [
		' . $ggl_url . '
		' . $tw_url . '
		' . $vk_wg_url . '
		' . $fb_url . '
		' . $pkt_url . '
	],
	iterator = apis.length,
	script = "script",
	fragment = document.createDocumentFragment(),
	element = document.createElement(script),
	clone;

	while (iterator--) {
		clone = element.cloneNode(false);
		clone.async = clone.src = apis[iterator];
		fragment.appendChild(clone);
	}

	clone = document.getElementsByTagName(script)[0];
	clone.parentNode.insertBefore(fragment, clone);

})(this, document);
	</script>';

	// ВК
	if (preg_match('/btn_vkontakte/i',$options['buttons_on'])) $script_2 = '<script type="text/javascript" src="http://vk.com/js/api/share.js?85" charset="windows-1251"></script>';
	else $script_2 = '';

	$block_page_type = mso_explode($options['block_page_type'], false);
	$block_page_exclude = mso_explode($options['block_page_exclude'], false);

	// Вывод стилей и скриптов
	if ($options['block_is_type'] == '0' and isset($options['buttons_on']) and $options['buttons_on'])
	{
		if (in_array($page['page_type_name'], $block_page_type))
		{
			if (is_type('page') and !in_array($page['page_id'], $block_page_exclude))
			{
				echo $style;
				echo $script;
				echo $script_2;
			}
			elseif (!is_type('page'))
			{
				echo $style;
				echo $script;
				echo $script_2;
			}
		}
	}
	elseif ($options['block_is_type'] == '1' and is_type('page') and isset($options['buttons_on']) and $options['buttons_on'])
	{
		if (in_array($page['page_type_name'], $block_page_type) and !in_array($page['page_id'], $block_page_exclude))
		{
			echo $style;
			echo $script;
			echo $script_2;
		}
	}
}

function social_buttons_content_end() // Формирование кнопок, их вывод на страницах
{
	global $page;

	$pg_url = getinfo('site_url') . 'page/' . $page['page_slug'];
	$pg_title = $page['page_title'];
	$pg_desc = mso_head_meta('description');

	$options = mso_get_option('plugin_social_buttons', 'plugins', array());

	if (!isset($options['buttons_on'])) $options['buttons_on'] = '';
	if (!isset($options['block_is_type'])) $options['block_is_type'] = 1;
	if (!isset($options['block_page_type'])) $options['block_page_type'] = 'blog, static';
	if (!isset($options['block_page_exclude'])) $options['block_page_exclude'] = '';
	if (!isset($options['text_before'])) $options['text_before'] = '';
	if (!isset($options['text_after'])) $options['text_after'] = '';

	$buttons_on = preg_split('/[,\s]+/', $options['buttons_on']); // разбиваем на подстроки, помещаем в массив
	$block_page_type = mso_explode($options['block_page_type'], false);
	$block_page_exclude = mso_explode($options['block_page_exclude'], false);

	if ($options['text_before'] != '') $text_before = '<div class="social-buttons_before">' . $options['text_before'] . '</div>';
	else $text_before = '';

	if ($options['text_after'] != '') $text_after = '<div class="social-buttons_after">' . $options['text_after'] . '</div>';
	else $text_after = '';

	// Twitter
	if (!isset($options['tw_view'])) $options['tw_view'] = 'horizontal';
	if (!isset($options['tw_size'])) $options['tw_size'] = 'medium';
	if (!isset($options['tw_lang'])) $options['tw_lang'] = 'ru';
	if (!isset($options['tw_username'])) $options['tw_username'] = '';

	if ($options['tw_view'] == 'horizontal') $tw_view = 'horizontal';
	elseif ($options['tw_view'] == 'vertical') $tw_view = 'vertical';
	else $tw_view = 'none';

	if ($options['tw_size'] == 'medium') $tw_size = 'medium';
	elseif ($options['tw_size'] == 'large') $tw_size = 'large';

	if ($options['tw_lang'] == 'ru') $tw_lang = 'ru';
	elseif ($options['tw_lang'] == 'en') $tw_lang = 'en';

	if ($options['tw_username']) $tw_username = $options['tw_username'];
	else $tw_username = '';

	$tw_out = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $pg_url . '" data-text="' . $pg_title . '" data-count="' . $tw_view . '" data-via="' . $tw_username . '" data-size="' . $tw_size .'" data-lang="' . $tw_lang . '">Твитнуть</a>';

	// Facebook
	if (!isset($options['fb_view'])) $options['fb_view'] = 'button_count';
	if (!isset($options['fb_width'])) $options['fb_width'] = '225';
	if (!isset($options['fb_text'])) $options['fb_text'] = 'like';
	if (!isset($options['fb_color'])) $options['fb_color'] = 'light';

	if ($options['fb_view'] == 'button_count') $fb_view = 'button_count';
	elseif ($options['fb_view'] == 'box_count') $fb_view = 'box_count';
	elseif ($options['fb_view'] == 'standard') $fb_view = 'standard';

	$fb_width = $options['fb_width'];

	if ($options['fb_text'] == 'like') $fb_text = 'like';
	elseif ($options['fb_text'] == 'recommend') $fb_text = 'recommend';

	if ($options['fb_color'] == 'light') $fb_color = 'light';
	elseif ($options['fb_color'] == 'dark') $fb_color = 'dark';

	$fb_out = '<div class="fb-like" data-layout="' . $fb_view . '" data-href="' . $pg_url . '" data-width="' . $fb_width . '" data-action="' . $fb_text . '" data-colorscheme="' . $fb_color .'" data-share="false"></div>';

	// ВК
	if (!isset($options['vk_view'])) $options['vk_view'] = 'round';
	if (!isset($options['vk_text'])) $options['vk_text'] = 'Сохранить';

	if ($options['vk_view'] == 'round') $vk_view = 'round';
	elseif ($options['vk_view'] == 'round_nocount') $vk_view = 'round_nocount';
	elseif ($options['vk_view'] == 'button') $vk_view = 'button';
	elseif ($options['vk_view'] == 'button_nocount') $vk_view = 'button_nocount';
	elseif ($options['vk_view'] == 'link') $vk_view = 'link';
	elseif ($options['vk_view'] == 'custom') $vk_view = 'custom';

	$vk_text = $options['vk_text'];
	if ($options['vk_view'] == 'custom') $vk_text = '<img src=\"http://vk.com/images/vk32.png?1\">';

	$vk_out = '<script type="text/javascript">document.write(VK.Share.button(
		{url: "' . $pg_url . '", title: "' . $pg_title . '", description: "' . $pg_desc . '"},
		{type: "' . $vk_view . '", text: "' . $vk_text . '"}
	));</script>';

	// ВК виджет
	if (!isset($options['vk_wg_view'])) $options['vk_wg_view'] = 'button';
	if (!isset($options['vk_wg_text'])) $options['vk_wg_text'] = 'like';
	if (!isset($options['vk_wg_height'])) $options['vk_wg_height'] = '22';
	if (!isset($options['vk_wg_width'])) $options['vk_wg_width'] = '350';
	if (!isset($options['vk_wg_apiid'])) $options['vk_wg_apiid'] = '';

	if ($options['vk_wg_view'] == 'button') $vk_wg_view = 'button';
	elseif ($options['vk_wg_view'] == 'full') $vk_wg_view = 'full';
	elseif ($options['vk_wg_view'] == 'mini') $vk_wg_view = 'mini';
	elseif ($options['vk_wg_view'] == 'vertical') $vk_wg_view = 'vertical';

	if ($options['vk_wg_text'] == 'interest') $vk_wg_text = '1';
	else $vk_wg_text = '0';

	if ($options['vk_wg_height'] == '22') $vk_wg_height = '22';
	elseif ($options['vk_wg_height'] == '18') $vk_wg_height = '18';
	elseif ($options['vk_wg_height'] == '20') $vk_wg_height = '20';
	elseif ($options['vk_wg_height'] == '24') $vk_wg_height = '24';

	$vk_wg_width = $options['vk_wg_width'];
	$vk_wg_apiid = $options['vk_wg_apiid'];

	$vk_wg_out = '<script>
		window.vkAsyncInit = function() {
				VK.init({apiId: ' . $vk_wg_apiid . ', onlyWidgets: true});
				VK.Widgets.Like(\'vk_like\', {pageUrl: \''. $pg_url .'\', type: \''. $vk_wg_view. '\', verb: ' . $vk_wg_text . ', width: ' . $vk_wg_width . ', height: ' . $vk_wg_height . '});
		}
	</script>
	<div id="vk_like"></div>';

	// Google
	if (!isset($options['ggl_size'])) $options['ggl_size'] = 'standard';
	if (!isset($options['ggl_count'])) $options['ggl_count'] = 'bubble';
	if (!isset($options['ggl_align'])) $options['ggl_align'] = 'left';
	if (!isset($options['ggl_width'])) $options['ggl_width'] = '250';

	if ($options['ggl_size'] == 'standard') $ggl_size = 'standard';
	elseif ($options['ggl_size'] == 'small') $ggl_size = 'small';
	elseif ($options['ggl_size'] == 'middle') $ggl_size = 'medium';
	elseif ($options['ggl_size'] == 'big') $ggl_size = 'tall';

	if ($options['ggl_count'] == 'bubble') $ggl_count = 'bubble';
	elseif ($options['ggl_count'] == 'inline') $ggl_count = 'inline';
	elseif ($options['ggl_count'] == 'nocount') $ggl_count = 'none';

	if ($options['ggl_align'] == 'left') $ggl_align = 'left';
	elseif ($options['ggl_align'] == 'right') $ggl_align = 'right';

	if ($options['ggl_count'] == 'inline') $ggl_width = 'data-width="' . $options['ggl_width'] . '"';
	else $ggl_width = '';

	$ggl_out = '<div class="g-plusone" data-href="' . $pg_url . '" data-size="' . $ggl_size . '" data-annotation="' . $ggl_count . '" data-align="' . $ggl_align . '" ' . $ggl_width . '></div>';

	// Pocket
	if (!isset($options['pkt_view'])) $options['pkt_view'] = 'horizontal';
	if (!isset($options['pkt_align'])) $options['pkt_align'] = 'left';

	if ($options['pkt_view'] == 'horizontal') $pkt_view = 'horizontal';
	elseif ($options['pkt_view'] == 'vertical') $pkt_view = 'vertical';
	else $pkt_view = 'none';

	if ($options['pkt_align'] == 'left') $pkt_align = 'left';
	elseif ($options['pkt_align'] == 'right') $pkt_align = 'right';

	$pkt_out = '<a href="https://getpocket.com/save" class="pocket-btn" data-lang="en" data-save-url="' . $pg_url . '" data-pocket-count="' . $pkt_view . '" data-pocket-align="' . $pkt_align . '">Pocket</a>';

	// Мой мир
	if (!isset($options['mm_view'])) $options['mm_view'] = 'horizontal';
	if (!isset($options['mm_size'])) $options['mm_size'] = 'medium';
	if (!isset($options['mm_text'])) $options['mm_text'] = 'like';
	if (!isset($options['mm_rounding'])) $options['mm_rounding'] = 'strong';

	if ($options['mm_view'] == 'horizontal')
	{
		$mm_view = '';
		$mm_view_nc = '';
	}
	elseif ($options['mm_view'] == 'vertical')
	{
		$mm_view = '\'vt\': \'1\', ';
		$mm_view_nc = '';
	}
	else
	{
		$mm_view = '';
		$mm_view_nc = '\'nc\': \'1\', ';
	}

	if ($options['mm_size'] == 'medium') $mm_size = 20;
	elseif ($options['mm_size'] == 'large') $mm_size = 30;

	if ($options['mm_text'] == 'like')
	{
		$mm_text_nt = '';
		$mm_text = 1;
	}
	elseif ($options['mm_text'] == 'share')
	{
		$mm_text_nt = '';
		$mm_text = 2;
	}
	elseif ($options['mm_text'] == 'recommend')
	{
		$mm_text_nt = '';
		$mm_text = 3;
	}
	elseif ($options['mm_text'] == 'notext')
	{
		$mm_text_nt = '\'nt\': \'1\', ';
		$mm_text = 1;
	}

	if ($options['mm_rounding'] == 'strong') $mm_rounding = 1;
	elseif ($options['mm_rounding'] == 'weak') $mm_rounding = 2;
	elseif ($options['mm_rounding'] == 'right') $mm_rounding = 3;

	$mm_out = '<a target="_blank" class="mrc__plugin_uber_like_button" href="' . $pg_url . '" data-mrc-config="{' . $mm_view . $mm_view_nc . $mm_text_nt . '\'cm\': \'' . $mm_text . '\', \'sz\': \''. $mm_size .'\', \'st\': \'' . $mm_rounding . '\', \'tp\': \'mm\'}">Нравится</a>
	<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>';

	// Одноклассники
	if (!isset($options['ok_view'])) $options['ok_view'] = 'horizontal';
	if (!isset($options['ok_size'])) $options['ok_size'] = 'medium';
	if (!isset($options['ok_text'])) $options['ok_text'] = 'cool';
	if (!isset($options['ok_rounding'])) $options['ok_rounding'] = 'strong';

	if ($options['ok_view'] == 'horizontal') $ok_view = '';
	elseif ($options['ok_view'] == 'vertical') $ok_view = ',vt:\'1\'';
	else $ok_view = ',nc:1';

	if ($options['ok_size'] == 'medium') $ok_size = 20;
	elseif ($options['ok_size'] == 'large') $ok_size = 30;

	if ($options['ok_text'] == 'cool') $ok_text = 'ck:1';
	elseif ($options['ok_text'] == 'share') $ok_text = 'ck:2';
	elseif ($options['ok_text'] == 'like') $ok_text = 'ck:3';
	elseif ($options['ok_text'] == 'notext') $ok_text = 'nt:1';

	if ($options['ok_rounding'] == 'strong') $ok_rounding = '\'oval\'';
	elseif ($options['ok_rounding'] == 'weak') $ok_rounding = '\'rounded\'';
	elseif ($options['ok_rounding'] == 'right') $ok_rounding = '\'straight\'';

	$ok_out = '<div id="ok_shareWidget"></div>
	<script>
		!function (d, id, did, st) {
				var js = d.createElement("script");
				js.src = "http://connect.ok.ru/connect.js";
				js.onload = js.onreadystatechange = function () {
					if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
						if (!this.executed) {
							this.executed = true;
							setTimeout(function () {OK.CONNECT.insertShareWidget(id,did,st);}, 0);
						}
				}};
				d.documentElement.appendChild(js);
			}(document,"ok_shareWidget","' . $pg_url . '","{st:' . $ok_rounding . ',sz:' . $ok_size . ',' . $ok_text . $ok_view . '}");
	</script>';

	// Вывод кнопок
	if ($options['block_is_type'] == '0' and isset($options['buttons_on']) and $options['buttons_on'])
	{
		if (in_array($page['page_type_name'], $block_page_type) and !in_array($page['page_id'], $block_page_exclude))
		{
			if (is_type('page'))
			{
				echo '<div class="social-buttons">' . $text_before . '<div class="social-buttons_grid">';
					foreach ($buttons_on as $button)
					{
						if ($button == 'btn_twitter') echo '<div class="social-buttons_cell social-buttons_tw">'. $tw_out . '</div>';
						elseif ($button == 'btn_facebook') echo '<div class="social-buttons_cell social-buttons_fb">' . $fb_out . '</div>';
						elseif ($button == 'btn_vkontakte') echo '<div class="social-buttons_cell social-buttons_vk">' . $vk_out . '</div>';
						elseif ($button == 'btn_vkontakte_wg') echo '<div class="social-buttons_cell social-buttons_vk-wg">' . $vk_wg_out . '</div>';
						elseif ($button == 'btn_google') echo '<div class="social-buttons_cell social-buttons_ggl">' . $ggl_out . '</div>';
						elseif ($button == 'btn_pocket') echo '<div class="social-buttons_cell social-buttons_pkt">' . $pkt_out . '</div>';
						elseif ($button == 'btn_mm') echo '<div class="social-buttons_cell social-buttons_mm">' . $mm_out . '</div>';
						elseif ($button == 'btn_ok') echo '<div class="social-buttons_cell social-buttons_ok">' . $ok_out . '</div>';
					}
				echo $text_after . '</div></div> <!--/.social-buttons-->';
			}
			else
			{
				echo '<div class="social-buttons">' . $text_before . '<div class="social-buttons_grid">';
					foreach ($buttons_on as $button)
					{
						if ($button == 'btn_twitter') echo '<div class="social-buttons_cell social-buttons_tw">'. $tw_out . '</div>';
						elseif ($button == 'btn_facebook') echo '<div class="social-buttons_cell social-buttons_fb">' . $fb_out . '</div>';
						elseif ($button == 'btn_vkontakte') echo '<div class="social-buttons_cell social-buttons_vk">' . $vk_out . '</div>';
						elseif ($button == 'btn_google') echo '<div class="social-buttons_cell social-buttons_ggl">' . $ggl_out . '</div>';
						elseif ($button == 'btn_pocket') echo '<div class="social-buttons_cell social-buttons_pkt">' . $pkt_out . '</div>';
						elseif ($button == 'btn_mm') echo '<div class="social-buttons_cell social-buttons_mm">' . $mm_out . '</div>';
					}
				echo $text_after . '</div></div> <!--/.social-buttons-->';
			}
		}
	}
	elseif ($options['block_is_type'] == '1' and is_type('page') and isset($options['buttons_on']) and $options['buttons_on'])
	{
		if (in_array($page['page_type_name'], $block_page_type) and !in_array($page['page_id'], $block_page_exclude))
		{
			echo '<div class="social-buttons">' . $text_before . '<div class="social-buttons_grid">';
				foreach ($buttons_on as $button)
				{
					if ($button == 'btn_twitter') echo '<div class="social-buttons_cell social-buttons_tw">'. $tw_out . '</div>';
					elseif ($button == 'btn_facebook') echo '<div class="social-buttons_cell social-buttons_fb">' . $fb_out . '</div>';
					elseif ($button == 'btn_vkontakte') echo '<div class="social-buttons_cell social-buttons_vk">' . $vk_out . '</div>';
					elseif ($button == 'btn_vkontakte_wg') echo '<div class="social-buttons_cell social-buttons_vk-wg">' . $vk_wg_out . '</div>';
					elseif ($button == 'btn_google') echo '<div class="social-buttons_cell social-buttons_ggl">' . $ggl_out . '</div>';
					elseif ($button == 'btn_pocket') echo '<div class="social-buttons_cell social-buttons_pkt">' . $pkt_out . '</div>';
					elseif ($button == 'btn_mm') echo '<div class="social-buttons_cell social-buttons_mm">' . $mm_out . '</div>';
					elseif ($button == 'btn_ok') echo '<div class="social-buttons_cell social-buttons_ok">' . $ok_out . '</div>';
				}
			echo $text_after . '</div></div> <!--/.social-buttons-->';
		}
	}
}

# end file