$(document).ready(function () {

	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
	$("#maintainance_machine_type").select2({ dropdownCssClass: "myFont" });

	$("#generate_report").click(function (e) {
		var fd = new FormData();
		fd.append("originId", $("#origin").val());
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("type", "generate_report");

		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_generate_summary_report",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#supplier_name").select2({
						dropdownCssClass: "myFont", minimumResultsForSearch: 0,
						dropdownParent: $("#add-modal-data-bd1")
					});
					$("#add-modal-data-bd1").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editcosting_extraction]', function () {
		var id = $(this).data('costing_id');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'type=editcosting&typeid=1&cid=' + id + '&originid=' + $("#origin").val(),
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.error != "") {
					toastr.error(response.error);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				} else if (response.result != "") {

					$("#extraction_suppliers").select2().val(response.result["supplierid"]).trigger("change");
					$("#extraction_date").val(response.result["expensedate"]);
					$("#extraction_quantity").val(response.result["treecount"]);
					$("#extraction_total_value").val(response.result["amount"]);
					$("#extraction_claim_remarks").val(response.result["remarks"]);
					$("#hdnEditId_extraction").val(response.result["costingid"]);
					$("#btnSaveCostingExtraction").text(response.result["updatetext"]);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editcosting_acpm]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'type=editcosting&typeid=4&cid=' + id + '&originid=' + $("#origin").val(),
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.error != "") {
					toastr.error(response.error);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				} else if (response.result != "") {

					$("#acpm_suppliers").select2().val(response.result["supplierid"]).trigger("change");
					$("#acpm_purchaser").select2().val(response.result["purchaserid"]).trigger("change");
					$("#acpm_invoice_number").val(response.result["invoiceno"]);
					$("#acpm_date").val(response.result["expensedate"]);
					$("#acpm_quantity").val(response.result["quantity"]);
					$("#acpm_claim_remarks").val(response.result["remarks"]);

					if (response.result["expensetype"] == 1) {
						$("#flexSwitchCheckChecked").prop("checked", true);
						$("#acpm_total_value").val("");
						$("#acpm_total_value").attr("disabled", true);
					} else {
						$("#flexSwitchCheckChecked").prop("checked", false);
						$("#acpm_total_value").val(response.result["amount"]);
						$("#acpm_total_value").attr("disabled", false);
					}

					$("#hdnEditId_acpm").val(response.result["costingid"]);
					$("#btnSaveCostingACPM").text(response.result["updatetext"]);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editcosting_maintenance]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'type=editcosting&typeid=5&cid=' + id + '&originid=' + $("#origin").val(),
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.error != "") {
					toastr.error(response.error);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				} else if (response.result != "") {

					$("#maintainance_suppliers").select2().val(response.result["supplierid"]).trigger("change");
					$("#maintainance_purchaser").select2().val(response.result["purchaserid"]).trigger("change");
					$("#maintainance_invoice_number").val(response.result["invoiceno"]);
					$("#maintainance_date").val(response.result["expensedate"]);
					$("#maintainance_machine_type").select2().val(response.result["machinetype"]).trigger("change");
					$("#maintainance_concept").val(response.result["concept"]);
					$("#maintainance_subtotal").val(response.result["subtotal"]);
					$("#maintainance_tax").val(response.result["taxamount"]);
					$("#maintainance_amount").val(response.result["amount"]);
					$("#maintainance_claim_remarks").val(response.result["remarks"]);

					$("#hdnEditId_maintenance").val(response.result["costingid"]);
					$("#btnSaveCostingMaintainance").text(response.result["updatetext"]);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editcosting_miscellaneous]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'type=editcosting&typeid=6&cid=' + id + '&originid=' + $("#origin").val(),
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else if (response.error != "") {
					toastr.error(response.error);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				} else if (response.result != "") {

					$("#miscellaneous_suppliers").select2().val(response.result["supplierid"]).trigger("change");
					$("#miscellaneous_purchaser").select2().val(response.result["purchaserid"]).trigger("change");
					$("#miscellaneous_invoice_number").val(response.result["invoiceno"]);
					$("#miscellaneous_date").val(response.result["expensedate"]);
					$("#miscellaneous_concept").val(response.result["concept"]);
					$("#miscellaneous_amount").val(response.result["amount"]);
					$("#miscellaneous_claim_remarks").val(response.result["remarks"]);

					$("#hdnEditId_miscellaneous").val(response.result["costingid"]);
					$("#btnSaveCostingMiscellaneous").text(response.result["updatetext"]);
					$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deletecosting_extraction]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'mode=modal&type=deletecostingconfirmation&cid=' + id + "&costingtype=1",
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

	$(document).on('click', 'button[data-role=deletecosting_acpm]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'mode=modal&type=deletecostingconfirmation&cid=' + id + "&costingtype=4",
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

	$(document).on('click', 'button[data-role=deletecosting_maintenance]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'mode=modal&type=deletecostingconfirmation&cid=' + id + "&costingtype=5",
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

	$(document).on('click', 'button[data-role=deletecosting_miscellaneous]', function () {
		var id = $(this).data('costing_id');
		$.ajax({
			url: base_url + "/dialog_costing_action",
			type: "GET",
			data: 'mode=modal&type=deletecostingconfirmation&cid=' + id + "&costingtype=6",
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

	$("#generate_acpm_report").click(function (e) {

		var fd = new FormData();
		fd.append("originId", $("#origin").val());
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("type", "generate_acpm");

		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_generate_acpm_report",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#supplier_name").select2({
						dropdownCssClass: "myFont", minimumResultsForSearch: 0,
						dropdownParent: $("#add-modal-data-bd1")
					});
					$("#add-modal-data-bd1").modal('show');
				}
			}
		});
	});

	$("#generate_maintainance_report").click(function (e) {

		var fd = new FormData();
		fd.append("originId", $("#origin").val());
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("type", "generate_maintainance_report");

		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_generate_maintainance_report",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#supplier_name").select2({
						dropdownCssClass: "myFont", minimumResultsForSearch: 0,
						dropdownParent: $("#add-modal-data-bd1")
					});
					$("#add-modal-data-bd1").modal('show');
				}
			}
		});
	});

	$("#generate_miscellaneous_report").click(function (e) {

		var fd = new FormData();
		fd.append("originId", $("#origin").val());
		fd.append("csrf_cgrerp", $("#hdnCsrf").val());
		fd.append("type", "generate_miscellaneous_report");

		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			type: "POST",
			url: base_url + "/dialog_generate_miscellaneous_report",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#supplier_name").select2({
						dropdownCssClass: "myFont", minimumResultsForSearch: 0,
						dropdownParent: $("#add-modal-data-bd1")
					});
					$("#add-modal-data-bd1").modal('show');
				}
			}
		});
	});
});