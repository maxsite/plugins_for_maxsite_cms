<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	mso_cur_dir_lang('admin');
	
	DEFINE('FM_GOOD_RESP', '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	DEFINE('FM_ERROR_RESP', '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}');
	
	//Получаем настройки
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );	
	
 // сформируем массив сортировок 
 if (!isset($options['sort_fields']))
   $options['sort_fields'] = array(
       'name_asc' => 'Имя',
       'name_dest' => 'Имя обратно',
       'date_asc' => 'Дата',
       'date_dest' => 'Дата обратно',
       
   //    'date_photo' => 'Дата снимка',
    //   'file' => 'Файл',
    //   'width' => 'Ширина',
    //   'position' => 'Положение',
    //   'tags' => 'Метки',
         );	
	
	$allowed_types = (isset($options['allowed_types'])) ? $options['allowed_types'] : 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';
	$hide_options = (isset($options['hide_options'])) ? $options['hide_options'] : '1';
	$show_size = (isset($options['show_size'])) ? $options['show_size'] : '1';
?>

<h1><?= t('Загрузки. Файлы. Галереи') ?></h1>
<p class="info"><?= t('Здесь вы можете выполнить необходимые операции с файлами.') ?></p>
<script type="text/javascript">
    jQuery(document).ready(function(){
        // Скрываем все спойлеры
        <?php if($hide_options == '1') echo "jQuery('.spoiler-body').hide()"; ?>
        // по клику отключаем класс folded, включаем unfolded, затем для следующего
        // элемента после блока .spoiler-head (т.е. .spoiler-body) показываем текст спойлера
        jQuery('.spoiler-head').click(function(){
            jQuery(this).toggleClass("folded").toggleClass("unfolded").next().slideToggle()
        })
    })
</script>

