<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
$seop = mso_get_option('seotext', 'plugins', array() );
if (!isset($seop['down'])) $seop['down'] = false;
$info = array(
    'name' => 'Seotext',
    'description' => 'Плагин для вывода сеотекста (По дефолту выводться вверху). <br/> <lablel for="seotext_down"><b>Выводить сеотекст ниже новостей - </b></label><input type="checkbox" name="seotext_down" value="true" id="seotext_down" '.(($seop['down'])?'chechked=checked':'').' onchange=\'$.post("'.'/ajax/'. base64_encode('plugins/seotext/option-ajax.php').'","seotext_down="+((this.checked)?"true":"false"));\' />',
    'version' => '1.0',
    'author' => 'Юрий Ш.',
    'plugin_url' => 'http://jet-web.org/maxsite/seotext',
    'author_url' => 'http://jet-web.org/',
    'group' => 'template'
);

?>