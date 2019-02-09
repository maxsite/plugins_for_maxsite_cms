<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
  $segment = mso_strip(mso_segment(4));
  
  if ($segment)
  {
     //проверяем, есть ли источник с таким id
     $CI->db->select('*');
	   $CI->db->where('source_id', $segment);
	   $query = $CI->db->get('source');
	   if ($query->num_rows()>0) // если источник есть
	   {
  	   $row = $query->row_array(1);
	     $source_id = $row['source_id'];
	     $source_name = $row['source_name'];
	     $source_fn = getinfo('plugins_dir') . 'taggallery/admin/import/' . $source_name . '.php';
	     if(file_exists($source_fn)) require($source_fn);
	     else echo '<div class="error">Нет файла источника ' . $source_fn . '</div>';
     }
     else echo '<div class="error">Источник с таким номером отсутствует: ' . $segment . '</div>';
  }
  else
  {
      // новый источник импорта фотографий
     if ( $post = mso_check_post(array('f_import_source', 'f_import_source_submit')) ) //если нажата кнопка изменить обложку
     {
	      mso_checkreferer();
        $import_source_name = $post['f_import_source'];
        // проверим есть ли источник с таким именем
        $CI->db->select('*');
	      $CI->db->where('source_name', $import_source_name);
	      $query = $CI->db->get('source');
	      if ($query->num_rows()>0) // если источник есть
	      {
           echo '<div class="error">Источник с именем ' . $import_source_name . ' существует.</div>';
        }
        else 
        {
          $ins_data = array(
             'source_name'=>$import_source_name ,
             'source_type'=>1,
             'source_dir'=>'',
             'source_link'=>''
             );
          $res = ($CI->db->insert('source', $ins_data)) ? '1' : '0';
		      if ($res) echo '<div class="update">Источник имопрта добавлен.</div>';
		      else '<div class="update">Ошибка добавления.</div>';
        }  
	   }

     // получим все источники импорта
     $CI->db->select('*');
	   $query = $CI->db->get('source');
	   if ($query->num_rows()>0) // если источник есть
	   {
        $sources = $query->result_array(); 
        echo '<H3>Выберите источник импорта:</H3>';		
        foreach ($sources as $source)
        {
           echo '<p><a href="' . $plugin_url . 'import/'. $source['source_id'] . '" class="select">' . $source['source_name'] . '</a></p>';
        }
     }
     else  echo '<H3>Нет источников импорта. Добавьте.</H3>';
      
		$form = '';
		$form .= '<th>' . t('Добавление источников импорта.', 'plugins') . '</th>';
		$form .= '<tr><td>' . t('Имя источника:', 'plugins') . ' </td>' . '<td><input name="f_import_source" type="text" value=""></td></tr>';
		$form .= '<tr><td>' . t('(для каждого источника<br />должен существовать файл<br />содержащий сценарий импорта<br />с именем источника<br />в admin/import/) ', 'plugins') . '</td></tr>';
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<table>';
        echo $form;
        echo '</table>';
		echo '<input type="submit" name="f_import_source_submit" value="' . t('Добавить', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		echo '</form>';

  }//else segment
?>