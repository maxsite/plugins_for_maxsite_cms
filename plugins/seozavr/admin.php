<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Seozavr
 * (c) http://maxsitecms.ru/
 */

  global $MSO;
  $CI = & get_instance();
  
  $options_key = 'seozavr';
  
  if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_kod', 'f_cat_link', 'f_own_title')) )
  {
    mso_checkreferer();
    
    $options = array();
    $options['kod'] = $post['f_kod'];
    $options['cat_link'] = $post['f_cat_link'];
    $options['own_title'] = $post['f_own_title'];
    
    $options['go'] = 0; // признак, что код установлен верно - каталог есть и доступен для записи
    
    // проверим введенный код
    $fn = $_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'] . '/seozavr.php';
    
    if (!file_exists($fn)) // нет файла, просто выведем предупреждение
    {
      echo '<div class="error">Введенный вам код, возможно неправильный, или вы не распаковали архив на сервере!</div>';
    }
    else // есть файл, проверим что каталог доступен на запись
    {
      if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'] . '/db.txt'))
        echo '<div class="error">Файл db.txt недоступен для записи. Установите для него права 777 (разрешающие запись).</div>';
      else
        $options['go'] = 1; // нет ошибок
    }
    
    $options['start'] = isset($post['f_start']) ? 1 : 0;
    $options['no_part_link'] = isset($post['f_no_part_link']) ? 1 : 0;
    $options['leave_del_art'] = isset($post['f_leave_del_art']) ? 1 : 0;

    mso_add_option($options_key, $options, 'plugins');
    echo '<div class="update">Настройки обновлены!</div>';
  }
  
?>
<h1>Настройка seozavr.ru</h1>
<p>С помощью этой страницы вы можете настроить свою работу с <a href="http://seozavr.ru/index.php?id=19439" target="_blank">seozavr.ru</a>. Перед началом работы вам следует выполнить следующие действия:</p>
<ol>
<li>Скачать с <a href="http://seozavr.ru/index.php?id=19439" target="_blank">seozavr.ru</a> архив с вашим кодом для загрузки на сервер.
<li>Распаковать архив. Внутри него будет лежать папка с именем вроде такого: «331a666697e2aca44a359c7ace1f5a0bccdcbe99».
<li>Загрузите эту папку на ваш сервер в корень(!!!) вашего сайта.
<li>Установите права доступа 777 на файл db.txt внутри этой папки.
<li>Создайте страницу типа static, отключите комментирование и публикацию в рсс. Укажите заголовок (например <b>Статьи</b>). В текст страницы добавьте тэг <b>[seozavr]</b>. Введите короткую ссылку для страницы (Например <b>articles</b>). Эта страница будет главной страницей каталога статей и она будет иметь адрес <b>http://вашсайт.ru/короткая_ссылка</b>.
</ol>
<br/>
<p><strong>Только после этого вы можете выполнить настройки на этой странице!</strong></p>
<ol>
<li>Укажите свой код (он совпадает с именем папки).
<li>Укажите короткую ссылку страницы с каталогом .
<li>Укажите свой заголовок для каталога (если необходимо).
<li>Поставьте ссылку с главной страницы на главную страницу каталога статей (Или добавьте новый пункт верхнего меню в настройках шаблона).
</ol>
<br/>
<p>Вывести ссылки на статьи на главную страницу, Вы можете двумя способами:</p>
<ol>
<li>С помощью тега <b>[seozavr_list]</b> в тексте любой записи, находящейся на главной страницы
<li>Через виджет text_block, указав тип PHP и вставив следующий код:
<pre>

 &lt;?php if (function_exists('seozavr_out')) echo(seozavr_out()); ?&gt;

</pre>
</ol>


<p><strong>Обратите внимание! Помощь по установке кода <a href="http://seozavr.ru/index.php?id=19439" target="_blank">seozavr.ru</a>, любые подсказки и разъяснения пр этому поводу я оказываю только на платной основе - 30WMZ. Форма для связи: <a href="http://maxsitecms.ru/contact">http://maxsitecms.ru/contact</a></strong></p>
<br />

<?php
    $options = mso_get_option($options_key, 'plugins', array());
    if ( !isset($options['kod']) ) $options['kod'] = ''; 
    if ( !isset($options['cat_link']) ) $options['cat_link'] = '';
    if ( !isset($options['no_part_link']) ) $options['no_part_link'] = true;
    if ( !isset($options['start']) ) $options['start'] = true; 
    if ( !isset($options['own_title']) ) $options['own_title'] = '';
    if ( !isset($options['leave_del_art']) ) $options['leave_del_art'] = false;
    
    $checked_context = $options['no_part_link'] ? ' checked="checked" ' : '';
    $checked_start = $options['start'] ? ' checked="checked" ' : '';
    $checked_anticheck = $options['leave_del_art'] ? ' checked="checked" ' : '';
    
    $form = '';
    $form .= '<p><strong>Ваш номер/код в <a href="http://seozavr.ru/index.php?id=19439" target="_blank">seozavr.ru</a>:</strong> ' . ' <input name="f_kod" type="text" style="width: 300px;" value="' . $options['kod'] . '"></p>';
    $form .= '<p><strong>Короткая ссылка страницы с каталогом</strong> ' . ' <input name="f_cat_link" type="text" style="width: 300px;" value="' . $options['cat_link'] . '"></p>';
    $form .= '<p><strong>Свой заголовок для каталога</strong> ' . ' <input name="f_own_title" type="text" style="width: 300px;" value="' . $options['own_title'] . '"></p>';

    $form .= '<p><input name="f_start" type="checkbox"' . $checked_start . '> Включить плагин</p>';
    $form .= '<p><input name="f_no_part_link" type="checkbox"' . $checked_context . '> Не выводить партнерскую ссылку на seozavr.ru из каталога статей</p>';
    $form .= '<p><input name="f_leave_del_art" type="checkbox"' . $checked_anticheck . '> Не показывать удаленные статьи</p>';
    
    echo '<form action="" method="post">' . mso_form_session('f_session_id');
    echo $form;
    echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
    echo '</form>';

?>
