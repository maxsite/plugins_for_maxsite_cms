<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
mso_cur_dir_lang('admin');

require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные

function grgallpicts($arr = array())
	{
		
		global $MSO;
		global $grgll;
		global $page;
		
		$cache_add_key = serialize($arr).serialize($page);
		
		if ( !isset($arr['cache']) ) $arr['cache'] = TRUE;	// по умолчанию кешируется, если явно указываем, то берем из кэша
		$cache_key = $grgll['main_key_options'].'_page_picts' . $cache_add_key;
		if ($arr['cache'] != FALSE)
			{	
				$k = mso_get_cache($cache_key);
				if ($k) return $k; // да есть в кэше
			}
	

	
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
	
		$id = $page['page_id'];
		$out_all = '';
		$pict_dir = getinfo('uploads_dir').$grgll['uploads_pict_dir'].'/'.$grgll['prefix'].$id;
		$pict_url = getinfo('uploads_url').$grgll['uploads_pict_dir'].'/'.$grgll['prefix'].$id.'/';
	
		$dirs = directory_map($pict_dir, true); // только в текущем каталоге
		if (!$dirs) $dirs = array();
		
		// описания файлов хранятся в виде серилизованного массива в uploads/../_mso_i/_mso_descritions.dat
		$fn_mso_descritions = $pict_dir . '/_mso_i/_mso_descriptions.dat';

		if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
			write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив

		if (file_exists( $fn_mso_descritions )) // файл есть
			{
				$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
			}
		else $mso_descritions = array();
	
		foreach ($dirs as $datefile=>$file)
			{
				if (@is_dir($pict_dir.'/'.$file)) continue; // это каталог
		//$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		//if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

				$cod = '<p>';
				$title = '';
				$title_f = '';
				if (isset($mso_descritions[$file]))
					{
						$title = $mso_descritions[$file];
						if ($title) $title_f = '<em>' . htmlspecialchars($title) . '</em>';
					}
				$title_alt = str_replace('"', '&amp;quot;', $title);
				$title_alt = str_replace('<', '&amp;lt;', $title_alt);
				$title_alt = str_replace('>', '&amp;gt;', $title_alt);
				$title_alt = str_replace('\'', '&amp;#039;', $title_alt);
				$out_all .= '<p><a class="lightbox" href="' . $pict_url . $file . '" target="_blank" title="' . htmlspecialchars($title).'"><img alt="" src="' . $pict_url .'/mini/'. $file . '"></a></p>'; // 
			}
		$out_all = '<div class="page-block-pict">'.$out_all.'</div>';
		mso_add_cache($cache_key, $out_all); // Добавили в кэш
		return $out_all;
	}

function grgalltags($arr = array())
	{
	global $grgll;
	global $page;
	global $MSO;
	
	$cache_add_key = serialize($arr).serialize($page);
	if ( !isset($arr['cache']) ) $arr['cache'] = TRUE;	// по умолчанию кешируется, если явно указываем, то берем из кэша
	$cache_key = $grgll['main_key_options'].'_page_tags' . $cache_add_key;
	if ($arr['cache'] != FALSE)
		{	
			$k = mso_get_cache($cache_key);
			if ($k) return $k; // да есть в кэше
		}
			
	
	require_once( getinfo('common_dir') . 'meta.php' );
	require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку	
	
	$out = '';
	$price_tags = mso_get_meta ('price', 'page', $page['page_id']);
	$grouptags = get_group_tag(array('cache' => true));
	$tag_pages = get_pages_tag(); // формируем массив тегов и страниц
	
	$price_tags_arr = @unserialize($price_tags[0]['meta_value']); //активные услуги и тарифы
	$all_tags_arr = mso_get_all_tags_page(); //все услуги
	
	$grgll_options = mso_get_option($grgll['main_key_options'], 'plugins', array()); // получение опций
	$view_all_tags = $grgll_options['view_all_tags'];
	
	if ($grgll_options['view_groups_page'] == 1) // если выводить группами
	{
		$outgr = '';
		foreach ($grouptags as $group => $arrtags)
		{
			$strstartgr = '<div class="group-tags">';
			$strendgr = '</div>';
		
			$outstk = '';
			$headgroup = '';
			$grstart = '';
			$grend = '';
			if ($grgll_options['view_all_tags'] == 1) 
						{
						$grstart = $strstartgr;
						$headgroup = '<div class="head-group"><h2>'.$group.'</h2></div>';
						$grend = $strendgr;
						}
			if (isset ($tag_pages[$group][$page['page_id']]))
						{
						$grstart = $strstartgr;
						$grend = $strendgr;
						$headgroup = '<div class="head-group"><h2>'.$group.'</h2></div>';
						}
			$outstk .= $headgroup;
			$ftagsgroup = false; // инициализируем флаг наличия услуг в группе
			if (gettype($arrtags) == "array")
				{
				foreach ($arrtags as $key => $val)
					{	
						$ftagsgroup = true;						
						$yes = '<div class="one-tag"><div class="no-tag">X</div></div><br>';
						$out_ntag = '<div class="name-tag">'.$key.'</div>';
						$nametag = $out_ntag;
						$v = '<div class="yes-tag">V</div>';
						if ($grgll_options['view_all_tags'] != 1) {$yes = ''; $nametag = ''; $v = '';};
						if (isset ($price_tags_arr[$key])) 
							{
								$yes = '<div class="one-tag">'.$v.$price_tags_arr[$key].'</div><br>';
								$nametag = $out_ntag;
								
							}
						$outstk .= $nametag.$yes;					
					}
				}
			if ($ftagsgroup == true) $outgr .= $grstart.$outstk.$grend;
		} // закрываем вывод одной группы
		$out .= $outgr;
	}
	else
	{	
		foreach ($all_tags_arr as $ntag => $nah)
		{
		if (!isset($grouptags[$ntag]))
			{
			$yes = '<div class="one-tag"><div class="no-tag">X</div></div><br>';
			$out_ntag = '<div class="name-tag">'.$ntag.'</div>';
			$nametag = $out_ntag;
			$v = '<div class="yes-tag">V</div>';
			if ($grgll_options['view_all_tags'] != 1) {$yes = ''; $nametag = ''; $v = '';};
			if (isset ($price_tags_arr[$ntag])) 
				{$yes = '<div class="one-tag">'.$v.$price_tags_arr[$ntag].'</div><br>';
				$nametag = $out_ntag;}
		
			$out .= $nametag.$yes;
			}
		}

	}
	if (mso_segment(2) == 'oferta') $out = ''; // это для обнуления на такой странице
	$out = '<div class="page-block-tags">'.$out.'</div>';
	mso_add_cache($cache_key, $out); // Добавили в кэш	
	return $out;
	}
?>