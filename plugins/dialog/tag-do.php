<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// этот файл подключить (например, из tag_do) для вывода дискуссий по метке

$discussions = dialog_get_discussions_on_tags(array(htmlspecialchars(mso_segment(2)))); 
if ($discussions)
{
  echo '<h3>Дискуссии на форуме:</h3>';
  echo '<ul class="category">';
  $url = '<li><a href="' . getinfo('siteurl') . 'discussion/';
  foreach ($discussions as $discussion)
    echo $url . $discussion['discussion_id'] . '" title="Перейти к дискуссии">' . $discussion['discussion_title'] . '</li>';
  echo '</ul>';
}

// 
function dialog_get_discussions_on_tags($tags=array())
{
	  $CI = & get_instance();

   $CI->db->select('discussion_id , discussion_title');
   $CI->db->join('dmeta', 'dmeta.meta_id_obj = ddiscussions.discussion_id');
	 $CI->db->where('meta_key', 'tags');
	 $CI->db->where('meta_table', 'ddiscussions');
	 $CI->db->where_in('meta_value', $tags);
	 $query = $CI->db->get('ddiscussions');
	 if ($query->num_rows() > 0) 
	   return $query->result_array();
	 else return false;

}
