$(document).ready(function () {

	
	$("#loading").hide();

	$("#error-username").hide();
	$("#error-password").hide();

	$("#ttkerp-form").submit(function (e) {
		e.preventDefault();
		var username = $("#login-username").val().trim();
		var password = $("#login-password").val().trim();
		var isValid = true;

		if (username.length == 0) {
			$("#error-username").show();
			isValid = false;
		} else {
			$("#error-username").hide();
			isValid = true;
		}

		if (password.length == 0) {
			$("#error-password").show();
			isValid = false;
		} else {
			$("#error-password").hide();
			isValid = true;
		}

		if (isValid) {

			$("#loading").show();

			$('.login').prop('disabled', true);
			toastr.info(processing_request);
			var obj = $(this), action = obj.attr('name'), redirect_url = obj.data('redirect'), form_table = obj.data('form-table'), is_redirect = obj.data('is-redirect');
			$.ajax({
				type: "POST",
				url: e.target.action,
				data: obj.serialize() + "&is_ajax=1&form=" + form_table,
				cache: false,
				success: function (JSON) {
					$("#loading").hide();
					if (JSON.error != '') {
						toastr.clear();
						toastr.error(JSON.error);
						$('.login').prop('disabled', false);
						$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
					} else {
						toastr.clear();
						toastr.success(JSON.result);
						$('.login').prop('disabled', false);
						$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						if (is_redirect == 1) {
							window.location = site_url + 'dashboard?module=dashboard';
						}
					}
				}
			});
		}
	});
});
