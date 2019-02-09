<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Плагин sm_profiler (профилировщик sql-запросов и др.)
 * Автор : searchingman ( http://wpcodex.ru)
 */



# функция автоподключения плагина
function sm_profiler_autoload()
{
	mso_hook_add('body_end', 'sm_profiler_custom',0);
}

# функция выполняется при активации (вкл) плагина
function sm_profiler_activate($args = array())
{	
	mso_create_allow('sm_profiler_admin', t('Админ-доступ к плагину sm_profiler'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function sm_profiler_deactivate($args = array())
{	
	// mso_delete_option('plugin_sm_profiler', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function sm_profiler_uninstall($args = array())
{		
	mso_remove_allow('sm_profiler_admin'); // удалим созданные разрешения
	mso_remove_hook('body_end', 'sm_profiler_custom'); 
	return $args;
}

# функции плагина
function sm_profiler_custom($arg = array())
{
		// and  (mso_check_allow('sm_profiler_edit'))
		if (is_login() and (mso_check_allow('sm_profiler_admin')))
		{
			$CI = & get_instance(); // подключение CodeIgniter
			$CI->load->library('profiler');
			$sections = array(
				'benchmarks' 		=> false, 	// Прошедшее время по точкам тестирования и общее время выполнения
				'config'  			=> false, 	// Переменные конфигурации CodeIgniter
				'controller_info' 	=> false,	// Класс контроллера и запрашиваемый метод 
				'get' 				=> false,	// Любые данные GET, переданные в запросе
				'http_headers' 		=> false,    // HTTP-заголовки для текущего запроса
				'post' 				=> false,    // Любые данные POST, переданные в запросе
				'queries' 			=> TRUE,  	// Список всех выполненных запросов в БД, включая время выполнения
				'uri_string' 		=> false,  	// URI текущего запроса
				'memory_usage' 		=> false,  	// использование памяти
				'session_data' 		=> false  	// URI текущего запроса
				);
			$CI->profiler->set_sections($sections);
			$out = $CI->profiler->run();
			echo '
			<style type="text/css">
				div.sm-profiler {padding:10px 10px 0 10px;}
				#codeigniter_profiler {padding-top:0 !important;}
			</style>';
			echo '<div class="all-wrap"><div class="sm-profiler"><div class="page_other_pages_header" style="margin:0;">Плагин: <a href="#" title="Страница плагина">sm_profiler</a>&nbsp;&nbsp;&nbsp;Версия: 1.0&nbsp;&nbsp;&nbsp;Автор: <a href="http://wpcodex.ru" target="_blank" title="сайт автора плагина sm-profiler">searchingman</a></div></div>'.$out.'</div>';
		}
}

# end file