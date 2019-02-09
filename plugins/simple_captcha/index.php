<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function simple_captcha_autoload($args = array()) {	
	if ( !is_login() and !is_login_comuser() ) {
		mso_hook_add( 'comments_content_end', 'simple_captcha_go'); 
		mso_hook_add( 'comments_new_captcha', 'simple_captcha_new_comment');
		if ( is_type('page') ) mso_hook_add( 'head' , 'simple_captcha_head');
	}
}

function simple_captcha_new_comment($args = array()) {
	global $MSO;
	
	if ( $_POST['dcaptcha_sess'] == 1) {
		return true;
	} else {
		return false; 
	}
}

function simple_captcha_head () {
?>
<style>
	.dcaptcha_red{
		display: inline-block;
		font-family: arial;
		font-size: 12px;
		color: #AA0000;
		padding: 5px;
		background: #AA0000;
	}

	.dcaptcha_yellow{
		display: inline-block;
		font-family: arial;
		font-size: 12px;
		color: #AA0000;
		padding: 5px;
		background: #FFFFFF;
	}</style>

<?php	
}

function simple_captcha_go($args = array()) {
	global $MSO;
?>
<script language="javascript">
	function dcaptcha_change(){
		if (document.getElementById('dcaptcha_captcha1').className == "dcaptcha_yellow"){
			document.getElementById('dcaptcha_captcha1').className = "dcaptcha_red";
			document.getElementById('dcaptcha_sess').value = 0;
		}
		else{
			document.getElementById('dcaptcha_captcha1').className = "dcaptcha_yellow";
			document.getElementById('dcaptcha_sess').value = 1;
		}
	}

	</script>

	<div id="dcaptcha"><p>

		<input type="checkbox" class="dcaptcha_red" id="dcaptcha_captcha1" onclick="dcaptcha_change();" value="Я - человек!">
		Я не робот.<br>
	</div>
	<input type="hidden" name="dcaptcha_sess" id="dcaptcha_sess" value="0">

	<script>
	var commentField = document.getElementById("url");
    var submitp = commentField.parentNode;
    var answerDiv = document.getElementById("dcaptcha");	    
    submitp.appendChild(answerDiv, commentField);
</script>

<?php }
?>
