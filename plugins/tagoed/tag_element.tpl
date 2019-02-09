<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$count = mb_substr_count  ($id, ',') + 1;
	$rename = tagoed_link_create(t('Переименовать тег', __FILE__), 'tagrename/', $tag, '?');
	$delete = tagoed_link_create(t('Удалить тег', __FILE__), 'tagdelete/', $tag, 'x');
	$tag = tagoed_link_create(t('Показать статьи по тегу', __FILE__), 'articlelist/', $tag, $tag);

	$CI->table->add_row($tag, $count, $rename, $delete);

?>
