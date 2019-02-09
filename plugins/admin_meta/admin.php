<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * admin tag plugin
 * Author: (c) Tux
 * Plugin URL: http://6log.ru/admin_tag 
 */
 
	define('COUNT', 15);//кол-во выводимых тегов на странице.

	mso_cur_dir_lang(__FILE__);
	
	$CI = & get_instance();

	# автоназначение slugs
	if ( $post = mso_check_post(array('f_all_slugs_submit')) )
	{
		mso_checkreferer();
		
		$CI->db->select('meta_id,meta_value,meta_slug');
		$query=$CI->db->get('meta');
 		// проверяем сколько получено записей
		if ($query->num_rows() > 0) // больше нуля, можно работать
		{
 			$i = 0;
			$str = '';
			$new_id = '';
			$new_str = "UPDATE mso_meta SET meta_slug = CASE";
			$new_query = '';
  			foreach ($query->result_array() as $tag2) // обходим в цикле
			{
			 	if ($tag2['meta_slug']=='')
				{
			 		$str=$tag2['meta_value'];
					$str = mso_slug($str);
												
					$arr[$i] = array('id' => $tag2['meta_id'], 'meta_slug' => $str);
					$new_id.=','.$tag2['meta_id'];
					$new_str.=" WHEN meta_id = ".$tag2['meta_id']." THEN '".$str."'";
					$i++;	
				}	
    		}
		}	 
		// Обновляем записи, если есть что
		if ($i > 0)
		{
			$new_id = substr_replace ($new_id,'',strpos($new_id,','),1);
			$new_query = $new_str." END WHERE meta_id IN (".$new_id.")";
//			print_r($new_query);
			$result = $CI->db->query($new_query);
			if (isset($result)) 
			{
				mso_flush_cache(); // сбросим кэш
				echo '<div class="update">' . t('Добавлено!') . '</div>';
			}
			else
				echo '<div class="error">' . t('Ошибка добавления!') . $result . ' </div>';
		} 
		else
			echo '<div class="error">' . t('Ошибка, записей на добаление нет!') . '</div>';
	}
		
	# редактирование тега
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_submit','f_meta_name','f_meta_slug','f_meta_value')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']); 
		
		$arr=array(
			'meta_value'=>$post['f_meta_name'][$f_id],
			'meta_slug'=>$post['f_meta_slug'][$f_id]
			);
//		$CI->db->where('meta_id',$f_id);
		$CI->db->where('meta_value', $post['f_meta_value'][$f_id]);
		$result=$CI->db->update('meta',$arr);
	
		if (isset($result)) 
		{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Обновлено!') . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}
	
	# удаление тега
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit','f_meta_value')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_submit']); 
		
		// подготавливаем данные
//		$data = array('meta_id' => $f_id );
		$data = array('meta_value'=> $post['f_meta_value'][$f_id]);
		$result = $CI->db->delete('meta',$data);
				
		if (isset($result)) 
		{	
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Удалено!') . $result['description'] . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка удаления!') . $result['description'] . '</div>';
	}
	
	# добавление нового тега
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 'f_new_name', 'f_new_slug')) )
	{
		mso_checkreferer();
		
		// Ищем схожие записи
		if ($post['f_new_name'] == '') 
		{
			echo '<div class="error">' . t('Ошибка, пустое поле!') . '</div>';
		}
		else 
		{
			if ($post['f_new_slug'] == '')  $post['f_new_slug'] = 'NULL';
			
			$CI->db->select('meta_id');
			$CI->db->where(array('meta_value'=>$post['f_new_name'] ));
			if ($post['f_new_slug'] != 'NULL')
			{
				$CI->db->or_where(array('meta_slug'=>$post['f_new_slug'] ));
			}
			$query = $CI->db->get('meta');

			if ( $query->num_rows() == 0 )
			{
				// такой записи нет, ее нужно вставить
				// подготавливаем данные для xmlrpc
				$data = array(
					'meta_key' => 'tags',
					'meta_table' => 'page',
					'meta_value' => $post['f_new_name'],
					'meta_slug' => $post['f_new_slug']
					);
				// выполняем запрос и получаем результат
				$result = $CI->db->insert('meta',$data);

				$CI->db->cache_delete_all();
			
				if (isset($result)) 
				{
					mso_flush_cache(); // сбросим кэш
					echo '<div class="update">' . t('Добавлено!') . '</div>';
				}
				else
					echo '<div class="error">' . t('Ошибка добавления!') . $result . ' </div>';
			} else
				echo '<div class="error">' . t('Ошибка, такая запись существует!') . ' </div>';
	   }
	}
?>

<!-- добавляем шапку -->
	<h1><?= t('Метки/Теги') ?></h1>
	<p class="info"><?= t('Настройка Тегов') ?></p>

