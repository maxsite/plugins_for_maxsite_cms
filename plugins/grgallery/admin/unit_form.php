<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

function unit_form ($args)
	{
	global $MSO;
	$CI = & get_instance();
	$CI->load->helper('form');	// подгружаем хелпер форм
	$CI->load->helper('file'); // хелпер для работы с файлами
	require ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	$id = '';
	if (mso_segment(3))	$id = mso_segment(3);	// получаем id страницы
	$idpg_dir = $grgll['uploads_pict_dir'];
	if (is_numeric($id)) $idpg_dir =$idpg_dir.'/'.$grgll['prefix'].$id;
	$new_dir = getinfo('uploads_dir').$idpg_dir;
	
	$out = '';
	
	//$out .= form_fieldset('<h3>'.t('Изображения этой страницы',  'admin').'</h3>');
	
	
	# вывод формы с картинками и загрузками
	
	$admin_files_field_count = (int) mso_get_option('admin_files_field_count', 'general', 3);
	if ($admin_files_field_count < 1) $admin_files_field_count = 3;
	if ($admin_files_field_count > 50) $admin_files_field_count = 50;
	
	// получаем из мета название главной картинки для радиобокса
	$mpict = '';
	if ($id != '') 
		{
		$res = mso_get_meta('mpict','page', $id);
		foreach ($res as $row)
			{
			$mpict = $row['meta_value'];
			}
		}	
	// форма загрузки
	$out .= '<h3>'.t('Загрузка изображений для этой страницы', 'admin').'</h3>';
	$out.= '<div class="upload_file">
		<p>'.t('Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Готово». Размер файла не должен превышать', 'admin').' '. ini_get ('post_max_size') . '.</p>
		<form action="" method="post" enctype="multipart/form-data">' . mso_form_session('f_session2_id') .
		'<p>';
	
	for ($i = 1; $i <= $admin_files_field_count; $i++)
	{
		$out.= '<input type="file" name="f_userfile[]" size="50">';
		if ($i < $admin_files_field_count) $out.= '<br>';
	};
	
	$new_dir .= '/';
	
	$out.= '<br><input type="reset" class="inputSubmit" value="' . t('Очистить форму загрузки', 'admin') . '"></p>';
		//$out.='</form>
		$out.='</div>
		';

	// как выводим файлы
	$admin_view_files = mso_get_option('admin_view_files', 'general', 'mini');
	$admin_sort_files = mso_get_option('admin_sort_files', 'general', 'name_asc');
	
	if ($admin_view_files == 'table')
	{
		$CI->load->library('table');
		$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="110">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

		$CI->table->set_template($tmpl); // шаблон таблицы
		//$CI->table->set_heading('&bull;', t('настройки', 'admin')); // заголовки
	}

	// проходимся по каталогу аплоада и выводим их списком

	$uploads_dir = $new_dir;
	$uploads_url = getinfo('uploads_url') . $idpg_dir.'/';

	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
	if (!$dirs) $dirs = array();
	
	// сортировка файлов
	$dirs0 = array();
	$i = 1; // счетчик для случаев, если время файлов совпадает
	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		$dirs0[filemtime($uploads_dir . $file) . 'START' . $i . 'END'] = $file;
		$i++;
	}
	$dirs = $dirs0;
	
		
	if ($admin_sort_files == 'name_asc') asort($dirs);// по имени
	elseif ($admin_sort_files == 'name_dest') arsort($dirs);// по имени обратно
	elseif ($admin_sort_files == 'date_asc') ksort($dirs);// по дате
	else krsort($dirs); // по дате обратно

	// разрешенные типы файлов
	$allowed_types = mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz');
	$allowed_ext = explode('|', $allowed_types);

	$out_all = ''; // весь вывод
	
