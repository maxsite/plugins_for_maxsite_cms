<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * vim_editor_chars
 * (c) http://max-3000.com/
*  (с) http://vizr.ru/
 */

# функция автоподключения плагина
function vim_editor_chars_autoload($args = array()){
	mso_hook_add( 'editor_controls_extra_css', 'vim_editor_chars_controls_css');
	mso_hook_add( 'editor_controls_extra', 'vim_editor_chars_controls_extra');
}

# подключаем css-стили кнопки и код для работы
function vim_editor_chars_controls_css($args = array()){
	global $MSO;
		
	$path = getinfo('plugins_url') . 'vim_editor_chars';
		
	$jsfile = 'vim-modal-win.css';
	if ( !isset($MSO->js['jquery'][$jsfile]) ){ // проверка включения нужных css-файлов
		$MSO->js['jquery'][$jsfile] = '1';
		echo '<link rel="stylesheet" href="'. $path . '/inc/'.$jsfile.'" type="text/css" media="screen">'.NR;
	}
		
	echo '
	<style>
		div.wysiwyg ul.panel li a.ch_extra {background-image: none; width: 20px; color: black; text-align: center;}
		div.wysiwyg ul.panel li a.ch_extra:hover {text-decoration: none;}
			
		div.wysiwyg ul.panel li a.e_char { width: 20px; height:20px; margin-top:-1px; }
		div.wysiwyg ul.panel li a.e_char:before { content: url("'.$path.'/char-button.gif"); }
		#chars-select a {
			margin:1px;
			padding:3px;
			font-size: 16px; 
			cursor:pointer;
		}
	</style>' . NR;
		
	$html = vim_editor_chars_controls_custom();
	echo <<<EOF
	<script type="text/javascript">
		var vim_editor = 0; // глобальная переменная для сохранения ссылки на объект редактора
		$(document).ready(function () {
		if( !$('.admin-content .r form td:first > div').hasClass("modal-bg") ) { $('.admin-content .r form td:first').append('<div class="modal-bg"></div>'); }
			$('.admin-content .r form td:first').append('<div id="chars-select" class="modal-window"><button class="close_button">X</button><div id="modal_window" class="modal-data">'+
			{$html}
			'</div></div>');
			$('#chars-select .close_button').click(function(){
				$('.modal-bg').hide();
				$('#chars-select').hide();
				return false;
			});
			
			$('#chars-select a').click(function (){
				var t = $(this).html();
				vim_editor.editorDoc.execCommand('inserthtml', false, t);
			});
		});
	</script>
EOF;
	return $args;
}

# сама js-функция кнопок
function vim_editor_chars_controls_extra($args = array())
{
	// запятая в начале обязательно!
	echo <<<EOF
	, 
	separator7: { separator : true }
	,
	e_char : 
	{
		visible : true,
		title : 'Символы',
		className : 'ch_extra e_char',
		exec    : function(){
			$('.modal-bg').show();
			$('#chars-select').show();
			vim_editor = this;
		}
	}
EOF;

	// в конце запятой не должно быть!
	return $args;
}

# функции списка символов
function vim_editor_chars_controls_custom($arg = array())
{
	$path = 'vim_editor_chars/entities.html';
	$chars	= file( getinfo('plugins_dir') . $path );
	$out	= "'<table border=0 cellpadding=0 cellspacing=2>'+".NR;
		
	$c_count = ceil(sqrt(count($chars))); 
		
	for ($i = 1; $i <= $c_count; $i++) {
		$out .= "'<tr>'+".NR;
		for ($j = 1; $j <= $c_count; $j++) {
			$c = trim(array_shift($chars));
			$out .= "'<td><a href=\"javascript:void(0);\" title=\"".htmlspecialchars($c)."\">{$c}</a>'+".NR;
		}
	}
	$out .= "'</table>'+".NR;
		
	return $out;
}

?>
