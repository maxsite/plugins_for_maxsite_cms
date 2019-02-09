<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле подготавливается вывод комментов


			
			
  $out_answers = '';
  $out_parent = '';
  $user_links = ''; // ссылки на связь с пользователем
  $title_add =  ''; // добавка к заголовку коммента (который содержит дату)
  $out_child_disc = ''; //порожденные дискуссии
  
  // информация о пользователе
  $profile_link = dialog_profile_link($comment_creator_id, $profile_psevdonim, $options['profile_slug'] , $siteurl, $options['profile']);
  $user_rate = $options['user_rate'] . $profile_rate;
  
  // формируем ссылку отослать письмо
  // сам себе не может отсылать
  if ($profile_allow_msg and ($comuser_id !=$comment_creator_id)) 
       $user_links .= '<a href="' . $siteurl . $options['profile_slug'] . '/' . $comment_creator_id . '/'. $options['send_email_slug'] . '" title ="' . $options['send_email_title'] . '"><img src="' . $template_url . 'images/email.png" alt="' . $options['send_email_title'] . '"></a>';
  
  
  // формируем ссылку на веб страницу комюзера
  if ($comusers_url) 
     $user_links .= '<a href="' . $comusers_url . '" title ="' . $options['comusers_url'] . '"><img src="' . $template_url . 'images/url.png" alt="' . $options['comusers_url'] . '"></a>';
  
  
  $user_comments_count = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $comment_creator_id . '/all/' . $options['comments_slug'] . '" title ="' . $options['show_comments'] . '">' . $options['user_count'] . $profile_comments_count . '</a>';
  
  // благодарности
  if ($profile_dankes) $user_dankes = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $comment_creator_id . '/' . $options['guds_slug'] . '" title ="' . $options['show_dankes'] . '">' . $options['user_dankes'] . $profile_dankes . '</a>';
  else $user_dankes = '';
  

  
  // перелинковки ______________________________________________________________________
 
 
  // если порожденные дискуссии_________________
  if ($child_disc)
  {
     $count_cd = count($child_disc);
     $out_child_disc = $options['child_disc'];
     foreach ($child_disc as $cur)
     {
        $cur_out_child_disc = '<a href="' . $siteurl . $options['discussion_slug'] . '/' . $cur['discussion_id'] . '" title = "' . $options['goto_child_disc'] . '">' . $cur['discussion_title'] . '</a>';
        
        if ($count_cd>1) $out_child_disc .= $cur_out_child_disc . '; ';
        else $out_child_disc .= $cur_out_child_disc;
     } 
  }
  
  
  // если есть родитель_________________
  if ($parent)
  {
       $out_parent = dialog_perelink_title($comment_id , $parent['comment_id'] , $comment_date_create, $parent['comment_date_create'], $parent['profile_user_id'] , $parent['profile_psevdonim'], $parent['comment_discussion_id'], $id_in_page , $options , array("↓","↑","←","→") , $options['question_title_mask']);
  
     $out_parent .= '<input id="parent_on' . $comment_id . '" type="button" value="' . $options['show_parent']  . '" title="' . $options['show_parent_title'] . '" onClick="showParent(' . $comment_id . ') "><input id="parent_off' . $comment_id . '"  style="display:none" type="button" value="' . $options['hide_parent'] . '" title="' . $options['show_parent_title'] . '" onClick="hideParent(' . $comment_id . ') "></p>
     <div class="parent_out"  id="parent_out' . $comment_id .'" style="display:none"><blockquote>' . $parent['comment_content'] . '</blockquote></div>'; 
     
    $title_add .=  ': ' . $options['title_comment_answer'] . ' ' . $parent['profile_psevdonim'] . ' ';
  }
  

  $out_answer = '';
  // если есть потомки________________
  if ($answers)
  {
     $count_answers = count($answers);
     foreach ($answers as $answer)
     {
       $out_answer .= dialog_perelink_title($comment_id , $answer['comment_id'] , $comment_date_create, $answer['comment_date_create'], $answer['profile_user_id'] , $answer['profile_psevdonim'], $answer['comment_discussion_id'], $id_in_page , $options , array("↓","↑","←","→") , $options['answer_title_mask']);
 
       $out_answer .= '<blockquote>' . $answer['comment_content'] . '</blockquote>';
     }
     $out_answer = '<div class="answers" id="answers' . $comment_id . '"><input id="answers_on' . $comment_id . '" type="button" value="' . $options['show_answers'] . ' (' . $count_answers . ')" title="' . $options['show_answers_title'] . '" onClick="showAnswers(' . $comment_id . ') "><input id="answers_off' . $comment_id . '"  style="display:none" type="button" value="' . $options['hide_answers'] . '" title="' . $options['show_answers_title'] . '" onClick="hideAnswers(' . $comment_id . ') "><div class="answers_out"  id="answers_out' . $comment_id .'" style="display:none">' . $out_answer . '</div></div>';
  
  }
  
    
