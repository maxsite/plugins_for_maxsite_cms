<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header("Content-type: text/css"); 

$path = getinfo('plugins_url') . 'specialbox/';
$Options = array( 
			'rounded_corners' => 'true', 
			'text_shadow' => 'false', 
			'box_shadow' => 'false', 
			'border_style' => 'solid',
			'margin' => '10px 10px 10px 10px',
			'color' => '000000',
			'caption_color' => 'ffffff',
			'background' => 'f7cdf5',
			'caption_background' => 'f844ee',
			'border_color' => 'f844ee',
			'image' => '',
//			'bigImg' => '',
			'bigImg' => 'false',
			'showImg' => 'true' );

$captionbox = ''; $bodybox = ''; $namebox = ''; $out = '';

$options = mso_get_option('plugin_specialbox', 'plugins', array());
$boxes = mso_get_option('plugin_specialbox_boxes', 'plugins', array());

$Options = array_merge($Options, $options);

if( count($boxes) > 0 ){
foreach ($boxes as $id => $box)
{
echo '	
.stb-' . $box['name'] . '_box 
{
	color: #'. $box['color'] . ';
	background-color: #'. $box['bgcolor'] .';'; 
 	if ($Options['showImg'] === 'true') 
	{ 
echo '
	background-image: url(';  echo (($box['big'] === 'true') ? $box['image'] :  $box['image']) . ');';
	}

echo '
	background-repeat: no-repeat;
	margin: ' .  $Options['margin']. ';
	border: 1px ' .  $Options['border_style']. ' #' .  $box['bcolor']. ';
	';
	if ($Options['showImg'] === 'true') 
	{ 
	echo ' 
	padding-left: '; echo (($box['big'] === 'true') ? '50' : '25' ) . 'px;
	min-height: '; echo (($box['big'] === 'true') ? '50' : '25') . 'px;
	'; } else { 
	echo '
	padding-left: 5px; ';
	}
echo '
}
.stb-' . $box['name'] . '-caption_box 
{
	color: #' .  $box['ccolor']. ';
	font-weight: bold;
	border: 1px ' .  $Options['border_style']. ' #' .  $box['bcolor']. ';
	background-color: #' .  $box['cbgcolor']. ';';
	if ($Options['showImg'] === 'true') 
	{ 
echo '
	background-image: url(' .  $box['image']. ');';
	}
echo '
	background-repeat: no-repeat;
	margin: ' .  $Options['margin']. ';
	margin-bottom: 0px;
	padding-left: '; echo (($Options['showImg'] === 'true') ? '25' : '5' ) . 'px;
}
.stb-' . $box['name'] . '-body_box 
{
	color: #' .  $box['color']. ';
	border: 1px ' .  $Options['border_style']. ' #' .  $box['bcolor']. ';
	background-color: #' .  $box['bgcolor']. ';
	margin: ' .  $Options['margin']. ';
	margin-top: 0px;
}
';
$captionbox .= '.stb-' . $box['name'] . '-caption_box,';
$bodybox .= '.stb-' . $box['name'] . '-body_box,';
$namebox .= '.stb-' . $box['name'] . '_box,';
} //end foreach

$captionbox = substr($captionbox, 0, -1);
$bodybox = substr($bodybox, 0, -1);
$namebox = substr($namebox, 0, -1);

$pattern = 
'
[captionbox] 
{
	border-width: 1px;
	border-bottom-width: 0px;
	padding: 3px 5px 3px 5px;
}
[bodybox]
{
	border-width: 1px;
	border-top-width: 0px;
	padding: 3px 5px 3px 5px;
}
';
if ($Options['rounded_corners'] == "true") 
{
$pattern .= '
[box]  
{
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
[captionbox]  
{
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
}
[bodybox] 
{
	-webkit-border-bottom-left-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;
}';
}
if ( $Options['box_shadow'] == "true" ) 
{
$pattern .= '
[box]
[captionbox]
[bodybox] 
{
	-webkit-box-shadow: 3px 3px 3px #888;
	-moz-box-shadow: 3px 3px 3px #888;
	box-shadow: 3px 3px 3px #888;
}'; 
} 

if ($Options['text_shadow'] == "true") 
{
$pattern .= '
[bodybox] 
{
	text-shadow: 1px 1px 2px #888;
}';
}

$out = str_replace(array('[captionbox]', '[bodybox]', '[box]'),
					array($captionbox, $bodybox, $namebox), $pattern);
echo $out;
} //end if
?>