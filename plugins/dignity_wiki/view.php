<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

// получаем доступ к CI
$CI = & get_instance();

wiki_menu();

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id)
{
	// загружаем данные из базы
	$CI->db->from('dignity_wiki');
	$CI->db->where('dignity_wiki_id', $id);
    if (!is_login())
    {
        $CI->db->where('dignity_wiki_approved', true);
    }
	$CI->db->join('dignity_wiki_category', 'dignity_wiki_category.dignity_wiki_category_id = dignity_wiki.dignity_wiki_category', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = dignity_wiki.dignity_wiki_comuser_id', 'left');
	if (!is_login())
	{
		$CI->db->where('dignity_wiki_approved', true);
	}
	$query = $CI->db->get();

	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$allpages = $query->result_array();
		
		// обьявляем переменую	
		$out = '';
		
		// возвращаем туда, откуда пришел
		$url = getinfo('site_url') . $options['slug'];
		
		// цикл
		foreach ($allpages as $onepage) 
		{
			
			// подсчёт количества просмотров через cookie
			global $_COOKIE;
			$name_cookies = 'dignity-soft';
			$expire = 2592000;
			$slug = getinfo('siteurl') . $options['slug'] . '/' . mso_segment(2) . '/' . mso_segment(3);
			$all_slug = array();
			
			if (isset($_COOKIE[$name_cookies]))
			{
				$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			}
			
			if (in_array($slug, $all_slug))
			{
				false; // уже есть текущий урл - не увеличиваем счетчик
			}
			else
			{
				// нужно увеличить счетчик
				$all_slug[] = $slug; // добавляем текущий slug
				$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
				$all_slug = implode('|', $all_slug); // соединяем обратно в строку
				$expire = time() + $expire;
				
				@setcookie($name_cookies, $all_slug, $expire);
				
				$page_view_count = $onepage['dignity_wiki_views'] + 1;
				
				$CI->db->where('dignity_wiki_id', $id);
				$CI->db->update('dignity_wiki', array('dignity_wiki_views'=>$page_view_count));
			}
			
		$out .= '<div class="page_only">';
        
        $status = '';
        if ($onepage['dignity_wiki_approved'])
        {
            $status .= '';
        }
        else
        {
            $status .= ' (' . t('Черновик', __FILE__) . ') ';
        }
		
		$out .= '<div class="info info-top">';
		$out .= '<h1><a href="' . $url . '">' . $onepage['dignity_wiki_title'] . '</a>' . $status . '</h1>';
		$out .= '</div>';
		
		// если вошел автор
		if ($onepage['dignity_wiki_comuser_id'] == getinfo('comusers_id')){
			// выводим ссылку «редактировать»
			$out .= '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_wiki_id'] . '">' . t('Редактировать', __FILE__) . '</a></p>';
		}
		
		$out .= '<p>' . wiki_cleantext($onepage['dignity_wiki_cuttext']) . '</p>';
		$out .= '<p>' . wiki_cleantext($onepage['dignity_wiki_text']) . '</p>';
		
		$out .= '<div class="info info-bottom">';
		$out .= $onepage['comusers_nik'] . ', ';
			$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_wiki_datecreate']) . ' | ';
			$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_wiki_category_id'] . '">' . $onepage['dignity_wiki_category_name'] . '</a> | ';
		
		$out .= t('Просмотров: ', __FILE__) . $onepage['dignity_wiki_views'];
		
		$out .= '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>';
		$out .= '<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div>';

		$out .= '</div>';
		$out .= '<div class="break"></div>';
		$out .= '</div><!--div class="page_only"-->';
		
		}
		// выводим всё
		echo $out;
		
		// meta-тэги
		mso_head_meta('title', $onepage['dignity_wiki_title']);
		mso_head_meta('description', $onepage['dignity_wiki_keywords']);
		mso_head_meta('keywords', $onepage['dignity_wiki_description']);
	}
	else
		// если запись не найдена
		echo '<p>' . t('Статья не найдена.', __FILE__) . '</p>';
}
else{
	// если запись не найдена
	echo '<p>' . t('Статья не найдена.', __FILE__) . '</p>';
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла