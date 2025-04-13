$(document).ready(function () {

	$("#loading").hide();

	$("#origin_ledgertypes").select2({ dropdownCssClass: "myFont" });
	$("#origin_accounthead").select2({ dropdownCssClass: "myFont" });

	fetchLedgerTypes();
	fetchAccountHeads();

	//LEDGER TYPES
	$("#origin_ledgertypes").change(function () {
		fetchLedgerTypes();
	});

	$('#btn_new_ledgertypes').click(function () {
		$.ajax({
			url: base_url + "/dialog_ledger_type_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addledgertype',
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

	$(document).on('click', 'button[data-role=editledger]', function () {
		var id = $(this).data('ledger_id');
		$.ajax({
			url: base_url + "/dialog_ledger_type_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editledgertype&lid=' + id,
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

	//ACCOUNT HEAD
	$("#origin_accounthead").change(function () {
		fetchAccountHeads();
	});

	$('#btn_new_accounthead').click(function () {
		$.ajax({
			url: base_url + "/dialog_account_head_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addaccounthead',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#ledger_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=editaccounthead]', function () {
		var id = $(this).data('account_head_id');
		$.ajax({
			url: base_url + "/dialog_account_head_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editaccounthead&aid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
					$("#status").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#ledger_type").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});
});

function fetchLedgerTypes() {
	$('#xin_table_ledgertypes').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/ledger_type_list?originid=" + $("#origin_ledgertypes").val(),
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
}

function fetchAccountHeads() {
	$('#xin_table_accountheads').DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/accounthead_list?originid=" + $("#origin_accounthead").val(),
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
}