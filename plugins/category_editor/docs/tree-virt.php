<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<h2>Виртуальные меню <strong style="color:red">(только для версии 3+)</strong></h2>
<p>Виртуальное меню позволяет создавать свое собственное меню неограниченной вложенности с поддержкой "drug and drop".</p>
<p>Элементы меню поддерживают следующие свойства:</p>
<ul>
<li><strong>Статус</strong> - публиковать или нет пункт меню;</li>
<li><strong>Ссылка</strong> - ссылка пункта меню. <strong>Указывать без домена!</strong>;</li>
<li><strong>Класс Li</strong> - класс элемента списка li (&lt;li class="КЛАСС"&gt;);</li>
<li><strong>Класс A</strong> - класс самой ссылки элемента меню;</li>
<li><strong>Атрибуты A</strong> - прочие атрибуты для ссылки (style="color:red" или другое);</li>
<li><strong>Связь</strong> - связь элемента виртуального меню с одной из категорий. Дочерние подкатегории привязанной категории станут частью меню. Указывается ID привязываемой категории. 0 - нет связи;</li>
</ul>

<p>Для того, чтобы отобразить меню на сайте, используйте функцию:</p> 
<p><code>ce_build_menu(2)</code>. Она принимает первый параметр - Id меню. Вы можете передать ей второй параметр - направление сортировки - "asc" или "desc"</p> 
<p><strong>ВАЖНО!</strong> Функция формирует список без главного UL. Вы его прописываете сами. Например - <code>echo  '&lt;ul&gt' . ce_build_menu('.$key.') . '&lt;/ul&gt';</code></p>

<h2>Связи</h2>
<p>Виртуальное меню поддерживает связи с категориями сайта. Достаточно при редактировании элемента виртуального меню указать ему ID категории, так при формировании меню на сайте он автоматически подключит подкатегории привязанной категории и включит их в список подкаталогов</p>
<p>Для конкретного элемента связанного меню (подкатегорий) можно указать свои свойства исключительно для виртуального меню. Перейдите во вкладку <a href="/admin/category_editor/setting">НАСТРОЙКА ПОЛЕЙ</a> и создайте новое текстовое поле со следующим ключем:</p>
<ul>
<li><strong>menu_css_li</strong> - значение этого ключа будет подставлено в класс элемента виртуального меню при выводе по связи (&lt;li class="КЛАСС"&gt;);</li>
<li><strong>menu_css_a</strong> - тоже самое, только для ссылки;</li>
<li><strong>menu_css_attr</strong> - тоже самое, только атрибуты ссылки</li>
</ul>
<p>Вы можете использовать все ключи одновременно</p>
<p>Например, вы связываете меню с ID = 1 (категория новости) с пунктом виртуального меню под названием <strong>"Пункт 3"</strong>. Привязанная категория имеет дочерние подкатегории - Мои новости, Новости сайта, Новости в мире.</p>
<p>Создайте во вкладке <a href="/admin/category_editor/setting">НАСТРОЙКА ПОЛЕЙ</a> простое тектовое поле с ключем <strong>menu_css_li</strong>. Назовем его <strong>Класс элемента</strong>. Сохранили изменения.</p>
<p>Дальше открываем для редактирования любую категорию из списка выше. И вписываем в поле класс, например <strong>my_class</strong></p>
<p>Допустим наше виртуальное меню имеет ID = 1. А класс для LI мы прописали к подкатегории <strong>"Новости сайта"</strong></p>
<pre>
...
// вставляем меню
echo  '&lt;ul&gt' . ce_build_menu('.$key.') . '&lt;/ul&gt';

</pre>
<p>Переходим на сайт, и видим там сформированное меню</p>
<pre>
&lt;ul&gt;
&lt;li&gt; Пункт 1 &lt;/li&gt;
&lt;li&gt; Пункт 2 &lt;/li&gt;
&lt;--теперь связанный пункт--&gt;
&lt;li&gt; Пункт 3 
	&lt;ul&gt;
    &lt;li&gt; Мои новости &lt;/li&gt;
    &lt;--вот тут наша настройка начинает работать--&gt;
    &lt;li class="my_class"&gt; Новости сайта &lt;/li&gt;
    &lt;li&gt; Новости в мире &lt;/li&gt;
    &lt;/ul&gt;


&lt;/li&gt;

&lt;/ul&gt;


</pre>