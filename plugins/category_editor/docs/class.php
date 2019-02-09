<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<h2>Использование значений категории</h2>

<p>Для организации доступа к сохраненным значениям параметров плагин имеет класс <strong>CategoryEditor</strong>. Класс автоматически инициализируется во время загрузки страницы категории (когда <strong>mso_segment(2) == 'category'</strong>). Класс организован в виде Singleton (Одиночка), поэтому значения загружаются только один раз и остаются прежними до конца загрузки страницы сайта.</p>

<p>Получение параметра организовано следующим образом: <strong>CategoryEditor::getInstance()->ПАРАМЕТР;</strong>. Вы можете определить класс в переменную следующим образом:
<pre>
$Category = CategoryEditor::getInstance();
// первый параметр
$par = $Category->ПАРАМЕТР1;
// второй
$par_2 = $Category->ПАРАМЕТР2;

// и т.д
</pre>

</p>



<p>После инициализации становятся доступными следующие параметры категории:</p>
<ul>
<li><em>Основные базовые свойства</em></li>
<li><code>$par = CategoryEditor::getInstance()->_parent;</code> - возвращает значение поля <strong>category_id_parent</strong> из таблицы category;</li>
<li><code>$par = CategoryEditor::getInstance()->_type;</code> - возвращает значение поля <strong>category_type</strong> из таблицы category;</li>
<li><code>$par = CategoryEditor::getInstance()->_name;</code> - возвращает значение поля <strong>category_name</strong> из таблицы category;</li>
<li><code>$par = CategoryEditor::getInstance()->_desc;</code> - возвращает значение поля <strong>category_desc</strong> из таблицы category;</li>
<li><code>$par = CategoryEditor::getInstance()->_slug;</code> - возвращает значение поля <strong>category_slug</strong> из таблицы category;</li>
<li><code>$par = CategoryEditor::getInstance()->_menu_order;</code> - возвращает значение поля <strong>category_menu_order</strong> из таблицы category;</li>

<li><em>Прочие базовый свойства</em></li>
<li><code>$par = CategoryEditor::getInstance()->title;</code> - возвращает значение <strong>title</strong> категории;</li>
<li><code>$par = CategoryEditor::getInstance()->description;</code> - возвращает значение <strong>description</strong> категории;</li>
<li><code>$par = CategoryEditor::getInstance()->keywords;</code> - возвращает значение <strong>keywords</strong> категории;</li>
<li><code>$par = CategoryEditor::getInstance()->template;</code> - возвращает формат отображения категории(left-sidebar, no-sidebar...);</li>

</ul>

<p>После того, как вы создали <a href="/admin/category_editor/setting">свои поля</a>, они становятся доступными через класс <strong>CategoryEditor</strong>. Например, вы создали тестовое поле с ключем <strong>note_info</strong>. После этого, на <a href="/admin/category_editor/edit/1">странице редактирования категории</a> появится соответствующее поле.</p>
<p>На сайте это значение можно получить следующим образом: <code>$my_value = CategoryEditor::getInstance()->note_info;</code></p>

<h2>Свойства других категорий</h2>
<p>Бывает ситуация, когда необходимо получить свойства других категорий. Для этого класс содержит метод <code>$res = CategoryEditor::getInstance()->categoryes_values($cats_id);</code>. Здесь <code>$cats_id</code> - массив ID категорий (<em>array(1,2,3...15,20)</em>), свойства которых требуется получить.</p>
<p>Если вы хотите перезаписать загруженные значения текущей категории на значения другой, используйте <code>CategoryEditor::getInstance()->load = $category_id;</code> или <code>CategoryEditor::getInstance()->load_from_slug = $category_slug;</code>. Здесь <code>$category_id</code>, <code>$category_slug</code> - ID или Slug загружаемой категории соответственно.</p>
<p><strong>ВАЖНО!</strong> - использование методов <strong>CategoryEditor::getInstance()->load</strong> и <strong>CategoryEditor::getInstance()->load_from_slug</strong> проводит к перезаписи всех загруженных параметров. Иными словами, если вы находитесь на странице одной категории, а загрузите другую, то параметры первой станут недоступны! Для загрузки свойств текущей категории обратно, используйте те же методы, но предайте в него <strong>ID</strong> или <strong>Slug</strong> текущей категории.</p>

<p>Посмотреть загруженные параметры можно, использую метод <code>$res = CategoryEditor::getInstance()->all_params();</code></p>



