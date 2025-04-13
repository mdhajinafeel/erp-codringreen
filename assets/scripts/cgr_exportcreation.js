var checkedItems = 0;
$(document).ready(function () {

	$("#loading").hide();

	$("#origin").select2({ dropdownCssClass: "myFont" });
	//$("#product_name").select2({ dropdownCssClass: "myFont" });
	//$("#product_type").select2({ dropdownCssClass: "myFont" });

	fetch_exportcreation();

	$("#origin").change(function () {
		fetch_exportcreation();
		check_box_click("");
	});

	$("#product_name").change(function () {
		fetch_exportcreation();
		check_box_click("");
	});

	$("#product_type").change(function () {
		fetch_exportcreation();
		check_box_click("");
	});

	$("#btn_create_export").click(function () {
		toastr.clear();
		var selectedContainerIds = [];
		var containers = document.getElementsByName('container[]');
		for (var i = 0, n = containers.length; i < n; i++) {
			if (containers[i].checked) {
				selectedContainerIds.push(containers[i].value);
			}
		}

		if (selectedContainerIds.length > 0) {
			$("#loading").show();
			var fd = new FormData();

			fd.append("type", "createexport");
			fd.append("dispatchids", selectedContainerIds);
			fd.append("csrf_cgrerp", $("#hdnCsrf").val());
			fd.append("originid", $("#origin").val());

			$.ajax({
				url: base_url + "/dialog_create_export",
				type: "POST",
				data: fd,
				contentType: false,
				processData: false,
				success: function (response) {
					$("#loading").hide();
					if (response.redirect == true) {
						window.location.replace(login_url);
					} if (response.messagetype == "info") {
						$("#titlehead").text(response.pageheading);
						$("#infomessage").text(response.pagemessage);
						$("#alert-dialog-info").modal('show');
					} else {
						$("#ajax_modal_bd").html(response);
						$("#add-modal-data-bd").modal('show');
						$("#port_of_discharge").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
						$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					}
				}
			});
		} else {
			toastr.error(error_select_container);
		}
	});

	$("#generate_summary_report").click(function () {
		
		toastr.clear();
		var selectedContainerIds = [];
		var containers = document.getElementsByName('container[]');
		for (var i = 0, n = containers.length; i < n; i++) {
			if (containers[i].checked) {
				selectedContainerIds.push(containers[i].value);
			}
		}

		if (selectedContainerIds.length > 0) {
			$("#loading").show();
			var fd = new FormData();
			fd.append("type", "dialogmeasurement");
			fd.append("dispatchids", selectedContainerIds);
			fd.append("csrf_cgrerp", $("#hdnCsrf").val());
			fd.append("originid", $("#origin").val());

			$.ajax({
				url: base_url + "/dialog_create_export",
				type: "POST",
				data: fd,
				contentType: false,
				processData: false,
				success: function (response) {
					$("#loading").hide();
					if (response.redirect == true) {
						window.location.replace(login_url);
					} else if (response.messagetype == "info") {
						$("#titlehead").text(response.pageheading);
						$("#infomessage").text(response.pagemessage);
						$("#alert-dialog-info").modal('show');
					}  else {
						$("#ajax_modal_bd").html(response);
						$("#add-modal-data-bd").modal('show');
						$("#measurement_system").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd') });
					}
				}
			});
		} else {
			toastr.error(error_select_container);
		}
	});
});

function china_toggle(source) {
	checkboxes = document.getElementsByName('container[]');
	for (var i = 0,
		n = checkboxes.length; i < n; i++) {
		checkboxes[i].checked = source.checked;
	}
}

function check_box_click(source) {

	if (source == "") {
		checkedItems = 0;
		$("#selectall").prop("checked", false);
	} else {
		if (source.checked == true) {
			checkedItems = checkedItems + 1;
		} else {
			if (checkedItems > 0) {
				checkedItems = checkedItems - 1;
			}
		}

		var totalItems = document.getElementsByName('container[]').length;

		if (checkedItems == totalItems) {
			$("#selectall").prop("checked", true);
		} else {
			$("#selectall").prop("checked", false);
		}
	}
}

function fetch_exportcreation() {
	$("#xin_table_exportcreation").DataTable({
		"bDestroy": true,
		"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
		"ajax": {
			url: base_url + "/exportcreation_list?originid=" + $("#origin").val() + "&pid=" + $("#product_name").val() + "&tid=" + $("#product_type").val(),
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
		"ordering": false,
		"language": {
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
