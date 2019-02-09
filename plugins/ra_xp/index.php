<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once( getinfo('common_dir') . 'page.php' );
require_once( getinfo('common_dir') . 'meta.php' );
require_once( getinfo('common_dir') . 'category.php' );

# функция автоподключения плагина
function ra_xp_autoload(){
	mso_create_allow('ra_xp_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('ra_xp', __FILE__));
	mso_hook_add( 'admin_init', 'ra_xp_admin_init'); # хук на админку
	mso_hook_add( 'admin_page_form_add_all_meta', 'ra_xp_meta', '0');
	mso_hook_add( 'edit_page', 'ra_xp_edit_post','0');
	mso_hook_add( 'new_page', 'ra_xp_edit_post','0');
	//	mso_hook_add( 'edit_page', 'ra_xp_delete_post','0');
	//mso_hook_add( 'delete_page', 'ra_xp_delete_post','0');
}

# функция выполняется при активации (вкл) плагина
function ra_xp_activate($args = array()){	
	return $args;
}



# Функция добавляет форму для параметров кросспостинга
function ra_xp_meta($args = array()){	
	$i=0;
	$meta=array();
	$id = mso_segment(3);
	$mm=array();
	if($id) {
		if($metaa=mso_get_meta('ra_xp', 'page',$id)) $meta = @unserialize($metaa[0]['meta_value']);
	}
	//pr($meta);
	foreach($meta as $m=>$v){
		$mm[$m]=$v;
	}
	
	$out = '<div>';
	$out .= '<h3>Параметры кросспостинга</h3>'; 
	$options=mso_get_option('ra_xp','plugins',array());
	//pr($meta);

	foreach($options as $op){	//поиск профиля из опции в массиве меток этой записи
		if(isset($mm[$op['ra_xp_num']])) { 
			$mess=' Изменить удаленный пост ';
			$xpid =' ('.$mm[$op['ra_xp_num']].'). ';
			//$as=array('id'=>$id, 'profile'=>$op['ra_xp_num'],'itemid'=>$mm[$op['ra_xp_num']]);
			//pr($as);
			//$ser=urlencode($as);
			//echo $ser;
			$xpdel='<a href="/admin/ra_xp/delete/?'.$id.'&'.$op['ra_xp_num'].'&'.$mm[$op['ra_xp_num']].'" target=_blank>Удалить</a>';
			//$xpdel='<a href="/admin/ra_xp/delete/'.$ser.'" target=_blank>Удалить</a>';
		} else { 
			$mess=' Добавить удаленный пост.';
			$xpid ='';
			$xpdel='';
		}
	if(!isset($op['ra_xp_status'])) $op['ra_xp_status']=0;
	if($op['ra_xp_status']=='1') $c=1;  //если в профиле статус по умолчанию 1
	elseif($op['ra_xp_status']=='0')$c=0;
	elseif($op['ra_xp_status']=='2' AND !$id) $c=1; 
	else $c=0; 
	
	$out .= '<label><input name="ra_xp_num['.$op['ra_xp_num'].']"  '.ra_xp_checked($c, 1).' type="checkbox"> '.$mess.$op['ra_xp_profile'].$xpid.$xpdel.'</label><br>';
	$i++;
	}
	// здесь брать параметры записи и синхронизировать с профилями кросспостинга.
	$out .= '</div>'; 
	
	return $args.$out;
}
# функция выполняется при деактивации (выкл) плагина
function ra_xp_deactivate($args = array()){	
	// mso_delete_option('plugin_ra_ljxp', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function ra_xp_uninstall($args = array()){	
	mso_delete_option('ra_xp', 'plugins'); // удалим созданные опции
	mso_remove_allow('ra_xp_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function ra_xp_admin_init($args = array()) {
	if ( !mso_check_allow('ra_xp_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'ra_xp'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Мульти-Кросспостинг', __FILE__));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/ra_ljxp
	if(mso_segment(3)=='delete') {
		ra_xp_del();
	}
	mso_admin_url_hook ($this_plugin_url, 'ra_xp_admin_page');

	
	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function ra_xp_admin_page($args = array()) {
	# выносим админские функции отдельно в файл

	if ( !mso_check_allow('ra_xp_edit') ) 	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('ra_xp', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('ra_xp', __FILE__) . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'ra_xp/admin.php');
	return $args;
}


# функции плагина

function ra_xp_edit_post($args = array(), $num = 1){
//global $options;
	$id=$args['0'];
	$meta=array();
	if($metaa=mso_get_meta('ra_xp', 'page',$id)) $meta = @unserialize($metaa[0]['meta_value']);

	$options=mso_get_option('ra_xp','plugins',array());
//_pr($options);
	if(isset($_POST['ra_xp_num']))	$pp=$_POST['ra_xp_num']; else $pp=array();

	//проверка нашей рубрики
	if(!isset($_POST['f_cat'])) return false;
	
	if($_POST['f_status'][0]!='publish') return false;
	
	foreach($options as $op){
		if(isset($pp[$op['ra_xp_num']]) ) {	//есть отметка в форме
			if(!array_intersect($op['ra_xp_cats'],$_POST['f_cat'])) continue;
			if(!isset($meta[$op['ra_xp_num']]) OR $meta[$op['ra_xp_num']]=='') { //заметка не кросспощена
				ra_xp($id,$op);
			} else {
				if($op['ra_xp_status_edit']!='1') continue;
				ra_xp($id,$op,$meta[$op['ra_xp_num']]);
			}
		} 			
	}
	return $args;
}
/*
function ra_xp_delete_post($args = array(), $num = 1){
//перебираем все метки поста
	$id=$args['0'];
	$meta=array();
	if($metaa=mso_get_meta('ra_xp', 'page',$id)) $meta = @unserialize($metaa[0]['meta_value']); else return $args;
	pr($meta);
//	ra_xp_del($id,$meta,$)
	//$options=mso_get_option('ra_xp','plugins',array());
return $args;
}
*/

function ra_xp_checked($a,$b){
	if ($a==$b) return "checked=checked";
	return false;
}


//подготовка поста к кросспостингу
function ra_xp($id,$op,$itemid=0){

require_once( getinfo('common_dir') . 'page.php' );
//if(!isset())
	switch($op['ra_xp_more']){
		default :
				$content=$_POST['f_content'];
			break;
		case 'copy' :
				$content = str_ireplace(array('[cut]','[xcut]'),'',$_POST['f_content']);
			break;
		case 'cuta' :
			if($pos = stripos($_POST['f_content'], '[cut]'))$cut="[cut]";
			elseif ($pos = stripos($_POST['f_content'], '[xcut]'))$cut="[xcut]";
			else $cut='';
			if($cut=='') $content=$_POST['f_content'];
			else {
				$co=explode($cut,$_POST['f_content'],2);
				$content=$co[0].'<lj-cut text="'.$op['ra_xp_cut'].'">'.$co[1].'</lj-cut>';
			}
			break;
		case 'link' :
			if($pos = stripos($_POST['f_content'], '[cut]')) 
				$content=substr($_POST['f_content'], 0, $pos).mso_page_title($_POST['f_slug'], $op['ra_xp_cute'], ' ', ' ', true, false); 
			elseif($pos = stripos($_POST['f_content'], '[xcut]')) 
				$content=substr($_POST['f_content'], 0, $pos).mso_page_title($_POST['f_slug'], $op['ra_xp_cute'], ' ', ' ', true, false); 
			else $content=$_POST['f_content'];
			break;
	}
	if($op['ra_xp_header']) $postHeader=$op['ra_xp_header']; else $postHeader='';
	
	if(isset($op['ra_xp_comments_link']) AND $op['ra_xp_comments_link']=='1') {
			$postHeader.= sprintf('<p>%s.</p>', mso_page_title($_POST['f_slug'], $op['ra_xp_comments_text'], ' ', ' ', true, false));
			$ra_xp_comments='0';
		} else $ra_xp_comments='1';
	
	if($op['ra_xp_footer'])$postFooter=$op['ra_xp_footer']; else $postFooter='';
		
	if($_POST['f_password']!='') $content=sprintf('<p><a href="%s">Запись защищена паролем.</a>.</p>', mso_page_title($_POST['f_slug'], '', ' ', ' ', true, false)) ;
	
	$content=$postHeader.$content.$postFooter;
	
	$metki='';
	$metka=array();
	if($op['ra_xp_metki_cat']==1) $metka=array_merge($metka,ra_xp_get_cats($_POST['f_cat']));
	if($op['ra_xp_metki_tag']==1) $metka=array_merge($metka,explode(',',$_POST['f_tags']));
	

	$metki=trim(implode(", ",$metka));

	$post=array(
	'subject'=>$_POST['f_header'], 
	'event'=>$content,
	'year'=>$_POST['f_date_y'],
	'mon'=>$_POST['f_date_m'],
	'day'=>$_POST['f_date_d'],
	'hour'=>$_POST['f_time_h'],
	'min'=>$_POST['f_time_m'],
	'metki'=>$metki,
	'comments'=>$ra_xp_comments
	);
	switch($op['ra_xp_privacy']) {
		case "public":
			$post['security'] = 'public';
			break;
		case "private":
			$post['security'] = 'private';
			break;
		case "friends":
			$post['security'] = 'usemask';
			$post['allowmask'] = '1';
			break;
		default :
			$post['security'] = "public";
			break;
	}
	if($op['ra_xp_community'])  $post['usejournal'] = stripslashes($op['ra_xp_community']); //else $post['usejournal'] = $op['ra_xp_name'];
	
	if($itemid) {
		$post['itemid']=$itemid;
		//$itemid_edit=ra_xp_post($post,$op);
	}
	
	if($response=ra_xp_post($post,$op)) {
		echo "<br>Заметка кросспощена: <a href=".$response['url']." target=_blank>".$response['url']."</a>";
		ra_xp_add_profile2meta($id,$op['ra_xp_num'],$response['itemid']);
		//ra_xp_add_profile2meta($id,$op['ra_xp_num'],$response['url']);
		} else {
			return false;
		}
	
}
function ra_xp_del(){
									
	
	$a=explode('&',$_SERVER['QUERY_STRING']);	
	$id=$a[0];
	$profile=$a[1];
	$itemid=$a[2];
	
	$post=array(
	'subject'=>'Delete this entry', 
	'event'=>'',
	'itemid'=>$itemid
	,'year'=>''
	,'mon'=>''
	,'day'=>''
	,'hour'=>''
	,'min'=>''
	,'comments'=>''
	,'security'=>''
	,'metki'=>''
	);
	
	$options=mso_get_option('ra_xp','plugins',array());
	//_pr($options[$profile]);
	$op=$options[$profile];
	if($op['ra_xp_community'])  $post['usejournal'] = stripslashes($op['ra_xp_community']); //else $post['usejournal'] = $op['ra_xp_name'];
	
	if(ra_xp_post($post,$op)) {
	ra_xp_delete_profile2meta($id,$profile);
	echo "Запись удалена.";
	exit();
	} else 
	exit();
}
function ra_xp_delete_profile2meta($id,$p){
	//получаем запись мета. разсериализируем его. обновляем массив. и отправляем на стандартную мету.
	$result=mso_get_meta('ra_xp', 'page',$id);
	$result = @unserialize($result[0]['meta_value']);
	unset($result[$p]);
	$pmeta = serialize($result);
	mso_add_meta('ra_xp',$id,'page',$pmeta);
}
	
function ra_xp_add_profile2meta($id,$p,$num){
	if ($num==0) return false;
	//echo $num."- new ";
	//получаем запись мета. разсериализируем его. обновляем массив. и отправляем на стандартную мету.
	$result=mso_get_meta('ra_xp', 'page',$id);
	$result = @unserialize($result[0]['meta_value']);
	$result[$p]=$num;
	$pmeta = serialize($result);
	//pr($result);
	mso_add_meta('ra_xp',$id,'page',$pmeta);
}

function ra_xp_get_cats($cats){
	$cats_n=array();
	$all=mso_cat_array_single();
	foreach ($all as $k=>$v){
		if(in_array($k,$cats)) $cats_n[]=$v['category_name'];
	}
	return $cats_n;
}


	
function ra_xp_post($post,$options){
	//pr($options);
	//echo "+".$options['ra_xp_host']."+";
	$CI = & get_instance();
	$CI->load->library('xmlrpc');

	//$options=mso_get_option('ra_xp','plugins',array());
	$CI->xmlrpc->server($options['ra_xp_host']);

	//отправляем challange-запрос
		$CI->xmlrpc->method('LJ.XMLRPC.getchallenge');
		if (!$CI->xmlrpc->send_request()) {
			echo " <br>Ошибка (профиль ".$options['ra_xp_profile']."): ".$CI->xmlrpc->display_error()."";
			//======================
			return false;
			//$challenge_response='123456';
		}
		else {
			$challenge_response=$CI->xmlrpc->display_response();
			//$challenge_response='123456';
		}
		//pr($options);
		$data=array(	             'username'=>array($options['ra_xp_name'],'string')
									,'auth_method'=>array('challenge','string')
									,'auth_challenge'=>array($challenge_response['challenge'],'string')
									,'auth_response'=>array(md5($challenge_response['challenge'].md5($options['ra_xp_pass'])),'string')
									,'ver'=>array('1','string')
									,'event'=>array($post['event'],'string')
									,'subject'=>array($post['subject'],'string')
									,'year'=>array($post['year'],'int')
									,'mon'=>array($post['mon'],'int')
									,'day'=>array($post['day'],'int')
									,'hour'=>array($post['hour'],'int')
									,'min'=>array($post['min'],'int')
									,'lineendings'=>array('pc','string')
									//,'community'=>array($post['community'],'string')
									,'props'=>array(
											array('opt_nocomments'=>array($post['comments'],'boolean'),
													//'opt_backdated'=>array(true,'boolean'),
													'taglist'=>array($post['metki'],'string')),
											'struct')
									,'security'=>array($post['security'],'string'));
									
		if(isset($post['itemid'])) $data['itemid']=array($post['itemid'],'int');
		if(isset($post['allowmask'])) $data['allowmask']=array($post['allowmask'],'int');
		if(isset($post['usejournal'])) {
			$data['usejournal']=array($post['usejournal'],'string');
		}
		
		//pr($data);
		//формируем массив с данными для создания записи
			$request = array(
							array(
								$data,'struct'
								)
						);
			//_pr($request);
			//отправляем запрос серверу
			if(isset($post['itemid'])) 
				$CI->xmlrpc->method('LJ.XMLRPC.editevent');
			else 
				$CI->xmlrpc->method('LJ.XMLRPC.postevent');
			
			$CI->xmlrpc->request($request);
			if (!$CI->xmlrpc->send_request()) {
				echo $CI->xmlrpc->display_error();
				return false;
			}
			else {
				$response = $CI->xmlrpc->display_response();
				//pr($response);
				//$postData['url'] – содержит адрес созданной записи
				 //$response['itemid'];
				 return $response;
			}
}
?>