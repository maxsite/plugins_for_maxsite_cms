<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<!-- TinyMCE -->
	<script type="text/javascript" src="<?= $editor_config['url'] ?>tiny_mce/tiny_mce_gzip.js"></script>
	<script type="text/javascript">
tinyMCE_GZ.init({
        plugins: "advimage,advlink,fullscreen,media,pagebreak,paste,searchreplace",
       	theme : "advanced",
        skin : "o2k7",
		language : "ru",
        disk_cache : true,
        debug : false,
});
</script>
<script type="text/javascript">
	 tinyMCE.init({
             // General options
             mode: "exact",
             elements : "elm1",
             theme : "advanced",
             skin : "o2k7",
             language : "ru",
             doctype : '<!DOCTYPE HTML>',
             element_format :  "html",
             document_base_url : "<?php getinfo('siteurl') ?>",
             //убираем относительные пути url
			relative_urls : false,
			convert_urls : false,
			remove_script_host : false,
			remove_linebreaks : true,
			// add elFinder icon in input	 
			file_browser_callback : "elFinderBrowser",
		    //===== Плагины ========
            plugins: "advimage,advlink,fullscreen,media,pagebreak,paste,searchreplace",
     //===== Оформление темы ========
             theme_advanced_toolbar_location : "top",
             theme_advanced_toolbar_align : "left",
             theme_advanced_statusbar_location : "bottom",
             theme_advanced_resizing : true,
             theme_advanced_buttons1 : "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect",
             theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,|,image",
             theme_advanced_buttons3 : "hr,|,removeformat,|,sub,sup,|,charmap",
              //добавочные
		 theme_advanced_buttons1_add :"fontselect,fontsizeselect,forecolorpicker,backcolorpicker",
		 theme_advanced_buttons2_add :",|,pasteword,pastetext,|,search,replace",
		 theme_advanced_buttons3_add :"pagebreak,fullscreen,|,help",
		 theme_advanced_font_sizes : "9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px",
		 //* плагин - pagebreak - разрыв страницы
		 pagebreak_separator : "[cut]",
		 // при вставке с блокнота (активная кнопка) в редакторе html небыло <br>
		 paste_text_linebreaktype : "p", 
		  //можно добавить div
       theme_advanced_blockformats : "div,p,address,code,pre,h1,h2,h3,h4,h5,h6",
       //путь к файлу со стилями отображаемые в редакторе
       content_css : src="<?= $editor_config['url'] ?>tiny_mce.css",
               // Style formats
             style_formats : [
                 //имитация br , но необходимо чтобы в основном файле стилей был class br-p с 0 отступами
				 {title : 'br-p', block : 'p', classes : 'br-p'},
                 {title : 'div.center', inline : 'div', classes : 'center'},
                 {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
                 {title : 'Example 1', inline : 'span', classes : 'example1'},
               
             ],
			 
         });
		 
	 
<!-- eLfinder -->
	function elFinderBrowser (field_name, url, type, win) {
    var cmsURL = "<?php getinfo('siteurl') ?>/application/maxsite/plugins/dv_elfinder/elfinder/elfinder_tinymce.php";   
				     if (cmsURL.indexOf("?") < 0) {
                    //add the type as the only query parameter
                    cmsURL = cmsURL + "?type=" + type;
                }
                else {
                    //add the type as an additional query parameter
                    // (PHP session ID is now included if there is one at all)
                    cmsURL = cmsURL + "&type=" + type;
                }

                tinyMCE.activeEditor.windowManager.open({
                    file : cmsURL,
                    title : 'elFinder 2.0',
                    width : 700,  
                    height : 520,
                    resizable : "yes",
                    inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
                    popup_css : false, // Disable TinyMCE's default popup CSS
                    close_previous : "no"
                }, {
                    window : win,
                    input : field_name
                });
                return false;
				} 
		 		 
     </script>
     <!-- /TinyMCE -->
     <form method="post" <?= $editor_config['action'] ?> >
     <?= $editor_config['do'] ?>
     <textarea id="elm1" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
     <?= $editor_config['posle'] ?>
     </form>