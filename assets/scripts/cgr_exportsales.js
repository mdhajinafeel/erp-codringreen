$(document).ready(function () {

	$("#loading").hide();

	fetch_export_order();

	$("#generate_report").click(function () {

		$("#loading").show();
		$.ajax({
			url: base_url + "/generate_missing_inventory_order_report?originid=" + $("#origin").val(),
			type: "GET",
			data: "jd=1&is_ajax=3",
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

	//EXPORT ORDER
	$("#origin_export_order").select2({ dropdownCssClass: "myFont" });
	//$("#shipping_line").select2({ dropdownCssClass: "myFont" });
	//$("#product_type").select2({ dropdownCssClass: "myFont" });

	$("#origin_export_order").change(function () {
		fetch_export_order();
	});

	$("#shipping_line").change(function () {
		fetch_export_order();
	});

	$("#product_type").change(function () {
		fetch_export_order();
	});

	$(document).on('click', 'button[data-role=viewexportorder]', function () {

		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");

		$("#loading").show();
		var fd = new FormData();
		fd.append("type", "viewexport");
		fd.append("export_order_id", export_id);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("sa_number", sa_number);
		fd.append("origin_id", $("#origin_export_order").val());
		toastr.info(processing_request);
		$.ajax({
			url: base_url + "/dialog_export_order_option",
			type: "POST",
			data: fd,
			contentType: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.messagetype == "info") {
					toastr.clear();
					toastr.error(response.pagemessage);
					$("#titlehead").text(response.pageheading);
					$("#infomessage").text(response.pagemessage);
					$("#alert-dialog-info").modal('show');
				} else {
					toastr.clear();
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
					//$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
				}
			}
		});
	});
	
	$(document).on('click', 'button[data-role=proformainvoice]', function () {

		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");

		$("#loading").show();
		var fd = new FormData();
		fd.append("type", "generateinvoice");
		fd.append("export_order_id", export_id);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("sa_number", sa_number);
		fd.append("origin_id", $("#origin_export_order").val());
		toastr.info(processing_request);
		$.ajax({
			url: base_url + "/dialog_proforma_invoice_option",
			type: "POST",
			data: fd,
			contentType: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.messagetype == "info") {
					toastr.clear();
					toastr.error(response.pagemessage);
					$("#titlehead").text(response.pageheading);
					$("#infomessage").text(response.pagemessage);
					$("#alert-dialog-info").modal('show');
				} else {
					toastr.clear();
					$("#ajax_modal_xl").html(response);
					//$("#buyer_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
					$("#add-modal-data-xl").modal('show');
					
				}
			}
		});
	});
	
	$(document).on('click', 'button[data-role=invoicehistory]', function () {

		var export_id = $(this).data("export_id");
		var sa_number = $(this).data("sa_number");

		$("#loading").show();
		var fd = new FormData();
		fd.append("type", "invoicehistory");
		fd.append("export_order_id", export_id);
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("sa_number", sa_number);
		fd.append("origin_id", $("#origin_export_order").val());
		toastr.info(processing_request);
		$.ajax({
			url: base_url + "/dialog_invoice_history_option",
			type: "POST",
			data: fd,
			contentType: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.messagetype == "info") {
					toastr.clear();
					toastr.error(response.pagemessage);
					$("#titlehead").text(response.pageheading);
					$("#infomessage").text(response.pagemessage);
					$("#alert-dialog-info").modal('show');
				} else {
					toastr.clear();
					$("#ajax_modal_xl").html(response);
					$("#add-modal-data-xl").modal('show');
					
				}
			}
		});
	});
});

function fetch_export_order() {
	$("#xin_table_export_order").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/export_order_list?originid=" + $("#origin_export_order").val() + "&sid=" + $("#shipping_line").val() + "&tid=" + $("#product_type").val(),
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