$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });

	$('#xin_table_farms').DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/farm_list?originid=" + $("#origin").val(),
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
		"columnDefs": [
			 {
				"searchable": true,
				"orderable": true,
				"targets": 5,
				"type": 'date'
			 }
		],
		"order": [
			[0, "asc"]
		], "language": {
			"url": datatable_language
		}
	});

	$("#origin").change(function () {
		$('#xin_table_farms').DataTable({
			"bDestroy": true,
			"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
			"ajax": {
				url: base_url + "/farm_list?originid=" + $("#origin").val(),
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
			"columnDefs": [
				{
				   "searchable": true,
				   "orderable": true,
				   "targets": 5,
				   "type": 'date'
				}
		   ],
			"order": [
				[0, "asc"]
			], "language": {
				"url": datatable_language
			}
		});
	});

	$('#btn_new_farm').click(function () {
		$.ajax({
			url: base_url + "/dialog_farm_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addfarm',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_farms").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin_farms").attr("disabled", false);
					$("#supplier_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#purchase_contract").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#service_payto").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#logistic_payto").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#wh_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#wh_name_roundlogs").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#measurement_system_roundlogs").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=downloadfarm]', function () {
		var id = $(this).data('farm_id');
		var contract_id = $(this).data('contract_id');
		var inventory_order = $(this).data('inventory_order');
		var created_from = $(this).data('created_from');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_farm_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=downloadfarm&fid=' + id + '&cid=' + contract_id + '&io=' + inventory_order + '&cf=' + created_from,
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
					wait(3000);
					deletefilesfromfolder();
				}
			}
		});
	});
	
	$(document).on('click', 'button[data-role=downloadreceipt]', function () {
		var id = $(this).data('farm_id');
		var contract_id = $(this).data('contract_id');
		var inventory_order = $(this).data('inventory_order');
		var created_from = $(this).data('created_from');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_farm_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=downloadreceipt&fid=' + id + '&cid=' + contract_id + '&io=' + inventory_order + '&cf=' + created_from,
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
					wait(3000);
					deletefilesfromfolder();
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=viewfarm]', function () {
		var id = $(this).data('farm_id');
		var contract_id = $(this).data('contract_id');
		var inventory_order = $(this).data('inventory_order');
		$.ajax({
			url: base_url + "/dialog_farm_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewfarm&fid=' + id + '&cid=' + contract_id + '&io=' + inventory_order,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#service_payto").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#logistic_payto").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deletefarm]', function () {
		var id = $(this).data('farm_id');
		var contract_id = $(this).data('contract_id');
		var inventory_order = $(this).data('inventory_order');
		$.ajax({
			url: base_url + "/dialog_farm_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=deletefarmconfirmation&fid=' + id + '&cid=' + contract_id + '&io=' + inventory_order,
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
});

function deletefilesfromfolder() {
	$.ajax({
		type: "GET",
		url: base_url + "/deletefilesfromfolder",
		contentType: false,
		cache: false,
		processData: false,
		success: function (JSON) {
			$("#loading").hide();
		}
	});
}

function wait(ms) {
	var start = new Date().getTime();
	var end = start;
	while (end < start + ms) {
		end = new Date().getTime();
	}
}