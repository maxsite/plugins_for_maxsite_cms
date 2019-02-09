<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header("Content-type: text/css"); 

$path = getinfo('plugins_url') . 'specialbox/';
$Options = array( 
			'rounded_corners' => 'true', 
			'text_shadow' => 'false', 
			'box_shadow' => 'false', 
			'border_style' => 'solid',
			'margin' => '10px 10px 10px 10px',
			'cb_color' => '000000',
			'cb_caption_color' => 'ffffff',
			'cb_background' => 'f7cdf5',
			'cb_caption_background' => 'f844ee',
			'cb_border_color' => 'f844ee',
			'cb_image' => '',
			'cb_bigImg' => '',
			'bigImg' => 'false',
			'showImg' => 'true' );
$options = mso_get_option('plugin_specialbox', 'plugins', array());
$Options = array_merge($Options, $options);
?>
.stb-alert_box {
	background-color: #FFE7E6;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/alert<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	padding: 5px;
	border: 1px <?php echo $Options['border_style']; ?> #FF4F4A;
}
.stb-download_box {
	background-color: #DFF0FF;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/download<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	padding: 5px;
	border: 1px <?php echo $Options['border_style']; ?> #65ADFE;
}
.stb-grey_box
{
	color: #333333;
	background: #EEEEEE;
	padding: 5px;
	margin: 10px;
	border: 1px <?php echo $Options['border_style']; ?> #BBBBBB;
}
.stb-code_box 
{
	color: #333333;
	background-color: #EEEEEE;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/code<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	padding: 5px;
	border: 1px <?php echo $Options['border_style']; ?> #BBBBBB;
}
.stb-info_box {
	background-color: #E2F8DE;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/info<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	background-position: left top;
	padding: 5px;
	border: 1px <?php echo $Options['border_style']; ?> #7AD975;
}
.stb-warning_box {
	background-color: #FEFFD5;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/warning<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	padding: 5px;
	border: 1px <?php echo $Options['border_style']; ?> #FE9A05;
}
.stb-black_box {
	background-color: #000000;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/earth<?php if($Options['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	background-repeat: no-repeat;
	padding: 5px;
	color: #FFFFFF;
	border: 1px <?php echo $Options['border_style']; ?> #6E6E6E;
}
.stb-black-caption_box {
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/earth.png);
	<?php } ?>
	background-repeat: no-repeat;
	background-color: #333333;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
	border:1px <?php echo $Options['border_style']; ?> #333333;
	border-bottom-width: 0px;
}
.stb-black-body_box {
	background-color: #000000;
	padding: 5px;
	color: #FFFFFF;
    border:1px <?php echo $Options['border_style']; ?> #333333;
	border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}
.stb-alert-caption_box {
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/alert.png);
	<?php } ?>
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #FF4F4A;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
    border:1px <?php echo $Options['border_style']; ?> #FF4F4A;
	border-bottom-width: 0px;
}
.stb-alert-body_box {
	background-color: #FFE7E6;
	padding: 5px;
	color: #333333;
    border:1px <?php echo $Options['border_style']; ?> #FF4F4A;
	border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}
.stb-download-caption_box {
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/download.png);
	<?php } ?>
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #65ADFE;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
    border: 1px <?php echo $Options['border_style']; ?> #65ADFE;
	border-bottom-width: 0px;
}
.stb-download-body_box {
	background-color: #DFF0FF;
	padding: 5px;
	color: #333333;
    border: 1px <?php echo $Options['border_style']; ?> #65ADFE;
    border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}
.stb-info-caption_box {
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/info.png);
	<?php } ?>
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #7AD975;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
	border: 1px <?php echo $Options['border_style']; ?> #7AD975;
	border-bottom-width: 0px;
}
.stb-info-body_box {
	background-color: #E2F8DE;
	padding: 5px;
	color: #333333;
    border: 1px <?php echo $Options['border_style']; ?> #7AD975;
	border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}
