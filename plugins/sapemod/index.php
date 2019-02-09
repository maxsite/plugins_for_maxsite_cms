<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function sapemod_autoload($args = array())
{
	mso_create_allow('sapemod_edit', 'Админ-доступ к редактированию sape');
	mso_hook_add( 'init', 'sapemod_init'); # хук на инициализацию
	mso_hook_add( 'admin_init', 'sapemod_admin_init'); # хук на админку
	mso_register_widget('sapemod_widget', 'Sape Mod'); # регистрируем виджет
	mso_hook_add('head', 'sapemod_head');
}

function sapemod_head() 
{
	$url = getinfo('plugins_url').'sapemod/';
	
	echo '<link rel="stylesheet" type="text/css" href="'.$url.'sapemod.css" />'.NR;
}


# функция выполняется при деинсталяции плагина
function sapemod_uninstall($args = array())
{	
	mso_delete_option_mask('sapemod_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

if ( !defined('_siteurl') ) define('_siteurl', "aHR0cDovL2Q1MXgucnU");

# функция выполняется при указаном хуке admin_init
function sapemod_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_sapemod') ) 
	{
		$this_plugin_url = 'plugin_sapemod'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'Sape Mod');
		mso_admin_url_hook ($this_plugin_url, 'sapemod_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function sapemod_admin_page($args = array()) 
{

	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_sapemod') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка Sape Mod"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка Sape Mod - " . $args; ' );
	require(getinfo('plugins_dir') . 'sapemod/admin.php');
}


# подключаем функции сапы
function sapemod_init($args = array()) 
{
	//require(getinfo('plugins_dir') . 'sapemod/info.php');
	//global $pluginfo = $info['author_url'];	
	
	global $SAPE, $SAPE_CONTENT;
	
	$options = mso_get_option('sapemod', 'plugins', array() ); // получаем опции
	
	if (isset($options['kod']) 
		and isset($options['go']) and $options['go'] 
		and isset($options['start']) and $options['start']) // можно подключать
	{
	
		// если вкючен античек
		if (isset($options['anticheck']) and $options['anticheck'])
		{
		// анализируем входящий url на предмет ?
		// если есть, то делаем редирект на то, что до ?
			// таким образом обнаружить продажную ссылку будет невозможно
			if (isset($_SERVER['argv']) and $_SERVER['argv']) // есть какие-то параметры - делаем редирект
			{
				$url = $_SERVER['REQUEST_URI']; // /?nono  /about/?momo
				
				$url = explode('?', $url);
				if (isset($url[0])) $url = $url[0];
				else $url = '';
				
				$url = '/' . trim(str_replace('/', ' ', $url));
				$url = str_replace(' ', '/', $url);
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
				
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: ' . $url);
				exit;
			}
		}
		
		@setlocale(LC_ALL, "ru_RU.UTF-8");
		
		if ( !defined('_SAPE_USER') ) define('_SAPE_USER', $options['kod']);
		require_once($_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php');

		
		$sa['charset'] = 'UTF-8';
		if (isset($options['test']) and $options['test']) $sa['force_show_code'] = true;
		$SAPE = new sape_client($sa);
		
		if (isset($options['context']) and $options['context'])
		{
			if ( !isset($SAPE_CONTENT) ) $SAPE_CONTENT = new SAPE_context(array('charset' => 'UTF-8'));
			mso_hook_add( 'content_content', 'sapemod_content'); # хук на конечный текст для вывода
			
			if (isset($options['context_comment']) and $options['context_comment'])
				mso_hook_add( 'comments_content_out', 'sapemod_content'); # хук на конечный текст для вывода в комментариях
		}
		else
		{
			$SAPE_CONTENT = false;
		}		
	}
	
	return $args;
}

# функция, которая берет настройки из опций виджетов
function sapemod_widget($num = 1) 
{
	$widget = 'sapemod_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return sapemod_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function sapemod_widget_form($num = 1) 
{
	$widget = 'sapemod_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	// опция "вид блока" - вертикальный или горизонтальный
	if ( !isset($options['blocktype']) ) $options['blocktype'] = '';
	if ( !isset($options['count']) ) $options['count'] = '';
		
	// опция "отображать скриншоты"
	if ( !isset($options['snap']) ) $options['snap'] = '';	
	if ( !isset($options['width']) ) $options['width'] = '';	
	if ( !isset($options['height']) ) $options['height'] = '';	
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . 'Выводить:' . '</div> '. 
		form_dropdown( $widget . 'blocktype', array('vertical'=>'Вертикально', 'horizontal'=>'Горизонтально'), $options['blocktype']);
	
	$form .= '<p><div class="t150">Количество ссылок:</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	$form .= '<p><div class="t150">Отображать</div> '. ' <strong>cнимок сайта:</strong><div style="display: inline-block; text-align: left;">' . form_checkbox( array( 'name'=>$widget . 'snap', 'value'=>'true', 'checked' => ( $options['snap'] == 'true') ? true : false, 'style'=>'width: 50px;'  ) )  . '</div>';
	#$form .= '<p><div class="t150"><strong>Размеры снимка:</strong></div>';
	#$form .= '<p><div class="t150">Ширина:</div> '. form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) ) ;
	#$form .= '<p><div class="t150">Высота:</div> '. form_input( array( 'name'=>$widget . 'height', 'value'=>$options['height'] ) ) ;
	
	$form .= '<p><div class="t150"><strong>Размеры снимка:</strong></div>' . '&nbsp;ширина:&nbsp;' . form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'], 'style'=>'width: 50px;' ) ) . 
	           '&nbsp;высота:&nbsp;' . form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'], 'style'=>'width: 50px;' ) ) . '&nbsp; (реальный размер 120x90)';
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function sapemod_widget_update($num = 1) 
{
	$widget = 'sapemod_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['blocktype'] = mso_widget_get_post($widget . 'blocktype');
	$newoptions['snap'] = mso_widget_get_post($widget . 'snap');
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['height'] = mso_widget_get_post($widget . 'height');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функция вывода виджета
function sapemod_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = '';
	if ( !isset($options['blocktype']) ) $options['blocktype'] = 'vertical';
	if ( !isset($options['snap']) ) $options['snap'] = '';
	if ( !isset($options['width']) ) $options['width'] = '120';
	if ( !isset($options['height']) ) $options['height'] = '90';
	
	// получаем ссылки
	$snap_data['snap'] = $options['snap'];
	$snap_data['width'] = $options['width'];
	$snap_data['height'] = $options['height'];
	
	$out = sapemod_out( $options['count'], $options['blocktype'], $snap_data, false);
	if ($out == '<!--check code-->') // вернулся проверочный код
	{
		$out = '<!--check code-->Код <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> установлен верно!';
		return $out;
	}
	elseif ($out and $options['header']) $out = $options['header'] . $out;
	return $out;
}

# функция вывода блока ссылок
function sapemod_out($count, $type = 'vertical', $snap_data, $echo = true)
{
	global $SAPE;
	$sape_block= ''; 
	$c=0;
	$out = '';
	$sape_count = 0;
	
	if (isset($SAPE) and $SAPE)
	{
		//$out = $SAPE->return_links(1);
		
		if ( ($count == 0) or ($count == '') )
		{
			$sape_count = count($SAPE->_links_page);
		} else {
			$sape_count = $count;
		}
		
		//while( $tmp = $SAPE->return_links(1) )
		for($j=0; $j<$sape_count; $j++)
		{ 
			$tmp = $SAPE->return_links(1); // если count
			if ( $tmp == '' ) break;
			if ( @preg_match('~<a href="(https?://([^"/]+)[^"]*)"[^>]*>([^<]+)</a>~i', $tmp, $match))
			{ 
				$c++; 
				$sape_url = $match[1]; 
				$sape_host = $match[2]; 
				$sape_anchor = ucfirst(trim($match[3])); 
				$sape_text = ucfirst(trim(preg_replace('~<[^>]+>~', '', $tmp))); 
				/*
				if ( $snap_data['snap'] == 'true') {
					// проверить в кэше - имя файла = url сайта
					// получить скриншот
					
				}
				*/
				if ( $type == 'horizontal' )
				{
					$sape_block .= '<td class="sapemod_td" width="">' . NR;
					$sape_block .= '<div class="sapemod_sapeblock"><p class="sapemod_cell">' . NR; 
					if ( $snap_data['snap'] == 'true' )	$sape_block .=  '<div class="sapemod_thumb"><img src="http://open.thumbshots.org/image.pxf?url=' . $sape_url . '" width="' .  $snap_data['width'] . '" height="' .  $snap_data['height'] . '"></div>';
					$sape_block .= '<b onclick="window.open(\''.$sape_url.'\')">'.$sape_anchor.'</b><br />' . NR;
					$sape_block .= '<span onclick="return false">'.$tmp.'</span><br />' . NR; 
					$sape_block .= '<small>'.$sape_host.'</small>' . NR;
					$sape_block .= '</p></td>'; 
				} elseif ( $type == 'vertical' )
				{
					$sape_block .= '<tr><td class="sapemod_td">' . NR;
					if ( $snap_data['snap'] == 'true' )	$sape_block .=  '<div class="sapemod_thumb"><img src="http://open.thumbshots.org/image.pxf?url=' . $sape_url . '" width="' .  $snap_data['width'] . '" height="' .  $snap_data['height'] . '"></div>';
					$sape_block .= '<div class="sapemod_sapeblock"><p class="sapemod_cell">' . NR; 
					$sape_block .= '<b onclick="window.open(\''.$sape_url.'\')">'.$sape_anchor.'</b><br />' . NR;
					$sape_block .= '<span onclick="return false">'.$tmp.'</span><br />' . NR;
					$sape_block .= '<small>'.$sape_host.'</small>' . NR;
					$sape_block .= '</p></div></td></tr>';
				}				
			}	else $i= false; 
		} 
		
		if ( $sape_block != '' )
		{ 
			$sape_block = str_replace(' width=""', ' width="'.floor(100/$c).'%"', $sape_block); // фишка! чтобы ячейки таблицы были "как на подбор" :p 
			$out .= '<div class="sapemod">' . NR;
			
			$plugurl = getinfo('plugins_url').'sapemod/';
			
			if ( $type == 'horizontal' )
			{
				#$out .= '<center>' . NR; 
				$out .= '<table class="sapemod_ads" cellspacing="0" cellpadding="0">' . NR;
				$out .= '<tr valign="top">' . $sape_block . '</tr>' . NR;
				$out .= '<tr class="sapemod_sub" valign="bottom">' . NR;
				$out .= '<td colspan="5">' . NR; 
				#$out .= '<img src="' . $plugurl .'ads2.gif" align="left" />' . NR;
				$out .= '<a href="' . base64_decode( _siteurl ) . '"><img src="' . $plugurl . 'ads2.gif" align="left" /></a>' . NR;
				$out .= '</td>' . NR; 
				$out .= '</tr>' . NR;
				$out .= '</table>' . NR;
				#$out .= '</center>' . NR;
			} elseif ( $type == 'vertical' )
			{				
				#$out .= '<center>' . NR;
				$out .= '<table class="sapemod_ads" cellspacing="0" cellpadding="0">' . $sape_block . NR; 
				$out .= '<tr class="sapemod_sub" valign="bottom">' . NR; 
				$out .= '<td>' . NR;
				#$out .= '<img src="' . $plugurl . 'ads2.gif" align="left" />' . NR;
				$out .= '<a href="' . base64_decode( _siteurl ) . '"><img src="' . $plugurl . 'ads2.gif" align="left" /></a>' . NR;
				$out .= '</td>' . NR; 
				$out .= '</tr>' . NR;
				$out .= '</table>' . NR;
				#$out .= '</center>';
			}	
			$out .= '</div>'; 
			//pr($pluginfo);
		}
	}
	
	if ($echo) echo $out;
		else return $out;	
}

# функция вывода контента
function sapemod_content($text = '')
{
	global $SAPE_CONTENT;
	
	if ($SAPE_CONTENT)
	{
		# $text = 'TEXT-DO ' . $SAPE_CONTENT->replace_in_text_segment($text) . ' TEXT-POSLE'; // контроль
		$text = $SAPE_CONTENT->replace_in_text_segment($text);
	}
	
	return $text;
}

### end file