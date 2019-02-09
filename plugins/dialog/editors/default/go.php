<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


  // кнопки из плагина comment_button____________________________________
	echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;

	echo '<p class="comment_button">
	<input type="button" value="B" title="' . t('Полужирный', 'plugins') . '" onClick="addText(\'<b>\', \'</b>\') ">
	<input type="button" value="I" title="' . t('Курсив', 'plugins') . '" onClick="addText(\'<i>\', \'</i>\') ">
	<input type="button" value="U" title="' . t('Подчеркнутый', 'plugins') . '" onClick="addText(\'<u>\', \'</u>\') ">
	<input type="button" value="S" title="' . t('Зачеркнутый', 'plugins') . '" onClick="addText(\'<s>\', \'</s>\') ">
	<input type="button" value="' . t('Цитата', 'plugins') . '" title="' . t('Цитата', 'plugins') . '" onClick="addText(\'<blockquote>\', \'</blockquote>\') ">
	<input type="button" value="' . t('Код', 'plugins') . '" title="' . t('Код или преформатированный текст', 'plugins') . '" onclick="addText(\'<pre>\', \'</pre>\') ">
	<input type="button" value="Br" title="' . t('Разрыв', 'plugins') . '" onClick="addText(\'\', \'<br />\') ">
	</p>';
	
	
  // смайлы из плагина comment_smiles___________________________________
	//echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>' . NR;

	$image_url=getinfo('uploads_url').'smiles/';
	$CI = & get_instance();
	$CI->load->helper('smiley_helper');
	$smileys=_get_smiley_array();
  
	// идея Евгений - http://jenweb.info/page/hide-smileys, http://forum.max-3000.com/viewtopic.php?f=6&t=3192
	echo NR . '<div style="width: 19px; height: 19px; float: right; text-align: right; margin-top: -23px; cursor: pointer; background: url(\'' . getinfo('plugins_url') . 'comment_smiles/bg.gif\') no-repeat;" title="' . t('Показать/скрыть смайлики', 'plugins') . '" class="btn-smiles"></div>' . NR; 
  
	echo '<p style="padding-bottom:5px;" class="comment_smiles">';
  
	//кусок кода из smiley_helper
	$used = array();
	foreach ($smileys as $key => $val)
	{
		// Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
		// показывалась только одна кнопка
		if (isset($used[$smileys[$key][0]])) continue;
			
		echo "<a href=\"javascript:void(0);\" onclick=\"addSmile('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" title=\"".$smileys[$key][3]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\"></a> ";
		$used[$smileys[$key][0]] = TRUE;
	}
  
	echo '</p><script>$("p.comment_smiles").hide();</script>';
	
	


?>