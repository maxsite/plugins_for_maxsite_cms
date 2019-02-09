<?php
if (!$_SERVER['HTTP_X_REQUESTED_WITH'])	// HTTP_X_REQUESTED_WITH = XMLHttpRequest
	{
	header("HTTP/1.0 404 Not Found");
	exit(0);
	}

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')
	{
	header("HTTP/1.0 404 Not Found");
	exit(0);
	}

if (!$_REQUEST['skey'])
	{
	header("HTTP/1.0 404 Not Found");
	exit(0);
	}

# генератор md5 свой
function mso_md5($t = '')
{
	global $MSO;

	if ($MSO->config['secret_key'])
		return strrev( md5($t . $MSO->config['secret_key']) );
	else
		return strrev( md5($t . $MSO->config['site_url']) );
}

define('BASEPATH', 'digraph hook');

require_once '../../../mso_config.php';

$skey= mso_md5('digraph');

//print $_REQUEST['skey'].'=?='.$skey;

if ($_REQUEST['skey'] != $skey)
	{
	header("HTTP/1.0 404 Not Found");
	exit(0);
	}