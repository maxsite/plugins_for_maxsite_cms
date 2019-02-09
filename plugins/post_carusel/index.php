<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function post_carusel_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'post_carusel_admin_init'); # хук на админку
	mso_hook_add( 'head', 'post_carusel_head');
	mso_hook_add( 'body_end', 'post_carusel_body_end');
	mso_hook_add( 'body_start', 'post_carusel_body_start');
	mso_hook_add( 'admin_page_form_add_all_meta', 'post_carusel_add_form');
}

# функция выполняется при деинсталяции плагина
function post_carusel_uninstall($args = array())
{	
	mso_delete_option_mask('post_carusel_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function post_carusel_admin_init($args = array()) 
{
	$this_plugin_url = 'post_carusel'; // url и hook
		
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
		
	mso_admin_menu_add('plugins', $this_plugin_url, t('Карусель постов', __FILE__));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/androidfan_models
	mso_admin_url_hook ($this_plugin_url, 'post_carusel_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function post_carusel_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('post_carusel', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('post_carusel', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'post_carusel/admin.php');
}


function post_carusel_head($args = array())
{
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'post_carusel/js/stepcarousel.js"></script>';

	$css_file = getinfo('plugins_url') . 'post_carusel/board.css';
	if ( file_exists( getinfo('template_dir') . 'board.css' ) ) $css_file = getinfo('template_url') . 'board.css';
	echo '<link rel="stylesheet" href="' . $css_file . '" type="text/css" media="screen">';
	
	return $args;
}

function post_carusel_show( $w = '', $h = '' )
{
	$options_key = 'post_carusel';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if ( !isset($options['count'])) $options['count'] = 5;
	$options['count'] = (int) $options['count'];
	if ($options['count'] < 1) $options['count'] = 5;
	$cnt = $options['count'];

	if ( !isset( $w ) or empty( $w ) )
	{
		if ( !isset($options['width'])) $options['width'] = 908;
		$options['width'] = (int) $options['width'];
		if ($options['width'] < 1) $options['width'] = 908;
		$width = $options['width'];
	} else {
		$width = (int)$w;
		if ($width < 1) $width = 908;
	}

	if ( !isset( $h ) or empty( $h ) )
	{
		if ( !isset($options['height'])) $options['height'] = 226;
		$options['height'] = (int) $options['height'];
		if ($options['height'] < 1) $options['height'] = 226;
		$height = $options['height'];
	} else {
		$height = (int)$h;
		if ($height < 1) $height = 226;
	}
	
	$height2 = $height - 40;
	$height3 = $height2 - 2;
	if( !isset( $options['pagehooks'])) $options['pagehooks'] = 0;
	$pagehooks = $options['pagehooks'];
	
	if( !isset( $options['randompage'])) $options['randompage'] = 0;
	$randompage = $options['randompage'];
	$randompage = ( $randompage ) ? 'random' : 'desc';
	
	$out = '';
	if ( $cnt > 0) 
	{
	
		//$cache_key = 'post_carusel' . mso_md5(serialize($options));
		
		//$k = mso_get_cache($cache_key, true);
		//if ($k) return $k; // да есть в кэше
	
		$cpar = array(  'limit' => $cnt, 
						'cut' => mso_get_option('more', 'templates', 'Далее…'),
						'type'=> 'blog',
						'custom_type'=> 'home',
						'pagination'=> false,
						'a_link_cut'=>'',
						'order' => 'page_id',
						'order_asc' => $randompage
					); 

		$cpages = mso_get_pages($cpar, $cpagination);
		if ( $cpages ) 
		{ 
			$coint_i = count($cpages); 

			$out .= '<div id="board" style="width: ' . $width . 'px; height: ' . $height . 'px;"><div id="board_left"><div id="board_items" style="width: ' . ($width - 40 ) . 'px;"><div id="board_body"><div id="board_carusel" style="width: ' . ($width - 40) . 'px; height: ' . $height2 . 'px;"><div class="belt">';

			foreach ($cpages as $cpage) 
			{ 
				extract($cpage);
				$out .= '<div class="board_item" style="width: ' . ($width-40) . 'px; height: ' . $height3 . 'px;">';
				$out .= mso_page_title($page_slug, $page_title, '<h2>', '</h2>', true, false);
				$out .= mso_page_meta('caruselpict', $page_meta, '<a href="'.getinfo('siteurl').'page/'.$page_slug.'" class="cpict"><img src="'.getinfo('siteurl'), '" alt="'.$page_title.'" ></a>', ' ', false);
				if ( $ctext = mso_page_meta('caruseltext', $page_meta, '', '', '', false)) {
					$out .= $ctext.'<p><a href="'.getinfo('siteurl').'page/'.$page_slug.'">'.mso_get_option('more', 'templates', 'Далее…').'</a></p>';
				} else {
					if ( $pagehooks ) $page_content = mso_hook('content_content', $page_content);
					$out .= $page_content; 				
				}
				$out .= '</div>';
			}
			
			$out .= '</div></div></div><div class="brdcar"><ul id="board_carusel_nav">';
			
			for( $i = 1; $i <= $coint_i; $i++ ) 
			{ 
				$sel = ( $i == 1 ) ? ' class="selected"' : '';
				$out .= '<li id="board_carusel_nav_' . $i . '"><a ' . $sel . ' href="javascript:stepcarousel.stepTo(\'board_carusel\', ' . $i . ')">' . /*$i*/'' . '</a></li>';
			}
			
			$out .= '</ul></div></div></div></div>';
		}
		//mso_add_cache($cache_key, $out, false, true); // сразу в кэш добавим
	}	
	return $out;
}

function post_carusel_body_end( $args = array() )
{
	$options_key = 'post_carusel';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if ( !isset($options['count'])) $options['count'] = 5;
	$options['count'] = (int) $options['count'];
	if ($options['count'] < 1) $options['count'] = 5;

	if ( !isset($options['speed'])) $options['speed'] = 700;
	$options['speed'] = (int) $options['speed'];
	if ($options['speed'] < 1) $options['speed'] = 700;
	
	if ( !isset($options['pause'])) $options['pause'] = 3000;
	$options['pause'] = (int) $options['pause'];
	if ($options['pause'] < 1) $options['pause'] = 3000;
		
	if( !isset( $options['autorotate'])) $options['autorotate'] = 1;
    $autorotate = 	($options['autorotate'])? 'true' : 'false';
	//if ($cn > 1 and $coint_i > 1) 
	{
		echo '<script type="text/javascript">
	stepcarousel.setup({
		galleryid: \'board_carusel\',
		beltclass: \'belt\',
		panelclass: \'board_item\',
		autostep: {enable:' . $autorotate . ', moveby:1, pause:' . $options['pause'] . '},
		panelbehavior: {speed:' . $options['speed'] . ', wraparound:false, persist:false},
		defaultbuttons: {enable: false, moveby: 1, leftnav: [\'' . getinfo('stylesheet_url') . 'img/01.gif\', -5, 80], rightnav: [\'' . getinfo('stylesheet_url') . 'img/02.gif\', -20, 80]},
		statusvars: [\'statusA\', \'statusB\', \'statusC\'],
		contenttype: [\'inline\']
	})
	</script>';
	}

	return $args;
}

function post_carusel_body_start( $args = array() )
{
	$options_key = 'post_carusel';
	$options = mso_get_option($options_key, 'plugins', array());

	if( !isset( $options['showtop'])) $options['showtop'] = 0;
	if ($options['showtop']) {
		if ( function_exists('post_carusel_show') ) echo post_carusel_show();
	}
	return $args;
}

function post_carusel_add_form() {
	$all_meta = '<h3>' . t('Поля для карусели', 'admin') . '</h3>';
	$page_id = mso_segment(3);
	
    $CI = & get_instance();
	$CI->db->select('meta_value, meta_key');
	$CI->db->where( array ('meta_id_obj' => $page_id , 'meta_table' => 'page' ) );
	$query = $CI->db->get('meta');
	
	$page_all_meta = array();
	foreach ($query->result_array() as $row)
	{
		$page_all_meta[$row['meta_key']][] = $row['meta_value'];
	}	
	
	$value1 = '';
	$value2 = '';
	if ( isset($page_all_meta['caruselpict']) and !empty($page_all_meta['caruselpict']) )
	{
		$value1 = $page_all_meta['caruselpict'][0];
	}
	if ( isset($page_all_meta['caruseltext']) and !empty($page_all_meta['caruseltext']) )
	{
		$value2 = $page_all_meta['caruseltext'][0];
	}
	
	$description1 = 'Укажите картинку, которая будет отображаться в карусели для поста. Путь указывается относительно корня сайта, т.е. uploads/picture.jpg';
	$description2 = 'Укажите текст, который будет отображаться в карусели вместо текста поста';
	$options_key1 = 'caruselpict';
	$options_key2 = 'caruseltext';
	$name_f1 = 'f_options[' . $options_key1 . ']';
	$name_f2 = 'f_options[' . $options_key2 . ']';
	$f1 = '<input type="text" name="' . $name_f1 . '" value="' . $value1 . '">' . NR;
	$f2 = '<input type="text" name="' . $name_f2 . '" value="' . $value2 . '">' . NR;
	$f1 .= '<p>' .  $description1 . '</p>';
	$f2 .= '<p>' .  $description2 . '</p>';
	$key1 = 'Картинка для карусели';
	$key2 = 'Текст для карусели';
	$key1 = '<h3>' . $key1 . '</h3>';
	$key2 = '<h3>' . $key2 . '</h3>';
	$all_meta .= '<div>' . $key1 . NR . $f1 . '</div>';
	$all_meta .= '<div>' . $key2 . NR . $f2 . '</div>';	
	return $all_meta;
}
?>