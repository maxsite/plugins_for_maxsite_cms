<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function social_count_autoload()
{
	mso_hook_add( 'admin_init', 'social_count_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function social_count_activate($args = array())
{	
	mso_create_allow('social_count_edit', t('Админ-доступ к плагину Mail Send'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function social_count_uninstall($args = array())
{	
	mso_delete_option('social_count', 'plugins' ); // удалим созданные опции
	mso_remove_allow('social_count_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function social_count_admin_init($args = array()) 
{
	if ( !mso_check_allow('social_count_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'social_count'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Социальный счётчик'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/social_count
	mso_admin_url_hook ($this_plugin_url, 'social_count_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function social_count_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл

	if ( !mso_check_allow('social_count_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Социальный счётчик') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Социальный счётчик') . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'social_count/admin.php');
}

function social_count_get($type , $url ) {
    $key = "social_count_${type}_${url}";
    $k = mso_get_cache($key);
    if ($k) {
        return $k['count'];
    }
    
    switch ($type) {
        case 'facebook_likes':
            $json_string = file_get_contents('http://graph.facebook.com/' . $url);
            
            $json = json_decode($json_string);
            $count = 0;

            if(property_exists($json,'shares')) $count = intval( $json->shares );

            break;
        case 'twitter_retweets':
            $json_string = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
            
            $json = json_decode($json_string, true);
            
            $count = intval( $json['count'] );
            break;
        case 'vkontakte_shares':
            $vk_request = file_get_contents('http://vk.com/share.php?act=count&index=1&url='.$url);
            $temp = array();
            
            preg_match('/^VK.Share.count\(1, (\d+)\);$/i',$vk_request,$temp);
            
            $count = $temp[1];
            break;
        case 'googleplus_plusones':
            $curl = curl_init();
            
            curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            
            $curl_results = curl_exec ($curl);
            curl_close ($curl);
         
            $json = json_decode($curl_results, true);
         
            $count = intval( $json[0]['result']['metadata']['globalCounts']['count'] );
            break;
    }
    mso_add_cache($key, array('type' => $type, 'count' => $count));
    
    return $count;
}

# end file
