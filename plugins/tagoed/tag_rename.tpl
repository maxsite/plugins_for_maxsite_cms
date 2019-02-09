<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$old_tag = $post['f_old_tag'];
		$new_tag = $post['f_new_tag'];
		if($new_tag != $old_tag)
		{
			$result = tagoed_tag_rename($old_tag, $new_tag);
			echo '<div class="update">' . t('Переименовано!', __FILE__) . '</div>';
			$old_tag = $new_tag;
		}
	}
	if(!isset($old_tag))
	{
		$old_tag = mso_segment(4);
	}
	if(!isset($new_tag))
	{
		$new_tag = $old_tag;
	}

?>
<h1><?= t('ТегоРедактор', __FILE__) ?></h1>
<p class="info"><?= t('Небольшой редактор тегов', __FILE__) ?></p>
<br/>
<h2><?= t('Переименовать тег', __FILE__) ?></h2>

<?php

	$form = '';
	$form .= '<p><strong>' . t('Старое значение', __FILE__) . '</strong> ' . $old_tag . '</p>';
	$form .= '<input name="f_old_tag" type="hidden" value="' . $old_tag . '" >';
	$form .= '<p><strong>' . t('Новое значение', __FILE__) . '</strong> ' . ' <input name="f_new_tag" type="text" value="' . $new_tag . '"></p>';
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="' . t('Переименовать', __FILE__) . '" style="margin: 25px 0 5px 0;" />';
	echo '</form>';

?>