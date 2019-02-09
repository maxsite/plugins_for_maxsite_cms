<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


# вывод аватарки комментатора
# на входе массив комментария из page-comments.php
function profile_avatar($comment, $img_add = 'style="float: left; margin: 5px 10px 10px 0;" class="gravatar"', $echo = false, $size = false)
{
	extract($comment);

	$avatar_url = '';
	if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
	elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
	
	if (!$avatar_url) 
	{ 
		// аватарки нет, попробуем получить из gravatara
	   
	        if ($size === false)
		        $avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
	        else
		        $avatar_size = $size;
    	    if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;	
    	    
    	    	
		if ($users_email) $grav_email = $users_email;
		elseif ($comusers_email) $grav_email = $comusers_email;
		else 
		{
			$grav_email = $comments_author_name; // имя комментатора
		}
		
		if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
			$d = '&amp;d=' . urlencode($gravatar_type);
		else 
			$d = '';
		
		if (!empty($_SERVER['HTTPS'])) 
		{
		   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		} 
		else 
		{
		   $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		}
	}
	
	/*
	if ($avatar_url) 
		$avatar_url =  '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" '. $img_add . '>';
	*/
	
	if ($echo) echo $avatar_url;	
		else return $avatar_url;
}


?>