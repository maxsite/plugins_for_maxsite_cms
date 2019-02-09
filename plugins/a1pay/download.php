<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$res = true;
	$res = $res && isset($get_arr['tid']) && !empty( $get_arr['tid'] ) && ( is_numeric($get_arr['tid']));
	$res = $res && ( isset($get_arr['check']) && !empty( $get_arr['check'] ) );
	if ( $res ) {
		$CI = & get_instance();
		
		$CI->db->select('duration, '. 
		                'ip, ' . 
						'date, ' . 
						'p.service_id, ' . 
						'check, ' . 
						'filename, ' . 
						'subfolder, ' .
						'downcount');
		$CI->db->from( 'a1pay_purchases p');
		$CI->db->join( 'a1pay_services s', 'p.service_id = s.service_id');
		$CI->db->where('p.tid', $get_arr['tid']);
		
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			// проверяем check
			if ( $row->check == $get_arr['check']) {
			
				$options = mso_get_option('a1pay', 'plugins', array());
				if ( !isset($options['check_ip']) ) $options['check_ip'] = 0; 
				$check_ip = $options['check_ip'];
			
				if ( !isset($options['check_duration'] )) $options['check_duration'] = 0;
				$check_duration = $options['check_duration'];	

				// если учитываем проверку по IP, то
				// echo 'По данной ссылке вы сможете скачать только с этого компьютера\n\n';
				if ( $check_ip ) {
					if ( $row->ip == $_SERVER['REMOTE_ADDR']) {

					} else {
						$t = '<h1>Вы не можете скачивать с данного IP-адреса: ' . $_SERVER['REMOTE_ADDR'] . '</h1>';
						die( $t );					
					}
				}	
	
				// если учитываем время жизни ссылки, то
				// echo 'Данная ссылка будет действовать ' . $duration . ' мсек\n\n';
				if ( $check_duration ) {
					$curtime = time();
					$linktime = strtotime( $row->date );
					$delta = $curtime - $linktime; 
					if ( $delta <= $row->duration ) {
						/*
						echo 'Ссылка действует.<br>';
						$dt = $row->duration - $delta;
						$dt = date('Y m d H i s Z', $dt);
						$stime = explode(' ', $dt);
						$delta = $stime[6] / 60 / 60;

						$dt = getdate( mktime( $stime[3] - $delta, $stime[4], $stime[5], $stime[1], $stime[2], $stime[0]));
						$datestr = '';
						
						$datestr .= ( $dt['yday'] > 0 ) ?  $dt['yday'] . ' дн ' : '';
						$datestr .= ( $dt['hours'] > 0 ) ?  $dt['hours'] . ' ч ' : '';
						$datestr .= ( $dt['minutes'] > 0 ) ?  $dt['minutes'] . ' мин ' : '';
						$datestr .= ( $dt['seconds'] > 0 ) ?  $dt['seconds'] . ' сек ' : '';
						echo 'Еще осталось ' . $datestr;
						*/
					} else {
						$t = '<h1>Данная ссылка не действительна. Время действия ссылки истекло.</h1>';
						die( $t );
					}
				}
			

				//будем отдавать файл пользователю
				$options = mso_get_option('a1pay', 'plugins', array());
				if ( !isset($options['folder']) ) $options['folder'] = 'downloads'; 
				$folder = $options['folder'];
				//$file =  end(explode("/", $row->filename));
				$file =  $row->filename;
				
				$xpath = (empty($row->subfolder)) ? '/' . $folder . '/' : '/' . $folder . '/' . $row->subfolder . '/';
				$fpath = $_SERVER['DOCUMENT_ROOT'] . $xpath . $row->filename;
				if	( !file_exists( $fpath ) ) {
						header ( 'HTTP/1.1 404 Not Found' );
						die('Файл не существует');
				}		

				$fsize = filesize( $fpath );	
				$ftime = date( 'D, d M Y H:i:s T', filemtime( $fpath ) );
				$range = 0;		
				$handle = @fopen( $fpath, 'rb' );				
				if( !$handle ){
					header ( 'HTTP/1.1 404 Not Found' );
					die('Ошибка чтения файла, либо файл не существует.');
				}
				$mimetype =  end(explode(".", $file));
				
				// поддерживаем докачку?
				if( isset($_SERVER['HTTP_RANGE']) ) {
					$range = $_SERVER['HTTP_RANGE'];
					$range = str_replace( 'bytes=', '', $range );
					$range = str_replace( '-', '', $range );
					// смещаемся по файлу на нужное смещение
					if ( $range ) fseek( $handle, $range );
				}
				
				// если есть смещение
				if( $range ) {
					header( 'HTTP/1.1 206 Partial Content' );
					// докачка не учитывает счетчик скачиваний
				} else {
					header( 'HTTP/1.1 200 OK' );
					// внесем скачку файла в базу
					$dc = $row->downcount + 1;
					$date = date('Y-m-d H:i:s');
					// обновим запись на предмет кол-ва скачиваний и даты последнего скачивания
					$data = array( 'downcount' => $dc, 'lastdownload' => $date);
					$CI->db->where('service_id', $row->service_id );	
					$CI->db->update('a1pay_services', $data);						
				}	

				header("Cache-Control: ");
				header("Pragma: ");
				header('Expires: 0');
				header( 'Content-Disposition: attachment; filename="'.$file.'"' );
				header( 'Last-Modified: '.$ftime );
				header( 'Content-Length: '.($fsize-$range) );
				header( 'Accept-Ranges: bytes' );
				header( 'Content-Range: bytes '.$range.'-'.($fsize - 1).'/'.$fsize );
					
				switch( $mimetype ) {
					case 'pdf' : $ctype = 'application/pdf'; break;
					case 'zip' : $ctype = 'application/zip'; break;
					case 'doc' : $ctype = 'application/msword'; break;
					case 'xls' : $ctype = 'application/vnd.ms-excel'; break;
					case 'gif' : $ctype = 'image/gif'; break;
					case 'png' : $ctype = 'image/png'; break;
					case 'jpeg':
					case 'jpg' : $ctype = 'image/jpg'; break;
					case 'mp3' : $ctype = 'audio/mpeg'; break;
					case 'wav' : $ctype = 'audio/x-wav'; break;
					case 'mpeg':
					case 'mpg' :
					case 'mpe' : $ctype = 'video/mpeg'; break;
					case 'mov' : $ctype = 'video/quicktime'; break;
					case 'avi' : $ctype = 'video/x-msvideo'; break;
					default    : $ctype = 'application/octet-stream';
				}
				header( 'Content-Type: '.$ctype );
				fpassthru( $handle);
				header ("Connection: close");
				exit();
					
			} else {
				header ( 'HTTP/1.1 404 Not Found' );
				die('Ссылка на скачивание не верна.');
			}
		} else {
			header ( 'HTTP/1.1 404 Not Found' );
			die( 'Такой ссылки не существует!');
		}
	} else {
		header ( 'HTTP/1.1 404 Not Found' );
		die( 'Ссылка на скачивание не верна.' );
	}	
	
	
 
?>