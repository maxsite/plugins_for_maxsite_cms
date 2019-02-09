<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 */
 
# функция автоподключения плагина
function comments_autoload($args = array())
{
	mso_hook_add('head', basename(dirname(__FILE__)).'_head'); # подключаем стили
	mso_hook_add('body_end', basename(dirname(__FILE__)).'_body_end'); # подключаем скрипт
	mso_hook_add('content_in', basename(dirname(__FILE__)).'_page_content', 99); # контроль вывода контента страницы
	mso_hook_add('type-foreach-file', basename(dirname(__FILE__)).'_type_foreach_remap'); # хук для перебивки foreach_file

	mso_hook_add('custom_ts_file', basename(dirname(__FILE__)).'_custom_ts_file'); # реакция на хук custom_ts_file для вывода каментов
	mso_hook_add('page_comments', basename(dirname(__FILE__)).'_show'); # выводим каменты если используется наш хук page_comments

	mso_hook_add('comments_pagination', basename(dirname(__FILE__)).'_pagination'); # реакция на пагинацию
	mso_hook_add('head_start', basename(dirname(__FILE__)).'_head_start'); # для добавления примеси в мета-информацию страницы при пагинации
	
	mso_hook_add('xml_sitemap', basename(dirname(__FILE__)).'_xml_sitemap'); # выводим пагинацию каментов в sitemap.xml

	mso_hook_add('admin_content_do', basename(dirname(__FILE__)).'_admin_content_do'); # переделываем ссылки в панели упралвения комментариями
	mso_hook_add('mso_admin_content', basename(dirname(__FILE__)).'_admin_content'); # переделываем ссылки в панели упралвения комментариями

}

# функция выполняется при активации (вкл) плагина
function comments_activate($args = array())
{	
	mso_create_allow(basename(dirname(__FILE__)).'_options', t('Админ-доступ к настройкам') . ' ' . t('Comments'));
		
	return $args;
}

function comments_uninstall($args = array())
{	
	mso_remove_allow(basename(dirname(__FILE__)).'_options'); # удалим созданные разрешения
		
	return $args;
}

# реакция на хук custom_ts_file - выводим комментарии
function comments_custom_ts_file( $fn )
{
	if( $fn == 'type/page/units/page-comments.php' )
	{
		return getinfo('plugins_dir').basename(dirname(__FILE__)).'/units/page-comments-custom-file.php';
	}
}

# подключаем стили плагина
function comments_head($arg = array())
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());

	if( !isset($options['comments_default_css']) ) $options['comments_default_css'] = false;

	static $comments_css = false;

	if( is_type('page') && !$comments_css && $options['comments_default_css'] )
	{
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/default.css" type="text/css">'.NR;
			
		$comments_css = true;
	}
	
	if( is_type('page') && !$comments_css && file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/custom.css') )
	{
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/custom.css" type="text/css">'.NR;
			
		$comments_css = true;
	}

	return $arg;
}

