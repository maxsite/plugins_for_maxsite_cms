<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit')) )
	{
		mso_checkreferer();
		var_export($post);
	}
?>

<h1><?= t('ТегоРедактор', __FILE__) ?></h1>
<p class="info"><?= t('Небольшой редактор тегов', __FILE__) ?></p>

<?php

	//Отображаем список тегов
	//Получаем список тегов
	$tagList = tagoed_tag_list_all();
	//Открываем форму
	require_once('tag_start.tpl');
	//Выводим их в цикле
	foreach($tagList as $tag => $id)
	{
		require('tag_element.tpl');
	}
	//Закрываем форму
	require_once('tag_end.tpl');

?>