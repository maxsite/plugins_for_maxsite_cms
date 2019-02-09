<?php
/*
 * DiGraph
 *
 * Copyright (c) 2009 Andrew Gromoff <andrew@gromoff.net>, http://gromoff.net/digraph
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 09.12.17 alfa
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<!-- digraph start -->
<script type="text/javascript" src="<?= $editor_config['url'] ?>js/xregexp061.js"></script>
<script type="text/javascript" src="<?= $editor_config['url'] ?>js/parser.js"></script>
<script type="text/javascript" src="<?= $editor_config['url'] ?>js/editor.js"></script>
<script type="text/javascript" src="<?= $editor_config['url'] ?>js/typograph.js"></script>

<?php
// подключение плагинов
$path = $editor_config['dir'] . 'plugins/';

chdir($path);

foreach (glob("*.js") as $filename)
	{
	echo '<script type="text/javascript" src="' . $editor_config['url'] . 'plugins/' . $filename .'"></script>' . "\n";
	}
// end 
?>

<!--[if IE]>
<script type="text/javascript" src="<?= $editor_config['url'] ?>js/ie.js"></script>
<![endif]-->

<!--[if IE 6]>
<style type="text/css">
.digraph-window {width:640px;}
</style>
<![endif]-->

<!--[if IE]>
<style type="text/css">
#digraph-content-ftd
	{
	width:514px;
	}
#digraph-charmap
	{
	width:660px;
	}
</style>
<![endif]-->

<script type="text/javascript">//<![CDATA[

$(".navline").hide();
$(".admin-content h1").hide();

$(function()
	{
/*
	if ($.browser.msie)
		{
		document.execCommand("BackgroundImageCache", false, true);
		}
*/
	
	
	DiGraph = new DiGraph('<?= $editor_config['url'] ?>', 'f_content');
	DiGraph.skey='<?=mso_md5('digraph')?>';
	DiGraph.init(); 
	});

//]]></script>

<form method="post" <?=$editor_config['action']?>>
<fieldset>
<?= $editor_config['do'] ?>
	<textarea id="f_content" name="f_content" rows="25" cols="80" class="ui-corner-bl ui-corner-br" style="height:<?=$editor_config['height']?>px;" ><?=$editor_config['content']?></textarea>
	<span style="float:right;font-size:10px;color:red;font-weight:bold;font-family:Verdana;">09.12.17 alfa</span>
<?= $editor_config['posle'] ?>
</fieldset>
</form>


<div id="digraph-wait" class="digraph-window ui-corner-all" style="width:300px;">
	<div class="digraph-body ui-corner-all3">
		<div class="digraph-header ui-corner-tl3 ui-corner-tr3"><span id="digraph-wait-title">ТИПОГРАФИРОВАНИЕ</span></div>
		<div class="digraph-content">
			<p style="text-align:center;font-family:verdana;font-size:12px;">одну минуточку, уже делаю<span id="digraph-typograf-ps">...</span></p>
		</div>
	</div>
</div>

<div id="digraph-charmap" class="digraph-window ui-corner-all">
	<div class="digraph-body ui-corner-all3">
		<div class="digraph-header ui-corner-tl3 ui-corner-tr3"><span id="digraph-charmap-title">CharMap:</span></div>
		<div class="digraph-content">
			<table class="digraph-content-table">
			<tr>
				<td id="digraph-content-ftd">
				<select id="digraph-char-font" class="ui-corner-all" style="float:right;">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="Arial">Arial</option>
					<option value="Arial Black">Arial Black</option>
					<option value="Comic Sans MS">Comic Sans MS</option>
					<option value="Courier New">Courier New</option>
					<option value="Georgia">Georgia</option>
					<option value="Impact">Impact</option>
					<option value="Lucida Console">Lucida Console</option>
					<option value="Lucida Sans Unicode">Lucida Sans Unicode</option>
					<option value="MS Sans Serif">MS Sans Serif</option>
					<option value="MS Serif">MS Serif</option>
					<option value="Tahoma">Tahoma</option>
					<option value="Times New Roman">Times New Roman</option>
					<option value="Trebuchet MS">Trebuchet MS</option>
					<option value="Verdana">Verdana</option>
				</select>
				<select id="digraph-select-range" class="ui-corner-all" style="float:none;"><option value=""></option></select>
				</td>
				<td id="digraph-preview-code" style="text-align:center;"></td>
			</tr><tr>
				<td id="digraph-char-list" valign="top"></td>
				<td style="width:110px;height:206px;text-align:center;" valign="top"><div id="digraph-preview-box" class="ui-corner-all"><span id="digraph-preview-char" class="ui-corner-all"></span></div></td>
			</tr>	
			</table>
		</div>
	</div>
