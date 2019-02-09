<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Uppod-плеер» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-uppod-player
 */

# функция автоподключения плагина
function uppod_autoload($args = array())
{
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow('uppod_options', 'Админ-доступ к опциям плагина «Uppod-плеер»');
		
	mso_hook_add('head', 'uppod_head'); # хук для подключения стилей на внешних страницах
		
	# Обработка контента
	mso_hook_add('content', 'uppod_content'); # хук на вывод контента
		
	# Нужна ли обработка комментариев?
	$options = uppod_get_options(); # получаем опции
	if( $options['comments'] )
	{
		mso_hook_add('comments_content_out', 'uppod_content'); # обработка комментариев
	}
		
	# Подключаем кнопки в редакторах
	if( $options['editor_buttons'] )
	{
		mso_hook_add('editor_controls_extra_css', 'uppod_editor_controls_extra_css');
		mso_hook_add('editor_controls_extra', 'uppod_editor_controls_extra');
			
		mso_hook_add('admin_head', 'uppod_admin_head'); # хук для подключения стилей на внутренних страницах
		mso_hook_add('editor_markitup_bbcode', 'uppod_editor_markitup_bbcode');
	}
}

# функция выполняется при активации (вкл) плагина
function uppod_to_hook_activate($args = array())
{
	// по-хорошему, здесь нужно провести сканирование статей для составления плейлиста для виджета
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function uppod_to_hook_deactivate($args = array())
{
	return $args;
}

# функция выполняется при деинсталяции плагина
function uppod_uninstall($args = array())
{
	# удалим созданные опции
	mso_delete_option('plugin_uppod', 'plugins');
		
	# удалим созданные разрешения
	mso_remove_allow('uppod_options');
		
	return $args;
}

# формирование страницы опций в админке
function uppod_mso_options()
{
	if( !mso_check_allow('uppod_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	# получаем опции
	$options = uppod_get_options(false);
		
	# выносим функции формирования массива опций в отдельный файл
	require(getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-options.php');
}

# подключение своих стилей на внешних страницах
function uppod_head($args = array())
{
	global $MSO;
		
	$plugin_url = getinfo('plugins_url').basename(dirname(__FILE__)).'/'; # внешний адрес папки плагина
		
	# получаем опции
	$options = uppod_get_options();
		
	# если JS вариант плеера
	if( $options['pleerversion'] == 'html5' )
	{
		# подключение скрипта плеера
		echo NR.mso_load_jquery( 'uppod.js', $plugin_url );
			
		# подключения js-стиля для видео-плеера
		if( $options['style_video'] != '' )
		{
			echo mso_load_jquery( $options['style_video'], $plugin_url.'style/' );
		}
			
		# подключения js-стиля для аудио-плеера
		if( $options['style_audio'] != '' )
		{
			echo mso_load_jquery( $options['style_audio'], $plugin_url.'style/' );
		}
	}
		
	# если JS вариант плеера
	if( $options['pleerversion'] == 'swf' )
	{
		# подключение скрипта SWFObject
		echo mso_load_jquery( 'swfobject.js', 'http://ajax.googleapis.com/ajax/libs/swfobject/2.2/' );
	}
		
	return $args;
}

# функция подключения дополнительных стилей  в админке
function uppod_admin_head($args = array())
{
	if( mso_segment(1) == 'admin' && ( mso_segment(2) == 'page_edit' || mso_segment(2) == 'page_new' ) )
	{
		# Стили	
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/editor-options.css" type="text/css" media="screen">'.NR;
	}
}

# Основная функция плагина - поиск своих бб-кодов при выводе контента
function uppod_content($text)
{
	# получаем опции
	$options = uppod_get_options();
		
	$tags = array();
	
	if( $options['tag_youtube'] )
	{
		$tags[] = 'youtube';
	}
		
	$tags[] = trim($options['code_video']);
	$tags[] = trim($options['code_audio']);
	$tags = implode('|', array_filter($tags));
		
	# Обрабатываем раздельные тэги video/audio
	$pattern = '/\[('.$tags.')\](.*?)\[\/\\1\]/mis';
	$text = preg_replace_callback($pattern, 'uppod_replace_main', $text);
		
	if( $options['tag_uppod'] )
	{
		# Обрабатываем универсальный тэг uppod
		$pattern = '/\[(uppod)(.*?)\]/mis';
		$text = preg_replace_callback($pattern, 'uppod_replace_main', $text);
	}
		
    return $text;
}

# функция замены
function uppod_replace_main($matches)
{
	#pr($matches);
	global $MSO;
	# определяем глобальный счётчик сделанных плагином замен
	if( !isset($MSO->uppod) )
	{
		$MSO->uppod = 1;
	}
	else
	{
		$MSO->uppod++;
	}
	$uid = 'uppodpleer'.$MSO->uppod;
		
	# получаем опции
	$options = uppod_get_options();
		
	$plugin_url = getinfo('plugins_url').basename(dirname(__FILE__)).'/'; # внешний адрес папки плагина
		
	$out = '';
	if( $matches[1] == 'uppod' && $options['tag_uppod'] )
	{
		$pattern = '/(file|type|caption|style|width|height|pl|poster|volume|comment|st|vol)\=(.*?)([\s]|$)/mis';
	}
	else
	{
		$pattern = '/\[(caption|style|width|height|pl|poster|volume|comment|st|vol)\](.*?)\[\/\\1\]/mis';
	}
	if( preg_match_all($pattern, $matches[2], $tags) || $matches[2] != '' )
	{
		$tags = isset($tags) ? $tags : array();
			
		if( $matches[1] == 'uppod' && $options['tag_uppod'] )
		{
			foreach( $tags[1] as $key => $tag )
			{
				if( $tag == 'file' )
				{
					$media = $tags[2][$key];
				}
					
				if( $tag == 'type' )
				{
					$type = $tags[2][$key] == 'video' ? 'video' : 'audio';
				}
			}
				
			$media = isset($media) ? $media : '';
			$type = isset($type) ? $type : 'audio';
		}
		else
		{
			$type  = trim($options['code_video']) == $matches[1] | $matches[1] == 'youtube' ? 'video' : 'audio';
				#pr($matches[2]);
			$media = preg_replace($pattern, '', $matches[2]);
		}
		#pr($media);	
		if( $options['width_audio'] != '' && $options['width_video'] != '' )
		{
			$width  = $type == 'audio'? $options['width_audio'] : $options['width_video'];
		}
		if( $options['height_audio'] != '' && $options['height_video'] != '' )
		{
			$height = $type == 'audio'? $options['height_audio'] : $options['height_video'];
		}
			
		if( $media != '' )
		{
			# делаем адрес медиа-файла абсолютным
			$media = substr($media, 0, 1) == '/' ? substr($media, 1) : $media;
			$media = strpos($media, 'http://') === false ? getinfo('siteurl').$media : $media;
				
			$opt = array('m:"'.$type.'"', 'file:"'.$media.'"', 'uid:"'.$uid.'"');
			foreach( $tags[1] as $key => $tag )
			{
				$v = preg_replace("/^[\"\']*|[\"\']*$/msi", '', trim($tags[2][$key]));
					
				$k = mb_strtolower($tag);
				$k = ( $k == 'style' ) ? 'st' : $k;
				$k = ( $k == 'caption' ) ? 'comment' : $k;
				$k = ( $k == 'vol' ) ? 'volume' : $k;
					
				if( $k == 'width' )
				{
					$width = $v;
					continue;
				}
					
				if( $k == 'height' )
				{
					$height = $v;
					continue;
				}
					
				if( $k == 'poster' || $k == 'st' || $k == 'pl' )
				{
					$v = substr($v, 0, 1) == '/' ? substr($v, 1) : $v;
					$v = strpos($v, 'http://') === false ? getinfo('siteurl').$v : $v;
				}
					
				if( $k == 'st' && $options['pleerversion'] == 'html5' )
				{
					$stfile = file_get_contents($v);
					$stfile = preg_replace("/var uppod".$type." =/msi", 'var uppod'.$type.$MSO->uppod.' =', trim($stfile));
					$v = 'uppod'.$type.$MSO->uppod;
				}
					
				if( $k == 'type' || $k == 'file' ) continue;
					
				$opt[] = $k.':"'.$v.'"';
			}
			#pr($opt);
			$placestyle = array();
			if( isset($width) ) $placestyle[] = 'width:'.$width.'px';
			if( isset($height) ) $placestyle[] = 'height:'.$height.'px';
			if( count($placestyle) > 0 )
			{
				$placestyle = ' style="'.implode(';', $placestyle).'"';
			}
			else
			{
				$placestyle = '';
			}
			$placeholder = '<div id="'.$uid.'"'.$placestyle.'>'.$options['warning_message'].'</div>';
				
			if( $options['pleerversion'] == 'html5' ) # если тип плеера HTML5
			{
				if( !isset($stfile) )
				{
					$stfile = '';
						
					if( $options['style_'.$type] != '' )
					{
						$opt[] = 'st:"uppod'.$type.'"';
					}
				}
					
				$script = $stfile.'this.'.$uid.' = new Uppod({'.implode(',', $opt).'});';
					
				unset($stfile);
			}
			else # в остальных случаях считаем, что тип плеера SWF
			{
				$script = 'var flashvars = {'.implode(',', $opt).'};var params = {bgcolor:"#'.$options['bgcolor'].'", allowFullScreen:"'.($type == $options['code_video'] && $options['fullscreen'] ? 'true':'false').'", allowScriptAccess:"always", id:"'.$uid.'"}; new swfobject.embedSWF("'.$plugin_url.'uppod.swf", "'.$uid.'", "'.( isset($width) ? $width : '' ).'", "'.( isset($height) ? $height : '' ).'", "9.0.115.0", "'.$plugin_url.'expressInstall.swf", flashvars, params);';
			}
				
			if( $options['center'] )
			{
				$placeholder = '<center>'.$placeholder.'</center>';
			}
				
			$script = '<script type="text/javascript">'.NR.$script.NR.'</script>'.NR;
				
			$out = $placeholder.NR.$script;
		}
	}
		
	return $out;
}

# подключаем css-стили своих кнопок в редакторе editor_nic
function uppod_editor_controls_extra_css($args = array())
{
	echo '
	<style>
		div.wysiwyg ul.panel li a.e_uppod_v {background: url(\'/application/maxsite/admin/plugins/editor_markitup/images/flash.png\') no-repeat scroll 0 0 transparent;}
		div.wysiwyg ul.panel li a.e_uppod_a {background: url(\'/application/maxsite/admin/plugins/editor_markitup/images/audio.png\') no-repeat scroll 0 0 transparent;}
	</style>' . NR;
		
	return $args;
}

# интеграция в editor_nic
function uppod_editor_controls_extra($args = array())
{
	# получаем опции
	$options = uppod_get_options();
		
	# запятая в начале обязательна!
	echo <<<EOF
	, 

	e_uppod_v : 
	{
		visible : true,
		title : 'Uppod-плеер для видео',
		className : 'extra e_uppod_v',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '<br>[{$options['code_video']}]' + selection + '[/{$options['code_video']}]<br>');
		}
	},
	
	e_uppod_a : 
	{
		visible : true,
		title : 'Uppod-плеер для аудио',
		className : 'extra e_uppod_a',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '<br>[{$options['code_audio']}]' + selection + '[/{$options['code_audio']}]<br>');
		}
	},
	separator3 : { separator : true }
	
EOF;
	# в конце запятой не должно быть!
		
	return $args;
}

# интеграция в editor_markitup
function uppod_editor_markitup_bbcode($args = array())
{
	# получаем опции
	$options = uppod_get_options();
		
	$uppod = '';
	$main = "[{$options['code_video']}] [/{$options['code_video']}]";
	if( $options['tag_uppod'] )
	{
		$main = '[uppod ]';
		$uppod = <<<EOF
			{name:'Видео [uppod]', openWith:'[uppod type={$options['code_video']} file=]', className:"movies"},
			{name:'Аудио [uppod]', openWith:'[uppod type={$options['code_audio']} file=]', className:"movies"},
			{name:'Плейлист [uppod]', openWith:'[uppod pl=]', className:"movies"},
			{name:'Все атрибуты [uppod]', openWith:'type= file= pl= style= caption= width= height= vol=', className:"movies"},
			{name:'Полный комплект [uppod]', openWith:'[uppod type= file= pl= style= caption= width= height= vol=]', className:"movies"},
			{separator:'---------------' },
EOF;
	}

	if( $options['tag_youtube'] )
	{
		$youtube = <<<EOF
			{name:'[youtube]', openWith:'[youtube]', closeWith:'[/youtube]', className:"youtube"}, 
			{separator:'---------------' },
EOF;
	}
	else
	{
		$youtube = '';
	}

	echo <<<EOF
		{separator:'---------------' },	
		
		{name:'Плеер', openWith:'{$main}', className:"flash", dropMenu: [
			{$uppod}
			{name:'[{$options['code_video']}]', openWith:'[{$options['code_video']}]', closeWith:'[/{$options['code_video']}]', className:"flash"}, 
			{name:'[{$options['code_audio']}]', openWith:'[{$options['code_audio']}]', closeWith:'[/{$options['code_audio']}]', className:"audio"}, 
			{name:'[{$options['code_video']}]+[атрибуты]', openWith:'[{$options['code_video']}][caption][/caption][style][/style][width][/width][height][/height]', closeWith:'[/{$options['code_video']}]', className:"flash"},
			{name:'[{$options['code_audio']}]+[атрибуты]', openWith:'[{$options['code_audio']}][caption][/caption][style][/style][width][/width][height][/height]', closeWith:'[/{$options['code_audio']}]', className:"audio"},
			{separator:'---------------' },{$youtube}
			{name:'Справка по кодам', className:'help', beforeInsert:function(){ window.open('http://vizr.ru/page/plugin-uppod-player-help'); } },
		]},
EOF;
	return $args;
}

# подключаем файл для работы с опциями
require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/lib-options.php' );
?>