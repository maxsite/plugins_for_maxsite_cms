<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# cross_posting - замените на имя плагина


# функция автоподключения плагина
function cross_posting_autoload()
{
	//mso_create_allow('cross_posting_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('cross_posting', __FILE__));
	mso_hook_add('new_page','cross_posting_new');
	mso_hook_add('edit_page','cross_posting_edit');
	mso_hook_add('admin_init','cross_posting_init');
	
}

# функция выполняется при активации (вкл) плагина
function cross_posting_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function cross_posting_deactivate($args = array())
{	
	// mso_delete_option('plugin_cross_posting', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function cross_posting_uninstall($args = array())
{	
	// mso_delete_option('plugin_cross_posting', 'plugins'); // удалим созданные опции
	// mso_remove_allow('cross_posting_edit'); // удалим созданные разрешения
	return $args;
}

function cross_posting_init($arg = array())
{
 // обработка хука на админку
		mso_admin_menu_add('plugins','cross_posting','Кросс-постинг');
		mso_admin_url_hook('cross_posting','cross_posting_admin_page'); 
	
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function cross_posting_admin_page($args = array()){
	
	mso_hook_add_dinamic('mso_admin_header',' return $args . "' . t('cross_posting', __FILE__) . '"; ' );
	mso_hook_add_dinamic('admin_title',' return "' . t('cross_posting', __FILE__) . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'cross_posting/admin.php');
}	
	
# функции плагина
function cross_posting_new($arg = array())
{
	// обработка хука на new_page
	$page_status = isset( $arg[2] ) ? $arg[2] : ''; // publish
	$page_id = isset( $arg[0] ) ? $arg[0] : -1;
	
	if ( $page_id >= 0 && $page_status == 'publish') 
	{
		$CI = & get_instance();
		$CI->db->select('page_title, page_content, page_slug, page_comment_allow');
		$CI->db->from('page');
		$CI->db->where('page_id', $page_id );
		$query = $CI->db->get();
		if ($query and $query->num_rows() > 0)
		{
			$pages = $query->result_array();
			$page_title = $pages[0]['page_title'];
			$page_content = $pages[0]['page_content'];
			$page_slug = $pages[0]['page_slug'];
			$page_comment_allow = $pages[0]['page_comment_allow'];
			//$page_content = mso_html_to_text( $page_content ); 

			// смотрим метки
			require_once( getinfo('common_dir') . 'meta.php' );
			$tags = mso_get_meta('tags', 'page', $page_id);
			$page_tags = array();
			foreach( $tags as $tag ) {
					$page_tags[] = $tag['meta_value'];
			}
			
			// публикуем
			$options_key = 'cross_posting';
			$options = mso_get_option($options_key, 'plugins', array());

			if ( !isset($options['more_text']) ) $options['more_text'] = 'Ссылка на оригинал'; 
			if ( !isset($options['cat_tag']) ) $options['cat_tag'] = 0; 

			if ( !isset($options['yaru_post']) ) $options['yaru_post'] = 0; 
			if ( !isset($options['yaru_username']) ) $options['yaru_username'] = ''; 
			
			if ( !isset($options['yaru_password']) ) $options['yaru_password'] = ''; 
			if ( !isset($options['yaru_post_comments']) ) $options['yaru_post_comments'] = 1; 
			if ( !isset($options['yaru_words']) ) $options['yaru_words'] = 0; 
			
			$comments_allow = $options['yaru_post_comments'] && $page_comment_allow;
			$username = $options['yaru_username'];
			$password = $options['yaru_password'];
			$words = $options['yaru_words'];
			$more = $options['more_text'];
			$cat_tag = $options['cat_tag'];
			
			if ( $cat_tag ) {
				// рубрики в качестве меток
				/*
				SELECT p.page_title, cat.category_name FROM `mso_page` p
				left join `mso_cat2obj` co on p.page_id = co.page_id
				left join `mso_category`  cat on cat.category_id = co.category_id
				where p.page_id = 146 
				*/
				$CI->db->select('category_name');
				$CI->db->from('page p');
				$CI->db->join('cat2obj co', 'p.page_id = co.page_id', 'left' );
				$CI->db->join('category cat', 'cat.category_id = co.category_id', 'left' );
				$CI->db->where( 'p.page_id', $page_id );
				$query = $CI->db->get();
				if ($query and $query->num_rows() > 0)
				{
					$res_cats = $query->result_array();
					foreach ( $res_cats as $res_cat ) {
						$page_tags[] = $res_cat['category_name'];
					}
				}
			}
			
			include( 'yaru.php' );
			
			
			// обрежем пост
			if ( $words > 0 ) {
				require_once( getinfo('common_dir') . 'common.php' );
				$page_content = str_replace( '[cut]', ' ', $page_content );
				$page_content = str_replace( '[xcut]', ' ', $page_content );
				
				$page_content = mso_str_word( $page_content, $words ) . ' ...';
			} 
			
			// ищем кут
			$cut_pos = strpos($page_content,  '[cut]' );
			$xcut_pos = strpos($page_content,  '[xcut]' );
			
			if ( $cut_pos > 0 || $xcut_pos > 0 ) {
				// есть разделитель
				if ( $cut_pos !== FALSE )
					$tmp =  mb_substr( $page_content, 0, $cut_pos - 1 );
				else	
					$tmp =  mb_substr( $page_content, 0, $xcut_pos - 1 );
				$page_content = $tmp;//.  mb_substr($page_content, $cut_pos + 5, mb_strlen($page_content) - $cut_pos + 5 ) . '</cut>';
			}			
			// формируем ссылку на оригинал
			$page_url = '<a href="' . getinfo('site_url') . 'page/' . $page_slug . '">'.$more.'</a>';
			$page_content .= '<br><br>' . $page_url;
			
			$YaRu = new YaRu( $username, $password );
			$postN = $YaRu->makePost( $page_id, $page_title, $page_content, $page_tags, $comments_allow );	
			if ( $postN !== FALSE )
			{
				// запишем в мету номер поста - yaru_post = 
				require_once( getinfo('common_dir') . 'meta.php' );
				mso_add_meta( 'yaru_post_id', $page_id, 'page', $postN);
			}
		}	
	}
}

function cross_posting_edit($arg = array())
{
	// для я.ру не реализован PUT и DELETE ?
		/*
	// обработка хука на edit_page
	$page_status = isset( $arg[2] ) ? $arg[2] : ''; // publish
	$page_id = isset( $arg[0] ) ? $arg[0] : -1;
	
	if ( $page_id >= 0 && $page_status == 'publish') 
	{
		$CI = & get_instance();
		$CI->db->select('page_title, page_content');
		$CI->db->from('page');
		$CI->db->where('page_id', $page_id );
		$query = $CI->db->get();
		if ($query and $query->num_rows() > 0)
		{
			$pages = $query->result_array();
			$page_title = $pages[0]['page_title'];
			$page_content = $pages[0]['page_content'];
			//$page_content = mso_html_to_text( $page_content ); 

			// публикуем
			$options_key = 'cross_posting';
			$options = mso_get_option($options_key, 'plugins', array());
			if ( !isset($options['yaru_post']) ) $options['yaru_post'] = 0; 
			if ( !isset($options['yaru_username']) ) $options['yaru_username'] = ''; 
			if ( !isset($options['yaru_password']) ) $options['yaru_password'] = ''; 
			if ( !isset($options['yaru_post_comments']) ) $options['yaru_post_comments'] = 1; 
			
			$comments_allow = $options['yaru_post_comments'];
			$username = $options['yaru_username'];
			$password = $options['yaru_password'];
			
			// get postN
			require_once( getinfo('common_dir') . 'meta.php' );
			$postN = mso_get_meta('yaru_post_id', 'page', $page_id);
			$postN = $postN[0]['meta_value'];
			if ( isset( $postN ) && !empty( $postN ) and ( $postN >= 0 ) )
			{
				include( 'yaru.php' );
				$YaRu = new YaRu( $username, $password );
				$YaRu->editPost( $page_id, $page_title, $page_content, $postN, $comments_allow );	
			}	
		}	
	}
	*/	
}

?>
