<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class minifyBase
{
    protected $content = '';

    public function __construct($content)
    {
        $this->content = $content;
        //Хорошее место для отлова ошибок
    }

    //Читает страницу и собирает теги
    abstract public function read();

    //Объединение и оптимизация собранных вещей
    abstract public function minify();

    //Кэширует вещи
    abstract public function cache();

    //Возвращает содержимое
    abstract public function getcontent();

    //Преобразует URL в полный путь
    protected function getpath($url)
    {
        if (strpos($url, '%') !== false) {
            $url = urldecode($url);
        }

        // нормализация
        if (strpos($url, '//') === 0) {
            $url = "http:" . $url;
        } else if ((strpos($url, '//') === false) && (strpos($url, parse_url(getinfo('siteurl'), PHP_URL_HOST)) === false)) {
            $url = getinfo('siteurl') . $url;
        }

        // первая проверка; имя сайта должно совпадать
        if (parse_url($url, PHP_URL_HOST) !== parse_url(getinfo('siteurl'), PHP_URL_HOST)) {
            return false;
        }

        // попытка удалить корень сайта из url не трогая http<>https
        $tmp_ao_root = preg_replace('/https?/', '', getinfo('siteurl'));
        $tmp_url = preg_replace('/https?/', '', $url);
        $path = str_replace($tmp_ao_root, '', $tmp_url);

        // финальная проверка; если путь начинается с :// или //, это не локальный url, и мы не сможем подключиться
        if (preg_match('#^:?//#', $path)) {
            /** Внешние script/css (adsense, и др.) */
            return false;
        }

        // проверка на require-maxsite - бесполезно, не используется
        if (strpos($path, 'require-maxsite') !== false) {
            $path = str_replace('require-maxsite/', '', $path);
            $path = getinfo('base_dir') . base64_decode($path);
        } else $path = str_replace('//', '/', getinfo('FCPATH') . $path);

        return $path;
    }

    //Преобразует путь в URL
    protected function geturl($path)
    {
        // попытка удалить корень сайта из path
        $path = str_replace(getinfo('FCPATH'), '', $path);
        $path = str_replace('//', '/', $path);
        if (strpos($path, '/') == 0) $path = substr($path, 1);
        $url = getinfo('site_url') . $path;
        return $url;
    }

    // логгер
    protected function ao_logger($logmsg, $appendHTML = true)
    {
        if ($appendHTML) {
            $logmsg = "<!--noptimize--><!-- " . $logmsg . " --><!--/noptimize-->";
            $this->content .= $logmsg;
        } else {
            $logfile = getinfo('FCPATH') . '/ao_log.txt';
            $logmsg .= "\n--\n";
            file_put_contents($logfile, $logmsg, FILE_APPEND);
        }
    }

    // скрыть все между noptimize тегами
    protected function hide_noptimize($noptimize_in)
    {
        if (preg_match('/<!--\s?noptimize\s?-->/', $noptimize_in)) {
            $noptimize_out = preg_replace_callback(
                '#<!--\s?noptimize\s?-->.*?<!--\s?/\s?noptimize\s?-->#is',
                create_function(
                    '$matches',
                    'return "%%NOPTIMIZE%%".base64_encode($matches[0])."%%NOPTIMIZE%%";'
                ),
                $noptimize_in
            );
        } else {
            $noptimize_out = $noptimize_in;
        }
        return $noptimize_out;
    }

    // отобразить noptimize тэги
    protected function restore_noptimize($noptimize_in)
    {
        if (strpos($noptimize_in, '%%NOPTIMIZE%%') !== false) {
            $noptimize_out = preg_replace_callback(
                '#%%NOPTIMIZE%%(.*?)%%NOPTIMIZE%%#is',
                create_function(
                    '$matches',
                    'return stripslashes(base64_decode($matches[1]));'
                ),
                $noptimize_in
            );
        } else {
            $noptimize_out = $noptimize_in;
        }
        return $noptimize_out;
    }

    protected function hide_iehacks($iehacks_in)
    {
        if (strpos($iehacks_in, '<!--[if') !== false) {
            $iehacks_out = preg_replace_callback(
                '#<!--\[if.*?\[endif\]-->#is',
                create_function(
                    '$matches',
                    'return "%%IEHACK%%".base64_encode($matches[0])."%%IEHACK%%";'
                ),
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    protected function restore_iehacks($iehacks_in)
    {
        if (strpos($iehacks_in, '%%IEHACK%%') !== false) {
            $iehacks_out = preg_replace_callback(
                '#%%IEHACK%%(.*?)%%IEHACK%%#is',
                create_function(
                    '$matches',
                    'return stripslashes(base64_decode($matches[1]));'
                ),
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    protected function hide_comments($comments_in)
    {
        if (strpos($comments_in, '<!--') !== false) {
            $comments_out = preg_replace_callback(
                '#<!--.*?-->#is',
                create_function(
                    '$matches',
                    'return "%%COMMENTS%%".base64_encode($matches[0])."%%COMMENTS%%";'
                ),
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }

    protected function restore_comments($comments_in)
    {
        if (strpos($comments_in, '%%COMMENTS%%') !== false) {
            $comments_out = preg_replace_callback(
                '#%%COMMENTS%%(.*?)%%COMMENTS%%#is',
                create_function(
                    '$matches',
                    'return stripslashes(base64_decode($matches[1]));'
                ),
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }

    protected function inject_in_html($payload, $replaceTag)
    {
        if (strpos($this->content, $replaceTag[0]) !== false) {
            if ($replaceTag[1] === "after") {
                $replaceBlock = $replaceTag[0] . $payload;
            } else if ($replaceTag[1] === "replace") {
                $replaceBlock = $payload;
            } else {
                $replaceBlock = $payload . $replaceTag[0];
            }
            $this->content = str_replace($replaceTag[0], $replaceBlock, $this->content);
        } else {
            $this->content .= $payload;
            if (!$tagWarning) {
                $this->content .= "<!--noptimize--><!-- RG-Minify обнаружил проблему с HTML в вашем шаблоне, тэг " . $replaceTag[0] . " отсутствует --><!--/noptimize-->";
                $tagWarning = true;
            }
        }
    }
}
