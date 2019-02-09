<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-comment-leave">{{ tf('Оставьте комментарий!') }}</div>

<div class="mso-comment-form general">
	<form method="post">
		<input type="hidden" name="comments_page_id" value="{{ $page['page_id'] }}">
		{{ mso_form_session('comments_session') }}

		<div class="mso-comments-autharea{% if( is_login() || is_login_comuser() ): %} user{% else : %} anonim{% endif %}">
			
			{% if( is_login() || $comuser = is_login_comuser() ): %}
				{%  if (is_login()) : %}
					<input type="hidden" name="comments_user_id" value="{{ getinfo('users_id') }}">
					
					<div class="mso-comments-user">
						<a href="{{ getinfo('siteurl').'admin/users/edit/'.getinfo('users_id') }}">{{ getinfo('users_nik') }}</a>
					</div>
				{% endif %}

				{% if ($comuser = is_login_comuser()) : %}
					<input type="hidden" name="comments_email" value="{{ $comuser['comusers_email'] }}">
					<input type="hidden" name="comments_password" value="{{ $comuser['comusers_password'] }}">
					<input type="hidden" name="comments_password_md" value="1">
					<input type="hidden" name="comments_reg" value="reg">
		
					<div class="mso-comments-user mso-comments-comuser">
						<a href="{{ getinfo('siteurl').'users/'.$comuser['comusers_id'] }}">{% if (!$comuser['comusers_nik']) : %}Комментатор №{{ $comuser['comusers_id'] }}{% else : %}{{ $comuser['comusers_nik'] }}{% endif %}</a>
					</div>
				{% endif %}
				
				<img src="{{ $avatar }}" alt="" class="mso-gravatar">
				
				<p><a href="{{ getinfo('siteurl') }}logout">{{ tf('Выйти') }}</a></p>
			{% else : %}
				<div class="mso-tabs_widget mso-tabs_widget_commentsform">
					<div class="mso-tabs">
						<ul class="mso-tabs-nav">
							<li class="mso-tabs-elem mso-tabs-current">
								<span>Без регистрации</span>
							</li>
							<li class="mso-tabs-elem">
								<span>Вход/Регистрация</span>
							</li>
						</ul>
						<div class="mso-tabs-box mso-tabs-visible">
							<!-- без регистрации -->
							<input type="hidden" name="comments_reg" value="noreg">
							{%  if (mso_get_option('form_comment_easy', 'general', '0')) : %}
								<!-- простая форма -->
								<div class="mso-comments-auth">

									{% if (mso_get_option('allow_comment_anonim', 'general', '1') ) : %}
											
										<p>
											<label for="comments_author">{{ tf('Ваше имя') }}</label>
											<input type="text" name="comments_author" id="comments_author" class="mso-comments-input-author">
										</p>
											
										<p><i>{{ $to_moderate }}</i></p>
											
									{% endif %}
									
								</div> <!-- class="mso-comments-auth"-->
								<!-- / простая форма-->
							{% else : %}
								<!-- обычная форма -->
								<div class="mso-comments-auth">
									
									{% if (mso_get_option('allow_comment_anonim', 'general', '1') ) : %}
									
										<p>
											<label for="comments_author">{{ tf('Ваше имя') }}</label>
											<input type="text" name="comments_author" id="comments_author" class="mso-comments-input-author">
										</p>
										<p><i>{{ $to_moderate }}</i></p>
									
									{% endif %}
								</div> <!-- class="mso-comments-auth"-->
									<!-- / обычная форма-->
							{% endif %}
							<!-- / без регистрации -->
						</div>
						<div class="mso-tabs-box">
							<!-- с регистрацией -->
							<input type="hidden" name="comments_reg" value="reg">
							{%  if (mso_get_option('form_comment_easy', 'general', '0')) : %}
								<!-- простая форма -->
								<div class="mso-comments-auth">

									{% if (mso_get_option('allow_comment_comusers', 'general', '1')) : %}
										<p>{{ $to_login }}</p>
									{% endif %}
									
								</div> <!-- class="mso-comments-auth"-->
								<!-- / простая форма-->
							{% else : %}
								<!-- обычная форма -->
								<div class="mso-comments-auth">
									
									{% if (mso_get_option('allow_comment_comusers', 'general', '1')) : %}
									
										<p>
											<a href="{{ getinfo('siteurl') }}login">{{ tf('(войти без комментирования)') }}</a>
										</p>
										
										<p>
											<label for="comments_email">{{ tf('E-mail') }}</label>
											<input type="email" name="comments_email" id="comments_email"> 
										</p>
											
										<p>
											<label for="comments_password">{{ tf('Пароль') }}</label>
											<input type="password" name="comments_password" id="comments_password">
										</p>
										
										<p>
											<label for="comments_comusers_nik">{{ tf('Ваше имя') }}</label>
											<input type="text" name="comments_comusers_nik" id="comments_comusers_nik">
										</p>
										
										{% if (mso_get_option('allow_comment_comuser_url', 'general', '0')) : %}
										<p>
											<label for="comments_comusers_url">{{ tf('Сайт') }}</label>
											<input type="url" name="comments_comusers_url" id="comments_comusers_url">
										</p>
										{% endif %}
									
									{% endif %}

								{% if ($form_comment_comuser = mso_get_option('form_comment_comuser', 'general', '')) : %} 
									<p><i>{{ $form_comment_comuser }}</i></p>
								{% endif %}
									
								</div> <!-- class="mso-comments-auth"-->
								<!-- / обычная форма-->
							{% endif %}
							<!-- / с регистрацией -->
						</div>
					</div>
				</div>
			{% endif %}
		</div><!-- div class="mso-comments-autharea" -->

		<div class="mso-comments-textarea{% if( is_login() || is_login_comuser() ): %} user{% endif %}">
			
			<div class="mso-comments-buttonarea">
				{% mso_hook('comments_content_start') %}
			</div>
			
			<p><textarea name="comments_content" id="comments_content" rows="10"></textarea></p>
			
			<div class="mso-comments-captcharea">
				{% mso_hook('comments_content_end') %}
			</div>
			
			<p><button name="comments_submit" type="submit">{{ tf('Отправить') }}</button></p>
			<script>
				$(".mso-comments-captcharea").hide(); $('button[name="comments_submit"]').click( function() { var el = $(this).parents(".mso-comments-textarea").find(".mso-comments-captcharea"); if( $(el).is(":hidden") && $(el).html().trim() != '' ){ $(el).show(); $("input:first", el).focus(); return false; }	return true; });
			</script>
		</div><!-- div class="mso-comments-textarea" -->
	</form>
</div><!-- div class=mso-comment-form -->
