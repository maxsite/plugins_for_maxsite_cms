<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
  Доступны переменные:
  
  $comments_ajax_path  - адрес для отправки ajax-запросов
  $comments_base_url   - базовый адрес страницы для которой выводятся комментарии
  $comments_type       - тип отображения списка комментариев: simple || complex
  $comments_replyto    - нужно ли вставлять имя автора исходного комментария при ответе: 0 || 1

  $comments_msg        - html-блок сообщения об ошибке сохранения комментария или блок-плейсхолдер
  $comments_count      - общее количество комментариев (число)
  $comments_best       - html с блоком лучших комментариев
  $comments_list       - html-код списка (ul) комментариев
  $comments_pagination - html-код блока пагинации
  $comments_form       - html-код формы комментирования
*/
?>
<div class="mso-type-page-comments">

	<span><a id="comments" data-ajax="{{ $comments_ajax_path }}" data-base="{{ $comments_base_url }}" data-replyto="{{ $comments_replyto }}"></a></span>

	{% if( $f = mso_page_foreach('page_comments_start') ) require($f); %}

	{{ mso_hook('page_comments_start') }}

	{{ $comments_msg }}
	
	{% if( $f = mso_page_foreach('page-comments-do-list') ) require($f); %}

	<div class="mso-comments {{ $comments_type }}">

		{{ $comments_best }}

		<p class="mso-comments-count">
			<span class="mso-comments-all-count">{{ tf('Комментариев') }}: {{ $comments_count }}</span> <a href="{{ mso_page_url($page['page_slug']) }}/feed" class="mso-comments-rss">RSS</a>
		</p>

		{{ $comments_form }}
		
		<section>
			{{ $comments_list }}
		</section>
	</div>

	{{ $comments_pagination }}

	{% if( $f = mso_page_foreach('page-comments-posle-list') ) require($f); %}
</div>