<?php
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	
	$CI->load->helper('directory');
	$CI->load->helper('form');	

	
	// по сегменту определяем текущий каталог
	// если каталога нет, скидываем на дефолтный ''
	$current_dir = getCurDir();
	$path = getinfo('uploads_dir') . $current_dir;
	
	if ( ! is_dir($path) ) // нет каталога
	{
		$path = getinfo('uploads_dir');
		$current_dir = $current_dir_h2 = '';
	}

	// Если необходимо - создаем новый каталог
	// в текущем или удаляем текущйи каталог
	delete_this_cat($current_dir);
	create_new_cat($path);

	// нужно создать в этом каталоге _mso_i и mini если нет
	if ( ! is_dir($path . '_mso_i') ) @mkdir($path . '_mso_i', 0777); // нет каталога, пробуем создать
	if ( ! is_dir($path . 'mini') ) @mkdir($path . 'mini', 0777); // нет каталога, пробуем создать

	// описания файлов хранятся в виде серилизованного массива в
	// uploads/_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';

	if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
		write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив

	if (file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
	{
		// массив данных: fn => описание )
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $mso_descritions = array();

	# Добавление Рамира -  редактирование описания
	if ( $post = mso_check_post(array('f_session_id', 'f_file_name', 'f_file_description', 'f_edit_submit')) )
	{
		mso_checkreferer();

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$post['f_file_name']]);
			$mso_descritions[$post['f_file_name']]=$post['f_file_description'];
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл

		echo '<div class="update">' . t('Описание обновлено!', 'admin') . '</div>';
	}
	# Конец Добавление Рамира

	// Если загружали файлы - то выводим сообщения
	// о результатах загрузки
	if ( $post = mso_check_post(array('f_session_id2', 'upl_message')) )
	{
		mso_checkreferer();
		//die($post['upl_message']);
		$post['upl_message'] = nl2br($post['upl_message']); //Совместимость с Оперой
		$post['upl_message'] = str_replace(FM_GOOD_RESP, "", $post['upl_message']);
		$post['upl_message'] = str_replace(FM_ERROR_RESP, "", $post['upl_message']);
		echo preg_replace('|</div>\s*<div\sclass="error">|i',"<br>", $post['upl_message']); //$post['upl_message'];
	}	
	
	# удаление выделенных файлов
	delete_selected($post, $current_dir);
	
	// после загрузки сразу обновим массив описаний - он ниже используется
	if (file_exists( $fn_mso_descritions )) // файла нет, нужно создать массив
	{
		// массив данных: fn => описание )
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $mso_descritions = array();


 // если нажата кнопка сортировки
 if ( $post = mso_check_post(array('f_session_id3', 'f_sort_submit')) ) 
 {
	  mso_checkreferer();
	  $sort_field = mso_array_get_key($post['f_sort_submit']);
    if (isset($options['sort_fields'][$sort_field]))
    {
   		 mso_add_option('admin_sort_files', $sort_field , 'general');
   		 echo '<div class="update">Порядок сортировки изменен: ' . $options['sort_fields'][$sort_field] . '</div>';
    }
    else 
    {
   		// mso_add_option('admin_sort_files', 'date_asc' , 'general');
       echo '<div class="error">Ошибка сортировки</div>';
    }
 } 

	
	// Выводим навигацию
	echo '<div style="float:left">';
	navigate_block();
	
	// форма нового каталога
	new_folder_block();
	echo '</div>';

	// форма загрузки
	upload_block($allowed_types, $current_dir);

  // форма кнопок сортировки
  upload_sort_block($options['sort_fields']);

	//Список файлов + создание галереи
	files_block($allowed_types, $current_dir, $mso_descritions, $show_size);


/******************************************
*******************************************
			ФУНКЦИИ ВЫВОДА БЛОКОВ
*******************************************
******************************************/
	
/*****
Блок навигации
***/
function navigate_block(){
	global $MSO;
	require($MSO->config['plugins_dir'] . 'file_manager/tree/tree.php');
}

/*****
Блок добавления категории
***/
function new_folder_block(){    
	echo '
	<br>
    <a href="javascript:void(0)" class="spoiler-head folded">Создание/удаление каталогов</a>
    <div class="spoiler-body">
	
		<div class="new_cat_upload" style="width:230px;">
		<form action="" method="post">' . mso_form_session('f_session3_id') .
		'<p> <input type="text" style="width:95%; margin-bottom:5px;" name="f_cat_name" value="">
		<input type="submit" name="f_newcat_submit" style="width:100%;" value="'. t('Создать новый каталог', 'admin'). '" onClick="if(confirm(\'' . t('В активном каталоге будет создана новая папка. Продолжить?', 'admin') . '\')) {return true;} else {return false;}" ></p>
		</form></div>
		
		<div class="new_cat_upload" style="width:230px;">
		<form action="" method="post">' . mso_form_session('f_session4_id') .
		'<p> <input type="submit" name="f_delcat_submit" style="width:100%;" value="'. t('Удалить текущий каталог', 'admin'). '" onClick="if(confirm(\'' . t('Текущий каталог вместе со всеми подкаталогами и файлами будет удален. Продолжить?', 'admin') . '\')) {return true;} else {return false;}" ></p>
		</form></div>
		
	</div>';
}

/*****
Блок загрузки файлов
***/
function upload_block($allowed_types, $current_dir){
	global $MSO;
	$allowed_types = str_replace ("|", ",", $allowed_types);

	$resize_images = (int) mso_get_option('resize_images', 'general', 600);
	if ($resize_images < 1) $resize_images = 600;
	
	$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 150);
	if ($size_image_mini < 1) $size_image_mini = 150;

	$watermark_type = mso_get_option('watermark_type', 'general', 1);
	
	$mini_type = mso_get_option('image_mini_type', 'general', 1);	

	echo '<form id="file_upload" action="" method="post">' . mso_form_session('f_session_id2');
	echo '<input name="upl_message" id="upl_message" type="hidden" value=""></form>';

	echo
		'<div class="upload_file" style="margin-left:270px;">
		<h2>' . t('Загрузка файлов', 'admin') . '</h2>
		<p>' . t('Добавьте файлы в список и нажмите кнопку "Start upload". Максимальный размер одного ф ', 'admin') . ' ' . ini_get  ('upload_max_filesize') . '.</p>
		<input type="hidden" id="f_session2_id" value="'. $MSO->data['session']['session_id'] .'">
		<p>
		';
	require($MSO->config['plugins_dir'] . 'file_manager/uploader/selector.php'); 
	
	echo '
    <a href="javascript:void(0)" class="spoiler-head folded">Настройки обработки рисунков</a>
    <div class="spoiler-body">
		<p>' . t('Описание файла:', 'admin') . ' <input type="text" id="f_userfile_title" class="description_file" value=""></p>

		<p><label><input type="checkbox" id="f_userfile_resize" checked="checked" value=""> ' . t('Для изображений изменить размер до', 'admin') . '</label>
			<input type="text" id="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).', 'admin') . '</p>

		<p><label><input type="checkbox" id="f_userfile_mini" checked="checked" value=""> ' . t('Для изображений сделать миниатюру размером', 'admin') . '</label>
			<input type="text" id="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '"> ' . t('px (по максимальной стороне).', 'admin') . ' <br><em>' . t('Примечание: миниатюра будет создана в каталоге', 'admin') . ' <strong>uploads/' . $current_dir . 'mini</strong></em></p>


		<p>' . t('Миниатюру делать путем:', 'admin') . ' <select id="f_mini_type">
		<option value="1"'.(($mini_type == 1)?(' selected="selected"'):('')).'>' . t('Пропорционального уменьшения', 'admin') . '</option>
		<option value="2"'.(($mini_type == 2)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) по центру', 'admin') . '</option>
		<option value="3"'.(($mini_type == 3)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого верхнего края', 'admin') . '</option>
		<option value="4"'.(($mini_type == 4)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого нижнего края', 'admin') . '</option>
		<option value="5"'.(($mini_type == 5)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого верхнего края', 'admin') . '</option>
		<option value="6"'.(($mini_type == 6)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого нижнего края', 'admin') . '</option>
		<option value="7"'.(($mini_type == 7)?(' selected="selected"'):('')).'>' . t('Уменьшения и обрезки (crop) в квадрат', 'admin') . '</option>
		</select></p>

		<p><label><input type="checkbox" id="f_userfile_water" value="" '.
			((file_exists(getinfo('uploads_dir') . 'watermark.png')) ? '' : ' disabled="disabled"') . 
			((mso_get_option('use_watermark', 'general', 0)) ? (' checked="checked"') : ('')) .
			'> ' . t('Для изображений установить водяной знак', 'admin') . '
			<select id="f_water_type">
			<option value="1"'.(($watermark_type == 1)?(' selected="selected"'):('')).'>' . t('По центру', 'admin') . '</option>
			<option value="2"'.(($watermark_type == 2)?(' selected="selected"'):('')).'>' . t('В левом верхнем углу', 'admin') . '</option>
			<option value="3"'.(($watermark_type == 3)?(' selected="selected"'):('')).'>' . t('В правом верхнем углу', 'admin') . '</option>
			<option value="4"'.(($watermark_type == 4)?(' selected="selected"'):('')).'>' . t('В левом нижнем углу', 'admin') . '</option>
			<option value="5"'.(($watermark_type == 5)?(' selected="selected"'):('')).'>' . t('В правом нижнем углу', 'admin') . '</option>
			</select>
			</label>
			<br><em>' . t('Примечание: водяной знак должен быть файлом <strong>watermark.png</strong> и находиться в каталоге', 'admin') . ' <strong>uploads</strong></em></p>
		</div>
	</div>
	<div style="clear:both;"></div>
