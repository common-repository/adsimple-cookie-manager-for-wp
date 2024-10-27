(function ($, document) {
	$(document).ready(function () {
		/* START NOTICES */
		var notices = $('.js-as_cm-notice');
		if (notices.length)
			notices.each(function () {
				$(this).insertAfter($('#wpbody .wrap ' + ($('#wpbody .wrap hr.wp-header-end').length ? 'hr.wp-header-end' : 'h1')));
				$(this).show();
			});

		$('.js-as_cm-notice .js-as_cm-notice-actions .js-as_cm-notice-actions-dismiss').on('click', function (e) {
			e.preventDefault();

			var button = $(this);

			$.ajax({
					   url: $(this).data('dismiss-url'),
					   type: 'GET',
					   cache: false,
					   timeout: 0,
					   processData: false,
					   contentType: false,
					   success: function (data) {
						   if (data === 'success')
							   button.closest('.js-as_cm-notice').hide();
					   },
				   });
		});

		$('.js-as_cm-notice.js-as_cm-notice-ajax .js-as_cm-notice-actions .js-as_cm-notice-actions-button')
			.on('click',
				function (e) {
					e.preventDefault();

					var wrapper = $(this).closest('.js-as_cm-notice'),
						loader  = wrapper.find('.js-as_cm-notice-actions .js-as_cm-notice-actions-button'),
						confirm_text = $(this).attr('data-confirm');

					if( confirm_text === '' || confirm(confirm_text) ) {
						if (wrapper.hasClass('js-as_cm-ajax-go'))
							return;

						$.ajax({
								   url: $(
									   this).attr(
									   'href'),
								   type: 'GET',
								   cache: false,
								   timeout: 0,
								   processData: false,
								   contentType: false,
								   success: function (data) {
									   data = JSON.parse(
										   data);

									   if (data.status === 'success')
										   wrapper.find(
											   '.js-as_cm-notice-actions').hide();
									   else {
										   wrapper.removeClass(
											   'js-as_cm-ajax-go');
									   }

									   loader.removeClass(
										   'as_cm-notice__button-loading');

									   wrapper.find(
										   '.js-as_cm-notice-text').html('<span class="as_cm-notice__message-text as_cm-notice-text-type-' + data.status + '">' + data.message + '</span>');
								   },
								   beforeSend: function () {
									   wrapper.addClass(
										   'js-as_cm-ajax-go');
									   loader.addClass(
										   'as_cm-notice__button-loading');
								   }
							   });
					}
				}
			);
		/* END NOTICES */
	});
})(jQuery);