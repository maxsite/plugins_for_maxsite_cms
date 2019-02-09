<?php
	$view = isset($_GET["view"]) ? $_GET["view"] : "";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <script src="/MoxieManager/js/moxman.loader.min.js"></script>
  <script>
    function getUrlParam(name) {
    	var reg = new RegExp('[\?&]' + name + '=([^&]+)', 'i');
    	var match = reg.exec(window.top.location.search);
     
    	if (match && match.length > 1) {
    		return decodeURIComponent(match[1]);
    	} else {
    		return '';
    	}
    }
    
    function returnFile(url) {
     	var callback = getUrlParam('CKEditorFuncNum');
    	window.top.opener.CKEDITOR.tools.callFunction(callback, url);
    	window.top.close();
    	window.top.opener.focus();
    }
    
    function open() {
    	moxman.browse({
    		fullscreen : true,
    		<?php echo $view ? "view : '". $view ."',\r\n" : ""; ?>
    		relative_urls : false,
    		no_host : true,
    		oninsert: function(args) {
    			returnFile(args.focusedFile.path);
    		}
    	});
    }
  </script>
</head>

<body onload="open();">
</body>

</html>