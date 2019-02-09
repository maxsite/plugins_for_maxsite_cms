<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


/*
1. выпадающие списки с именами файлов
2. только залогинение могут
3. засерить для незалогиненых
4. ваш голос учтен?
5. поле в page или 2 поля, карма суммируется, но есть положительная и отрицательная
6. в этом случае при инсталяции/деинсталяции нудна модификация БД

*/
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function pagerate_autoload()
{
	if (!is_feed() and (is_type('page') or is_type('home')))
	{	
		mso_hook_add( 'head', 'pagerate_head');
		mso_hook_add('content_content', 'pagerate_content'); # хук на вывод контента
		
	}	
	/* при подключении плагина надо создать столбец*/
}


# функция выполняется при деинсталяции плагина
function pagerate_uninstall($args = array())
{	
	mso_delete_option('plugin_pagerate', 'plugins'); // удалим созданные опции
	/*
		удалить столбец из таблицы
	*/
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function pagerate_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagerate', 'plugins', 
		array(
/*			'align' => array(
						'type' => 'select', 
						'name' => 'Выравнивание блока', 
						'description' => 'Укажите выравнивание блока. Он добавляется в начало каждой записи.',
						'values' => 'left||Влево # right||Вправо # none||Нет',
						'default' => 'right'
					),*/
			/*
			'style' => array(
						'type' => 'text', 
						'name' => 'Стиль блока', 
						'description' => 'Укажите свой css-стиль блока.', 
						'default' => ''
					), 
			*/
			'header' => array(
						'type' => 'text', 
						'name' => 'Заголовок', 
						'description' => 'Заголовок блока рейтинга', 
						'default' => ''
					),		
			'nostatic' => array(
						'type' => 'checkbox', 
						'name' => 'Кроме статических страниц', 
						'description' => 'Страницы static не учавствуют рейтинге', 
						'default' => '1'
					),	
			'show_only_page' => array(
						'type' => 'select', 
						'name' => 'Отображение', 
						'description' => 'Выводить ли блок только на одиночной странице',
						'values' => '1||Отображать только на одиночной странице # 0||Везде',
						'default' => '0'
					),	
			'only_logged' => array(
						'type' => 'checkbox', 
						'name' => 'Анонимные не голосуют', 
						'description' => 'Только залогиненые пользователи могут голосовать за пост',
						'default' => ''
					),							
			'send_mail' => array(
						'type' => 'select', 
						'name' => 'Уведомлять по почте', 
						'description' => 'Уведомлять по почте при изменении рейтинга записи',
						'values' => '3||При изменении minus и plus # 2||Только при изменении plus #1||Только при изменении minus # 0||Не уведомлять',
						'default' => '0'
					),		
			'mail_subject' => array(
						'type' => 'text', 
						'name' => 'Тема письма', 
						'description' => 'Укажите тему письма, отправляемого при изменении рейтинга записи', 
						'default' => 'Изменение рейтинга'
					),						
			'disable_minus' => array(
						'type' => 'checkbox', 
						'name' => 'Запрет минусования', 
						'description' => 'Отметьте галкой, что Вы запрещаете "минусование" постов', 
						'default' => ''
					),		
			'pagerate_minus_img' => array(
						'type' => 'text', 
						'name' => 'Картинка кнопки "минус"', 
						'description' => 'Укажите название файла картинки, файл должен быть в корне папки плагина', 
						'default' => 'pagerate_minus.gif'
					),					
					
			'pagerate_minus_text' => array(
						'type' => 'text', 
						'name' => 'Подсказка кнопки "минус"', 
						'description' => 'Укажите подсказку кнопки', 
						'default' => 'Неинтересный пост'
					),	
					
			'pagerate_plus_img' => array(
						'type' => 'text', 
						'name' => 'Картинка кнопки "плюс"', 
						'description' => 'Укажите название файла картинки, файл должен быть в корне папки плагина', 
						'default' => 'pagerate_plus.gif'
					),						
			'pagerate_plus_text' => array(
						'type' => 'text', 
						'name' => 'Подсказка кнопки "плюс"', 
						'description' => 'Укажите подсказку кнопки', 
						'default' => 'Интересный пост'
					),						
			),
		'Настройки плагина PageRate', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина

function pagerate_head($args = array())
{
	mso_load_jquery();
	$path = getinfo('plugins_url') . 'pagerate/';
	echo '<script type="text/javascript" src="' . $path . 'jquery.pagerate.js"></script>' . NR;
	echo '<link rel="stylesheet" href="' . $path . 'pagerate.css" type="text/css" media="screen">' . NR;	
	
	$user_logged = ( is_login() or is_login_comuser() ) ? 1 : 0;
	echo '<script type="text/javascript">
		var path_ajax = "' . getinfo('ajax') . base64_encode('plugins/pagerate/pagerate-ajax.php') . '";
		var user_logged = "' . $user_logged  . '";
	</script>';

	
	
}

function pagerate_content($text = '')
{
	global $page;
	
	
	if (!is_type('page') and !is_type('home')) return $text;
	
	
	$options = mso_get_option('plugin_pagerate', 'plugins', array() ); // получаем опции
	
	// отображать только на одиночной странице
	if (!isset($options['show_only_page'])) $options['show_only_page'] = 0; 
	if ($options['show_only_page'] and !is_type('page')) return $text;
	
	if (!isset($options['header'])) $options['header'] = '';
	if (!isset($options['nostatic'])) $options['nostatic'] = 1;
	if ( ( $page['page_type_name'] == 'static' ) and ($options['nostatic'] == 1) ) return $text;
	
	if (!isset($options['style'])) $options['style'] = '';
	if (!isset($options['align'])) $options['align'] = 'right';
	if (!isset($options['only_logged'])) $options['only_logged'] = '';
	if (!isset($options['disable_minus'])) $options['disable_minus'] = '';
	
	if (!isset($options['send_mail'])) $options['send_mail'] = 0;
	if (!isset($options['mail_subject'])) $options['mail_subject'] = 'Изменение рейтинга';
	
	if (!isset($options['pagerate_minus_img'])) $options['pagerate_minus_img'] = 'pagerate_minus.gif';
	if (!isset($options['pagerate_plus_img'])) $options['pagerate_plus_img'] = 'pagerate_plus.gif';
	if (!isset($options['pagerate_minus_text'])) $options['pagerate_minus_text'] = 'Неинтересный пост';
	if (!isset($options['pagerate_plus_text'])) $options['pagerate_plus_text'] = 'Интересный пост';
	
		echo '<script type="text/javascript">
		var only_logged = "' . $options['only_logged']  . '";
		var send_mail = "' . $options['send_mail'] . '";
		var page_title ="' . $page['page_title'] . '";
		var page_slug ="' . $page['page_slug'] . '";
		</script>';
			
	$path = getinfo('ajax') . base64_encode('plugins/pagerate/pagerate-ajax.php');
	
	$style = '';
	$js = '';

	$parerate_rating = '0';
	/* get from db */
	$CI = & get_instance();
	$CI->db->select('meta_value');
	$CI->db->from('meta');
	$CI->db->where('meta_key', 'pagerate_value');
	$CI->db->where('meta_id_obj', $page['page_id']);
	$CI->db->limit(1);
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
	{
		$row = $query->row();
		$pagerate_rating = $row->meta_value;
	} else {
			# иначе значение из параметров - N/A
		$pagerate_rating = '0';
	}
	

	$classx = '';
	if ( $pagerate_rating > 0 ) {
		$classx = 'pagerate_value_plus';
	} else if ( $pagerate_rating < 0 ){
		$classx = 'pagerate_value_minus';
	} else {
		$classx = 'pagerate_value_null';
	}
	$pagerate_rating = '<span class="' . $classx . '">' . $pagerate_rating . '</span>';
		
	/* ------------- */	
	/* если запрещаем минусовать, то ... */
	$img_path = getinfo('plugins_url') . 'pagerate/';
	$img_sux = '<img src="' . $img_path . $options['pagerate_minus_img'] . '" alt="' . $options['pagerate_minus_text'] . '" />';
	$img_cool = '<img src="' . $img_path . $options['pagerate_plus_img'] . '" alt="' . $options['pagerate_plus_text'] . '" />';
	
	$pagerate_out = '';
	$pagerate_out .= '<div id="pagerate">';
	
	/* заголовок */
	$pagerate_out .= '<h4 class="pagerate_header">' . $options['header'] . '</h4>';
	
	if ( $options['disable_minus'] != 1 ) {
		$pagerate_out .= '<div id="pagerate_minus"><a onclick="vote(' . $page['page_id'] . ',0)" title="' . $options['pagerate_minus_text'] . '">' . $img_sux . '</a></div>';
	}	
	$pagerate_out .= '<div id="pagerate_rating"><span class="pagerate_value" id="pagerate_' . $page['page_id'] . '">' . $pagerate_rating . '</span></div>';
	$pagerate_out .= '<div id="pagerate_plus"><a onclick="vote(' . $page['page_id'] . ',1)" title="' . $options['pagerate_plus_text'] . '">' . $img_cool . '</a></div>';


	$pagerate_out .= '<div class="bubbleInfo bubbleInfo_' . $page['page_id'] . '">'.
					 //'<img class="trigger trigger_' . $page['page_id'] . '" src="' . $img_path  . 'popup.png" />'.
					 '	<div class="popup popup_' . $page['page_id'] . '">' .
					 '		<div class="popup_top"></div>' .
					 '		<div class="popup_center"></div>' .
					 '		<div class="popup_bottom"></div>' .					 					 
					 '	</div>'. 
					 '</div>';


	$pagerate_out .= '</div>';
	
	// ищем %PAGERATE%
	/*
	if ( strpos( $text, '%PAGERATE%' ) !== false )
	{
		$text = str_replace( '%PAGERATE%', $pagerate_out, $text );
	} else {
		$text = $pagerate_out . $text;
	}	
	*/
	$text = $pagerate_out . $text;
	
	return $text;
}


# end file
