<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	//$options = mso_get_option('diaog_editor_markitup', 'plugins', array() ); // получаем опции
	/*
	   preview: aftertext, dotext, window
	   previewautorefresh: no , yes
	*/
	
  $murktup_dir = getinfo('plugins_dir') . 'dialog/editors/markitup/';
  $murktup_url = getinfo('plugins_url') . 'dialog/editors/markitup/';
  
	
	if (!isset($options['preview'])) $options['preview'] = 'dotext'; // aftertext
	if (!isset($options['previewautorefresh'])) $options['previewautorefresh'] = 'yes';
	
	if ($options['preview'] == 'window') $editor_config['preview'] = 'previewInWindow: "width=960, height=800, resizable=yes, scrollbars=yes",';
	else $editor_config['preview'] = 'previewInWindow: "",';
		
 if ($options['preview'] == 'dotext') $editor_config['previewposition'] = 'previewPosition:	\'do\',';

			
	if ($options['previewautorefresh'] == 'no') $editor_config['previewautorefresh'] = 'previewAutoRefresh: false,';
	else $editor_config['previewautorefresh'] = 'previewAutoRefresh: true,';
	
	
	//$editor_config['url'] = getinfo('plugins_url') . 'editor_markitup/';
//	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_markitup/';

	

	# Приведение строк с <br> в первозданный вид
	$comment_comment_content = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $comment_comment_content);
	$comment_comment_content = preg_replace('"&lt;br&gt;"i', "\n", $comment_comment_content);

	// смайлы - код из comment_smiles
	$image_url=getinfo('uploads_url').'smiles/';
	$CI = & get_instance();
	$CI->load->helper('smiley_helper');
	$smileys=_get_smiley_array();
	$used = array();
	$smiles = '';
	foreach ($smileys as $key => $val)
	{
		// Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
		// показывалась только одна кнопка
		if (isset($used[$smileys[$key][0]]))
		{
		  continue;
		}
		
		$im = "<img src='" . $image_url.$smileys[$key][0] . "' title='" . $key . "'>";
		$smiles .= '{name:"' .  addcslashes($im, '"') . '", notitle: "1", replaceWith:"' . $key . '", className:"col1-0" },' . NR;
		
		$used[$smileys[$key][0]] = TRUE;
	}
	if ($smiles)
	{
		$smiles = NR . "{name:'Смайлы', openWith:':-)', closeWith:'', className:'smiles', dropMenu: [" 
				. $smiles
				. ']},';
	}

?>

<script type="text/javascript">
<?php require($murktup_dir . 'bb.js.php') ?>
</script>

<?php echo '<script type="text/javascript" src="'. $murktup_url . 'jquery.markitup.js"></script>'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. $murktup_url . 'style.css">'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. $murktup_url . 'bb.style.css">'; ?>

<?php
 if (isset($discussion_id)) $auto_id = $comuser_id . '_' . $discussion_id; else $auto_id = $comuser_id; 
?>
	
<script language="javascript">
	autosaveurl = '<?= getinfo('ajax') . base64_encode('plugins/dialog/editors/markitup/autosave-post-ajax.php') ?>';
	autosaveid = '<?= $auto_id ?>';

	$(document).ready(function() 
	{
		$('#comments_content').markItUp(myBbcodeSettings);
	});
</script>
<?php
?>