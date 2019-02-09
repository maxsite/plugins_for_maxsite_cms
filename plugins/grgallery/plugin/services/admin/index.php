<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	mso_cur_dir_lang('admin');
	
	if ( !mso_check_allow('grgallery_edit') ) 
	{
	 echo t('Доступ запрещен', 'plugins/grgallery');
	 return;
	}

	global $MSO;
	$CI = & get_instance();
	$CI->load->helper('form');	// подгружаем хелпер форм
	$CI->load->helper('file'); // хелпер для работы с файлами
	require_once( getinfo('common_dir') . 'meta.php' ); 
	require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	
	$sluggroup = mso_segment('4');	
	$out='';

	$tag_pages = get_pages_tag(); // формируем массив тегов и страниц
	$group_tags = get_group_tag(); // формируем список групп тегов
	
	$arr_child_tags = false;
	if ($sluggroup != '') $arr_child_tags = $group_tags[$sluggroup];

	// удаление отмеченных групп
	if ($post = mso_check_post(array('f_session_id', 'delgroups')))
		{
			mso_checkreferer();
			if (isset($post['del_group']))
			{
				$data = array ('meta_key' => 'group_tag');		
				$CI->db->where($data);
				$CI->db->delete('meta'); //--удаляем все группы	они есть в массиве $group_tags				
				foreach ($post['del_group'] as $key => $val)
					{
						$data = array ('meta_key' => 'tags', 'meta_value' => $val, 'meta_table' => 'page');
						$CI->db->where($data);
						$CI->db->delete('meta');
						unset ($group_tags[$val]);
					}
				$group_tags_ser = serialize($group_tags);
				$data = array ('meta_key' => 'group_tag', 'meta_table' => 'tags', 'meta_value' => $group_tags_ser);
				$CI->db->insert('meta', $data);			
			}
		}
	
	// Добавление группового тега и включение в него обычных тегов
	if ($post = mso_check_post(array('f_session_id', 'new_group')))
		{
			mso_checkreferer();
			
			$data = array ('meta_key' => 'group_tag');		
			$CI->db->where($data);
			$CI->db->delete('meta'); //--удаляем все группы	они есть в массиве $group_tags
						
			if ($post['new_group_tag'] != '')	
				{				
					$group_tags[$post['new_group_tag']] = '';
				}
						
			if ($post['sluggroup'] != '')
				{

				$sluggroup = $post['sluggroup'];

				
					$data = array ('meta_key' => 'tags', 'meta_table' => 'page', 'meta_value' => $sluggroup);		
					$CI->db->where($data);
					$CI->db->delete('meta'); //--удаляем эту групповую услугу из всех страниц
					
					$group_tags[$sluggroup] = '';				
					if (isset($post['deltag']))
						{	
							$arrtags = array ();
							$signarr = array ();
	
							foreach ($post['deltag'] as $key => $var)
								{
								/* тут надо удалить этот тег из всех групп 
								что бы один тег мог участвовать только в одной группе*/
								foreach ($group_tags as $nmgroup => $arrgroup)
									{
										if ( gettype($arrgroup) == "array") //если из текущей уже удалили, а в других и не было
											{
												foreach ($arrgroup as $ktag => $vtag)
													{
														if ($vtag == $var) unset($group_tags[$nmgroup][$ktag]);
													}
											}
									}
								// вот до сюда удаляем
								$arrtags [$key] = $var; // Добавляем в список участников этой группы
								foreach ($tag_pages[$var] as $key => $idpage) //--по всем тегам страницам пробегаемся
									{
									if (!isset($signarr[$idpage]))
										{	// добавляем групповой тег всем страницам тегов его группы
											$data = array ('meta_key' => 'tags', 'meta_table' => 'page', 'meta_id_obj' => $idpage, 'meta_value' => $sluggroup);
											$CI->db->insert('meta', $data);
											$signarr[$idpage] = $sluggroup; //сигнальный массив если уже записали для этой стран
										}
									}								
								}
							//$group_tags[$post['sluggroup']] = serialize($arrtags); 
							$group_tags[$sluggroup] = $arrtags; 
						}
				}
				
			// тут будет добавление в массив конкретной метка!!!!
				
			$group_tags_ser = serialize($group_tags);
			$data = array ('meta_key' => 'group_tag', 'meta_table' => 'tags', 'meta_value' => $group_tags_ser);
			$CI->db->insert('meta', $data);
			mso_flush_cache();
			// тут редирект, что бы вернуться на редактируемую группу услуг
			if ($post['sluggroup'] != '') {$urlredir = 'admin/grgallery/services/'.$sluggroup.'/'; mso_redirect ($urlredir);}
			 			
		}	
		
	// удаление всех тегов
	if ($post = mso_check_post(array('f_session_id', 'delalltags')))
		{
			mso_checkreferer();
			$data = array ('meta_key' => 'tags', 'meta_table' => 'page');
			$CI->db->where($data);
			$CI->db->delete('meta');		
		}
		
	// удаление отмеченных тегов
	if ($post = mso_check_post(array('f_session_id', 'delanytag')))
		{
			mso_checkreferer();
			if (isset($post['deltag']))
			{
			$dltg = $post['deltag'];
			foreach ($dltg as $key => $val)
				{
				$deltag[$val] = $val;
				}

			foreach ($group_tags as $group_name => $arr_tag)
				{
				foreach ($arr_tag as $key => $tag)
					{
					if (isset ($deltag[$tag])) unset ($group_tags[$group_name][$key]); //удалили из списка группы
					}
				}
				
			$data = array ('meta_key' => 'group_tag');		
			$CI->db->where($data);
			$CI->db->delete('meta'); //--удаляем все группы	они есть в массиве $group_tags
				
			$group_tags_ser = serialize($group_tags); // запишем обновленный список тегов по группам
			$data = array ('meta_key' => 'group_tag', 'meta_table' => 'tags', 'meta_value' => $group_tags_ser);
			$CI->db->insert('meta', $data);

			foreach ($deltag as $key => $val)
				{
				$data = array ('meta_key' => 'tags', 'meta_value' => $val, 'meta_table' => 'page');
				$CI->db->where($data);
				$CI->db->delete('meta');			
				}
			}
		}
	
// вывод списка тегов
	mso_flush_cache();
	require_once($MSO->config['plugins_dir'] . 'grgallery/plugin/services/admin/list.php');
	echo $out;
	
	//if (mso_segment(3) != 'pages') $pagination['next_url'] = 'pages/next';
	//mso_hook('pagination', $pagination);
?>