//конец перелинковкам ____________________________________________________________________ 
  

  
  
  $comment_date = _dialog_date('j F Y в H:i:s' , $comment_date_create);	 
  
  $register_date = _dialog_date('j F Y' , $profile_date_first_visit);	  
	
	// статус и управление комментариями
	$comment_status = '';
	// проверка коммента
	
	if ( ($comuser_role==2) or ($comuser_role==3) )
	{
	  $comment_status .= '<table class="comment_status">';
	  $comment_status .= '<tr><td>';
	  
   // спам - не спам - не проверен
	  if ( $comment_check != '1')
	  {
			 $style_spam = '';
			 $style_not_spam = '';
			 $sp_style_spam = 'display:none';
			 $sp_style_not_spam = 'display:none';	 
		}   
	  elseif ($comment_spam != '0')
	  {
			 $style_spam = 'display:none';
			 $style_not_spam = '';
			 $sp_style_spam = '';
			 $sp_style_not_spam = 'display:none';				 
	  } 
	  else 
	  {
			 $style_spam = '';
			 $style_not_spam = 'display:none';
			 $sp_style_spam = 'display:none';
			 $sp_style_not_spam = 'display:none';				 
	  } 	  
	  	  
    $comment_status .=  '<span style="'.$style_spam .$style_not_spam.'" id="d_result_spam_not_check_'.$comment_id.'">' . $options['not_spam_check'] . '</span>';
    $comment_status .=  '<span style="'.$sp_style_spam .'" id="d_result_spam_'.$comment_id.'">' . $options['spam'] . '</span>';
    $comment_status .=  '<span style="' .$sp_style_not_spam.'" id="d_result_not_spam_'.$comment_id.'">' . $options['not_spam'] . '</span>';
       
			$comment_status .= '<input type="button" class="d_button_spam" style="' . $style_spam . '" id="d_spam_button_' . $comment_id . '" value="' . $options['spam'] . '"  title="' . $options['spam'] . '"onclick="javascript:spam('.$comment_id.','.$comuser_id.');">';  
			$comment_status .= '<input type="button" class="d_button_not_spam" style="' . $style_not_spam . '" id="d_not_spam_button_' . $comment_id . '" value="' . $options['not_spam'] . '"  title="' . $options['not_spam'] . '"onclick="javascript:not_spam('.$comment_id.','.$comuser_id.');">';			
			     
			 $comment_status .=  "<div class=\"d_loader\" id=\"d_loader_spam_check_{$comment_id}\"><img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>"; 	  

	  
	  $comment_status .= '</td><td>';
	  	
	  // разрешить - запретить
	  if ( $comment_approved != '1')
	  {
			 $style_approved = 'display:none';
			 $style_not_approved = '';
		}	 
	  else 
	  {
			 $style_approved = '';
			 $style_not_approved = 'display:none';
		}	 
	  	  
    $comment_status .=  '<span style="'.$style_not_approved .'" id="d_result_approved_'.$comment_id.'">' . $options['not_approved'] . '</span>';
       
			$comment_status .= '<input type="button" class="d_button_approved" style="' . $style_not_approved . '" id="d_approved_button_' . $comment_id . '" value="' . $options['form_approved'] . '"  title="' . $options['form_approved'] . '"onclick="javascript:approved('.$comment_id.','.$comuser_id.');">';  
			$comment_status .= '<input type="button" class="d_button_not_approved" style="' . $style_approved . '" id="d_not_approved_button_' . $comment_id . '" value="' . $options['form_unapproved'] . '"  title="' . $options['form_unapproved'] . '"onclick="javascript:not_approved('.$comment_id.','.$comuser_id.');">';			
			 $comment_status .=  "<div class=\"d_loader\" id=\"d_loader_approved_{$comment_id}\"><img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>"; 	  

	  $comment_status .= '</td><td>';	  
	  	

	  // удалить - восстановить
	  if ( $comment_deleted != '0')
	  {
			 $style_deleted = '';
			 $style_not_deleted = 'display:none';
		}	 
	  else 
	  {
			 $style_deleted = 'display:none';
			 $style_not_deleted = '';
		}	 
	  	  
    $comment_status .=  '<span style="'.$style_deleted .'" id="d_result_deleted_'.$comment_id.'">' . $options['comment_deleted'] . '</span>';
       
			$comment_status .= '<input type="button" class="d_button_deleted" style="' . $style_not_deleted . '" id="d_deleted_button_' . $comment_id . '" value="' . $options['form_delete'] . '"  title="' . $options['form_delete'] . '"onclick="javascript:deleted('.$comment_id.','.$comuser_id.');">';  
			 if ($comuser_role==3) $comment_status .= '<input type="button" class="d_button_not_deleted" style="' . $style_deleted . '" id="d_not_deleted_button_' . $comment_id . '" value="' . $options['form_undelete'] . '"  title="' . $options['form_undelete'] . '"onclick="javascript:not_deleted('.$comment_id.','.$comuser_id.');">';			
			 $comment_status .=  "<div class=\"d_loader\" id=\"d_loader_deleteded_{$comment_id}\"><img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>"; 	  

	  $comment_status .= '</td><td>';	
	  
	  
	  // бан
	  if (!$profile_spam_check)
	  {
	    $style_ban = '';
        $comment_status .=  '<span style="'.$style_ban .'" id="d_result_ban_'.$comment_id.'"></span>';
	    $comment_status .= '<input type="button" class="d_button_ban" id="d_ban_button_' . $comment_id . '" value="' . $options['form_ban'] . '"  title="' . $options['form_ban'] . '"onclick="javascript:ban('.$comment_id.','.$comment_creator_id.','.$comuser_id.');">';  
			// if ($comuser_role==3) $comment_status .= '<input type="button" class="d_button_not_deleted" style="' . $style_deleted . '" id="d_not_deleted_button_' . $comment_id . '" value="' . $options['form_undelete'] . '"  title="' . $options['form_undelete'] . '"onclick="javascript:not_deleted('.$comment_id.','.$comuser_id.');">';			
	    $comment_status .=  "<div class=\"d_loader\" id=\"d_loader_ban_{$comment_id}\"><img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>"; 	  
	  }
	  else $comment_status .= $options['form_ban'];

	  $comment_status .= '</td><td>';		  
	  
	  
	  // флуд - не флуд
	  if ( $comment_flud != '0')
	  {
			 $style_flud = 'display:none';
			 $style_not_flud = '';
		}	 
	  else 
	  {
			 $style_flud = '';
			 $style_not_flud = 'display:none';
		}	 
	  	  
    $comment_status .=  '<span style="'.$style_flud .'" id="d_result_flud_'.$comment_id.'">' . $options['flud'] . '</span>';
       
			$comment_status .= '<input type="button" class="d_button_flud" style="' . $style_not_flud . '" id="d_flud_button_' . $comment_id . '" value="' . $options['flud'] . '"  title="' . $options['flud'] . '"onclick="javascript:flud('.$comment_id.','.$comuser_id.');">';  
			$comment_status .= '<input type="button" class="d_button_not_flud" style="' . $style_flud . '" id="d_not_flud_button_' . $comment_id . '" value="' . $options['not_flud'] . '"  title="' . $options['not_flud'] . '"onclick="javascript:not_flud('.$comment_id.','.$comuser_id.');">';			
			 $comment_status .=  "<div class=\"d_loader\" id=\"d_loader_flud_{$comment_id}\"><img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>"; 	
	  
	  $comment_status .= '</td></tr>';
	  $comment_status .= '</table>';
  }
  else
  {
    // если непроверенный или удаленный коммент просматривает его автор
	  if ($comment_deleted)	$comment_status .= ' <span>' . $options['comment_deleted'] . '</span>';
	  if (!$comment_approved)	$comment_status .= ' <span>' . $options['not_approved'] . '</span>';
	  if ($comment_spam)	$comment_status .= ' <span>' . $options['spam'] . '</span>';    
  }
  // если просматривает автор, модератор или администратор - то показывать ссылку на редактирование коммента
  
  
 	        //может коммент просматривается автором
	        if ($comment_creator_id == $comuser_id) $autor = true;
	        else $autor = false;  
  

