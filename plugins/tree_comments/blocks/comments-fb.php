<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		if (!isset($options['tc_fb_limit'])) $options['tc_fb_limit'] = '10';
		if (!isset($options['tc_fb_width'])) $options['tc_fb_width'] = '660';
		
		echo '<div class="tabs-box'; 
		if (!$options['tc_tabs']) echo 'tabs-visible';
		echo '">';
		echo '<div id="fb-root"></div>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));</script>';
		
		echo '<div class="fb-comments" data-href="'. $url .'" data-num-posts="'. $options['tc_fb_limit'] .'" data-width="'. $options['tc_fb_width'] .'"></div>';
		echo '</div>';
?>