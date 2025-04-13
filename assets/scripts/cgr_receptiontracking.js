$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
    $("#status").select2({ dropdownCssClass: "myFont" });

    fetch_receptiontracking();

	$("#origin").change(function () {
		fetch_receptiontracking();
	});

    $("#status").change(function () {
		fetch_receptiontracking();
	});

    $(document).on('click', 'button[data-role=edittracking]', function () {
        var reception_id = $(this).data("reception_id");
		var inventory_order = $(this).data("inventory_order");
        $.ajax({
			url: base_url + "/dialog_receptiontracking_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editreceptiontracking&rid=' + reception_id + '&io=' + inventory_order,
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

function fetch_receptiontracking() {
	$("#xin_table_receptiontracking").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/receptiontracking_list?originid=" + $("#origin").val() + "&status=" + $("#status").val(),
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
		"columnDefs": [
			 {
				"searchable": true,
				"orderable": true,
				"targets": 5,
				"type": 'date'
			 }
		],
		"order": [
			[0, "asc"]
		], "language": {
			"url": datatable_language
		}
	});
}