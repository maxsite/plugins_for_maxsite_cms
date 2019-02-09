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
	                        
// заголовок темы
$form .= '<p><strong>' . t('Тема:', __FILE__) . '<span style="color:red;">*</span></strong><br>
	<input name="f_dignity_forum_topic_subject" type="text" value="' . $dignity_forum_topic_subject . '"
	maxlength="70" style="width:50%" required="required"></p>';
	                                
// редактор
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->editor();
	                        
// текст
$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) 
	. '<span style="color:red;">*</span></strong><br>'
	. '<textarea name="f_dignity_forum_topic_text" class="markItUp"
	cols="90" rows="10" value="" required="required">' . $dignity_forum_topic_text . '</textarea></p>';
				
// конец формы
$form .= '<p><input type="submit" class="forum_new_topic_submit" name="f_submit_dignity_forum_topic" 
	value="' . t('Отправить', __FILE__) . '"></p>';
	
$form .= '</form>';
                        
// выводим форму
echo $form;

#end of file