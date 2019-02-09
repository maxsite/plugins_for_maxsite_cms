<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
?>

<h1><?= t('Архивация данных', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет просматривать посещения сайта.', __FILE__) ?></p>
<?
if (mso_check_post(array('f_session_id', 'in_arhive'))){	//Тут будем хранить архивы посещений
	$path = getinfo('uploads_dir').'arhive/';
	//если папка поддерживает запись
	if (!is_writeable($path)){		echo t('Папка для записи архивов не доступна! Выставьте права на папку arhive в папке uploads 777', __FILE__);	}else{
		$str = '';
		$name = my_date_small(time());
		$name = str_replace(' ', '', $name);
		$query = $CI->db->get('view_visit');
		foreach($query->result() as $row){			$str .= $row->num.";".$row->referer.";".$row->link.";".$row->browser_small.";".$row->platform.";".$row->resolution.";".$row->lang.";".my_date_small($row->time).";".long2ip($row->ip).";\n";		}
			$CI->load->library('zip');
			$CI->zip->add_data($name.'.csv', $str);
			$CI->zip->archive($path.$name.'.zip');
			//$CI->zip->download(my_date_small(time()).'.zip');
			$data_arhive['hits'] = $query->num_rows();
			$data_arhive['hosts'] = count(array_hosts(null));
///// массив браузеров
			$data_arhive['browser'] = array_browser();
/////
///// массив платформ
			$data_arhive['platform'] = array_platform();
/////
///// массив разрешений
			$data_arhive['resolution'] = array_resolution();
/////
///// массив языков
			$data_arhive['language'] = array_language();
/////
///// массив стран
			$data_arhive['country'] = array_country();
/////
			add_arhive($data_arhive);
			if (is_file($path.$name.'.zip')){
				echo '<p>'.t('В архиве', __FILE__).'</p>';
			}
			$CI->db->truncate('view_visit');
			echo $path.$name.'.zip';
	}
}
?>

<?
$query = $CI->db->get('view_visit');
echo '<p>'.t('Всего записей в БД:', __FILE__).' <b>'.$query->num_rows().'</b></p>';

echo '<form method="POST">' . mso_form_session('f_session_id');
echo '<input type="submit" name="in_arhive" value="'.t('В архив', __FILE__).'">';
echo '</form>';

echo t('Сохраненные ранее архивы можна посмотреть ', __FILE__).'<a href="'.getinfo('site_admin_url').'files/arhive">'.t('здесь', __FILE__).'</a>';
?>