$edit = '';	
// если коммент не родительский для данной дискуссии  
if (!isset($comment['parent_for']))
{
  if (($comuser_role == 2) or ($comuser_role == 3))
  $edit = '<a href ="' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '" tite = "' . $options['do_moderate'] . '"><img src="' . $template_url . 'images/edit.png"alt="' . $options['do_moderate'] . '"></a><a class="comment_move" id="comment_move' . $comment_id . '" href="javascript: void(0);" title="' . $options['comm-disc'] . '" onclick="javascript:commDisc('.$comment_id.','.$comuser_id.','.$comment_creator_id.','.$comment_discussion_id.');"><img src="' . $template_url . 'images/comment_move.png" alt="' . $options['comm-disc'] . '"></a>
		<input class="cancel_move" id="cancel_move' . $comment_id . '" type="button" value="Отменить" title="Отменить" onClick="cancelcommDisc('.$comment_id.') ">
		<div class="d_loader" id="d_loader_move_' . $comment_id . '"><img src="'. getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' .'" alt="Идет загрузка…"></div>'; 
		
  elseif ( $autor and ( (time()-$comment_date_create)<$options['allow_edit_time'] ) )
  $edit = '<a href ="' . $siteurl . $options['comment_slug'] . '/' . $comment_id . '" tite = "' . $options['do_edit'] . '"><img src="' . $template_url . 'images/edit.png" alt="' . $options['do_edit'] . '"></a> '; 		
}  
   
					
    

  // что можно делать с сообщением
  $comment_actions = '';

  // если мы не в дискуссии
  if (!$flag_discussion)
    // выводим ссылку перейти к комменту
    $comment_actions .= ' <a href="' . $siteurl . $options['goto_slug'] . '/disc/' .  $comment_discussion_id . '/comm/' . $comment_id . '">' . $options['goto_comment'] . '</a>';

  // если флага ответа
  if ($flag_replay)
  {
       // если кто-то залогинен и флаг ответа, выводим ссылку "Ответить"
     // $comment_actions .= '<p><a href="#form">' . $options['new_post'] . '</a></p>';

       $comment_actions .= '<p><input type="button" id = "button_parent-' . $comment_id . '" value="' . $options['new'] . '" title="' . $options['new_title'] . '" onClick="addPD('.$comment_id.','.$comuser_id.','.$discussion['discussion_category_id'].') "></p>'; 
             
       $comment_actions .= '<p><input type="button" value="' . $options['new_post'] . '" title="' . $options['new_post'] . '" onClick="addNew() "></p>';      
      
       $comment_actions .= '<p><input type="button" value="' . $options['quote'] . '" title="' . $options['quote_title'] . '" onClick="addQuote('. $comment_id .' , \'%AUTOR%\') "></p>';
        
       $comment_actions .= '<p><input type="button" value="' . $options['answer'] . '" title="' . $options['answer_title'] . '" onClick="addAnswer('. $comment_id .' , \'%AUTOR%\') "></p>'; 
       
       $comment_actions = str_replace("%AUTOR%" , $profile_psevdonim , $comment_actions); 
  }

