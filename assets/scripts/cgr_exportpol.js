$(document).ready(function () {
	
	$("#loading").hide();

	$('#xin_table_pol').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/pol_list?originid=" + $("#origin_pol").val(),
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

	$('#btn_new_exportpol').click(function () {
		$.ajax({
			url: base_url + "/dialog_exportpol_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addexportpol',
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

	$(document).on('click', 'button[data-role=editpol]', function () {
		var id = $(this).data('pol_id');
		$.ajax({
			url: base_url + "/dialog_exportpol_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editexportpol&pid=' + id,
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

	$("#origin_pol").change(function () {
		$('#xin_table_pol').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/pol_list?originid=" + $("#origin_pol").val(),
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
	});
});