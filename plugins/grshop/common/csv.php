<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
mso_cur_dir_lang('admin');

/**
 * Функция для перекодировки данных произвольной структуры из кодировки cp1251 в кодировку UTF-8.
 * Функция может работать без использования библиотеки iconv.
 *
 * @param   mixed  $data
 * @return  mixed
 *
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   Nasibullin Rinat, http://orangetie.ru/
 * @charset  ANSI
 * @version  1.0.1
 */
function cp1251_to_utf8_recursive(/*mixed*/ $data)
{
    if (is_array($data))
    {
        $d = array();
        foreach ($data as $k => $v) $d[cp1251_to_utf8_recursive($k)] = cp1251_to_utf8_recursive($v);
        return $d;
    }
    if (is_string($data))
    {
        if (function_exists('iconv')) return iconv('cp1251', 'utf-8//IGNORE//TRANSLIT', $data);
        if (! function_exists('cp1259_to_utf8')) include_once 'cp1259_to_utf8.php';
        return cp1259_to_utf8($data);
    }
    if (is_scalar($data) or is_null($data)) return $data;
    #throw warning, if the $data is resource or object:
    trigger_error('An array, scalar or null type expected, ' . gettype($data) . ' given!', E_USER_WARNING);
    return $data;
}

function parse_csv_file($file, $columnheadings = false, $delimiter = ';', $enclosure = "\"") 
	{
	$row = 1;
	$rows = array();
	$handle = fopen($file, 'r');
	while (($data = fgetcsv($handle , 1000, $delimiter, $enclosure )) !== FALSE ) 
		{
       		if (!($columnheadings == "false") && ($row == 1)) 
			{
			$headingTexts = $data;
			} 
		elseif (!($columnheadings == "false")) 
			{
			foreach ($data as $key => $value) 
				{
				unset($data[$key]);
				$data[$headingTexts[$key]] = $value;
				}
			$rows[] = $data;
			}
		else 
			{
			$rows[] = $data;
			}
		$row++;
		}
	fclose($handle);
	$rows = cp1251_to_utf8_recursive($rows);	
       return $rows;
}

function form_upload_file ($in=array('path_form_action' => '', 'descr_head' => 'Select CSV-FILE for data export', 'name_form_sub' => 'predexport'))
/*
функция выводит страницу загрузки CSV-файла для экспорта таблицы в БД.
*/
{
	$out = '';

	//////form_radio('data_tip', 'pictzip', FALSE).' экспорт архива с картинками<br/>'.///////
	$out .= '<h1 class="content">'.$in['descr_head'].'</h1><br>'.

	form_open_multipart($in['path_form_action']).mso_form_session('f_session_id').
	form_radio('data_tip', 'prod', TRUE).' '.t('экспорт данных о товарах', 'plugins/grshop').'<br/>'.
	form_radio('data_tip', 'cat', FALSE).' '.t('экспорт данных о категориях', 'plugins/grshop').'<br/>'.
	form_radio('data_tip', 'act', FALSE).' '.t(' экспорт данных об акциях', 'plugins/grshop').'<br/><br/>'.


	'<p class="info">'.
	t('Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать', 'plugins/grshop') .' '.ini_get ('post_max_size').'</p>
	<div style="margin: 20px 0; padding: 5px 10px 15px 10px; border: 1px solid gray;">'.
	form_open_multipart($in['path_form_action']).
	form_upload($data = array('name'=>'f_userfile', 'size'=>'80')).NR.'<br />'.
	form_input($data = array('name'=>'delimiter', 'id'=>'delimiter', 'size'=>'2', 'maxlength'=>'1', 'value'=>';', 'class'=>'code')).NR.t(' используемый в файле разделитель', 'admin').NR.'<br />'.
	form_submit($in['name_form_sub'], t('загрузить файл', 'plugins/grshop')).
	form_close().
	'</form></div>';
	return $out;
}

function form_conform_field (	$in=array('path_form_action' => '', 
				'name_form_sub'=>'export',
				'name_tabl_db'=>'',
				'delimiter'=>';',
				'name_title'=>'',
				'data_tip'=>'prod',
				'descr_head'=>'',
				'f_userfile' => '',
				'qfadd' => ''))
