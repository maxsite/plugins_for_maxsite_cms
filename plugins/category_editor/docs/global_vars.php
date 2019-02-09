<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<h2>Глобальные значения <strong style="color:red">(только для версии 3+)</strong></h2>

<p>Созданы упростить обслуживание большого количества категорий. Представляют собой некие "переменные", хранящие различные значения.</p>
<p>Для создания глобального значения, перейдите во вкладку <a href="/admin/category_editor/global">Глобальные параметры</a>, заполните форму и назмите сохранить. В форме следующие поля - <strong>Ключ</strong>, по которому значение будет вызываться, <strong>Значение</strong> - само значение ключа и описание, которое служит для пояснения к значению.</p>

<h2>Как использовать</h2>
<p>Допустим, у нас 50 категорий. Каждая категория имеет значение, например, <strong>лимит страниц (ключ - limit)</strong>. У всех 50-ти категорий значение <strong>лимита</strong> одинаковое. Поменять у 50-ти категорий значение бывает достаточно долго, поэтому мы создаем <strong>Глобальное значение</strong>:</p>
<pre>
// Ключ
limit_page_global

// Значение
25

// Описание
Наш лимит страниц для 50-ти категорий
</pre>
<p>Теперь на странице редактировани <a href="/admin/category_editor/edit/1">странице редактирования категории</a> в поле <strong>Лимит страниц (ключ - limit)</strong> вместо значения <strong>25</strong> указываем <code>{limit_page_global}</code> <strong>(фигурные скобки обязательны!)</strong>.</p>
<p>Теперь при получения значения ключа <strong>limit</strong> выражение <code>CategoryEditor::getInstance()->limit</code> вернет нам не 
<strong>{limit_page_global}</strong>, а сохраненное для него значение <strong>25</strong>.</p>
<p>Таким образом, меняя значение глобального параметра <strong>limit_page_global</strong> мы меняем значение у всех категорий, которым оно присвоено.</p>