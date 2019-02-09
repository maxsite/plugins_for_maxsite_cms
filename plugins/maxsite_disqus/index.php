<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function maxsite_disqus_autoload()
{
   mso_hook_add('type-foreach-file', 'maxsite_disqus_f1'); // type_foreach-файлы
}

# функция выполняется при деинсталяции плагина
function maxsite_disqus_uninstall($args = array())
{	
	mso_delete_option('maxsite_disqus', 'plugins'); // удалим созданные опции
	return $args;
}

function maxsite_disqus_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('maxsite_disqus', 'plugins', 
		array(
			'disqus_shortname' => array(
						'type' => 'text', 
						'name' => 'Короткое имя в DISQUS', 
						'description' => 'your forum shortname.<br> Зарегистрирутесь на <a href="http://disqus.com/register/" target="_blank">Disqus</a>',
						'default' => ''
					),										
			),
		'Настройки плагина DISQUS MaxSite', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

function maxsite_disqus_f1($tff = false) 
{   
   // смотрим какие type_foreach-файлы и от этого подключаем свои
/*   if ($tff == 'page-comments-do') return getinfo('plugins_dir') . 'maxsite_disqus/type_foreach/page-comments-do.php';
   elseif ($tff == 'page-comments') return getinfo('plugins_dir') . 'maxsite_disqus/type_foreach/page-comments.php';
   else */ if ($tff == 'page-comment-form-do') return getinfo('plugins_dir') . 'maxsite_disqus/type_foreach/page-comment-form-do.php';
   elseif ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'maxsite_disqus/type_foreach/page-comment-form.php';
   
   return false;
}


# end file