<?php
	# Выводим все теги
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id') .
			'<table class="page" style="width: 100%; border-collapse: collapse;">
			
			<colgroup style="width: 50px; padding: 0 4px;">
			<colgroup style="width: 200px; padding: 0 4px;">
			<colgroup style="width: 150px; padding: 0 4px;">
			<colgroup style="width: 80px; padding: 0 4px;">
			<colgroup style="width: 80px; padding: 0 4px;">
			
			<tr style="vertical-align: top; font-weight: bold;">
			<td>Количество</td>
			<td>Название</td>
			<td>Ссылка</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr></table>' ;
	
	$format = '
	<table class="page" style="width: 100%; border-collapse: collapse;">
	
	<colgroup style="width: 50px; padding: 0 4px;">
	<colgroup style="width: 200px; padding: 0 4px;">
	<colgroup style="width: 150px; padding: 0 4px;">
	<colgroup style="width: 80px; padding: 0 4px;">
	<colgroup style="width: 80px; padding: 0 4px;">
	
	<tr style="text-align: center; vertical-align: top;">
	
	<input type="hidden" name="f_meta_value[[ID]]" value="[VALUE]" />
	<td class="alt"><strong title="' .
	 t('В этой рубрике [COUNT] страниц') . '">[COUNT]</strong></td>
	
	<td><input title="' . t('Название') . '" name="f_meta_name[[ID]]" value="[VALUE]" maxlength="500" style="width: 100%; margin: 0 -4px;" type="text" /></td>
		
	<td><input title="' . t('Короткая ссылка') . '" name="f_meta_slug[[ID]]" value="[SLUG]" maxlength="500" style="width: 100%; margin: 0 -4px;" type="text" /></td>
		
	<td><input type="submit" name="f_edit_submit[[ID]]" value="' . t('Изменить') . '" style="width: 100%; margin: 0 -2px;"></td>
	
	<td><input type="submit" name="f_delete_submit[[ID]]" value="' . t('Удалить') . '" style="width: 100%; margin: 0 -2px;" onClick="if(confirm(\'' . t('Удалить тег?') . '\')) {return true;} else {return false;}" ></td>
	
	</tr></table>';

	$count_tags = COUNT; // константа. кол-во выводимых тегов на странице.
	
	if ( (mso_segment(3) == 'next') && (mso_segment(4)) != '' ) {$next = mso_segment(4);} else {$next = 0;}
	if ( isset($next) )
	{
		$CI->db->select('meta_id,meta_value,meta_slug,COUNT(meta_value) AS meta_count');
		$CI->db->group_by('meta_value');
		$CI->db->limit($count_tags, $next*$count_tags);
		$query = $CI->db->get('meta');
	}

 	// проверяем сколько получено записей
	if ($query->num_rows() > 0) // больше нуля, можно работать
	{
 		$i=0;
		$tmp='';
    	$out = '';
	    foreach ($query->result_array() as $tag) // обходим в цикле
     	{
         // формируем вывод 
			if (isset($tag['meta_slug'])) {$tmp = $tag['meta_slug'];} else {$tmp = 'NULL';}
		
			$out .= str_replace(array('[ID]','[VALUE]', '[SLUG]','[COUNT]'),
//rev 0.5b		array($tag['meta_id'], $tag['meta_value'], $tmp, $tag['meta_count']), $format);
				array($tag['meta_id'], stripslashes(htmlspecialchars(trim($tag['meta_value']))), $tmp, $tag['meta_count']), $format);
			$i++;
		}
     	echo $out; // выводим
 	}
 	echo t('(NULL - поле не задано, значение по умолчанию)');
	
	#кнопко автоназначения ссылок для всех полей
	echo '<br><br><input type="submit" name="f_all_slugs_submit" value="' .
		 t('Получить Ссылки') . '"> -' . t('автоматически заполнятся поля ссылок') . '<br>';


	#Пагинация
	$CI->db->select('meta_id');
	$CI->db->group_by('meta_value');
	$query=$CI->db->get('meta');
	$count = $query->num_rows();
	
	$menu = '<br><div align="center">';
	if ($next!=0)
	{
		$menu .= '<a style="padding-right:20px" href="' .getinfo('siteurl'). 'admin/meta/next/' . ($next - 1) . '">&lt;&lt;Назад</a>';
	}
	if ( ($next+1)*$count_tags < $count)
	{
		$menu .= '<a style="padding-left:20px" href="' .getinfo('siteurl'). 'admin/meta/next/'.($next + 1) . '">Вперед&gt;&gt;</a>';
	}
	$menu .= '</div>';
	echo $menu;
	
	# строчка для добавления нового тега //rev 1.0b
	echo '
	<br><br><br>
	<b>' . t('Название') . '</b> <input style="width: 200px;" type="text" name="f_new_name" value="">
	<b>' . t('Ссылка') . '</b> <input style="width: 200px;" type="text" name="f_new_slug" value="">
	<br><br><input type="submit" name="f_new_submit" value="' . t('Добавить новый тег') . '">
	</form>';

?>