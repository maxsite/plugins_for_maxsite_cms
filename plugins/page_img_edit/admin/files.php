<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');	
	// нужно вывести навигацию по каталогам в uploads
	$all_dirs = directory_map(getinfo('uploads_dir'), 3); // карта каталога uploads — до 3 уровня
	
	$out_dirs = '';

	//@ksort($all_dirs, SORT_STRING);

	// pr($all_dirs);
	
	
	$out_dirs .=   '<select class="admin_file_filtr" onchange="{
		var dir = $(this).val();
		getPDir(dir);
	};">';

	$selected = ($segments) ? '' : ' selected';
	$out_dirs .=   '<option value="/"' . $selected . '>uploads</option>';

    $n1='';
    $found  = '';
	foreach ($all_dirs as $n=>$d)
	{
		//@ksort($d, SORT_STRING);

		// нам нужны только каталоги
		if (!is_array($d)) continue; // каталоги — это массив
		if ($n == 'mini' or $n == '_mso_i' or $n == '_mso_float' or $n == 'smiles') continue; // эти нас не интересуют

		if ($n != '_pages') // этот не выводим
		{
			$selected = ($segments == $n) ? ' selected' : '';
			$out_dirs .=   '<option value="' . $n . '"' . $selected . '>' . $n . '</option>';
			if ($selected) $found  = $n;
		}
		// посмотрим, что там за подкаталоги 
		foreach ($d as $n1=>$d1)
		{
			//@ksort($d1, SORT_STRING);

			if (!is_array($d1)) continue; // каталоги — это массив
			if ($n1 == 'mini' or $n1 == '_mso_i') continue; // эти нас не интересуют
			
			if ($n != '_pages') // если это _pages, то добавляем только текущий подкаталог
			{
				$selected = ($segments == $n . '/' . $n1) ? ' selected' : '';
				$out_dirs .=   '<option value="' . $n . '/' . $n1 .'"' . $selected . '>' . $n . '/' . $n1 . '</option>';
			    if ($selected) $found  = $n . '/' . $n1;
			}
			else
			{
				if ($selected = ($segments == $n . '/' . $n1) ? ' selected' : '')
				{
					$out_dirs .=   '<option value="' . $n . '/' . $n1 .'"' . $selected . '>' . $n . '/' . $n1 . '</option>';
			        if ($selected) $found  = $n . '/' . $n1;
				}
			}
		}
	}
	
   // if ($found) $goto = t('Перейти'); else $goto = t('Создать и перейти');
   //<a id="goto_files" href="' . getinfo('site_admin_url') . 'files/' . $found . '" target="_blank" class="goto-files">' . t('Перейти') . '</a>
	$out_dirs .=   '</select>';
	

# end file