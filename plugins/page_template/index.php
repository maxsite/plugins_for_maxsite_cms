<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function page_template_autoload($args = array())
{	
	mso_hook_add('admin_page_form_add_all_meta', 'page_template_page_form_add_all_meta');
	mso_hook_add('new_page', 'page_template_custom_new');
	mso_hook_add('edit_page', 'page_template_custom_edit');
}

function page_template_activate($args = array())
{
 
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function page_template_deactivate($args = array())
{
	//mso_delete_option('plugin_page_template', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function page_template_uninstall($args = array())
{

		return $args;
}

function page_template_head($args = array())
{
	
  return $args;
}

function page_template_page_form_add_all_meta($args = array())
{
  
  $page_id = mso_segment(3);
  $page_publ_google="1";
  $CI = & get_instance();
  $CI->db->select('*');
  $CI->db->where('page_id',$page_id);
  $query = $CI->db->get('page');
  foreach ($query->result_array() as $row)
   	{      	
    $page_template=$row['page_template'];
    }
  
  
  if (!isset($page_template)) $page_template="0";
  if ($page_template=="1") $f_page_template="checked"; else $f_page_template="";
  
  global $page_template;
  $GLOBALS['page_template'] = '<div><h3>Оформление новости</h3><input class="checkbox" name="f_page_template" value="1" type="checkbox" '.$f_page_template.'> Использовать свой шаблон для новости</div>';	
	
  return $args;
}

function page_template_custom_new($args = array()){
	$id = $args[0];
	

  //print_r($args); 
  //$out="-----------------------";

 // do_it($id);

	return $args;
}

function page_template_custom_edit($args = array())
{
 

	//$id = mso_segment(3);
	//if (!is_numeric($id)) $id = false; // не число
	//else $id = (int) $id;

  //do_it($id);

	return $args;
}

/** helper fuctions */


function page_template_do_it($id)
{
	
}

function page_template_resize_img(&$CI,$fn_old,$fn_new,$size)
{        

}

function page_template_insert_meta($id,&$CI,$fn,$fn_small)
{

}

// for normal use multi-dimensional $_FILES array with CI uploader library
function page_template_format_files_array($files, $first_key = 'page_template_serv_filename'){
		return $new;
}


function page_template_slug($slug)
{

		// таблица замены
		$repl = array(
		"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
		"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
		"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
		"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
		"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
		"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
		"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

		"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
		"е"=>"e", "ё"=>"jo", "ж"=>"zh",
		"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
		"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
		"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
		"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
		"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

		# украина
		"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
		"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",

		"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
		"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

		"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

		"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
		"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
		"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
		"'"=>"",

		);

		$slug = strtolower(strtr(trim($slug), $repl));

	return $slug;
}


?>