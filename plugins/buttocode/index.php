<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# функция автоподключения плагина
function buttocode_autoload($args = array())
{
	mso_hook_add( 'editor_custom', 'buttocode'); # хук на подключение своего редактора
}

# функция выполняется при деинсталяции плагина
function buttocode_uninstall($args = array())
{
	mso_delete_option('buttocode', 'plugins'); // удалим созданные опции
	return $args;
}

function buttocode($args = array())
{

	$options = mso_get_option('buttocode', 'plugins', array() ); // получаем опции

	$editor_config['url'] = getinfo('plugins_url') . 'buttocode/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'buttocode/';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';

	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';

	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';

	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';

	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else
	{
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}

	# Приведение строк с <br> в первозданный вид
	$editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i',"\n",$editor_config['content']);
	$editor_config['content'] = preg_replace('"&lt;br&gt;"i',"\n",$editor_config['content']);

	if (isset($options['editor']))
		$editor_type = $options['editor'] == 'BB-CODE' ? 'editor-bb.php' : 'editor.php';
	else $editor_type = 'editor-bb.php';
  ?>
  <style type="text/css">
.b2c-group input {
	   padding: 0px;
	   height: 24px;
	   margin: 0px;
	   font-size: 100%;
	   }
.b2c-group {
	white-space: nowrap;
	background-color: #efefef;
	margin-right: 5px;
	padding: 1px 5px 1px 5px;
	display: block;
	float: left;
	font-weight: bold;
  	}
#b2cpaldiv {	position: absolute;
	visibility: hidden;
	top: 50px;
	left: 50px;
	border: none;}
#b2cpaldiv table td {	border: none;
	padding: 0px;
	vertical-align: top;}
.b2c-palette td a {	font-family: Verdana, Tahoma, sans-serif;
	font-size: 8px;
	display: block;
	text-decoration: none;
	line-height: 8px;}
.b2c-palette td a:hover {	color: #fff;}
.b2c-palette td {	padding: 1px !important;
	border: 1px solid #FFF !important;}

</style>
  <?
	require($editor_config['dir'] . $editor_type);
}

function buttocode_mso_options()
{
	mso_admin_plugin_options('buttocode', 'plugins',
		array(
			'editor' => array(
							'type' => 'select',
							'name' => 'Редактор',
							'description' => 'Выберите тип редактора',
							'values' => 'HTML # BB-CODE',
							'default' => 'HTML'
						),
			)
	);

}

?>