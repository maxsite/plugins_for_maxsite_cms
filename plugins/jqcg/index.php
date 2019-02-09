<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



# функция автоподключения плагина
function jqcg_autoload()
{
	mso_hook_add( 'head', 'jqcg_head');
	mso_hook_add( 'admin_head', 'jqcg_head');
	mso_hook_add( 'content_out', 'jqcg_content'); # хук на вывод контента после обработки всех тэгов
	mso_hook_add('editor_controls_extra_css', 'jqcg_editor_controls_extra_css');
	mso_hook_add('editor_controls_extra', 'jqcg_editor_controls_extra');
	mso_hook_add('editor_markitup_bbcode', 'jqcg_editor_markitup_bbcode');
}


function jqcg_head($args = array()) 
{

	echo mso_load_jquery();

	$url = getinfo('plugins_url') . 'jqcg/';	


	echo <<<EOF
		<link rel="stylesheet" href="{$url}css/jquery.jqcoolgallery.1.8.css">
		<link rel="stylesheet" href="{$url}css/jqcg.css">
		<script src="${url}js/jquery.jqcoolgallery.1.8.js"></script>

<script type="text/javascript">
	$(document).ready (function () {
		
	$('#gallery').jqCoolGallery ({
		galleryTileWidth: 210,				
		galleryTileHeight: 140,
		galleryColumnCount: -3,	
		galleryHoverExpandPx: 3,
		imageAreaWidth: 600,				
		imageAreaHeight: 400,
		imageAreaPadding: '0 15px 0 0',				
		panelAreaWidth: 120,				
		thumbStyle: 'list',					
		thumbWidth: 50,						
		thumbHeight: 50,					
		thumbHelperMaxWidth: 110,			
		thumbHelperMaxHeight: 110,
		forceImageReload: false,
		forceDemandReload: false,
		htmlHome: 'Все<br>галереи',
		homeToolTip: 'вернуться к списку галерей',
		playToolTip: 'запустить слайдшоу',
		pauseToolTip: 'остановить слайдшоу',
		prevGalleryToolTip: 'открыть предыдущую галерею',
		nextGalleryToolTip: 'открыть следующую галерею',
		nextSlideToolTip: 'следующий слайд',	
		prevSlideToolTip: 'предыдущий слайд',
		rewindToolTip: 'первый слайд',
		debug:true							
	});

	$('#gallerywide').jqCoolGallery ({
		galleryTileWidth: 216,				
		galleryTileHeight: 144,
		galleryColumnCount: -4,	
		galleryHoverExpandPx: 3,
		imageAreaWidth: 675,				
		imageAreaHeight:450,
		imageAreaPadding: '0 15px 0 0',				
		panelAreaWidth: 240,				
		thumbStyle: 'grid',
		thumbAreaWidth: -1,					
		thumbWidth: 50,						
		thumbHeight: 50,					
		thumbHelperMaxWidth: 110,			
		thumbHelperMaxHeight: 110,
		forceImageReload: false,
		forceDemandReload: false,
		htmlHome: 'Все галeреи',
		homeToolTip: 'вернуться к списку галерей',
		playToolTip: 'запустить слайдшоу',
		pauseToolTip: 'остановить слайдшоу',
		prevGalleryToolTip: 'открыть предыдущую галерею',
		nextGalleryToolTip: 'открыть следующую галерею',
		nextSlideToolTip: 'следующий слайд',	
		prevSlideToolTip: 'предыдущий слайд',
		rewindToolTip: 'первый слайд',
		debug:true							
	});


});
</script>
EOF;


}

function jqcg_content($text = '')
{
	$preg = array(

'~\[jqcg-galleries\]~si' => '<div id="gallery" class="jqcoolgallery"><ul class="jqcg-gallery" style="opacity:0;">','~\[gallery-jqcg=(.*?) (.*?)\](.*?)\[\/gallery-jqcg\]~si' => '<li data-title="Галерея $1" data-desc="$2" alt="$2"><img src="$3" width="210" height="140" data-desc="Галерея $1" alt="$2" /><ul class="jqcg-viewer-slides">','~\[slide=(.*?) (.*?)\](.*?) (.*?) (.*?) (.*?)\[\/slide\]~si' => '<li><img src="$3" $4 $5 data-thumb="$6" alt="Photo $1 " /><div class="caption"><center><h4>слайд $1</h4></center>$2</div></li>','~\[end-gallery-jqcg\]~si' => '</ul></li>',
'~\[\/jqcg-galleries\]~si' => '</ul></div>','~\[jqcg-gallerieswide\]~si' => '<div id="gallerywide" class=""><ul class="jqcg-gallery" style="opacity:0;">','~\[gallery-jqcg=(.*?) (.*?)\](.*?)\[\/gallery-jqcg\]~si' => '<li data-title="Галерея $1" data-desc="$2" alt="$2">
<img src="$3" width="210" height="144" data-desc="Галерея $1" alt="$2" /><ul class="jqcg-viewer-slides">','~\[slide=(.*?) (.*?)\](.*?) (.*?) (.*?) (.*?)\[\/slide\]~si' => '<li><img src="$3" $4 $5 data-thumb="$6" alt="Photo $1" /><div class="caption"><center><h4>слайд $1</h4></center>$2</div></li>','~\[end-gallery-jqcg\]~si' => '</ul></li>','~\[\/jqcg-gallerieswide\]~si' => '</ul></div>',

	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# интеграция в editor_markitup
function jqcg_editor_markitup_bbcode($args = array()){
	echo <<<EOF

		{separator:'---------------' },	
		
		{name:'Галереи jqcoolGallery', openWith:'[]', className:"jqcg-markitup-icon", dropMenu: [
			{name:'Галереи', openWith:'[html][jqcg-galleries][gallery-jqcg=номер Заголовок]Адрес_миниатюры[/gallery-jqcg]Здесь коды слайдов (см. file_manager)[end-gallery-jqcg][/jqcg-galleries][/html]', className:""},
			{separator:'---------------' },
			{separator:'---------------' },
			{name:'Добавить галерею', openWith:'[gallery-jqcg=номер Заголовок]Адрес_миниатюры[/gallery-jqcg]Здесь коды слайдов (см. file_manager)[end-gallery-jqcg]', className:""},
			{name:'Добавить слайд (демо)', openWith:'[slide=номер(имя) Описание]Адрес_изобр. width="" height="" Адрес_миниатюры[/slide]', className:""},
			{separator:'---------------' },
			{separator:'---------------' },
			{name:'Галереи на всю страницу', openWith:'[html][jqcg-gallerieswide][gallery-jqcg=номер Заголовок]Адрес_миниатюры[/gallery-jqcg]Здесь коды слайдов (см. file_manager)[end-gallery-jqcg][/jqcg-gallerieswide][/html]', className:""},

		]},
EOF;
	return $args;
}
# end file