# подключаем js-часть плагина
function comments_body_end($arg = array())
{
	static $comments_js = false;
	
	if( ( is_type('page') || is_type('users') ) && !$comments_js )
	{
		echo mso_load_jquery('jquery.scrollto.js');
			
		if( file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/jquery.comments.custom.js') )
		{
			echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/jquery.comments.custom.js"></script>'.NR; # подключаем кастомный js вместо основного
		}
		else
		{
			echo '<script src="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/jquery.comments.min.js"></script>'.NR; # подключаем js
		}
			
		$comments_js = true;
	}
		
	return $arg;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function comments_mso_options() 
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_options') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	if( !function_exists('mso_get_ini_file') )
	{
		require_once( getinfo('common_dir') . 'inifile.php' ); # функции для работы с ini-файлом
	}

	if( !file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/options.ini') ) return;
		
	$prefs = mso_get_ini_file( getinfo('plugins_dir') . basename(dirname(__FILE__)) . '/options.ini'); # и свой файл опций

	$options = array();
	foreach( $prefs as $name => $params )
	{
		$options[ $params['options_key'] ] = 
			array(
				'type' => $params['type'], 
				'name' => $name, 
				'description' => $params['description'], 
				'default' => $params['default']
			);
		if( isset( $params['values'] ) )
		{
			$options[ $params['options_key'] ]['values'] = $params['values'];
		}

		if( isset( $params['section'] ) )
		{
			$title = $params['section'];
		}

		if( isset( $params['section_description'] ) )
		{
			$description = $params['section_description'];
		}
	}
	
	#pr($prefs);
	#pr($options);

	# ключ, тип, ключи массива
	mso_admin_plugin_options( 'plugin_'.basename(dirname(__FILE__)), 'plugins', $options, $title, $description );
	
	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# основная функция
function comments_show( $page )
{
	# вывод текста комментариев в зависимости от пароля записи
	$page_text_ok = isset($page['page_password']) && $page['page_password'] ? (isset($page['page_password_ok'])) : true;
		
	# если страница не определена или комментарии запрещены, то выходим
	if( !$page || !$page['page_comment_allow'] || $page['page_status'] !== 'publish' || !$page_text_ok ) return '';
		
	global $MSO;
		
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
		
	if( !isset($options['comments_order']) ) $options['comments_order'] = 'asc';
	if( !isset($options['comments_show_type']) ) $options['comments_show_type'] = 'simple';
	if( !isset($options['comments_page_limit']) ) $options['comments_page_limit'] = 0;
	if( !isset($options['comments_child_limit']) ) $options['comments_child_limit'] = 0;
	if( !isset($options['comments_pagination_type']) ) $options['comments_pagination_type'] = 'none';
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	if( !isset($options['comments_pagination_page_content']) ) $options['comments_pagination_page_content'] = true;
	if( !isset($options['comments_max_tree_level']) ) $options['comments_max_tree_level'] = 0;
	if( !isset($options['comments_rating_min_show']) ) $options['comments_rating_min_show'] = 0;
	if( !isset($options['comments_pagination_best_show']) ) $options['comments_pagination_best_show'] = false;
	if( !isset($options['comments_replyto']) ) $options['comments_replyto'] = true;
		
	$html = ''; $comments_msg = '';
		
	require_once( getinfo('common_dir').'comments.php' ); # стандартные функции работы с комментариями
		
	# если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
	if( $out = mso_get_new_comment( array('page_title'=>$page['page_title'], 'css_ok' => 'message mini ok save', 'css_error' => 'message mini error save') ) )
	{
		$comments_msg = $out;
	}
		
	# переменные для настройки кеширования
	$cache_time = (int) mso_get_option('home_cache_time', 'templates', 0); # время жизни кэша
	$cache_uri = str_replace('/', '-', $_SERVER['REQUEST_URI']);
	$cache_person = is_login() ? ( !mso_check_allow('admin_comments') || !mso_check_allow('admin_comments_edit') ? getinfo('users_nik') : 'user' ) : ( getinfo('comusers_nik') ? 'comuser' : 'anonimous' );
		
	$cache_key = 'page-comments'.$cache_uri.'-'.$cache_person;
	if( $cache_time > 0 and $cache = mso_get_cache($cache_key) ) # пытаемся использовать полный кеш блока комментирования
	{
		$html = $cache;
			
		if( $comments_msg )
		{
			$html = preg_replace(
									'/<div class\=\"comments\_msg\"\>\<\/div\>/msi',
									$comments_msg,
									$html
								);
		}
	}
	else
	{
		# размещаем сообщение об ошибке сохранения нового комментария или задаём место для вставки сообщения (чтобы в кеше заменять)
		$comments_msg = $comments_msg ? $comments_msg : '<div class="comments_msg"></div>';
			
		# получаем все разрешенные комментарии сперва из кеша пытаемся и только потом из базы тянем
		$comments = array(); $pagination = false;
		$cache_key_src = 'page-comments-src'.$cache_uri;
		if( $cache_time > 0 and $cache = mso_get_cache($cache_key_src) ) # пытаемся использовать полный кеш блока комментирования
		{
			$comments = unserialize(str_replace('_serialize_', '', $cache));
		}
		else
		{
			# получаем список комментариев текущей страницы
			$par = array(
				'limit' => $options['comments_page_limit'],
				'order' => $options['comments_order'],
				'order_childs' => $options['comments_childs_order'],
				'max_tree_level' => $options['comments_max_tree_level'],
				'limit_child' => $options['comments_child_limit'],
				'pagination' => $options['comments_pagination_type'] != 'none' ? $options['comments_pagination_type'] : false,
				'pagination_next_url' => $options['comments_pagination_next'],
				'out_type' => $options['comments_show_type'],
			);
			$comments = comments_get_comments($page['page_id'], $par, $pagination);	#_pr($comments);
			mso_add_cache($cache_key_src, '_serialize_'.serialize( $comments ), $cache_time * 60); 
		}	
			
		# если есть комментарии или разрешено оставлять новый
		if( $comments || $page['page_comment_allow'] )
		{
			# формируем базовый адрес тарницы - удаляем следы пагинации комментариев
			$comments_base_url = mso_current_url();
			if( $options['comments_pagination_next'] && preg_match( '!\/('.$options['comments_pagination_next'].')\/(.+?)($|\/)!is', $comments_base_url ) )
			{	
				$comments_base_url = preg_replace('!\/('.$options['comments_pagination_next'].')\/(.+?)($|\/)!is', '', $comments_base_url);
			}
				
			# Куда отправлять AJAX-запросы
			$comments_ajax_path = getinfo('ajax').base64_encode('plugins/'.basename(dirname(__FILE__)).'/do-ajax.php');
				
			# формируем «счётчик комментариев»
			$comments_count = comments_count( $page['page_id'] ); # вывод количества коментариев может потребовать особого подсчёта 
				
			# тип комментариев - simple или complex
			$comments_type = $options['comments_show_type'];
			
			# нужно ли вставлять имя автора исходного комментария при ответе
			$comments_replyto = $options['comments_replyto'] ? 1 : '';
				
			# формируем блок лучших комментариев
			$comments_best = '';
			if( $comments && $page_text_ok ) # есть комментарии и их можно показывать
			{
				if(
					$options['comments_best'] > 0 && 
					( $options['comments_rating_min_show'] == 0 || $comments_count > $options['comments_rating_min_show'] ) && 
					( $options['comments_pagination_best_show'] || ( !$options['comments_pagination_best_show'] && mso_current_paged( $options['comments_pagination_next'] ) < 2 ) ) 
				)
				{
					$cache_key_best = 'page-comments-best-src'.$comments_base_url;
					if( $cache_time > 0 and $cache = mso_get_cache($cache_key_best) ) # пытаемся использовать полный кеш блока комментирования
					{
						$comments_best = unserialize(str_replace('_serialize_', '', $cache));
					}
					else
					{
						# получаем все разрешенные комментарии
						$par = array(
							'best' => true,
							'limit' => $options['comments_best'],
							'pagination' => false,
							'out_type' => 'simple',
						);
						$comments_best = comments_get_comments($page['page_id'], $par, $best_pag); #pr($comments_best);
						mso_add_cache($cache_key_best, '_serialize_'.serialize( $comments_best ), $cache_time * 60); 
					}
						
					if( $comments_best )
					{
						$cache_key_best = 'page-comments-best-html'.$comments_base_url.$cache_person;
						if( $cache_time > 0 and $cache = mso_get_cache($cache_key_best) ) # пытаемся использовать полный кеш блока комментирования
						{
							$comments_best = $cache;
						}
						else
						{
							ob_start();
								
							$best_caption = isset($options['comments_best_caption']) && $options['comments_best_caption'] ? $options['comments_best_caption'] : "Лучшие комментарии";
							
							eval(comments_tmpl_ts('page-comments-best.php')); # заголовок блока
							
							comments_show_simple( $page, $comments_best, 0, true );
								
							$comments_best = ob_get_contents(); ob_end_clean();
							
							mso_add_cache($cache_key_best, $comments_best, $cache_time * 60); 
						}
					}
					else
					{
						$comments_best = '';
					}
				}
			}
				
			# основной вывод комментариев
			$comments_list = '';
			if( $comments && $page_text_ok ) # есть комментарии и их можно показывать
			{
				$cache_key_list = 'page-comments-list'.$cache_uri.$cache_person;
				if( $cache_time > 0 and $cache = mso_get_cache($cache_key_list) ) # пытаемся использовать полный кеш вывода списка комментариев
				{
					$comments_list = $cache;
				}
				else
				{
					ob_start();
						
					if( $options['comments_show_type'] == 'simple' ) # простой вывод коментариев
					{
						comments_show_simple( $page, $comments );
					}
					else # древовидный вывод комментариев
					{
						#pr($comments);
						comments_show_tree( $page, $comments );
					}
						
					$comments_list = ob_get_contents(); ob_end_clean();
						
					mso_add_cache($cache_key_list, $comments_list, $cache_time * 60);
				}
			}
				
			# вывод пагинации комментариев
			$comments_pagination = '';
			if( $comments && $page_text_ok ) # есть комментарии и их можно показывать
			{
				$cache_key_pagination = 'page-comments-pagination'.$cache_uri;
				if( $cache_time > 0 and $cache = mso_get_cache($cache_key_pagination) ) # пытаемся использовать полный кеш вывода списка комментариев
				{
					$comments_pagination = $cache;
				}
				else
				{
					if( $pagination )
					{
						ob_start();
							
						mso_hook('comments_pagination', $pagination);
							
						$comments_pagination = ob_get_contents(); ob_end_clean();
							
						mso_add_cache($cache_key_pagination, $comments_pagination, $cache_time * 60);
					}
				}
			}
		}
			
		# если можно оставлять комментарии, то готовим форму
		$comments_form = '';
		if( $page['page_comment_allow'] && $page_text_ok )
		{
			ob_start();
							
			# если запрещены комментарии и от анонимов и от комюзеров, то выходим
			if( mso_get_option('allow_comment_anonim', 'general', '1') || mso_get_option('allow_comment_comusers', 'general', '1') )
			{
				$to_login = tf('Вы можете <a href="#LOG#">войти</a> под своим логином или <a href="#REG#">зарегистрироваться</a> на сайте.');
				$to_login = str_replace('#LOG#', getinfo('site_url') . 'login', $to_login);
				$to_login = str_replace('#REG#', getinfo('site_url') . 'registration', $to_login);
			
				if( mso_get_option('new_comment_anonim_moderate', 'general', '1') )
				{
					$to_moderate = mso_get_option('form_comment_anonim_moderate', 'general', tf('Комментарий будет опубликован после проверки'));
				}
				else
				{
					$to_moderate = mso_get_option('form_comment_anonim', 'general', tf('Используйте нормальные имена'));
				}
			
				$av = $MSO->data['session'];
				if( !isset($av['users_avatar_url']) ) $av['users_avatar_url'] = '';
				if( !isset($av['comusers_avatar_url']) ) $av['comusers_avatar_url'] = '';
				if( !isset($av['users_email']) ) $av['users_email'] = '';
				if( !isset($av['comusers_email']) ) $av['comusers_email'] = '';
				if( !isset($av['comments_author_name']) ) $av['comments_author_name'] = '';
				$avatar = mso_avatar($av, '', false,  false, true); # только адрес граватарки
			
				# если запрещены комментарии от анонимов и при этом нет залогиненности, 
				# то форму при простой форме не выводим
				if( !mso_get_option('allow_comment_anonim', 'general', '1') && !is_login() && !is_login_comuser() && mso_get_option('form_comment_easy', 'general', '0') ) 
				{
					if( mso_get_option('allow_comment_comusers', 'general', '1') )
					{
						eval(comments_tmpl_ts('page-comment-to-login-tmpl.php'));
					}
				}
				else
				{
					eval(comments_tmpl_ts('page-comment-form-tmpl.php')); 
				}
			}
				
			$comments_form = ob_get_contents(); ob_end_clean();
		}

		ob_start();

		eval(comments_tmpl_ts('page-comments-general-tmpl.php'));

		$html = ob_get_contents(); ob_end_clean();
			
		mso_add_cache($cache_key, $html, $cache_time * 60); # сохраняем полный кеш на диск
	}
		
	return $html;
}

# расширенная функция получения каментов
# за основу взята стандартная функция mso_get_comments © MAX
function comments_get_comments( $page_id = 0, $r = array(), &$pag )
{
	global $MSO;
	$offset = 0;
		
	$r = mso_hook('mso_get_comments_args', $r);
		
	if ( !isset($r['limit']) )	$r['limit'] = false;
	if ( !isset($r['best']) )	$r['best'] = false;
	if ( !isset($r['order']) )	$r['order'] = 'asc';
	if ( !isset($r['order_childs']) )	$r['order_childs'] = 'asc';
	if ( !isset($r['tags']) )	$r['tags'] = '<p><img><strong><em><i><b><u><s><pre><code><blockquote>';
	if ( !isset($r['tags_users']) )	$r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><pre><code><blockquote>';
	if ( !isset($r['tags_comusers']) )	$r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><pre><code><blockquote>';
	if ( !isset($r['anonim_comments']) )	$r['anonim_comments'] = array();
	if ( !isset($r['anonim_title']) )	$r['anonim_title'] = '';// ' ('. t('анонимно'). ')'; // дописка к имени для анонимов
	if ( !isset($r['anonim_no_name']) )	$r['anonim_no_name'] = tf('Аноним');// Если не указано имя анонима
	if ( !isset($r['out_type']) )		$r['out_type'] = 'simple'; // тип вывода комментариев
	#if ( !isset($r['parent_id']) )		$r['parent_id'] = 0; // id родительского комментария
	if ( !isset($r['max_tree_level']) )	$r['max_tree_level'] = 0; // максимальный уровень погружения в дерево комментариев. Всё, что "глубже" - добавляется на максимальный уровень
	if ( !isset($r['limit_child']) )	$r['limit_child'] = false; // ограничивать или нет количество потомков в дереве
	if ( !isset($r['level']) )	$r['level'] = 1;
		
	if ( !isset($r['current']) )	$r['current'] = 1; // текущая порция для древовидных каментов
	if ( !isset($r['pagination']) )		$r['pagination'] = false; // использовать пагинацию
	if ( !isset($r['pagination_next_url']) )	$r['pagination_next_url'] = 'comments-next'; // сегмент, признак пагинации

	if ( $r['pagination'] && !$r['limit'] )
	{
		$r['limit'] = 7; // сколько отдавать комментариев
	}
	// проверим входящий лимит - он должен быть числом
	$r['limit'] = (int) $r['limit'];
	$r['limit'] = abs( $r['limit'] );
	if (!$r['limit']) $r['limit'] = 7; // что-то не то, заменяем на дефолт = 7
		
	// если аноним указывает имя с @, то это страница в твиттере - делаем ссылку
	if ( !isset($r['anonim_twitter']) )	$r['anonim_twitter'] = true; 

	// дописка к имени для комментаторов без ника
	if ( !isset($r['add_author_name']) )	$r['add_author_name'] = tf('Комментатор');

	$CI = & get_instance();
	
	# расчёты для пагинации
	if( $r['pagination'] || ( isset($r['parent_id']) && $r['parent_id'] != 0 ) )
	{
		$r['parent_id'] = isset($r['parent_id']) ? $r['parent_id'] : 0;
		
		# основной запрос для вывода каментов
		$CI->db->select('SQL_BUFFER_RESULT '.$CI->db->dbprefix('page').'.`page_id`, '.$CI->db->dbprefix('page').'.`page_slug`, '.$CI->db->dbprefix('page').'.`page_title`, comments.*', false);

		if ($page_id) $CI->db->where('page.page_id', $page_id);
	
		// если нет анонимого коммента, то вводим условие на comments_approved=1 - только разрешенные
		if (!$r['anonim_comments'])
		{
			$CI->db->where('comments.comments_approved', '1');
		}
		else // есть массив с указанными комментариям - они выводятся отдельно
		{
			$CI->db->where('comments.comments_approved', '0');
			$CI->db->where_in('comments.comments_id', $r['anonim_comments']);
		}

		// вот эти два join жутко валят мускуль...
		// пока решение не найдено, все запросы к комментам следует кэшировать на уровне плагина
		//$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
		//$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
			
		// вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
		$CI->db->where($CI->db->dbprefix . 'page.page_id', $CI->db->dbprefix . 'comments.comments_page_id', false);
	
		$CI->db->where('page.page_status', 'publish');
			
		# если тип вывода древовидный, то пагинацию рассчитываем только по первому уровню
		if( $r['out_type'] == 'tree' )
		{
			$CI->db->where('comments.comments_parent_id', $r['parent_id']);
		}
			
		$CI->db->order_by('comments.comments_date', ( $r['level'] == 1 ? $r['order'] : $r['order_childs'] ) );
			
		$CI->db->from('comments, page');
			
		$qry = $CI->db->get();
			
		
		if( is_object($qry) && $qry->num_rows() > 0 )
		{
			$pag_row = $qry->num_rows();
				
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего страниц пагинации
			$pag['limit'] = $r['limit']; // комментариев на страницу
				
			if( $r['parent_id'] == 0 && $r['current'] == 1 )
			{
				$current_paged = mso_current_paged($r['pagination_next_url']);
			}
			else
			{
				$current_paged = $r['current'];
			}
			
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
				
			if( $r['parent_id'] != 0 && !$r['limit_child'] )
			{
				$offset = 0;
			}
			else
			{
				$offset = $current_paged * $pag['limit'] - $pag['limit'];
			}
				
			if( $r['limit_child'] && $r['level'] > 1 )
			{
				$r['limit'] = $r['limit_child'] + 1; 
			}
				
			$pag['next_url'] = $r['pagination_next_url'];
		}
		else
		{
			$pag = false;
		}
	}
	else
		$pag = false;
	
	
	# основной запрос для вывода каментов
	$CI->db->select('page.page_id, page.page_slug, page.page_title, comments.*,
	users.users_id, 
	users.users_nik,
	users.users_count_comments,
	users.users_url,
	users.users_email,
	users.users_avatar_url,
	
	comusers.comusers_id, 
	comusers.comusers_nik,
	comusers.comusers_count_comments,
	comusers.comusers_allow_publish,
	comusers.comusers_email,
	comusers.comusers_avatar_url,
	comusers.comusers_url
	');

	if ($page_id) $CI->db->where('page.page_id', $page_id);
	
	// если нет анонимого коммента, то вводим условие на comments_approved=1 - только разрешенные
	if (!$r['anonim_comments'])
	{
		$CI->db->where('comments.comments_approved', '1');
	}
	else // есть массив с указанными комментариям - они выводятся отдельно
	{
		$CI->db->where('comments.comments_approved', '0');
		$CI->db->where_in('comments.comments_id', $r['anonim_comments']);
	}
		
	// вот эти два join жутко валят мускуль...
	// пока решение не найдено, все запросы к комментам следует кэшировать на уровне плагина
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
		
	// вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
	$CI->db->where('`'.$CI->db->dbprefix . 'page`.`page_id`', '`'.$CI->db->dbprefix . 'comments`.`comments_page_id`', false);
		
	$CI->db->where('page.page_status', 'publish');
		
	if( (isset($r['parent_id']) && $r['parent_id'] != 0 ) || $r['out_type'] == 'tree' )
	{
		if( !isset($r['parent_id']) ) $r['parent_id'] = 0;
			
		$CI->db->where('comments.comments_parent_id', $r['parent_id']); # выбираем детей указанного камента
	}
		
	if( $r['best'] )
	{
		$CI->db->where('comments.comments_rating >', 0);
		$CI->db->order_by('comments.comments_rating', 'desc' );
	}
	else
	{
		$CI->db->order_by('comments.comments_date', ( $r['level'] == 1 ? $r['order'] : $r['order_childs'] ) );
	}
	
	if( ( ( isset($r['parent_id']) && $r['parent_id'] == 0 ) || $r['limit_child'] || $r['best'] ) && $r['limit'] )
	{
		if( $pag and $offset )
		{
			$CI->db->limit($r['limit'], $offset);
		}
		else
			$CI->db->limit($r['limit']);
	}
	
	$CI->db->from('comments, page');
 		
	$qry = $CI->db->get(); #_sql_(); pr($CI->db->last_query());
		
	if( is_object($qry) && $qry->num_rows() > 0 )
	{
		$comments = $qry->result_array(); # pr($comments);
			
		if( !function_exists('mso_comuser_update_count_comment') ) require_once( getinfo('common_dir').'comments.php' ); # стандартные функции комментариев
			
		# получим список всех комюзеров, где посдчитается количество их комментариев
		$all_comusers = mso_comuser_update_count_comment();
			
		foreach ($comments as $key => $comment)
		{
			#pr($comment);
			if( $r['limit_child'] && ($key + 1) > $r['limit_child'] ) continue;
				
			$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
				
			if ($comment['comusers_id']) // это комюзер
			{
				if ($comment['comusers_nik']) $comment['comments_author_name'] = $comment['comusers_nik'];
				else $comment['comments_author_name'] = $r['add_author_name'] . ' ' . $comment['comusers_id'];
				$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
						. $comment['comments_author_name'] . '</a>';
				
				// есть адрес страницы
				if ($comment['comusers_url'])
				{
					// зачистка XSS
					$comments[$key]['comusers_url'] = mso_xss_clean($comment['comusers_url'], '');
				}
				
				// зачистка XSS комюзер имя
				if ($comment['comusers_nik'])
				{
					$comments[$key]['comusers_nik'] = mso_xss_clean($comment['comusers_nik']);
				}
				
				$commentator = 1;

				if (isset($all_comusers[$comment['comusers_id']]))
					$comments[$key]['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];

			}
			elseif ($comment['users_id']) // это автор
			{
				if ($comment['users_url'])
						$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
					else $comment['comments_url'] = $comment['users_nik'];
				$commentator = 2;
			}
			else // просто аноним
			{
				if (!$comment['comments_author_name']) $comment['comments_author_name'] = $r['anonim_no_name'];
				if ($r['anonim_twitter']) // разрешено проверять это твиттер-логин?
				{
					
					if (strpos($comment['comments_author_name'], '@') === 0) // первый символ @
					{	
						$lt = mso_slug( substr($comment['comments_author_name'], 1) ); // вычленим @
						
						$lt = mso_xss_clean($lt, 'Error', $lt, true); // зачистка XSS
						
						$comment['comments_url'] = '<a href="http://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
					}
					else $comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title']; 
				}
				else
				{
					$comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title']; 
				}
			}

			$comments_content = $comment['comments_content'];
			
			if (mso_hook_present('comments_content_custom'))
			{
				$comments_content = mso_hook('comments_content_custom', $comments_content);
			}
			else
			{
				$comments_content = mso_comments_autotag($comments_content, $commentator, $r);
			}
				
			$comments[$key]['comments_content'] = $comments_content;
			$comments[$key]['comments_url'] = $comment['comments_url'];
				
			if( $r['out_type'] == 'tree' )
			{
				$chpar = $r;
				$chpar['parent_id'] = $comment['comments_id'];
				$chpar['current'] = 1;
				$chpar['level'] = $chpar['level'] + 1;
				$childs = comments_get_comments($page_id, $chpar, $chpag);
					
				if( $childs ) # есть потомки
				{
					if( $r['max_tree_level'] && $r['max_tree_level'] < $chpar['level'] )
					{
						$comments = array_merge($comments, $childs);
					}
					else
					{
						$comments[$key]['childs'] = $childs;
					}
				}
			}
		}
	}
	else
		$comments = array();

	return $comments;
}

# функция контроля вывода содержимого записи при пагинации каментов
function comments_page_content( $text = '' )
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());

	if( !isset($options['comments_pagination_page_content']) ) $options['comments_pagination_page_content'] = true;
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	
	if( is_type('page') && !$options['comments_pagination_page_content'] && strpos($_SERVER['REQUEST_URI'], $options['comments_pagination_next']) !== false )
	{
		$text = ''; # удаляем содержимое записи, если нужно
	}
		
	return $text;
}

# функция для контроля вывода содержимого записи при пагинации каментов
function comments_type_foreach_remap( $target = false )
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());

	if( !isset($options['comments_pagination_page_content']) ) $options['comments_pagination_page_content'] = true;
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	
	if( !$options['comments_pagination_page_content'] && strpos($_SERVER['REQUEST_URI'], $options['comments_pagination_next']) !== false )
	{
		if( $target == 'page-content-page' && file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/page-content-stopper.php') )
		{
			return getinfo('plugins_dir').basename(dirname(__FILE__)).'/page-content-stopper.php';
		}
	}
		
	return false;
}

function comments_pagination($r = array())
{
	global $MSO;
		
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	if( !isset($options['comments_pagination_caption']) || !$options['comments_pagination_caption'] ) $options['comments_pagination_caption'] = t('Показать больше');
	if( !isset($options['comments_pagination_loader']) || !$options['comments_pagination_loader'] ) $options['comments_pagination_loader'] = t('Загружаю...');
	if( !isset($options['comments_ajax_placeholder']) || !$options['comments_ajax_placeholder'] ) $options['comments_ajax_placeholder'] = '.mso-comments section';
		
	$r_orig = $r; #pr($r);
		
	if( $options['comments_pagination_type'] == 'ajax' || $options['comments_pagination_type'] == 'simple-ajax' )
	{
		if( !$r ) return $r;
		if( !isset($r['maxcount']) ) return $r;
		if( !isset($r['limit']) ) return $r; // нужно указать сколько комментариев выводить
		
		if( !isset($r['next_url']) ) $r['next_url'] = $options['comments_pagination_next'];
		
		# текущая пагинация вычисляется по адресу url
		# должно быть /next/6 - номер страницы
		$current_paged = mso_current_paged($r['next_url']);
		if( $current_paged > $r['maxcount'] ) $current_paged = $r['maxcount'];
			
		# текущий адрес
		$cur_url = mso_current_url(true);
			
		# Куда отправлять AJAX-запросы
		$ajax_path = getinfo('ajax').base64_encode('plugins/'.basename(dirname(__FILE__)).'/do-ajax.php');
			
		if( $current_paged < $r['maxcount'] )
		{
			echo 
				'<div class="pagination comments"><nav><div class="button">'.NR.
					'<a data-ajax="'.$ajax_path.'" data-current="'.(++$current_paged).'" data-max="'.$r['maxcount'].'" data-limit="'.$r['limit'].'" data-placeholder="'.$options['comments_ajax_placeholder'].'">'.tf($options['comments_pagination_caption']).'</a>'.NR.
					'<span>'.$options['comments_pagination_loader'].'</span>'.NR.
				'</div></nav></div>';
		}
	}
		
	if( $options['comments_pagination_type'] == 'simple' || $options['comments_pagination_type'] == 'simple-ajax' )
	{
		mso_hook('pagination', $r);
	}
		
	return $r_orig;
}

# функция простого вывода комментариев
function comments_show_simple( $page, $comments, $begin_num = 0, $best = false )
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	
	$rating = isset($options['comments_rating']) ? $options['comments_rating'] : 0;
	$rating_loader = isset($options['comments_rating_loader']) && $options['comments_rating_loader'] ? $options['comments_rating_loader'] : '';
		
	$best_context = $best && isset($options['comments_best_context']) && $options['comments_best_context'] ? true : false;
		
	if( is_login() ) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';
		
	static $comment_num = 0; # номер комментария по порядку - если нужно выводить в type_foreach-файле
		
	$comment_num = $begin_num;
		
	foreach( $comments as $comment )  # выводим в цикле
	{       
		$comment_num++;

		if( $f = mso_page_foreach('page-comments') ) 
		{
			require($f);
			continue; # следующая итерация
		}

		extract($comment);

		if( $comment_num & 1 ) $a_class = 'mso-comment-odd'; # нечетное
		else $a_class = 'mso-comment-even'; # четное

		if( $users_id ) $a_class .= ' mso-comment-users';
		elseif( $comusers_id ) $a_class .= ' mso-comment-comusers';
		else $a_class .= ' mso-comment-anonim';

		if( $best ) $a_class .= ' mso-comment-best';

		$avatar = mso_avatar($comment, '', false,  false, true); # только адрес граватарки

		# $comments_content = mso_comments_content($comments_content);

		if( !$comusers_url or !mso_get_option('allow_comment_comuser_url', 'general', 0) ) $comusers_url = '';
		
		eval(comments_tmpl_ts('page-comments-article-tmpl.php')); # выполнение через шаблонизатор
	}
}

# функция древовидного вывода комментариев
function comments_show_tree( $page, $comments, $begin_num = 0, $current = 1, $best = false )
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	if( !isset($options['comments_pagination_caption']) || !$options['comments_pagination_caption'] ) $options['comments_pagination_caption'] = t('Показать больше');
	if( !isset($options['comments_pagination_loader']) || !$options['comments_pagination_loader'] ) $options['comments_pagination_loader'] = t('Загружаю...');
	if( !isset($options['comments_max_tree_level']) ) $options['comments_max_tree_level'] = 0;
	if( !isset($options['comments_hide_reply']) ) $options['comments_hide_reply'] = 0;
		
	$rating = isset($options['comments_rating']) ? $options['comments_rating'] : 0;
	$rating_loader = isset($options['comments_rating_loader']) && $options['comments_rating_loader'] ? $options['comments_rating_loader'] : '';
		
	$best_context = $best && isset($options['comments_best_context']) && $options['comments_best_context'] ? true : false;

	if( is_login() ) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';
		
	static $comment_num = 0; # номер комментария по порядку - если нужно выводить в type_foreach-файле

	static $level; # текущий уровень
	if( !isset($level) ) { $level = 1; } else { $level++; }

	$comment_num = $begin_num > 0 ? $begin_num : 0;
	$i = 1;
	$more = false;
	
	if( $current === 1 ) echo '<ul class="comments">'.NR;
		
	foreach( $comments as $k => $comment )  # выводим в цикле
	{       
		if( $k !== 'childs' && $options['comments_child_limit'] && $k >= $options['comments_child_limit'] && $comment['comments_parent_id'] ) { $more = true; break; }
			
		$comment_num++;
			
		echo '<li>'.NR;
			
		if( $k !== 'childs' )
		{
			if( $f = mso_page_foreach('page-comments') ) 
			{
				require($f);
				continue; # следующая итерация
			}
			#pr($comment);
			extract($comment);

			if( $comment_num & 1 ) $a_class = 'mso-comment-odd'; # нечетное
			else $a_class = 'mso-comment-even'; # четное

			if( $users_id ) $a_class .= ' mso-comment-users';
			elseif( $comusers_id ) $a_class .= ' mso-comment-comusers';
			else $a_class .= ' mso-comment-anonim';

			$avatar = mso_avatar($comment, '', false,  false, true); # только адрес граватарки

			// $comments_content = mso_comments_content($comments_content);

			if( !$comusers_url or !mso_get_option('allow_comment_comuser_url', 'general', 0) ) $comusers_url = '';
			
			# нужно ли скрывать кнопку ответить на последнем уровне вложенности комментариев
			$comments_hide_reply = $options['comments_hide_reply'] && $level >= $options['comments_max_tree_level'] ? true : false;
			
			eval(comments_tmpl_ts('page-comments-article-tree-tmpl.php')); # выполнение через шаблонизатор
				
			if( isset($childs) )
			{
				$comment_num = comments_show_tree( $page, $childs, ( $begin_num < 0 ? -1 : $comment_num ) );
				unset($childs);
				unset($users_id);
				unset($comusers_id);
				unset($avatar);
				unset($a_class);
			}
				
			$i++;
		}
		else
		{
			$comment_num = comments_show_tree($page, $comment, ( $begin_num < 0 ? -1 : $comment_num ) );
		}
			
		echo '</li>'.NR;
	}
		
	if( $current === 1 )
	{
		echo '</ul>'.NR;
		if( $level > 1 ) $level--; # уменьшаем уровень вложенности дерева
	}
		
	if( $more && $current === 1 )
	{
		echo '<div class="mso-comment-more"><button type="button" class="more" data-current="1" data-limit="'.$options['comments_child_limit'].'" data-parent="'.$comment['comments_parent_id'].'">'.tf($options['comments_pagination_caption']).'</button><span>'.$options['comments_pagination_loader'].'</span></div>'.NR;
	}
		
	return $comment_num;
}

