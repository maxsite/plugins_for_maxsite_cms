<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$add = tagoed_link_create(t('Добавить тег к статье', __FILE__), 'articletagadd/', $id, '+');
	$title = $article['0'];
	$tags_array = $article['1'];
	$tags = '';
	foreach($tags_array as $tag_id => $tag_name)
	{
		if(is_integer($tag_id))
		{
			$rename = tagoed_link_create(t('Переименовать тег', __FILE__), 'tagrename/', $tag_name, '?');
			$delete = tagoed_link_create(t('Удалить тег из статьи', __FILE__), 'articletagdelete/', $id . '/' . $tag_name, 'x');
			if($tags !== '')
			{
				$tags .= ', ' . tagoed_link_create(t('Показать статьи по тегу', __FILE__), 'articlelist/', $tag_name, $tag_name);
				$tags .= '(' . $rename . ' - ' . $delete . ')';
			}
			else
			{
				$tags = tagoed_link_create(t('Показать статьи по тегу', __FILE__), 'articlelist/', $tag_name, $tag_name);
				$tags .= '(' . $rename . ' - ' . $delete . ')';
			}
		}
	}

	$CI->table->add_row($title, $tags, $add);
	
?>
