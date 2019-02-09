<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function dv_adminsession_autoload()
{
	
		if (is_login()){
		session_start();
		$_SESSION['adminlogin'] = true;
		}	
if (is_type('logout')){unset($_SESSION['adminlogin']);}

}

# end file