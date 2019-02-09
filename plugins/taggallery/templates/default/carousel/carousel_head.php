<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

/*
jCarousel Lite 
(jQuery + mouseWheel plugin).
Карусель из картинок + прокрутка колесом мыши.

Взята сдесь
http://www.gmarwaha.com/jquery/jcarousellite/index.php

Описание на Русском
http://ka.nnov.ru/process/jcarousel/

*/
  $plugin_url = getinfo('plugins_url') . 'taggallery/';
  $this_template_url = $plugin_url . 'templates/' . $options['template'] . '/';
  $carousel_url = $this_template_url . 'carousel/';
/*

*/
 echo '
<script type="text/javascript" src="'. $carousel_url . 'js/lib.js"></script>
<script type="text/javascript" src="'. $carousel_url . 'js/jquery.js"></script>
<script type="text/javascript" src="'. $carousel_url . 'js/jquery.mousewheel.min.js"></script>
<script>
$(document).ready(function(){

$(".mouseWheel .jCarouselLite").jCarouselLite({
mouseWheel: true,
    btnNext: ".next",
    btnPrev: ".prev",
    visible: 3
});

});
</script>
';

 echo '<link rel="stylesheet" type="text/css" href="'. $carousel_url . 'css/jsscroll.css">';
  
?>