.stb-warning-caption_box {
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/warning.png);
	<?php } ?>
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #FE9A05;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
    border: 1px <?php echo $Options['border_style']; ?> #FE9A05;
	border-bottom-width: 0px;
}
.stb-warning-body_box {
	background-color: #FEFFD5;
	padding: 5px;
	color: #333333;
    border: 1px <?php echo $Options['border_style']; ?> #FE9A05;
	border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}
.stb-grey-caption_box
{
	font-weight: bold;
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #BBBBBB;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
    border: 1px <?php echo $Options['border_style']; ?> #BBBBBB;
	border-bottom-width: 0px;
}
.stb-code-caption_box 
{
	font-weight: bold;
	<?php if ($Options['showImg'] === 'true') {?>
	background-image: url(<?php echo $path;?>images/code.gif);
	<?php } ?>    
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
	background-color: #BBBBBB;
	color: #FFFFFF;
	padding: 3px 5px 3px 5px;
    border: 1px <?php echo $Options['border_style']; ?> #BBBBBB;
	border-bottom-width: 0px;
}
.stb-grey-body_box,
.stb-code-body_box 
{
	background-color: #EEEEEE;
	padding: 5px;
	color: #333333;
    border: 1px <?php echo $Options['border_style']; ?> #BBBBBB;
	border-top-width: 0px;
   	margin: <?php echo $Options['margin']; ?>;  
	margin-top: 0px; 
}

<?php if ($Options['rounded_corners'] == "true") { ?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-code_box,
.stb-info_box ,
.stb-warning_box,
.stb-black_box
{
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.stb-black-caption_box,
.stb-alert-caption_box,
.stb-download-caption_box,
.stb-info-caption_box,
.stb-warning-caption_box,
.stb-grey-caption_box,
.stb-code-caption_box
{
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
}
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box,
.stb-code-body_box
{
	-webkit-border-bottom-left-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;
}
<?php 
}
if ( $Options['box_shadow'] == "true" ) { ?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-code_box,
.stb-info_box ,
.stb-warning_box,
.stb-black_box,
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box,
.stb-black-caption_box,
.stb-alert-caption_box,
.stb-download-caption_box,
.stb-info-caption_box,
.stb-warning-caption_box,
.stb-grey-caption_box
{
	-webkit-box-shadow: 3px 3px 3px #888;
	-moz-box-shadow: 3px 3px 3px #888;
	box-shadow: 3px 3px 3px #888;
}	
<?php 
} 

if ($Options['text_shadow'] == "true") {?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-code_box,
.stb-info_box,
.stb-warning_box,
.stb-black_box,
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box
{
	text-shadow: 1px 1px 2px #888;
}
<?php 
}
?>
.stb-black-caption_box { border: 1px <?php echo $Options['border_style']; ?> #6E6E6E; }
.black-body_box { border: 1px <?php echo $Options['border_style']; ?> #000000; }

.stb-alert_box, 
.stb-download_box,
.stb-info_box, 
.stb-warning_box, 
.stb-black_box {  
	margin: <?php echo $Options['margin']; ?>;  

	<?php if ($Options['showImg'] === 'true') { ?>
	padding-left: <?php echo (($Options['bigImg'] === 'true') ? '50' : '25' ); ?>px;
	min-height: <?php echo (($Options['bigImg'] === 'true') ? '40' : '20');?>px;
	<?php } else { ?>
	padding-left: 5px; 
	<?php } ?>
}
.stb-grey_box, .stb-code_box 
{  
	margin: <?php echo $Options['margin']; ?>;  
	padding-left: 5px;
}

.stb-alert-caption_box, 
.stb-download-caption_box, 
.stb-info-caption_box, 
.stb-warning-caption_box,
.stb-black-caption_box {  
	margin: <?php echo $Options['margin']; ?>;  
	margin-bottom: 0px;  
	padding-left: <?php echo (($Options['showImg'] === 'true') ? '25' : '5' ); ?>px;
}
.stb-grey-caption_box
{
	margin: <?php echo $Options['margin']; ?>;  
	margin-bottom: 0px;  
	padding-left: 5px;
}	
.stb-code-caption_box 
{
	margin: <?php echo $Options['margin']; ?>;   
	margin-bottom: 0px;  
	padding-left: 25px;
}	