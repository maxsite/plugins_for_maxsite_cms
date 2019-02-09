$(function () {
	var sideNavLinks = $(".sb-nav").find("a, button"),
		hiddenField = $("[name = 'plugin_social_buttons-plugins[buttons_on]']"),
		hiddenFieldVal = hiddenField.val().split(","),
		btnSave = $("[name = 'f_submit']"),
		listSocialActive = $("#sb-list-active"),
		lists = listSocialActive.add("#sb-list"),
		scrolling = function(scroll) {
			$("html, body")
			.stop()
			.animate({
				scrollTop: scroll
			}, 300);
		};

	$.each(hiddenFieldVal, function () {
		$("#" + this).appendTo(listSocialActive);
	});

	lists.sortable({
		connectWith: ".connectedSortable",
		opacity: 1,
		tolerance: "pointer",
		placeholder: "ui-state-highlight",
		forcePlaceholderSize: true,
		update: function (event, ui) {
			hiddenField.val(listSocialActive.sortable("toArray"));
		}
	});

	sideNavLinks.click(function(e) {
		if (this.id === "sb-plugin-nav_top") {
			scrolling(0);
		} else if (this.id === "sb-nav-btn") {
			btnSave.trigger(e.type);
		} else {
			scrolling($(this.hash).offset().top);
		}

		e.preventDefault();
	});
});