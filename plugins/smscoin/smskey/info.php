<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$options_key='plugin_smskey';
$options = mso_get_option($options_key, 'plugins', array());
if(isset($options['f4']))	$smstag=$options['f4'];

$info = array(
	'name' => t('smskey', __FILE__),
	'description' => t('Cмс оплата скрытого текста. Для скрытия контента, используйте теги <b>['.$smstag.'] ... [/'.$smstag.']</b> ', __FILE__),
	'version' => '0.1',
	'author' => 'Denis L',
	'plugin_url' => 'http://smscoin.com/',
	'author_url' => 'mailto: denis.l@smscoin.com',
	'group' => 'template',
	#'help' => getinfo('plugins_url') . 'pluginX/help.txt', # ссылка на help плагина 
);

# end file
