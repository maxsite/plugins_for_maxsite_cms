<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 
			// покажим фотку
			extract( $foto );
			
			echo '<h1 class="foto-title">' . $foto_title . '</h1>';
			//echo '<div class="foto-date">' . $foto_date . '</div>';
			if ( is_login() ) echo '<span><a href="'.getinfo('site_url').'admin/edit-foto/' . $foto_id . '">[Редактировать]</a></span>';
			echo '<div class="foto-current">';
				$big = getinfo('uploads_dir'). $options['upload_path'] . '/origin/' . $foto_path;
				$big_foto = getinfo('uploads_url'). $options['upload_path'] . '/origin/' . $foto_path;
				$is_big = file_exists( $big );
				//if ( $is_big ) echo '<a class="lightbox" href="'.$big_foto.'" title="'.$foto_title.'">';
				echo '<img src="'.getinfo('uploads_url'). $options['upload_path'] . '/current/' . $foto_path . '" />';
				//if ( $is_big ) echo '</a>';
				if ( $is_big ) echo '<div class="big-foto"><a href="'.$big_foto.'" title="'.$foto_title.'" target="_blank">Оригинальный размер</a></div>';
			echo '</div>';
			
			echo '<div class="foto-meta">';
				echo '<div class="foto-metadata">';
					echo '<div class="foto-view-count">';
						echo '<span><strong>Просмотров: </strong>'.$foto_view_count.'</span>';
					echo '</div>';
					echo '<div class="foto-album">';
						$album = get_current_album( $foto_album_id );
						$album_url = '<a href="'.getinfo('site_url').$foto_albums.'/'.$album['foto_album_slug'].'">'.$album['foto_album_title'].'</a>';
						echo '<span><strong>Альбом: </strong>'.$album_url.'</span>';
					echo '</div>';
					echo '<div class="foto-uploaded">';
						echo '<span><strong>Загружено: </strong>' . $foto_date . '</span>';
					echo '</div>';
					echo '<div class="foto-descr">';
					if ( $foto_descr ) echo '<span><strong>Описание: </strong>' . $foto_descr . '</span>'; 
					else if ( is_login() ) {
						echo '<span class="add-description" onclick="add_description();">Нажмите, чтобы добавить описание</span>';
						echo '<div class="description">';
							echo '<textarea name="description-text"></textarea><br>';
							echo '<input type="button" name="f_add_description" value="Добавить" onclick="submit_description('.$foto_id.');">';
							echo '<input type="button" name="f_cancel_description" value="Отмена" onclick="cancel_description();">';
						echo '</div>';
					}
					echo '</div>';
					
					// метки
					$tags = get_foto_tags( $foto_id, ', ', is_login() );
					echo '<div class="foto-tags">';

					
					if ( $tags ) {
						
							echo '<span><strong>Метки: </strong></span>';
							echo $tags;

					}
					
					echo '</div>';
					
					if ( is_login() ) {
						// возможность добавления тегов прям на странице
						$metki = '<a href="" id="admin-new-tag" onclick="add_new_tag(); return false;">добавить метку</a>';
						$metki .= '<div id="add-new-meta" style="display: none"><input type="textfield" name="add-new-meta-value"><input type="button" value="Добавить" onclick="add_new_meta('.$foto_id.')"></div>';
						echo $metki;
					}					
				echo '</div>';
				echo '<div class="foto-exif">';
						echo '<a href="" onclick="show_exif_data(); return false;">Информация о снимке (EXIF)</a>';
						$foto_exif = unserialize( $foto_exif );
						extract( $foto_exif );
											if ( isset( $DateTimeOriginal ) ) {
											    $DateTimeOriginal = explode(' ', $DateTimeOriginal);
    											    $DateTimeOriginal = str_replace(':', '-', $DateTimeOriginal[0]) . ' ' . $DateTimeOriginal[1];
											    $DateTimeOriginal = mso_page_date($DateTimeOriginal, 											
											    array(	'format' => 'j F Y H:i:s', // 'd/m/Y H:i:s'
											    'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
												'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
												'', '', false);
											} else $DateTimeOriginal = '';	
											echo '<div class="foto-exif-data" style="display: none;">';
												if ( isset( $Model ) && !empty($Model) ) echo '<div class="foto-exif-model"><strong>Модель: </strong><span>' . $Model . '</span></div>';
												if ( isset( $DateTimeOriginal ) && !empty($DateTimeOriginal) ) echo '<div class="foto-exif-date"><strong>Дата снимка: </strong><span>' . $DateTimeOriginal . '</span></div>';
												if ( isset( $ExposureTime ) && !empty($ExposureTime) )echo '<div class="foto-exif-exposure-time"><strong>Выдержка: </strong><span>' . $ExposureTime . '</span></div>';
												if ( isset( $FNumber ) && !empty($FNumber) )echo '<div class="foto-exif-fnumber"><strong>Диафрагма: </strong><span>' . $FNumber . '</span></div>';
												if ( isset( $ExposureProgram ) && !empty($ExposureProgram) )echo '<div class="foto-exif-exposure-program"><strong>Режим: </strong><span>' . $ExposureProgram . '</span></div>';
												if ( isset( $ISOSpeedRatings ) && !empty($ISOSpeedRatings) )echo '<div class="foto-exif-iso"><strong>ISO: </strong><span>' . $ISOSpeedRatings . '</span></div>';
												if ( isset( $FocalLength ) && !empty($FocalLength) )echo '<div class="foto-exif-focal-length"><strong>Фокусное: </strong><span>' . $FocalLength . '</span></div>';
												if ( isset( $ExposureMode ) && !empty($ExposureMode) )echo '<div class="foto-exif-exposure-mode"><strong>Экспозиция: </strong><span>' . $ExposureMode . '</span></div>';
												if ( isset( $MeteringMode ) && !empty($MeteringMode) )echo '<div class="foto-exif-metering-mode"><strong>Режим замера: </strong><span>' . $MeteringMode . '</span></div>';
												if ( isset( $SceneCaptureType ) && !empty($SceneCaptureType) )echo '<div class="foto-exif-scene-type"><strong>Тип съемки: </strong><span>' . $SceneCaptureType . '</span></div>';
											echo '</div>';
				echo '</div>';
				
				// рейтинг фотки
				//if (  ($foto_rate_count == 0) or ( $foto_rate_minus > $foto_rate_plus )  ) $foto_rate = 0; 
				//else 
				
				$foto_rate = $foto_rate_plus - $foto_rate_minus;
				echo '<div class="foto-rating">';
					echo '<div class="foto-rate"><strong>Рейтинг: </strong><span>'.$foto_rate.'</span></div>';
					echo '<div class="foto-rate-plus"><strong>+ </strong><span>'.$foto_rate_plus.'</span></div>';
					echo '<div class="foto-rate-minus"><strong>- </strong><span>'.$foto_rate_minus.'</span></div>';
					echo '<div class="foto-rate-count"><strong>Голосов: </strong><span>'.$foto_rate_count.'</span></div>';
					echo '<div class="foto-rate-buttons">';
					if ( ! check_allready_vote ( $foto_id ) ) {
						echo '<input type="button" name="rating-good" value="Хорошо" onclick="rating_change('.$foto_id.', \'good\');">';
						echo '<input type="button" name="rating-bad" value="Плохо" onclick="rating_change('.$foto_id.', \'bad\');">';
					} else {
						echo '<span class="foto-rate-voted">Спасибо за ваш голос!</span>';
					}
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
			// вывести еще фотки из этого альбома 
			echo '<div class="break"></div>';
			$count = (int)$options['other_foto_count']; // взять из опций
			$html_do = '<div class="other-foto"><h3>'.$options['other_foto_title'].'</h3>';
			$html_posle = '</div>';
			$sort_order = 'random';
			$exclude_foto = $foto_id;
			$from_album = false;
			$fotki = get_last_thumb_fotos( $count, $html_do, $html_posle, $sort_order, $exclude_foto, $from_album, 'last-foto-80' );
			echo $fotki;
			
			
			echo '<div class="break"></div>';
			require_once( getinfo('plugins_dir') . $plug_url . '/foto-comments.php' );
?>