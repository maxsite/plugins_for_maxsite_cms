<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * LeoXCoder
 * (c) http://rgblog.ru/
 * За основу используется Autoptimize 1.9.1
 */


# функция автоподключения плагина
function rg_minify_autoload()
{
    mso_hook_add('global_cache_start', 'rg_minify_start');
}

# функция выполняется при активации (вкл) плагина
function rg_minify_activate($args = array())
{
    mso_create_allow('rg_minify_edit', t('Админ-доступ к настройкам RG-Minify'));
    # Сразу вписываем настройки по-умолчанию
    $options = rg_minify_default_config();
    mso_add_option('plugin_rg_minify', $options, 'plugins');
    return $args;
}

# функция выполняется при деинсталяции плагина
function rg_minify_uninstall($args = array())
{
    mso_remove_allow('rg_minify_edit'); # удалим созданные разрешения
    mso_delete_option('plugin_rg_minify', 'plugins'); # удалим созданные опции
    return $args;
}

function rg_minify_default_config()
{
    return array(
        'legacy_minifers' => 0, # Какую группу класов использовать
        'cachedir' => getinfo('cache_dir') . 'assets/', # Путь к кэшу
        'cachedelay' => 1, # Использовать конфигурацию delayed или default. По умолчанию delayed, новая.
        # Cache
        'nogzip' => true, # Не использовать GZIP сжатие
        # HTML
        'html' => 0, # Оптимизировать код HTML?
        'html_keepcomments' => 1, # Сохранять комментарии HTML? <!-- -->
        # JS
        'js' => 1, # Оптимизировать код JavaScript
        'js_do_minify' => 1, # Минифицировать код JavaScript
        'js_forcehead' => 0, # Помещать JavaScrip в HEAD
        'js_justhead' => 0, # Искать скрипты только в HEAD
        'js_exclude' => "s_sid, smowtion_size, sc_project, WAU_, wau_add, comment-form-quicktags, edToolbar, ch_client, nonce, post_id", # Исключить скрипты из обработки
        'js_trycatch' => 0, # Добавить обертку try-catch
        'js_include_inline' => 1, # Сжимать встроенный JavaScript
        # CSS
        'css' => 1, # Оптимизировать код CSS
        'css_do_minify' => 1, # Минифицировать код CSS
        'css_datauris' => 0, # Создавать data:УРЛы для картинок
        'css_justhead' => 0, # Искать стили только в HEAD
        'css_defer' => 0, # Отложить загрузку CSS
        'css_defer_inline' => "h1,h2{color:red !important;}", # Использовать этот CSS код при отложенной загрузке
        'css_inline' => 0, # Встроить все CSS в код HTML
        'css_exclude' => "admin-bar.min.css, dashicons.min.css", # Исключить CSS из обработки
    );
}

