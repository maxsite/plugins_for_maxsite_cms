<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* TODO можно ли юзера с тем же именем, что комьюзера. Что насчёт активации юзера и его полномочий...
*/
	require(getinfo('template_dir') . 'main-start.php');

	if ( is_login() or (mso_segment(1) != $options['slug_users']) ) $options['reg_users'] = 0;
	if ( is_login_comuser() or (mso_segment(1) != $options['slug_comusers']) ) $options['reg_comusers'] = 0;

	if ( $post = mso_check_post(array('comusers_session', 'comusers_submit', 'comusers_nik', 'comusers_email', 'comusers_password')) and ($options['reg_comusers']) )
	{
		if ( $options['comusers_invite'] and mso_segment(2) != mso_md5($post['comusers_email'].$options['slug_comusers']) )
		{
			echo '<h1>Ваш e-mail не соответствует коду приглашения.</h1>';
		}
		else
		{
			mso_checkreferer();
			require('do_register.php');
			echo do_register($post, 'comusers', array());
		}
	}
	elseif ( $post = mso_check_post(array('users_session', 'users_submit', 'users_login', 'users_nik', 'users_email', 'users_password')) and ($options['reg_users']) )
	{
		if ( $options['users_invite'] and mso_segment(2) != mso_md5($post['users_email'].$options['slug_users']) )
		{
			echo '<h1>Ваш e-mail не соответствует коду приглашения.</h1>';
		}
		else
		{
			mso_checkreferer();
			require('do_register.php');
			echo do_register($post, 'users', array());
		}
	}
	else
	{
		if ($options['reg_comusers'])
		{
?>

<div class="comment-form">
	<form action="" method="post">
		<input type="hidden" name="comments_page_id" value="register">
		<?= mso_form_session('comusers_session') ?>
		<div class="comments-reg">
			<div class="black"><?= $options['comusers_legend'] ?></div>
			<p>
			<input type="text" name="comusers_nik" value="" class="text">
			<label for="comusers_nik"><?= t('Имя') ?></label>
			</p>
			<p>
			<input type="text" name="comusers_email" value="" class="text">
			<label for="comusers_email"><?= t('E-mail') ?></label>
			</p>
			 <p>
			<input type="password" name="comusers_password" id="comusers_password" value="" class="text">
			<label for="comusers_password"><?= t('Пароль') ?></label>
			</p>
		</div>
		<?php mso_hook('comments_content_end'); ?>
		<div><input name="comusers_submit" type="submit" value="<?=t('Отправить')?>" class="comments_submit"></div>
	</form>
</div><!-- div class=comment-form -->

<?php
		}
		if ($options['reg_users'])
		{
?>

<div class="comment-form">
	<form action="" method="post">
		<?= mso_form_session('users_session') ?>
		<div class="comments-reg">
			<div class="black"><?= $options['users_legend'] ?></div>
			<p>
			<input type="text" name="users_login" value="" class="text">
			<label for="users_login"><?= t('Логин') ?></label>
			</p>
			<p>
			<input type="text" name="users_nik" value="" class="text">
			<label for="users_nik"><?= t('Имя') ?></label>
			</p>
			<p>
			<input type="text" name="users_email" value="" class="text">
			<label for="users_email"><?= t('E-mail') ?></label>
			</p>
			 <p>
			<input type="password" name="users_password" id="users_password" value="" class="text">
			<label for="users_password"><?= t('Пароль') ?></label>
			</p>
		</div>
		<?php mso_hook('comments_content_end'); ?>
		<div><input name="users_submit" type="submit" value="<?=t('Отправить')?>" class="comments_submit"></div>
	</form>
</div><!-- div class=comment-form -->

<?php
		}
	}
	require(getinfo('template_dir') . 'main-end.php');
