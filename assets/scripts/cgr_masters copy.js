$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin_product").select2({ dropdownCssClass: "myFont" });
	$("#origin_supplier").select2({ dropdownCssClass: "myFont" });
	$("#origin_warehouse").select2({ dropdownCssClass: "myFont" });
	$("#origin_shipping").select2({ dropdownCssClass: "myFont" });
	$("#origin_measurement").select2({ dropdownCssClass: "myFont" });
	$("#origin_inputparameters").select2({ dropdownCssClass: "myFont" });
	$("#origin_qrcode").select2({ dropdownCssClass: "myFont" });
	$("#origin_inputparametersettings").select2({ dropdownCssClass: "myFont" });

	$("#generate_type").select2({ dropdownCssClass: "myFont" });

	//PRODUCTS
	$('#xin_table').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/product_list?originid=" + $("#origin_product").val(),
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

	$('#btn_new').click(function () {
		$.ajax({
			url: base_url + "/dialog_product_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addproduct',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=edit]', function () {
		var id = $(this).data('product_id');
		$.ajax({
			url: base_url + "/dialog_product_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editproduct&pid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$("#origin_product").change(function () {
		$('#xin_table').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/product_list?originid=" + $("#origin_product").val(),
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
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
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
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
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

	//WAREHOUSE
	$('#xin_table_warehouse').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/warehouse_list?originid=" + $("#origin_warehouse").val(),
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

	$('#btn_new_warehouse').click(function () {
		$.ajax({
			url: base_url + "/dialog_warehouse_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addwarehouse',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#port_of_loading").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#port_of_loading").attr("disabled", true);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editwarehouse]', function () {
		var id = $(this).data('warehouse_id');
		$.ajax({
			url: base_url + "/dialog_warehouse_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editwarehouse&wid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#port_of_loading").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$("#origin_warehouse").change(function () {
		$('#xin_table_warehouse').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/warehouse_list?originid=" + $("#origin_warehouse").val(),
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

	//SHIPPING LiNE
	$('#xin_table_shipping').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/shipping_list?originid=" + $("#origin_shipping").val(),
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

	$('#btn_new_shippingline').click(function () {
		$.ajax({
			url: base_url + "/dialog_shipping_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addshipping',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editshipping]', function () {
		var id = $(this).data('shipping_id');
		$.ajax({
			url: base_url + "/dialog_shipping_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editshipping&sid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$("#origin_shipping").change(function () {
		$('#xin_table_shipping').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/shipping_list?originid=" + $("#origin_shipping").val(),
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

	//MEASUREMENT SYSTEM
	$('#xin_table_measurementsystem').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/measurementsystem_list?originid=" + $("#origin_measurement").val(),
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

	$('#btn_new_measurementsystem').click(function () {
		$.ajax({
			url: base_url + "/dialog_measurementsystem_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addmeasurementsystem',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editmeasurement]', function () {
		var id = $(this).data('measurement_id');
		$.ajax({
			url: base_url + "/dialog_measurementsystem_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editmeasurement&mid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$("#origin_measurement").change(function () {
		$('#xin_table_measurementsystem').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/measurementsystem_list?originid=" + $("#origin_measurement").val(),
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

	//INPUT PARAMETERS
	$('#xin_table_inputparameter').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/inputparameters_list?originid=" + $("#origin_inputparameters").val(),
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

	$('#btn_new_inputparameter').click(function () {
		$.ajax({
			url: base_url + "/dialog_inputparameter_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addinputparameter',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#producttype").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editinputparameter]', function () {
		var id = $(this).data('input_parameter_id');
		$.ajax({
			url: base_url + "/dialog_inputparameter_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editinputparameter&ipid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#producttype").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$("#origin_inputparameters").change(function () {
		$('#xin_table_inputparameter').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/inputparameters_list?originid=" + $("#origin_inputparameters").val(),
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

	//QR CODE GENERATOR

	$('#xin_table_qrcodes').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/qrcode_list?originid=" + $("#origin_qrcode").val(),
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

	$("#divqrcode").hide();
	$("#divshowcode").hide();

	$("#generate_type").change(function () {
		if ($("#generate_type").val() == 0) {
			$("#divqrcode").hide();
			$("#error-origin").hide();
			$("#error-qrcode").hide();
			$("#divshowcode").hide();
		} else {
			if ($("#generate_type").val() == 1) {
				$("#lblqrcode").text(number_of_qr_code);
				$("#input_qrcode").attr("placeholder", number_of_qr_code);
			} else if ($("#generate_type").val() == 2) {
				$("#lblqrcode").text(qr_code_number);
				$("#input_qrcode").attr("placeholder", qr_code_number);
			}
			$("#input_qrcode").val("");
			$("#error-qrcode").hide();
			$("#divqrcode").show();
			$("#divshowcode").show();
		}

		getlastgeneratedqrcode();
	});

	$("#origin_qrcode").change(function () {
		if($("#origin_qrcode").val() == 0) {
			$("#generate_type").attr("disabled", "disabled");
		}else {
			$("#generate_type").removeAttr("disabled");
		}
		$("#generate_type").val("0").change();
		$("#input_qrcode").val("");
		$("#error-qrcode").hide();
		$("#divqrcode").hide();
		$("#divshowcode").hide();

		$('#xin_table_qrcodes').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/qrcode_list?originid=" + $("#origin_qrcode").val(),
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

function getlastgeneratedqrcode() {
	$("#loading").show();
	$.ajax({
		type: "GET",
		url: base_url + "/getlastgeneratedqrcode?originid=" + $("#origin_qrcode").val(),
		contentType: false,
		cache: false,
		processData: false,
		success: function (JSON) {
			$("#loading").hide();
			if (JSON.redirect == true) {
				window.location.replace(login_url);
			} else if (JSON.error != '') {
				toastr.clear();
				toastr.error(JSON.error);
				$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
			} else {
				toastr.clear();
				$("#last_qrcode").val(JSON.result);
				$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
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
		success: function(JSON) {
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
