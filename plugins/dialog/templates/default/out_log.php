<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	// выведем лог действий пользователя на форуме
	
      $old_log_date  = '';
      
echo'
<script type="text/javascript">
<!--

function list_show(id){
 $("#"+id).fadeToggle();

}


// -->
</script>
';      
      
      foreach ($log as $cur_log)
      {
         $log_date = _dialog_date('j F Y' , $cur_log['log_date']);
         $log_date_id = (int) ('1' . _dialog_date('dmy' , $cur_log['log_date']));	
	
         $log_time = _dialog_date('H:i:s' , $cur_log['log_date']);	
         
         if (isset($cur_log['profile_psevdonim']))
            $log_user = dialog_profile_link($cur_log['log_user_id'], $cur_log['profile_psevdonim'] , $options['profile_slug'] , $siteurl, $options['profile']);
         
         if (isset($options['log_action'.$cur_log['log_value']])) 
             $log_action = $options['log_action'.$cur_log['log_value']];
         else $log_action = 'Unknow Log Value';   
         
         if (!isset($cur_log['discussion_title']))
         {
             if (!$old_log_date) echo '<div><table>';
             echo '<tr><td>' . $log_date . ' в ' . $log_time . '</td>';
         }    
         else
         {
           if ($old_log_date != $log_date)
           {
             if ($old_log_date) echo '</table></div>';
             
             echo NR . '<p><a href="javascript: void(0);" title="' . $options['show_hide'] . '" onclick="javascript:list_show('.$log_date_id.')">' . $log_date . '</a></p><div class="log_day" style="display:none" id="' .$log_date_id . '"><table>';
           }
           echo '<tr><td>' . $log_time . '</td>';
         }
 
         if (isset($cur_log['profile_psevdonim'])) echo '<td>' . $log_user . '</td>';
         echo '<td>' . $log_action . '</td>';
         
         if (isset($cur_log['discussion_title']))
         {
           $comment_link = '<a href="' . $siteurl . $options['goto_slug'] . dialog_get_url($cur_log['discussion_id'] , $cur_log['log_comment_id']) . '" title="' . $options['goto_comment'] . '">' . $cur_log['discussion_title'] . '</a>';
           $edit_link = '<a href="' . $siteurl . $options['comment_slug'] . '/' . $cur_log['log_comment_id'] . '" title="' . $options['do_edit'] . '">#' . $cur_log['log_comment_id'] . '</a>';           
           echo '<td>' . $comment_link . '</td>';
           echo '<td>' . $edit_link . '</td>';
         }
         
         echo '</tr>';
         $old_log_date = $log_date;
      }
      
      echo '</table></div>' . NR;


?>