<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле выводятся сообщения что в массиве $comments (вариант вывода номер 1 - развернутый)

// флаг возможности ответа
if (!isset($flag_replay)) $flag_replay = false;
if (!isset($flag_discussion)) $flag_discussion = false;

/*
$out .= '<table width=100%>';
*/

?>
<input type="hidden" id="d_ajax_path" value="<?= $ajax_path ?>">
<input type="hidden" id="d_get_ajax_path" value="<?= $get_ajax_path ?>">
<div class="break"></div>
<div class="comments_list">
<table class="disc-comm">
<?php
$i = 0; // счетчик комментов текущей страницы пагинации
$cur_pag = mso_current_paged();
// тогда номер сообщения вообще будет $pag * $vur_pag + $i

// массив номеров комментов на текущей странице пагинации
$id_in_page = array_keys($comments);

  // стиль коммента
  if ($comuser['profile_font_size']) $comment_style = 'style="font-size: ' . $comuser['profile_font_size'] . 'px"';
  else $comment_style=''; 
$comm_count = count($comments);
foreach ($comments as $key=>$comment)
{
  $i++;
  $comment_no = ($cur_pag - 1) * $comuser['profile_comments_on_page'] +$i;

  extract ($comment);
  
  // формируем постоянную ссылку на коммент
  $comment_url = $siteurl . $options['goto_slug'] . '/disc/' . $comment_discussion_id . '/comm/' . $comment_id;
  $comment_no = '<a href="' . $comment_url . '" title ="' . $options['comment_true_link'] . '">#' . $comment_no . '</a>';

  
    $fn = 'out_comment_do1.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn); 
          
  // если задана ушка рекламы между комментами  
  // и текущий номер коммента в массиве номеров для вывоа рекламы
  if ( (in_array($i , $options['adds_forum'])) and function_exists('ushka') and ($adds=ushka('adds_forum'))) 
  {
?>  
   <tr>
     <td class="comment_user">
     </td>
     <td class="comment_info">
     <?= $adds ?>
     </td>        
   </tr>   
<?php
  }
    // выводим переменные, подготовленные в out_comment_do1.php в сетку

?>
 <tr> 
 
   <td class="comment_user" valign="top">
     <div class="user_link"><?= $profile_link ?></div> 
     <div class="user_avatar">
            <?= dialog_avatar($comment , '') ?>
     </div> 
     <div class="user_info">
        <p class="user_register"><?= $register_date ?></p> 
        <p class="user_comments_count"><?= $user_comments_count ?></p> 
        <p class="user_rate"><?= $user_rate ?></p> 
        <p class="user_dankes"><?= $user_dankes ?></p> 
        <p class="user_links"><?= $user_links ?></p> 
        
     </div>
   </td>
   
   <td class="comment_info">
    <table id="key<?= $comment_id ?>" class="table_comment">
     <tr>
      <td>
       <div class="comment_top">
          <span class="comment_date"><?= $comment_date ?></span>
          <span class="comment_title"><?= $title_add . $out_discussion_title ?></span>
          <span class="comment_edit"><?= $edit ?></span>
          <span class="comment_bad"><?= $button_bad ?></span>
          <span class="comment_no"><?= $comment_no ?></span>
       </div>
       <div class="select_disc" id="commdisc<?= $comment_id ?>"></div>
       </td>
     </tr> 
     <tr>
       <td class="tc">
         <div class="comment_status"><?= $comment_status ?></div>
         <div class="comment_parent" id="parent-<?= $comment_id ?>"><?= $out_parent ?></div>
         <div id="comment-<?= $comment_id ?>" class="comment_comment" <?= $comment_style ?> ><?= $comment_content ?></div>
         <div class="comment_answers" id="answers-<?= $comment_id ?>"><?= $out_answer ?></div>
         <div class="comment_child_disc"><?= $out_child_disc ?></div>
      </td>        
    </tr>
    <tr>
       <td class="bottom">
         <table class="comment_footer">
           <tr>
             <td class="comment_user_podpis"><?= $profile_podpis ?></td>
           </tr>
           <tr>
             <td class="comment_actions">
               <span class="votes"><?= $button_vote ?></span><span class="dankes"><?= $button_danke ?></span>
               <span class="actions"><?= $comment_actions ?></span>
             </td>
           </tr>  
           <tr>
             <td>
               <div id="parent_form-<?= $comment_id ?>" class="parent_form"></div>
             </td>
           </tr>      
           <tr>     
             <td>    
              <table class="comment_bottom"><tr>
               <td width="20%"><span class="navi_buttons"><?= $navi_block ?></span></td>
               <td width="50%"><div class="comment_perelinks"><?= $out_comment_perelinks ?>
               <td width="30%"><div class="comment_danke"><?= $comment_dankes ?></div></td>
              </tr></table>   
            </td>
          </tr> 
         </table>
       </td>
     </tr>
   </table>
   
  </td>
 </tr>      
<?php
}
?>
</table>
</div>
<div class="break"></div>

<!-- end -->