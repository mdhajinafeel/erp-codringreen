$(document).ready(function () {

	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });

	$('#xin_table_sawmills').DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/sawmill_list?originid=" + $("#origin").val(),
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
		// "columnDefs": [
		// 	 {
		// 		"searchable": true,
		// 		"orderable": true,
		// 		"targets": 5,
		// 		"type": 'date'
		// 	 }
		// ],
		"order": [
			[0, "asc"]
		], "language": {
			"url": datatable_language
		}
	});

	$('#accordionExample').on('shown.bs.collapse', function () {
		$($.fn.dataTable.tables(true)).DataTable()
			.columns.adjust();
	});

	$("#origin").change(function () {
		$('#xin_table_sawmills').DataTable({
			"bDestroy": true,
			"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
			"ajax": {
				url: base_url + "/sawmill_list?originid=" + $("#origin").val(),
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
			// 	"columnDefs": [
			// 		{
			// 		   "searchable": true,
			// 		   "orderable": true,
			// 		   "targets": 5,
			// 		   "type": 'date'
			// 		}
			//    ],
			"order": [
				[0, "asc"]
			], "language": {
				"url": datatable_language
			}
		});

		echartsBasicBarChartInit();
		summaryDataInit();
	});

	$("#generate_report").click(function (e) {
		e.preventDefault();
		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			url: base_url + "/generate_sawmill_report?originid=" + $("#origin").val(),
			type: "GET",
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
					//wait(3000);
					//deletefilesfromfolder();
				}
			}
		});
	});

	$("#generate_received_report").click(function (e) {
		e.preventDefault();
		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			url: base_url + "/generate_received_report?originid=" + $("#origin").val(),
			type: "GET",
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
					//wait(3000);
					//deletefilesfromfolder();
				}
			}
		});
	});

	$("#generate_exported_report").click(function (e) {
		e.preventDefault();
		toastr.clear();
		toastr.info(processing_request);
		$("#loading").show();
		$.ajax({
			url: base_url + "/generate_exported_report?originid=" + $("#origin").val(),
			type: "GET",
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
					//wait(3000);
					//deletefilesfromfolder();
				}
			}
		});
	});
});