$fn_mso_descritions = $new_dir . '_mso_i/_mso_descriptions.dat';
		if (file_exists( $fn_mso_descritions )) // файл есть
			{
			$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
			}
		else $mso_descritions = array();
		
	foreach ($dirs as $datefile=>$file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

		$cod = '<p>';
		$title = '';
		$title_f = '';
		
		$radch = false;
		if ($mpict == $file) $radch = true;
		
		if (isset($mso_descritions[$file]))
			{
			$title = $mso_descritions[$file];
			if ($title) $title_f = '<em>' . htmlspecialchars($title) . '</em>';
			}
		
		$datefile = preg_replace('!START(.*)END!', '', $datefile);
		$headik = '<label for="'. mso_strip($file).'">'. $file .'<br>'. $title_f . '</label>';
		$timeupl ='<i>' . date("Y-m-d H:i:s", $datefile) . '</i>';
		$rad = form_radio('f_radio_files', $file, $radch, 'title="выводить на главной" id="' . mso_strip($file) . '" class="f_radio_files"').'выводить на главной';
		$sel = form_checkbox('f_check_files[]', $file, false, 'title="'. htmlspecialchars($title) . '" id="' . mso_strip($file) . '" class="f_check_files"')
		.' удалить';
						
		$title_alt = str_replace('"', '&amp;quot;', $title);
		$title_alt = str_replace('<', '&amp;lt;', $title_alt);
		$title_alt = str_replace('>', '&amp;gt;', $title_alt);
		$title_alt = str_replace('\'', '&amp;#039;', $title_alt);
	
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
			{
			
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) 
				{ $_f = '_mso_i/' . $file; }
			else 
				{ $_f = $file; }

			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . htmlspecialchars($title) . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . $uploads_url . $_f . '"></a>';
			}
		$cod .= '<a href="#" class="edit_descr_link" onClick="return false;">' . t('Изменить описание', 'admin') . '</a>';
		$out_all .= '<div class="cornerz"><div class="wrap">' . $headik.'<br>'.$timeupl.$predpr .'<br>'. $rad.'<br>'. $cod .'<br><br>'.$sel .'</div></div>'; // 
		if ($admin_view_files == 'table') $CI->table->add_row($predpr, $headik.'&nbsp;&nbsp;&nbsp;'.$rad.$cod.'&nbsp;&nbsp;&nbsp;'.$sel);
	}

	// добавляем форму
	if ($out_all != '') 
	{
		if ($admin_view_files == 'table') 
			$out.= $CI->table->generate(); // вывод подготовленной таблицы
		else
			{
			$out.= '<div class="float-parent" style="width:100%">';
			$out.= $out_all;
			$out.= '<div style="clear:both"></div></div>';
			}

		$n = '\n';
		$up = $uploads_url;

		$mess = '';
		$save_button = t('Сохранить', 'admin');

		$out.= 
		<<<EOF
<script type="text/javascript">
function toggleAll() {
	var allCheckboxes = $("input.f_check_files:enabled");
	var notChecked = allCheckboxes.not(':checked');
	allCheckboxes.removeAttr('checked');
	notChecked.attr('checked', 'checked');
}

$(function()
{
	$("#check-all").click(function(){
		toggleAll()
	});

	//nicothin добавления
	if ($('script[src$="jquery/cornerz.js"]').size()) 
	{ 
		$('div.cornerz').cornerz({radius:10, background: "#FFFFFF"}); 
	}
	$('.edit_descr_link').toggle(function () 
	{
		if (!$(this).parent().parent().children('.edit_descr').size())
		{
			var file_name = $(this).parent().parent().children(':radio').attr('id');
			var old_descr = $(this).parent().parent().children('label').children('em').text();
			var form_code = '<div class="edit_descr" style="width: 100%;" style="display:none"><input type="hidden" name="f_file_name[]" value="' + file_name + '"><textarea name="f_file_description[]" >' + old_descr + '</textarea><br></div>';
			$(this).parent().parent().append(form_code);
		}
		$(this).parent().parent().find('.edit_descr').slideDown('fast');
	},
	function () {
		$(this).parent().parent().find('.edit_descr').slideUp('fast');
	});
	// nicothin конец добавления
	
	$('#gallerycodeclick').click(function()
	{
		$('#gallerycode').html('');

		codegal = '';
		$("input[name='f_check_files[]']").each( function(i)
		{
			if (this.checked)
			{
				t = this.title;
				if (!t) { t = ''; }
				else { t = ' ' + t; }
				
				codegal = codegal + '[gal={$up}mini/' + this.value + t + ']{$up}'+ this.value +'[\/gal]{$n}';
			}
		});

		if ( codegal )
		{
			n = $('#gallerycodename').val();
			if (n) { n = '[galname]' + n + '[/galname]';}
			else { n = ''; }

			codegal = '[gallery]' + n + '{$n}'+ codegal + '[/gallery]';
			$('#gallerycode').html(codegal);
			$('#gallerycode').css({ background: '#F0F0F0', width: '100%', height: '150px',
									border: '1px solid gray', margin: '20px 0',
									'font-family': 'Courier New',
									'font-size': '9pt'});
			$('#gallerycode').fadeIn('slow');
			$('#gallerycode').select();
		}
		else
		{
			$('#gallerycode').hide();
			alert('{$mess}');
		}
	});
});
</script>
<!-- <hr class="br"> -->
EOF;
	}
	else
	{
		$out.= '<p>' . t('Нет загруженных файлов', 'admin') . '</p>';
	}	
	//$out.='</form>';
	//$out.= form_fieldset_close();
	return $out;
	}
?>