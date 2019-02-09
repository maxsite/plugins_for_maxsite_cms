<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

#############################
#
# Alexander Schilling
# http://alexanderschilling.net
#
#############################

function vkontakte_comments_autoload()
{
	mso_hook_add('head', 'vkontakte_comments_head');
	mso_hook_add('type-foreach-file', 'vkontakte_comments_f1');
}

function vkontakte_comments_uninstall($args = array())
{	
	mso_delete_option('vkontakte_comments', 'plugins');
	return $args;
}

function vkontakte_comments_mso_options() 
{
	mso_admin_plugin_options('vkontakte_comments', 'plugins', 
		array(
			'apiid' => array(
						'type' => 'text', 
						'name' => 'API Id:',
						'description' => 'Получить API ID можно <a href="http://vk.com/dev/Comments" target="_blank">здесь</a>. Обычно он находится внутри кода, выдаваемого для вставки, в строке, которая выглядит так: <code>VK.init({apiId: 4308723, onlyWidgets:</code>. В показанном примере API ID будет равен 4308723. Также API ID можно получить через операцию <a href="http://vk.com/apps?act=manage" target="_blank">регистрации приложения</a>.',
						'default' => ''
					),										
			'limit' => array(
						'type' => 'text', 
						'name' => 'Количество отбражаемых комментариев',
						'description' => 'Укажите количество комментариев, которые будут отображены сразу. Остальные будут подгружаться при нажатии кнопки "К предыдущим записям". Стандартные значения: 5, 10, 15, 20.',
						'default' => '10'
					),										
			'width' => array(
						'type' => 'text', 
						'name' => 'Ширина виджета',
						'description' => 'Укажите ширину виджета',
						'default' => '600'
					),										
			),
		'Настройки плагина Vkontakte Comments',
		'Укажите необходимые опции.'
	);
}

function vkontakte_comments_f1($tff = false) 
{   
   if ($tff == 'page-comment-form-do') return getinfo('plugins_dir') . 'vkontakte_comments/type_foreach/page-comment-form-do.php';
   elseif ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'vkontakte_comments/type_foreach/page-comment-form.php';
   
   return false;
}

# подключаем стили плагина
function vkontakte_comments_head($arg = array())
{
	if( is_type('page') )
	{
		echo '<script type="text/javascript" src="//vk.com/js/api/openapi.js?120"></script>'.NR;
	}
	
	return $arg;
}
