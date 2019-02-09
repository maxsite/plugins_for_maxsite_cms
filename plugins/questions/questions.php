<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

mso_head_meta('title', t('Вопросы', __FILE__) ); // meta title страницы

// стили свои подключим
mso_hook_add('head', 'questions_css');

function questions_css($a = array())
{
	if (file_exists(getinfo('template_dir') . 'questions.css')) $css = getinfo('stylesheet_url') . 'questions.css';
		else $css = getinfo('plugins_url') . 'questions/questions.css';
		
	echo '<link rel="stylesheet" href="' . $css . '" type="text/css" media="screen">' . NR;
	
	return $a;
}

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

$CI = & get_instance();

$options = mso_get_option('plugin_questions', 'plugins', array());

if ( !isset($options['fields_arr']) ) 
	$options['fields_arr'] = array('name' => t('Ваше имя:', __FILE__), 'text' => t('Ваш вопрос:', __FILE__)); 

if ( isset($options['text']) ) echo $options['text']; // из опций смотрим текст перед всем
if ( !isset($options['limit']) ) $options['limit'] = 10; // вопросов на страницу
if ( !isset($options['email']) ) $options['email'] = false; // отправка на email
if ( !isset($options['moderation']) ) $options['moderation'] = 1; // модерация
// формат вывода
if ( !isset($options['format']) ) $options['format'] = '<tr><td colspan="2" class="header"><a name="questions-[id]"></a>[name]</td></tr>
<tr><td class="t1"><b>Возраст:</b></td><td class="t2">[age]</td></tr>
<tr><td class="t1"><b>Город:</b></td><td class="t2">[city]</td></tr>
<tr><td class="t1"><b>Вопрос:</b></td><td class="t2">[text]</td></tr>
<tr><td class="t1"><b>Ответ:</b></td><td class="t2">[answer]</td></tr>
<tr><td colspan="2" class="space">&nbsp;</td></tr>'; 

// текст до цикла
if ( !isset($options['start']) ) $options['start'] = '<h2 class="questions">Вопросы</h2><table class="questions">';
 
// текст после цикла
if ( !isset($options['end']) ) $options['end'] = '</table>'; 


$session = getinfo('session'); // текущая сессия 

