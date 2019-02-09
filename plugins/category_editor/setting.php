<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

echo '<p>'.tf('Настройки полей для редактирования').'</p>';
echo '<p class="t12px">Вы можете содать поля с ключами для использования их при формировании виртуального меню по связям (<a href="/admin/category_editor/docs/tree-virt">подробнее</a>)</p>';
echo '<p class="t12px t-red">Оставьте поле КЛЮЧ пустым, чтобы удалить поле.</p>';

if($post = mso_check_post(array('f_field')))
{
	
	mso_checkreferer();
	$pref = CategoryEditor::getInstance()->prefix; // префикс
	$save = $post['f_field'];
	foreach($save as $key => &$value)
	{
		$value['key'] = trim($value['key']);
		if(!$value['key'] || $value['key'] == ' ')
		{
			unset($save[$key]);
		}
		$value['key'] = $pref.$value['key'];
	}
	unset($value);
	
	$res = mso_add_option('category_editor_fields', $save, 'plugins', false);
	if($res)
		echo '<div class="update">Настройки сохранены</div>';
	else
		echo '<div class="error">Ощибка созранения</div>';
	mso_refresh_options();
	
}# end if post




$options = mso_get_option('category_editor_fields', 'plugins', false);
$fields_array = array(
			'textfield' => 'Простое текстовое поле',
			'textarea' => 'Большое текстовое поле',
			'select' => 'Выпадающий список'
			);
if($options){
	// спрячем префикс
	foreach($options as &$vv)
	{
		$vv['key'] = str_replace(CategoryEditor::getInstance()->prefix, '', $vv['key']);
	}
	unset($vv);
	
	$pr = '';
	$key = 0;
	
	
	
	if($options)
	{
		$fi = 0;
		foreach($options as $key => $value)
		{
			++$fi;
			$pr .= '<div class="flex flex-wrap pad40-l pos-relative bor-solid-b bor1 bor-blue pad25-b mar25-b" data-line="'.$key.'">';
			
			$pr .= '<span class="snum-list pos-absolute">'.$fi.'</span>';
			
			$pr .= '<div class="w35">';
				$pr .= '<label>';
				$pr .= '<span class="name-field">Название</span> ';
				$pr .= '<input class="c-name" type="text" name="f_field['.$key.'][name]" value="'.$value['name'].'">';
				$pr .= '</label>';
			$pr .= '</div> ';
			
			
			
			$pr .= '<div class="w35">';
				$pr .= '<label>';
				$pr .= '<span class="name-field">Ключ</span> ';
				$pr .= '<input class="c-key" type="text" name="f_field['.$key.'][key]" value="'.$value['key'].'">';
				$pr .= '</label>';
			$pr .= '</div> ';
				
				
			$pr .= '<div class="w24">';
				$pr .= '<label>';
				$pr .= '<span class="name-field">Тип поля</span> ';
				
				$pr .= '<select name="f_field['.$key.'][type]">';
					foreach($fields_array as $kk => $vval)
					{
						if($kk == $value['type'])
							$pr .= '<option value="'.$kk.'" selected="selected">'.$vval.'</option>';
						else
							$pr .= '<option value="'.$kk.'">'.$vval.'</option>';
					}
				$pr .= '</select>';
				$pr .= '</label>';
			
			$pr .= '</div> ';
			
			
			
			
			$pr .= '<div class="w100 mar5-t">';
				$pr .= '<label>';
				$pr .= '<span class="name-field">Значения</span> ';
				$pr .= '<input type="text" name="f_field['.$key.'][values]" value="'.$value['values'].'" placeholder="Возможные значения (только для select)">';
				$pr .= '</label>';
			$pr .= '</div>';
			
			$pr .= '</div>';
		}
	$key = $key+1;
	}
}
else
{
	$key = 0;
	$pr = '';
}

$pr .= '<div class="flex flex-wrap pad40-l pad25-t pos-relative bor-solid-b bor1 bor-blue pad25-b mar25-b">';
			
	$pr .= '<span class="snum-block-title pos-absolute">Создать новое поле</span>';
	
	$pr .= '<div class="w35">';
		$pr .= '<label>';
		$pr .= '<span class="name-field">Название</span> ';
		$pr .= '<input class="c-name" type="text" name="f_field['.$key.'][name]" value="">';
		$pr .= '</label>';
	$pr .= '</div> ';
	
	
	
	$pr .= '<div class="w35">';
		$pr .= '<label>';
		$pr .= '<span class="name-field">Ключ</span> ';
		$pr .= '<input class="c-key" type="text" name="f_field['.$key.'][key]" value="">';
		$pr .= '</label>';
	$pr .= '</div> ';
		
		
	$pr .= '<div class="w24">';
		$pr .= '<label>';
		$pr .= '<span class="name-field">Тип поля</span> ';
		
		$pr .= '<select name="f_field['.$key.'][type]">';
			foreach($fields_array as $kk => $vval)
			{
				$pr .= '<option value="'.$kk.'">'.$vval.'</option>';
			}
		$pr .= '</select>';
		$pr .= '</label>';
	
	$pr .= '</div> ';
	
	
	
	
	$pr .= '<div class="w100 mar5-t">';
		$pr .= '<label>';
		$pr .= '<span class="name-field">Значения</span> ';
		$pr .= '<input type="text" name="f_field['.$key.'][values]" value="" placeholder="Возможные значения (только для select)">';
		$pr .= '</label>';
	$pr .= '</div>';
			
$pr .= '</div>';
?>
<form action="#" method="post" class="ce-setting-field">
<?=$pr?>
<p>
<button type="submit" class="mso-save-ini i-save" name="save">Сохранить изменения</button>
</p>
</form>


<?
