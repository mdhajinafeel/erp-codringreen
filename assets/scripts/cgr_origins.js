$(document).ready(function () {

	$("#loading").hide();
	
	$('#xin_table').DataTable({
		"bDestroy": true,
		"ajax": {
			url: base_url + "/origin_list",
			type: 'GET'
		},
		paging: false,
		dom: '<f>t',
		searching: false,
		fixedColumns: true,
		responsive: true,
		"order": [[1, "asc"]],"language": {
			"url": datatable_language
		}
	});

	$('#btn_new').click(function () {
		$.ajax({
			url: base_url + "/dialog_origin_add/",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&data=origin',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#origin_id").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});
});
