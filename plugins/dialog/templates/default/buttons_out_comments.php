<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

  // выведем в $out кнопки для управления внешним видом дискуссии для конкретного пользователя        

  // кнопки вида: развернуть/свернуть
	if ($comuser['profile_vid'] == '1')
		$out .= '<input type="submit" name="dialog_status_profile_submit[vid0]" class="submit" value="' . $options['vid_min'] . '">';  
	else
	    $out .= '<input type="submit" name="dialog_status_profile_submit[vid1]" class="submit" value="' . $options['vid_max'] . '">'; 


  // кнопки кол-ва сообщений на странице: 10/20/30

	if ($comuser['profile_comments_on_page'] == 10) $disabled = ' disabled ';
    else $disabled = ''; 
	$out .= '<input type="submit" name="dialog_status_profile_submit[10]" class="submit" value="10"' . $disabled . '>';  
				   
	if ($comuser['profile_comments_on_page'] == 20) $disabled = ' disabled ';
    else $disabled = '';  
	$out .= '<input type="submit" name="dialog_status_profile_submit[20]" class="submit" value="20"' . $disabled . '>';  
				   
	if ($comuser['profile_comments_on_page'] == 30) $disabled = ' disabled ';
    else $disabled = '';
	$out .= '<input type="submit" name="dialog_status_profile_submit[30]" class="submit" value="30"' . $disabled . '>';  
				  
	// кнопка смены шрифта
	$out .= '<input type="button" class="d_button_font" id="d_font_button" value="˄A˅"  title="' . $options['font_size_mody'] . '"onclick="javascript:font('.$comuser_id.');">'; 

?>