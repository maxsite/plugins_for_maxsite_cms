<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Picture Cleaner
 */
 
?>
<h1>Picture Cleaner</h1>
<p class="info">С помощью данной страницы вы можете произвести поиск всех не используемых в статьях картинок и удалить их. Просто запустите поиск, а потом выделите файлы, которые нужно удалить.<br />
<font color="red"><b>Осторожно:</b> плагин делает очень много запросов в БД во время поиска</font> (запросов примерно в 1-2 раза больше чем файлов в выделенных папках)</p>

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

	
});
</script>

<?php
	if(isset($_POST['proc'])) $proc = $_POST['proc']; else $proc = FALSE;
	$uploads_url = getinfo('uploads_url');
	$uploads_path = getinfo('uploads_dir');
	$show_search = true;
	
	if($proc == "search"){
		//=========== ПОИСК КАРТИНОК ==================
		if(isset($_POST['f_file_type'])) $f_file_type = $_POST['f_file_type']; else $f_file_type = 0;
		if(isset($_POST['f_tables'])) $f_tables = $_POST['f_tables']; else $f_tables = 0;
		if(isset($_POST['f_dir'])) $f_dir = $_POST['f_dir']; else $f_dir[] = 'uploads / ';
		
		
		$list = getImagesList($f_file_type, $f_dir);
		getUnusedImages($list, $f_tables);
		
		if($list){			
			echo "<br />Найдено <b>". sizeof($list) ."</b> не используемых файлов";
			echo '<form method="post"><input type="hidden" name="proc" value="delete">'. mso_form_session('f_session_id');
			echo '<table class="page" border="0" width="100%"><colgroup width="110">
				<tr>
					<th>Файл</th>
					<th>&bull;</th>
				</tr>';
			
			$class_alt = false;
			$img_ext = explode('|', 'gif|jpg|jpeg|png');
			
			foreach ($list as $file)
			{
				if($class_alt) $ins = ' class="alt"'; 
				else $ins = '';	
				
				$filename = substr(strrchr($file, "/"), 1);
				if($filename == ''){
					$filename = $file;
					$file_folder = '';
				} else $file_folder = '' . substr($file, 0, strpos($file, '/') + 1);
				
				if(file_exists($uploads_path . $file_folder . '_mso_i/'. $filename)) $full_url = $uploads_url . $file_folder . '_mso_i/'. $filename;
				else $full_url = $uploads_url . $file;
				
				$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
				if (in_array($ext, $img_ext)){
					echo '<tr><td'. $ins .'><a class="lightbox" href="'. $uploads_url . $file_folder . $filename .'" target="_blank"><img class="file_img" src="'. $full_url .'" title="'. $file .'" alt="'. $filename .'" /></a></td>';
				} else {
					echo '<tr><td'. $ins .'><a href="'. $full_url .'" target="_blank"><img class="file_img" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png" title="'. $file .'" alt="'. $filename .'" /></a></td>';
				}
				echo '<td'. $ins .'><input type="checkbox" name="f_check_files[]" value="'. $file .'" class="f_check_files" checked /><label for="'. $file .'"> '. $filename .'</label></td></tr>';
				
				$class_alt = !$class_alt;
			}
			
			echo '</table>
			<p><input type="button" id="check-all" value="Инвертировать выделение">
			<input type="submit" class="buttons" value="Удалить все отмеченые картинки" onClick="if(confirm(\'Выделенные файы будут безвозвратно удалены! Удалять?\')) {return true;} else {return false;}"></p>			
			</form>';
		} else echo "<br />По заданым критериям ничего не найдено!";
		
	} elseif($proc == "delete"){
		//=========== УДАЛЕНИЕ КАРТИНОК ==================
		if(isset($_POST['f_check_files'])) $f_check_files = $_POST['f_check_files']; else $f_check_files = FALSE;
		
		if($f_check_files){
			$path = getinfo('uploads_dir');
			foreach ($f_check_files as $file){
				//Узнаю папку и имя файла отдельно
				$filename = substr(strrchr($file, "/"), 1);
				if($filename == ''){
					$filename = $file;
					$file_folder = '';
				} else $file_folder = '' . substr($file, 0, strpos($file, '/') + 1);
			
				//Удаляю зверят
				unlink($path . $file);
				if (file_exists($path . $file_folder . '_mso_i/'. $filename)) unlink($path . $file_folder . '_mso_i/'. $filename);
				if (file_exists($path . $file_folder . 'mini/'. $filename)) unlink($path . $file_folder . 'mini/'. $filename);
			}
		}
		
		echo "<br />Удалено <b>". sizeof($f_check_files) ."</b> не используемых файлов";
	} 
	if($show_search){
		//=========== НАЧАЛЬНАЯ СТРАНИЦА ==================
		echo '
		<!--Форма поиска-->
		<form method="post">'. mso_form_session('f_session_id').  '
		<table class="page" border="0" width="100%"><colgroup width="110">
		<tr>
			<th>Настройка</th>
			<th>Значение</th>
		</tr>
		<tr>
			<td>Что искать</td>
			<td>
				<select name="f_file_type">
					<option value="0" >Только картинки</option>
					<option value="1" >Только не картинки</option>
					<option value="2" >Все файлы</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="alt">Какие папки анализировать</td>
			<td class="alt">
				<ul>
					'.getFileFolders().'
				</ul>
			</td>
		</tr>
		<tr>
			<td>Где искать</td>
			<td>
				<select name="f_tables">
					<option value="0" >Только в текстах страниц</option>
					<option value="1" >Только в мета полях</option>
					<option value="2" >Все поля</option>
				</select>
			</td>
		</tr>
		</table>
			<p><input type="hidden" name="proc" value="search"></p>
			<p><input type="submit" class="buttons" value="Начать новый поиск не используемых картинок"></p>
		</form>';
	}
	
	
	//**************************************************************
	//******************** FUNCTIONS *******************************
	//**************************************************************
	
	function getImagesList($f_file_type, $f_dir){
		$all_files = array();
		foreach ($f_dir as $key => $value) $f_dir[$key] = str_replace(' / ', '/', $value) . '/';

		getDirFiles('', $all_files, $f_file_type, $f_dir);		
		return $all_files;
	}
	
	
	function getDirFiles($dir, &$all_files, $f_file_type, $f_dir){
		$CI = & get_instance();
		$CI->load->helper('directory');
		
		switch($f_file_type){
			case 1:
				$allowed_types = 'mp3|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz';
				$allowed_ext = explode('|', $allowed_types);
				break;
			case 2:
				$allowed_types = 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz';
				$allowed_ext = explode('|', $allowed_types);
				break;
			default:
				$allowed_types = 'gif|jpg|jpeg|png';
				$allowed_ext = explode('|', $allowed_types);
		}

		$path = getinfo('uploads_dir');
		$files = directory_map($path . $dir, true);
		foreach ($files as $file)
		{
			if (is_dir($path . $file) AND ($file == '_mso_float' OR $file == 'mini' OR $file == '_mso_i' OR $file == 'smiles')) continue;
			
			if (is_dir($path . $file)) {					
				getDirFiles($dir . $file . '/', $all_files, $f_file_type, $f_dir);
				continue;
			}			
			
			$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
			if (!in_array($ext, $allowed_ext) ) continue;
			if (!in_array('uploads/' . $dir, $f_dir)) continue;
			
			$all_files[] = $dir . $file;
		}
	}
	
	function getUnusedImages(&$list, $f_tables){
		$CI = & get_instance();
		
		foreach ($list as $key => $value){
			switch($f_tables){
				case 2: //Все поля
				case 0: //Тексты
					$CI->db->select('page_id');
					$CI->db->where("page_content LIKE '%uploads/$value%'");
					$query = $CI->db->get('page');
					
					if ($query->num_rows()){
						unset($list[$key]);
						break;
					}
					if($f_tables == 0) break;
				case 1: //Мета
					$CI->db->select('meta_id');
					$CI->db->where("meta_value LIKE '%uploads/$value%'");

					$query = $CI->db->get('meta');

					if ($query->num_rows()) unset($list[$key]);
					break;
			}			
			
			
		}
	}
	
	function getFileFolders(){
		$folders = array();
		$folders[] = 'uploads / ';
		getJustFolder('', $folders);
		
		$result = '';
		foreach ($folders as $dir){
			$result .= '<li><input type="checkbox" name="f_dir[]" value="'. $dir .'" checked > '. $dir .'</li>' . "\r\n";
		}
		return $result;
	}
	
	function getJustFolder($dir, &$folders){
		$CI = & get_instance();
		$CI->load->helper('directory');
		
		$path = getinfo('uploads_dir');
		$files = directory_map($path . $dir, true);
		foreach ($files as $file)
		{
			if (is_dir($path . $file) AND ($file == '_mso_float' OR $file == 'mini' OR $file == '_mso_i' OR $file == 'smiles')) continue;
			
			if (is_dir($path . $file)) {	
				$folders[] = 'uploads / ' . $dir . $file;
				getJustFolder($dir . $file . '/', $folders);
			}
		}
	}	
?>