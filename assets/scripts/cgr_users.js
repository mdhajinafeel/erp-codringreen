$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });

	$('#xin_table').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/user_list?originid=" + $("#origin").val(),
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
			url: base_url + "/dialog_user_add/",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=adduser',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#default_language").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#default_timezone").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=edit]', function () {
		var id = $(this).data('user_id');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_user_add/",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=edituser&uid=' + id,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#default_language").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					$("#default_timezone").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
				}
			}
		});
	});

	$("#origin").change(function() {
		$('#xin_table').DataTable({
			"bDestroy": true,
			"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
			"ajax": {
				url: base_url + "/user_list?originid=" + $("#origin").val(),
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
