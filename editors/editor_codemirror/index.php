<? if (!defined('BASEPATH')) exit(':-)');

function textarea_autoload($args = array()) {
	mso_hook_add('editor_custom', 'textarea');
}

function textarea_uninstall($args = array()) {
	mso_delete_option('textarea', 'plugins');
	return $args;
}

function textarea($args = array()) {

	$editor_config['url'] = getinfo('plugins_url') . 'textarea/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'textarea/';

	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';

	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';

	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
		else $editor_config['content'] = '';

	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else {
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}

	$editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $editor_config['content']);
	$editor_config['content'] = preg_replace('"&lt;br&gt;"i', "\n", $editor_config['content']);

	require($editor_config['dir'] . 'editor.php');

}

?>