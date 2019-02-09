<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function editor_showdown_autoload()
{
    if (mso_segment(2) === 'page_new' || mso_segment(2) === 'page_edit') {
        mso_hook_add('editor_custom', 'editor_showdown');
        mso_hook_add('admin_head'   , 'editor_showdown_head');
    }
}

# функция выполняется при активации (вкл) плагина
function editor_showdown_activate($args = array())
{	
	return $args;
}

# функция выполняется при деинсталяции плагина
function editor_showdown_uninstall($args = array())
{	
	mso_delete_option('editor_showdown', 'plugins' ); // удалим созданные опции
	return $args;
}

function editor_showdown_mso_options () {
    mso_admin_plugin_options('editor_showdown', 'plugins', 
        array(
            'editor' => array(
                            'type' => 'select', 
                            'name' => t('Редактор'), 
                            'description' => t('Выберите тип редактора'),
                            'values' => 'Markdown # BBCode', 
                            'default' => 'Markdown'
                        ),  
            'autosave' => array(
                            'type' => 'select', 
                            'name' => t('Автосохранение в браузере'), 
                            'description' => t(''),
                            'values' => 'Включено # Выключено', 
                            'default' => 'Включено'
                        ),  
            )
    );
}

# функция выполняется при указаном хуке admin_init

function editor_showdown_head($args = array()) {
    
        $url = getinfo('plugins_url') . 'editor_showdown/';
        $options = mso_get_option('editor_showdown', 'plugins', array() );

        if (isset($options['editor'])) {
            switch ($options['editor']) {
                case 'BBCode':
                    $js_options = 'bbcode';
                    $markup = 'BBCode';
                    break;
                case 'Markdown':
                    $js_options = 'markdown';
                    $markup = 'Markdown';
                    break;
                default:
                    $js_options = 'markdown';
                    $markup = 'Markdown';
                    break;

            }
        } else {
            $js_options = 'markdown';
            $markup = 'Markdown';
        }

        echo mso_load_jquery('ui/ui.core.packed.js');
        echo mso_load_jquery('ui/ui.tabs.packed.js');

        echo <<<EOF
        <script src="${url}static/js/showdown.js"></script>
        <script src="${url}static/js/editor.js"></script>
        <script src="${url}static/js/{$js_options}.js"></script>
        <link rel="stylesheet" href="{$url}static/css/tabs.css" type="text/css" media="screen">
        <link rel="stylesheet" href="{$url}static/css/icons.css" type="text/css" media="screen">
        <link rel="stylesheet" href="{$url}static/css/buttons.css" type="text/css" media="screen">
EOF;
        if (isset($options['autosave']) ) {
            if ($options['autosave'] == 'Включено') {
                echo <<<EOF
                <script src="${url}static/js/jstorage.js"></script>
                <script src="${url}static/js/sisyphus.min.js"></script>
EOF;
            }
        }
        
}


# функции плагина
function editor_showdown($args = array())
{

    $options = mso_get_option('editor_showdown', 'plugins', array() );

    $editor_config['url'] = getinfo('plugins_url') . 'editor_showdown/';
    $editor_config['dir'] = getinfo('plugins_dir') . 'editor_showdown/';

    if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
        else $editor_config['action'] = '';

    if (isset($args['content'])) $editor_config['content'] = $args['content'];
        else $editor_config['content'] = '';

    if (isset($args['do'])) $editor_config['do'] = $args['do'];
        else $editor_config['do'] = '';

    if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
        else $editor_config['posle'] = '';

    if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
    else 
    {
        $editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
        if ($editor_config['height'] < 100) $editor_config['height'] = 400;
    }

    if (isset($options['editor'])) {
        switch ($options['editor']) {
            case 'BBCode':
                $js_options = 'bbcode';
                $markup = 'BBCode';
                break;
            case 'Markdown':
                $js_options = 'markdown';
                $markup = 'Markdown';
                break;
            default:
                $js_options = 'markdown';
                $markup = 'Markdown';
                break;

        }
    } else {
        $js_options = 'markdown';
        $markup = 'Markdown';
    }

    # Приведение строк с <br> в первозданный вид
    $editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $editor_config['content']);
    $editor_config['content'] = preg_replace('"&lt;br&gt;"i', "\n", $editor_config['content']);

    require($editor_config['dir'] . 'editor.php');
	
}


# end file
