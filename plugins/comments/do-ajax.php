<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Comments» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков
 */

# читаем опции плагина
$options = mso_get_option('plugin_'.basename(dirname(__FILE__)), 'plugins', array() ); // получаем опции
if( count($options) == 0 )
{
	echo false;
	die();
}
if( !isset($options['comments_order']) ) $options['comments_order'] = 'asc';
if( !isset($options['comments_show_type']) ) $options['comments_show_type'] = 'simple';
if( !isset($options['comments_page_limit']) ) $options['comments_page_limit'] = 0;
if( !isset($options['comments_child_limit']) ) $options['comments_child_limit'] = 0;
if( !isset($options['comments_pagination_type']) ) $options['comments_pagination_type'] = 'none';
if( !isset($options['comments_pagination_next']) ) $options['comments_pagination_next'] = 'comments-next';
if( !isset($options['comments_pagination_page_content']) ) $options['comments_pagination_page_content'] = true;
if( !isset($options['comments_max_tree_level']) ) $options['comments_max_tree_level'] = 0;

$CI = & get_instance();
mso_checkreferer();
global $MSO;
	
# подгурзка по пагинации и по загрузке дополнительных комментариев
if( $post = mso_check_post( array('do', 'current', 'base') ) )
{
	$post = mso_clean_post( array(
		'do' => 'base',
		'current' => 'int|base',
		'base' => 'base',
		'parent_id' => 'int|base',
	) );
		
	# загружаем новую порцию записей
	if( $post['do'] == 'loadmore' && is_numeric($post['current']) )
	{
		require_once(getinfo('common_dir') . 'page.php'); # функции страниц 
			
		$post['base'] = str_replace('/'.$options['comments_pagination_next'].'/1', '', $post['base']);

		if( function_exists('friendly_urls_parse_page_uri') && strpos($post['base'], 'page/') === false )
		{
			$page = friendly_urls_parse_page_uri('/'.$post['base']);
			$post['base'] = $page['slug'];
		}
			
		# параметры для получения страницы
		$par = array( 
				'type' => false,
				'custom_type' => 'page',
				'slug' => str_replace('page/', '', $post['base']),
			);
		$pages = mso_get_pages($par, $pagination);	#_pr($pages);
		if( !$pages ) die();

		$page = $pages[0];

		# если страница не определена или комментарии запрещены, то выходим
		if( !$page || !$page['page_comment_allow'] || $page['page_status'] !== 'publish' ) die();
		
		$page_text_ok = true; # разрешить вывод текста комментария в зависимости от пароля записи
		if( isset($page['page_password']) and $page['page_password'] ) # есть пароль у страницы
		{
			$page_text_ok = (isset($page['page_password_ok'])); # нет отметки, что пароль пройден
		}
		if( !$page_text_ok ) die(); // пароль к записи неверный
			
		# параметры для получения страниц
		$par = array(
			'limit' => $options['comments_page_limit'],
			'order' => $options['comments_order'],
			'max_tree_level' => $options['comments_max_tree_level'],
			'pagination' => $options['comments_pagination_type'] != 'none' ? $options['comments_pagination_type'] : false,
			'pagination_next_url' => $options['comments_pagination_next'],
			'out_type' => $options['comments_show_type'],
			'current' => $post['current'],
		);
				
		$comments = comments_get_comments($page['page_id'], $par, $pagination);	#pr($comments);

		if( $comments ) # есть комментарии
		{
			if( $options['comments_show_type'] == 'simple' )
			{
				comments_show_simple( $page, $comments, $options['comments_page_limit'] * ( $post['current'] - 1 ) );
			}
			else
			{
				comments_show_tree( $page, $comments, -1, $post['current'] );
			}
		}
			
		die();
	}

	# загружаем новую порцию записей
	if( $post['do'] == 'getmore' && is_numeric($post['current']) )
	{
		$out = '';
			
		require_once(getinfo('common_dir') . 'page.php'); # функции страниц 
			
		$post['base'] = str_replace('/'.$options['comments_pagination_next'].'/1', '', $post['base']);

		if( function_exists('friendly_urls_parse_page_uri') && strpos($post['base'], 'page/') === false )
		{
			$page = friendly_urls_parse_page_uri('/'.$post['base']);
			$post['base'] = $page['slug'];
		}
			
		# параметры для получения страницы
		$par = array( 
				'type' => false,
				'custom_type' => 'page',
				'slug' => str_replace('page/', '', $post['base']),
			);
		$pages = mso_get_pages($par, $pagination);	#pr($pages);
		if( !$pages ) die();
		$page = $pages[0];
			
		# если страница не определена или комментарии запрещены, то выходим
		if( !$page || !$page['page_comment_allow'] || $page['page_status'] !== 'publish' ) die();
		
		$page_text_ok = true; # разрешить вывод текста комментария в зависимости от пароля записи
		if( isset($page['page_password']) and $page['page_password'] ) # есть пароль у страницы
		{
			$page_text_ok = (isset($page['page_password_ok'])); # нет отметки, что пароль пройден
		}
		if( !$page_text_ok ) die(); // пароль к записи неверный
			
		# параметры для получения страниц
		$par = array(
			'limit' => $options['comments_page_limit'],
			'order' => $options['comments_order'],
			'max_tree_level' => $options['comments_max_tree_level'],
			'pagination' => $options['comments_pagination_type'] != 'none' ? $options['comments_pagination_type'] : false,
			'pagination_next_url' => $options['comments_pagination_next'],
			'out_type' => $options['comments_show_type'],
			'limit_child' => $options['comments_child_limit'],
			'current' => intval($post['current']) + 1,
			'parent_id' => $post['parent_id'],
		);
			
		$comments = comments_get_comments($page['page_id'], $par, $pagination);	#pr($comments);
			
		if( $comments ) # есть комментарии
		{
			ob_start();
			comments_show_tree( $page, $comments, -1, intval($post['current']) + 1 );
			$out .= ob_get_contents(); ob_end_clean();
		}
			
		die( json_encode( array(
			'res' => $out,
			'hide' => (intval($post['current']) + 1) >= $pagination['maxcount'] ? true : false,
		)));
	}
	
}
elseif( $post = mso_check_post( array('do', 'comments_session', 'comments_page_id', 'comments_content') ) )
{
	$post = mso_clean_post( array(
		'do' => 'base',
		'comments_session' => 'base',
		'comments_page_id' => 'int|base',
		'base' => 'base',
		'comments_parent_id' => 'int|base',
		'comments_content' => '',
		'comments_submit' => 'base',
	) );
		
	if( $post['do'] == 'save' )
	{
		require_once(getinfo('common_dir') . 'page.php'); # функции страниц 
			
		$post['base'] = preg_replace("!\/".$options['comments_pagination_next']."\/(.+?)$!", '', $post['base']);

		$base =	$post['base'];
		
		if( function_exists('friendly_urls_parse_page_uri') && strpos($post['base'], 'page/') === false )
		{
			$page = friendly_urls_parse_page_uri('/'.$post['base']);
			$post['base'] = $page['slug'];
		}
			
		# параметры для получения страницы
		$par = array( 
				'type' => false,
				'custom_type' => 'page',
				'slug' => str_replace('page/', '', $post['base']),
			);
		$pages = mso_get_pages($par, $pagination);
		if( !$pages ) die('page not found');
		$page = $pages[0];
			
		require_once( getinfo('common_dir').'comments.php' ); # стандартные функции комментариев
			
		mso_hook_add('mso_email_message_new_comment', basename(dirname(__FILE__)).'_new_comment', 10); # new_comment

		$_SERVER['REQUEST_URI'] = '/'.$base;
			
		if( $out = mso_get_new_comment( array('page_title'=>$page['page_title']) ) )
		{
			die( json_encode( array(
				'res' => $out,
				'max_tree_level' => $options['comments_max_tree_level'],
				'err' => true,
			)));
		}
	}
}
elseif( $post = mso_check_post( array('do', 'id') ) )
{
	$post = mso_clean_post( array(
		'do' => 'base',
		'id' => 'int|base',
	) );
		
	if( $post['do'] == 'like' )
	{
		# данные хранятся в куках посетителя
		$name_cookies = 'comment_rating';
		$expire = 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд

		if( isset($_COOKIE[$name_cookies]) )
			$all_comments = $_COOKIE[$name_cookies]; # значения текущего кука
		else
			$all_comments = ''; # нет такой куки вообще

		$id = $post['id']; # коммментарий для которого пришел запрос

		$all_comments = explode(' ', $all_comments); # разделим в массив
			
		$comment_rating = 0; # рейтинг комментария по-умолчанию
			
		if( in_array($id, $all_comments) ) # уже есть текущий каммент - уменьшаем счетчик
		{
			$key = array_search($id, $all_comments);
		
			unset($all_comments[$key]); # удаляем текущий id
			$all_comments = array_unique($all_comments); # удалим дубли на всякий пожарный
			$all_comments = implode(' ', $all_comments); # соединяем обратно в строку
			$expire = time() + $expire;
			@setcookie($name_cookies, $all_comments, $expire); # записали в куку
				
			$CI->db->select('comments_rating');
			$CI->db->where('comments_id', $id);
			$CI->db->limit(1);

			$qry = $CI->db->get('comments');
				
			if( is_object($qry) && $qry->num_rows() > 0 )
			{
				$row = $qry->row();
				$comment_rating = $row->comments_rating; // текущая оценка
				$comment_rating--; 
				$comment_rating = $comment_rating < 0 ? 0 : $comment_rating;
				
				$CI->db->where('comments_id', $id);
				$CI->db->update('comments', array( 'comments_rating' => $comment_rating ) );
					
				mso_hook('global_cache_all_flush'); # сбрасываем весь html-кэш
			}
		}
		else # увеличиваем счётчик
		{
			$all_comments[] = $id; # добавляем текущий id
			$all_comments = array_unique($all_comments); # удалим дубли на всякий пожарный
			$all_comments = implode(' ', $all_comments); # соединяем обратно в строку
			$expire = time() + $expire;
			@setcookie($name_cookies, $all_comments, $expire); # записали в куку
				
			$CI->db->select('comments_rating');
			$CI->db->where('comments_id', $id);
			$CI->db->limit(1);
				
			$qry = $CI->db->get('comments');
				
			if( is_object($qry) && $qry->num_rows() > 0 )
			{
				$row = $qry->row();
				$comment_rating = $row->comments_rating; // текущая оценка
				$comment_rating++;
				
				$CI->db->where('comments_id', $id);
				$CI->db->update('comments', array( 'comments_rating' => $comment_rating ) );
					
				mso_hook('global_cache_all_flush'); # сбрасываем весь html-кэш
			}
		}
			
		die(strval($comment_rating));
	}
}
	
die('method not found');
?>