<hr class="br"><br>';		
}

/*****
Блок списка файлов + создание галереи
***/
function files_block($allowed_types, $current_dir, $mso_descritions, $show_size){
	global $CI;
	// как выводим файлы
	$admin_view_files = mso_get_option('admin_view_files', 'general', 'mini');
	$admin_sort_files = mso_get_option('admin_sort_files', 'general', 'name_asc');
	
	$ext_w_image = 'mp3|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz';
	$ext_w_image = explode('|', $ext_w_image);
	$ext_imgdir_url = getinfo('plugins_url') . 'file_manager/ext/';
	
	if ($admin_view_files == 'table')
	{
		$CI->load->library('table');
		$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="110">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

		$CI->table->set_template($tmpl); // шаблон таблицы
		// заголовки
		$CI->table->set_heading('&bull;', t('Коды для вставки', 'admin'));
	}

	// проходимся по каталогу аплоада и выводим их списком

	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;

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
	
		
	if ($admin_sort_files == 'name_asc') // по имени
		asort($dirs);
	elseif ($admin_sort_files == 'name_dest') // по имени обратно
		arsort($dirs);
	elseif ($admin_sort_files == 'date_asc') // по дате
		ksort($dirs);
	else 
		krsort($dirs); // по дате обратно


	$allowed_ext = explode('|', $allowed_types);

	$out_all = ''; // весь вывод

	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог

		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

		$cod = '<p>';
		$title = '';
		$title_f = '';
		$size = '';
		
		if($show_size == '1'){
			$size .= ' (';
			if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  ) $size .= getImageSizeInfo($uploads_dir . $file);
			$size .= getFileSizeInfo($uploads_dir . $file);
			$size .= ')';
		}
		
		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file];
			if ($title) $title_f = '<br><em>' . htmlspecialchars($title) . '</em>';
		}

		$sel = form_checkbox('f_check_files[]', $file, false,
			'title="' . htmlspecialchars($title) . '" id="' . mso_strip($file) . '" class="f_check_files"')
			. '<label for="' . mso_strip($file)
			. '"> '
			. $file . $size . $title_f . '</label>';

		$cod1 = stripslashes(htmlspecialchars( $uploads_url . $file ) );

		$cod .= '<a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=4>' . $cod1 . '</textarea>\', \'Адрес файла\'); return false;">Адрес</a>';
		
		$title_alt = str_replace('"', '&amp;quot;', $title);
		$title_alt = str_replace('<', '&amp;lt;', $title_alt);
		$title_alt = str_replace('>', '&amp;gt;', $title_alt);
		$title_alt = str_replace('\'', '&amp;#039;', $title_alt);

		//Если картинка - делаем ссылку превьюшкой, иначе титлом или именем файла.
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  ) 
		{
			$title_alt = str_replace('"', '&amp;quot;', $title);
			$title_alt = str_replace('<', '&amp;lt;', $title_alt);
			$title_alt = str_replace('>', '&amp;gt;', $title_alt);
			$title_alt = str_replace('\'', '&amp;#039;', $title_alt);
			
			if ($title) 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . 'mini/' . $file . '" alt="' . $title_alt . '" title="' . $title_alt . '"></a>') );
			else 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . 'mini/' . $file . '" alt=""></a>') );
		}
		else 
		{
			if ($title) 
				$cod2 = stripslashes(htmlspecialchars( '<a title="' . $title_alt . '" href="' . $uploads_url . $file . '">' . $title . '</a>') );
			else 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		}
      
      
		$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=5>' . $cod2 . '</textarea>\', \'HTML-ссылка файла\'); return false;">HTML-ссылка</a>';

		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) $_f = '_mso_i/' . $file;
			else $_f = $file;

			if (file_exists( $uploads_dir . 'mini/' . $file  ))
				$file_mini = '=' . $uploads_url . 'mini/' . $file;
			else $file_mini = '=' . $uploads_url . $file;


			if ($title)
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ' ' . str_replace('\'', '&#039;', $title) . ']' . $uploads_url . $file . '[/image]') );
			else
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ']' . $uploads_url . $file . '[/image]') );

			$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=6>' . $cod3 . '</textarea>\', \'Код [image] файла\'); return false;">Код [image]</a>';

			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . htmlspecialchars($title) . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . $uploads_url . $_f . '"></a>';
		}
		else
		{
			if (in_array($ext, $ext_w_image))
			{
				$ext_img = ($ext == 'htm') ? 'html' : $ext;
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . $ext_imgdir_url . $ext_img . '.png"></a>';

				if($ext == 'mp3') $cod .= ' | <a href="#"onClick = "jAlert(\'<textarea cols=60 rows=6>' . stripslashes(htmlspecialchars( '[audio=' . $uploads_url . $file . ']') ) . '</textarea>\', \'Код [audio] файла\'); return false;">Код [audio]</a>';

			}
			else
			{
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png"></a>';
			}
		}

		// nicothin добавил:
		$cod .= '<br><a href="#" class="edit_descr_link" onClick="return false;">' . t('Изменить описание', 'admin') . '</a>';
		// конец добавления
		$out_all .= '<div class="cornerz"><div class="wrap">' . $sel . $predpr . $cod . '</div></div>';

		if ($admin_view_files == 'table') $CI->table->add_row($predpr, $sel . $cod);
	}

	// добавляем форму, а также текущую сессию
	if ($out_all != '') 
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		if ($admin_view_files == 'table') 
			echo $CI->table->generate(); // вывод подготовленной таблицы
		else
		{
			echo '<div class="float-parent" style="width:100%">' . $out_all . '<div style="clear:both"></div></div>';
		}

		echo '<p class="br"><input type="submit" name="f_delete_submit" value="' . t('Удалить', 'admin') . '" onClick="if(confirm(\'' . t('Выделенные файы будут безвозвратно удалены! Удалять?', 'admin') . '\')) {return true;} else {return false;}" ></p>
			<p class="br"><input type="button" id="check-all" value="' . t('Инвертировать выделение', 'admin') . '"></p>
			</form>';

		$n = '\n';
		$up = $uploads_url;

		$mess = t('Предварительно нужно выделить файлы для галереи', 'admin');
		$session = mso_form_session('f_session_id');
		$save_button = t('Сохранить', 'admin');

		echo <<<EOF
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
			var file_name = $(this).parent().parent().children(':checkbox').attr('id');
			var old_descr = $(this).parent().parent().children('label').children('em').text();
			var form_code = '<div class="edit_descr" style="width: 100%;" style="display:none"><form action="" method="post">{$session}<input type="hidden" name="f_file_name" value="' + file_name + '"><textarea name="f_file_description" >' + old_descr + '</textarea><br><input type="submit" name="f_edit_submit" value="{$save_button}"></form></div>';
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
				if (!t) { t = this.value; }
				codegal = codegal + '[gal={$up}mini/' + this.value + ' ' + t + ']{$up}'+ this.value +'[\/gal]{$n}';
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
<hr class="br">
EOF;
		echo '<h2 class="br">' . t('Создание галереи', 'admin') . '</h2>
		<p>' . t('Выделите нужные файлы. (У вас должен быть активирован плагин <strong>LightBox</strong>)', 'admin') . '</p>
		<p>' . t('Название:', 'admin') . ' <input type="text" id="gallerycodename" value=""> ' . t('(если нужно)', 'admin') . '<br><input class="br" type="button" id="gallerycodeclick" value="' . t('Генерировать код галереи', 'admin') . '">
		</p>
		<p><textarea id="gallerycode" style="display: none"></textarea>
		';
	}
	else
	{
		echo '<p>' . t('Нет загруженных файлов', 'admin') . '</p>';
	}
}

