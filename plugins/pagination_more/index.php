<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function pagination_more_autoload() 
{
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow( basename(dirname(__FILE__)).'_options', 'Админ-доступ к опциям плагина «Pagination_more»' );
		
	mso_hook_add( 'head', basename(dirname(__FILE__)).'_head'); # подключаем стили
	mso_hook_add( 'body_end', basename(dirname(__FILE__)).'_body_end'); # подключаем скрипт
		
	$options = mso_get_option( 'plugin_'.basename(dirname(__FILE__)), 'plugins', array() ); # получаем опции
	mso_hook_add( 'pagination', basename(dirname(__FILE__)).'_go', ( isset($options['priority']) && $options['priority'] ? $options['priority'] : 10 ));
}

# функция выполняется при деинсталяции плагина
function pagination_more_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_'.basename(dirname(__FILE__)), 'plugins');
		
	# удалим созданные разрешения
	mso_remove_allow(basename(dirname(__FILE__)).'_options');
		
	return $args;
}

# подключаем стили плагина
function pagination_more_head($arg = array())
{
	static $pagination_more_css = false;
	
	if( !$pagination_more_css && file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/custom.css') )
	{
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/custom.css" type="text/css">'.NR;
	}
	
	$pagination_more_css = true;
	
	return $arg;
}

# подключаем js-часть плагина
function pagination_more_body_end($arg = array())
{
	static $pagination_more_js = false;
	
	if( !$pagination_more_js )
	{
		echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/engine.js"></script>'.NR; # подключаем js
	}
	
	$pagination_more_js = true;
	
	return $arg;
}

function pagination_more_go($r = array()) 
{
	global $MSO;
	
	$r_orig = $r; #pr($r);
	
	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип
	
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';
	
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array() ); // получаем опции
	if( !isset($options['caption']) || !$options['caption'] ) $options['caption'] = t('Показать больше');
	if( !isset($options['loader']) || !$options['loader'] ) $options['loader'] = t('Загружаю...');
	if( !isset($options['use']) || !$options['use'] ) return $r_orig;
	if( !isset($options['types']) || !$options['types'] ) $options['types'] = 'home, category, tag, archive';
	if( !isset($options['home-offset']) || !$options['home-offset'] ) $options['home-offset'] = '';

	# для учёта пагинации сторонних плагинов
	$options['next-slug-tmpl'] = isset($options['next-slug-tmpl']) && $options['next-slug-tmpl'] ? $options['next-slug-tmpl'] : '';
	$options['next-slug-tmpl'] = $options['next-slug-tmpl'] ? array_map('trim', explode(',', $options['next-slug-tmpl'])) : false;
	$options['next-slug-tmpl'] = $options['next-slug-tmpl'] ? implode('|', $options['next-slug-tmpl']) : false;

	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы
	$current_paged = mso_current_paged($r['next_url']);
	if( $current_paged > $r['maxcount'] ) $current_paged = $r['maxcount'];

	# текущий тип
	$types = array_map('trim', explode(',', trim($options['types'])));
	$type = $r['type'] !== false ? $r['type'] : $MSO->data['type'];
	if( !in_array($type, $types) ) return $r_orig;
	
	# текущий адрес
	$cur_url = mso_current_url();

	# удаляем следы пагинации сторонних плагинов
	if( $options['next-slug-tmpl'] && preg_match( '!\/('.$options['next-slug-tmpl'].')\/(.+?)($|\/)!is', $cur_url ) )
	{	
		$cur_url = preg_replace('!\/('.$options['next-slug-tmpl'].')\/(.+?)($|\/)!is', '', $cur_url);
	}

	$cur_url = preg_replace('!\/next\/(.+?)!is', '', $cur_url);
	
	# Куда отправлять AJAX-запросы
	$ajax_path = getinfo('ajax').base64_encode('plugins/'.basename(dirname(__FILE__)).'/do-ajax.php');

	if( $current_paged < $r['maxcount'] )
	{
		echo 
			'<div class="pagination pages"><nav><div class="button">'.NR.
				'<a data-ajax="'.$ajax_path.'" data-current="'.$current_paged.'" data-max="'.$r['maxcount'].'" data-type="'.$type.'" data-limit="'.$r['limit'].'" data-base="'.$cur_url.'">'.$options['caption'].'</a>'.NR.
				'<span>'.$options['loader'].'</span>'.NR.
			'</div></nav></div>';
	}
	
	return $r_orig;
}

function pagination_more_mso_options() 
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_'.basename(dirname(__FILE__)), 'plugins', 
		array(
			'use' => array(
							'type' => 'checkbox', 
							'name' => t('Использовать more-пагинацию'), 
							'description' => 'При установленной галочке плагин начинает срабатывать на хук <b>pagination</b>, т.е. включается в работу. Иначе пагинация этим плагином выводится не будет.', 
							'default' => '0'
						),
			'priority' => array(
							'type' => 'text', 
							'name' => t('Приоритет запуска плагина'), 
							'description' => t('Задайте свой приоритет для хука на вывод пагинации, чтобы задать порядок вывода в случае использования нескольких типов пагинации одновременно. Если нужно, чтобы выводился раньше всех, то ставим более 10. Если нужно выводить последним - ставим приоритет менее 10.'), 
							'default' => '10'
						),
			'caption' => array(
							'type' => 'text', 
							'name' => t('Надпись на кнопке'), 
							'description' => t('Задайте надпись, которая будет отображаться на кнопке подгрузки новой порции записей.'), 
							'default' => 'Показать ещё'
						),
			'loader' => array(
							'type' => 'text', 
							'name' => t('Надпись на кнопке в момент загрузки'), 
							'description' => t('Задайте надпись, которая будет отображаться на кнопке в момент загрузки. Можно использовать HTML для создания прелоадера.'), 
							'default' => 'Загружаю...'
						),
			'types' => array(
							'type' => 'text', 
							'name' => t('Где выводить'), 
							'description' => t('Задайте типы страниц шаблона, на которых будет работать плагин. Доступны значения: home, category, tag. Перечислите через запятую. Оставьте пустым, если надо работать на всех доступных типах.'), 
							'default' => ''
						),
			'home-offset' => array(
							'type' => 'text', 
							'name' => t('Сколько пропускать записей для вывода на home'), 
							'description' => t('Укажите сколько записей выводится на главной, чтобы учитывах их при расчёте пагинации на страницах <b>/home/</b>. Можно просто продублировать значение из настроек шаблона. Если оставить пустым, то в расчёте будут участвовать все страницы.'), 
							'default' => ''
						),
			'next-slug-tmpl' => array(
							'type' => 'text',
							'name' => 'Учитывать нестандартные next-сегменты в адресах',
							'description' => 'Если на сайте иcпользуются плагины, которые для своих нужд генерируют адреса с пагинацией, отличной от стандартной ("/next/"), то для корректной работы чпу нужно перечислить используемые сегменты. Например, «comment-next, more». Оставьте поле пустым, если вы не поняли о чём идёт речь.',
							'default' => ''
						),
			),
		t('Настройки плагина Pagination_more'), # титул
		t('Укажите необходимые опции. Количество подгружаемых записей настраивается опциями шаблона.' )  # инфо
	);

	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# end file