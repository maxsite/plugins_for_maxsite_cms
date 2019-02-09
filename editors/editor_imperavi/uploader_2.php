<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$error = $ok = '';

$CI = & get_instance();
$CI->load->helper('file'); // хелпер для работы с файлами
$CI->load->helper('directory');
$CI->load->helper('form');
require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
// разрешенные типы файлов
$allowed_types = mso_get_option('allowed_types', 'general', 'gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|flv|swf|mp3|wav|xls|7z');
$r = array(// массив прочих опций
    'userfile_mini' => true, // делать миниатюру?
    'userfile_mini_size' => (int) mso_get_option('size_image_mini', 'general', 150), // размер миниатюры
    'mini_type' => 1, // тип миниатюры
    'prev_size' => 100, // размер превьюхи
    'userfile_resize_size' => (int) mso_get_option('resize_images', 'general', 600),
    'resize_images' => true

);
$current_dir = date("Y/m/d");

if ($current_dir)
    $current_dir .= '/';

$path = getinfo('uploads_dir') . '' . $current_dir;
$path_url = getinfo('uploads_url') . '' . $current_dir;

if (!is_dir($path))
    mkdir($path, 0777, TRUE); // нет каталога, пробуем создать

    
# загрузка нового файла=====================================================================================================
if (isset($_FILES['file'])) {
    $_FILES['fileToUpload'] = $_FILES['file'];

    mso_checkreferer();
    $config['upload_path'] = $path;
    $config['allowed_types'] = $allowed_types;


    $CI->load->library('upload', $config);

    // если была отправка файла, то нужно заменить поле имени с русского на что-то другое
    // это ошибка при копировании на сервере - он не понимает русские буквы
    if (isset($_FILES['fileToUpload']['name'])) {
        $f_temp = $_FILES['fileToUpload']['name'];

        // оставим только точку
        $f_temp = str_replace('.', '__mso_t__', $f_temp);
        $f_temp = mso_slug($f_temp); // остальное как обычно mso_slug
        $f_temp = str_replace('__mso_t__', '.', $f_temp);

        $_FILES['fileToUpload']['name'] = $f_temp;
    }

    $res = $CI->upload->do_upload('fileToUpload');

    if ($res) {
        $ok.= '<div class="update">Загрузка выполнена</div>';

        // если это файл картинки, то нужно сразу сделать скриншот маленький в _mso_i 100px, который будет выводиться в
        // списке файлов
        $up_data = $CI->upload->data();

        // файл нужно поменять к нижнему регистру
        if ($up_data['file_name'] != strtolower($up_data['file_name'])) {
            // переименуем один раз
            if (rename($up_data['full_path'], $up_data['file_path'] . strtolower('__' . $up_data['file_name']))) {
                // потом второй в уже нужный - это из-за бага винды
                rename($up_data['file_path'] . strtolower('__' . $up_data['file_name']), $up_data['file_path'] . strtolower($up_data['file_name']));

                $up_data['file_name'] = strtolower($up_data['file_name']);
                $up_data['full_path'] = $up_data['file_path'] . $up_data['file_name'];
                // echo '<div class="update">' . $up_data['full_path'] . $up_data['file_name'] . '</div>';
            }
            else
                $error.= '<div class="error">Не удалось перименовать файл в нижний регистр</div>';
        }
        if ($up_data['is_image']) { // это картинка
            $CI->load->library('image_lib');
            $CI->image_lib->clear();
            
            $size = abs((int) $r['userfile_resize_size']);

            ($up_data['image_width'] >= $up_data['image_height']) ? ($max = $up_data['image_width']) : ($max = $up_data['image_height']);
            if ($size > 1 and $size < $max) { // корректный размер
                $r_conf = array(
                    'image_library' => 'gd2',
                    'source_image' => $up_data['full_path'],
                    'new_image' => $up_data['full_path'],
                    'maintain_ratio' => true,
                    'width' => $size,
                    'height' => $size,
                );

                $CI->image_lib->initialize($r_conf);

                if (!$CI->image_lib->resize())
                    echo '<div class="error">' . t('Уменьшение изображения:') . ' ' . $CI->image_lib->display_errors() . '</div>';
            }
            # делаем миниатюру
            mso_upload_mini($up_data, $r);

            # превьюшка
            mso_upload_prev($up_data, $r);
        }
    } else {
        $er = $CI->upload->display_errors();
        $error.= '<div class="error">Ошибка загрузки файла.' . $er . $path_url . '</div>';
    }
    if ($error)
        $error_status = 1; else
        $error_status = 0;
    echo '<img src="' . $path_url . '' . $up_data['file_name'] . '"> ';
    exit();
}
echo '{
"error_status":"1",
"error_description":"Нет картинки"
}';
exit();

