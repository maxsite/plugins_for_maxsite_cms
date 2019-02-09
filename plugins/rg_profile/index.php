<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# TODO: Глянуть https://github.com/acornejo/jquery-cropbox
# функция автоподключения плагина
function rg_profile_autoload()
{
    global $MSO;
    # Если тип страницы users, то меняем на page_404, чтобы сработал наш хук
    if ($MSO->data['type'] == 'users' && mso_segment(2) != '') $MSO->data['type'] = 'page_404';
    mso_hook_add('custom_page_404', 'rg_profile_custom');
}

# функция выполняется при активации (вкл) плагина
function rg_profile_activate($args = array())
{
    $folder = getinfo('uploads_dir') . 'avatars';
    if (!is_dir($folder)) mkdir($folder);
    return $args;
}


# функции плагина
function rg_profile_custom($args = array())
{
    global $MSO;
    $CI = get_instance();

    # А с пользователями все подменяем
    if (mso_segment(1) == 'users') {
        if (mso_segment(3) == 'edit') {
        	if ( $fn = file_exists(getinfo('plugins_dir') . 'rg_profile/users-form.php'))
      		require_once $fn;
            return true;
        }
        elseif (mso_segment(2) == 'delfile') {
            $file_path = $_POST['url'];
            $filename = getinfo('uploads_dir') . 'avatars/' . substr($file_path, strrpos($file_path, '/')+1);
            if (file_exists($filename))
                unlink($filename);
            return true;
        }
    }
    return $args;
}

