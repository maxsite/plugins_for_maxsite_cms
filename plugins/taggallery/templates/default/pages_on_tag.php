<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод страниц по меткам $picture['tags']
// выводятся статьи, получаемые по всем меткам, которые есть у картинки

	$CI = & get_instance();
	$CI->db->select('page_slug, page_title, page_content');
	$CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->where('page_status', 'publish');
	$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
	$CI->db->where('meta_key', 'tags');
	$CI->db->where('meta_table', 'page');
  $CI->db->where('meta_value', $gallery['gallery_name']);
	$CI->db->from('page');
	$CI->db->order_by('page_id', 'random');
	$CI->db->limit($options['similar_posts_count']);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
	   $pages_on_tag = $query->result_array();
	  
	   $out .= $options['similar_posts_start'] . $options['similar_posts_title'] . $options['similar_posts_do'];

	   foreach ($pages_on_tag as $page_on_tag)
	   {
	      extract($page_on_tag);
        $picture = '';
	      if ($page_content)
	      {	    
	         if ($prev = stristr($page_content, "img "))
	          if ($prev2 = stristr($prev, "src"))
	            if ($prev3 = stristr($prev2, "http")) 
	            {
	              $num = explode('"', $prev3);
                if (trim($num[0]))
                {
                 $picture =  trim($num[0]);
                 $picture = '<img src="' . $picture . '" width="' . $options['similar_posts_width'] . '" alt="' . $page_title . '">';
                }
              }
        }
        $link = '<a href="' . getinfo('site_url') . 'page/' . $page_slug . '" title="' . mso_strip($page_title) . '">' . $page_title . '</a>';
		    $out .= str_replace( 
			      array('[link]',	'[image]','[title]'), 
			      array($link, $picture	, $page_title),
			      $options['similar_posts_format']);			  
	   }  
	   $out .= $options['similar_posts_posle'];
     $out .= $options['similar_posts_end'];
   }	



?>