# получение одного комментария по его id
function comments_get_comment( $id = 0 )
{
	$comment = array();
		
	if( !$id ) return $comment;
		
	$cache_time = (int) mso_get_option('home_cache_time', 'templates', 0); # время жизни кэша
	$cache_key = 'page-comment-'.$id;
	if( $cache_time > 0 and $cache = mso_get_cache($cache_key) ) # пытаемся использовать кеш комментария
	{
		$comment = unserialize(str_replace('_serialize_', '', $cache));
	}
	else
	{
		# получим список всех комюзеров, где посдчитается количество их комментариев
		static $all_comusers;
		if( !$all_comusers )
		{
			$all_comusers = mso_comuser_update_count_comment();
		}
			
		$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
		if( !isset($options['comments_anonim_twitter']) ) $options['comments_anonim_twitter'] = true;
			
		$CI = & get_instance();
			
		# получение данных комментария
		$CI->db->select('page.page_id, page.page_slug, page.page_title, comments.*,
		users.users_id, 
		users.users_nik,
		users.users_count_comments,
		users.users_url,
		users.users_email,
		users.users_avatar_url,
		
		comusers.comusers_id, 
		comusers.comusers_nik,
		comusers.comusers_count_comments,
		comusers.comusers_allow_publish,
		comusers.comusers_email,
		comusers.comusers_avatar_url,
		comusers.comusers_url
		');

		$CI->db->from('comments');
		$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
		$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
		$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
		$CI->db->where('comments_id', $id);

		$qry = $CI->db->get();

		# если есть данные, то выводим
		if( is_object($qry) && $qry->num_rows() > 0 )
		{
			$comment = $qry->row_array(); 
				
			$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
				
			if ($comment['comusers_id']) // это комюзер
			{
				if ($comment['comusers_nik']) $comment['comments_author_name'] = $comment['comusers_nik'];
				else $comment['comments_author_name'] = $r['add_author_name'] . ' ' . $comment['comusers_id'];
				$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
						. $comment['comments_author_name'] . '</a>';
				
				// есть адрес страницы
				if ($comment['comusers_url'])
				{
					// зачистка XSS
					$comment['comusers_url'] = mso_xss_clean($comment['comusers_url'], '');
				}
				
				// зачистка XSS комюзер имя
				if ($comment['comusers_nik'])
				{
					$comment['comusers_nik'] = mso_xss_clean($comment['comusers_nik']);
				}
				
				$commentator = 1;

				if (isset($all_comusers[$comment['comusers_id']]))
					$comment['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];

			}
			elseif ($comment['users_id']) // это автор
			{
				if ($comment['users_url'])
						$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
					else $comment['comments_url'] = $comment['users_nik'];
				$commentator = 2;
			}
			else // просто аноним
			{
				if (!$comment['comments_author_name']) $comment['comments_author_name'] = tf('Аноним');
				if ($options['comments_anonim_twitter']) // разрешено проверять это твиттер-логин?
				{
					if (strpos($comment['comments_author_name'], '@') === 0) // первый символ @
					{	
						$lt = mso_slug( substr($comment['comments_author_name'], 1) ); // вычленим @
						
						$lt = mso_xss_clean($lt, 'Error', $lt, true); // зачистка XSS
						
						$comment['comments_url'] = '<a href="http://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
					}
					else $comment['comments_url'] = $comment['comments_author_name']; 
				}
				else
				{
					$comment['comments_url'] = $comment['comments_author_name']; 
				}
			}
				
			$comments_content = $comment['comments_content'];
				
			if( mso_hook_present('comments_content_custom') )
			{
				$comments_content = mso_hook('comments_content_custom', $comments_content);
			}
			else
			{
				$comments_content = mso_comments_autotag($comments_content, $commentator);
			}
			
			$comments_content = mso_hook('comments_content_out', $comments_content);
			
			$comment['comments_content'] = $comments_content;
			$comment['comments_url'] = $comment['comments_url'];
				
			mso_add_cache($cache_key, '_serialize_'.serialize( $comment ), $cache_time * 60); 
		}
	}
		
	return $comment;
}

# обработка хука при сохранении комментария через ajax
function comments_new_comment( $data = array() )
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	if( !isset($options['comments_max_tree_level']) ) $options['comments_max_tree_level'] = 0;
	if( !isset($options['comments_show_type']) ) $options['comments_show_type'] = 'simple';
	if( !isset($options['comments_order']) ) $options['comments_order'] = 'asc';
		
	$rating = isset($options['comments_rating']) ? $options['comments_rating'] : 0;
	$rating_loader = isset($options['comments_rating_loader']) && $options['comments_rating_loader'] ? $options['comments_rating_loader'] : '';
		
	$best = false; # это не вывод лучшего камента
	$best_context = $best && isset($options['comments_best_context']) && $options['comments_best_context'] ? true : false;

	$comment = comments_get_comment( $data['id'] ); # получаем комментарий
	#pr($comment);
		
	if( is_login() ) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';
	
	# скрываем кнопку ответить
	$comments_hide_reply = true;
		
	ob_start();
		
	if( $f = mso_page_foreach('page-comments') ) 
	{
		require($f);
	}
	else
	{
		extract($comment);
			
		$a_class = 'mso-comment-even';

		if( $users_id ) $a_class .= ' mso-comment-users';
		elseif( $comusers_id ) $a_class .= ' mso-comment-comusers';
		else $a_class .= ' mso-comment-anonim';

		$avatar = mso_avatar($comment, '', false,  false, true); # только адрес граватарки

		if( !$comusers_url or !mso_get_option('allow_comment_comuser_url', 'general', 0) ) $comusers_url = '';
		
		# выполнение через шаблонизатор
		if( $options['comments_show_type'] == 'simple' )
		{
			eval(comments_tmpl_ts('page-comments-article-tmpl.php'));
		}
		else
		{
			eval(comments_tmpl_ts('page-comments-article-tree-tmpl.php'));  
		}
	}
			
	$out = ob_get_contents(); ob_end_clean();
		
	if( $options['comments_show_type'] != 'simple' ) $out = '<li>'.NR.$out.'</li>'.NR;
		
	mso_flush_cache();

	# 
	# фрагмент из стандартной функции mso_email_message_new_comment
	# 
		
	# рассылаем комментарий всем, кто на него подписан
	mso_email_message_new_comment_subscribe($data);
	
	# После рассылки смотрим, какие уведомления мы хотим получать.
	$level = mso_get_option('email_comments_level', 'general', 1);
	$return = false; //А это потому, что пых не понимает return false; внутри кейсов.
	switch ($level)
	{
		case 6 : $return = true; break;                                    // Ни от кого.
		case 5 : if ( $data['comments_approved'] ) $return = true; break;  // Требующий модерации
		case 4 : if ( (array_key_exists('comments_users_id', $data) or array_key_exists('comments_comusers_id', $data)) ) $return = true; break;
		case 3 : if ( !array_key_exists('comments_comusers_id', $data) ) $return = true; break; // От комментаторов
		case 2 : if ( array_key_exists('comments_users_id', $data) ) $return = true; break;     // От всех кроме юзеров
		case 1 : break;                                                                         // От всех
	}
		
	if( !mso_get_option('subscribe_message_my_comment', 'general', true) && isset($data['comments_users_id']) && $data['comments_users_id'] == getinfo('users_id') ) $return = true; # не посылаем уведомление о своём комментарии если нет специальной опции
		
	if( !$return )
	{
		$id = $data['id'];
		$page_title = $data['page_title'];
		
		$email = mso_get_option('comments_email', 'general', false); // email куда приходят уведомления
		if (!$email) $email = mso_get_option('admin_email', 'general', false); // если не задан, отдельный email, то берём email администратора.
		
		if( $email )
		{
			$CI = & get_instance();
				
			if (!$data['comments_approved']) // нужно промодерировать
				$subject = '[' . getinfo('name_site') . '] ' . '(-) '. tf('Новый комментарий'). ' (' . $id . ') "' . $page_title . '"';
			else
				$subject = '[' . getinfo('name_site') . '] ' . tf('Новый комментарий'). ' (' . $id . ') "' . $page_title . '"';
				
			// шаблон уведомления
			$def_option = 'Новый комментарий на "{{ $page_title }}"
{{ $comment_url }} 

{% if (!$comments_approved) : %}
Комментарий требует модерации: {{ $edit_link }} 

{% endif %}
Автор IP: {{ $comment_ip }} 
Referer: {{ $comment_referer }} 
Дата: {{ $comment_date }} 

{% if ($user) : %}
Пользователь: {{ $user_id }} 
{% endif %}
{% if ($comuser) : %}
Комюзер: id={{ $comuser_id }}, ник: {{ $comuser_nik }}, email: {{ $comuser_email }} 
Профиль: {{ $comuser_url }} 
{% endif %}
{% if ($anonim) : %}
Аноним: {{ $anonim }} 
{% endif %}

Текст:
{{ $comment_content }} 

Администрировать комментарий вы можете по ссылке:
{{ $edit_link }}
';

			$template = mso_get_option('template_email_message_new_comment', 'general', $def_option);
				
			$comment_url = mso_get_permalink_page($data['comments_page_id'])  . '#comment-' . $id;
				
			$comments_approved = $data['comments_approved'];
				
			$comment_ip = $data['comments_author_ip'];	
				
			$comment_referer = $_SERVER['HTTP_REFERER'];
				
			$comment_date = $data['comments_date'];
				
			$user = $comuser = $anonim = false;
				
			if( isset($data['comments_users_id']) )
			{
				$user = true;
				$user_id = $data['comments_users_id'];
			}

			if( isset($data['comments_comusers_id']) )
			{
				$comuser = true;
				$comuser_id = $data['comments_comusers_id'];
				
				$CI->db->select('comusers_nik, comusers_email');
				$CI->db->from('comusers');
				$CI->db->where('comusers_id', $data['comments_comusers_id']);

				$query = $CI->db->get();

				if( is_object($query) && $query->num_rows() > 0 )
				{
					$comusers = $query->row();
					
					$comuser_nik = $comusers->comusers_nik;
					$comuser_email = $comusers->comusers_email;
					$comuser_url = getinfo('siteurl') . 'users/' . $data['comments_comusers_id'];
				}
			}

			if( isset($data['comments_author_name']) )
			{
				$anonim = $data['comments_author_name'];
			}
				
			$comment_content = $data['comments_content'];
				
			$edit_link = getinfo('site_admin_url') . 'comments/edit/' . $id;
				
			$template = mso_tmpl_prepare($template, false);
			
			ob_start();
			eval( $template );
			$text = ob_get_contents(); ob_end_clean();
				
			$data = array_merge($data, array('comment' => true));     # Чтобы плагин smtp_mail точно знал, что ему подсунули коммент, а не вычислял это по subject
			$res = mso_mail($email, $subject, $text, false, $data);   # А зная о комментарии, он сможет сотворить некоторые бонусы.
			#pr($res);
		}
	}
		
	# нужно остановить редирект в функции mso_get_new_comment	
	die( json_encode( array(
		'res' => $out,
		'max_tree_level' => $options['comments_max_tree_level'],
		'order' => $options['comments_order'],
		'err' => false,
	)));
}

# функция подсчёта количества комментариев
function comments_count( $page_id = 0 )
{
	global $MSO;
	
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	if( !isset($options['comments_order']) ) $options['comments_order'] = 'asc';

	$CI = & get_instance();
		
	# организуем кеширование списка id комментариев
	if( !isset($MSO->data[basename(dirname(__FILE__))]['context'][$page_id]) )
	{
		# используем кэширование
		$cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
		$cache_key = basename(dirname(__FILE__)).'-comment-context-'.$page_id;

		if( $cache_time > 0 and $cache = mso_get_cache($cache_key) )
		{
			$comments = unserialize(str_replace('_serialize_', '', $cache));
		}
		else
		{
			# получаем из базы данных
			$CI->db->select('comments_id, comments_parent_id');
			$CI->db->from('comments');
			$CI->db->where('comments_approved', '1');
			$CI->db->where('comments_page_id', $page_id);
			$CI->db->order_by('comments.comments_date', $options['comments_order']);
				
			$q = $CI->db->get(); #_pr($CI->db->last_query());
				
			if( is_object($q) && $q->num_rows() > 0 )
			{
				$comments = $q->result_array(); #pr($r);
			}
			else
			{
				$comments = array();
			}
				
			# сохраняем в кэш информацию о комментариях
			mso_add_cache($cache_key, '_serialize_'.serialize( $comments ), $cache_time * 60);
		}
			
		$MSO->data[basename(dirname(__FILE__))]['context'][$page_id] = $comments;
	}
	else
	{
		$comments = $MSO->data[basename(dirname(__FILE__))]['context'][$page_id];
	}
	
	return count($comments);
}

# функция вычисления url-адреса комментария
function comments_comment_link( $comment = array() )
{
	global $MSO;
	static $urls = array(); # кеширование в рамках генерации одной страницы
	
	if( isset( $urls[ $comment['comments_id'] ] ) ) return $urls[ $comment['comments_id'] ];
	
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());
	if( !isset($options['comments_order']) ) $options['comments_order'] = 'asc';
	if( !isset($options['comments_show_type']) ) $options['comments_show_type'] = 'simple';
	if( !isset($options['comments_page_limit']) ) $options['comments_page_limit'] = 0;
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	if( !isset($options['comments_pagination_type']) ) $options['comments_pagination_type'] = 'none';

	$res = '';
	$CI = & get_instance();
	$pagination = $options['comments_pagination_type'] != 'none' ? true : false;
		
	# организуем кеширование списка id комментариев
	if( !isset($MSO->data[basename(dirname(__FILE__))]['context'][$comment['page_id']]) )
	{
		# используем кэширование
		$cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
		$cache_key = basename(dirname(__FILE__)).'-comment-context-'.$comment['page_id'];

		if( $cache_time > 0 and $cache = mso_get_cache($cache_key) )
		{
			$context = unserialize(str_replace('_serialize_', '', $cache));
		}
		else
		{
			# получаем из базы данных
			$CI->db->select('comments_id, comments_parent_id');
			$CI->db->from('comments');
			$CI->db->where('comments_approved', '1');
			$CI->db->where('comments_page_id', $comment['page_id']);
			$CI->db->order_by('comments.comments_date', $options['comments_order']);
				
			$q = $CI->db->get(); #_pr($CI->db->last_query());
				
			if( is_object($q) && $q->num_rows() > 0 )
			{
				$context = $q->result_array(); #pr($r);
			}
			else
			{
				$context = array();
			}
			#_pr($context);	
			# сохраняем в кэш информацию о комментариях
			mso_add_cache($cache_key, '_serialize_'.serialize( $context ), $cache_time * 60);
		}
			
		$MSO->data[basename(dirname(__FILE__))]['context'][$comment['page_id']] = $context;
	}
	else
	{
		$context = $MSO->data[basename(dirname(__FILE__))]['context'][$comment['page_id']];
	}
		
	$res = '#comment-'.$comment['comments_id']; 
		
	if( $options['comments_show_type'] == 'tree' )
	{
		if( $pagination && $options['comments_page_limit'] ) # если есть пагинация, то к адресу нужно найти её "приставку"
		{
			# нужно найти предка, который участвует в пагинации (т.е. у него парент id = 0)
			$found = false;
			$i = 0;
			$parent_id = $comment['comments_parent_id'];
			
			while( !$found && isset($context[$parent_id]) ) #  && $parent_id != 0
			{
				foreach( $context as $kament )
				{
					if( $parent_id == 0 && !$found && $kament['comments_id'] == $comment['comments_id'] )
					{
						$found = $kament;
						break;
					}
					if( $kament['comments_id'] == $parent_id )
					{
						$parent_id = $kament['comments_parent_id'];
							
						if( $kament['comments_parent_id'] == 0 )
						{
							$found = $kament;
						}
						break;
					}
				}
			}
			#pr($found);
			if( $found )
			{
				# нужно выбрать все комментарии с нулевым предком чтобы определить позицию нашего камента
				$position = 0; $i = 0;
				foreach( $context as $kament )
				{
					if( $kament['comments_parent_id'] == 0 )
					{
						if( $found['comments_id'] == $kament['comments_id'] )
						{
							$position = $i;
							break;
						}
							
						$i++;
					}
				}
				
				# нужно посчитать на какой странице оказался нужный нам предок
				$pg = ceil(($position + 1) / $options['comments_page_limit']);
					
				if( intval($pg) > 1 )
				{
					$res = '/'.$options['comments_pagination_next'].'/'.intval($pg).$res;
				}
			}
		}
	}
	else
	{
		if( $pagination && $options['comments_page_limit'] ) # если есть пагинация, то к адресу нужно найти её "приставку"
		{
			foreach( $context as $num => $kament )
			{
				if( $kament['comments_id'] == $comment['comments_id'] )
				{
					$pg = ceil(($num + 1) / $options['comments_page_limit']);
						
					if( intval($pg) > 1 )
					{
						$res = '/'.$options['comments_pagination_next'].'/'.intval($pg).$res;
					}
						
					break;
				}
			}
		}
	}
		
	$urls[ $comment['comments_id'] ] = '/page/'.$comment['page_slug'].$res;
		
	return $urls[ $comment['comments_id'] ];
}

