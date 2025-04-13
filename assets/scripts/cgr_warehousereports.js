$(document).ready(function () {

	$("#loading").hide();

	$("#origin_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#download_criteria_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#supplier_name_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#inventory_order_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#product_name_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#product_type_inventoryreport").select2({ dropdownCssClass: "myFont" });
	$("#status_inventoryreport").select2({ dropdownCssClass: "myFont" });

	$("#download_criteria_inventoryreport").attr("disabled", "disabled");

	$("#divSupplierNameInventoryReports").hide();
	$("#divInventoryOrderInventoryReports").hide();
	$("#divStartDateInventoryReports").hide();
	$("#divEndDateInventoryReports").hide();
	$("#divProductInventoryReports").hide();
	$("#divProductTypeInventoryReports").hide();
	$("#divInputInventoryOrderInventoryReports").hide();

	$("#error-inventoryreportorigin").hide();
	$("#error-inventoryreportdownload").hide();
	$("#error-inventoryreportsupplier").hide();
	$("#error-inventoryreportinventory").hide();
	$("#error-inventoryreportstartdate").hide();
	$("#error-inventoryreportenddate").hide();
	$("#error-inventoryreportproduct").hide();
	$("#error-inventoryreportproducttype").hide();
	$("#error-inventoryreportinputinventory").hide();

	$("#origin_inventoryreport").change(function () {

		if ($("#origin_inventoryreport").val() == 0) {
			$("#download_criteria_inventoryreport").attr("disabled", "disabled");
		} else {
			$("#error-inventoryreportorigin").hide();
			$("#download_criteria_inventoryreport").removeAttr("disabled");
		}

		$("#download_criteria_inventoryreport").select2("val", "0");

		$("#divSupplierNameInventoryReports").hide();
		$("#divInventoryOrderInventoryReports").hide();
		$("#divStartDateInventoryReports").hide();
		$("#divEndDateInventoryReports").hide();
		$("#divProductInventoryReports").hide();
		$("#divProductTypeInventoryReports").hide();
		$("#divInputInventoryOrderInventoryReports").hide();

		$("#supplier_name_inventoryreport").select2("val", "0");
		$("#inventory_order_inventoryreport").select2("val", "0");
		$("#product_name_inventoryreport").select2("val", "0");
		$("#product_type_inventoryreport").select2("val", "0");
		$("#start_date_inventoryreport").val("");
		$("#end_date_inventoryreport").val("");
		$("#input_inventory_order_inventoryreport").val("");
	});

	$("#download_criteria_inventoryreport").change(function () {

		if ($("#download_criteria_inventoryreport").val() == 0) {

			$("#error-inventoryreportdownload").hide();

			$("#divSupplierNameInventoryReports").hide();
			$("#divInventoryOrderInventoryReports").hide();
			$("#divStartDateInventoryReports").hide();
			$("#divEndDateInventoryReports").hide();
			$("#divProductInventoryReports").hide();
			$("#divProductTypeInventoryReports").hide();
			$("#divInputInventoryOrderInventoryReports").hide();

			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#inventory_order_inventoryreport").select2("val", "0");
			$("#product_name_inventoryreport").select2("val", "0");
			$("#product_type_inventoryreport").select2("val", "0");
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");
			$("#input_inventory_order_inventoryreport").val("");

		} else if ($("#download_criteria_inventoryreport").val() == 1) {

			$("#error-inventoryreportdownload").hide();

			$("#divSupplierNameInventoryReports").show();
			$("#divInventoryOrderInventoryReports").show();
			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#inventory_order_inventoryreport").select2("val", "0");

			$("#divStartDateInventoryReports").hide();
			$("#divEndDateInventoryReports").hide();
			$("#divProductInventoryReports").hide();
			$("#divProductTypeInventoryReports").hide();
			$("#divInputInventoryOrderInventoryReports").hide();

			$("#product_name_inventoryreport").select2("val", "0");
			$("#product_type_inventoryreport").select2("val", "0");
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");
			$("#input_inventory_order_inventoryreport").val("");

			fetchSuppliers();

		} else if ($("#download_criteria_inventoryreport").val() == 2) {

			$("#error-inventoryreportdownload").hide();

			$("#divStartDateInventoryReports").show();
			$("#divEndDateInventoryReports").show();
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");

			$("#divSupplierNameInventoryReports").hide();
			$("#divInventoryOrderInventoryReports").hide();
			$("#divProductInventoryReports").hide();
			$("#divProductTypeInventoryReports").hide();
			$("#divInputInventoryOrderInventoryReports").hide();

			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#inventory_order_inventoryreport").select2("val", "0");
			$("#product_name_inventoryreport").select2("val", "0");
			$("#product_type_inventoryreport").select2("val", "0");
			$("#input_inventory_order_inventoryreport").val("");

		} else if ($("#download_criteria_inventoryreport").val() == 3) {

			$("#error-inventoryreportdownload").hide();

			$("#divProductInventoryReports").show();
			$("#product_name_inventoryreport").select2("val", "0");

			$("#divSupplierNameInventoryReports").hide();
			$("#divInventoryOrderInventoryReports").hide();
			$("#divStartDateInventoryReports").hide();
			$("#divEndDateInventoryReports").hide();
			$("#divProductTypeInventoryReports").hide();
			$("#divInputInventoryOrderInventoryReports").hide();

			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#inventory_order_inventoryreport").select2("val", "0");
			$("#product_type_inventoryreport").select2("val", "0");
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");
			$("#input_inventory_order_inventoryreport").val("");

			fetchProductsByOrigins();

		} else if ($("#download_criteria_inventoryreport").val() == 4) {

			$("#error-inventoryreportdownload").hide();

			$("#divProductTypeInventoryReports").show();
			$("#product_type_inventoryreport").select2("val", "0");

			$("#divSupplierNameInventoryReports").hide();
			$("#divInventoryOrderInventoryReports").hide();
			$("#divStartDateInventoryReports").hide();
			$("#divEndDateInventoryReports").hide();
			$("#divProductInventoryReports").hide();
			$("#divInputInventoryOrderInventoryReports").hide();

			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#inventory_order_inventoryreport").select2("val", "0");
			$("#product_name_inventoryreport").select2("val", "0");
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");
			$("#input_inventory_order_inventoryreport").val("");

			fetchProductTypes();

		} else if ($("#download_criteria_inventoryreport").val() == 5) {

			$("#error-inventoryreportdownload").hide();

			$("#divInputInventoryOrderInventoryReports").show();
			$("#input_inventory_order_inventoryreport").val("");

			$("#divInventoryOrderInventoryReports").hide();
			$("#divProductTypeInventoryReports").hide();
			$("#divSupplierNameInventoryReports").hide();
			$("#divStartDateInventoryReports").hide();
			$("#divEndDateInventoryReports").hide();
			$("#divProductInventoryReports").hide();

			$("#inventory_order_inventoryreport").select2("val", "0");
			$("#supplier_name_inventoryreport").select2("val", "0");
			$("#product_name_inventoryreport").select2("val", "0");
			$("#start_date_inventoryreport").val("");
			$("#end_date_inventoryreport").val("");
			$("#product_type_inventoryreport").select2("val", "0");
		}
	});

	$("#supplier_name_inventoryreport").change(function () {
		$("#error-inventoryreportsupplier").hide();
		fetchSupplierInventory();
	});

	$("#btn_download_inventory_report").click(function() {

		var selectedOrigin = $("#origin_inventoryreport").val();
		var downloadCriteria = $("#download_criteria_inventoryreport").val();
		var supplierId = $("#supplier_name_inventoryreport").val();
		var inventoryOrder = $("#inventory_order_inventoryreport").val();
		var productId = $("#product_name_inventoryreport").val();
		var productTypeId = $("#product_type_inventoryreport").val();
		var reportStartDate = $("#start_date_inventoryreport").val();
		var reportEndDate = $("#end_date_inventoryreport").val();
		var inputInventoryOrder = $("#input_inventory_order_inventoryreport").val();
		var reportStatus = $("#status_inventoryreport").val();

		var isValid1 = true,
			isValid2 = true,
			isValid3 = true,
			isValid4 = true;

		if (selectedOrigin == 0) {
			isValid1 = false;
			$("#error-inventoryreportorigin").show();
		} else {
			isValid1 = true;
			$("#error-inventoryreportorigin").hide();
		}

		if (downloadCriteria == 0) {
			isValid2 = false;
			$("#error-inventoryreportdownload").show();
		} else {
			isValid2 = true;
			$("#error-inventoryreportdownload").hide();
		}

		if (isValid2) {

			if (downloadCriteria == 1) {

				if (supplierId == 0) {
					isValid3 = false;
					$("#error-inventoryreportsupplier").show();
				} else {
					isValid3 = true;
					$("#error-inventoryreportsupplier").hide();
				}
			} else if (downloadCriteria == 2) {

				if (reportStartDate.length == 0) {
					isValid3 = false;
					$("#error-inventoryreportstartdate").show();
				} else {
					isValid3 = true;
					$("#error-inventoryreportstartdate").hide();
				}

				if (reportEndDate.length == 0) {
					isValid4 = false;
					$("#error-inventoryreportenddate").show();
				} else {
					isValid4 = true;
					$("#error-inventoryreportenddate").hide();
				}
			} else if (downloadCriteria == 3) {

				if (productId == 0) {
					isValid3 = false;
					$("#error-inventoryreportproduct").show();
				} else {
					isValid3 = true;
					$("#error-inventoryreportproduct").hide();
				}
			} else if (downloadCriteria == 4) {

				if (productTypeId == 0) {
					isValid3 = false;
					$("#error-inventoryreportproducttype").show();
				} else {
					isValid3 = true;
					$("#error-inventoryreportproducttype").hide();
				}
			} else if (downloadCriteria == 5) {

				if (inputInventoryOrder.length == 0) {
					isValid3 = false;
					$("#error-inventoryreportinputinventory").show();
				} else {
					isValid3 = true;
					$("#error-inventoryreportinputinventory").hide();
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
				fd.append("reportStatus", reportStatus);
				fd.append("csrf_cgrerp", $("#hdnCsrf").val());

				toastr.clear();
				toastr.info(processing_request);
				$("#loading").show();
				$.ajax({
					url: base_url + "/generate_warehouse_reports",
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
							//wait(3000);
							//deletefilesfromfolder();
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
		url: base_url + "/get_supplier_by_origin?originid=" + $("#origin_inventoryreport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#supplier_name_inventoryreport").empty();
				$("#supplier_name_inventoryreport").append(JSON.result);
			}
		}
	});
}

function fetchSupplierInventory() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_warehouse_inventory_by_supplier?originid=" + $("#origin_inventoryreport").val() + "&supplierid=" + $("#supplier_name_inventoryreport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#inventory_order_inventoryreport").empty();
				$("#inventory_order_inventoryreport").append(JSON.result);
			}
		}
	});
}

function fetchProductsByOrigins() {
	$("#loading").show();
	$.ajax({
		url: base_url + "/get_products_by_origin?originid=" + $("#origin_inventoryreport").val(),
		cache: false,
		method: "GET",
		dataType: "json",
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.result != '') {
				$("#product_name_inventoryreport").empty();
				$("#product_name_inventoryreport").append(JSON.result);
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
				$("#product_type_inventoryreport").empty();
				$("#product_type_inventoryreport").append(JSON.result);
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