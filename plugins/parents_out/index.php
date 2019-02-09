<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# Возвращает массив дочерних страниц страницы с переданным ID
function parent_sql($parent)
{
 $cache_key = 'parent_sql' . $parent;
 $k = mso_get_cache($cache_key);
 if ($k) return $k; //есть в кэше  
  
  $CI = & get_instance();
  $CI->db->select('page_id_parent, page_id, page_slug, page_title, page_date_publish, page_status, page_content, page_view_count, page_rating, page_rating_count');
  $CI->db->where('page_id_parent', $parent);
  $CI->db->where('page_status', 'publish');
  $CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
  $query = $CI->db->get('page');
  $result = $query->result_array(); // здесь все 
  mso_add_cache($cache_key, $result); // в кэш
  return $result;
}

//возращает путь к странице через связи дочерние-родительские страницы как Хлебные крошки
function parents_out_way_to ($page_id = 0) 
{
  $cache_key = 'parents_out_way_to' . $page_id;
  $k = mso_get_cache($cache_key);
  if ($k) return $k; // да есть в кэше
  $r ='';
  $CI = & get_instance();
  $CI->db->select('page_id, page_id_parent, page_title, page_slug');
  $CI->db->where('page_id', $page_id);
  $CI->db->order_by('page_menu_order');
  $query = $CI->db->get('page');
  $result = $query->result_array(); 

  if ($result)
  {
    foreach ($result as $key=>$page2)
    {
		  extract($page2);
      $r = $page_title;  
      if ($page_id_parent > 0) 
      {
       $parents = parents_out_way_parents($page_id_parent);
       if ( $parents )
        {
          $r = $parents.'->'.$page_title; 
        } 
      }
    }  
  }
 
  mso_add_cache($cache_key, $r); // в кэш

  return $r;
}


//рекурентная для parents_out_way_to
function parents_out_way_parents ($page_id = 0) 
{
  $r = '';
  $CI = & get_instance();
  $CI->db->select('page_id, page_id_parent, page_title, page_slug');
  $CI->db->where('page_id', $page_id);
  $CI->db->order_by('page_menu_order');
  $query = $CI->db->get('page');
  $result = $query->result_array(); 
    
  global $MSO;
  if ($result)
  {
    foreach ($result as $key=>$page2)
    {
         extract($page2);
         $page_link = '<a href="' . $MSO->config['site_url'] . 'page' . '/' . $page_slug . '" title="' . mso_strip($page_title) . '">' . $page_title . '</a>';
         $r = $page_link;  
         if ($page_id_parent>0) 
         {
           $parents = parents_out_way_parents($page_id_parent);
           $r = $parents.'->'.$page_link;  
         }  
    }  
  }
  return $r;
}


# функция автоподключения плагина
function parents_out_autoload($args = array())
{
   mso_hook_add( 'my_content_end', 'parents_out_content_end');

}


# функция вывода пути плагина
function parents_out_way($page_id = 0)
{
   if ($way = parents_out_way_to ($page_id ))
   {
     $do = NR . '<div class="widget widget"><div class="w0"><div class="w1">'; // оформление виджета - начало блока
     $posle = '</div><div class="w2"></div></div></div>' . NR; // оформление виджета - конец блока			  
     echo $do;
     echo $way;
     echo $posle;
   }  
}

//Функция выводит список дочерних страниц с превьюшкой в форме найденного случайно изображения.
//
//
function parents_out_content_end($arg = array())
{
 global $page;
 
 $cur_parent = $page['page_id'];
 //выводим дочерние страницы;
 $flag = false;
 $do = NR . '<div class="widget widget"><div class="w0"><div class="w1">'; // оформление виджета - начало блока
 $posle = '</div><div class="w2"></div></div></div>' . NR; // оформление виджета - конец блока			  

 if ($page_nav = parent_sql($cur_parent))
   {
    foreach ($page_nav as $page2):
      echo $do;
      mso_page_title($page2['page_slug'], $page2['page_title'], '<h4>', '</h4>', true);  
      echo '<table border = "0">';
      echo '<tr>';
      echo '<td>';
	    $content=$page2['page_content'];
      $pic = stristr($content, "img");
	    if ($pic)
      {
	      $pic2 = stristr($pic, "http");
	      if ($pic2) 
	      {
	        $num = explode('"', $pic2);
          if ($num[0])
          {
            $pic = $num[0];
            $image = "<img src = " . $pic . " width = 100 >";
            echo  $image;
            $flag = true;
          }
       }
     }   
     echo '</td>';  
	   if ( preg_match('/\[cut(.*?)?\]/', $content, $matches) )
				{
					$content = explode($matches[0], $content, 2);
					$cut = $matches[1];
				}
				else
				{
					$content = array($content);
					$cut = '';
				}

        $out = $content[0];
        $proverka = stristr($out, "src");	           
	      if ($proverka)
	      {
	        $content2 = explode("</div>", $out);
	        if (isset($content2[1])) $out = $content2[1];
         } 
        echo '<td>';
        echo $out;
	      echo '</td>';
	      echo '</tr>';
        echo '</table>';
        echo $posle;
      endforeach;
    
    }
 	
}

# функция выполняется при деинсталяции плагина
function parents_out_uninstall($args = array())
{	
	 // удалим созданные опции
	return $args;
}




# функции плагина
function parents_out_custom($options = array(), $num = 1)
{
	
}

?>