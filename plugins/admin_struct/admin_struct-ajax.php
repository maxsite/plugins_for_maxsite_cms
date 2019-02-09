<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$CI = & get_instance();

if ( $post = mso_check_post(array('id','data')) ){
	$in_id = $post['id'];
	$in_data = $post['data'];
	
	//echo 'ОТРАБОТАЛО - '.$in_id.'<br><br><br>';
	//exit;
	$CI->db->select('*');
	$CI->db->join('cat2obj','page.page_id = cat2obj.page_id');
	//$CI->db->where('page_id', 'page_id');
	$CI->db->where('cat2obj.category_id', $in_id);
	if ($query = $CI->db->get('page')) {
		$res = $query->result_array();
		$cnt = $query->num_rows;
		echo '<ul class="list_page">';
		$i=1;
		foreach($res as $page) {
		if($i == $cnt) { $ad_class = ' last';}else{$ad_class='';}
			echo '<li class="page_list_child'.$ad_class.'"><div class="in-hov"><a href="http://svetar.com/admin/page_edit/'.$page['page_id'].'">'.$page['page_title'].'</a></div></li>';
		$i++;
		}
		echo '</ul>';
		//print_r($res);
		
	}
	
	
	
	exit;
	
	
	
	
	
	$stat = 'publish';
	if ($in_data == 'Публикация') {	//если нажата кнопка "Публикация"
		$CI->db->select('page_title');
		$CI->db->where('page_id', $in_id);
		$CI->db->where('page_status',$stat);
		if ($query = $CI->db->get('page')) {
			$res = $query->result_array();
			if($res) {//если опубликована
				$data = array('page_status' => 'draft');
				$this->db->where('page_id', $in_id);
				$this->db->update('mso_page', $data); 			
				echo 'Страница снята с публикации!';
			}else{
				$data = array('page_status' => 'publish');
				$this->db->where('page_id', $in_id);
				$this->db->update('mso_page', $data);
				echo 'Страница опубликована!';
			}
		}
	}
		
	if ($in_data == 'Удалить страницу') {	//если нажата кнопка "Удалить"	
	echo 'Пока не работает';	
		
		
	}
		
		
		
		
		
		
		
	}
//echo $page_id.'Отправился'.$data;
/*
$HostName = "127.0.0.1"; 
$UserName = "u2025781_amigo"; 
$Password = "15071987"; 
$DBName = "u2025781_ideal-roject"; 
	
$connect = new mysqli($HostName, $UserName, $Password,$DBName ) or exit("Нет такой базы");
         
        $connect->set_charset("utf8");
if ($data == 'Отменить публикацию') {
$select_type = "SELECT page_id  FROM mso_page WHERE page_status = 'draft' AND page_id = '$page_id'";
	$query = $connect->query($select_type);
		$num = $query->num_rows;
			//echo $num;
if($num) {
	//echo 'Черновик';
	$sql= "UPDATE `mso_page` SET  `page_status`='publish' WHERE `page_id`= '$page_id'";
	$mess = 'опубликована';
}else{
	$sql= "UPDATE `mso_page` SET  `page_status`='draft' WHERE `page_id`= '$page_id'";
	$mess = 'снята с публикации';
	//echo 'В публикации';
}

$query = $connect->query($sql);
	if($query){
		echo 'Страница '.$page_id.' '.$mess;
	}
}

if ($data == 'Удалить страницу'){
$del = "DELETE FROM `mso_page` WHERE `page_id`= '$page_id'";
	$query = $connect->query($del);
		if($query) {
			$opt = "OPTIMIZE TABLE `mso_page`";
				$query_opt = $connect->query($opt);
			echo 'Страница '.$page_id.' удалена';
		}

}
*/






# end files