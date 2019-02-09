<h1>Где типографировать изволите?</h1>

<?php

	if( isset($_POST['tp_settings_submit']) ){
		mso_add_option('tp_bbcode', $_POST['tp_bbcode']);		 
	}
	
?>

<form method="POST">
  <div style="margin-left: 10px; margin-top: 15px; line-height: 130%;">

    <input type="radio" name="tp_bbcode" id="out" value="1" <?= mso_get_option('tp_bbcode')==1 ? 'checked="cheked"' : '' ?> /> Вне bb-кода [tp][/tp] (почти везде)<br>
    <input type="radio" name="tp_bbcode" id="in" value="0" <?= mso_get_option('tp_bbcode')==0 ? 'checked="cheked"': '' ?> /> Внутри кода (избирательно)<br>
    <p style="margin-top: 10px;">
      <input type="submit" name="tp_settings_submit" value="Сохранить">
    <p>

  <div>
</form>