/*
функция выводит страницу сопоставления полей базы данных и полей таблицы экспортируемой из CSV-файла
*/
{
	global $MSO;
	global $CI;
	$out = '';
	$plugin_url = $MSO->config['site_admin_url'] . 'grshop';

	//if ($in['descr_head'] == '') $in['descr_head'] = t('сопоставление полей', 'plugins/grshop');
	if  ($in['data_tip'] == 'prod')
		{
		$add_db_table = 'grsh_catprod';
		$arrc = get_array_db ('cat');	//-- считываем таблицу категорий grsh_cat
		$catarr['0']=t('КАТЕГОРИЯ', 'plugins/grshop');
		if ($arrc != FALSE) 
			{
			foreach ($arrc as $row) {$catarr[$row['id_cat']]=$row['name_cat'];};
			};
		$arra = get_array_db ('add');	//-- считываем таблицу характеристик grsh_add
		if ($arra != FALSE) 
			{
			$addarr[0] = t('характ.', 'plugins/grshop');
			foreach ($arra as $row) {$addarr[$row['id_add']]=$row['name_add'];};
			};
		};
	if  ($in['data_tip'] == 'cat')
		{
		$add_db_table = '';		
		};
	if  ($in['data_tip'] == 'act')
		{
		$add_db_table = '';
		};

	$config['allowed_types'] = 'csv|txt';				// разрешенные типы файлов
	$config['upload_path'] = $MSO->config['uploads_dir'].'/';
	$CI->load->library('upload', $config);
	// если была отправка файла, то нужно заменить поле имени с русского на что-то другое
	// это ошибка при копировании на сервере - он не понимает русские буквы
	if (isset($_FILES['f_userfile']['name']) && $_FILES['f_userfile']['name'] != '') 
		{
		$f_temp = $_FILES['f_userfile']['name'];
		// оставим только точку
		$f_temp = str_replace('.', '__mso_t__', $f_temp);
		$f_temp = mso_slug($f_temp); // остальное как обычно mso_slug
		$f_temp = str_replace('__mso_t__', '.', $f_temp);
		$_FILES['f_userfile']['name'] = $f_temp;
		$f_userfile = $_FILES['f_userfile']['name'];
		}
	else
		{
		return 'nofile';
		};
	$res = $CI->upload->do_upload('f_userfile');
	if (!isset($f_userfile)) $f_userfile = $in['f_userfile'];
	$csvfile = $MSO->config['uploads_dir'].$f_userfile;
	$csvarray = parse_csv_file($csvfile, true, $in['delimiter'] );	//получаем массив данных из временного файла
	//----------сопоставление--------------------------------


	$size=sizetable($csvarray);
	$qff=$size['col'];				// количество полей CSV - таблицы
	$fdb_ff = $CI->db->list_fields($in['name_tabl_db']);    // получаем название полей таблицы БД !!!!!!!!!!!!!!!!!
	$size=sizetable($fdb_ff);			// получаем количество полей БД !!!!!!!!!!!!!!!
	$qfdb=$size['full'];				//собственно количество полей БД!!!!!!!!!!!!!!!!!

	$nmfldcsv['0']=t('по умолчанию', 'plugins/grshop');	// это массив с названиями полей CSV-файла
	for ($j=1; $j<=$qff; $j++)
		{
		$nmfldcsv[$j]=$csvarray[0][$j-1];
		};



	$out .= '<h1 class="content">'.t('Сопоставление полей таблиц для экспорта списка', 'plugins/grshop').' '.$in['name_title'].' '.t('из CSV-файла', 'plugins/grshop').'</h1>
	<p class="info">'.t('Выберите для каждого поля базы данных', 'plugins/grshop').' '.$in['name_title'].' '.t('подходящее поле таблицы из csv-файла:', 'plugins/grshop').' '.$f_userfile.'.<br>'.
	t('Для обновления или дополнения существующих данных ОБЯЗАТЕЛЬНО отметьте ключевое поле "Ключ"', 'plugins/grshop').'</p>';

	$out .= form_open($in['path_form_action']).mso_form_session('f_session_id');
	$out .= '<table style="width: 99%; border: none; line-height: 1.4em;">';

	if  ($in['data_tip'] == 'prod')
		{
		$out.= t('Категория').' :'.NR;
		if (isset($catarr)) $out.= form_dropdown('cat', $catarr, '0').NR;
		else 	{
			$out .= ' '.t('список пуст', 'plugins/grshop');
			};
		};
	$out.= 	'<tr><td style="vertical-align: top; padding: 0 0px 0 0;">'.t('Имя поля БД или характеристики', 'plugins/grshop').'</td>'.NR.'
			<td>'.t('CSV-поле или значение', 'plugins/grshop').NR.'</td><td>'.t('Ключ', 'plugins/grshop').NR.'</td><tr>';	
	for($i=1; $i<$qfdb; $i++)
		{
		$out.= 	'<tr><td style="vertical-align: top; padding: 0 0px 0 0;">'.t('Поле БД', 'plugins/grshop').': '.$fdb_ff[$i].'</td>'.NR.'
			<td>'.form_dropdown($fdb_ff[$i], $nmfldcsv, '0').' '.t('или', 'plugins/grshop').' '.form_input($fdb_ff[$i].'_def', '').
			'</td><td>'.form_radio('keyfield', $fdb_ff[$i], 0).NR.'</td><tr>';	
		};

	//--------тут вставка add-полей---------------
	if  ($in['data_tip'] == 'prod')
		{
		$qfadd = $qff - 1; if ($in['qfadd'] > $qff) $qfadd = $in['qfadd'];	// это если надо больше характеристик, чем CSV-полей
		$qfadd++;
		$nmfldcsv['0']=t('csv-поле');	// это массив с названиями полей CSV-файла
		for($f=1; $f<=$qfadd; $f++)
			{
			$out .=  '<tr><td>';
			if (isset($addarr)) $out .= form_dropdown('name_add[]', $addarr, '0').' или '.form_input('name_add_alt[]', '').NR;
			else 	{$out .= 	t('Новая характеристика', 'plugins/grshop').': '.form_input('name_add_alt[]', '').NR;};
			$out .= '</td><td>'.
			form_dropdown('add[]', $nmfldcsv, '0').' '.t('или', 'plugins/grshop').' '.form_input('add_alt[]', '').NR.'</td></td><td></tr>';
			};
		};
	//--------конец вставки add-полей-----------
	//-----------конец сопоставления------------------------

	$out.= '</table>'.
	form_hidden('qfadd', $qfadd).NR.
	form_hidden('f_userfile', $f_userfile).NR.
	form_hidden('data_tip', $in['data_tip']).NR.
	form_hidden('delimiter', $in['delimiter']).NR.''.
	form_submit ('predexport', t('добавить характеристику', 'plugins/grshop') ).
	form_submit ($in['name_form_sub'], t('экспортировать данные', 'plugins/grshop') ).
	form_submit ('canselcsv', t('отменить экспорт', 'plugins/grshop') ).
	form_close().
	t('Предварительный просмотр таблицы из файла', 'plugins/grshop').': '.$f_userfile.
	buildtable($csvarray);			// вывод таблицы предпросмотра
	return $out;
}

