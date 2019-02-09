<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
  Доступны переменные:
  
  $avatar   - ссылка на аватарку
  $a_class  - mso-comment-odd|mso-comment-even, mso-comment-best, mso-comment-users|mso-comment-comusers|mso-comment-anonim
  $begin_num - начальный номер 
   
  $comment_num - номер комментария по порядку
  $comments_id - id комментария
  $comusers_url - ссылка на сайт комюзера
  $comments_content - содержимое комментария
  $comments_approved - признак промодерированности комментария
  $comments_rating - рейтинг комментария (количество лайков)
  $comments_hide_reply - скрывать ли кнопку ответить на последнем уровне вложенности
  $comments_url - имя автора (возможно обёрнутое в ссылку на твиттер-аккаунт)
  
  $best
  $best_context
*/
?>

<article class="mso-comment-article {{ $a_class }} clearfix">
	
	<div class="comment-author-image">
		<img src="{{ $avatar }}" alt="" class="mso-gravatar">
	</div>
		
	<div class="comment-body">
		<p class="mso-comment-info">
			<span class="mso-comment-num">{{ isset($begin_num) && $begin_num >= 0 && $comment_num > 0 ? $comment_num : '-' }}</span>
			
			<span class="mso-comment-author">{{ $comments_url }}</span>
			
			{% if ($comusers_url) : %}
				<a href="{{ $comusers_url }}" rel="nofollow" class="mso-comuser-url">{{ tf('Сайт') }}</a>
			{% endif %}
			
			<a href="{{ comments_comment_link($comment) }}"{% if (!$best) : %} id="comment-{{ $comments_id }}"{% endif %} class="mso-comment-date">{{ mso_date_convert('d-m-Y H:i', $comments_date) }}</a>
			
			{% if (!$comments_approved) : %}
				<span class="mso-comment-approved">{{ tf('Ожидает модерации') }}</span>
			{% endif %}
			
			{% if ($edit_link) : %}
				<a href="{{ $edit_link . $comments_id }}" class="mso-comment-edit">edit</a>
			{% endif %}

			
			{% if ($rating && $comments_approved) : %}
				<button type="button" class="rating" data-id="{{ $comments_id }}">{{ $comments_rating }}</button>{% if ($rating_loader) : %}<span>{{ $rating_loader }}</span>{% endif %}
			{% endif %}
			
		</p>
		
		<div class="mso-comment-content">{{ $comments_content }}</div>

		{% if ($comments_approved && !$comments_hide_reply) : %}
			<div class="mso-comment-reply"><button type="button" class="reply" data-parent="{{ $comments_id }}">{{ tf('Ответить') }}</button><button type="button" class="cancel">{{ tf('Отмена') }}</button></div>
		{% endif %}
	</div>

</article>
