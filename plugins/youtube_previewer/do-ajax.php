<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Youtube_Previewer» for MaxSite CMS
 *
 * Author: (c) Илья Земсков http://vizr.ru/
 */

# Проверка прав доступа
if( !mso_check_allow(basename(dirname(__FILE__)).'_edit') )
{
	echo false;
	die();
}

$CI = & get_instance();
mso_checkreferer();

# обрабатываем запрос
if( $post = mso_check_post(array('do')))
{
	if( $post['do'] == 'search' )
	{
		$out = '';
		$res = false;
		$tmpl = '<div class="page"><p>[ <a href="/admin/page_edit/[PID]" title="Редактировать" target=_blank>[PID]</a> ] | <a href="/page/[SLUG]">[TITLE]</a></p><p class="url">[URL]</p><p class="url">[PREVIEW]</p><p>[IMG]</p></div>'.NR;
		$img = '';
		$url = '';

		$CI->db->select('mp.page_id, mp.page_title, mp.page_slug, mp.page_content, mm.meta_value img');
		$CI->db->from('mso_page mp');
		$CI->db->join('mso_meta mm', 'mm.meta_id_obj = mp.page_id AND mm.meta_key = "image_for_page"', 'left');
		$CI->db->where('mm.meta_value', '');
		$CI->db->like('mp.page_content', 'youtube.com/embed');
		$CI->db->order_by('mp.page_id', 'asc');
		$CI->db->limit(1);

		$qry = $CI->db->get(); #pr($CI->db->last_query());

		if( isset($qry) && is_object($qry) && $qry->num_rows() > 0 )
		{
			$res = $qry->result_array(); #pr($res);
			$pg = $res[0];
			$path = getinfo('uploads_dir').'_pages/'.$pg['page_id'].'/';

			preg_match_all('/<iframe(.*?)><\/iframe>/msi', $pg['page_content'], $ma);
			
			if( isset($ma[1]) and count($ma[1]) > 0 ) # в записи найден html код вставки ролика
			{
				foreach( $ma[1] as $code )
				{
					preg_match('/youtube.com\/embed\/(.*?)\"/msi', $code, $rid);
				
					if( isset($rid[1]) and $rid[1] )
					{
						require_once( getinfo('common_dir') . 'uploads.php' ); # функции загрузки 
						require_once( getinfo('common_dir') . 'meta.php' ); # функции работы мета

						# формируем - https://img.youtube.com/vi/<insert-youtube-video-id-here>/0.jpg
						$url = 'https://img.youtube.com/vi/'.$rid[1].'/0.jpg';
						$pict = strtolower($rid[1]).'.jpg'; # файл картинки на диске
						$preview = getinfo('uploads_url').'_pages/'.$pg['page_id'].'/'.$pict; # адрес превьюшки в папке uploads
						$img = '<img src="'.$preview.'" width=200>';

						# скачивание картинку
						file_put_contents($path.$pict, file_get_contents($url));

						$up_data = array();
						$up_data['full_path'] = $path.$pict;
						$up_data['file_path'] = $path;
						$up_data['file_name'] = $rid[1].'.jpg';
				
						$r = array();
						$r['userfile_mini'] = 1; // делать миниатюру
						$r['userfile_mini_size'] = (int) mso_get_option('size_image_mini', 'general', 150);
						$r['mini_type'] = mso_get_option('image_mini_type', 'general', 1);
						$r['prev_size'] = 100;
				
						mso_upload_mini($up_data, $r); // миниатюра 
						mso_upload_prev($up_data, $r); // превьюшка

						# сохраняем мета-поле записи
						$meta = mso_add_meta( 'image_for_page', $pg['page_id'], 'page', $preview );
							
						break; # выходим из цикла, т.к. нашли картинку
					}	
				}
			}
			else #кода нет - значит было просто упоминание адреса
			{
				$img = '<p>Не удалось вычислить адрес превью-картинки. Возможно в записи непраивльный код вставки youtube-ролика.</p>'.NR;
			}
			
			$out = $tmpl;

			$out = str_replace('[PID]', $pg['page_id'], $out);
			$out = str_replace('[SLUG]', $pg['page_slug'], $out);
			$out = str_replace('[TITLE]', $pg['page_title'], $out);
			$out = str_replace('[PREVIEW]', $preview, $out);
			$out = str_replace('[IMG]', $img, $out);
			$out = str_replace('[URL]', $url, $out);

			$res = $meta;
		}
		else
		{
			$out = '<div class="update">Поиск закончен</div>';
		}

		die(json_encode(array(
			'html' => $out,
			'res' => $res,
		)));
	}
}
