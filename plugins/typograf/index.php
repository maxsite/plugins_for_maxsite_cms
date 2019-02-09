<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function typograf_autoload() 
{
	mso_hook_add('content', 'typograf'); // хуки пошли..
  mso_hook_add('admin_init', 'typograf_admin'); // админка
}  

# функция выполняется при деинсталяции плагина
function typograf_uninstall()
{
  if(mso_get_option('tp_bbcode')) mso_delete_option('tp_bbcode');
}

# подключаем настройки плагина
function typograf_admin()
{
  mso_admin_menu_add('plugins', 'typograf', 'Типограф');
	mso_admin_url_hook('typograf', 'typograf_admin_page');
}

function typograf_admin_page()
{
  global $MSO;
  require($MSO->config['plugins_dir'] . 'typograf/admin.php');
}

function make_it_typografed($m) 
{				
  $remoteTypograf = new RemoteTypograf('UTF-8');
  
  $remoteTypograf->htmlEntities(); // советую не трогать
  $remoteTypograf->br (false);  // простановка переносы строк
  $remoteTypograf->p (true);  // разметка параграфов
  $remoteTypograf->nobr (3); // оптимально
	// подробнее: http://www.artlebedev.ru/tools/typograf/preferences/	
	return $remoteTypograf->processText($m[1]);
}

function typograf($text) 
{
	$preg = '~\[tp\](.*?)\[\/tp\]~si';
  if(mso_get_option('tp_bbcode'))
    $text = '[tp]'.str_ireplace('[\tp]','[tp]',str_ireplace('[tp]','[/tp]',str_ireplace('[/tp]','[\tp]',$text))).'[/tp]';
	
	$text = preg_replace_callback($preg, "make_it_typografed" , $text);
  $text = str_ireplace('[tp]', '', $text);
	$text = str_ireplace('[/tp]', '', $text);
	return $text;
}

# К дальнейшему коду я отношения не имею :)

/*
	(c) Art. Lebedev Studio
*/

class RemoteTypograf
{
	var $_entityType = 4;
	var $_useBr = 1;
	var $_useP = 1;
	var $_maxNobr = 3;
	var $_encoding = 'UTF-8';

	function RemoteTypograf ($encoding)
	{
		if ($encoding) $this->_encoding = $encoding;
	}

	function htmlEntities()
	{
		$this->_entityType = 1;
	}

	function xmlEntities()
	{
		$this->_entityType = 2;
	}

	function mixedEntities()
	{
		$this->_entityType = 4;
	}

	function noEntities()
	{
		$this->_entityType = 3;
	}

	function br ($value)
	{
		$this->_useBr = $value ? 1 : 0;
	}
	
	function p ($value)
	{
		$this->_useP = $value ? 1 : 0;
	}
	
	function nobr ($value)
	{
		$this->_maxNobr = $value ? $value : 0;
	}

	function processText ($text)
	{
		$text = str_replace ('&', '&amp;', $text);
		$text = str_replace ('<', '&lt;', $text);
		$text = str_replace ('>', '&gt;', $text);

		$SOAPBody = '<?xml version="1.0" encoding="' . $this->_encoding . '"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<ProcessText xmlns="http://typograf.artlebedev.ru/webservices/">
	  <text>' . $text . '</text>
      <entityType>' . $this->_entityType . '</entityType>
      <useBr>' . $this->_useBr . '</useBr>
      <useP>' . $this->_useP . '</useP>
      <maxNobr>' . $this->_maxNobr . '</maxNobr>
	</ProcessText>
  </soap:Body>
</soap:Envelope>';

		$host = 'typograf.artlebedev.ru';
		$SOAPRequest = 'POST /webservices/typograf.asmx HTTP/1.1
Host: typograf.artlebedev.ru
Content-Type: text/xml
Content-Length: ' . strlen ($SOAPBody). '
SOAPAction: "http://typograf.artlebedev.ru/webservices/ProcessText"

'.
	$SOAPBody;

		$remoteTypograf = fsockopen ($host, 80);
		fwrite ($remoteTypograf, $SOAPRequest);
		$typografResponse = '';
		while (!feof ($remoteTypograf))
		{
			$typografResponse .= fread ($remoteTypograf, 8192);
		}
		fclose ($remoteTypograf);
		
		$startsAt = strpos ($typografResponse, '<ProcessTextResult>') + 19;
		$endsAt = strpos ($typografResponse, '</ProcessTextResult>');
		$typografResponse = substr ($typografResponse, $startsAt, $endsAt - $startsAt - 1);
		
		$typografResponse = str_replace ('&amp;', '&', $typografResponse);
		$typografResponse = str_replace ('&lt;', '<', $typografResponse);
		$typografResponse = str_replace ('&gt;', '>', $typografResponse);

		return  $typografResponse;
	}
}


?>
