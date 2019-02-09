<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


// эта форма подключается 
// 1 - на главной, для добавления дискуссии без категории и 1-го коммента в нее 
// 2 - на странице категории, для добавления дискуссии в эту категорию  и 1-го коммента в нее
// 3 - на странице дискуссии, для коммента в эту дискуссию
// 4 - на странице создания приватной дискуссии для созздания дискуссии с добавлением 1-го коммента и выбора приглашенных

// $new_discussion_flag = true, если добавляем новую дискуссию
// $free_discussion_flag = true, если добавляем новую дискуссию без категории
// $comuser_in_room_id > 0, если добавляем новую приватную дискуссию - тогда в качестве параметра - номер выбранного пользователя

  if (!isset($comment_comment_content)) $comment_comment_content = '';

?>

<div class="comment-form">
	<form action="" method="post">
		
		<?= mso_form_session('comments_session') ?>
				<input type="hidden" name="comment_email" value="<?= $comuser['comusers_email'] ?>">
				<input type="hidden" name="comment_password" value="<?= $comuser['comusers_password'] ?>">
				<input type="hidden" name="comment_password_md" value="1">
				<input type="hidden" id="parent_id" name="comment_parent_id" value="">

				<?php if (isset($free_discussion_flag) and $free_discussion_flag) echo '<input type="hidden" name="free_discussion_flag" value="1">' ?>
				<?php if (isset($category_id) ) echo '<input type="hidden" name="comment_category_id" value="' . $category_id . '">' ?>
				<?php if (isset($discussion_id)) echo '<input type="hidden" name="comment_discussion_id" value="' . $discussion_id . '">' ?>
				<?php if (isset($comment_id) and $comment_id) echo '<input type="hidden" name="comment_comment_id" value="' . $comment_id . '">' ?>
				


				<div class="comment-form_title">
					<?php
					  if ($form_title) echo '<H3 id ="form">' . $form_title . '</H3><span class="right">' . $link_login . '</span>';
					  if ($form_desc) echo '<p class="desc" id="desc">' . $form_desc . '</p>';
					  ?>
					  <p class="tips" id="tips"></p>
				</div>

				<?php if (isset($new_discussion_flag) and $new_discussion_flag) /*Новая дискуссия  */ { ?>
				   <input type="hidden" name="new_discussion_flag" value="1">
						<table class="no-border">
						<tr class="r1">
							 <td class="t1"><label for="discussion_title" class="discussion_title"><?= $options['form_discussion_title'] ?></label></td>
				       <td class="t2"><input type="text" name="discussion_title" value=""></td>
						</tr>						
					</table>				      
				<?php 
				  if (isset($comuser_in_room_id)) // члены приватной дискуссии
				  {
               $fn = 'new_private_select.php'; 
               if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
                    require($template_dir . $fn);
               else 
                    require($template_default_dir . $fn);
          }
				} ?>        


		<div class="comments-textarea">
		 	<div id="new_comment" style="display:none">
		 	  <p class="you-comment"><label for="comments_new"><?=$options['form_answer']?></label>
		 	  <span class="right"><input name="no_answer" type="button" onClick="noAnswer()" value="<?=$options['no_answer']?>" title="<?=$options['no_answer_title']?>" class="no_answer"></span></p>
			  <div class="answer" id="comments_new"></div>
			</div>
			<p class="you-comment"><label for="comments_content"><?=$options['form_you-comment']?></label> <span class="autosave-editor"></span></p>
			<p class="editor_do"></p>
			<?php
			mso_hook('forum_comments_content_start');
			if ( $options['editor_name'] and file_exists($plugin_dir . 'editors/' . $options['editor_name'] . '/go.php') ) require ($plugin_dir . 'editors/' . $options['editor_name'] . '/go.php');
			else require ($plugin_dir . 'editors/default/go.php');
			 ?>
			<textarea name="comments_content" id="comments_content" rows="10" cols="80"><?= $comment_comment_content ?></textarea>
			<?php 
			   if (isset($comment_form_add)) echo '<span class="right">' . $comment_form_add . '</span>';
			   mso_hook('forum_comments_content_end');
			?>
			  <div>
			    <input id="comment_submit" name="dialog_submit" type="submit" value="<?=$options['form_send']?>" class="comments_submit">

			 </div>
		</div><!-- div class="comments-textarea" -->
	</form>
</div><!-- div class=comment-form -->
