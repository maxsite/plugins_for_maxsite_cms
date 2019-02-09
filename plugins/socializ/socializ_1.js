function socializ(u,t) {
	document.write('<div id="socializ"></div>');
	var s = $('#socializ');
	s.css({top: m1});
	function margin() {
		var top = $(window).scrollTop();
		if (top+m2 < m1) {
			s.css({top: m1-top});
		} else {
			s.css({top: m2});
		}
	}
	$(window).scroll(function() { margin(); })

	s.append(
		'<a href="http://twitter.com/home?status=RT @Dimox_ru ' + t + ' - ' + u + '" title="Добавить в Twitter"><img src="' + f + 'twitter.png" alt="" /></a>' +
		'<a href="http://www.google.com/reader/link?url=' + u + '&title=' + t + '&srcURL=http://dimox.name/" title="Добавить в Google Buzz"><img src="' + f + 'google-buzz.png" alt="" /></a>' +
		'<a href="http://www.friendfeed.com/share?title=' + t + ' - ' + u + '" title="Добавить в FriendFeed"><img src="' + f + 'friendfeed.png" alt="" /></a>' +
		'<a href="http://www.facebook.com/sharer.php?u=' + u + '" title="Поделиться в Facebook"><img src="' + f + 'facebook.png" alt="" /></a>' +
		'<a href="http://vkontakte.ru/share.php?url=' + u + '" title="Поделиться ВКонтакте"><img src="' + f + 'vkontakte.png" alt="" /></a>' +
		'<a href="http://connect.mail.ru/share?share_url=' + u + '" title="Поделиться в Моем Мире"><img src="' + f + 'moy-mir.png" alt="" /></a>' +
		'<a href="http://www.livejournal.com/update.bml?event=' + u + '&subject=' + t + '" title="Опубликовать в своем блоге livejournal.com"><img src="' + f + 'livejournal.png" alt="" /></a>' +
		'<a href="http://delicious.com/save?url=' + u + '&title=' + t + '" title="Сохранить закладку в Delicious"><img src="' + f + 'delicious.png" alt="" /></a>' +
		'<a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=' + u + '&title=' + t + '" title="Сохранить закладку в Google"><img src="' + f + 'google.png" alt="" /></a>' +
		'<a href="http://bobrdobr.ru/add.html?url=' + u + '&title=' + t + '" title="Забобрить"><img src="' + f + 'bobrdobr.png" alt="" /></a>' +
		'<a href="http://memori.ru/link/?sm=1&u_data[url]=' + u + '&u_data[name]=' + t + '" title="Сохранить закладку в Memori.ru"><img src="' + f + 'memori.png" alt="" /></a>' +
		'<a href="http://www.mister-wong.ru/index.php?action=addurl&bm_url=' + u + '&bm_description=' + t + '" title="Сохранить закладку в Мистер Вонг"><img src="' + f + 'mister-wong.png" alt="" /></a>' +
	'');

	s.find('a').attr({target: '_blank'}).css({opacity: 0.5}).hover(
		function() { $(this).css({opacity: 1}); },
		function() { $(this).css({opacity: 0.7}); }
	);
	s.hover(
		function() { $(this).find('a').css({opacity: 0.7}); },
		function() { $(this).find('a').css({opacity: 0.5}); }
	);

}