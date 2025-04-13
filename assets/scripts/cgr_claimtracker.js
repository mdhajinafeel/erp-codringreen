$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin_claimtracker").select2({ dropdownCssClass: "myFont" });

	//SHIPPING LiNE
	$('#xin_table_claimtracker').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/claim_list?originid=" + $("#origin_claimtracker").val(),
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

	$('#btn_new_claimtracker').click(function () {
		$.ajax({
			url: base_url + "/dialog_claim_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addclaim',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#sa_number").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					//$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editclaim]', function () {
		var id = $(this).data('shipping_id');
		$.ajax({
			url: base_url + "/dialog_claim_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editclaim&sid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
				}
			}
		});
	});

	$("#origin_claimtracker").change(function () {
		$('#xin_table_claimtracker').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/claim_list?originid=" + $("#origin_claimtracker").val(),
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
