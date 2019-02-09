<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$MSO->config['template'] = $old_template;
	$MSO->config['templates_dir'] = $old_path_templates;

	require(getinfo('template_dir') . 'main-start.php');
?>

	<p>Платеж был выполнен.</p>

<?	require(getinfo('template_dir') . 'main-end.php');

?>