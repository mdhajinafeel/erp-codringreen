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
});