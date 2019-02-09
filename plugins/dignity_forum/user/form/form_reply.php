<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// начало формы  
$form = '';     
		    
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
					
// редактор
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->editor();
						
$form .= '<textarea name="f_dignity_forum_reply_text" class="markItUp" id="reply"
	cols="80" rows="10" value="" required="required" 
	style="margin-top: 2px; margin-bottom: 2px; ">' . $dignity_forum_reply_text . '</textarea>';
					
// конец формы
$form .= '<p><input type="submit" class="forum_new_reply_submit" name="f_submit_dignity_forum_reply"
	value="' . t('Отправить', __FILE__) . '"></p>';

$form .= '</form>';
		                        
// выводим форму
echo $form;

#end of file