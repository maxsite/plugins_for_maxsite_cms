<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<p">Документация к плагину "Category editor"</p>

<ul class="docs-list">
	<li><a href="/admin/category_editor/docs/start">Общее описание</a></li>
    <li><a href="/admin/category_editor/docs/tree">Структура сайта</a></li>
    <li><a href="/admin/category_editor/docs/tree-virt">Виртуальное меню</a></li>
    <li><a href="/admin/category_editor/docs/class">Использование значений</a></li>
    <li><a href="/admin/category_editor/docs/create-params">Создание дополнительных полей</a></li>
    <li><a href="/admin/category_editor/docs/global-vars">Глобальные значения</a></li>
</ul>

<?

switch(mso_segment(4))
{
	case 'start':
		include(getinfo('plugins_dir') . 'category_editor/docs/start.php');
		break;
	case 'class':
		include(getinfo('plugins_dir') . 'category_editor/docs/class.php');
		break;
	case 'tree':
		include(getinfo('plugins_dir') . 'category_editor/docs/tree.php');
		break;
	case 'tree-virt':
		include(getinfo('plugins_dir') . 'category_editor/docs/tree-virt.php');
		break;
	case 'create-params':
		include(getinfo('plugins_dir') . 'category_editor/docs/create_params.php');
		break;
	case 'global-vars':
		include(getinfo('plugins_dir') . 'category_editor/docs/global_vars.php');
		break;	
	
	default: include(getinfo('plugins_dir') . 'category_editor/docs/start.php');
	
}
# end file