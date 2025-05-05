$(document).ready(function () {

	$("#loading").hide();

	// $("#origin_farmreport").select2({ dropdownCssClass: "myFont" });
	// $("#download_criteria_farmreport").select2({ dropdownCssClass: "myFont" });
	// $("#supplier_name_farmrepport").select2({ dropdownCssClass: "myFont" });
	// $("#inventory_order_farmrepport").select2({ dropdownCssClass: "myFont" });
	// $("#product_name_farmreport").select2({ dropdownCssClass: "myFont" });
	// $("#product_type_farmreport").select2({ dropdownCssClass: "myFont" });

	//$("#download_criteria_farmreport").attr("disabled", "disabled");

	$("#divSupplierNameFarmReports").hide();
	$("#divInventoryOrderFarmReports").hide();
	$("#divStartDateFarmReports").hide();
	$("#divEndDateFarmReports").hide();
	$("#divProductFarmReports").hide();
	$("#divProductTypeFarmReports").hide();
	$("#divInputInventoryOrderFarmReports").hide();

	$("#error-farmreportorigin").hide();
	$("#error-farmreportdownload").hide();
	$("#error-farmreportsupplier").hide();
	$("#error-farmreportinventory").hide();
	$("#error-farmreportstartdate").hide();
	$("#error-farmreportenddate").hide();
	$("#error-farmreportproduct").hide();
	$("#error-farmreportproducttype").hide();
	$("#error-farmreportinputinventory").hide();

	$("#origin_farmreport").change(function () {

		if ($("#origin_farmreport").val() == 0) {
			$("#download_criteria_farmreport").attr("disabled", "disabled");
		} else {
			$("#error-farmreportorigin").hide();
			$("#download_criteria_farmreport").removeAttr("disabled");
		}

		//$("#download_criteria_farmreport").select2("val", "0");

		$("#divSupplierNameFarmReports").hide();
		$("#divInventoryOrderFarmReports").hide();
		$("#divStartDateFarmReports").hide();
		$("#divEndDateFarmReports").hide();
		$("#divProductFarmReports").hide();
		$("#divProductTypeFarmReports").hide();
		$("#divInputInventoryOrderFarmReports").hide();

		// $("#supplier_name_farmrepport").select2("val", "0");
		// $("#inventory_order_farmrepport").select2("val", "0");
		// $("#product_name_farmreport").select2("val", "0");
		// $("#product_type_farmreport").select2("val", "0");
		$("#start_date_farmreport").val("");
		$("#end_date_farmreport").val("");
		$("#input_inventory_order_farmreport").val("");
	});

	$("#download_criteria_farmreport").change(function () {

		if ($("#download_criteria_farmreport").val() == 0) {

			$("#error-farmreportdownload").hide();

			$("#divSupplierNameFarmReports").hide();
			$("#divInventoryOrderFarmReports").hide();
			$("#divStartDateFarmReports").hide();
			$("#divEndDateFarmReports").hide();
			$("#divProductFarmReports").hide();
			$("#divProductTypeFarmReports").hide();
			$("#divInputInventoryOrderFarmReports").hide();

			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#inventory_order_farmrepport").select2("val", "0");
			// $("#product_name_farmreport").select2("val", "0");
			// $("#product_type_farmreport").select2("val", "0");
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");
			$("#input_inventory_order_farmreport").val("");

		} else if ($("#download_criteria_farmreport").val() == 1) {

			$("#error-farmreportdownload").hide();

			$("#divSupplierNameFarmReports").show();
			$("#divInventoryOrderFarmReports").show();
			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#inventory_order_farmrepport").select2("val", "0");

			$("#divStartDateFarmReports").hide();
			$("#divEndDateFarmReports").hide();
			$("#divProductFarmReports").hide();
			$("#divProductTypeFarmReports").hide();
			$("#divInputInventoryOrderFarmReports").hide();

			// $("#product_name_farmreport").select2("val", "0");
			// $("#product_type_farmreport").select2("val", "0");
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");
			$("#input_inventory_order_farmreport").val("");

			fetchSuppliers();

		} else if ($("#download_criteria_farmreport").val() == 2) {

			$("#error-farmreportdownload").hide();

			$("#divStartDateFarmReports").show();
			$("#divEndDateFarmReports").show();
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");

			$("#divSupplierNameFarmReports").hide();
			$("#divInventoryOrderFarmReports").hide();
			$("#divProductFarmReports").hide();
			$("#divProductTypeFarmReports").hide();
			$("#divInputInventoryOrderFarmReports").hide();

			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#inventory_order_farmrepport").select2("val", "0");
			// $("#product_name_farmreport").select2("val", "0");
			// $("#product_type_farmreport").select2("val", "0");
			$("#input_inventory_order_farmreport").val("");

		} else if ($("#download_criteria_farmreport").val() == 3) {

			$("#error-farmreportdownload").hide();

			$("#divProductFarmReports").show();
			//$("#product_name_farmreport").select2("val", "0");

			$("#divSupplierNameFarmReports").hide();
			$("#divInventoryOrderFarmReports").hide();
			$("#divStartDateFarmReports").hide();
			$("#divEndDateFarmReports").hide();
			$("#divProductTypeFarmReports").hide();
			$("#divInputInventoryOrderFarmReports").hide();

			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#inventory_order_farmrepport").select2("val", "0");
			// $("#product_type_farmreport").select2("val", "0");
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");
			$("#input_inventory_order_farmreport").val("");

			fetchProductsByOrigins();

		} else if ($("#download_criteria_farmreport").val() == 4) {

			$("#error-farmreportdownload").hide();

			$("#divProductTypeFarmReports").show();
			//$("#product_type_farmreport").select2("val", "0");

			$("#divSupplierNameFarmReports").hide();
			$("#divInventoryOrderFarmReports").hide();
			$("#divStartDateFarmReports").hide();
			$("#divEndDateFarmReports").hide();
			$("#divProductFarmReports").hide();
			$("#divInputInventoryOrderFarmReports").hide();

			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#inventory_order_farmrepport").select2("val", "0");
			// $("#product_name_farmreport").select2("val", "0");
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");
			$("#input_inventory_order_farmreport").val("");

			fetchProductTypes();

		} else if ($("#download_criteria_farmreport").val() == 5) {

			$("#error-farmreportdownload").hide();

			$("#divInputInventoryOrderFarmReports").show();
			$("#input_inventory_order_farmreport").val("");

			$("#divInventoryOrderFarmReports").hide();
			$("#divProductTypeFarmReports").hide();
			$("#divSupplierNameFarmReports").hide();
			$("#divStartDateFarmReports").hide();
			$("#divEndDateFarmReports").hide();
			$("#divProductFarmReports").hide();

			// $("#inventory_order_farmrepport").select2("val", "0");
			// $("#supplier_name_farmrepport").select2("val", "0");
			// $("#product_name_farmreport").select2("val", "0");
			$("#start_date_farmreport").val("");
			$("#end_date_farmreport").val("");
			//$("#product_type_farmreport").select2("val", "0");
		}
	});

	$("#supplier_name_farmrepport").change(function () {
		$("#error-farmreportsupplier").hide();
		fetchSupplierInventory();
	});

	$("#btn_download_farm_report").click(function() {

		var selectedOrigin = $("#origin_farmreport").val();
		var downloadCriteria = $("#download_criteria_farmreport").val();
		var supplierId = $("#supplier_name_farmrepport").val();
		var inventoryOrder = $("#inventory_order_farmrepport").val();
		var productId = $("#product_name_farmreport").val();
		var productTypeId = $("#product_type_farmreport").val();
		var reportStartDate = $("#start_date_farmreport").val();
		var reportEndDate = $("#end_date_farmreport").val();
		var inputInventoryOrder = $("#input_inventory_order_farmreport").val();

		var isValid1 = true,
			isValid2 = true,
			isValid3 = true,
			isValid4 = true;

		if (selectedOrigin == 0) {
			isValid1 = false;
			$("#error-farmreportorigin").show();
		} else {
			isValid1 = true;
			$("#error-farmreportorigin").hide();
		}

		if (downloadCriteria == 0) {
			isValid2 = false;
			$("#error-farmreportdownload").show();
		} else {
			isValid2 = true;
			$("#error-farmreportdownload").hide();
		}

		if (isValid2) {

			if (downloadCriteria == 1) {

				if (supplierId == 0) {
					isValid3 = false;
					$("#error-farmreportsupplier").show();
				} else {
					isValid3 = true;
					$("#error-farmreportsupplier").hide();
				}
			} else if (downloadCriteria == 2) {

				if (reportStartDate.length == 0) {
					isValid3 = false;
					$("#error-farmreportstartdate").show();
				} else {
					isValid3 = true;
					$("#error-farmreportstartdate").hide();
				}

				if (reportEndDate.length == 0) {
					isValid4 = false;
					$("#error-farmreportenddate").show();
				} else {
					isValid4 = true;
					$("#error-farmreportenddate").hide();
				}
			} else if (downloadCriteria == 3) {

				if (productId == 0) {
					isValid3 = false;
					$("#error-farmreportproduct").show();
				} else {
					isValid3 = true;
					$("#error-farmreportproduct").hide();
				}
			} else if (downloadCriteria == 4) {

				if (productTypeId == 0) {
					isValid3 = false;
					$("#error-farmreportproducttype").show();
				} else {
					isValid3 = true;
					$("#error-farmreportproducttype").hide();
				}
			} else if (downloadCriteria == 5) {

				if (inputInventoryOrder.length == 0) {
					isValid3 = false;
					$("#error-farmreportinputinventory").show();
				} else {
					isValid3 = true;
					$("#error-farmreportinputinventory").hide();
				}
			}

			if (isValid1 && isValid2 && isValid3 && isValid4) {

				var fd = new FormData();
				fd.append("originId", selectedOrigin);
				fd.append("downloadCriteria", downloadCriteria);
				fd.append("supplierId", supplierId);
				fd.append("inventoryOrder", inventoryOrder);
				fd.append("productId", productId);
				fd.append("productTypeId", productTypeId);
				fd.append("reportStartDate", reportStartDate);
				fd.append("reportEndDate", reportEndDate);
				fd.append("inputInventoryOrder", inputInventoryOrder);
				fd.append("csrf_cgrerp", $("#hdnCsrf").val());

				toastr.clear();
				toastr.info(processing_request);
				$("#loading").show();
				$.ajax({
					url: base_url + "/generate_farm_reports",
					type: "POST",
					data: fd,
					contentType: false,
					processData: false,
					success: function(response) {
						$("#loading").hide();
						if (response.redirect == true) {
							window.location.replace(login_url);
						} else if (response.error != '') {
							toastr.clear();
							toastr.error(response.error);
							$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(response.successmessage);
							$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
							window.location = response.result;
							wait(3000);
							deletefilesfromfolder();
						}
					}
				});
			}
		}
	});
});

function fetchSuppliers() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_supplier_by_origin?originid=" + $("#origin_farmreport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#supplier_name_farmrepport").empty();
				$("#supplier_name_farmrepport").append(JSON.result);
			}
		}
	});
}

function fetchSupplierInventory() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_farm_inventory_by_supplier?originid=" + $("#origin_farmreport").val() + "&supplierid=" + $("#supplier_name_farmrepport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#inventory_order_farmrepport").empty();
				$("#inventory_order_farmrepport").append(JSON.result);
			}
		}
	});
}

function fetchProductsByOrigins() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_products_by_origin?originid=" + $("#origin_farmreport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#product_name_farmreport").empty();
				$("#product_name_farmreport").append(JSON.result);
			}
		}
	});
}

function fetchProductTypes() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_product_type",
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#product_type_farmreport").empty();
				$("#product_type_farmreport").append(JSON.result);
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