/*****
Получить размер изображения и файла
***/
function getImageSizeInfo($file){
	if(!file_exists($file)) return;
	
	list($x, $y, $t, $attr) = getimagesize($file);
	return $x . 'x' . $y . "px, ";
}

function getFileSizeInfo($file){
	if(!file_exists($file)) return;
	$bytes = filesize($file);
	
	if ($bytes < 1024) return $bytes.' B';
	elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
	elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
	elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
	else return round($bytes / 1099511627776, 2).' TB';	
}


/*****
Создание нового каталога
***/
function create_new_cat($path){
	# новый каталог - создаем до того, как отобразить навигацию
	if ( $post = mso_check_post(array('f_session3_id', 'f_cat_name', 'f_newcat_submit')) )
	{
		mso_checkreferer();

		$f_cat_name = mso_slug($post['f_cat_name']);

		if (!$f_cat_name)
			echo '<div class="error">' . t('Нужно ввести имя каталога', 'admin') . '</div>';
		else
		{
			$new_dir = $path . $f_cat_name;

			if ( is_dir($new_dir) ) // уже есть
			{
				echo '<div class="error">' . t('Такой каталог уже есть!', 'admin') . '</div>';
			}
			else
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
				echo '<div class="update">' . sprintf(t('Каталог <strong>%s</strong> создан!'), $f_cat_name) . '</div>';
				
				//Очищаем кеш дерева
				mso_flush_cache_mask('fmtree_');
			}
		}
	}
}

