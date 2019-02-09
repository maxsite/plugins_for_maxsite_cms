<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function https_autoload($args = array())
{
	mso_hook_add( 'init', 'https_init');
}

# функция выполняется при деинсталяции плагина
function https_uninstall($args = array())
{
	mso_delete_option('plugin_https', 'plugins' ); // удалим созданные опции
	return $args;
}


function https_mso_options()
{

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_https', 'plugins',
		array(
			'https_level' => array(
							'type' => 'select',
							'name' => t('Где использовать'),
							'description' => t('Укажите, где должен работать плагин. Перед включением проверьте, что у Вас сайт настроен для работы по HTTPS: ') .
                                     '<a target="_blank" href="https://' . $_SERVER['HTTP_HOST'] . '/admin">ссылка</a>.',
							'values' => t('1||В админке #2|| В админке и на страницах сайта #3 ||Выключено'),
							'default' => '3'
						),
			),
		t('Настройки плагина https'),
		t('Укажите необходимые опции.')
	);
}

# функции плагина
function https_init()
{
    $admin_urls = array (
                'login',
                'admin'
            );
    $https_on = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? true : false);

    $options = mso_get_option('plugin_https', 'plugins', array());
	if (!array_key_exists('https_level', $options)) $options['https_level'] = 3;
    extract($options);

    $redirect = false;

    switch ($https_level) {
        case '1':
            if (in_array(mso_segment(1),$admin_urls)) {
                $redirect = true;
            }
            break;
        case '2':
            $redirect = true;
            break;
        case '3':
            $redirect = false;
            break;

    }
    
    if (mso_segment(1) == 'login') $request_uri = '/admin';
        else $request_uri =  $_SERVER['REQUEST_URI'];
    //fallback
    if (mso_segment(1) == 'admin' && mso_segment(2) == 'plugin_options' && mso_segment(3) == 'https') $redirect = false;

    if ($redirect && $https_on == false) header('Location: https://' . $_SERVER['HTTP_HOST'] . $request_uri);
}



# end file
