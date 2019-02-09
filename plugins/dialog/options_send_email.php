<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле инициализируются дефолтные опции отправки сообщений пользователями

  $options_send_email = array(
    'email_fields' => '
    [field]
        require = 1   
        type = select
        description = Выберите тип сообщения
        values = Вопрос # Ответ # Пожелание # Спам # Благодарность # Информация к размышлению
        default = Информация к размышлению
        tip = Тип сообщения поможет аресату
    [/field]

    [field]
        require = 0   
        type = text
        description = Ваше место проживания
        tip = Указывайте Город И Страну
    [/field]

    [field]
        require = 1
        type = textarea
        description = Ваше сообщение
    [/field]    
    ' ,
   
    
		)   ; 

foreach ($options_send_email as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;

     
?>