//$button_bad .= '<input type="button" class ="d_button_bad" id="d_bad_button_' . $comment_id . '" value="' . $options['bad'] . '"  title="' . $options['bad_title'] . '"onclick="javascript:bad('.$comment_id.','.$comuser_id.','.$comment_creator_id.');">';

   // сконструируем кнопку жалобы
   $button_bad = '';
   // если пользователь есть и себе жалобу тоже нельзя
   if ($comuser_id and ($comuser_id != $comment_creator_id) )
   {
			$button_bad .= '<a href="javascript: void(0);"  class ="d_button_bad" id="d_bad_button_' . $comment_id . '" title="' . $options['bad_title'] . '" onclick="javascript:bad('.$comment_id.','.$comuser_id.','.$comment_creator_id.');"><img src="'. $template_url . 'images/warning.png" alt="' . $options['bad'] . '"></a>';
			
			$button_bad .=  "<div class=\"d_loader_bad\" id=\"d_loader_bad_{$comment_id}\">
				<img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\">
				<p>Идет загрузка…</p>
			  </div>";
			$button_bad .=  '<span id="d_count_bad_' . $comment_id . '" class="bad_count"></span>';
      $button_bad .=  "<div class=\"d_result_bad\" id=\"d_result_bad_{$comment_id}\"></div>";
   }


  $out_comment_perelinks = '';
  // выведем под сообщением кто цитировал?
  if ($comment_perelinks) // если есть информация
  {  
      $out_comment_perelinks = '<a class="quotes_show" href="javascript: void(0);" title="' . $options['show_hide_who'] . '" onclick="javascript:showQuotes('.$comment_id.');">' . $options['who_quote'] . '</a>(<span class="quotes_count">' . count($comment_perelinks) . '</span>)';
  
  
    $quotes_list = dialog_get_comment_perelinks($comment_id , $comment_date_create , $options , $comment_perelinks , $id_in_page); 
    $out_comment_perelinks .= '<div id="d_q_list_' . $comment_id . '" class="quotes_list">' . $quotes_list . '</div>';
  }
  
  
  
     //показываем список благодарностей
     $comment_dankes = '';
     // показываем кол-во, если есть спасибы
     if ($comment_danke)
     {
        $comment_dankes .= '<a class="danke_show" href="javascript: void(0);" title="' . $options['show_hide_who'] . '" onclick="javascript:showDanke('.$comment_id.');">' . $options['danke_count'] . '</a>(<span class="danke_count" id="d_count_' . $comment_id . '">' . count($comment_danke) . '</span>)';
     
        $danke_list = '';
        if ($comment_danke) 
        {
          $danke_list = '';
          foreach ($comment_danke as $danke)
          { 
             $cur_danke = dialog_profile_link($danke['gud_user_id'], $danke['profile_psevdonim'], $options['profile_slug'] , $siteurl, $options['profile']). '</li>'; 
              if ($danke_list) $danke_list .= '; ' . $cur_danke;
              else $danke_list .= $cur_danke;   
          }     
        }
        $comment_dankes .= '<span id="d_list_' . $comment_id . '" class="danke_list"> : ' . $danke_list . '</span>';
     }
     
     
    
  // сконструируем кнопку спасибо
     $button_danke = '';     
   // если пользователь есть и пользователь не говорил спасибо за этот коммент  
   // себе спасибо тоже говорить нельзя
   if ($comuser_id and ($comuser_id != $comment_creator_id) and !isset($comment_danke[$comuser_id]))
   {
			$button_danke .= '<p><input type="button" class ="d_button_danke" id="d_danke_button_' . $comment_id . '" value="' . $options['danke'] . '"  title="' . $options['danke_title'] . '"onclick="javascript:aaa('.$comment_id.','.$comuser_id.','.$comment_creator_id.');"></p>';
			
			$button_danke .=  "<div class=\"d_loader\" id=\"d_loader_{$comment_id}\">
				<img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>";
			
      $button_danke .=  "<div class=\"d_result\" id=\"d_result_{$comment_id}\"></div>";
   }
   

  // кнопки голосования       
	$button_vote = '';  

   // если пользователь есть и пользователь не голосовал за этот коммент  
   // за свой коммент голосовать нельзя
   if ($comuser_id and ($comuser_id != $comment_creator_id) and !isset($comment_votes_who[$comuser_id]))
   {
     $button_vote .= '<a id="vote_plus_button_' . $comment_id . '" href="javascript: void(0);" title="' . $options['vote_plus_title'] . '" onclick="javascript:vote_plus('.$comment_id.','.$comuser_id.','.$comment_creator_id.');"><img src="'. $template_url . 'images/vote_plus.png" alt="' . $options['vote_plus'] . '"></a><span class="vote_count" id="vote_plus_count_' . $comment_id . '">' . count($comment_votes_plus) . '</span>';  
   
     $button_vote .= '<a id="vote_minus_button_' . $comment_id . '" href="javascript: void(0);" title="' . $options['vote_minus_title'] . '" onclick="javascript:vote_minus('.$comment_id.','.$comuser_id.','.$comment_creator_id.');"><img src="'. $template_url . 'images/vote_minus.png" alt="' . $options['vote_minus'] . '"></a><span class="vote_count" id="vote_minus_count_' . $comment_id . '">' . count($comment_votes_minus) . '</span>';  
     
	   $button_vote .=  "<div class=\"vote_loader\" id=\"vote_loader_{$comment_id}\">
				<img src=\"". getinfo('plugins_url') . 'dialog/templates/default/images/ajax-loader.gif' ."\" alt=\"Идет загрузка…\"></div>";
			
     $button_vote .=  "<div class=\"vote_result\" id=\"vote_result_{$comment_id}\"></div>";     
   }
   else  // голосование невозможно
   {
     // покажем - почему голосование невозможно
     if (isset($comment_votes_who[$comuser_id])) $vote_title = $options['allredy_vote'];
     elseif ($comuser_id == $comment_creator_id) $vote_title = $options['you_comment'];
     else $vote_title = $options['register_for_vote'];
     
   
     $button_vote .= '<img title="' . $vote_title . '" src="'. $template_url . 'images/vote_plus.png" alt="' . $options['vote_plus'] . '"><span class="vote_count" id="vote_plus_count_' . $comment_id . '">' . count($comment_votes_plus) . '</span>';  
   
     $button_vote .= '<img title="' . $vote_title . '" src="'. $template_url . 'images/vote_minus.png" alt="' . $options['vote_minus'] . '"><span class="vote_count" id="vote_minus_count_' . $comment_id . '">' . count($comment_votes_minus) . '</span>';     
   }
   

  // если есть поле discussion_title - значит мы не на странице дискуссии - значит надо вывести ссылку и заголовок дискуссии
  // или если комент из другой дискуссии (родительской для данной)
  $out_discussion_title = '';
  if (isset($discussion_title));
  {
    if ( isset($discussion['discussion_id']) and ($comment_discussion_id != $discussion['discussion_id']) )
       $out_discussion_title = $options['from_disc'] . '<a href="' . $siteurl . $options['goto_slug'] . '/disc/' . $discussion_id  . '/comm/' . $comment_id. '" title = "' . $options['parent_comment'] . '">' . $discussion_title . '</a>';
    elseif (!$flag_discussion) $out_discussion_title = ' >> ' . '<a href = "' . $siteurl . $options['discussion_slug'] . '/' . $comment_discussion_id . '">' . $discussion_title . '</a>';
  }
    
  // блок навигации (если не категория)
  if (!isset($navi_block)) $navi_block = ''; 

   
  
?>



			
