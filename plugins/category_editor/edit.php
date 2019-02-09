<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once(getinfo('common_dir').'category.php');
require_once(getinfo('common_dir').'meta.php');

$category_id = mso_segment(4);

function ce_val_to_select($values, $select_name = '', $value = '')
{
	$f = '';
	if ($values)
	{
		$values = explode('#', $values);
		$f = '<select name="'.$select_name.'">';
		
		foreach($values as $val) 
		{
			$val = trim($val);
			$val_t = $val;
			
			$ar = explode('||', $val);
			if (isset($ar[0])) $val = trim($ar[0]);
			if (isset($ar[1])) $val_t = trim($ar[1]);
			
			if ($value == $val) $checked = 'selected="selected"';
			  else $checked = '';
			$f .= NR . '<option value="' . $val . '" ' . $checked . '>' . $val_t . '</option>';
		}
		$f .= NR . '</select>' . NR;
	}
	return $f;
}

function get_velues_option($key_option)
{
	$options = mso_get_option('category_editor_fields', 'plugins', false);
	foreach($options as $value)
	{
		if($key_option == $value['key'])
		{
			return $value['values'];
		}
	}
}


if($post = mso_check_post(array('f_category', 'f_meta')))
{
	mso_checkreferer();
	$CI = & get_instance();
	$data_cat = $post['f_category'];
	$CI->db->where('category_id', $category_id);
	$upd = $CI->db->update('category', $data_cat);
	$data_cat_meta = $post['f_meta'];
	foreach($data_cat_meta as $dk => $dv)
	
	$upd_meta = mso_add_meta($dk, $category_id, 'category', $dv);
	
	
	
	if($upd)
	{
		
		echo '<div class="update">Обновлено!</div>';
	}
	else
	{
		echo '<div class="error">Ошибка обновления</div>';
	}
	mso_flush_cache();
}# end if post


CategoryEditor::getInstance()->load = $category_id;


echo '<p class="t12px">На этой странице вы можете внести измения в параметры категории.</p>';


$prefix = CategoryEditor::getInstance()->prefix;

if(!CategoryEditor::getInstance()->all_params()) 
{
	echo '<div class="error">Ошибка. Категории не существует.</div>';
	return;
}

echo '<h2>'.CategoryEditor::getInstance()->_name.'</h2>';
?>
<form action="#" method="post" class="ce-cat-page-edit">
<div class="flex flex-wrap">
    <div class="w35">
    <label><span class="name-field">Название категории</span>
    <input type="text" name="f_category[category_name]" value="<?=CategoryEditor::getInstance()->_name?>" id="field_to_link"/>
    </label>
    </div>
    
   <div class="w35">
   <label><span class="name-field">Title категории</span>
    <input type="text" name="f_meta[<?=$prefix?>title]" value="<?=CategoryEditor::getInstance()->title?>" id="title_category"/>
    </label>
    </div>
   
    <div class="w25">
    <label><span class="name-field">Ссылка</span>
    <input type="text" name="f_category[category_slug]" value="<?=CategoryEditor::getInstance()->_slug?>" id="field_on_link"/>
    </label>
    </div>
</div>

<div class="w100 mar15-t">
    
</div>



<p>
<label><span class="name-field">Description категории</span>
<textarea name="f_meta[<?=$prefix?>description]" rows="3"><?=CategoryEditor::getInstance()->description?></textarea>
</label>
</p>
<p>
<label><span class="name-field">Keywords категории</span>
<textarea name="f_meta[<?=$prefix?>keywords]" rows="3"><?=CategoryEditor::getInstance()->keywords?></textarea>
</label>
</p>

<p>
<label><span class="name-field">Описание категории</span>
<textarea name="f_category[category_desc]" rows="10"><?=CategoryEditor::getInstance()->_desc?></textarea>
</label>
</p>

<p>
<label><span class="name-field">Шаблон оформления категории</span>
<?
$value = '';
$dirs = mso_get_dirs(getinfo('template_dir') . 'main/', array(), 'main.php');
	
if ($dirs)	$values = ' ||Обычный #' . implode($dirs, '#');
else $values = ' ||Обычный';

echo ce_val_to_select($values, "f_meta[{$prefix}template]", CategoryEditor::getInstance()->template);

?>
</label>
</p>

<?
$options = mso_get_option('category_editor_fields', 'plugins', false);

if($options)
{
	$option_form = '';
	foreach($options as $key => $value)
	{
		$option_form .= '<p>';
		$option_form .= '<label>';
		$option_form .= '<span class="name-field">'.$value['name'].'</span>';
		
		$value_view = str_replace($prefix, '', $value['key']);
		
		switch($value['type'])
		{
			case 'select':
				$option_form .= ce_val_to_select(get_velues_option($value['key']), "f_meta[".$value['key']."]", CategoryEditor::getInstance()->$value_view);
				break;
			case 'textarea':
				$option_form .= '<textarea name="f_meta['.$value['key'].']" rows="4">'.htmlspecialchars(CategoryEditor::getInstance()->$value_view).'</textarea>';
				break;
			case 'textfield':
				$option_form .= '<input type="text" name="f_meta['.$value['key'].']" value="'.htmlspecialchars(CategoryEditor::getInstance()->$value_view).'"/>';
				break;
		}
		$option_form .= '</label></p>';
	}
echo $option_form;
}
?>
<p>
<button type="submit" class="mso-save-ini i-save" name="save">Сохранить изменения</button>
</p>
</form>
