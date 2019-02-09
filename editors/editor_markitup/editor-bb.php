<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/bb.js"></script>'; ?>
<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.js"></script>'; ?>
<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_markitup/smiles.js"></script>'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'editor_markitup/style.css" />'; ?>
<?php echo '<link rel="stylesheet" type="text/css" href="'. getinfo('plugins_url') . 'editor_markitup/bb.style.css" />'; ?>

<script language="javascript">
$(document).ready(function() {
    $('#f_content').markItUp(myBbcodeSettings);
});
</script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data">
<?= $editor_config['do'] ?>
<?
  $image_url=getinfo('uploads_url').'smiles/';
  $CI = & get_instance();
  $CI->load->helper('smiley_helper');
  $smileys=_get_smiley_array();
   echo '<p style="padding-bottom:5px;">';
  $used = array();
  foreach ($smileys as $key => $val)
  {
    if (isset($used[$smileys[$key][0]]))
    {
      continue;
    }
    echo "<a href=\"javascript:void(0);\" onclick=\"addSmile('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" title=\"".$smileys[$key][3]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\"></a> ";
    $used[$smileys[$key][0]] = TRUE;
  }
  
  echo '</p>';
?>

<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
