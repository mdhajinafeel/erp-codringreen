$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });

	$('#xin_table_contracts').DataTable({
		"bDestroy": true,
		"lengthMenu": [[30, 60, 100, -1], [30, 60, 100, "All"]],
		"ajax": {
			url: base_url + "/contract_list?originid=" + $("#origin").val(),
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

	$("#origin").change(function () {
		$('#xin_table_contracts').DataTable({
			"bDestroy": true,
			"lengthMenu": [[30, 50, 100, -1], [20, 60, 100, "All"]],
			"ajax": {
				url: base_url + "/contract_list?originid=" + $("#origin").val(),
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
			url: base_url + "/generate_contract_report",
			type: "GET",
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
					//wait(3000);
					//deletefilesfromfolder();
				}
			}
		});
	});

	$('#btn_new_contract').click(function () {
		$.ajax({
			url: base_url + "/dialog_contract_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addcontract',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_contracts").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin_contracts").attr("disabled", false);
					$("#contract_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#contract_type").attr("disabled", false);
					$("#supplier_pm_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#measuremet_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#currency").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#payment_method").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editcontract]', function () {
		var id = $(this).data('contract_id');
		$.ajax({
			url: base_url + "/dialog_contract_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editcontract&cid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_contracts").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin_contracts").attr("disabled", true);
					$("#contract_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#contract_type").attr("disabled", true);
					$("#supplier_pm_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#measuremet_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#currency").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#payment_method").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=viewcontract]', function () {
		var id = $(this).data('contract_id');
		$.ajax({
			url: base_url + "/dialog_contract_view",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewcontract&cid=' + id,
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