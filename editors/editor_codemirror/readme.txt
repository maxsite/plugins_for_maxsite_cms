textarea — невизуальный редактор HTML с подсветкой (подсвечивает HTML+CSS+JS). В качестве скрипта подсветки используется CodeMirror 0.67 (http://marijn.haverbeke.nl/codemirror/).
Версия — 0.2.
Работа плагина проверена в Firefox 3.6, Chrome 5, Safari 4, Opera 10.5, IE 8.

Для правильной работы редактора требуется отключить автобалансировку тегов. Для этого необходимо добавить в «index.php» темы веб-сайта:

	mso_remove_hook('content_auto_tag', 'mso_auto_tag');
	mso_remove_hook('content_balance_tags', 'mso_balance_tags');

Или же, если по каким-либо причинам изменения в шаблон внести не получается, необходимо в «/application/maxsite/common/page.php» закомментировать строки:

	$content = mso_hook('content_auto_tag', $content);
	$content = mso_hook('content_balance_tags', $content);