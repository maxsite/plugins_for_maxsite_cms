<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# function for automatic plugin setup
function smskey_autoload($args = array())
{
	$options_key='plugin_smskey';
	mso_hook_add('content', 'smskey_custom'); # hook for content display
	$options = mso_get_option($options_key, 'plugins', array());
}

# function is executed during plugin uninstallation
function smskey_uninstall($args = array())
{
	$options_key='plugin_smskey';
	mso_delete_option($options_key,'plugins');
	return $args;
}

# function that processes plugin mso_options
function smskey_mso_options() 
{
	$options_key='plugin_smskey';
	 # key, type, massive keys
	mso_admin_plugin_options($options_key,'plugins', 
		array(
			'f1' => array(
							'type' => 'text', # field type in the form
							'name' => t('sms:key id'), 
							'description' => t('sms:key service ID'), 
							'default' => ''
						),
			'f2' => array(
							'type' => 'select', 
							'name' => t('Language'), 
							'description' => t('Select plugin language'),
							'values' => t('0.00||russian # 1.00||english'),  // select instructions as in ini-файлах
							'default' => t('0.00')
						),					
			'f3' => array(
							'type' => 'select', 
							'name' => t('Encoding'), 
							'description' => t('Choose encoding'),
							'values' => t('0.00||UTF-8 # 1.00||Windows-1251'),
							'default' => t('0.00') 
						),	
			'f4' => array(
							'type' => 'text', # field type in the form
							'name' => t('Тег'), 
							'description' => t('A tag that will hide the content'), 
							'default' => ''
						),					
			),
		t('Sms:key plugin setup - hide content'), // title
		t('<p><i>Specify the necessary options.</i></p>')  // information

	);
}

function smskey_hide_content($hidden_text,$k_id,$lang,$enc) {
	### SMS:Key v1.0.6 ###
	$old_ua = @ini_set('user_agent', 'smscoin_key_1.0.6');
	$response = @file("http://key.smscoin.com/key/?s_pure=1&s_key=".$k_id
	."&s_enc=".$enc."&s_pair=".urlencode(substr($_GET["s_pair"],0,10))
	."&s_language=".urlencode(substr($lang,0,10))
	."&s_ip=".$_SERVER["REMOTE_ADDR"]
	."&s_url=".$_SERVER["SERVER_NAME"].htmlentities(urlencode($_SERVER["REQUEST_URI"])));
	if ($response !== false) {
	 if (count($response)>1 || $response[0] != 'true') {
	  return implode("", $response);
	 } else{
	// ****************** CONTENT HIDDEN WITH A KEY ******************
		return $hidden_text;
	 }
	} else die('Failed to request the external server');
	@ini_set('user_agent', $old_ua);
	### SMS:Key end ###
}

# plugin functions
function smskey_custom($text)
{
	$options_key='plugin_smskey';
	
	/* Settings*/
	$options = mso_get_option($options_key, 'plugins', array());
	if(isset($options['f1']))	$result = $options['f1'];
	else  $options['f1']='';
	if(isset($options['f2']))	$lang=$options['f2'];
	if(isset($options['f3']))	$enc=$options['f3'];
	if(isset($options['f4']))	$smstag=$options['f4'];
	if($lang=='0.00')	$lang='russian';
	else $lang='english';
	if($enc=='0.00')	$enc='UTF-8';
	else $enc='Windows-1251';
	$pattern = "@\[".$smstag."(=)?(.*?)\](.*?)\[\/".$smstag."\]@is";
	// замена  [smskey]...[/smskey] тегов
	if (preg_match_all($pattern, $text, $matches))
	{
		for ($i = 0; $i < count($matches[0]); $i++)
		{
			$id = 'id' . rand(100,999);
			$html = '';
			$smshide=smskey_hide_content($matches[3][$i],$result,$lang,$enc);
			$html .= '<div><p>' . $smshide .'</p></div>';
			$text = preg_replace($pattern, $html, $text, 1);
		}

	return $text;
	}
}
