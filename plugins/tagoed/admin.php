<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$CI = & get_instance();

	$plugin_url = getinfo('site_admin_url') . 'plugin_tagoed';
	$plugin_dir = getinfo('plugins_dir') . 'tagoed/';

?>

<div class="admin-h-menu">
<?php

	# сделаем меню горизонтальное в текущей закладке
	$menu  = mso_admin_link_segment_build($plugin_url, 'tags', t('Теги', __FILE__), 'select') . ' | ';
	$menu .= mso_admin_link_segment_build($plugin_url, 'articles', t('Статьи', __FILE__), 'select');
	echo $menu;

?>
</div>

<?php
	// Определим текущую страницу (на основе сегмента url)
	$seg = mso_segment(3);

	switch ($seg)
	{
		case 'articlelist':
				if (mso_segment(4))
				{
					$articleList = tagoed_tag_list_articles(mso_segment(4));
					$seg = 'articles';
				}
			break;

		case 'tagrename':
				if (mso_segment(4))
				{
					require_once($plugin_dir . 'tag_rename.tpl');
				}
			break;

		case 'tagdelete':
				if (mso_segment(4))
				{
					tagoed_tag_delete(mso_segment(4));
					$absolute_url = getinfo('site_admin_url') . 'plugin_tagoed/tags';
					mso_redirect($absolute_url, true);
				}
			break;

		case 'articletagadd':
				if (mso_segment(4))
				{
					require_once($plugin_dir . 'article_tag_add.tpl');
				}
			break;

		case 'articletagdelete':
				if (mso_segment(4))
				{
					tagoed_article_tag_delete(mso_segment(4), mso_segment(5));
					$absolute_url = getinfo('site_admin_url') . 'plugin_tagoed/articles';
					mso_redirect($absolute_url, true);
				}
			break;

	}

	// Подключаем соответственно нужный файл
	if ($seg == 'tags')
	{
		require_once($plugin_dir . 'tags.php');
	}
	elseif ($seg == 'articles')
	{
		require_once($plugin_dir . 'articles.php');
	}
	
?>