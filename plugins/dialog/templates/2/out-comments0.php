<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле в $out выводятся сообщения что в массиве $comments

// флаг возможности ответа
if (!isset($flag_replay)) $flag_replay = false;
/*
$out .= '<table width=100%>';
*/

?>
<input type="hidden" id="d_ajax_path" value="<?= $ajax_path ?>">
<div class="break"></div>
<div class="comments_list">
<ul>
<?php
$i = 0; // счетчик комментов текущей страницы пагинации
$cur_pag = mso_current_paged();
// тогда номер сообщения вообще будет $pag * $vur_pag + $i
foreach ($comments as $key=>$comment)
{
  $i++;
  $comment_no = ($cur_pag - 1) * $comuser['profile_comments_on_page'] +$i;
  extract ($comment);
  
  require ($template_dir . 'comment_do.php'); 
  
?>
  
 <li id="key<?= $comment_id ?>">
   <div id="<?= $comment_id ?>" class="comment">

    <div class="comment_info">
       <div class="comment_top">
          <span class="comment_date"><?=$profile_link .  ' >> ' . $comment_date ?></span>
          <span class="comment_title"><?= $out_discussion_title ?></span>
          <span class="comment_edit"><?= $edit ?></span>
          <span class="comment_bad"><?= $button_bad ?></span>
          <span class="comment_no"><?= $comment_no ?></span>
       </div>
       <div class="comment_status"><?= $comment_status ?></div>
       <div id="comment-<?= $comment_id ?>" class="comment_comment"><?= $comment_content ?></div>
       <div class="comment_user_podpis"><?= $profile_podpis ?></div>
       <div class="comment_actions"><?= $comment_actions ?></div>
       <div class="comment_danke"><?= $button_danke ?></div>
    </div>
  </div>
 </li>      
<?php
}
?>
</ul>
</div>
<div class="break"></div>

<!-- end -->