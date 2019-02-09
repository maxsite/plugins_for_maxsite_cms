<?
$enabled = true;
require_once('FastJSON.class.php');
require_once('Typographus.php');
$result = '';
if ($enabled)
{
	if ($_REQUEST['text'])
	{
		$text = $_REQUEST['text'];
		$typo = new Typographus('UTF-8');
		$result = $typo->process($text);
		if (get_magic_quotes_gpc())
		{
			$result = stripslashes($result);
		}
	}
}

echo $result;

?>