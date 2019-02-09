<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

  // это поле выводится только у страниц с заданным типом
  

  // получим тип нужных страниц
  if (!isset($row['page_type'])) $row['page_type'] = '';
  if ($row['page_type'])
  {
     $CI = & get_instance();
	   $CI->db->select('page_id, page_slug, page_title , page_type_name ');
	   $CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	   $CI->db->where('page_status', 'publish');
	   $CI->db->where('page_id', $page_id);
	   $CI->db->where('page_type_name', $row['page_type']);
	   $CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	   $CI->db->from('page');
	   $CI->db->order_by('page_id', 'random');
	
	   $query = $CI->db->get();
	
	   if ($query->num_rows() > 0)	// текущая страница нужного типа
	   {	
	      $type_desc = '';
	      foreach ($query->result_array() as $r) 
	        if (isset($r['page_type_name'])) $type_desc = $r['page_type_name'];
  
      
		   	// выясняем какого типа таблица из которой подставляется поле
			  $source_type = $row['source_type'];
			  // какое поле нужно подставлять
			  $result_field = $row['result_field'];
			  // какое поле нужно отображать
			  $lookup_field = $row['lookup_field'];
			
			  // получаем нужные поля нужного имени для всех таблиц нужного типа
			
       $CI = & get_instance();
       $CI->db->select($result_field . ',' . $lookup_field);
	     $CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	     $CI->db->where('page_status', 'publish');
	     $CI->db->where('page_type_name', $source_type);
	     $CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	     $CI->db->from('page');
	     $CI->db->order_by('page_id', 'random');
	
	     $query = $CI->db->get();
	     $lookup_count = $query->num_rows();
//			 $f .= '<p>' . t('Страницы типа ', 'admin') . $type_desc . ' (' . $lookup_count . ')</p>';
			 
	     if ($lookup_count > 0)	// есть страницы
	     {
				  $f .= '<select name="' . $name_f . '">';	     
	        foreach ($query->result_array() as $r)
	        {
					   $val = $r[$result_field];
					   $val_t = $r[$lookup_field];
					   if ($value == $val) $checked = 'selected="selected"';
						   else $checked = '';
						
					   $f .= NR . '<option value="' . $val . '" ' . $checked . '>' . $val_t . '</option>';	      
	         }
	       $f .= NR . '</select>' . NR;
	     }
	     else
	     {
	       //если страниц для выбора нет, тогда выведем как текстовое поле
	       $value = str_replace('_QUOT_', '&quot;', $value);
         $f .= '<input type="text" name="' . $name_f . '" value="' . $value . '">' . NR;
	     }	
     }
   }  
?>