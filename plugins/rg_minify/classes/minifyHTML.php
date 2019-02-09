<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class minifyHTML extends minifyBase
{
    private $keepcomments = false;

    //Ничего не делает
    public function read()
    {
        $conf = rg_minify_config();
        //Не удалять HTML комментарии?
        $this->keepcomments = (bool)$conf['html_keepcomments'];

        return true;
    }

    //Соединение и оптимизация
    public function minify()
    {
        if (class_exists('Minify_HTML')) {
            // скрываем noptimize тэги
            $this->content = $this->hide_noptimize($this->content);

            // Минификация HTML
            $options = array('keepComments' => $this->keepcomments);

            if (@is_callable(array(new Minify_HTML, "minify"))) {
                $tmp_content = Minify_HTML::minify($this->content, $options);
                if (!empty($tmp_content)) {
                    $this->content = $tmp_content;
                    unset($tmp_content);
                }
            }

            // восстанваливаем noptimize тэги
            $this->content = $this->restore_noptimize($this->content);
            return true;
        }

        //Нет минификации, класс не найден :(
        return false;
    }

    //Ничего не делает
    public function cache()
    {
        //Не использовать кэш для HTML
        return true;
    }

    //Возвращает содержимое
    public function getcontent()
    {
        return $this->content;
    }
}
