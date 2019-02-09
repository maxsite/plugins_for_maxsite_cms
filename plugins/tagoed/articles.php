<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<h1><?= t('ТегоРедактор', __FILE__) ?></h1>
<p class="info"><?= t('Небольшой редактор тегов', __FILE__) ?></p>


<?php

//Отображаем список статей
if (!isset($articleList))
{
	//Получаем список статей
	$articleList = tagoed_article_list_all();
}
//Открываем форму
require_once('article_start.tpl');
//Выводим их в цикле
foreach($articleList as $id => $article)
{
	require('article_element.tpl');
}
//Закрываем форму
require_once('article_end.tpl');

?>