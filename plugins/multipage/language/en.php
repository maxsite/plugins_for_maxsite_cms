<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Language for MaxSite CMS (c) http://max-3000.com/
 * Multipage plugin
 * Author: (c) Wave
 * Author URL: http://wave.fantregata.com/
 * Update URL: http://wave.fantregata.com/page/work-for-maxsite
 */

$lang['Multipage'] = 'Multipage';
$lang['Плагин разбивает длинные тексты постов на несколько страниц. Разделитель задаётся в настройках. Для вывода навигации нужен плагин типа Pagination.'] = 'Plugin breaks up long texts of posts on a several pages. A delimiter is set in options. For the output of navigation needed plugin like «Pagination».';

$lang['Разделитель страниц'] = 'Delimiter of pages';
$lang['Разделитель страниц в тексте: [pagebreak], &lt;!-- Page break --&gt; или как вам будет угодно.'] = 'Delimiter of pages in text: [pagebreak], &lt;!-- Page break --&gt; or as you wish.';
$lang['«Next» в ссылках'] = '«Next» in urls';
$lang['«Next» в ссылках http://site.com/page/slug/next/2 — например: next, page, pageid.'] = '«Next» in urls http://site.com/page/slug/next/2 — example: next, page, pageid.';
$lang['Обрабатывать тексты на главной, в категориях и т.п.'] = 'Process texts on home, in categories etc.';
$lang['Если не обрабатывать, тексты выводятся только до первого разделителя. Иначе разделитель нужно ставить после [cut] или в виде html-комментария.<br>Не обрабатывать — экономней по ресурсам.'] = 'If not to process, texts hatch only to the first delimiter. Otherwise a delimiter needs to be put after [cut] or in a kind html-comment.<br>Not to process — economy on resources.';
$lang['0||Не обрабатывать # 1||Удалять разделители # 2||Выводить до первого разделителя'] = '0||Do not process # 1||Delete delimiters # 2||Show before first delimiter';
$lang['Автоматически закрывать теги на страницах'] = 'Automaticaly close tags on pages';
$lang['Плагин сам закрывает те теги, которые разбивает разделитель, и тем самым спасает от глюков с сайдбарами и т.п.. Экономней делать это вручную, а опцию отключить.'] = 'Plugin close tags, breaked by [pagebreak]. This prevert bugs with sidebars, etc.. You can do it manualy and disable this option.';
$lang['Показывать меню настройки плагина в админке'] = 'Show menu of options of this plugin in admin area of site';
$lang['Выводить листалку над текстом'] = 'Show pagination before page';
$lang['Выводить листалку под текстом'] = 'Show pagination after page';
$lang['Текст перед листалкой'] = 'Text before pagination';
$lang['Если вы хотите предварить листалку текстом или обернуть в какие-то теги.'] = 'If you want to anticipate pagination text or to turn in some tags.';
$lang['Текст после листалки'] = 'Text after pagination';
$lang['А здесь теги закрываются.'] = 'Here tags closed.';
$lang['Настройки плагина «Multipage»'] = 'Options of plugin «Multipage»';
$lang['Укажите необходимые опции.'] = 'Store your options.';

?>