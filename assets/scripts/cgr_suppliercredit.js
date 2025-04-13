$(document).ready(function () {

    $("#loading").hide();

    $("#origin").select2({ dropdownCssClass: "myFont" });
    //$("#purchase_contract").select2({ dropdownCssClass: "myFont" });
    //$("#supplier_name").select2({ dropdownCssClass: "myFont" });

    $("#origin_ledger").select2({ dropdownCssClass: "myFont" });
   // $("#supplier_name_ledger").select2({ dropdownCssClass: "myFont" });
    
    $(document).on('click', 'button[data-role=deletecreditamount]', function () {
        var transaction_id = $(this).data("transaction_id");
        var supplier_id = $(this).data("supplier_id");
        var contract_id = $(this).data("contract_id");
        var origin_id = $(this).data("origin_id");
        $.ajax({
			url: base_url + "/dialog_ledger_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=deleteledgerconfirmation&tid=' + transaction_id + "&sid=" + supplier_id + "&cid=" + contract_id + "&oid=" + origin_id,
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
});