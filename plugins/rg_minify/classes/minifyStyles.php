<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class minifyStyles extends minifyBase
{
    private $css = array();
    private $csscode = array();
    private $url = array();
    private $restofcontent = '';
    private $mhtml = '';
    private $datauris = false;
    private $hashmap = array();

    //Поиск тегов стилей
    public function read()
    {
        $conf = rg_minify_config();

        // Удалить все, что не заголовок
        if ($conf['css_justhead'] == true) {
            $content = explode('</head>', $this->content, 2);
            $this->content = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }

        // исключения
        $excludeCSS = $conf['css_exclude'];
        if ($excludeCSS !== "") {
            $this->dontmove = array_filter(array_map('trim', explode(",", $excludeCSS)));
        }

        // Использовать отложенную загрузку?
        $this->defer = $conf['css_defer'];

        // отсрочка для встроенного кода?
        $this->defer_inline = $conf['css_defer_inline'];

        // should we inline?
        $this->inline = $conf['css_inline'];

        // Store data: URIs setting for later use
        $this->datauris = $conf['css_datauris'];

        // noptimize me
        $this->content = $this->hide_noptimize($this->content);

        // исключить noscript, поскольку те могут содержать CSS
        if (strpos($this->content, '<noscript>') !== false) {
            $this->content = preg_replace_callback(
                '#<noscript>.*?</noscript>#is',
                create_function(
                    '$matches',
                    'return "%%NOSCRIPT%%".base64_encode($matches[0])."%%NOSCRIPT%%";'
                ),
                $this->content
            );
        }

        // Сохранить IE хаки
        $this->content = $this->hide_iehacks($this->content);

        // Спрятать комментарии
        $this->content = $this->hide_comments($this->content);

        // Получаем <style> и <link>
        if (preg_match_all('#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $tag) {
                if ($this->ismovable($tag)) {
                    // Получаем media
                    if (strpos($tag, 'media=') !== false) {
                        preg_match('#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $medias);
                        $medias = explode(',', $medias[1]);
                        $media = array();
                        foreach ($medias as $elem) {
                            // $media[] = current(explode(' ',trim($elem),2));
                            $media[] = $elem;
                        }
                    } else {
                        //Не определенный - относится ко всем
                        $media = array('all');
                    }

                    if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                        //<link>
                        $url = current(explode('?', $source[2], 2));
                        $path = $this->getpath($url);

                        if ($path !== false && preg_match('#\.css$#', $path)) {
                            //Хороший link
                            $this->css[] = array($media, $path);
                        } else {
                            //Динамический link (.php или другой)
                            $tag = '';
                        }
                    } else {
                        // встроенный css может быть обернут в комментарий, поэтому восстанавливаем их
                        $tag = $this->restore_comments($tag);
                        preg_match('#<style.*>(.*)</style>#Usmi', $tag, $code);

                        // и повторно скрываем их
                        $tag = $this->hide_comments($tag);
                    }

                    //Удаляем оригинальные теги стилей
                    $this->content = str_replace($tag, '', $this->content);
                }
            }
            return true;
        }
        // Действительно, нет стилей?
        return false;
    }

    // Соединение и оптимизация CSS
    public function minify()
    {
        $conf = rg_minify_config();
        foreach ($this->css as $group) {
            list($media, $css) = $group;
            if (preg_match('#^INLINE;#', $css)) {
                //<style>
                $css = preg_replace('#^INLINE;#', '', $css);
                $css = $this->fixurls(ABSPATH . '/index.php', $css);
            } else {
                //<link>
                if ($css !== false && file_exists($css) && is_readable($css)) {
                    $css = $this->fixurls($css, file_get_contents($css));
                    $css = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $css);
                } else {
                    //Не удалось прочитать CSS. Может getpath не работает?
                    $css = '';
                }
            }

            foreach ($media as $elem) {
                if (!isset($this->csscode[$elem]))
                    $this->csscode[$elem] = '';
                $this->csscode[$elem] .= "\n/*FILESTART*/\n" . $css;
            }
        }

        // Проверка на дупликат кода
        $md5list = array();
        $tmpcss = $this->csscode;
        foreach ($tmpcss as $media => $code) {
            $md5sum = md5($code);
            $medianame = $media;
            foreach ($md5list as $med => $sum) {
                //Если же код
                if ($sum === $md5sum) {
                    //Добавить объединенной код
                    $medianame = $med . ', ' . $media;
                    $this->csscode[$medianame] = $code;
                    $md5list[$medianame] = $md5list[$med];
                    unset($this->csscode[$med], $this->csscode[$media]);
                    unset($md5list[$med]);
                }
            }
            $md5list[$medianame] = $md5sum;
        }
        unset($tmpcss);

        //Управление @imports, пока для управления рекурсивным импортом
        foreach ($this->csscode as &$thiscss) {
            // Флаг для запуска воссоздания импорта и переменная поддерживающая внешний импорт
            $fiximports = false;
            $external_imports = "";

            while (preg_match_all('#^(/*\s?)@import.*(?:;|$)#Um', $thiscss, $matches)) {
                foreach ($matches[0] as $import) {
                    $url = trim(preg_replace('#^.*((?:https?:|ftp:)?//.*\.css).*$#', '$1', trim($import)), " \t\n\r\0\x0B\"'");
                    $path = $this->getpath($url);
                    $import_ok = false;
                    if (file_exists($path) && is_readable($path)) {
                        $code = addcslashes($this->fixurls($path, file_get_contents($path)), "\\");
                        $code = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $code);
                        if (!empty($code)) {
                            $tmp_thiscss = preg_replace('#(/\*FILESTART\*/.*)' . preg_quote($import, '#') . '#Us', '/*FILESTART2*/' . $code . '$1', $thiscss);
                            if (!empty($tmp_thiscss)) {
                                $thiscss = $tmp_thiscss;
                                $import_ok = true;
                                unset($tmp_thiscss);
                            }
                            unset($code);
                        }
                    }

                    if (!$import_ok) {
                        // импорт внешних и общих fall-back
                        $external_imports .= $import;
                        $thiscss = str_replace($import, '', $thiscss);
                        $fiximports = true;
                    }
                }
                $thiscss = preg_replace('#/\*FILESTART\*/#', '', $thiscss);
                $thiscss = preg_replace('#/\*FILESTART2\*/#', '/*FILESTART*/', $thiscss);
            }

            // добавить внешние импорт в верхней части совокупного CSS
            if ($fiximports) {
                $thiscss = $external_imports . $thiscss;
            }
        }
        unset($thiscss);

        // $this->csscode имеет весь несжатый код сейчас.
        $mhtmlcount = 0;
        foreach ($this->csscode as &$code) {
            // Проверка для уже минифицированного кода
            $hash = md5($code);
            $ccheck = new minifyCache($hash, 'css');
            if ($ccheck->check()) {
                $code = $ccheck->retrieve();
                $this->hashmap[md5($code)] = $hash;
                continue;
            }
            unset($ccheck);

            // Делаем изображения
            $imgreplace = array();
            preg_match_all('#(background[^;}]*url\((?!data)(.*)\)[^;}]*)(?:;|$|})#Usm', $code, $matches);

            if (($this->datauris == true) && (function_exists('base64_encode')) && (is_array($matches))) {
                foreach ($matches[2] as $count => $quotedurl) {
                    $iurl = trim($quotedurl, " \t\n\r\0\x0B\"'");

                    // если querystring, удаляем его из url
                    if (strpos($iurl, '?') !== false) {
                        $iurl = reset(explode('?', $iurl));
                    }

                    $ipath = $this->getpath($iurl);

                    $datauri_max_size = 4096;

                    if (!empty($datauri_exclude)) {
                        $no_datauris = array_filter(array_map('trim', explode(",", $datauri_exclude)));
                        foreach ($no_datauris as $no_datauri) {
                            if (strpos($iurl, $no_datauri) !== false) {
                                $ipath = false;
                                break;
                            }
                        }
                    }

                    if ($ipath != false && preg_match('#\.(jpe?g|png|gif|bmp)$#', $ipath) && file_exists($ipath) && is_readable($ipath) && filesize($ipath) <= $datauri_max_size) {
                        $ihash = md5($ipath);
                        $icheck = new minifyCache($ihash, 'img');
                        if ($icheck->check()) {
                            // у нас есть base64 изображение в кэше
                            $headAndData = $icheck->retrieve();
                        } else {
                            // Это - изображение, и мы не имеем его в кэше, получаем тип
                            $explA = explode('.', $ipath);
                            $type = end($explA);

                            switch ($type) {
                                case 'jpeg':
                                    $dataurihead = 'data:image/jpeg;base64,';
                                    break;
                                case 'jpg':
                                    $dataurihead = 'data:image/jpeg;base64,';
                                    break;
                                case 'gif':
                                    $dataurihead = 'data:image/gif;base64,';
                                    break;
                                case 'png':
                                    $dataurihead = 'data:image/png;base64,';
                                    break;
                                case 'bmp':
                                    $dataurihead = 'data:image/bmp;base64,';
                                    break;
                                default:
                                    $dataurihead = 'data:application/octet-stream;base64,';
                            }

                            // Кодирование данных
                            $base64data = base64_encode(file_get_contents($ipath));
                            $headAndData = $dataurihead . $base64data;

                            // Сохранение в кэше
                            $icheck->cache($headAndData, "text/plain");
                        }
                        unset($icheck);

                        //Добавить в список для замены
                        $imgreplace[$matches[1][$count]] = str_replace($quotedurl, $headAndData, $matches[1][$count]) . ";\n*" . str_replace($quotedurl, 'mhtml:%%MHTML%%!' . $mhtmlcount, $matches[1][$count]) . ";\n_" . $matches[1][$count] . ';';

                        //Хранить изображение на документа mhtml
                        $this->mhtml .= "--_\r\nContent-Location:{$mhtmlcount}\r\nContent-Transfer-Encoding:base64\r\n\r\n{$base64data}\r\n";
                        $mhtmlcount++;
                    }
                }
            }

            if (!empty($imgreplace)) {
                $code = str_replace(array_keys($imgreplace), array_values($imgreplace), $code);
            }

            //Минификация
            if ($conf['css_do_minify']) {
                if (class_exists('Minify_CSS_Compressor')) {
                    $tmp_code = trim(Minify_CSS_Compressor::process($code));
                } else if (class_exists('CSSmin')) {
                    $cssmin = new CSSmin();
                    if (method_exists($cssmin, "run")) {
                        $tmp_code = trim($cssmin->run($code));
                    } elseif (@is_callable(array($cssmin, "minify"))) {
                        $tmp_code = trim(CssMin::minify($code));
                    }
                }

                if (!empty($tmp_code)) {
                    $code = $tmp_code;
                    unset($tmp_code);
                }
            }

            $this->hashmap[md5($code)] = $hash;
        }
        unset($code);
        return true;
    }

    //Кэширует CSS в несжатом, дефлированном и сжатом виде.
    public function cache()
    {
        $conf = rg_minify_config();
        if ($this->datauris) {
            // MHTML Подготовка
            $this->mhtml = "/*\r\nContent-Type: multipart/related; boundary=\"_\"\r\n\r\n" . $this->mhtml . "*/\r\n";
            $md5 = md5($this->mhtml);
            $cache = new minifyCache($md5, 'txt');
            if (!$cache->check()) {
                //Кэшировать изображения для IE
                $cache->cache($this->mhtml, 'text/plain');
            }
            $mhtml = $this->geturl($conf['cachedir']) . $cache->getname();
        }

        //CSS кэш
        foreach ($this->csscode as $media => $code) {
            $md5 = $this->hashmap[md5($code)];

            if ($this->datauris) {
                // Изображения для ie! Получить правильный url
                $code = str_replace('%%MHTML%%', $mhtml, $code);
            }

            $cache = new minifyCache($md5, 'css');
            if (!$cache->check()) {
                // Кэш кода
                $cache->cache($code, 'text/css');
            }
            $this->url[$media] = $this->geturl($conf['cachedir']) . $cache->getname();
        }
    }

    //Вовзращает контент
    public function getcontent()
    {
        // восстановление IE хаков
        $this->content = $this->restore_iehacks($this->content);

        // восстановление комментариев
        $this->content = $this->restore_comments($this->content);

        // восстановление noscript
        if (strpos($this->content, '%%NOSCRIPT%%') !== false) {
            $this->content = preg_replace_callback(
                '#%%NOSCRIPT%%(.*?)%%NOSCRIPT%%#is',
                create_function(
                    '$matches',
                    'return stripslashes(base64_decode($matches[1]));'
                ),
                $this->content
            );
        }

        // восстановление noptimize
        $this->content = $this->restore_noptimize($this->content);

        //Восстановить полное содержание
        if (!empty($this->restofcontent)) {
            $this->content .= $this->restofcontent;
            $this->restofcontent = '';
        }

        // Ввод новых таблиц стилей
        $replaceTag = array("<title", "before");

        if ($this->inline == true) {
            foreach ($this->csscode as $media => $code) {
                $this->inject_in_html('<style type="text/css" media="' . $media . '">' . $code . '</style>', $replaceTag);
            }
        } else {
            if ($this->defer == true) {
                $deferredCssBlock = "<script>function lCss(url,media) {var d=document;var l=d.createElement('link');l.rel='stylesheet';l.type='text/css';l.href=url;l.media=media; d.getElementsByTagName('head')[0].appendChild(l);}function deferredCSS() {";
                $noScriptCssBlock = "<noscript>";
                $defer_inline_code = $this->defer_inline;
                if (!empty($defer_inline_code)) {

                    $iCssHash = md5($defer_inline_code);
                    $iCssCache = new minifyCache($iCssHash, 'css');
                    if ($iCssCache->check()) {
                        // мы имеем оптимизированный встроенный CSS в кэше
                        $defer_inline_code = $iCssCache->retrieve();
                    } else {
                        if (class_exists('Minify_CSS_Compressor')) {
                            $tmp_code = trim(Minify_CSS_Compressor::process($this->defer_inline));
                        } else if (class_exists('CSSmin')) {
                            $cssmin = new CSSmin();
                            $tmp_code = trim($cssmin->run($defer_inline_code));
                        }

                        if (!empty($tmp_code)) {
                            $defer_inline_code = $tmp_code;
                            $iCssCache->cache($defer_inline_code, "text/css");
                            unset($tmp_code);
                        }
                    }
                    $code_out = '<style type="text/css" media="all">' . $defer_inline_code . '</style>';
                    $this->inject_in_html($code_out, $replaceTag);
                }
            }

            foreach ($this->url as $media => $url) {
                //Добавление любых отложенных стилей (import внизу) или нормальные links в head
                if ($this->defer == true) {
                    $deferredCssBlock .= "lCss('" . $url . "','" . $media . "');";
                    $noScriptCssBlock .= '<link type="text/css" media="' . $media . '" href="' . $url . '" rel="stylesheet" />';
                } else {
                    $this->inject_in_html('<link type="text/css" media="' . $media . '" href="' . $url . '" rel="stylesheet" />', $replaceTag);
                }
            }

            if ($this->defer == true) {
                $deferredCssBlock .= "}if(window.addEventListener){window.addEventListener('DOMContentLoaded',deferredCSS,false);}else{window.onload = deferredCSS;}</script>";
                $noScriptCssBlock .= "</noscript>";
                $this->inject_in_html($noScriptCssBlock, array('<title>', 'before'));
                $this->inject_in_html($deferredCssBlock, array('</body>', 'before'));
            }
        }

        //Возвращаем модифицированные стили
        return $this->content;
    }

    private function fixurls($file, $code)
    {
        $conf = rg_minify_config();
        $file = str_replace(getinfo('FCPATH'), '/', $file);
        $dir = dirname($file); //Like /wp-content

        // быстрый фикс для проблем импорта
        $code = preg_replace('#@import ("|\')(.+?)\.css("|\')#', '@import url("${2}.css")', $code);

        if (preg_match_all('#url\((?!data)(.*)\)#Usi', $code, $matches)) {
            $replace = array();
            foreach ($matches[1] as $k => $url) {
                // Удаление кавычек
                $url = trim($url, " \t\n\r\0\x0B\"'");
                $noQurl = trim($url, "\"'");
                if ($url !== $noQurl) {
                    $removedQuotes = true;
                } else {
                    $removedQuotes = false;
                }
                $url = $noQurl;
                if (substr($url, 0, 1) == '/' || preg_match('#^(https?://|ftp://|data:)#i', $url)) {
                    //URL абсолютный
                    continue;
                } else {
                    // относительный URL
                    $newurl = preg_replace('/https?:/', '', getinfo('site_url') . str_replace('//', '/', $dir . '/' . $url));
                    $hash = md5($url);
                    $code = str_replace($matches[0][$k], $hash, $code);

                    if (!empty($removedQuotes)) {
                        $replace[$hash] = 'url(\'' . $newurl . '\')';
                    } else {
                        $replace[$hash] = 'url(' . $newurl . ')';
                    }
                }
            }
            //Сделайте замену здесь, чтобы избежать поломки URL
            $code = str_replace(array_keys($replace), array_values($replace), $code);
        }
        return $code;
    }

    private function ismovable($tag)
    {
        if (is_array($this->dontmove)) {
            foreach ($this->dontmove as $match) {
                if (strpos($tag, $match) !== false) {
                    //Что-то совпало
                    return false;
                }
            }
        }

        //Если мы здесь, значит можно переместить
        return true;
    }

}
