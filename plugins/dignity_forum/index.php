<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// функция авто подключения плагина
function dignity_forum_autoload()
{
	// хук на админку
	mso_hook_add('admin_init', 'dignity_forum_admin_init');
	
	// хук для подключения к шаблону
	mso_hook_add('custom_page_404', 'dignity_forum_custom_page_404');

	// для вывода количества тем и ответов /users/id
	mso_hook_add('users_add_out', 'dignity_forum_users_add_out', '90');
	
	// регестируем виджет
	mso_register_widget('dignity_forum_widget', t('Недавно обновленные темы на форуме', __FILE__));

	// хук на css стили
	mso_hook_add('head', 'forum_style_css');
}

// функция выполняется при активации (вкл) плагина
function dignity_forum_activate($args = array())
{	
	// подключаем файл
	require_once(getinfo('plugins_dir') . 'dignity_forum/core/activate.php');

	return $args;
}

// функция выполняется при деинсталяции плагина
function dignity_forum_uninstall($args = array())
{
	// подключаем файл
	require_once(getinfo('plugins_dir') . 'dignity_forum/core/uninstall.php');

	return $args;
}

// подключаем функции виджета из файла
require_once(getinfo('plugins_dir') . 'dignity_forum/widgets/forum_news.php');

// функция выполняется при указаном хуке admin_init
function dignity_forum_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_forum_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_forum'; 
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Форум', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_forum_admin_page');
	
	return $args;
}

// функция вызываемая при хуке, указанном в mso_admin_url_hook
function dignity_forum_admin_page($args = array()) 
{

	if ( !mso_check_allow('dignity_forum_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Форум', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Форум', __FILE__) . ' - " . $args; ' );

	// редактирования категорий
	if ( mso_segment(3) == 'edit_category') require(getinfo('plugins_dir') . 'dignity_forum/admin/category/edit_category.php');
	elseif ( mso_segment(3) == 'editone_category') require(getinfo('plugins_dir') . 'dignity_forum/admin/category/editone_category.php');
	
	// редактирования тем
	elseif ( mso_segment(3) == 'edit_topic') require(getinfo('plugins_dir') . 'dignity_forum/admin/topic/edit_topic.php');
	elseif ( mso_segment(3) == 'editone_topic') require(getinfo('plugins_dir') . 'dignity_forum/admin/topic/editone_topic.php');
	
	// редактирования ответов
	elseif ( mso_segment(3) == 'edit_reply') require(getinfo('plugins_dir') . 'dignity_forum/admin/reply/edit_reply.php');
	elseif ( mso_segment(3) == 'editone_reply') require(getinfo('plugins_dir') . 'dignity_forum/admin/reply/editone_reply.php');
	
	elseif ( mso_segment(3) == 'meta') require(getinfo('plugins_dir') . 'dignity_forum/admin/meta.php');

	elseif ( mso_segment(3) == 'maintenance') require(getinfo('plugins_dir') . 'dignity_forum/admin/maintenance.php');

	elseif ( mso_segment(3) == 'rules') require(getinfo('plugins_dir') . 'dignity_forum/admin/rules.php');

	else require(getinfo('plugins_dir') . 'dignity_forum/admin/admin.php');
}

// для подключения к шаблону
function dignity_forum_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'forum';
	if ( !isset($options['offline']) ) $options['offline'] = false;
	if ( !isset($options['offline_text']) ) $options['offline_text'] = t('Форум закрыть на обслуживания! Зайдите пожалуйста позже!', __FILE__); 
   
	if ( mso_segment(1)==$options['slug'] )
	{
		
		// если включён режим обслуживания и не вошел админ
		if ($options['offline'] && !is_login())
		{
			// загружаем начало шаблона
			require(getinfo('shared_dir') . 'main/main-start.php');
	  
			// выводим текст из опции
			echo $options['offline_text'];
				
			// для поисковых систем
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Retry-After: ' . gmdate("D, d M Y H:i:s", time() + 86400) . ' GMT');
				
			// загружаем конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  
		}
		// если выключен режим обслуживания, либо вошел админ...
		else
		{
			// если просмотр темы
			if(mso_segment(2) == 'view')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/view.php' );
			}
			// если тема
			elseif (mso_segment(2) == 'topic')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/topic.php' ) ;
			}
			// если редактировать
			elseif (mso_segment(2) == 'edit')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/user/edit_topic.php' ) ;
			}
			// если редактировать ответ
			elseif (mso_segment(2) == 'edit_reply')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/user/edit_reply.php' ) ;
			}
			// если пользователи
			elseif (mso_segment(2) == 'profile')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/profile.php' ) ;
			}
			// если новые темы
			elseif (mso_segment(2) == 'topics')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/new_topics.php');
			}
			// если ответы пользователя
			elseif (mso_segment(2) == 'replys')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/new_replys.php');
			}
			// если rss лента
			elseif (mso_segment(2) == 'rss')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/rss.php');
			}
			// если карта сайта (xml)
			elseif (mso_segment(2) == 'sitemap')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/sitemap.php');
			}
			// если правила
            elseif (mso_segment(2) == 'rules')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/rules.php');
			}
			// если поиск
            elseif (mso_segment(2) == 'search')
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/search.php');
			}
			// иначе загружаем forums
			else
			{
				require( getinfo('plugins_dir') . 'dignity_forum/views/forums.php' ) ;
			}
		}
		
		return true;
	}

	return $args;
}

// подключаем css стили
function forum_style_css($a = array())
{
	// если существует файл custom, то...
	if (file_exists(getinfo('plugins_dir') . 'dignity_forum/css/custom.css'))
	{
		$css = getinfo('plugins_url') . 'dignity_forum/css/custom.css';
	}
	// иначе...
	else $css = getinfo('plugins_url') . 'dignity_forum/css/style.css';
		
	echo '<link rel="stylesheet" href="' . $css . '">' . NR;
	
	return $a;
}

// функция хука users_add_out
// выводит количество тем и ответов на странице комюзера /users/id
function dignity_forum_users_add_out($comuser = array())
{
	// подключаем файл
	require_once(getinfo('plugins_dir') . 'dignity_forum/user/users_add_out.php');

	return $comuser;
}

#end of file
