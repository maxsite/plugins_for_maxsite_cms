<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function notifications_autoload()
{

	mso_hook_add('head', 'notifications_head');
}

# функция выполняется при активации (вкл) плагина
function notifications_activate($args = array())
{	
	mso_create_allow('notifications_edit', t('Админ-доступ к настройкам notifications'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function notifications_deactivate($args = array())
{	
	mso_delete_option('notifications_Plugin', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function notifications_uninstall($args = array())
{	
	//mso_delete_option('notifications_Plugin', 'plugins' ); // удалим созданные опции
	mso_remove_allow('notifications_edit'); // удалим созданные разрешения
	return $args;
}

function notifications_head($args = array()) 
{
	echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'notifications/style.css" type="text/css" media="screen">' . NR;
	echo '	<script type="text/javascript" src="' . getinfo('plugins_url') . 'notifications/ttw-simple-notifications-min.js"></script>';
	echo '<script type="text/javascript">
        $(document).ready(function() {
            var notifications = $(\'body\').ttwSimpleNotifications(),
            msgs = [
                \'Эти CSS3 уведомления не используют изображения.\',
                \'Этот легкий плагин имеет простой, но очень полезный API\',
                \'Эти уведомления чрезвычайно легко интегрируется в любой проект\',
                \'Вы можете стрельнуть мне на электронную почту, если есть какие-либо конкретные бесплатные разработки Вы хотели бы видеть на этом сайте\'
            ];

            notifications.show({msg:\'<a href="http://www.codebasehero.com" target="_blank">Codebase Hero</a> Это уведомление будет автоматически закрыто через 6 секунд\', autoHideDelay:6000});

            setTimeout(function() {
                notifications.show({msg:\'Просто случайные уведомления\', icon:\'http://localhost/www/application/maxsite/templates/Light/images/icon.png\'});
            }, 13000);

            setTimeout(function() {
                notifications.show({msg:\'Убедитесь, что <a href="http://www.codebasehero.com" target="_blank">Codebase Hero</a> есть в Ваших закладках и заходите почаще\', icon:\'http://localhost/www/application/maxsite/templates/Light/images/icon.png\', autoHide:false});

                setTimeout(function() {
                    notifications.show({msg:\'Не забудьте проверить мои работы на <a href="http://codecanyon.net/user/23andwalnut/portfolio" target="_blank">Premium Files</a>\', icon:\'http://localhost/www/application/maxsite/templates/Light/images/icon.png\', autoHide:false});

                }, 5000);
            }, 2000);

            var i = 0;
            setInterval(function(){
                notifications.show({msg:msgs[i], icon:\'http://localhost/www/application/maxsite/templates/Light/images/icon.png\'});
                i++;

                if(i >= msgs.length)
                    i=0;
            }, 8000);
        });
    </script>';

	return $args;
}

# end file