# обработка POST из формы комюзера
function my_mso_comuser_edit($args = array())
{
    global $MSO;

    if (!isset($args['css_ok'])) $args['css_ok'] = 'comment-ok';
    if (!isset($args['css_error'])) $args['css_error'] = 'comment-error';

    # id комюзера, который в сессии
    if (isset($MSO->data['session']['comuser']) and $MSO->data['session']['comuser'])
        $id_session = $MSO->data['session']['comuser']['comusers_id'];
    else $id_session = false;


    if ($post = mso_check_post(array('f_session_id', 'f_submit', 'f_comusers_activate_key'))) // это активация
    {
        # защита рефера
        mso_checkreferer();


        # защита сессии - если не нужно закомментировать строчку!
        if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();

        // получаем номер юзера id из f_submit[]
        $id = (int)mso_array_get_key($post['f_submit']);
        if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';

        # проверяем id в сессии с сабмитом
        // if ($id != $id_session)
        //	return '<div class="' . $args['css_error']. '">'. t('Ошибочный номер пользователя'). '</div>';

        $f_comusers_activate_key = trim($post['f_comusers_activate_key']);
        if (!$f_comusers_activate_key) return '<div class="' . $args['css_error'] . '">' . tf('Неверный (пустой) ключ') . '</div>';

        // нужно проверить если у указанного комюзера не равные ключи
        // если они равны, то ничего не делаем
        $CI = & get_instance();

        $CI->db->select('comusers_activate_string, comusers_activate_key');
        $CI->db->from('comusers');
        $CI->db->where('comusers_id', $id);
        $CI->db->limit(1);

        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $comuser = $query->result_array(); // данные комюзера

            if ($comuser[0]['comusers_activate_string'] == $comuser[0]['comusers_activate_key']) {
                // уже равны, активация не требуется
                return '<div class="' . $args['css_ok'] . '">' . tf('Активация уже выполнена') . '</div>';
            } else {
                // ключи в базе не равны
                // сверяем с переданным ключом из формы
                if ($f_comusers_activate_key == $comuser[0]['comusers_activate_key']) {
                    // верный ключ - обновляем в базе

                    $CI->db->where('comusers_id', $id);
                    $res = ($CI->db->update('comusers',
                        array('comusers_activate_string' => $f_comusers_activate_key))) ? '1' : '0';

                    $CI->db->cache_delete_all();

                    if ($res)
                        return '<div class="' . $args['css_ok'] . '">' . tf('Активация выполнена!') . '</div>';
                    else
                        return '<div class="' . $args['css_error'] . '">' . tf('Ошибка БД при добавления ключа активации') . '</div>';
                } else {
                    return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный ключ активации') . '</div>';
                }
            }
        } else // вообще нет такого комюзера
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';
    } elseif ($post = mso_check_post(array('flogin_session_id', 'flogin_submit', 'flogin_user', 'flogin_password',
        'flogin_redirect'))
    ) {
        // логинимся через стандартную _mso_login()
        _mso_login();
        return;
    } // это форма?
    elseif ($post = mso_check_post(array('f_session_id', 'f_submit', 'f_comusers_email', 'f_comusers_password',
        'f_comusers_nik', 'f_comusers_url', 'f_comusers_icq', 'f_comusers_msn', 'f_comusers_jaber',
        'f_comusers_date_birth', 'f_comusers_description'))
    ) // это обновление формы
    {
        if (!is_login_comuser())
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочные данные пользователя') . '</div>';

        # защита рефера
        mso_checkreferer();

        # защита сессии - если не нужно закомментировать строчку!
        if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();

        // получаем номер юзера id из f_submit[]
        $id = (int)mso_array_get_key($post['f_submit']);
        if (!$id) return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';

        # проверяем id в сессии с сабмитом
        if ($id != $id_session)
            return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный номер пользователя') . '</div>';


        $f_comusers_email = trim($post['f_comusers_email']);
        $f_comusers_password = trim($post['f_comusers_password']);

        if (!$f_comusers_email or !$f_comusers_password)
            return '<div class="' . $args['css_error'] . '">' . tf('Необходимо указать email и пароль') . '</div>';

        // проверим есть ли такой комюзер
        $CI = & get_instance();

        $CI->db->select('*');
        $CI->db->from('comusers');

        # CodeIgniter экранирует where, даже когда только условия в полях
        $CI->db->where('comusers_activate_string=comusers_activate_key', '', false); // активация должна уже быть

        $CI->db->where(array('comusers_id' => $id,
            'comusers_email' => $f_comusers_email,
            'comusers_password' => $f_comusers_password
        ));
        $CI->db->limit(1);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            // все ок - логин пароль верные
            $comuser = $query->result_array(); // данные комюзера

            $f_comusers_avatar_url = '';
            if (isset($post['f_comusers_avatar_url'])) $f_comusers_avatar_url = $post['f_comusers_avatar_url'];
            if (!empty($_FILES['f_comusers_avatar_url']['name'])) {
                require_once(getinfo('common_dir') . 'uploads.php'); // функции загрузки
                // параметры для mso_upload
                // конфиг CI-библиотеки upload
                $mso_upload_ar1 = array(
                    'upload_path' => getinfo('uploads_dir') . 'avatars',
                    'allowed_types' => 'gif|jpg|jpeg|png',
                );
                $mso_upload_ar2 = array( // массив прочих опций
                    'userfile_resize' => true, // нужно ли менять размер
                    'userfile_resize_size' => 150, // размер
                    'userfile_mini' => false, // делать миниатюру?
                    'prev_size' => false, // размер превьюхи
                    'message1' => '', // не выводить сообщение о загрузке каждого файла
                );
                $res = mso_upload($mso_upload_ar1, 'f_comusers_avatar_url', $mso_upload_ar2);
                unset($_FILES['f_comusers_avatar_url']);
                if ($res) $f_comusers_avatar_url = getinfo('uploads_url') . 'avatars/' . $CI->upload->file_name;
                else $f_comusers_avatar_url = '';
            }

            if (!isset($post['f_comusers_notify'])) $post['f_comusers_notify'] = '0';

            if (!isset($post['f_comusers_skype'])) $post['f_comusers_skype'] = ''; // скайп


            $post = mso_clean_post(array(
                'f_comusers_nik' => 'base',
                'f_comusers_url' => 'base',
                'f_comusers_icq' => 'base',
                'f_comusers_msn' => 'base',
                'f_comusers_jaber' => 'base',
                'f_comusers_skype' => 'base',
                'f_comusers_date_birth' => 'base',
                'f_comusers_description' => 'base',
                'f_comusers_notify' => 'int',
            ), $post);


            $upd_date = array(
                'comusers_nik' => strip_tags($post['f_comusers_nik']),
                'comusers_url' => strip_tags($post['f_comusers_url']),
                'comusers_icq' => strip_tags($post['f_comusers_icq']),
                'comusers_msn' => strip_tags($post['f_comusers_msn']),
                'comusers_jaber' => strip_tags($post['f_comusers_jaber']),
                'comusers_skype' => strip_tags($post['f_comusers_skype']),
                'comusers_date_birth' => strip_tags($post['f_comusers_date_birth']),
                'comusers_description' => strip_tags($post['f_comusers_description']),
                'comusers_avatar_url' => $f_comusers_avatar_url,
                'comusers_notify' => $post['f_comusers_notify'],
            );

            # pr($upd_date );

            $CI->db->where('comusers_id', $id);
            $res = ($CI->db->update('comusers', $upd_date)) ? '1' : '0';

            // если переданы метаполя, то обновляем и их
            if (isset($post['f_comusers_meta']) and $post['f_comusers_meta']) {
                //pr($post);

                foreach ($post['f_comusers_meta'] as $key => $val) {

                    // вначале грохаем если есть такой ключ
                    $CI->db->where('meta_table', 'comusers');
                    $CI->db->where('meta_id_obj', $id);
                    $CI->db->where('meta_key', $key);
                    $CI->db->delete('meta');

                    // теперь добавляем как новый
                    $ins_data = array(
                        'meta_table' => 'comusers',
                        'meta_id_obj' => $id,
                        'meta_key' => $key,
                        'meta_value' => $val
                    );

                    $CI->db->insert('meta', $ins_data);
                }
            }

            $CI->db->cache_delete_all();
            // mso_flush_cache(); // сбросим кэш

            if ($res)
                return '<div class="' . $args['css_ok'] . '">' . tf('Обновление выполнено!') . '</div>';
            else
                return '<div class="' . $args['css_error'] . '">' . tf('Ошибка БД при обновлении') . '</div>';
        } else return '<div class="' . $args['css_error'] . '">' . tf('Ошибочный email и пароль') . '</div>';

    } // обновление формы
}
# end file