<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

		if (!isset($options['tc_vk_apiid'])) $options['tc_vk_appiid'] = '';
		if (!isset($options['tc_vk_limit'])) $options['tc_vk_limit'] = '20';
		if (!isset($options['tc_vk_width'])) $options['tc_vk_width'] = '660';
		if (!isset($options['tc_vk_init'])) $options['tc_vk_init'] = '1';
	
		echo '<div class="tabs-box'; 
		if (!$options['tc_tabs']) echo 'tabs-visible';
		echo '">';
		echo '<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>';
		
		if ($options['tc_vk_init'])
		echo '<script type="text/javascript">
				VK.init({apiId:'. $options['tc_vk_apiid'] .', onlyWidgets: true});
			</script>';
		echo '<div id="vk_comments"></div>
			<script type="text/javascript">
				VK.Widgets.Comments("vk_comments", {limit: '. $options['tc_vk_limit'] .', width: "'. $options['tc_vk_width'] .'", attach: "*"});
			</script>';
		echo '</div>';
?>