function export_csv_db($post)
/**
* ф-ция осуществляет непосредственный экспорт из файла в базу данных
* названия полей файла она получает через post
*/
{
	global $MSO;
	global $CI;
	global $out;
	$CI = & get_instance();

	if  ($post['data_tip'] == 'prod')
		{
		$add_db_table = 'grsh_catprod';
		$nametbldb = 'grsh_prod';
		$catarray = get_array_db('cat');
		$name_id = array_name_id ('cat', $catarray);
		$id_cat = $post['cat'];
		if ($post['cat'] == '0') return 'nocat';	// если категория не отмечена то уходим
		$qfadd = $post['qfadd']; // количество добавочных характеристик
		$addarray = get_array_db('add');	// массив с данными об add (grsh_add)
		if (isset($post['name_add'])) $id_add_arr = $post['name_add'];	// массив из поста с именами характеристик
		$name_add_alt_arr = $post['name_add_alt'];
		$num_csv_name_arr = $post['add'];
		$val_add_const_arr = $post['add_alt'];
		};
	if  ($post['data_tip'] == 'cat')
		{
		$add_db_table = '';
		$nametbldb = 'grsh_cat';	
		};
	if  ($post['data_tip'] == 'act')
		{
		$add_db_table = '';
		$nametbldb = 'grsh_act';
		};

	//----------создаем массив данных из файла--------------
	$csvfile = $MSO->config['uploads_dir'].$post['f_userfile'];
	$delimiter = $post['delimiter'];
	$csvarray = parse_csv_file($csvfile, true, $delimiter );	//создали массив данных из файла
	if (file_exists($csvfile)) unlink($csvfile); 	// если файл существует, удаляем его
	//--------------------------------------------------------------------------------
	$size=sizetable($csvarray);		//количество элементов массива из файла
	$qrf=$size['row'];			//количество строк в файле
	$qff=$size['col'];				// количество полей CSV - таблицы

	$fdb_ff = $CI->db->list_fields($nametbldb);    // получаем название полей таблицы БД
	$size=sizetable($fdb_ff);			// получаем количество полей БД
	$qfdb=$size['full'];				//собственно количество

	$nmfldcsv['0'] = 0;
	for ($j=1; $j<=$qff; $j++)			// массив с названиями CSV - полей 
		{
		$nmfldcsv[$j]=$csvarray[0][$j-1];	// ключ - номер из поста
		};

	$arr_keyfield = FALSE;

	if (isset($post['keyfield']) && $post['data_tip'] == 'prod')	
	//-- тут формируем массив $arr_keyfield[$id] : ключ - id, 
	//-- значение - значение ключевого поля этого товара
		{
		$oper = 1;	//--- это флаг апдейта, для add параметров товара
		//echo $post['keyfield'];
		//$in['descripton'] = '1';
		//$in['add'] = '1';
		//$in['link'] = '';
		//$in['res'] = 'full';
		$in['id_cat'] = $id_cat;
		$in['nocache'] = '1';

		$arrprod = get_arr_prod($in);
		if ($arrprod != FALSE)
			{
			foreach ($arrprod as $id_prod => $prod)
				{
				if (isset($prod[$post['keyfield']])) $arr_keyfield[$id_prod] = $prod[$post['keyfield']];
				}
			}
		}


	for($i=1; $i<$qrf; $i++)			//цикл по количеству строк
		{
		unset ($new);
		for ($j=1; $j<$qfdb; $j++)		//цикл по количеству полей
			{
			//-- присваиваем новому продукту значение из csv-массива или из пост-поля поумолчанию
			if 	($post[$fdb_ff[$j]]!=0 && $csvarray[$i][$post[$fdb_ff[$j]]-1] != '') 	$new[$fdb_ff[$j]]=$csvarray[$i][$post[$fdb_ff[$j]]-1];
			elseif 	($post[$fdb_ff[$j].'_def']!='') {$new[$fdb_ff[$j]]=$post[$fdb_ff[$j].'_def'];}
			};

		if ($arr_keyfield != FALSE  && $id = array_search($new[$post['keyfield']], $arr_keyfield))	//-- если есть вообще ключевые значения
			{
			$CI->db->where('id_'.$post['data_tip'], $id);
			$res=$CI->db->update($nametbldb, $new);	//--ВОТ ТУТ ОБНОВЛЯЕМ ТОВАР!	
			$oper = 1;
			}
		else
			{
			$res=$CI->db->insert($nametbldb, $new);	//--ВОТ ТУТ СОХРАНЯЕМ НОВЫЙ ТОВАР!
			if ($res!=0) $id = $CI->db->insert_id();	//---- теперь знаем id нового товара
			$oper = 0;	//-- это значит что не апдейт....
			}	//--- это то же для апдейта ----

		if  ($post['data_tip'] == 'prod')
			{
			$newcatprod['id_prod'] = $id;
			$newcatprod['id_cat'] = $id_cat;
			if ($oper == 0) $res=$CI->db->insert('grsh_catprod', $newcatprod);	// записали в выбранную категорию если не апдейт

			for ($kh = 0; $kh < $qfadd; $kh++)	// цикл по количеству характеристик из формы
				{
				$id_add = 0;		// инициализир
				if (isset($id_add_arr[$kh]))	$id_add = $id_add_arr[$kh];
				if ($id_add == 0) $id_add = get_id_name($name_add_alt_arr[$kh], 'add', $addarray);
				if ($id_add == 0) $id_add = get_id_name($nmfldcsv[$num_csv_name_arr[$kh]], 'add', $addarray);
				if ($id_add != 0)	// если после всего этого таки выбрана характеристика
					{
					$val_add = $val_add_const_arr[$kh]; if ($num_csv_name_arr[$kh] != 0) $val_add = $csvarray[$i][$num_csv_name_arr[$kh]-1];
					//-- проверяем существование такого параметра------
					$CI->db->where('id_prod', $id);
					$CI->db->where('id_add', $id_add);
					$query = $CI->db->get('grsh_prodadd');	
					$oper = 0;
					if ($query->num_rows() > 0) $oper = 1;    //-- если параметр есть, то флаг апдейта--

					if ($oper == 1)  //-если апдейт, то исправляем старые записи-
						{
						$addp['val_prodadd']=$val_add;
						$CI->db->where('id_prod', $id);
						$CI->db->where('id_add', $id_add);
						$CI->db->update('grsh_prodadd', $addp);
						};
					if ($oper == 0)
						{
						$CI->db->set('id_add', $id_add);
						$CI->db->set('id_prod', $id);
						$CI->db->set('val_prodadd', $val_add);
						$CI->db->insert('grsh_prodadd');
						}
					};
				};
			};
		}	
}
?>