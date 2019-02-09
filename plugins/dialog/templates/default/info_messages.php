<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// это текстовые сообщения которые выводятся в шаблонных файлах
   $messages = array(
     // 'new_comment_info' => 'new_comment_info' ,
      'breadcrumbs_razd' => ' >> ' ,	  
      'perelink_title_mask' => '<p class="comment_perelink">##AUTOR## в сообщении <a href="##COMMENT_URL##" title ="Перейти к сообщению">##TIMEAGO## ##ARROW##</a></p>',
      'quotes_title_mask' => '<p class="comment_perelink">##AUTOR## сказал(а) (<a href="##COMMENT_URL##" title ="Перейти к сообщению">##TIMEAGO## ##ARROW##</a>) :</p>',
      'question_title_mask' => '<p class="comment_perelink">##AUTOR## сказал(а) (<a href="##COMMENT_URL##" title ="Перейти к сообщению">##TIMEAGO## ##ARROW##</a>)  ',
      'answer_title_mask' => '<p class="comment_perelink">##AUTOR## ответил(а) (<a href="##COMMENT_URL##" title ="Перейти к сообщению">##TIMEAGO## ##ARROW##</a>)</p>',
 
       'to_forum' => 'Перейти на форум',
       'comm-disc'  => 'Переместить',
       'from_disc' => '<br>Порожденная из: ',
       'parent_comment' => 'Перейти к этому комменту в родительской дискуссии',
       'child_disc' => 'Порожденные дискуссии: ',
       'goto_child_disc' => 'Перейти в порожденную дискуссию',
       'parent' => 'Родитель: ',
       
       'only_registered' => 'Только зарегистрированные пользователи могут добавлять сообщения.',
 
      'send_email_title' => 'Отправить сообщение',
      'send_not_allow' => 'Только зарегистрированные и проверенные пользователи могут посылать сообщения.',
      'send_not_allow_rate' => 'Ваш рейтинг на форуме не позволяет посылать сообщения.',
      'send_not_allow_user' => 'Пользователь запретил отсылать себе сообщения.',
            
      'perelink_date_format' => 'j F Y',
     
      'title_category' => 'Разделы форума' ,
      'title_comments' => '' ,
      'login' => 'Войти' ,
      'register' => 'Регистрация' ,
      'profile_main' => 'Основные' ,
      'goto_comment' => 'Перейти к комменту в дискуссии',
 
      'count_discussions' => 'Дискуссий' ,
      'new_discussions' => 'Новых' ,
      'comments_count' => 'Сообщений',
      'return_category' => 'В категорию', // title
      'return_main' => 'На главную', // title
      'disc_up' => 'Вверх',
      'disc_up_title' => 'Поднятся вверх страницы',
      'to_form_title' => 'Опуститься к форме ввода сообщения',
      
      'add_ok' => 'Комментарий добавлен.' ,
      'edit-ok' => 'Изменения сохранены.' ,
      'title_private' => 'Приватные дискуссии' ,
      'desc_private' => 'Дискуссии, к которым имеют доступ только приглашенные автором пользователи' ,
      'title_activity' => 'Последние сообщения' ,
      'desc_activity' => 'Последняя активность в дискуссиях' ,
      
      'link_activity' => 'Все активные дискуссии >>' ,
      'link_activity_title' => 'Все дискуссии по убыванию последней активности' ,
    
      'title_last_desc' => 'Популярные:',
            
      'title_free_discussions' => 'Свободные дискуссии (без раздела)' ,
      
      'title_free_discussions_form' => 'Добавить новую свободную дискуссию (пока без раздела)' ,
      'desc_free_discussions_form' => 'Если есть подходящий раздел - перейдите и добавьте дискуссию в раздел.' ,

      'title_private_discussions_form' => 'Добавить новую приватную дискуссию' ,
      'desc_private_discussions_form' => '(Дискуссия будет доступна только приглашенным пользователям)' ,
      'form_members_title' => 'Пригласить участников приватной дискуссии (отметить галочкой)' ,
      'new_private' => 'Начать приватную дискуссию с этим пользователем' ,
      'private_members' => 'Участники',
 
       'welcome_forum_users' => 'Пригласить еще участников форума',      
       'welcome_no_forum_users' => 'Пригласить пользователей сайта, пока не учавствовавших в форуме',      

      'title_discussions_form' => 'Добавить новую дискуссию' ,
      'desc_discussions_form' => '(Дискуссия будет добавлена в эту категорию)' ,
      
      'title_new_comment_form' => 'Ответить' ,
      'desc_new_comment_form' => '(Продолжить дискуссию)' ,  
      'news_title' => 'Новые дискуссии' ,
      'news_desc' => '(В которых есть новые комменты с момента последнего просмотра)' ,        
      
      'title_edit_comment_form' => 'Редактирование комментария' ,
      'desc_edit_comment_form' => '(Изменить комментарий)' ,  
      'title_edit_discussion_form' => 'Редактирование дискуссии' ,
      'desc_edit_discussion_form' => '(Изменить дискуссию)' ,       
                
      'out_of_element' => 'Не найден элемент для вывода' ,
      'text_go_to_main' => 'Перейти на главную форума: ' ,
      'form_discussion_title' => 'Тема:' ,
      'form_discussion_desc' => 'Описание дискуссии:' ,
      'news' => 'Новости' ,
      'form_discussion_order' => 'Важность (целое от 0) :',
      'form_discussion_discussion_style_id' => 'Стиль (целое от 0) :',
      'form_discussion_tags' => 'Метки дискуссии:',
      
      'error_sortable' => 'Ошибка сортировки' ,
      
      'form_hello' => 'Пользователь:' ,
      'form_exit' => 'Выйти' ,
      'form_you-comment' => 'Ваш ответ:' ,
      'form_send' => 'Отправить' ,
      'out_of_discussion' => 'Дискуссия не найдена.' ,
      'not_approved' => 'Требуется модерация' ,
      'comment_deleted' => 'Комментарий удален' ,

      'discussion_closed' => 'Дискуссия закрыта',
      'out_of_comments' => 'Нет комментариев',
      'all-comments' => 'Все сообщения',
      'all-discussions' => 'Все дискуссии',
      
      'out_of_category' => 'Нет категории',
      
      'sortable' => 'Сортировка: ',

      'autor' => 'Автор: ',
      'date_create' => 'Дата создания: ',

      'do_edit' => 'Редактировать' ,
      'do_moderate' => 'Модерировать' ,
      'out_of_comment' => 'Нет комментария или комментарий еще не одобрен' ,
      'comment_number' => 'Комментарий #' ,
      'out_of_profiles' => 'Нет профилей' ,
      
      'form_undelete' => 'Восстановить' ,
      'form_delete' => 'Удалить' ,
      'form_unapproved' => 'Запретить' ,
      'form_approved' => 'Разрешить' ,
      'form_unspam' => 'Не спам' , 
      'form_spam' => 'Спам' , 
	  'form_ban' => 'Бан' , 
      
      'flud' => 'Флуд',
      'not_flud' => 'Не флуд',
      
      'form_select_category' => 'Переместить в категорию: ' , 
      
      'vid_min' => 'Свернуть' ,      
      'vid_max' => 'Развернуть' ,      

      'subscribe' => 'Подписаться' ,      
      'unsubscribe' => 'Отписаться' ,      

      'subscribe-ok' => 'Вы подписались на новые сообщения в этой теме' ,      
      'unsubscribe-ok' => 'Вы отменили подписку на новые сообщения в этой теме' , 

      'discussion_private' => 'Приватная дискуссия' , 

 
      'vid0-ok' => 'Применен сокращенный вид сообщений' ,      
      'vid1-ok' => 'Применен развернутый вид сообщений' ,
      'pag-ok' => 'Колличество сообщений на странице изменено' ,
      
      'delete-ok' => 'Статус изменен на "Удалено"' ,
      'undelete-ok' => 'Статус изменен на "Восстановлено"' ,
      'approved-ok' => 'Статус изменен на "Разрешено"' ,
      'unapproved-ok' => 'Статус изменен на "Запрещено"' ,  
      'spam-ok' => 'Статус изменен на "Спам"' ,
      'unspam-ok' => 'Статус изменен на "Не Спам"' ,
      'closed-ok' => 'Дискуссия закрыта' ,
      'unclosed-ok' => 'Дискуссия снова открыта' ,
        
       'font_size_mody' => 'Изменить размер шрифта',
            
      'access_denided' => 'Доступ запрещен' ,  
      'redirect_title' => 'Перенаправление на страницу комментария в дискуссии: ' , 
      'goto_title' => 'Страница комментария в дискуссии' , 
      
      'closed' => 'Дискуссия закрыта' ,
      'new_private_disc'  => 'Новая приватная дискуссия' ,
      'edit_status' => 'Изменить статус: ' ,
      'edit_discussion' => 'Редактировать' ,
          
      'comment_page' => 'Просмотреть комментарий в дискуссии' ,    
      'discussion_page' => 'Просмотреть дискуссию' ,    

      'profiles' => 'Пользователи' ,    
      'profile' => 'Профиль пользователя' ,    
      'subscribers' => 'Подписки на темы' ,    
      'disc_user_comment' => 'Дискуссии с участием',
      'you-comments'=>'Ваши сообщения',
      'you-subscribe'=>'Подписки',
      'comusers_url' => 'Сайт', 
      
      'spam' => 'Спам' ,
      'not_spam' => 'Не спам' , 
      'not_spam_check' => 'Не проверен' ,
      'closed' => 'Закрыть' ,
      'unclosed' => 'Открыть' ,
      'error_db_subscribe' => 'Ошибка БД при обновлении подписок' ,	    

    
      
      'quote' => 'Цитировать',
      'quote_title' => 'Выделенный текст будет добавлен в сообщение',
      'answer' => 'Ответить' ,
      'answer_title' => 'Ваше сообщение будет опубликовано, как ответ на выбранное сообщение' ,
      'form_answer' => 'Ответ на сообщение:' ,
      'no_answer' => 'Отменить ответ' ,
      'no_answer_title' => 'Отменить ответ на сообщение' ,
      'new_post' => 'Новое сообщение' ,
      'new_post_title' => 'Добавить новое сообщение в эту дискуссию' ,
      
      'show_answers' => 'Показать ответы' ,
      'hide_answers' => 'Скрыть ответы' ,
      'show_answers_title' => 'Показать/скрыть ответы' ,
      'show_parent' => 'Показать сказанное' ,
      'hide_parent' => 'Скрыть сказанное' ,
      'show_parent_title' => 'Показать/скрыть цитируемое' ,
      'who_quote' => 'Коммент цитировали' ,
       'title_comment_answer' => 'Ответ для ',  
            
      'guds' => 'Благодарности',
      'danke' => 'Спасибо',
      'danke_title' => 'Сказать спасибо за это сообщение',
      'danke_count' => 'Сказали спасибо', 
      'show_hide_who' => 'Показать/скрыть кто', 
      'bad' => 'Жаловаться',
      'bad_title' => 'Отправить коммент на рассмотрение модератора',
      
      'show_comments' => 'Смотреть комментарии',
      'user_dankes' => 'Благодарили: ',
      'show_dankes' => 'Смотреть благодарности',
      'user_count' => 'Сообщений: ',
      'user_rate' => 'Рейтинг: ',
      
      'comment_true_link' => 'Постоянная ссылка',
      
      'first' => 'В начало',
      'last' => 'В конец',
      'up' => 'В верх',
      'down' => 'В низ',
      
      'disc_new' => 'NEW',
      'disc_news' => 'NEWS',
      'new' => '*->',
      'new_title' => 'Новая дискуссия от этого сообщения',
      
      'need_approved' => 'Требует моерации',
      'you_autor' => 'Вы автор',
      'disc_closed' => 'Закрыта',
      'disc_private' => 'Приватная',
      
      'goto_new_comments' => 'Перейти к первому непросмотренному сообщению',
      'first_new_coment' => '(*)',
      'not_watch_count' => 'Новых: ',
      
      'all_log_title' => 'Лог админдействий',
      'all_log_desc' => 'Административные действия пользователей на форуме',
      'log_comment' => 'Лог действий',
      'log_user' => 'Лог действий',
      'all_log' => 'Все действия на форуме',
      'show_hide' => 'Показать/скрыть', 
      
      'vote_plus' => '+',
      'vote_minus' => '-',
      'vote_plus_title' => 'Хорошо',
      'vote_minus_title' => 'Плохо',      
      'allredy_vote' => 'Вы уже голосовали',
      'you_comment' => 'Нельзя хвалить себя',
      'register_for_vote' => 'Зарегистрируйтесь чтобы голосовать',
      
      //1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить, 7-флуд, 8-не флуд, 9-edit, 10-перенесен 11-бан
      'log_action1' => 'Разрешить',
      'log_action2' => 'Запретить',
      'log_action3' => 'Спам',
      'log_action4' => 'Не спам',
      'log_action5' => 'Удалить',
      'log_action6' => 'Восстановить',
      'log_action7' => 'Флуд',
      'log_action8' => 'Не флуд',
      'log_action9' => 'Редактирование',
      'log_action10' => 'Перенос',
      'log_action11' => 'Бан',
 
      // тексты писем
      'new_message'=>'Новое сообщение в дискуссии',
	    'goto_discussion'=>'Перейти к комментарию в дискуссии: ',
	    'unsubscribe_title'=>'Для того чтобы отписаться от новостей дискуссии перейдите по ссылке: ',      
      'new_comment'=>'Новое сообщение',
      'new_comment_on'=>'Новое сообщение в дискуссии',
      'comment_need_moderate'=>'Сообщение требует модерации',
      'nik'=>'Ник',
      'text'=>'Текст',
      'question' => 'На ваше сообщение',
      'get_answer' => 'Вам ответили',
      'quote_comment' => 'Сообщение которое процитировали',
      'get_quote' => 'Вас процитировали',
      
      'create_child_disc' => 'Дискуссия - ответвление созданна',
      'new_child_disc' => 'Ответвление в дискуссии',
      'new_child_on' => 'Ответвление в подписанной дискуссии',
      'from_comment' => 'От сообщения',
      'new_child_disc' => 'Порождннная дискуссия',
      'goto_new_discussion' => 'Перейти в новую дискуссию',
      'new_child_disc_you' => 'Ваше сообщение стало причиной дискуссии',
	  
	  'profile_no_active' => 'Нет',
	  'moderate-ok' => 'Появится после одобрения'
      // 
      
        );
  
 // извлечем сообщения из опций
 $options_messages = mso_get_option('dialog_messages' , 'plugins', array());
 
  // проинициализируем дефолтные
 foreach ($messages as $key => $val)
    if (!isset($options_messages[$key])) $options_messages[$key] = $val;

    
?>