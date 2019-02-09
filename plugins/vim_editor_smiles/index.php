<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * vim_editor_smiles
 * (c) http://max-3000.com/
*  (с) http://vizr.ru/
 */

# функция автоподключения плагина
function vim_editor_smiles_autoload($args = array()){
	mso_hook_add( 'editor_controls_extra_css', 'vim_editor_smiles_controls_css');
	mso_hook_add( 'editor_controls_extra', 'vim_editor_smiles_controls_extra');
}

# подключаем css-стили кнопки и код для работы
function vim_editor_smiles_controls_css($args = array()){
	global $MSO;
		
	$path = getinfo('plugins_url') . 'vim_editor_smiles';
		
	$jsfile = 'vim-modal-win.css';
	if ( !isset($MSO->js['jquery'][$jsfile]) ){ // проверка включения нужных css-файлов
		$MSO->js['jquery'][$jsfile] = '1';
		echo '<link rel="stylesheet" href="'. $path . '/inc/'.$jsfile.'" type="text/css" media="screen">'.NR;
	}
		
	echo '
	<style>
		div.wysiwyg ul.panel li a.sm_extra {background-image: none; width: 20px; color: black; text-align: center;}
		div.wysiwyg ul.panel li a.sm_extra:hover {text-decoration: none;}
			
		div.wysiwyg ul.panel li a.e_smile { width: 20px; height:20px; margin-top:-1px; }
		div.wysiwyg ul.panel li a.e_smile:before { content: url("'.$path.'/smile-button.gif"); }
	</style>' . NR;
		
	$html = vim_editor_smiles_controls_custom();
	echo <<<EOF
	<script type="text/javascript">
		var vim_editor = 0; // глобальная перемная для сохранения ссылки на объект редактора
		$(document).ready(function () {
		if( !$('.admin-content .r form td:first > div').hasClass("modal-bg") ) { $('.admin-content .r form td:first').append('<div class="modal-bg"></div>'); }
			$('.admin-content .r form td:first').append('<div id="smiles-select" class="modal-window"><button class="close_button">X</button><div id="modal_window" class="modal-data">'+
			{$html}
			'</div></div>');
			$('#smiles-select .close_button').click(function(){
				$('.modal-bg').hide();
				$('#smiles-select').hide();
				return false;
			});
			
			$('#smiles-select a').click(function (){
				var t = $(this).html();
				vim_editor.editorDoc.execCommand('inserthtml', false, t);
			});
		});
	</script>
EOF;
	return $args;
}

# сама js-функция кнопки
function vim_editor_smiles_controls_extra($args = array())
{
	// запятая в начале обязательно!
	echo <<<EOF
	, 
	e_smile : 
	{
		visible : true,
		title : 'Смайлы',
		className : 'sm_extra e_smile',
		exec    : function(){
			$('.modal-bg').show();
			$('#smiles-select').show();
			vim_editor = this;
		}
	},
	separator6: { separator : true }
	
EOF;
	// в конце запятой не должно быть!
	return $args;
}

# функции генерации списка смайлов
function vim_editor_smiles_controls_custom($arg = array())
{
	$path = 'vim_editor_smiles/smiles/';
	$image_dir = getinfo('plugins_dir') . $path;
	$image_url = getinfo('plugins_url') . $path;
	$files	= scandir($image_dir);
	$out	= "";
		
	foreach ($files as $id => $f) {
		$img = $image_dir.$f;
		if($f != '.' and $f != '..' and is_file($img)){
			$size = getimagesize($img);
			if(is_array($size)){
				list($iname, $ext) = split('\.', $f);
				$out .= "'<a href=\"javascript:void(0);\"><img src=\"".$image_url.$f."\" {$size[3]} title=\"{$iname}\" alt=\"{$iname}\" class=\"smile\" /></a>'+".NR;
			}
		}
	}
	return $out;
}

?>