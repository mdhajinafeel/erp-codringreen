$(document).ready(function () {
	
	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
    $("#product_name").select2({ dropdownCssClass: "myFont" });
    $("#product_type").select2({ dropdownCssClass: "myFont" });

    fetch_exportready();

	$("#origin").change(function () {
		fetch_exportready();
	});

    $("#product_name").change(function () {
		fetch_exportready();
	});

    $("#product_type").change(function () {
		fetch_exportready();
	});

    $(document).on('click', 'button[data-role=viewdispatch]', function () {
		var dispatch_id = $(this).data("dispatch_id");
		var container_number = $(this).data("container_number");
		$.ajax({
			url: base_url + "/dialog_export_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=viewdispatch&did=' + dispatch_id + '&cn=' + container_number,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal").html(response);
					$("#add-modal-data").modal('show');
					$("#shipping_line").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
					$("#wh_name").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=downloaddispatch]', function () {
		var dispatch_id = $(this).data("dispatch_id");
		var container_number = $(this).data("container_number");
		$("#loading").show();
		$.ajax({
			url: base_url + "/dialog_export_action",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=downloaddispatch&did=' + dispatch_id + '&cn=' + container_number,
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
});

function fetch_exportready() {
	$("#xin_table_exportready").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/exportready_list?originid=" + $("#origin").val() + "&pid=" + $("#product_name").val() + "&tid=" +$("#product_type").val(),
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

function deletefilesfromfolder() {
	$.ajax({
		type: "GET",
		url: base_url + "/deletefilesfromfolder",
		contentType: false,
		cache: false,
		processData: false,
		success: function (JSON) {
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