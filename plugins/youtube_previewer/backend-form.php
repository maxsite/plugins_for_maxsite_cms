<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Youtube_Previewer» for MaxSite CMS
 *
 * Author: (c) Илья Земсков http://vizr.ru/
 */

$CI = & get_instance();

if( mso_segment(4) != '' && is_numeric(mso_segment(4)) )  # все контакты собранные формой
{
	$id = intval(mso_segment(4));

	if( $form = acceptor_get_form($id) )
	{

?><h1>Данные, собранные формой с UID «<a href="<?=getinfo('site_admin_url').basename(dirname(__FILE__)).'/form/'.$id;?>" target="_blank"><?= $form['form_uid']; ?></a>»</h1><?

		$CI->db->select('*');
		$CI->db->from('ac_values');
		$CI->db->where('form_id', $id);
		$CI->db->order_by('vtime', 'desc');

		$qry = $CI->db->get(); #_pr($CI->db->last_query());

		if( isset($qry) && is_object($qry) && $qry->num_rows() > 0 )
		{
?>
<!--div class="button">
<button type="submit" class="i csv" onClick="location.href='<?=getinfo('site_admin_url').basename(dirname(__FILE__)).'/export/'.$id;?>'">CSV</button>
</div-->
<p class="info"><?= 'На этой странице перечислены данные, собранные с помощью указанной формы.' ?></p>
<?
			$values = $qry->result_array();

			# для картинок вычисляем баовый адрес хранения
			$save_url = getinfo('uploads_url').( isset($form['form_prefs']['upload']) && $form['form_prefs']['upload'] ? $form['form_prefs']['upload'].'/' : '' );

			# Парсим список полей для вывода
			$form['form_prefs']['adminshow'] = array_map('trim', explode(',', trim($form['form_prefs']['adminshow'])));

			$CI->load->library('table'); # <colgroup style="width: 1%;"><colgroup style="width: 10%;"><colgroup style="width: 60%;"><colgroup style="width: 30%;">
			$tmpl = array (
				'table_open'	  => '<table class="page tablesorter"><colgroup style="width: 50px;">'.str_repeat('<colgroup>', count($form['form_prefs']['adminshow'])).'<colgroup style="width: 200px;"><colgroup style="width: 100px;">',
				'row_alt_start'	  => '<tr class="alt">',
				'cell_alt_start'  => '<td class="alt">',
			);

			$caption = array();
			$caption[] = '№';
			foreach( $form['form_prefs']['adminshow'] as $field )
			{
				$caption[] = $field;
			}
			$caption[] = 'Дата сохранения';
			$caption[] = 'Действия';

			$CI->table->set_template($tmpl);
			$CI->table->set_heading($caption);

			$i = 1;
			foreach( $values as $line => $value )
			{
				$value['vals'] = unserialize(str_replace('_serialize_', '', $value['vals']));

				$row = array();
				$row[] = $i;
				foreach( $form['form_prefs']['adminshow'] as $field )
				{
					if(
							(
								( isset($form['form_prefs']['fields'][$field][1]) && $form['form_prefs']['fields'][$field][1] == 'checkbox' ) ||
								( isset($form['form_prefs']['adminfields'][$field][1]) && $form['form_prefs']['adminfields'][$field][1] == 'checkbox' )
							)
						)
					{
						if( isset($value['vals'][$field]) && $value['vals'][$field] )
						{
							$row[] = '<input type="checkbox" checked disabled>';
						}
						else
						{
							$row[] = '<input type="checkbox" disabled>';
						}
					}
					elseif(
							(
								( isset($form['form_prefs']['fields'][$field][1]) && $form['form_prefs']['fields'][$field][1] == 'file' ) ||
								( isset($form['form_prefs']['adminfields'][$field][1]) && $form['form_prefs']['adminfields'][$field][1] == 'file' )
							)
						)
					{
						$row[] = isset($value['vals'][$field]) ? '<a href="'.$save_url.$value['vals'][$field].'" target=_blank class="lightbox">'.$value['vals'][$field].'</a>' : '';
					}
					else
					{
						$row[] = isset($value['vals'][$field]) ? $value['vals'][$field] : '';
					}

				}
				$row[] = $value['vtime'];
				$row[] =
					'<button type="submit" name="submit" class="button i-edit mar10-r" onClick="location.href=\''.getinfo('site_admin_url').basename(dirname(__FILE__)).'/values/edit/'.$value['vid'].'/'.$id.'\'"></button>'.
					'<button type="submit" name="submit" class="button i-trash" onClick="if( confirm(\'Вы действительно хотите удалить данные?\') ){ location.href=\''.getinfo('site_admin_url').basename(dirname(__FILE__)).'/values/delete/'.$value['vid'].'/'.$id.'\' }"></button>';

				$CI->table->add_row( $row	);
				$i++;
			}

			echo $CI->table->generate();
		}
		else
		{
			echo '<div class="error">Контактных данных для формы пока нет.</div>'.NR;
		}
	}
	else
	{
		echo '<div class="error">Форма с таким номером ('.$id.') не найдена!</div>'.NR;
	}
}
elseif( mso_segment(4) == 'edit' && mso_segment(5) != '' && is_numeric(mso_segment(5)) )  # редактирование данных
{
	$show = true; $redir = true;

	echo '<h1>Редактирование данных</h1>';
	echo '<p class="info">Задайте необходимые новые значения данных и сохраните результат.</p>';

	$vid = intval(mso_segment(5));
	$fid = intval(mso_segment(6));

	# получаем сами данные и информацию о форме
	if( $form = acceptor_get_form($fid) )
	{
		$data = acceptor_get_value($vid);

		if( !$data )
		{
			echo '<div class="error">Данные с ID №'.$pid.' в базе данных не обнаружены!</div>'.NR;
			$show = false;
		}
	}
	else
	{
		echo '<div class="error">Форма с ID №'.$fid.' в базе данных не обнаружена!</div>'.NR;
		$show = false;
	}

	# если POST запрос
	if( $show && $post = mso_check_post(array('session_id')) )
	{
		array_walk_recursive($_POST, 'mso_clean_str');
		$post = mso_clean_post( array_keys($_POST) );

		$save = $post['main'];
		$save['vals'] = $post['vals'];

		# Проверяем значения на уникальность
		if( isset($form['form_prefs']['uniq']) && $form['form_prefs']['uniq'] )
		{
			$lines = array_map('trim', explode(',', trim($form['form_prefs']['uniq'])));
			foreach( $lines as $k => $name )
			{
				if( isset($post['vals'][ $name ]) )
				{
					$CI->db->select('*');
					$CI->db->from('ac_values');
					$CI->db->where('vid !=', $vid);
					$CI->db->where('form_id', $fid);
					$CI->db->where('vtrash', '0');
					$CI->db->where('( vals REGEXP "\"'.addslashes($name).'\"\;s\:([0-9]+)\:\"'. addslashes($post['vals'][ $name ]) .'\"\;" )');

					$qry = $CI->db->get(); #_pr($CI->db->last_query());
					if( isset($qry) && is_object($qry) && $qry->num_rows() > 0 )
					{
						echo '<div class="error">Ошибка! Значение «'.$post['vals'][ $name ].'» уже используется!</div>'.NR;
						$save['vals'][ $name ] = $data['vals'][ $name ];
						$redir = false;
					}
				}
			}
		}

		# Проверяем поля с email
		if( isset($form['form_prefs']['email']) && $form['form_prefs']['email'] )
		{
			$lines = array_map('trim', explode(',', trim($form['form_prefs']['email'])));
			foreach( $lines as $k => $name )
			{
				if( isset($post['vals'][ $name ]) && $post['vals'][ $name ] && !mso_valid_email($post['vals'][ $name ]) )
				{
					echo '<div class="error">Ошибка! Емайл «'.$post['vals'][ $name ].'» не соответствует стандарту!</div>'.NR;
					$save['vals'][ $name ] = $data['vals'][ $name ];
					$redir = false;
				}
			}
		}

		if( $show )
		{
			#pr($data);
			#pr($save);
			$data = array_replace_recursive($data, $save);
		}

		# готовим массив знаений к сохраненияю
		$save['vals'] = '_serialize_'.serialize( $save['vals'] );

		$CI->db->where('vid', $vid);
		$CI->db->where('form_id', $fid);
		$res = ($CI->db->update('ac_values', $save)) ? '1' : '0';

		if( $res )
		{
			if( isset($_POST['redirect']) )
			{
				if( $redir )
				{
					mso_redirect('admin/'.basename(dirname(__FILE__)).'/values/'.$fid);
				}
			}
			else
			{
				echo '<div class="update">Данные успешно обновлены!</div>'.NR;
			}
		}
		else
		{
			echo '<div class="error">Данные не обновлены! Возможно произошла какая-то ошибка БД.</div>'.NR;
		}
	}

	if( $show )
	{
		$CI->load->library('table'); # будем формировать таблицу

		# вывод формы
		echo '<form action="' . mso_current_url(true) . '" method="post">' . mso_form_session('session_id').NR;

			$tmpl = array (
				'table_open'          => '<table class="page general"><colgroup style="width: 15%;">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
			);

			$CI->table->set_template($tmpl); # шаблон таблицы

			# заголовок формы с основными параметрами
			$CI->table->add_row(
				array(
					'class'=>'section',
					'colspan' => 2,
					'data' => '<div class="section"><h2>Сохранённые значения полей формы</h2></div>'
				)
			);

			foreach( $form['form_prefs']['fields'] as $key => $field )
			{
				# Добавляем поле
				$CI->table->add_row(
					$key,
					acceptor_field_html($field, $data['vals'], 'vals['.$key.']')
				);
			}

			echo $CI->table->generate(); # вывод подготовленной таблицы

			$CI->table->set_template($tmpl); # шаблон таблицы

			# заголовок формы со служебными параметрами
			$CI->table->add_row(
				array(
					'class'=>'section',
					'colspan' => 2,
					'data' => '<div class="section"><h2>Служебные параметры</h2></div>'
				)
			);

			foreach( $form['form_prefs']['adminfields'] as $key => $field )
			{
				# Добавляем поле
				$CI->table->add_row(
					$key,
					acceptor_field_html($field, $data['vals'], 'vals['.$key.']')
				);
			}

			# IP
			$CI->table->add_row(
				'IP адрес',
				'<input type="text" name="main[vip]" value="'.$data['vip'].'" placeholder="IP адрес">'
			);

			# Браузер
			$CI->table->add_row(
				'Браузер',
				'<input type="text" name="main[vbrowser]" value="'.$data['vbrowser'].'" placeholder="Браузер">'
			);

			# Реферер
			$CI->table->add_row(
				'Реферер',
				'<input type="text" name="main[vreferer]" value="'.$data['vreferer'].'" placeholder="Реферер">'
			);

			# Время регистрации
			$CI->table->add_row(
				'Время регистрации <p class="admin_page_qhint">YYYY-MM-DD HH:MM:SS</p>',
				'<input type="text" name="main[vtime]" value="'.$data['vtime'].'" placeholder="Время регистрации">'
			);

			# Признак удаления
			$CI->table->add_row(
				'Признак удаления',
				'<input type="hidden" name="main[vtrash]" value="0"><input type="checkbox" name="main[vtrash]" value="1"'.( isset($data['vtrash']) && $data['vtrash'] ? ' checked' : '' ).' placeholder="Признак удаления" title="Признак удаления">'
			);

			echo $CI->table->generate(); # вывод подготовленной таблицы

			echo '<button type="submit" name="submit" class="button i-save">' . t('Сохранить') . '</button> ';
			echo '<button type="submit" name="redirect" class="button i-save">' . t('Сохранить и вернуться к списку данных') . '</button>';

		echo '</form>';
	}
}
elseif( mso_segment(4) == 'delete' && mso_segment(5) != '' && is_numeric(mso_segment(5)) )  # удаление персоны
{
	$id = intval(mso_segment(5));

	# Удаление файла, если есть

	$CI->db->where('vid', $id);
	if( !$CI->db->delete('ac_values') )
	{
		echo '<h1>Удаление данных</h1>';
		echo '<div class="error">Данные с ID №'.$id.' в базе данных не обнаружены!</div>'.NR;
	}
	else
	{
		if( mso_segment(6) != '' && is_numeric(mso_segment(6)) )
		{
			mso_redirect('admin/'.basename(dirname(__FILE__)).'/values/'.mso_segment(6));
		}
		else
		{
			mso_redirect('admin/'.basename(dirname(__FILE__)));
		}
	}
}
else
{
	mso_redirect('admin/'.basename(dirname(__FILE__)));
}
