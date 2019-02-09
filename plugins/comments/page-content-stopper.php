<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$p->div_start('mso-page-content mso-type-' . getinfo('type') . '-content');
	
	
	// для page возможен свой info-bottom
	if ($f = mso_page_foreach('mso-info-bottom-page')) 
	{
		require($f);
	}
	elseif ($f = mso_page_foreach('info-bottom')) require($f);
	
	
	$p->html('<aside>');
		
		mso_page_content_end();
		
		$p->clearfix();
		
		if ($f = mso_page_foreach('page-content-end')) require($f);
			
	$p->html('</aside>');
	
$p->div_end('mso-page-content mso-type-' . getinfo('type') . '-content');

?>