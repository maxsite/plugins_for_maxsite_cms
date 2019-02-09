<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function update_autoload($args = array()){	
	mso_hook_add( 'admin_init', 'update_admin_init');
}

function update_activate($args = array()){	
	
	return $args;
}

function todo_uninstall($args = array()) {
	
}

function update_admin_init($args = array()) {
	global $MSO;
	if ( mso_check_allow('update') ) {
		$this_plugin_url = 'update'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'Update');
		mso_admin_url_hook ($this_plugin_url, 'update_admin');
	}
}
function update_admin($args = array()) {
	globMSO;

	if ( !mso_check_allow('update') ) {
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Update: обновление Maxsite CMS"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Update "' );

	echo  '<p>' .t('Ваша версия <strong>MaxSite CMS</strong>') . ':'.  getinfo('version') . '</p>';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_check_version')) ) {
		mso_checkreferer();

        list (  $result, $error_msg, $current_version,
                $latest_version, $current_url,
                $latest_url, $need_update
                 ) = fetch_update_info();

        if ($result) {
            if ($need_update) {
                echo '<div class="info">Вы можете <a href="' . $current_url . '">выполнить обновление</a>.</div>';
            } else {
                echo '<div class="info">' . t('Обновление не требуется.') . '</div>';
            } 
        } else {
            echo '<div class="error">'. $error_msg . '</div>';
        }
	}

    if ( $post = mso_check_post(array('f_session_id', 'f_submit_update_cms')) ) {
		mso_checkreferer();

        $root = preg_replace('/\/index.php/', '', FCPATH) . '/';
        $latest_url = 'http://max-3000.com/uploads/latest.zip';
        $latest_file = $MSO->config['uploads_dir'] . 'latest.zip';
        echo '<div class="update">Начинаем загрузку</div>';

        $latest_contents = fetch_file($latest_url, 60);

        if ($latest_contents) {
            if (write_to_file($latest_file, $latest_contents)) {
                echo '<div class="update">Загрузка завершена. Начинаю распаковку.</div>';

                require_once('pclzip.lib.php');
                $archive = new PclZip($latest_file);
                $list = $archive->extract(PCLZIP_OPT_PATH, $root, PCLZIP_OPT_REPLACE_NEWER, PCLZIP_OPT_STOP_ON_ERROR);
                if ($list == 0) {
                    echo '<div class="update">Распаковка выполнена</div>';
                } else {
                    echo '<div class="error">Распаковка не выполнена '. $archive->errorInfo(true) .'</div>';
                }
                    
            } else {
                echo '<div class="error">'. t('Не удалось записать файл архива') . '</div>';
            }
        } else {
            echo '<div class="error">'. t('Не удалось получить файл архива') . '</div>';
        }
    }

    echo '<form action="" method="post">' . mso_form_session('f_session_id');
    echo '<p><input type="submit" name="f_submit_check_version" value="' . t('Проверить последнюю версию MaxSite CMS') . '"></p>';
    echo '<p><input type="submit" name="f_submit_update_cms" value="' . t('Обновить версию до latest') . '"></p>';
    echo '</form>';
}

function fetch_update_info() {
	$url = 'http://max-3000.com/uploads/latest.txt';
    $contents = fetch_file($url);
	$latest = explode("\n", $contents); // массив
    if (!$latest) {
        $result = false;
        $error_msg =  t('Ошибка соединения с max-3000.com!'); 
        $need_update = '';
        $current_version = '';
        $latest_version = '';
        $current_url = '';
        $latest_url = '';
        
    } else {
        if ( !isset($latest[0]) or !isset($latest[1]) )	{
            $result = false;
            $error_msg = t('Полученная информация является ошибочной');
            $need_update = '';
            $current_version = '';
            $latest_version = '';
            $current_url = '';
            $latest_url = '';
        } else {
            $info1 = explode('|', $latest[0]);
            

            $info2 = explode('|', $latest[1]);

            $vers_1 = floor($info2[0]);
            $vers_2 = floor($info2[0] * 100);
            $vers = $vers_1 . '.' . $vers_2;
            
            $build = str_replace($vers, '', $info2[0]);
            

            if ( $info1[0] > getinfo('version') )
                $need_update = true;
            else
                $need_update = false;
    
            $current_version = $info1[0];
            $current_url = $info1[2];
            $latest_version = $info2[0];
            $latest_url = $info2[2];


            $result = true;
            $error_msg = '';
        }
    }
    return array (  $result, $error_msg,
                    $current_version,
                    $latest_version,
                    $current_url,
                    $latest_url,
                    $need_update
                 );
}

function fetch_file($url, $timeout = 5) {
    $ctx = stream_context_create(array( 
        'http' => array( 
            'timeout' => $timeout 
            ) 
        ) 
    );
    return file_get_contents($url, 0, $ctx);

}

function write_to_file($filename, $contents) {
    return file_put_contents ($filename, $contents);
}

?>
