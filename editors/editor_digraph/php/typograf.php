<?php
require_once 'lib.php';

$host = 'www.typograf.ru';
$script = '/webservice/';
$data='text='.urlencode($_REQUEST['text']).'&chr=UTF-8';
$buf = '';

$fp = fsockopen($host,80,$errno, $errstr, 30 );  
         
if ($fp)
	{ 
	fputs($fp, "POST $script HTTP/1.1\n");  

	fputs($fp, "Host: $host\n");  
	fputs($fp, "Content-type: application/x-www-form-urlencoded\n");  
	fputs($fp, "Content-length: " . strlen($data) . "\n");
	fputs($fp, "User-Agent: PHP Script\n");  
	fputs($fp, "Connection: close\n\n");  
	fputs($fp, $data);  
	while(fgets($fp,2048) != "\r\n" && !feof($fp));
	
	while(!feof($fp)) $buf .= fread($fp,2048);
	fclose($fp); 

	}
else	{ 
	echo json_encode(array("text"=>'', "error"=>'Сервер не отвечает'));
	}

echo json_encode(array("text"=>stripslashes($buf), "error"=>''));
 
