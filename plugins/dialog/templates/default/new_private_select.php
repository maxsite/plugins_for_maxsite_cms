<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// выбор членов комнаты 
// вызывается со страницы добавления или редактирования приватной дискуссии
// результат - в чекбоксах

//  $comusers_in_room - комюзеры, которые уже в комнате

 echo '<H3>' . $options['form_members_title'] . '</H3>';

 $get = mso_parse_url_get(mso_url_get());
 if ( isset($get['uid']) and ($get['uid'] != $comuser_id)) 
    $comuser_in_room_id = $get['uid'];
 else 
    $comuser_in_room_id = false;
 
 if (!isset($comusers_in_room)) $comusers_in_room = array(); 
   
 $all_comusers = dialog_get_comusers_room(array('sort_field'=>'profile_comments_count'));
 
 $out_selected = '';
 $out_on_forum = '';
 $out_off_forum = '';
 
echo'
<script type="text/javascript">
<!--

function oofShow(){
 $("#oof").fadeToggle();}

function oonfShow(){
 $("#oonf").fadeToggle();}
 
// -->
</script>
';       
 
 foreach ($all_comusers as $cur)
 {
   if ( ($cur['comusers_id'] == $comuser_id) or in_array($cur['comusers_id'] , $comusers_in_room) ) continue;
 
   if ($cur['comusers_id'] == $comuser_in_room_id) 
        $out_selected .= '<p><input type="checkbox" name="room_members[' . $cur['comusers_id'] . ']" checked="checked">' . $cur['comusers_nik'] . '</p>' . NR;
   elseif (isset($cur['profile_comments_count']))
			  $out_on_forum .= '<p><input type="checkbox" name="room_members[' . $cur['comusers_id'] . ']">' . $cur['comusers_nik'] . '</p>' . NR;
   else
        $out_off_forum .= '<p><input type="checkbox" name="room_members[' . $cur['comusers_id'] . ']">' . $cur['comusers_nik'] . '</p>' . NR;
			  
 }

 if ($out_selected)
 {
   echo '<div class="forum_members">';
   echo '<div class = "users_list">' . $out_selected . '</div>';
   echo '</div>';
 }   
 if ($out_on_forum)
 { 
   echo '<div class="forum_members">';
   echo '<a href="javascript: void(0);" title="' . $options['show_hide'] . '" onclick="javascript:oofShow()">' . $options['welcome_forum_users'] . '</a>'; 
   echo '<div class="users_list" style="display:none" id="oof">' . $out_on_forum . '</div>';
   echo '</div>';
 }   
 if ($out_off_forum)
 {    
   echo '<div class="forum_members">';
   echo '<a href="javascript: void(0);" title="' . $options['show_hide'] . '" onclick="javascript:oonfShow()">' . $options['welcome_no_forum_users'] . '</a>'; 
   echo '<div class="users_list" style="display:none" id="oonf">' . $out_off_forum . '</div>';
   echo '</div>';
 }
  
 

// получим инфу для вывоа чекбоксов выбора комюзеров 
function dialog_get_comusers_room($sort=array())
{
	 $CI = & get_instance();
  
   $CI->db->select('comusers.* , dprofiles.*'); 
	 $CI->db->join('dprofiles', 'dprofiles.profile_user_id = comusers.comusers_id', 'left');
	 
	 if (isset($sort['sort_field']))
	 {
	   if (!isset($sort['sort_order'])) $sort['sort_order'] = 'desc';
	   $CI->db->order_by($sort['sort_field'] , $sort['sort_order']);
	 }
	 
 	 $query = $CI->db->get('comusers');
 	
	 if ($query->num_rows() > 0) 
	 {	    
	    $row = $query->result_array();
	    return $row;    
	 }
	 else return array();     
}

?>