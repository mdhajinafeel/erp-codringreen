$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
    $("#status").select2({ dropdownCssClass: "myFont" });

    fetch_dispatchtracking();

	$("#origin").change(function () {
		fetch_dispatchtracking();
	});

    $("#status").change(function () {
		fetch_dispatchtracking();
	});

    $(document).on('click', 'button[data-role=edittracking]', function () {
        var dispatch_id = $(this).data("dispatch_id");
		var container_number = $(this).data("container_number");
        $.ajax({
			url: base_url + "/dialog_dispatchtracking_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=editdispatchtracking&did=' + dispatch_id + '&io=' + container_number,
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

function fetch_dispatchtracking() {
	$("#xin_table_dispatchtracking").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/dispatchtracking_list?originid=" + $("#origin").val() + "&status=" + $("#status").val(),
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