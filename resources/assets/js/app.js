/**
 * First, load all of our dependencies.
 */

require('./bootstrap');

$(document).foundation();

$('form').submit(function() {
	const $form = $(this);
	const $submitButton = $form.find(':submit').first();

	$submitButton.prop('disabled', true).text('One moment...');
});

//--------------------------------
// Sign in modals
//--------------------------------
$('#modalsSignIn').bind('open.zf.reveal', function() {
	$(this).find('input[name="email"]:first').focus();
});
$('#modalsForgotPassword').bind('open.zf.reveal', function() {
	$(this).find('input[name="email"]').focus();
});

//--------------------------------
// WYSIWYG editors
//--------------------------------
tinymce.init({
	selector: 'textarea.tinymce',
	plugins: [
		'code image link autoresize'
	],
});

//--------------------------------
// Tags
//--------------------------------
$('input.tags').selectize({
	delimiter: ',',
	persist: false,
	create: function (input) {
		return {
			value: input,
			text: input
		}
	}
});

$('label.is-invalid-label .selectize-input').focusin(function() {
	$(this).closest('.is-invalid-label').removeClass('is-invalid-label');
});

//--------------------------------
// Expanders
//--------------------------------
// @TODO:
// Perhaps make this so that it doesn't add the button if the .contents div is already smaller
// than 100px.
$(function() {
	const $expanders = $('.expander');

	$expanders.each(function () {
		const $expander = $(this);
		const contents = $expander.html();
		$expander.html('<div class="contents">' + contents + '</div>' +
			'<div class="expand">SHOW MORE</div>');
	});

	$expanders.on('click', '.expand', function() {
		const $expand = $(this);
		const $expander = $expand.closest('.expander');

		const text = $expand.html().trim();

		if (text == 'SHOW MORE') {
			$expander.css({'max-height': 'none'});
			$expand.html('SHOW LESS');
		}
		else {
			$expander.css({'max-height': '100px'});
			$expand.html('SHOW MORE');
		}
	});
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Vue.component('example', require('./components/Example.vue'));
// const app = new Vue({
// 	el: '#app'
// });
