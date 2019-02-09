<?php 
require_once 'php/confelfinder.php';
// меняем если переименовываем каталоги
$name_plugins = 'dv_elfinder';
$elf_catalog = 'elfinder';
// Не ТРОГАЕМ
define('ELF_URL', 'http://' . $domen . '/application/maxsite/plugins/' . $name_plugins . '/' . $elf_catalog . '/');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>elFinder 2.0</title>

		<!-- jQuery and jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ELF_URL; ?>googleapis/1.8.18_themes_smoothness/jquery-ui.css">
<script type="text/javascript" src="<?php echo ELF_URL; ?>googleapis/jquery_1.7.2/jquery.min.js"></script>
				<script type="text/javascript" src="<?php echo ELF_URL; ?>googleapis/jqueryui_1.8.18/jquery-ui.min.js"></script>

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="js/elfinder.min.js"></script>

		<!-- elFinder translation (OPTIONAL) -->
		<script type="text/javascript" src="js/i18n/elfinder.ru.js"></script>
<script type="text/javascript" src=" http://<? echo $domen ?>/application/maxsite/plugins/dv_editor_tinymce/tiny_mce/tiny_mce_popup.js"></script>

		</head>
	<body>	
		<script type="text/javascript">
  var FileBrowserDialogue = {
    init: function() {
      // Here goes your code for setting your custom things onLoad.
    },
    mySubmit: function (URL) {
      var win = tinyMCEPopup.getWindowArg('window');

      // pass selected file path to TinyMCE
      win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = URL;

      // are we an image browser?
      if (typeof(win.ImageDialog) != 'undefined') {
        // update image dimensions
        if (win.ImageDialog.getImageData) {
          win.ImageDialog.getImageData();
        }
        // update preview if necessary
        if (win.ImageDialog.showPreviewImage) {
          win.ImageDialog.showPreviewImage(URL);
        }
      }

      // close popup window
      tinyMCEPopup.close();
    }
  }

  tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

  $().ready(function() {
    var elf = $('#elfinder').elfinder({
      // set your elFinder options here
      url: 'php/connector.php',  // connector URL
	  lang: 'ru',             // language (OPTIONAL)
      getFileCallback: function(url) { // editor callback
        FileBrowserDialogue.mySubmit(url); // pass selected file path to TinyMCE 
      }
    }).elfinder('instance');      
  });
</script>
		
		
		
		

<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>
