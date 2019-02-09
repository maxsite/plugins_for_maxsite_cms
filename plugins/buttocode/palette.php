<div class="b2cpaldiv" id="b2cpaldiv"><table><tr><td><table cellspacing="0" cellpadding="0" border="0" class="b2c-palette">
<?php

	$b2c_pal =  file($editor_config['dir'].'palette.csv');
	$b2c_cols = "000;300;600;900;C00;F00;030;330;630;930;C30;F30;060;360;660;960;C60;F60;090;390;690;990;C90;F90;0C0;3C0;6C0;9C0;CC0;FC0;0F0;3F0;6F0;9F0;CF0;FF0;;003;303;603;903;C03;F03;033;333;633;933;C33;F33;063;363;663;963;C63;F63;093;393;693;993;C93;F93;0C3;3C3;6C3;9C3;CC3;FC3;0F3;3F3;6F3;9F3;CF3;FF3;;006;306;606;906;C06;F06;036;336;636;936;C36;F36;066;366;666;966;C66;F66;096;396;696;996;C96;F96;0C6;3C6;6C6;9C6;CC6;FC6;0F6;3F6;6F6;9F6;CF6;FF6;;009;309;609;909;C09;F09;039;339;639;939;C39;F39;069;369;669;969;C69;F69;099;399;699;999;C99;F99;0C9;3C9;6C9;9C9;CC9;FC9;0F9;3F9;6F9;9F9;CF9;FF9;;00C;30C;60C;90C;C0C;F0C;03C;33C;63C;93C;C3C;F3C;06C;36C;66C;96C;C6C;F6C;09C;39C;69C;99C;C9C;F9C;0CC;3CC;6CC;9CC;CCC;FCC;0FC;3FC;6FC;9FC;CFC;FFC;;00F;30F;60F;90F;C0F;F0F;03F;33F;63F;93F;C3F;F3F;06F;36F;66F;96F;C6F;F6F;09F;39F;69F;99F;C9F;F9F;0CF;3CF;6CF;9CF;CCF;FCF;0FF;3FF;6FF;9FF;CFF;FFF";
	$b2c_pal = explode(";;",$b2c_cols);

	for ($i=0;$i<count($b2c_pal); $i++) {		?><tr><?php
		                    $b2cline = explode(";",$b2c_pal[$i]);
		                    for ($j=0; $j<count($b2cline); $j++) {
		                    	$b2ccol = $b2cline[$j];		                    	?><td style="background-color:#<?php echo $b2ccol; ?>"><a href="javascript:;" title="<?php echo $b2ccol; ?>" style="color:#<?php echo $b2ccol; ?>" title="[color=#<?php echo $b2ccol; ?>][/color]" onClick="addText('[color=#<?php echo $b2ccol; ?>]','[/color]'); hidePalette();">+</a></td><?php		                    }
		?></tr><?php	}


?></table>
</td>
<td><a style="background-color: white; color: #000; font-weight: bold; text-decoration: none;" onClick="hidePalette()" href="javascript:;">x</a></td>
</tr></table></div>