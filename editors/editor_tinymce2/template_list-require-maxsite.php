<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// header('Content-type: text/css');

echo 'var tinyMCETemplateList = [';
if ($handle = opendir(getinfo('plugins_dir').'\editor_tinymce/templates')) {
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != "..") { 
            //echo $file.'<br>'; 
			echo '["'.$file.'", "'.getinfo('plugins_url').'editor_tinymce/templates/'.$file.'", "-"],';
        } 
    }
    closedir($handle); 
}
echo '];';

?>


