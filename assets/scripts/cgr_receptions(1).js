$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });

	fetch_receptions();

	$("#origin").change(function () {
		fetch_receptions();
	});

	$('#btn_new_reception').click(function () {
		$.ajax({
			url: base_url + "/dialog_reception_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addreception',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_reception").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin_reception").attr("disabled", false);
					$("#supplier_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#product_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#wh_name_add").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#upload_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#divReceptionUpload *").attr("disabled", "disabled").off('click');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=viewreception]', function () {
		var reception_id = $(this).data('reception_id');
		var inventory_order = $(this).data('inventory_order');
		$.ajax({
			url: base_url + "/dialog_reception_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewreception&rid=' + reception_id + '&io=' + inventory_order,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
					$("#supplier_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
					$("#wh_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deletereception]', function () {
		var reception_id = $(this).data('reception_id');
		var inventory_order = $(this).data('inventory_order');
		$.ajax({
			url: base_url + "/dialog_reception_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=deletereceptionconfirmation&rid=' + reception_id + '&io=' + inventory_order,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=downloadreception]', function () {
		var reception_id = $(this).data('reception_id');
		var inventory_order = $(this).data('inventory_order');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_reception_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=downloadreception&rid=' + reception_id + '&io=' + inventory_order,
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
});

function fetch_receptions() {
	$("#xin_table_receptions").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/reception_list?originid=" + $("#origin").val(),
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
				"targets": 4,
				"type": 'date'
			 }
		],
		"order": [
			[0, "asc"]
		], "language": {
			"url": datatable_language
		}
	});
}

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