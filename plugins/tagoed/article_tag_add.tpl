<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$id_article = mso_segment(4);
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$new_tag = $post['f_new_tag'];
		if($new_tag !== false)
		{
			tagoed_article_tag_add($id_article, $new_tag);
			$absolute_url = getinfo('site_admin_url') . 'plugin_tagoed/articles';
			mso_redirect($absolute_url, true);
		}
	}

?>
<h1><?= t('ТегоРедактор', __FILE__) ?></h1>
<p class="info"><?= t('Небольшой редактор тегов', __FILE__) ?></p>
<br/>
<h2><?= t('Добавить тег', __FILE__) ?></h2>

<?php

	$form = '';
	$form .= '<p><strong>' . t('Статья', __FILE__) . '</strong> ' . tagoed_article_get_title($id_article) . '</p>';
	$form .= '<p><strong>' . t('Новый тег', __FILE__) . '</strong> ' . ' <input name="f_new_tag" type="text" value=""></p>';
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="' . t('Добавить', __FILE__) . '" style="margin: 25px 0 5px 0;" />';
	echo '</form>';

?>