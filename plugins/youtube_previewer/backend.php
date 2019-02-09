<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Youtube_Previewer» for MaxSite CMS
 *
 * Author: (c) Илья Земсков http://vizr.ru/
 */

require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-menu.php' ); # подключаем файл вывода меню

?>
<h1><?= t('Поиск и заполнение превью-поля записей') ?></h1>
<p class="info"><?= t('После нажатия одной из поисковых кнопок будет запущен процесс поиска записей, у которых не заполнено мета-поле превью-картинки и в теле записи присутствует код вставки ролика с Youtube.com. Для найденных записей с Youtube будет скачана превью-картинка ролика. Файл картинки будет сохранён в uploads-папку записи, после чего будет заполнено соответствующее мета-поле записи.') ?></p>

<form class="fform">
	<button class="button i-refresh"><?= t('Начать поиск всех картинок'); ?></button> <button class="button i-image one"><?= t('Найти одну картинку'); ?></button>
</form>
<div class="results"></div>


