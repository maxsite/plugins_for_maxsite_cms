<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  
$tree_dir = getinfo('plugins_url') . 'taggallery/admin/tree/';
?>



<link rel="STYLESHEET" type="text/css" href="<?php echo $tree_dir; ?>dhtmlxtree.css">
<script  src="<?php echo $tree_dir; ?>dhtmlxcommon.js"></script>
<script  src="<?php echo $tree_dir; ?>dhtmlxtree.js"></script>
<script  src="<?php echo $tree_dir; ?>dhtmlxtree_start.js"></script>

	<div id="treeboxbox_tree" setImagePath="<?php echo $tree_dir; ?>csh_dhx_skyblue/" style="width:250px; height:406px;background-color:#f5f5f5;border :1px solid Silver; overflow:auto; margin-top:5px;">

<?php 	
//	$k = getTreeFromCache();
//	if($k){
//		echo $k;
//	} else {
		$k = getFoldersTree();
//		$cache_key = 'fmtree_' . mso_md5($_SERVER['REQUEST_URI']);
//		mso_add_cache($cache_key, $k, false, true);
		echo $k;	
//	}
?>
	
	</div>		


    <script>
			function tonclick(id){			
				if(id == 'uploads') id = '';
				self.location.href = '<?php echo getinfo('site_admin_url'); ?>taggallery/' + id;
			};

			var tree = dhtmlXTreeFromHTML("treeboxbox_tree"); 
			tree.checkbox = true;
			tree.setOnClickHandler(tonclick);		
	</script>
	
	
<?php
function getFoldersTree(){
	$result = '<xmp container="true">' . NR;
	
	$dir_path = '';
	$sel = treeSelectTag($dir_path );
	$result .= '<item text="uploads" open="1" id="uploads"' .$sel. '>' . NR;	
	
	$result .= scanChildrenDir($dir_path);
	
	$result .=  '</item>
	</xmp>';
	
	return $result;
}

function scanChildrenDir($dir_path){
	$result = '';
	$all_dirs = directory_map(getinfo('uploads_dir') . $dir_path, true);
	asort($all_dirs);

	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $dir_path . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
		{
			$sel = treeSelectTag($dir_path . $d . '/');		
			$result .= '<item text="'. $d . '" im0="folderClosed.gif" im1="folderOpen.gif" im2="folderClosed.gif"' .$sel. ' open="1" id="'. $dir_path . $d . '">';
			$result .= scanChildrenDir($dir_path . $d . '/');
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