# поиск файла HTML-шаблона в units-папках шаблона, плагина, shared
# использование: eval(comments_tmpl_ts('article-tmpl.php'));
function comments_tmpl_ts($fn, $replace = true)
{
	$fn1 = getinfo('template_dir') . 'type/page/units/' . $fn; # путь в шаблоне
	$fn2 = getinfo('plugins_dir') . basename(dirname(__FILE__)) . '/units/' . $fn; # путь в папке плагина
	$fn3 = getinfo('shared_dir') . 'type/page/units/' . $fn; # путь в shared
	
	if( file_exists($fn1) ) return mso_tmpl($fn1, $replace); # если есть в шаблоне
	elseif( file_exists($fn2) ) return mso_tmpl($fn2, $replace); # если есть в папке плагина
	elseif( file_exists($fn3) ) return mso_tmpl($fn3, $replace); # если есть в папке shared
	else return '?>';
}

# функция для вывода пагинации каментов в sitemap.xml
function comments_xml_sitemap()
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());

	if( !isset($options['comments_pagination_type']) ) $options['comments_pagination_type'] = 'none';
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	if( !isset($options['comments_page_limit']) ) $options['comments_page_limit'] = 0;
	if( !isset($options['comments_child_limit']) ) $options['comments_child_limit'] = 0;
	if( !isset($options['comments_show_type']) ) $options['comments_show_type'] = 'simple';
	if( !isset($options['comments_changefreq']) ) $options['comments_changefreq'] = 'daily';
	if( !isset($options['comments_priority']) ) $options['comments_priority'] = '0.5';
		
	$out = '';
		
	if( $options['comments_pagination_type'] != 'none' && $options['comments_page_limit'] != 0 )
	{
		# используем кэширование
		$cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
		$cache_key = basename(dirname(__FILE__)).'-xml-sitemap';

		if( $cache_time > 0 and $cache = mso_get_cache($cache_key) )
		{
			$out = $cache;
		}
		else
		{
			# временная зона сайта в формат +03:00 из 3.00
			# взято из \application\maxsite\plugins\xml_sitemap\index.php версии 1.6
			$time_zone = getinfo('time_zone'); // 3.00 -11.00;
			$znak = ( (int) $time_zone >= 0) ? '+' : '-';
			$time_zone = abs($time_zone);
			if ($time_zone == 0) $time_zone = '0.0';
			$time_zone = trim( str_replace('.', ' ', $time_zone) );
			$time_z = explode(' ', $time_zone);
			if (!isset($time_z[0])) $time_z[0] = '0';
			if (!isset($time_z[1])) $time_z[1] = '0';
			if ($time_z[0] < 10) $time_z[0] = '0' . $time_z[0];
			if ($time_z[1] < 10) $time_z[1] = '0' . $time_z[1];
			$time_zone = $znak . $time_z[0] . ':' . $time_z[1];

			$CI = & get_instance();
				
			# получаем из базы данных
			$CI->db->select('count(*) cnt, cm.comments_date, pg.page_id, pg.page_slug');
			$CI->db->from('comments cm');
			$CI->db->join('mso_page pg', 'cm.comments_page_id = pg.page_id', 'left');
			$CI->db->where('cm.comments_approved', '1');
			if( $options['comments_show_type'] == 'tree' && $options['comments_child_limit'] != 1 )
			{
				$CI->db->where('cm.comments_parent_id', 0);
			}
			$CI->db->group_by('cm.comments_page_id');
			$CI->db->having('cnt >', $options['comments_page_limit']);
			
			$CI->db->order_by('cm.comments_date', 'desc');
				
			$q = $CI->db->get(); #_pr($CI->db->last_query());
				
			if( is_object($q) && $q->num_rows() > 0 )
			{
				$res = $q->result_array();
				/*
				[0] => Array
				        (
				            [cnt] => 7878
				            [page_id] => 3
				            [page_slug] => proverka
				        )
				*/
			}
			else
			{
				$res = array();
			}
			#pr($res);
			foreach( $res as $r )
			{
				$base_url = getinfo('site_url').'page/'.$r['page_slug'];					
				$base_url = mso_hook('furl', $base_url); #pr($base_url);

				$date = str_replace(' ', 'T', $r['comments_date']).$time_zone;
				
				$pgs = round( $r['cnt'] / $options['comments_page_limit'] ); #pr($pgs);
				for( $i = 2; $i <= $pgs; $i++ )
				{
					$out .= '<url>'.NR;
					$out .= '<loc>'. $base_url.'/'.$options['comments_pagination_next'].'/'.$i.'</loc>'.NR;
					$out .= '<lastmod>'.$date.'</lastmod>'.NR;
					$out .= '<changefreq>'.$options['comments_changefreq'].'</changefreq>'.NR;
					$out .= '<priority>'.$options['comments_priority'].'</priority>'.NR;
					$out .= '</url>'.NR;
				}
			}
			
			# сохраняем в кэш информацию о комментариях
			mso_add_cache($cache_key, $out, $cache_time * 60);
		}
	}
		
	return $out;
}

