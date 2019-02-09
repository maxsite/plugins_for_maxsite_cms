<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	if ( mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found');

	mso_cur_dir_lang('templates');

	require(getinfo('template_dir') . 'main-start.php');

	echo NR . '<div class="type type_page_404">' . NR;


	if ($f = mso_page_foreach('page_404'))
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . t('404 - несуществующая страница') . '</h1>';
		echo '<p>' . t('Извините по вашему запросу ничего не найдено!') . '</p>';
		echo mso_hook('page_404');
	}

	echo NR . '</div><!-- class="type type_page_404" -->' . NR;

	require(getinfo('template_dir') . 'main-end.php');

?>