</div>

<div id="digraph-properties" class="digraph-window ui-corner-all" > <!-- width:640px; digraph-properties-tagname -->
	<div class="digraph-body ui-corner-all3">
		<div class="digraph-header ui-corner-tl3 ui-corner-tr3"><span id="digraph-properties-title"></span></div>
		<div class="digraph-content">
		
		<ul class="digraph-tabs">
			<li><a id="digraph-tab-properties" href="#digraph-tab-properties-box">properties</a></li>
			<li><a id="digraph-tab-style" href="#digraph-tab-style-box">style</a></li>	
			<li><a id="digraph-tab-font" href="#digraph-tab-text-box">font-style</a></li>		
			<li><a id="digraph-tab-text" href="#digraph-tab-font-box">text-style</a></li>	
		</ul>
		<form method="post"  action="/">
		<fieldset class="digraph-panes">

			<div id="digraph-tab-properties-box" class="ui-corner-all">
				<label for="digraph-attr-id">id:</label>
				<input id="digraph-attr-id" style="width:200px;" class="ui-corner-all" type="text" title="общесистемной уникальный ID" maxlength="255" value="" />
				<br />
				<label for="digraph-attr-class">class:</label>
				<input id="digraph-attr-class" style="width:200px;" class="ui-corner-all" type="text" title="класс для CSS" maxlength="255" value="" />
				<br />
				<label for="digraph-attr-title">title:</label>
				<input id="digraph-attr-title" style="width:460px;" class="ui-corner-all" type="text" title="всплывающая подсказка" maxlength="255" value="" />
				
				<input id="digraph-attr-style" style="display:none" class="ui-corner-all" disabled="disabled" type="text" maxlength="255" value="" />
				
				<br />
				<div id="digraph-attr-a" style="display:none;">
					<label for="digraph-attr-href">href:</label>
					<input id="digraph-attr-href" style="width:460px;" class="ui-corner-all" type="text"  maxlength="255" value="" />
					<br />

					<label for="digraph-attr-rel">rel:</label>
					<input id="digraph-attr-rel" style="width:200px;" class="ui-corner-all" type="text" title="укажите - external, что бы ссылка открывалась в новом окне" maxlength="255" value="" />

					<label for="digraph-attr-rev" style="width:46px;">rev:</label>
					<input id="digraph-attr-rev" style="width:200px;" class="ui-corner-all" type="text" maxlength="255" value="" />
				</div>
				
				<div id="digraph-attr-img" style="display:none;">
					<label for="digraph-attr-src">* src:</label>
					<input id="digraph-attr-src" style="width:460px;" class="ui-corner-all" type="text"  maxlength="255" value="" />
					<br />

					<label for="digraph-attr-alt">* alt:</label>
					<input id="digraph-attr-alt" style="width:460px;" class="ui-corner-all" type="text"  maxlength="255" value="" />
					<br />

					<label for="digraph-attr-height">height:</label>
					<input id="digraph-attr-height" style="width:120px;" class="ui-corner-all" type="text" title="числовое значение" maxlength="255" value="" />

					<label for="digraph-attr-width" style="width:40px;">width:</label>
					<input id="digraph-attr-width" style="width:120px;" class="ui-corner-all" type="text" title="числовое значение" maxlength="255" value="" />
				</div>
			</div>
			
			<div id="digraph-tab-style-box" class="ui-corner-all">
				<label for="digraph-color">color:</label>
				<input id="digraph-color" style="width:72px;" class="ui-corner-all" type="text" maxlength="7" value="" />
				<br />

				<label for="digraph-border">border:</label>
				<input id="digraph-border" style="width:460px;" class="ui-corner-all" type="text" maxlength="255" value="" />
				<br />

				<label for="digraph-background">background:</label>
				<input id="digraph-background" style="width:460px;" class="ui-corner-all" type="text" maxlength="255" value="" />
				<br />

				<label for="digraph-margin">margin:</label>
				<input id="digraph-margin" style="width:200px;" class="ui-corner-all" type="text" maxlength="32" value="" />
				<br />

				<label for="digraph-padding">padding:</label>
				<input id="digraph-padding" style="width:200px;" class="ui-corner-all" type="text" maxlength="32" value="" />
			</div>		

			<div id="digraph-tab-text-box" class="ui-corner-all">

				<label for="digraph-text-align">text-align:</label>
				<select id="digraph-text-align" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="left">left</option>
					<option value="right">right</option>
					<option value="center">center</option>
					<option value="justify">justify</option>
				</select> 
				<br />

				<label for="digraph-text-decoration">text-decoration:</label>
				<select id="digraph-text-decoration" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="blink">blink</option>
					<option value="ine-through">line-through</option>
					<option value="none">none</option>
					<option value="overline">overline</option>
					<option value="underline">underline</option>
				</select> 
				<br />

				<label for="digraph-text-indent">text-indent:</label>
				<input id="digraph-text-indent" style="width:48px;" class="ui-corner-all" type="text"  maxlength="5" value="" />
				<br />

				<label for="digraph-text-transform">text-transform:</label>
				<select id="digraph-text-transform" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="none">none</option>
					<option value="uppercase">uppercase</option>
					<option value="lowercase">lowercase</option>
					<option value="capitalize">capitalize</option>
				</select> 
				<br />

				<label for="digraph-white-space">white-space:</label>
				<select id="digraph-white-space" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="normal">normal</option>
					<option value="pre">pre</option>
					<option value="nowrap">nowrap</option>
				</select> 
				<br />

				<label for="digraph-letter-spacing">spacing:</label>
				<input id="digraph-letter-spacing" style="width:48px;" class="ui-corner-all" type="text"  maxlength="5" value="" />
				<label for="digraph-letter-spacing" style="text-align:left;width:48px;">(letter)</label>
				
				<input id="digraph-word-spacing" style="width:48px;" class="ui-corner-all" type="text"  maxlength="5" value="" />
				<label for="digraph-word-spacing" style="text-align:left;">(word)</label>

			</div>

			<div id="digraph-tab-font-box" class="ui-corner-all">

				<label for="digraph-font-style">font-style:</label>
				<select id="digraph-font-style" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="normal">normal</option>
					<option value="italic">italic</option>
					<option value="oblique">oblique</option>
				</select> 
				<br />

				<label for="digraph-font-variant">font-variant:</label>
				<select id="digraph-font-variant" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="normal">normal</option>
					<option value="small caps">small caps</option>
				</select> 
				<br />

				<label for="digraph-font-weight">font-weight:</label>
				<select id="digraph-font-weight" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="lighter">lighter</option>
					<option value="100">100</option>
					<option value="200">200</option>
					<option value="300">300</option>
					<option value="normal">400 (normal)</option>
					<option value="500">500</option>
					<option value="600">600</option>
					<option value="bold">700 (bold)</option>
					<option value="800">800</option>
					<option value="900">900</option>
					<option value="bolder">bolder</option>
				</select> 
				<br />

				<label for="digraph-font-size">font-size:</label>
				<input id="digraph-font-size" style="width:64px;" class="ui-corner-all" type="text"  maxlength="5" value="" />
				<label for="digraph-line-height"  style="width:72px;">line-height:</label>
				<input id="digraph-line-height" style="width:64px;" class="ui-corner-all" type="text"  maxlength="5" value="" />
				<br />

				<label for="digraph-font-family">font-family:</label>
				<select id="digraph-font-family" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="Arial">Arial</option>
					<option value="Arial Black">Arial Black</option>
					<option value="Comic Sans MS">Comic Sans MS</option>
					<option value="Courier New">Courier New</option>
					<option value="Georgia">Georgia</option>
					<option value="Impact">Impact</option>
					<option value="Lucida Console">Lucida Console</option>
					<option value="Lucida Sans Unicode">Lucida Sans Unicode</option>
					<option value="MS Sans Serif">MS Sans Serif</option>
					<option value="MS Serif">MS Serif</option>
					<option value="Tahoma">Tahoma</option>
					<option value="Times New Roman">Times New Roman</option>
					<option value="Trebuchet MS">Trebuchet MS</option>
					<option value="Verdana">Verdana</option>
				</select> 
				<br />

				<label for="digraph-font-stretch">font-stretch:</label>
				<select id="digraph-font-stretch" class="ui-corner-all">
					<option value=""></option>
					<option value="inhertit">inhertit</option>
					<option value="ultra-condensed">ultra-condensed</option>
					<option value="extra-condensed">extra-condensed</option>
					<option value="condensed">condensed</option>
					<option value="semi-condensed">semi-condensed</option>
					<option value="normal">normal</option>
					<option value="semi-expanded">semi-expanded</option>
					<option value="expanded">expanded</option>
					<option value="extra-expanded">extra-expanded</option>
					<option value="ultra-expanded">ultra-expanded</option>
				</select> 
			</div>
		</fieldset>
		</form>
		<div id="digraph-prop-cancel" class="wui-button">CANCEL</div><div id="digraph-prop-ok" class="wui-button">OK</div><div style="clear:right;"></div>
		<fieldset id="digraph-prop-preview" class="ui-corner-all"></fieldset>
		</div><!-- content -->
	</div><!-- body -->
</div>

<!-- digraph end -->