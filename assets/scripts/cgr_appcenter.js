$(document).ready(function () {

	$("#btn_add_apps").click(function () {
        $.ajax({
			url: BASE_URL_SUBFOLDER + "appcenter/applists/dialog_appcenter_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=add_appcenter',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
    });
});