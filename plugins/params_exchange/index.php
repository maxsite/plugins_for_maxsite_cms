<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

global $zzz;
$zzz = array();

function params_exchange_autoload()
{
	mso_hook_add( 'content_content', 'params_exchange_custom');
	mso_hook_add('body_start', 'params_exchange_load');
}

function params_exchange_load($content = '')
{
	$file = getinfo('uploads_dir') . 'parameters/parameters.txt';
	if ( file_exists( $file )) {
		$data = file( $file );
		if ( isset($data[0]) and !empty( $data[0]) ) {
			$main_params = explode( "\t", $data[0] );
			if ( !isset( $main_params[1] ) ) return;
			//if ( empty( trim($main_params[0]) ) ) return 0;
			global $zzz;
			$params_data = array();
			foreach ( $data as $idx => $value ) {
				if ( $idx == 0 ) continue;
				$params = explode( "\t", $value );
				if ( isset( $params[1] ) and !empty( $params[1] ) ) {
					$comment = trim($params[0]);
					$slug = trim($params[1]);
					foreach ( $params as $i => $param ) {
						if ( $i > 1 and !empty($param) and !empty( $slug ) ) {
							$params_data[ $slug ][ trim($main_params[ $i ]) ] = trim($param);
						}
					}
				}
			}	
			$zzz = $params_data;
		}
	}
}


function params_exchange_custom($content = '')
{
	$pattern = '~\[parameter=(.*?)\]~si';
	$content = preg_replace_callback($pattern, 'params_exchange_callback', $content);
	return $content;
}

function params_exchange_callback($matches) {
	$out = '';
	if ( !isset($matches[1]) ) return '';
	$m = $matches[1];
	global $zzz;
	global $page;
	$data = isset($zzz[$page['page_slug']]) ? $zzz[$page['page_slug']] : '';
	$out = isset($data[ $m ]) ? mb_convert_encoding($data[ $m ], 'UTF-8', 'WINDOWS-1251')  : '';
	return $out;
}
