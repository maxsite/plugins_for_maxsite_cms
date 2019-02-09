<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


if ( $post = mso_check_post(array('data')) )
{
	$output = $post['data'];
	/*
	$output = trim($output);
	$output = str_replace(chr(10), "<br>", $output);
	$output = str_replace(chr(13), "", $output);
				
	$output = mso_hook('content', $output);
	$output = mso_hook('content_auto_tag', $output);
	$output = mso_hook('content_balance_tags', $output);
	$output = mso_hook('content_out', $output);
	$output = mso_hook('content_content', $output);
	
	$output = mso_hook('content_content', $output);
*/

 // нам понаобятся опции
	$options = mso_get_option('dialog', 'plugins', array());
  $plugin_dir = getinfo('plugins_dir') . 'dialog/';
  require($plugin_dir . 'plugin_options_default.php');// дефолтые опции
  
  require	($plugin_dir . 'functions/functions.php');
  // id комюзера
  $comuser = is_login_comuser();
  if ($comuser) $options['comment_creator_id'] = $comuser['comusers_id']; else $options['comment_creator_id'] = 0;
	dialog_comment_to_out($output, $options);
	
	$css_link = getinfo('plugins_url') . 'dialog/editors/markitup/preview.css';
	$css_link2 = getinfo('plugins_url') . 'dialog/templates/' . $options['template'] . '/css1.css';
	
	echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
	<title>Предпросмотр</title>
	<meta http-equiv="X-UA-Compatible" content="IE=8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="generator" content="MaxSite CMS">
	<link rel="stylesheet" href="{$css_link}" type="text/css" media="screen">
	<link rel="stylesheet" href="{$css_link2}" type="text/css" media="screen">
</head><body>
<div id="all">
	<div class="all-wrap">

{$output}

	</div><!-- div class=class-wrap -->
</div><!-- div id=all -->
</body></html>
EOF;

	
}