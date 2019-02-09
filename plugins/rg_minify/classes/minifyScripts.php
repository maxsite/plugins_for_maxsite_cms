<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class minifyScripts extends minifyBase
{
    private $scripts = array();
    private $dontmove = array('document.write', 'html5.js', 'show_ads.js', 'google_ad', 'blogcatalog.com/w', 'tweetmeme.com/i', 'mybloglog.com/', 'histats.com/js', 'ads.smowtion.com/ad.js', 'statcounter.com/counter/counter.js', 'widgets.amung.us', 'ws.amazon.com/widgets', 'media.fastclick.net', '/ads/', 'comment-form-quicktags/quicktags.php', 'edToolbar', 'intensedebate.com', 'scripts.chitika.net/', '_gaq.push', 'jotform.com/', 'admin-bar.min.js', 'GoogleAnalyticsObject', 'plupload.full.min.js', 'syntaxhighlighter', 'adsbygoogle', 'potentialAction');
    private $domove = array('gaJsHost', 'load_cmc', 'jd.gallery.transitions.js', 'swfobject.embedSWF(', 'tiny_mce.js', 'tinyMCEPreInit.go');
    private $domovelast = array('addthis.com', '/afsonline/show_afs_search.js', 'disqus.js', 'networkedblogs.com/getnetworkwidget', 'infolinks.com/js/', 'jd.gallery.js.php', 'jd.gallery.transitions.js', 'swfobject.embedSWF(', 'linkwithin.com/widget.js', 'tiny_mce.js', 'tinyMCEPreInit.go');
    private $trycatch = false;
    private $forcehead = false;
    private $jscode = '';
    private $url = '';
    private $move = array('first' => array(), 'last' => array());
    private $restofcontent = '';
    private $md5hash = '';

    //Чтение страницы и сбор тегов скриптов
    public function read()
    {
        $conf = rg_minify_config();

        //Удалим все, что не в заголовке
        if ($conf['js_justhead'] == true) {
            $content = explode('</head>', $this->content, 2);
            $this->content = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }

        $excludeJS = $conf['js_exclude'];

        if ($excludeJS !== "") {
            $exclJSArr = array_filter(array_map('trim', explode(",", $excludeJS)));
            $this->dontmove = array_merge($exclJSArr, $this->dontmove);
        }

        //Использовать ли try-catch?
        if ($conf['js_trycatch'] == true)
            $this->trycatch = true;

        // вызывать js в head?
        if ($conf['js_forcehead'] == true)
            $this->forcehead = true;

        // noptimize me
        $this->content = $this->hide_noptimize($this->content);

        // Сохранить IE хаки
        $this->content = $this->hide_iehacks($this->content);

        // комментарии
        $this->content = $this->hide_comments($this->content);

        //Получить файлы скриптов
        if (preg_match_all('#<script.*</script>#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $tag) {
                if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                    //Внешний скрипт
                    $url = current(explode('?', $source[2], 2));
                    $path = $this->getpath($url);
                    if ($path !== false && preg_match('#\.js$#', $path)) {
                        //Встроенный скрипт
                        if ($this->ismergeable($tag)) {
                            //Мы можем объединить это
                            $this->scripts[] = $path;
                        } else {
                            //Не объединяем, но можем переместить
                            if ($this->ismovable($tag)) {
                                //Да, переместим его
                                if ($this->movetolast($tag)) {
                                    $this->move['last'][] = $tag;
                                } else {
                                    $this->move['first'][] = $tag;
                                }
                            } else {
                                //Мы не трогаем это
                                $tag = '';
                            }
                        }
                    } else {
                        //Внешний скрипт (пример: google analytics)
                        //ИЛИ Скрипт динамический (.php или другие)
                        if ($this->ismovable($tag)) {
                            if ($this->movetolast($tag)) {
                                $this->move['last'][] = $tag;
                            } else {
                                $this->move['first'][] = $tag;
                            }
                        } else {
                            //Мы не трогаем это
                            $tag = '';
                        }
                    }
                } else {
                    // Встроенный скрипт
                    // показать комментарии, поскольку js может быть обернут в теги комментария как в старые добрые времена
                    $tag = $this->restore_comments($tag);
                    if ($this->ismergeable($tag) && ($conf['js_include_inline'])) {
                        preg_match('#<script.*>(.*)</script>#Usmi', $tag, $code);
                        $code = preg_replace('#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm', '$1', $code[1]);
                        $code = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $code);
                        $this->scripts[] = 'INLINE;' . $code;
                    } else {
                        //Мы можем переместить это?
                        if ($this->ismovable($tag)) {
                            if ($this->movetolast($tag)) {
                                $this->move['last'][] = $tag;
                            } else {
                                $this->move['first'][] = $tag;
                            }
                        } else {
                            //Мы не трогаем это
                            $tag = '';
                        }
                    }
                    // повторно скрываем комментарии, чтобы бы можно было удалить основываясь на тэге из $this->content
                    $tag = $this->hide_comments($tag);
                }

                //Удаляем оригинальный скрипт тэг
                $this->content = str_replace($tag, '', $this->content);
            }

            return true;
        }

        // Нет скриптов, великолепно ;-)
        return false;
    }

    //Объединение и оптимизация JS
    public function minify()
    {
        $conf = rg_minify_config();
        foreach ($this->scripts as $script) {
            if (preg_match('#^INLINE;#', $script)) {
                //Встроенный скрипт
                $script = preg_replace('#^INLINE;#', '', $script);
                $script = rtrim($script, ";\n\t\r") . ';';
                //Добавить try-catch?
                if ($this->trycatch) {
                    $script = 'try{' . $script . '}catch(e){}';
                }
                $this->jscode .= "\n" . $script;
            } else {
                //Внешний скрипт
                if ($script !== false && file_exists($script) && is_readable($script)) {
                    $script = file_get_contents($script);
                    $script = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $script);
                    $script = rtrim($script, ";\n\t\r") . ';';
                    //Добавить try-catch?
                    if ($this->trycatch) {
                        $script = 'try{' . $script . '}catch(e){}';
                    }
                    $this->jscode .= "\n" . $script;
                }
                /*else{
                                    //Не удалось прочитать JS. Может getpath не работает?
                                }*/
            }
        }

        //Проверка для уже минифицированного кода
        $this->md5hash = md5($this->jscode);
        $ccheck = new minifyCache($this->md5hash, 'js');
        if ($ccheck->check()) {
            $this->jscode = $ccheck->retrieve();
            return true;
        }
        unset($ccheck);

        //$this->jscode имеет весь не сжатый код сейчас.
        if (class_exists('JSMin') && $conf['js_do_minify']) {
            if (@is_callable(array(new JSMin, "minify"))) {
                $tmp_jscode = trim(JSMin::minify($this->jscode));
                if (!empty($tmp_jscode)) {
                    $this->jscode = $tmp_jscode;
                    unset($tmp_jscode);
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Кэширует JS в несжатом, deflated и сжатом виде.
    public function cache()
    {
        $conf = rg_minify_config();
        $cache = new minifyCache($this->md5hash, 'js');
        if (!$cache->check()) {
            //Кэш нашего кода
            $cache->cache($this->jscode, 'text/javascript');
        }
        $this->url = $this->geturl($conf['cachedir']) . $cache->getname();
    }

    // Возвращает содержимое
    public function getcontent()
    {
        // Восстановить полное содержимое
        if (!empty($this->restofcontent)) {
            $this->content .= $this->restofcontent;
            $this->restofcontent = '';
        }

        // Добавить взятые скрипты в шапку или вниз страницы
        if ($this->forcehead == true) {
            $replaceTag = array("</title>", "after");
            $defer = "";
        } else {
            $replaceTag = array("</body>", "before");
            $defer = "defer ";
        }

        $bodyreplacement = implode('', $this->move['first']);
        $bodyreplacement .= '<script type="text/javascript" ' . $defer . 'src="' . $this->url . '"></script>';
        $bodyreplacement .= implode('', $this->move['last']);

        $this->inject_in_html($bodyreplacement, $replaceTag);

        // восстановить комментарии
        $this->content = $this->restore_comments($this->content);

        // восстановить IE хаки
        $this->content = $this->restore_iehacks($this->content);

        // восстановить noptimize
        $this->content = $this->restore_noptimize($this->content);

        // Возвращаем модифицированный HTML
        return $this->content;
    }

    //Проверка в белом списке
    private function ismergeable($tag)
    {
        foreach ($this->domove as $match) {
            if (strpos($tag, $match) !== false) {
                //Совпало что-то
                return false;
            }
        }

        if ($this->movetolast($tag)) {
            return false;
        }

        foreach ($this->dontmove as $match) {
            if (strpos($tag, $match) !== false) {
                //Совпало что-то
                return false;
            }
        }

        //Если мы здесь, то можно объеденить
        return true;
    }

    //Проверка в черном списке
    private function ismovable($tag)
    {
        foreach ($this->domove as $match) {
            if (strpos($tag, $match) !== false) {
                //Совпало что-то
                return true;
            }
        }

        if ($this->movetolast($tag)) {
            return true;
        }

        foreach ($this->dontmove as $match) {
            if (strpos($tag, $match) !== false) {
                //Совпало что-то
                return false;
            }
        }

        //Если мы здесь, то можно переместить
        return true;
    }

    private function movetolast($tag)
    {
        foreach ($this->domovelast as $match) {
            if (strpos($tag, $match) !== false) {
                //Совпало что-то
                return true;
            }
        }

        //Должен быть в 'first'
        return false;
    }
}
