<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



  // редактируем дискуссию
?>

<div class="comment-form">
	<form action="" method="post">
		
		<?= mso_form_session('comments_session') ?>
		
				<input type="hidden" name="comment_email" value="<?= $comuser['comusers_email'] ?>">
				<input type="hidden" name="comment_password" value="<?= $comuser['comusers_password'] ?>">
				<input type="hidden" name="comment_password_md" value="1">
				<input type="hidden" name="discussion_id" value="<?= $discussion_id ?>">
				<div class="comment-form_title">
				<?php
				    
	          $CI = & get_instance();
	          $CI->load->helper('form');
            $CI->db->select('category_id , category_title');
 	          $query = $CI->db->get('dcategorys');
	          $all_categorys = array();
	          if ($query->num_rows() > 0) 
	          {
	             $rows = $query->result_array();
	             foreach ($rows as $row)
	                 $all_categorys[$row['category_id']] = $row['category_title'];
	          }   
	          $all_categorys[0] = $options['title_free_discussions'];

					  echo '<H3>' . $options['title_edit_discussion_form'] . '</H3>';
					  echo '<span class="right">' . $link_login . '</span>';
					  if ($options['desc_edit_discussion_form']) echo '<p>' . $options['desc_edit_discussion_form'] . '</p>';
					  
					  // метки искуссии
					  $tags = implode(', ', dialog_get_tags(array('discussion_id'=>$discussion_id))); // метки в виде массива		
					   
		$f_all_tags = ''; // все метки сайта 
		if (function_exists('tagclouds_widget_custom')) 
		{
			$f_all_tags = '
			<script type="text/javascript">
				function addTag(t)
				{
					var elem = document.getElementById("f_tags");
					e = elem.value;
					if ( e != "" ) { elem.value = e + ", " + t; }
					else { elem.value = t; };
				}
				function shtags(sh)
				{
					var elem1 = document.getElementById("f_all_tags_max_num");
					var elem2 = document.getElementById("f_all_tags_all");
					
					if (sh == 1) 
					{ 
						elem1.style.display = "none"; 
						elem2.style.display = "block"; 
					}
					else
					{
						elem1.style.display = "block"; 
						elem2.style.display = "none"; 				
					}
				}			
			</script>' . NR;
			
			// только первые 20
			$f_all_tags .= tagclouds_widget_custom(array(
				'max_num' => isset($editor_options['tags_count']) ? $editor_options['tags_count'] : 20,
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_max_num">',
				'block_end' => ' <a title="' . t('Показать все метки', 'admin') . '" href="#" onClick="shtags(1); return false;">&gt;&gt;&gt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTag(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));
			
			// все метки
			$f_all_tags .= tagclouds_widget_custom(array(
				'max_num' => 9999,
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_all" style="display: none;">',
				'block_end' => ' <a title="' . t('Показать только самые популярные метки', 'admin') . '" href="#" onClick="shtags(2); return false;">&lt;&lt;&lt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTag(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));
		}						   
		
		// все метки форума 
		if (function_exists('dialog_tagclouds_widget_custom')) 
		{
			// только первые 20
			$f_all_tagsf = dialog_tagclouds_widget_custom(array(
				'max_num' => isset($editor_options['tags_count']) ? $editor_options['tags_count'] : 20,
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_max_numf">',
				'block_end' => ' <a title="' . t('Показать все метки', 'admin') . '" href="#" onClick="shtagsf(1); return false;">&gt;&gt;&gt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTagf(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));
			
			if ($f_all_tagsf) //  если есть метки - продолжаем
			{
			
			// все метки
			$f_all_tagsf .= dialog_tagclouds_widget_custom(array(
				'max_num' => 9999,
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_allf" style="display: none;">',
				'block_end' => ' <a title="' . t('Показать только самые популярные метки', 'admin') . '" href="#" onClick="shtagsf(2); return false;">&lt;&lt;&lt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTagf(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));

			$f_all_tagsf = '
			<script type="text/javascript">
				function addTagf(t)
				{
					var elem = document.getElementById("f_tags");
					e = elem.value;
					if ( e != "" ) { elem.value = e + ", " + t; }
					else { elem.value = t; };
				}
				function shtagsf(sh)
				{
					var elem1 = document.getElementById("f_all_tags_max_numf");
					var elem2 = document.getElementById("f_all_tags_allf");
					
					if (sh == 1) 
					{ 
						elem1.style.display = "none"; 
						elem2.style.display = "block"; 
					}
					else
					{
						elem1.style.display = "block"; 
						elem2.style.display = "none"; 				
					}
				}			
			</script>' . NR . $f_all_tagsf;
			}	
		}		
		 			   
         // возможности модератора и администратора
           if (($comuser_role == 3) or ($comuser_role == 2) )
           {
			       echo '<span class="right">'; 
             if ($discussion_approved) 
                echo '<input name="dialog_status_submit[unapproved]" type="submit" value="' . $options['form_unapproved'] . '" class="comments_submit unapproved">';            else
                echo '<input name="dialog_status_submit[approved]" type="submit" value="' . $options['form_approved'] . '" class="comments_submit approved">'; 

				     if ($discussion_closed == '1')
				          echo '<input type="submit" name="dialog_status_submit[unclosed]" class="submit" value="' . $options['unclosed'] . '">';  
				     else
				          echo '<input type="submit" name="dialog_status_submit[closed]" class="submit" value="' . $options['closed'] . '">';
             echo '</span>';     
           }						  
				?></div>

		<div class="comments-textarea">
			<p class="you-comment"><label for="discussion_title"><?=$options['form_discussion_title']?></label></p>
			<textarea name="discussion_title" id="discussion_title" rows="1" cols="60"><?= $discussion_title ?></textarea>		
			<p class="you-comment"><label for="discussion_desc"><?=$options['form_discussion_desc']?></label></p>
			<textarea name="discussion_desc" id="discussion_desc" rows="3" cols="60"><?= $discussion_desc ?></textarea>
			<?php	 if ($comuser_role == 3) {?>
			<p class="you-comment"><label for="discussion_order"><?=$options['form_discussion_order']?></label></p>
			<textarea name="discussion_order" id="discussion_order" rows="1" cols="60"><?= $discussion_order ?></textarea>	
			<p class="you-comment"><label for="discussion_style_id"><?=$options['form_discussion_discussion_style_id']?></label></p>
			<textarea name="discussion_style_id" id="discussion_style_id" rows="1" cols="60"><?= $discussion_style_id ?></textarea>			
			<?php } ?>
			<p class="you-comment"><label for="select_category_id"><?=$options['form_select_category']?></label>
			<?php	
			  		echo form_dropdown('select_category_id' , $all_categorys , $discussion_category_id) . '</p>';
      ?>
		       </div><!-- div class="comments-textarea" -->
		       <div class="comments-textarea">	      
           <p class="you-comment"><label for="discussion_tags"><?=$options['form_discussion_tags']?></label></p>
			     <textarea name="f_tags" id="f_tags" rows="2" cols="60"><?= $tags ?></textarea>	
			<?php	  
			   if ($f_all_tags)  
			     echo '<H3>Все метки статей сайта:</H3>' . $f_all_tags;
			   if ($f_all_tagsf)   	
           echo '<H3>Все метки форума:</H3>' . $f_all_tagsf; 
           	
		?></div><!-- div class="comments-textarea" -->
		  <div class="comments-textarea"><?php	
			
          if ($discussion_private == '1')  
          {
              // массив id комюзеров, которые уже в комнате
              $comusers_in_room = $members;
              
              $fn = 'new_private_select.php'; 
               if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
                    require($template_dir . $fn);
               else 
                    require($template_default_dir . $fn);          
          }
           
      ?>
		</div><!-- div class="comments-textarea" -->
		<input name="dialog_status_submit[edit]" type="submit" value="<?=$options['form_send']?>" class="comments_submit">
	</form>
</div><!-- div class=comment-form -->