# функция начала кеширования вывода для подмены ссылок в админ-панели управления комментариями
function comments_admin_content_do( $args )
{
	if( mso_segment(2) == 'comments' )
	{
		ob_start();
	}
	
	return $args;
}

# функция подмены ссылок в админ-панели управления комментариями
function comments_admin_content( $content )
{
	if( mso_segment(2) == 'comments' )
	{
		$content = ob_get_contents(); ob_end_clean();
		$content = preg_replace_callback('!\/page\/(.*?)\#comment\-(.+?)\"!mis', 'comments_replace_link', $content); #\#comment\-(.*?)
		#_pr($content);
	}
	
	echo $content;
}

# функция замены стандартных ссылок на комментарий на новые с учётом пагинации
function comments_replace_link( $matches )
{
/*
	pr($matches);
	(
	    [0] => /page/proverka-kbm#comment-9707"
	    [1] => proverka-kbm
	    [2] => 9707
	)
*/
	$CI = & get_instance();
		
	# получаем из базы данных
	$CI->db->select('comments_page_id, comments_parent_id'); #
	$CI->db->from('comments');
	$CI->db->where('comments_id', $matches[2]);
	$CI->db->where('comments_approved', '1');
		
	$q = $CI->db->get(); #pr($CI->db->last_query());
		
	if( is_object($q) && $q->num_rows() > 0 )
	{
		$r = $q->result_array(); $r = $r[0]; #pr($r);
			
		$comment = array(
			'comments_id' => $matches[2],
			'comments_parent_id' => $r['comments_parent_id'],
			'page_id' => $r['comments_page_id'],
			'page_slug' => $matches[1],
		);
		#pr($comment);
		return comments_comment_link( $comment ).'"';
	}
		
	return $matches[0]; # если новый адрес найти не удалось, то возвращаем исходный адрес
}

# реакция на хук head_start - добавление в конец мета-полей title и description информации о текущей странице пагинации
function comments_head_start()
{
	$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array());

	if( !isset($options['comments_pagination_head_start']) ) $options['comments_pagination_head_start'] = true;
	if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
	
	if( is_type('page') && $options['comments_pagination_head_start'] && strpos($_SERVER['REQUEST_URI'], $options['comments_pagination_next']) !== false )
	{
		preg_match( '!\/('.$options['comments_pagination_next'].')\/(.+?)($|\/)!is', $_SERVER['REQUEST_URI'], $match );
		$comments_pagination_current = $match[2];
		eval(comments_tmpl_ts('page-comments-head-start.php'));
	}
		
	return '';
}

# end file