// тут приём post
if ( $post = mso_check_post(array('f_session_id', 'f_submit_questions', 'f_fields_questions', 'f_questions_captha')) )
{
	mso_checkreferer();
	
	$captcha = $post['f_questions_captha']; // это введенное значение капчи
	// которое должно быть вычисляем как и в img.php
	$char = md5($session['session_id'] . mso_slug(mso_current_url()));
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
	$char = substr( $char, 1, 4);
	if ($captcha != $char)
	{ // не равны
		echo '<div class="error">' . t('Привет роботам!', __FILE__) . '</div>';
		mso_flush_cache();
	}
	else
	{ // прошла капча, можно добавлять вопросы
		
		// pr($post);
		
		// данные для новой записи
		$ins_data = array (
			'questions_date' => date('Y-m-d H:i:s'),
			'questions_ip' => $session['ip_address'],
			);
		
		if ( $options['moderation'] ) $ins_data['questions_approved'] = 0; // нужна модерация
		else $ins_data['questions_approved'] = 1; // сразу одобряем 
		
		// отправленные поля
		// сразу готовим для email
		$text_email = ''; 
		foreach( $options['fields_arr'] as $key => $val )
		{
			if ( isset($post['f_fields_questions'][$key]) ) 
			{
				$ins_data['questions_' . $key] = $post['f_fields_questions'][$key];
				$text_email .= $key . ': ' . $post['f_fields_questions'][$key] . "\n";
			}
		}
		

		// pr($ins_data);
		
		$res = ($CI->db->insert('questions', $ins_data)) ? '1' : '0';
		
		if ($res)
		{
			echo '<div class="update">' . t('Ваш вопрос добавлен!', __FILE__);
			if ( $options['moderation'] ) echo ' ' . t('Он будет опубликован после ответа.', __FILE__);
			echo '</div>';
			
			$text_email = t("Новая запись в вопросах") . ": \n" . $text_email;
			$text_email .= "\n" . t("Редактировать") . ": " . getinfo('siteurl') . 'admin/questions/editone/' 
						. $CI->db->insert_id() . "\n";
			
			if ( $options['email'] and mso_valid_email($options['email']) ) 
			{
				mso_mail($options['email'], t('Новая запись в вопросах', __FILE__), $text_email);
			}
			
		}
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
		mso_flush_cache();
		
		// тут бы редирект, но мы просто убиваем сессию
		$CI->session->sess_destroy();
	}
}
else
{
	// тут форма, если не было post
	echo '<div class="questions_form"><form action="" method="post">' . mso_form_session('f_session_id');
	
	echo '<table style="width: 100%;">';
	
	foreach( $options['fields_arr'] as $key => $val )
	{
		echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t($val, __FILE__) . '</strong> </td><td class="td2">';
		
		if ($key != 'text')
		{
			echo '<input name="f_fields_questions[' . $key . ']" type="text" style="width: 99%;"></td></tr>';
		}
		else
		{ 
			echo '<textarea name="f_fields_questions[' . $key . ']" style="width: 99%; height: 100px;"></textarea></td></tr>';
		}
	}

	// капча из плагина капчи
	
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Введите нижние символы', 'plugins') . ' </td>
			<td style="text-align: left;" class="td2"><input type="text" name="f_questions_captha" value="" maxlength="4"> <img src="' 
			. getinfo('plugins_url') . 'captcha/img.php?image='
			. $session['session_id']
			. '&page='
			. mso_slug(mso_current_url())
			. '&code='
			. time()
			. '" title="' . t('Защита от спама: введите только нижние символы', 'plugins') . '" align="absmiddle"></td></tr>';

	
	echo '<tr><td class="td1">&nbsp;</td><td style="vertical-align: top; text-align: left;" class="td2"><input type="submit" class="submit" name="f_submit_questions" value="' . t('Отправить', __FILE__) . '"></td></tr>';
	
	echo '</table></form></div>';
}


// тут последние вопросы с пагинацией
// нам нужна все поля таблицы
// вначале определим общее количество записей
$pag = array(); // пагинация
$pag['limit'] = $options['limit']; // записей на страницу
$pag['type'] = ''; // тип

$CI->db->select('questions_id');
$CI->db->from('questions');
$CI->db->where('questions_approved', '1');
$query = $CI->db->get();
$pag_row = $query->num_rows();

if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']); // всего станиц пагинации

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}

// теперь получаем сами записи
$CI->db->from('questions');
$CI->db->where('questions_approved', '1');
$CI->db->order_by('questions_date', 'desc');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
	else $CI->db->limit($pag['limit']);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	$out = '';
	foreach ($books as $book) 
	{
		// pr($book);
		$out .= '<a name="questions-' . $book['questions_id'] . '"></a>';
		$out .= str_replace( 
			array(
				'[id]', 
				'[ip]',
				'[date]', 
				'[name]', 
				'[text]', 
				'[email]', 
				'[age]', 
				'[city]', 
				'[answer]'), 
			array(
				$book['questions_id'],
				$book['questions_ip'],
				mso_date_convert('d-m-Y H:i:s', $book['questions_date']),
				htmlspecialchars($book['questions_name']),
				str_replace("\n", "<br>", htmlspecialchars($book['questions_text'])),
				htmlspecialchars($book['questions_email']),
				htmlspecialchars($book['questions_age']),
				htmlspecialchars($book['questions_city']),
				htmlspecialchars($book['questions_answer'])
			), $options['format']);
	}
	if ($out) echo $options['start'] . $out . $options['end'];
}

// здесь пагинация
mso_hook('pagination', $pag);

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>
