<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class minifyCache
{
    private $filename;
    private $mime;
    private $cachedir;
    private $delayed;

    public function __construct($md5, $ext = 'php')
    {
        $conf = rg_minify_config();
        $this->cachedir = $conf['cachedir'];
        $this->delayed = $conf['cachedelay'];
        $this->nogzip = $conf['nogzip'];
        if ($this->nogzip == false) {
            $this->filename = 'minify_' . $md5 . '.php';
        } else {
            if (in_array($ext, array("js", "css"))) {
                $this->filename = $ext . '/minify_' . $md5 . '.' . $ext;
            } else {
                $this->filename = '/minify_' . $md5 . '.' . $ext;
            }
        }
    }

    public function check()
    {
        if (!file_exists($this->cachedir . $this->filename)) {
            // Файо кэша не найден
            return false;
        }
        // Кэш найден!
        return true;
    }

    public function retrieve()
    {
        if ($this->check()) {
            if ($this->nogzip == false) {
                return file_get_contents($this->cachedir . $this->filename . '.none');
            } else {
                return file_get_contents($this->cachedir . $this->filename);
            }
        }
        return false;
    }

    public function cache($code, $mime)
    {
        if ($this->nogzip == false) {
            $file = ($this->delayed ? 'delayed.php' : 'default.php');
            $phpcode = file_get_contents(getinfo('plugins_dir') . 'rg_minify/config/' . $file);
            $phpcode = str_replace(array('%%CONTENT%%', 'exit;'), array($mime, ''), $phpcode);
            file_put_contents($this->cachedir . $this->filename, $phpcode);
            file_put_contents($this->cachedir . $this->filename . '.none', $code);
            if (!$this->delayed) {
                // Сжимаем сейчас!
                file_put_contents($this->cachedir . $this->filename . '.deflate', gzencode($code, 9, FORCE_DEFLATE));
                file_put_contents($this->cachedir . $this->filename . '.gzip', gzencode($code, 9, FORCE_GZIP));
            }
        } else {
            // Запишем код в кэш, не делая ничего
            file_put_contents($this->cachedir . $this->filename, $code);
        }
    }

    public function getname()
    {
        return $this->filename;
    }

    static function clearall()
    {
        if (!minifyCache::cacheavail()) {
            return false;
        }

        $conf = rg_minify_config();

        // сканирование директории кэша
        foreach (array("", "js", "css") as $scandirName) {
            $scan[$scandirName] = scandir($conf['cachedir'] . $scandirName);
        }

        // очистка директории кэша
        foreach ($scan as $scandirName => $scanneddir) {
            $thisAoCacheDir = rtrim($conf['cachedir'] . $scandirName, "/") . "/";
            foreach ($scanneddir as $file) {
                if (!in_array($file, array('.', '..')) && strpos($file, 'minify') !== false && is_file($thisAoCacheDir . $file)) {
                    @unlink($thisAoCacheDir . $file);
                }
            }
        }

        @unlink($conf['cachedir'] . "/.htaccess");

        // Нужно ли нам очищать кэш плагинов
        //$show_clear_cache = false;
        mso_flush_cache(); // сбросим кэш
        return true;
    }

    static function stats()
    {
        // кэш не доступен :(
        if (!minifyCache::cacheavail()) {
            return 0;
        }
        $conf = rg_minify_config();

        // количество данных в кэше
        $count = 0;

        // сканирование директории кэша
        foreach (array("", "js", "css") as $scandirName) {
            $scan[$scandirName] = scandir($conf['cachedir'] . $scandirName);
        }

        foreach ($scan as $scandirName => $scanneddir) {
            $thisAoCacheDir = rtrim($conf['cachedir'] . $scandirName, "/") . "/";
            foreach ($scanneddir as $file) {
                if (!in_array($file, array('.', '..')) && strpos($file, 'minify') !== false) {
                    if (is_file($thisAoCacheDir . $file)) {
                        if ($conf['nogzip'] && (strpos($file, '.js') !== false || strpos($file, '.css') !== false)) {
                            $count++;
                        } elseif (!$conf['nogzip'] && strpos($file, '.none') !== false) {
                            $count++;
                        }
                    }
                }
            }
        }

        // вывести количество экземпляров
        return $count;
    }

    static function cacheavail()
    {
        $conf = rg_minify_config();

        if (empty($conf['cachedir'])) {
            // Мы не включали кэширование
            return false;
        }

        foreach (array("", "js", "css") as $checkDir) {
            if (!minifyCache::checkCacheDir($conf['cachedir'] . $checkDir)) {
                return false;
            }
        }

        /** write index.html here to avoid prying eyes */
        $indexFile = $conf['cachedir'] . '/index.html';
        if (!is_file($indexFile)) {
            @file_put_contents($indexFile, '<html><body>Generated by <a href="http://rgblog.ru/">RG-Minify</a></body></html>');
        }
        /** write .htaccess here to overrule wp_super_cache */
        $htAccess = $conf['cachedir'] . '/.htaccess';
        if (!is_file($htAccess)) {
            if ($conf['nogzip'] == false) {

                @file_put_contents($htAccess, '<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
        <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all granted
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order allow,deny
        Allow from all
    </Files>
</IfModule>');
            } else {
                @file_put_contents($htAccess, '<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
        <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all denied
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order deny,allow
        Deny from all
    </Files>
</IfModule>');
            }
        }
        // Все хорошо
        return true;
    }

    static function checkCacheDir($dir)
    {
        // Проверить и создать, если не существует
        if (!file_exists($dir)) {
            @mkdir($dir, 0775, true);
            if (!file_exists($dir)) {
                return false;
            }
        }

        // Проверка на запись в директорию
        if (!is_writable($dir)) {
            return false;
        }

        // и создаем index.html чтобы избежать любопытных глаз
        $indexFile = $dir . '/index.html';
        if (!is_file($indexFile)) {
            @file_put_contents($indexFile, '<html><body>Generated by <a href="http://rgblog.ru/">RG-Minify</a></body></html>');
        }

        return true;
    }
}