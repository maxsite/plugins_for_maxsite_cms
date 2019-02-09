<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
// Форма добавления ссылки

  
	echo '<div class="links_form">';
  echo '<H3>' . 'Публикация нового сайта в этот раздел.' . '</H3>';
  echo '<p>' . 'Каталог является предмодерируемым и описание вашего сайта появится только после одобрения модератором.' . '</p>';
  echo '<p>' . 'Также, ваш сайт будет добавлен в систему социального поиска FasQu по указанным вами меткам.' . '</p>';
  echo '<p>' . 'Одобрены будут только нормальные описания сайтов.' . '</p>';
  echo '<p>' . 'Если термин "нормальные описания" непонятен - прочтите <a href="http://fasqu.com/help/recomendations.php?p=13658310" target="_blank">рекомендации по созданию описаний<a>.' . '</p>';	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '<table style="width: 100%;">';
	
	if (isset($postargs['url']) and $postargs['url']) $value = 'value="' . $postargs['url'] . '"'; else $value='';
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Адрес сайта', __FILE__) . '</strong> </td><td class="td2">';
	echo '<input name="f_url" type="text" ' . $value . 'style="width: 99%;"></td></tr>';

	if (isset($postargs['title']) and $postargs['title']) $value = 'value="' . $postargs['title'] . '"'; else $value='';
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Название сайта', __FILE__) . '</strong> </td><td class="td2">';
	echo '<input name="f_title" type="text" ' . $value . ' style="width: 99%;"></td></tr>';
	
	if (isset($postargs['description']) and $postargs['description']) $value = 'value="' . $postargs['description'] . '"'; else $value='';
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Описание', __FILE__) . '</strong> </td><td class="td2">';
	echo '<input name="f_desc" type="text" ' . $value . ' style="width: 99%;"></td></tr>';
	
	if (isset($postargs['queries']) and $postargs['queries']) $value = 'value="' . $postargs['queries'] . '"'; else $value='';
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Дополнительные метки', __FILE__) . '</strong> </td><td class="td2">';
	echo '<input name="f_queries" type="text" ' . $value . ' style="width: 99%;"></td></tr>';
		
	// капча из плагина капчи
	
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Введите нижние символы', 'plugins') . ' </td>
			<td style="text-align: left;" class="td2"><input type="text" name="f_captha" value="" maxlength="4"> <img src="' 
			. getinfo('plugins_url') . 'captcha/img.php?image='
			. $session['session_id']
			. '&page='
			. mso_slug(mso_current_url())
			. '&code='
			. time()
			. '" title="' . t('Защита от спама: введите только нижние символы', 'plugins') . '" align="absmiddle"></td></tr>';

	
	echo '<tr><td class="td1">&nbsp;</td><td style="vertical-align: top; text-align: left;" class="td2"><input type="submit" class="submit" name="f_submit" value="' . t('Отправить', __FILE__) . '"></td></tr>';
	
	echo '</table></form></div>';


?>