# из файла common/uploads.php
# функция делает миниатюры
# $up_data - массив данных из $CI->upload->data()
# $r - параметры из mso_upload()
function mso_upload_mini($up_data, $r = array())
{
	# получим размеры файла
	$image_info = GetImageSize($up_data['full_path']);
	
	if (!$image_info) return; // это не изображение
	
	$image_width = $image_info[0];
	$image_height = $image_info[1];

	// нужно создать в этом каталоге mini если нет
	if (!is_dir($up_data['file_path'] . 'mini')) @mkdir($up_data['file_path'] . 'mini', 0777); // нет каталога, пробуем создать
	
	$CI = & get_instance();
	$CI->load->library('image_lib');	
	$CI->image_lib->clear();
	
	# теперь нужно сделать миниатюру указанного размера в mini
	if ($r['userfile_mini'] and $r['userfile_mini_size'])
	{
		$size = abs((int) $r['userfile_mini_size']);

		($image_width >= $image_height) ? ($max = $image_width) : ($max = $image_height);
		if ( $size > 1 and $size < $max ) // корректный размер
		{
			$r_conf = array(
				'image_library' => 'gd2',
				'source_image' => $up_data['full_path'],
				'new_image' => $up_data['file_path'] . 'mini/' . $up_data['file_name'],
				'maintain_ratio' => true,
				'width' => $size,
				'height' => $size,
			);

			// pr($r_conf);
			
			$mini_type = $r['mini_type']; // тип миниатюры
			/*
			1 Пропорционального уменьшения
			2 Обрезки (crop) по центру
			3 Обрезки (crop) с левого верхнего края
			4 Обрезки (crop) с левого нижнего края
			5 Обрезки (crop) с правого верхнего края
			6 Обрезки (crop) с правого нижнего края
			7 Уменьшения и обрезки (crop) в квадрат
			*/

			if ($mini_type == 2) // Обрезки (crop) по центру
			{
				$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
				$r_conf['y_axis'] = round($image_height / 2 - $size / 2);
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
			{
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = 0;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
			{
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
			{
				$r_conf['x_axis'] = $image_width - $size;
				$r_conf['y_axis'] = 0;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
			{
				$r_conf['x_axis'] = $image_width - $size;
				$r_conf['y_axis'] = $image_height - $size;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 7) // Уменьшения и обрезки (crop) в квадрат
			{
				if ($image_width > $image_height) // Если ширина больше высоты
				{
					$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
					$r_conf['width'] = $resize;
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
					
					$r_conf['x_axis'] = round(($resize - $size) / 2);
					$r_conf['y_axis'] = 0;
					$r_conf['width'] = $size;
					$r_conf['maintain_ratio'] = false;
					$r_conf['source_image'] = $r_conf['new_image'];
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($image_width < $image_height) // Если высота больше ширины
				{
					$resize = round($size * $image_height / $image_width);
					$r_conf['height'] = $resize;
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
					
					$r_conf['x_axis'] = 0;
					$r_conf['y_axis'] = round(($resize - $size) / 2);
					$r_conf['height'] = $size;
					$r_conf['maintain_ratio'] = false;
					$r_conf['source_image'] = $r_conf['new_image'];
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				else // Равны
				{
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}					
			}
			else // ничего не указано - Пропорционального уменьшения
			{
				$CI->image_lib->initialize($r_conf);
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
		}
		else
		{
			//Размер некорректный и миниатюру просто копируем из большого изображения.
			copy($up_data['full_path'], $up_data['file_path']. 'mini/'. $up_data['file_name']);
		}
	}
}

# функция делает превьюшку 100x100
# $up_data - массив данных из $CI->upload->data()
# $r - параметры из mso_upload()
function mso_upload_prev($up_data, $r = array())
{
	# получим размеры файла
	$image_info = GetImageSize($up_data['full_path']);
	if (!$image_info) return; // это не изображение
	
	$image_width = $image_info[0];
	$image_height = $image_info[1];
	
	// нужно создать в этом каталоге _mso_i если нет
	if (!is_dir($up_data['file_path'] . '_mso_i')) @mkdir($up_data['file_path'] . '_mso_i', 0777); // нет каталога, пробуем создать
	
	
	$CI = & get_instance();
	$CI->load->library('image_lib');
	$CI->image_lib->clear();
	
	# всегда делаем 100 на 100
	# алгоритм тот же, что и у миниатюры
	$size = $r['prev_size'];
	
	if ($size > 0) // если нужно делать превьюху
	{	
		$r_conf = array(
			'image_library' => 'gd2',
			'source_image' => $up_data['full_path'],
			'new_image' => $up_data['file_path'] . '_mso_i/' . $up_data['file_name'],
			'maintain_ratio' => true,
			'width' => $size,
			'height' => $size,
		);


		$mini_type = $r['mini_type']; // тип миниатюры
		/*
		1 Пропорционального уменьшения
		2 Обрезки (crop) по центру
		3 Обрезки (crop) с левого верхнего края
		4 Обрезки (crop) с левого нижнего края
		5 Обрезки (crop) с правого верхнего края
		6 Обрезки (crop) с правого нижнего края
		7 Уменьшения и обрезки (crop) в квадрат
		*/

		if ($mini_type == 2) // Обрезки (crop) по центру
		{
			$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
			$r_conf['y_axis'] = round($image_height / 2 - $size / 2);

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
		{
			$r_conf['x_axis'] = 0;
			$r_conf['y_axis'] = 0;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
		{
			$r_conf['x_axis'] = 0;
			$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
		{
			$r_conf['x_axis'] = $image_width - $size;
			$r_conf['y_axis'] = 0;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
		{
			$r_conf['x_axis'] = $image_width - $size;
			$r_conf['y_axis'] = $image_height - $size;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 7) // Уменьшения и обрезки (crop) в квадрат
		{
			if ($image_width > $image_height) // Если ширина больше высоты
			{
				$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
				$r_conf['width'] = $resize;
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				
				$r_conf['x_axis'] = round(($resize - $size) / 2);
				$r_conf['y_axis'] = 0;
				$r_conf['width'] = $size;
				$r_conf['maintain_ratio'] = false;
				$r_conf['source_image'] = $r_conf['new_image'];
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($image_width < $image_height) // Если высота больше ширины
			{
				$resize = round($size * $image_height / $image_width);
				$r_conf['height'] = $resize;
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = round(($resize - $size) / 2);
				$r_conf['height'] = $size;
				$r_conf['maintain_ratio'] = false;
				$r_conf['source_image'] = $r_conf['new_image'];
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			else // Равны
			{
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}					
		}
		else // ничего не указано - Пропорционального уменьшения
		{
			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->resize())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}

	}

	
	
}