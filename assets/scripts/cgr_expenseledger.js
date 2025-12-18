$(document).ready(function () {

	$("#loading").hide();

    $("#origin").select2({ dropdownCssClass: "myFont" });
    $("#beneficiary_name").select2({ dropdownCssClass: "myFont" });

	//CREDIT
    $("#origin_ledger").select2({ dropdownCssClass: "myFont" });
    $("#beneficiary_name_ledger").select2({ dropdownCssClass: "myFont" });

    $(document).on('click', 'button[data-role=editcreditamount]', function () {
		var transaction_id = $(this).data('transaction_id');
		var transaction_display_id = $(this).data('transaction_display_id');
		var origin_id = $("#origin_ledger").val();
		var user_id = $("#beneficiary_name_ledger").val();
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_expense_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewcredit&tid=' + transaction_id + '&did=' + transaction_display_id + '&oid=' + origin_id + '&uid=' + user_id,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=deletecreditamount]', function () {
		var transaction_id = $(this).data('transaction_id');
		var transaction_display_id = $(this).data('transaction_display_id');
		var origin_id = $("#origin_ledger").val();
		var user_id = $("#beneficiary_name_ledger").val();
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_expense_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=deletecreditconfirmation&tid=' + transaction_id + '&did=' + transaction_display_id + '&oid=' + origin_id + '&uid=' + user_id,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
				}
			}
		});
		
	});

	//DEBIT
	$(document).on('click', 'button[data-role=viewdebittransaction]', function () {
		var transaction_id = $(this).data('transaction_id');
		var transaction_display_id = $(this).data('transaction_display_id');
		var origin_id = $("#origin_ledger").val();
		var user_id = $(this).data('user_id');
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_expense_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewdebit&tid=' + transaction_id + '&did=' + transaction_display_id + '&oid=' + origin_id + '&uid=' + user_id,
			success: function (response) {
				$("#loading").hide();
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_bd").html(response);
					$("#add-modal-data-bd").modal('show');
				}
			}
		});
	});

    $("#origin_report").select2({ dropdownCssClass: "myFont" });
});