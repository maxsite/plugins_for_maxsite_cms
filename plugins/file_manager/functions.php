<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

// общее для всех вариантов загрузчика

	DEFINE('FM_GOOD_RESP', '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	DEFINE('FM_ERROR_RESP', '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ""}, "id" : "id"}');

	$uploader_dir = getinfo('plugins_url') . 'file_manager/uploader/';
	$tree_dir = getinfo('plugins_url') . 'file_manager/tree/';
	
  echo'
  	<link href="' . getinfo('plugins_url') . 'file_manager/style.css" rel="stylesheet" type="text/css">

    <style type="text/css">@import url(' . $uploader_dir . 'css/pageedit-plupload.queue.css);</style>
    <script type="text/javascript" src="' . $uploader_dir . 'js/plupload.full.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/jquery.plupload.queue.min.js"></script>
    <script type="text/javascript" src="' . $uploader_dir . 'js/i18n.js"></script>

	  <link rel="STYLESHEET" type="text/css" href="' . $tree_dir . 'dhtmlxtree.css">
    <script  src="' . $tree_dir . 'dhtmlxcommon.js"></script>
    <script  src="' . $tree_dir . 'dhtmlxtree.js"></script>
    <script  src="' . $tree_dir . 'dhtmlxtree_start.js"></script>
   ';
   
function getFoldersTree(){
	$result = '<xmp container="true">' . NR;
	
	$dir_path = '';
	$sel = treeSelectTag($dir_path );
	$result .= '<item text="uploads" open="1" id="uploads"' .$sel. '>' . NR;	
	
	$options = mso_get_option('plugin_file_manager', 'plugins', array() );		
	$open = (isset($options['tree_expand'])) ? $options['tree_expand'] : '1';
	$open = ($open == '1') ? TRUE : FALSE;
	
	$result .= scanChildrenDir($dir_path, $open);
	
	$result .=  '</item>
	</xmp>';
	
	return $result;
}

function scanChildrenDir($dir_path, $open){
	$result = '';
	$all_dirs = directory_map(getinfo('uploads_dir') . $dir_path, true);
	asort($all_dirs);

	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $dir_path . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
		{
			$sel = treeSelectTag($dir_path . $d . '/');	
			$show_open = ($open === TRUE) ? ' open="1"' : '';
			$result .= '<item text="'. $d . '" im0="folderClosed.gif" im1="folderOpen.gif" im2="folderClosed.gif"' .$sel. $show_open . ' id="'. $dir_path . $d . '">';
			$result .= scanChildrenDir($dir_path . $d . '/', $open);
			$result .=  '</item>' . NR;
		}
	}
	
	return $result;
}

function treeSelectTag($path){
	$path = explode('/', $path);
	$i = 3;
	$result = true;
	
	foreach($path as $segment){
		if($segment != mso_segment($i)){
			$result = FALSE;
			break;
		}
		$i++;
	}
	return $result ? ' select="1"' : '';
}

function getTreeFromCache(){
	$cache_key = 'fmtree_' . mso_md5($_SERVER['REQUEST_URI']);
	$k = mso_get_cache($cache_key, true);
	if($k){
		return $k;
	} else {
		return FALSE;
	}
}


?>