/*****
Удаление текущего каталога
***/

function delete_this_cat($current_dir){
	if ( $post = mso_check_post(array('f_session4_id', 'f_delcat_submit')) )
	{
		mso_checkreferer();

		if ($current_dir == '')
			echo '<div class="error">' . t('Нельзя удалять каталог "uploads"', 'admin') . '</div>';
		else{
			$path = getinfo('uploads_dir') . $current_dir;
			removeDirRec($path);
			
			//Очищаем кеш дерева
			mso_flush_cache_mask('fmtree_');
			
			//Переходим на уровень выше
			$current_dir = substr($current_dir, 0, -1);
			$current_dir = substr($current_dir, 0, strrpos($current_dir, "/"));
			
			mso_redirect('admin/file_manager/' . $current_dir);
		}
	}
}

function removeDirRec($dir)
{
    if ($objs = glob($dir . "*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? removeDirRec($obj . '/') : unlink($obj);
        }
    }
    rmdir($dir);
}

/*****
Удаление выделенных файлов
***/
function delete_selected($post, $current_dir){
	global $mso_descritions, $fn_mso_descritions;
	
	if ( $post = mso_check_post(array('f_session_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();

		foreach ($post['f_check_files'] as $file)
		{
			@unlink(getinfo('uploads_dir') . $current_dir . $file);
			@unlink(getinfo('uploads_dir') . $current_dir . '_mso_i/' . $file);
			@unlink(getinfo('uploads_dir') . $current_dir . 'mini/' . $file);

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$file]);
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл
		}
		echo '<div class="update">' . t('Выполнено', 'admin') . '</div>';
	}
}

/*****
Получение путя активного каталога
***/
function getCurDir(){
	$i = 3;
	$f_directory = '';
	while(($seg = mso_segment($i)) != ''){
		$f_directory .= $seg . '/';
		$i++;
	}
	return $f_directory;
}

// вывод кнопок сортировки
function upload_sort_block($sort_fields)
{
	// добавляем форму, а также текущую сессию
  echo '<form action="" method="post">' . mso_form_session('f_session_id3');
	$admin_sort_files = mso_get_option('admin_sort_files', 'general', 'name_asc');
   echo 'Сортировка:';
   foreach ($sort_fields as $key=>$val)
   {
      if ($admin_sort_files == $key) $disabled = ' disabled '; 
      else $disabled = '';
      echo '<input type="submit" name="f_sort_submit[' . $key . ']" value="' . $val . '"' . $disabled . '>';
   }	
   	echo '</form>';

}


?>