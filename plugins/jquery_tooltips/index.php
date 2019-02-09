<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function jquery_tooltips_autoload()
{
	mso_hook_add( 'head', 'jquery_tooltips_head');
}

function jquery_tooltips_head($args = array())
{
	mso_load_jquery();
	echo NR . '
	<script type="text/javascript" src="' . getinfo('plugins_url') . 'jquery_tooltips/jquery.tooltip.min.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$("*:not(link,\'a.cboxElement\')[title]").tooltip({ // Исключает тег link и ColorBox
			track: true,
			delay: 0,
			showURL: false,
			fade: 200
		});
		$("a.cboxElement").each(function(){ // Решение Jack Moore, автора ColorBox
			$(this).colorbox({title:$(this).attr(\'title\')});
		}).filter(\'[title]\').tooltip({
			track: true, // Здесь настройки могут быть другими
			delay: 0,
			showURL: false,
			fade: 200
		});
	});
	//]]>
	</script>
	<style type="text/css">
	#tooltip {
		max-width: 400px;
		position: absolute;
		z-index: 9999;
		border: 1px solid #111;
		background-color: #eee;
		padding: 6px 12px;
		opacity: 0.85;
		border-radius: 4px;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
	}
	#tooltip h3 {font-size: 14px; color: #000;} /* font-weight: normal;*/
	</style>
	<!--[if lte IE 6]><style type="text/css">#tooltip {width: 200px;}</style><![endif]-->
	' . NR;
}

?>