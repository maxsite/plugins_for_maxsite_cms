<?
require_once 'lib.php';

function beforeValid2($v)	
	{
	return $v;
	}
function afterValid2($v)
	{
	$v = preg_replace("/(<p><\/p>)\n*/i","", $v);	
	$v = preg_replace("/(<\/p><p)/i","</p>\n\n<p", $v);	

	$v = preg_replace("/(<!-- [^>]*>)/i","\n\n$1", $v);	
	//
	$v = preg_replace("/<p>\s?(\[cut(.*?)?\])\s?<\/p>/i","$1", $v);	
	$v = preg_replace("/<p>\s?\[xcut\]\s?<\/p>/i","[xcut]", $v);

	$v = preg_replace("/<p><script/i","<script", $v);
	$v = preg_replace("/<\/script><\/p>/i","</script>", $v);


	$v = preg_replace("/<p>\s*<!--/i","<!--", $v);
	$v = preg_replace("/--><\/p>/i","-->", $v);

	//$v = preg_replace("/&lt;!\[CDATA\[/i","<![CDATA[",$v);		
	//$v = preg_replace("/\]\]&gt;/i","]]>",$v);	
	
	// Trim
	return preg_replace("/(^\s*)|(\s*$)/","", $v);
	}

if (!$_REQUEST['text'])
	{
	echo json_encode(array("error"=>"нет данных для валидации"));		
	exit(0);
	}

$max = 384; // 256, 384
	
if (strlen($_REQUEST['text']) > ($max*1024))
	{
	echo json_encode(array("error"=>'<ul><li><div><span class="error e1"><strong>слишком большой объем данных</strong></span> (' . strlen($_REQUEST['text']) . ") байт: <strong class=\"description\">валидатор готов обработать не более $max Kb символов.</strong></div></li></ul>"));
	exit(0);
	}

	
require_once '../../HTMLPurifier/HTMLPurifier.auto.php';
require_once '../../HTMLPurifier/HTMLPurifier.func.php';


$config = HTMLPurifier_Config::createDefault();

$config->set('Core.CollectErrors', true);

$config->set('HTML.TidyLevel', 'heavy');				// light | medium (default) | heavy
$config->set('HTML.Doctype', 'XHTML 1.0 Strict');			// 
$config->set('HTML.Trusted', true);

$config->set('CSS.Proprietary', true); 	
$config->set('Attr.EnableID', true);
$config->set('Attr.AllowedFrameTargets', '_blank, _parent, _self, _top');
$config->set('Attr.AllowedRel', 'external, nofollow, external nofollow, lightbox');
$config->set('Attr.AllowedRev', '');

$config->set('Filter.YouTube', true); 
	
$config->set('Output.Newline', "\n");
$config->set('Output.TidyFormat', true);
	
$config->set('Output.CommentScriptContents', false);

//$config->set('AutoFormat', 'AutoParagraph', true);

$config->set('AutoFormat.RemoveEmpty', true);

$purifier = new HTMLPurifier($config);

$html = $_REQUEST['text'];
$html = get_magic_quotes_gpc() ? stripslashes($html) : $html;
//$html = beforeValid($html);
$html = $purifier->purify($html);
//$html = afterValid($html);

$report = '';

if (@$config->get('Core', 'CollectErrors'))
	{
	$e = $purifier->context->get('ErrorCollector');
	$class = $e->getRaw() ? 'fail' : 'pass';
	if ($class == 'fail')
		$report = $e->getHTMLFormatted($config);
	}
	
echo json_encode(array("text"=>$html, "error"=>$report));