function rg_minify_mso_options()
{
    if (!mso_check_allow('rg_minify_edit')) {
        echo t('Доступ запрещен');
        return;
    }

    if (mso_segment(4) == 'clear')
    {
        include_once(getinfo('plugins_dir') . 'rg_minify/classes/minifyCache.php');
        minifyCache::clearall();
        echo '<div class="update">' . t('Кэш очищен!', 'plugins') . '</div>';
    }
    $settings = rg_minify_default_config();

    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_rg_minify', 'plugins',
        array(
            'info1' => array(
                'type' => 'info',
                'title' => 'Cache',
            ),
            'nogzip' => array(
                'type' => 'checkbox',
                'name' => t('Не использовать GZIP сжатие'),
                'description' => t('По умолчанию файлы сохраняются статическими css/js, выключите эту опцию, если Ваш сервер не правильно обрабатывает сжатие и сроки'),
                'default' => $settings['nogzip']
            ),
            'info2' => array(
                'type' => 'info',
                'title' => 'HTML',
            ),
            'html' => array(
                'type' => 'checkbox',
                'name' => t('Оптимизировать код HTML'),
                'description' => '',
                'default' => $settings['html']
            ),
            'html_keepcomments' => array(
                'type' => 'checkbox',
                'name' => t('Сохранять комментарии HTML'),
                'description' => t('Нужно ли удалять комментарии HTML кода.'),
                'default' => $settings['html_keepcomments']
            ),
            'info3' => array(
                'type' => 'info',
                'title' => 'JavaScript',
            ),
            'js' => array(
                'type' => 'checkbox',
                'name' => t('Оптимизировать код JavaScript'),
                'description' => '',
                'default' => $settings['js']
            ),
            'js_do_minify' => array(
                'type' => 'checkbox',
                'name' => t('Минифицировать код JavaScript'),
                'description' => '',
                'default' => $settings['js_do_minify']
            ),
            'js_forcehead' => array(
                'type' => 'checkbox',
                'name' => t('Помещать JavaScript в HEAD'),
                'description' => t('Для лучшей производительности нужно помещать скрипты в конец HTML документа, но иногда из-за этого скрипты не работают (Темы JQuery)'),
                'default' => $settings['js_forcehead']
            ),
            'js_justhead' => array(
                'type' => 'checkbox',
                'name' => t('Искать скрипты только в HEAD'),
                'description' => t('Особенно полезная опция в комбинации с предыдущей, когда используются темы на основе jQuery'),
                'default' => $settings['js_justhead']
            ),
            'js_exclude' => array(
                'type' => 'text',
                'name' => t('Исключить скрипты из обработки'),
                'description' => t('Вписываем часть названия скрипта или часть кода скрипта, если это встроенный в HTML скрипт'),
                'default' => $settings['js_exclude']
            ),
            'js_trycatch' => array(
                'type' => 'checkbox',
                'name' => t('Добавить обертку try-catch'),
                'description' => t('Каждый скрипт обернет комбинацией try{ }catch(e){ }. Чтобы скрипт с ошибкой не прерывал работу нижеследующего кода'),
                'default' => $settings['js_trycatch']
            ),
            'js_include_inline' => array(
                'type' => 'checkbox',
                'name' => t('Сжимать встроенный JavaScript'),
                'description' => t('Если галочку убрать, то встроенные скрипты не будут сжиматься'),
                'default' => $settings['js_include_inline']
            ),
            'info4' => array(
                'type' => 'info',
                'title' => 'CSS',
            ),
            'css' => array(
                'type' => 'checkbox',
                'name' => t('Оптимизировать код CSS'),
                'description' => '',                
                'default' => $settings['css']
            ),
            'css_do_minify' => array(
                'type' => 'checkbox',
                'name' => t('Минифицировать код CSS'),
                'description' => '',                
                'default' => $settings['css_do_minify']
            ),
            'css_datauris' => array(
                'type' => 'checkbox',
                'name' => t('Создавать data:УРЛы для картинок'),
                'description' => t('При включении маленькие background картинки в CSS будут встроены в создаваемый CSS файл'),
                'default' => $settings['css_datauris']
            ),
            'css_justhead' => array(
                'type' => 'checkbox',
                'name' => t('Искать стили только в HEAD'),
                'description' => t('Плагин обработает стили встречающиеся только в HEAD части документа'),
                'default' => $settings['css_justhead']
            ),
            'css_defer' => array(
                'type' => 'checkbox',
                'name' => t('Отложить загрузку CSS'),
                'description' => t('Стили будут загружены после полной загрузки HTML'),
                'default' => $settings['css_defer']
            ),
            'css_defer_inline' => array(
                'type' => 'textarea',
                'rows' => 2,
                'name' => t('Использовать этот CSS код при отложенной загрузке'),
                'description' => t('Данный код будет загружаться сразу, в отличие от всего остального'),
                'default' => $settings['css_defer_inline']
            ),
            'css_inline' => array(
                'type' => 'checkbox',
                'name' => t('Встроить все CSS в код HTML'),
                'description' => t('Стили будут встроены в HTML документ'),
                'default' => $settings['css_inline']
            ),
            'css_exclude' => array(
                'type' => 'text',
                'name' => t('Исключить CSS из обработки'),
                'description' => t('Таккже как для скриптов, указываем тут часть названия файла, которые не нужно объединять.'),
                'default' => $settings['css_exclude']
            ),
            'info5' => array(
                'type' => 'info',
                'title' => '<a href="'.getinfo('site_admin_url').'plugin_options/rg_minify/clear">Очистить кэш</a>',
            ),
        ),
        t('Настройки плагина RG-Minify'), # титул
        t('Укажите необходимые опции.') # инфо
    );
}

function rg_minify_config()
{
    $settings = rg_minify_default_config(); # опции по-умолчанию
    $options = mso_get_option('plugin_rg_minify', 'plugins', array()); # получаем опции
    return array_merge($settings, $options); # возвращаем объединенный массив
}

function rg_minify_start($args = array())
{
    $conf = rg_minify_config();
    include_once(getinfo('plugins_dir') . 'rg_minify/classes/minifyCache.php');
    include(getinfo('plugins_dir') . 'rg_minify/classes/minifyBase.php');

    if ($conf['html']) {
        include(getinfo('plugins_dir') . 'rg_minify/classes/minifyHTML.php');
        // БАГ: новый минификатор html не поддерживает сохранение html комментариев, пропускаем пока
        // if ($conf['legacy_minifers']) {
        @include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/minify-html.php');
        // } else {
        //	@include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/minify-2.1.7-html.php');
        // }
    }

    if ($conf['js']) {
        include(getinfo('plugins_dir') . 'rg_minify/classes/minifyScripts.php');
        if (!class_exists('JSMin')) {
            if ($conf['legacy_minifers']) {
                @include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/jsmin-1.1.1.php');
            } else {
                @include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/minify-2.1.7-jsmin.php');
            }
        }
    }

    if ($conf['css']) {
        include(getinfo('plugins_dir') . 'rg_minify/classes/minifyStyles.php');
        if ($conf['legacy_minifers']) {
            if (!class_exists('Minify_CSS_Compressor')) {
                @include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/minify-css-compressor.php');
            }
        } else {
            if (!class_exists('CSSmin')) {
                @include(getinfo('plugins_dir') . 'rg_minify/classes/external/php/yui-php-cssmin-2.4.8-3.php');
            }
        }
    }
    //Если папка кэша доступна, то запускаем работу плагина
    if (minifyCache::cacheavail()) {
        mso_hook_add('global_cache_end', 'rg_minify_end');
        ob_start();
    }
}

function rg_minify_end($args = array())
{
    $conf = rg_minify_config();
    $content = ob_get_contents();
    ob_end_clean();

    if (stripos($content, "<html") === false || stripos($content, "<xsl:stylesheet") !== false) {
        echo $content;
        exit;
    }

    // Выбор классов
    $classes = array();
    if ($conf['js']) $classes[] = 'minifyScripts';
    if ($conf['css']) $classes[] = 'minifyStyles';
    if ($conf['html']) $classes[] = 'minifyHTML';

    // Запуск классов
    foreach ($classes as $name) {
        $instance = new $name($content);
        if ($instance->read()) {
            $instance->minify();
            $instance->cache();
            $content = $instance->getcontent();
        }
        unset($instance);
    }

    echo $content;
}

# end file