<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function admin_announce_autoload($args = array())
{
	mso_hook_add('admin_home', 'admin_announce'); # хук на админ-анонс
	mso_hook_add('admin_head', 'admin_announce_head');
}

# функция выполняется при активации (вкл) плагина
function admin_announce_activate($args = array())
{	
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow('admin_announce_options', 'Админ-доступ к опциям плагина «Админ-анонс»');
		
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function admin_announce_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_admin_announce', 'plugins' );
		
	# удалим созданные разрешения
	mso_remove_allow('admin_announce_options');
	return $args;
}

function admin_announce_head($args = array()) 
{
	$hide = '';
	if( mso_segment(1)=='admin' and ( mso_segment(2)=='home' or mso_segment(2)=='' ) )
	{
		$options = mso_get_option('plugin_admin_announce', 'plugins', array());
			
		# подключаем стили
		echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'admin_announce/style.css">' . NR;
		if( file_exists(getinfo('plugins_dir').'admin_announce/custom.css') )
		{
			echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'admin_announce/custom.css">' . NR;
		}
			
		# попытка подключить табы
		if( function_exists('tabs_head') )
		{
			$args = tabs_head($args);
		}
			
		# формируем команды для реализации украшательств
		if( isset($options['hide_main_list']) and $options['hide_main_list'] == 1 )
		{
			$hide .= '$(".admin-menu-bread + h1").next().hide().next().hide();'.NR;
		}
		if( isset($options['hide_version_info']) and $options['hide_version_info'] == 1 )
		{
			$hide .= '$(".admin-menu-bread + h1 + br + ul").next().hide();'.NR;
			$hide .= '$(".admin-menu-bread ~ form button[name=\"f_submit_check_version\"]").hide();'.NR;
		}
		if( isset($options['hide_cache_button']) and $options['hide_cache_button'] == 1 )
		{
			$hide .= '$(".admin-menu-bread ~ form button[name=\"f_submit_clear_cache\"]").hide();'.NR;
		}
		if( isset($options['page_title']) and trim($options['page_title']) <> '' )
		{
			$hide .= '$(".admin-menu-bread ~ h1").html("'.addslashes(trim($options['page_title'])).'");'.NR;
		}
		if( isset($options['hide_title']) and $options['hide_title'] == 1 )
		{
			$hide .= '$(".admin-menu-bread + h1").hide();'.NR;
		}
		
		# подключение сортировщика таблиц
		echo mso_load_jquery('jquery.tablesorter.js');
		echo NR . '
		<script>
			$(function() {
				$("table.tablesorter").tablesorter();
				'.$hide.'
			});
		</script>';
	}
		
	return $args;
}

