$(document).ready(function () {
	
	$("#loading").hide();

	//SUPPLIERS
	$('#xin_table_suppliers').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/supplier_list?originid=" + $("#origin_supplier").val(),
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

	$('#btn_new_supplier').click(function () {
		$.ajax({
			url: base_url + "/dialog_supplier_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addsupplier',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=view]', function () {
		var id = $(this).data('supplier_id');
		$.ajax({
			url: base_url + "/dialog_supplier_view",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewsupplier&sid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editsupplier]', function () {
		var id = $(this).data('supplier_id');
		$.ajax({
			url: base_url + "/dialog_supplier_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editsupplier&sid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
				}
			}
		});
	});

	$("#origin_supplier").change(function () {
		$('#xin_table_suppliers').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/supplier_list?originid=" + $("#origin_supplier").val(),
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

	$("#generate_report").click(function (e) {
		e.preventDefault();
		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			url: base_url + "/generate_supplier_report",
			type: "GET",
			data: 'oid=' + $("#origin_supplier").val(),
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.error != '') {
					toastr.error(response.error);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				} else {
					toastr.success(response.successmessage);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
					window.location = response.result;
				}
			}
		});
	});
});