$(document).ready(function () {

	$("#loading").hide();

	$('#btn_add_extraction_cost').click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/extractioncost/dialog_extraction_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addcost',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_extraction").attr("disabled", false);
					$("#supplier_extraction").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });

					$("#btn_add_extraction_tree").hide();
					loadExtractionTreesTable(0);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editextraction]', function () {
		var id = $(this).data('extraction_id');
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/extractioncost/dialog_extraction_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=editcost&eId=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#origin_extraction").attr("disabled", false);
					$("#supplier_extraction").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });


					// 🔥 LOAD TREES LIST
					loadExtractionTreesTable(id);
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deleteextraction]', function () {
		let extractionId = $(this).data('extraction_id');

		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/extractioncost/dialog_extraction_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=deleteconfirmation&eid=' + extractionId,
			success: function (response) {

				$("#ajax_modal").html(response);
				$("#add-modal-data").modal('show');
			}
		});
	});

	$("#btn_dowload_extraction_cost").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/extractioncost/dialog_extraction_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generatereport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_maintainance_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generatemaintainancereport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_machinerental_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generatemachinerentalreport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_manuallabour_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generatemanuallabourreport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_acpm_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generateacpmreport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_lubricants_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generatelubricantsreport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});

	$("#generate_others_report").click(function () {
		$.ajax({
			url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operations_action",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=generateothersreport',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd1").html(response);
					$("#add-modal-data-bd1").modal('show');
					$("#origin_report").attr("disabled", false);
					$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
				}
			}
		});
	});
});