# формирование страницы опций в админке
function admin_announce_mso_options()
{
	if( !mso_check_allow('admin_announce_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	$options = mso_get_option('plugin_admin_announce', 'plugins', array());
	if( !isset($options['slug']) ) $options['slug'] = 'admin_announce';
		
	# выносим функции формирования массива опций в отдельный файл
	require(getinfo('plugins_dir').'admin_announce/backend-options.php');
}

# Формирование массива табов со статистикой
function admin_announce_stats($tabs = array(), $options = array())
{
	global $MSO;
	$CI = & get_instance();
	$cache_key = 'admin_announce_pages';
		
	###
	# Формируем данные для закладки «Статистика»
	$CI->db->select('page_id, page_title, page_date_publish, page_slug, page_view_count');
		
	$time_zone = getinfo('time_zone');
	if( $time_zone < 10 and $time_zone > 0 )
	{
		$time_zone = '0' . $time_zone;
	}
	elseif( $time_zone > -10 and $time_zone < 0 ) 
	{
		$time_zone = '0' . $time_zone;
		$time_zone = str_replace('0-', '-0', $time_zone);
	}
	else
	{
		$time_zone = '00.00';
	}
		
	$time_zone = str_replace('.', ':', $time_zone);
		
	if( !$options['show_future'] )
	{
		$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $time_zone . '" HOUR_MINUTE)', false);
	}
		
	$CI->db->where('page_status', 'publish');
	$CI->db->from('page');
	$query = $CI->db->get();

	$all_title = $query->result_array();

	$summ = $count = $avgcount = 0;
	$maxcount = $mincount = $all_title[0]['page_view_count'];
	foreach( $all_title as $page )
	{
		$count++;
		$summ += $page['page_view_count'];
		if( $maxcount < $page['page_view_count'] ) $maxcount = $page['page_view_count'];
		if( $mincount > $page['page_view_count'] ) $mincount = $page['page_view_count'];
	};
		
	$avgcount = $summ/$count;

	$users_id = $MSO->data['session']['users_id'];
	$CI->db->select('users_id, users_login, users_nik, users_last_visit, users_avatar_url');
	$CI->db->from('users');
	$query = $CI->db->get();
	$all_users = $query->result_array();

	$CI->load->library('table');
	$tmpl = array (
			'table_open'		  => '<table class="page tablesorter" id="table-0">',
			'row_alt_start'		  => '<tr class="alt">',
			'cell_alt_start'	  => '<td class="alt">',
			'heading_row_start' 	=> NR . '<tr>',
			'heading_row_end' 		=> '</tr>' . NR,
			'heading_cell_start'	=> '<th style="cursor: pointer;">',
			'heading_cell_end'		=> '</th>',
				);
	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Аватар'), t('ID', 'admin'), t('Логин', 'admin'), t('Ник', 'admin'), t('Время последнего визита'));
		
	foreach( $all_users as $user )
	{
		$CI->table->add_row(
							(($user['users_avatar_url'])?('<img src="'.$user['users_avatar_url'].'" width="80" height="80">'):('')),
							'<a href="/admin/users/edit/'.$user['users_id'].'" target="_blank">'.$user['users_id'].'</a>',
							$user['users_login'],
							'<a href="/author/'.$user['users_id'].'" target="_blank">'.$user['users_nik'].'</a>',
							$user['users_last_visit']
							);
		if( $user['users_id'] == $users_id )
		{
			$out = '<div class="info my">' . t('Ваш последний визит: ') . '<b>' . $user['users_last_visit'] . '</b></div>' . NR;
		}
	}

	###
	# Формируем закладку «Статистика»
	$tabs[] = array(
					t('Статистика'),
					'<div class="info"><ul>'.
					'<li>' . t('Всего опубликованных страниц: ') . '<b>' . $count . '</b></li>'.
					'<li>' . t('Всего просмотров: ') . '<b>' . $summ . '</b></li>'.
					'<li>' . t('Дельта подсчёта: ') . '<b>' . $options['delta'] . '</b></li>'.
					'<li>' . t('Максимум просмотров страницы: ') . '<b>' . $maxcount. '</b></li>'.
					'<li>' . t('Минимум просмотров страницы: '). '<b>' . $mincount.	'</b></li>'.
					'<li>' . t('В среднем: '). '<b>' . round($avgcount) . '</b></li>'.
					'</ul></div>' . NR . $out .
					$CI->table->generate()
					);
	$CI->table->clear();
	
	###
	# Формируем данные для закладки «Популярные страницы»
	$tmpl['table_open'] = '<table class="page tablesorter" id="table-1">';
	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок'), t('Просмотров'), t('Дата публикации', 'admin'));

	foreach ( $all_title as $page ) :
		if ( $page['page_view_count'] > ($maxcount - $options['delta']) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a> ' . '[<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '" class="editurl">' . t('редактировать'). '</a>]',
							$page['page_view_count'],
							$page['page_date_publish']
							);
	endforeach;

	###
	# Формируем закладку «Популярные страницы»
	$tabs[] = array(
					t('Популярные страницы'),
					$CI->table->generate()
					);
	$CI->table->clear();

	###
	# Формируем данные для закладки «Средние»
	$tmpl['table_open'] = '<table class="page tablesorter" id="table-2">';
	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок'), t('Просмотров'), t('Дата публикации', 'admin'));

	foreach ( $all_title as $page ) :
		if ( ($page['page_view_count'] < ($avgcount + $options['delta'])) and ($page['page_view_count'] > ($avgcount - $options['delta'])) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a> ' . '[<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '" class="editurl">' . t('редактировать'). '</a>]',
							$page['page_view_count'],
							$page['page_date_publish']
							);
	endforeach;

	###
	# Формируем закладку «Средние»
	$tabs[] = array(
					t('Средние'),
					$CI->table->generate()
					);
	$CI->table->clear();

	###
	# Формируем данные для закладки «Непопулярные страницы»
	$tmpl['table_open'] = '<table class="page tablesorter" id="table-3">';
	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок'), t('Просмотров'), t('Дата публикации', 'admin'));

	foreach ( $all_title as $page ) :
		if ( $page['page_view_count'] < ($mincount + $options['delta']) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a> ' . '[<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '" class="editurl">' . t('редактировать'). '</a>]',
							$page['page_view_count'],
							$page['page_date_publish']
							);
	endforeach;

	###
	# Формируем закладку «Непопулярные страницы»
	$tabs[] = array(
					t('Непопулярные страницы'),
					$CI->table->generate()
					);
	
	return $tabs;
}

# Формирование вывода Анонса
function admin_announce($args = array())
{
	# Читаем настройки плагина и задаём значения по-умолчанию
	$options = mso_get_option('plugin_admin_announce', 'plugins', array());
	
	if( !isset($options['tab_text']) )  $options['tab_text']  = '';
	if( !isset($options['tab_title']) )  $options['tab_title']  = t('Админ-анонс');
	if( !isset($options['admin_statistic']) ) $options['admin_statistic'] = true; // По умолчанию показываем статистику.
	if( !isset($options['admin_showall']) )   $options['admin_showall']   = true; // По умолчанию показываем статистику всем.
	if( !isset($options['delta']) or ($options['delta'] == 0) ) $options['delta']	= 10;
	if( !isset($options['show_future']) )     $options['show_future']     = true;
		
	# Подключение кастомной вкладки админа
	$tabs = array();
	if( trim($options['tab_text']) <> '' and isset($options['hide_tab']) and !$options['hide_tab'] )
	{
		$tabs[] = array( $options['tab_title'] ,  NR. '<div class="info">'. $options['tab_text']. '</div>'. NR );
	}
		
	# Подключаем к выводу табы со статистикой
	if( $options['admin_statistic'] and ($options['admin_showall'] or mso_check_allow('admin_announce_options')) )
	{
		$tabs = admin_announce_stats($tabs, $options);
	}
		
	# Подключаем табы сторонних плагинов
	$tabs = mso_hook('admin_announce', $tabs);
		
	# Формирование всего кода вкладок
	$out = '';
	if( function_exists('tabs_content') )
	{
		$out = NR.'[tabs]'.NR;
			
		foreach( $tabs as $key => $tab )
		{
			$out .= '[tab=' . $tab[0] . ']' . $tab[1] . '[/tab]' . NR;
		}
			
		$out .= '[/tabs]' . NR;
	}
		
	$out = preg_replace('/\[html\](.*?)\[\/html\]/i', '\\1', mso_hook('content', $out));
	echo $out;
		
	return $args;
}

# end file