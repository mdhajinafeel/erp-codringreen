$(document).ready(function () {

	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
	//$("#shipping_line").select2({ dropdownCssClass: "myFont" });
	//$("#product_type").select2({ dropdownCssClass: "myFont" });

	fetch_exports();

	$("#origin").change(function () {
		fetch_exports();
		fetch_shipping_lines();
	});

	$("#shipping_line").change(function () {
		fetch_exports();
	});

	$("#product_type").change(function () {
		fetch_exports();
	});

	$(document).on('click', 'button[data-role=viewexport]', function () {
		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");
		var fd = new FormData();
		fd.append("type", "viewexport");
		fd.append("eid", export_id);
		fd.append("sn", sa_number);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_export_action",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					//$("#port_of_discharge").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deleteexport]', function () {
		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");
		var dispatchids = $(this).data("dispatch_ids");
		var fd = new FormData();
		fd.append("type", "deleteexportconfirmation");
		fd.append("eid", export_id);
		fd.append("sn", sa_number);
		fd.append("did", dispatchids);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_export_action",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=viewexportdocuments]', function () {
		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");
		var fd = new FormData();
		fd.append("type", "viewexportdocuments");
		fd.append("eid", export_id);
		fd.append("sn", sa_number);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		$("#loading").show();
		$.ajax({
			type: "POST",	
			url: base_url + "/dialog_export_action",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					//$("#port_of_discharge").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					//$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});
});

function fetch_exports() {
	$("#xin_table_exports").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/export_list?originid=" + $("#origin").val() + "&sid=" + $("#shipping_line").val() + "&tid=" + $("#product_type").val(),
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
}

function fetch_shipping_lines() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_shipping_lines_by_origin?originid=" + $("#origin").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#shipping_line").empty();
				$("#shipping_line").append(JSON.result);
			}
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