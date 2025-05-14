$(document).ready(function () {
	
	$("#loading").hide();

	$('#xin_table_pod').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/pod_list",
			type: 'GET'
		},
		//dom: 'lBfrtip',
		"sScrollX": "100%",
		"scrollCollapse": true,
		"bPaginate": true,
		"sPaginationType": "full_numbers",
		paging: true,
		searching: true,
		fixedColumns: true,
		responsive: true,
		"order": [
			[0, "asc"]
		], "language": {
			"url": datatable_language
		}
	});

	$('#btn_new_exportpod').click(function () {
		$.ajax({
			url: base_url + "/dialog_exportpod_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addexportpod',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editpod]', function () {
		var id = $(this).data('pod_id');
		$.ajax({
			url: base_url + "/dialog_exportpod_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editexportpod&pid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
				}
			}
		});
	});
});