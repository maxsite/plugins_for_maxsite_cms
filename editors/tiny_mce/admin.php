<?php

$tadv_plugins = array( 'safari', 'style', 'emotions', 'print', 'searchreplace', 'xhtmlxtras', 'advlink', 'advimage' );

$tadv_btns1 = array( 'bold', 'italic', 'strikethrough', 'underline', 'separator', 'bullist', 'numlist', 'outdent',  'indent', 'separator', 'justifyleft', 'justifycenter', 'justifyright', 'separator', 'link', 'unlink', 'separator', 'image', 'more', 'styleprops', 'separator', 'separator', 'spellchecker', 'search', 'separator', 'fullscreen' );
$tadv_btns2 = array( 'fontsizeselect', 'formatselect', 'pastetext', 'pasteword', 'removeformat', 'separator', 'charmap', 'print', 'separator', 'forecolor', 'backcolor', 'emotions', 'separator', 'sup', 'sub', 'media', 'separator', 'undo', 'redo', 'attribs' );
$tadv_btns3 = $tadv_btns4 = array();
$tadv_allbtns = array( 'bold', 'italic', 'strikethrough', 'underline', 'bullist', 'numlist', 'outdent', 'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'cut', 'copy', 'paste', 'link', 'unlink', 'image', 'search', 'replace', 'fontselect', 'fontsizeselect', 'fullscreen', 'styleselect', 'formatselect', 'forecolor', 'backcolor', 'pastetext', 'pasteword', 'removeformat', 'cleanup', 'spellchecker', 'charmap', 'print', 'undo', 'redo', 'tablecontrols', 'cite', 'ins', 'del', 'abbr', 'acronym', 'attribs', 'layer', 'advhr', 'code', 'visualchars', 'nonbreaking', 'sub', 'sup', 'visualaid', 'insertdate', 'inserttime', 'anchor', 'styleprops', 'emotions', 'media', 'blockquote', 'separator', 'more', 'page', '|' );

// Theme options
//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
//theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",

############################

$update_tadv_options = false;
$imgpath = TINY_MCE_PLUGIN_URL . '/tiny_mce/images/';

$tadv_toolbars = mso_get_option('tadv_toolbars','plugins');
if ( ! is_array($tadv_toolbars) )
	$tadv_options = array( 'advlink' => 1, 'advimage' => 1, 'importcss' => 0, 'contextmenu' => 0, 'tadvreplace' => 0 );
else
{
	$tadv_options = mso_get_option('tadv_options','plugins');
	$tadv_toolbars['toolbar_1'] = isset($tadv_toolbars['toolbar_1']) ? (array) $tadv_toolbars['toolbar_1'] : array();
	$tadv_toolbars['toolbar_2'] = isset($tadv_toolbars['toolbar_2']) ? (array) $tadv_toolbars['toolbar_2'] : array();
	$tadv_toolbars['toolbar_3'] = isset($tadv_toolbars['toolbar_3']) ? (array) $tadv_toolbars['toolbar_3'] : array();
	$tadv_toolbars['toolbar_4'] = isset($tadv_toolbars['toolbar_4']) ? (array) $tadv_toolbars['toolbar_4'] : array();
}

if(isset($_POST['tadv-save']))
{
	mso_checkreferer(array('tadv-save-buttons-order'));

	$tb1 = $tb2 = $tb3 = $tb4 = $btns = array();
	parse_str( $_POST['toolbar_1order'], $tb1 );
	parse_str( $_POST['toolbar_2order'], $tb2 );
	parse_str( $_POST['toolbar_3order'], $tb3 );
	parse_str( $_POST['toolbar_4order'], $tb4 );

	$tadv_toolbars['toolbar_1'] = isset($tb1['pre'])?(array) $tb1['pre']:array();
	$tadv_toolbars['toolbar_2'] = isset($tb2['pre'])?(array) $tb2['pre']:array();
	$tadv_toolbars['toolbar_3'] = isset($tb3['pre'])?(array) $tb3['pre']:array();
	$tadv_toolbars['toolbar_4'] = isset($tb4['pre'])?(array) $tb4['pre']:array();

	mso_add_option( 'tadv_toolbars', $tadv_toolbars,'plugins' );

	$tadv_options['advlink'] = isset($_POST['advlink']) ? 1 : 0;
	$tadv_options['advimage'] = isset($_POST['advimage']) ? 1 : 0;
	$tadv_options['contextmenu'] = isset($_POST['contextmenu']) ? 1 : 0;
	$update_tadv_options = true;
}

$hidden_row = 0;
$i = 0;
foreach( $tadv_toolbars as $toolbar )
{
	$l = false;
	$i++;

	if ( empty($toolbar) )
	{
		$btns["toolbar_$i"] = array();
		continue;
	}

	foreach( $toolbar as $k => $v )
	{
		if ( strpos($v, 'separator') !== false ) $toolbar[$k] = 'separator';
		if ( 'layer' == $v ) $l = $k;
		if ( empty($v) ) unset($toolbar[$k]);
	}
	if ( $l ) array_splice( $toolbar, $l, 1, array('insertlayer', 'moveforward', 'movebackward', 'absolute') );

	$btns["toolbar_$i"] = $toolbar;
}

extract($btns);

if ( empty($toolbar_1) && empty($toolbar_2) && empty($toolbar_3) && empty($toolbar_4) )
{
	?>
	<div class="error" id="message"><p><?php echo t('All toolbars are empty! Default buttons loaded.', 'tadv');
	?>
	</p></div>
	<?php
	$allbtns = array_merge( $tadv_btns1, $tadv_btns2, $tadv_btns3, $tadv_btns4 );
}
else
	$allbtns = array_merge( $toolbar_1, $toolbar_2, $toolbar_3, $toolbar_4 );

if ( in_array('advhr', $allbtns) ) $plugins[] = 'advhr';
if ( in_array('insertlayer', $allbtns) ) $plugins[] = 'layer';
if ( in_array('visualchars', $allbtns) ) $plugins[] = 'visualchars';
if ( in_array('nonbreaking', $allbtns) ) $plugins[] = 'nonbreaking';
if ( in_array('styleprops', $allbtns) ) $plugins[] = 'style';
if ( in_array('emotions', $allbtns) ) $plugins[] = 'emotions';
if ( in_array('insertdate', $allbtns) || in_array('inserttime', $allbtns) ) $plugins[] = 'insertdatetime';
if ( in_array('tablecontrols', $allbtns) ) $plugins[] = 'table';
if ( in_array('print', $allbtns) ) $plugins[] = 'print';
if ( in_array('iespell', $allbtns) ) $plugins[] = 'iespell';
if ( in_array('search', $allbtns) || in_array('replace', $allbtns) ) $plugins[] = 'searchreplace';
if ( in_array('cite', $allbtns) || in_array('ins', $allbtns) || in_array('del', $allbtns) || in_array('abbr', $allbtns) || in_array('acronym', $allbtns) || in_array('attribs', $allbtns) ) $plugins[] = 'xhtmlxtras';
if ( $tadv_options['advlink'] == '1' ) $plugins[] = 'advlink';
if ( $tadv_options['advimage'] == '1' ) $plugins[] = 'advimage';
if ( $tadv_options['contextmenu'] == '1' ) $plugins[] = 'contextmenu';
if ( in_array('more', $allbtns)) $plugins[] = 'more';
if ( in_array('pagebreak', $allbtns)) $plugins[] = 'pagebreak';

$buttons = array( 'Quote' => 'blockquote', 'Bold' => 'bold', 'Italic' => 'italic', 'Strikethrough' => 'strikethrough', 'Underline' => 'underline', 'Bullet List' => 'bullist', 'Numbered List' => 'numlist', 'Outdent' => 'outdent', 'Indent' => 'indent', 'Allign Left' => 'justifyleft', 'Center' => 'justifycenter', 'Alligh Right' => 'justifyright', 'Justify' => 'justifyfull', 'Cut' => 'cut', 'Copy' => 'copy', 'Paste' => 'paste', 'Link' => 'link', 'Remove Link' => 'unlink', 'Insert Image' => 'image', 'Search' => 'search', 'Replace' => 'replace', '<!--fontselect-->' => 'fontselect', '<!--fontsizeselect-->' => 'fontsizeselect', 'Full Screen' => 'fullscreen', '<!--styleselect-->' => 'styleselect', '<!--formatselect-->' => 'formatselect', 'Text Color' => 'forecolor', 'Back Color' => 'backcolor', 'Paste as Text' => 'pastetext', 'Paste from Word' => 'pasteword', 'Remove Format' => 'removeformat', 'Clean Code' => 'cleanup', 'Check Spelling' => 'spellchecker', 'Character Map' => 'charmap', 'Print' => 'print', 'Undo' => 'undo', 'Redo' => 'redo', 'Table' => 'tablecontrols', 'Citation' => 'cite', 'Inserted Text' => 'ins', 'Deleted Text' => 'del', 'Abbreviation' => 'abbr', 'Acronym' => 'acronym', 'XHTML Attribs' => 'attribs', 'Layer' => 'layer', 'Advanced HR' => 'advhr', 'View HTML' => 'code', 'Hidden Chars' => 'visualchars', 'NB Space' => 'nonbreaking', 'Sub' => 'sub', 'Sup' => 'sup', 'Visual Aids' => 'visualaid', 'Insert Date' => 'insertdate', 'Insert Time' => 'inserttime', 'Anchor' => 'anchor', 'Style' => 'styleprops', 'Smilies' => 'emotions', 'Insert Movie' => 'media', 'IE Spell' => 'iespell', 'More tag' => 'more', 'Разрыв страницы' => 'pagebreak' );

if ( function_exists('moxiecode_plugins_url') )
{
	if ( moxiecode_plugins_url('imagemanager') ) $buttons['MCFileManager'] = 'insertimage';
	if ( moxiecode_plugins_url('filemanager') ) $buttons['MCImageManager'] = 'insertfile';
}

$tadv_allbtns = array_values($buttons);
$tadv_allbtns[] = 'separator';
$tadv_allbtns[] = '|';

if ( mso_get_option('tadv_plugins','plugins') != $plugins ) mso_add_option( 'tadv_plugins', $plugins, 'plugins' );
if ( mso_get_option('tadv_btns1','plugins') != $toolbar_1 ) mso_add_option( 'tadv_btns1', $toolbar_1, 'plugins' );
if ( mso_get_option('tadv_btns2','plugins') != $toolbar_2 ) mso_add_option( 'tadv_btns2', $toolbar_2, 'plugins' );
if ( mso_get_option('tadv_btns3','plugins') != $toolbar_3 ) mso_add_option( 'tadv_btns3', $toolbar_3, 'plugins' );
if ( mso_get_option('tadv_btns4','plugins') != $toolbar_4 ) mso_add_option( 'tadv_btns4', $toolbar_4, 'plugins' );
if ( mso_get_option('tadv_allbtns','plugins') != $tadv_allbtns ) mso_add_option( 'tadv_allbtns', $tadv_allbtns, 'plugins' );

for ( $i = 1; $i < 21; $i++ )
	$buttons["s$i"] = "separator$i";

if ( isset($_POST['tadv-save']) ) {	?>
	<div class="update" id="message"><p><?php echo t('Options saved', 'tadv'); ?></p></div>
<?php } ?>

<div class="wrap" id="contain">

	<form id="tadvadmin" method="post" action="" onsubmit="">
	<p><?php echo t('Перетяните кнопочки на панель ниже.', 'plugins'); ?></p>

	<div id="tadvzones">
		<input id="toolbar_1order" name="toolbar_1order" value="" type="hidden" />
		<input id="toolbar_2order" name="toolbar_2order" value="" type="hidden" />
		<input id="toolbar_3order" name="toolbar_3order" value="" type="hidden" />
		<input id="toolbar_4order" name="toolbar_4order" value="" type="hidden" />
		<input name="tadv-save" value="1" type="hidden" />

	<div class="tadvdropzone">
	<ul style="position: relative;" id="toolbar_1" class="container">
<?php
if ( is_array($tadv_toolbars['toolbar_1']) ) {
	$tb1 = array();
	foreach( $tadv_toolbars['toolbar_1'] as $k ) {
		$t = array_intersect( $buttons, (array) $k );
		$tb1 += $t;
	}

	foreach( $tb1 as $name => $btn ) {
		if ( strpos( $btn, 'separator' ) !== false ) { ?>

	<li class="separator" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"> </div></li>
<?php	} else { ?>

	<li class="tadvmodule" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"><img src="<?php echo $imgpath . $btn . '.gif'; ?>" title="<?php echo $name; ?>" />
	<span class="descr"> <?php echo $name; ?></span></div></li>
<?php   }
	}
	$buttons = array_diff( $buttons, $tb1 );
} ?>
	</ul></div>

	<div class="tadvdropzone">
	<ul style="position: relative;" id="toolbar_2" class="container">
<?php
if ( is_array($tadv_toolbars['toolbar_2']) ) {
	$tb2 = array();
	foreach( $tadv_toolbars['toolbar_2'] as $k ) {
		$t = array_intersect( $buttons, (array) $k );
		$tb2 = $tb2 + $t;
	}
	foreach( $tb2 as $name => $btn ) {
		if ( strpos( $btn, 'separator' ) !== false ) { ?>

	<li class="separator" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"> </div></li>
<?php	} else { ?>

	<li class="tadvmodule" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"><img src="<?php echo $imgpath . $btn . '.gif'; ?>" title="<?php echo $name; ?>" />
	<span class="descr"> <?php echo $name; ?></span></div></li>
<?php   }
	}
	$buttons = array_diff( $buttons, $tb2 );
} ?>
	</ul></div>

	<div class="tadvdropzone">
	<ul style="position: relative;" id="toolbar_3" class="container">
<?php
if ( is_array($tadv_toolbars['toolbar_3']) ) {
	$tb3 = array();
	foreach( $tadv_toolbars['toolbar_3'] as $k ) {
		$t = array_intersect( $buttons, (array) $k );
		$tb3 += $t;
	}
	foreach( $tb3 as $name => $btn ) {
		if ( strpos( $btn, 'separator' ) !== false ) { ?>

	<li class="separator" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"> </div></li>
<?php	} else { ?>

	<li class="tadvmodule" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"><img src="<?php echo $imgpath . $btn . '.gif'; ?>" title="<?php echo $name; ?>" />
	<span class="descr"> <?php echo $name; ?></span></div></li>
<?php   }
	}
	$buttons = array_diff( $buttons, $tb3 );
} ?>
	</ul></div>

	<div class="tadvdropzone">
	<ul style="position: relative;" id="toolbar_4" class="container">
<?php
if ( is_array($tadv_toolbars['toolbar_4']) ) {
	$tb4 = array();
	foreach( $tadv_toolbars['toolbar_4'] as $k ) {
		$t = array_intersect( $buttons, (array) $k );
		$tb4 += $t;
	}
	foreach( $tb4 as $name => $btn ) {
		if ( strpos( $btn, 'separator' ) !== false ) { ?>

	<li class="separator" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"> </div></li>
<?php	} else { ?>

	<li class="tadvmodule" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"><img src="<?php echo $imgpath . $btn . '.gif'; ?>" title="<?php echo $name; ?>" />
	<span class="descr"> <?php echo $name; ?></span></div></li>
<?php   }
	}
	$buttons = array_diff( $buttons, $tb4 );
} ?>
	</ul></div>
	</div>

	<div id="tadvWarnmsg">&nbsp;
	<span id="too_long" style="display:none;"><?php echo t('Adding too many buttons will make the toolbar too long and will not display correctly in TinyMCE!', 'tadv'); ?></span>
	</div>

	<div id="tadvpalettediv">
	<ul style="position: relative;" id="tadvpalette">
<?php
if ( is_array($buttons) ) {
	foreach( $buttons as $name => $btn ) {
		if ( strpos( $btn, 'separator' ) !== false ) { ?>

	<li class="separator" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"> </div></li>
<?php	} else { ?>

	<li class="tadvmodule" id="pre_<?php echo $btn; ?>">
	<div class="tadvitem"><img src="<?php echo $imgpath . $btn . '.gif'; ?>" title="<?php echo $name; ?>" />
	<span class="descr"> <?php echo $name; ?></span></div></li>
<?php   }
	}
} ?>
	</ul>
</div>

<table class="clear" style="margin:10px 0"><tr><td style="padding:2px 12px 8px;">
	Also enable:
	<label for="advlink" class="tadv-box"><?php echo t('Advanced Link', 'tadv'); ?> &nbsp;
	<input type="checkbox" class="tadv-chk"  name="advlink" id="advlink" <?php if ( $tadv_options['advlink'] == '1' ) echo ' checked="checked"'; ?> /></label> &bull;

	<label for="advimage" class="tadv-box"><?php echo t('Advanced Image', 'tadv'); ?> &nbsp;
	<input type="checkbox" class="tadv-chk"  name="advimage" id="advimage" <?php if ( $tadv_options['advimage'] == '1' ) echo ' checked="checked"'; ?> /></label> &bull;
	<label for="contextmenu" class="tadv-box"><?php echo t('Context Menu', 'tadv'); ?> &nbsp;

	<input type="checkbox" class="tadv-chk"  name="contextmenu" id="contextmenu" <?php if ( $tadv_options['contextmenu'] == '1' ) echo ' checked="checked"'; ?> /></label>
	<?php echo t('(to show the context menu in Firefox and use the spellchecker, hold down the Ctrl key).', 'tadv'); ?>
	</td></tr>
</table>

<p>
	<?php /*wp_nonce_field( 'tadv-save-buttons-order' );*/ ?>
	<input class="button tadv_btn" type="button" class="tadv_btn" value="<?php echo t('Remove Settings', 'tadv'); ?>" onclick="document.getElementById('tadv_uninst_div').style.display = 'block';" />
	<input class="button-primary tadv_btn" type="button" value="<?php echo t('Save Changes', 'tadv'); ?>" onclick="tadvSortable.serialize();" />
</p>
</form>

<div id="tadvWarnmsg2">&nbsp;
	<span id="sink_err" style="display:none;"><?php echo t('The Kitchen Sink button shows/hides the next toolbar row. It will not work at the current place.', 'tadv'); ?></span>
</div>

<div id="tadv_uninst_div" style="">
<form method="post" action="">
<?php /*wp_nonce_field('tadv-uninstall');*/ ?>
<div><?php echo t('Remove all saved settings from the database?', 'tadv'); ?>
<input class="button tadv_btn" type="button" name="cancel" value="<?php echo t('Cancel', 'tadv'); ?>" onclick="document.getElementById('tadv_uninst_div').style.display = 'none';" style="margin-left:20px" />
<input class="button tadv_btn" type="submit" name="tadv_uninstall" value="<?php echo t('Continue', 'tadv'); ?>" /></div>
</form>
</div>
</div>

<?php
if ( $update_tadv_options )
	mso_add_option( 'tadv_options', $tadv_options, 'plugins' );
