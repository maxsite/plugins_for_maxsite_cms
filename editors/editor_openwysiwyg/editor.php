<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<script type="text/javascript" src="<?= $editor_config['url'] ?>openwysiwyg/scripts/wysiwyg.js"></script>

<script type="text/javascript">
full = new WYSIWYG.Settings();
full.ImagesDir = "<?= $editor_config['url'] ?>openwysiwyg/images/";
full.PopupsDir = "<?= $editor_config['url'] ?>openwysiwyg/popups/";
full.CSSFile = "<?= $editor_config['url'] ?>openwysiwyg/styles/wysiwyg.css";
//full.Width = "85%"; 
//full.Height = "250px";
// customize toolbar buttons
full.addToolbarElement("font", 3, 1); 
full.addToolbarElement("fontsize", 3, 2);
full.addToolbarElement("headings", 3, 3);
// openImageLibrary addon implementation
//full.ImagePopupFile = "<?= $editor_config['url'] ?>openwysiwyg/addons/imagelibrary/insert_image.php";
//full.ImagePopupWidth = 600;
//full.ImagePopupHeight = 245;
WYSIWYG.attach('wysiwyg', full);
</script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

