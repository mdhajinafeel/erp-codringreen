$(document).ready(function () {

    $("#loading").hide();

    $("#origin_taxsetting").select2({ dropdownCssClass: "myFont" });

    fetchTaxSettings();

    $("#origin_taxsetting").change(function () {
        fetchTaxSettings();
    });

    $('#btn_new_tax').click(function () {
		$.ajax({
			url: base_url + "/dialog_tax_add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&type=addtax',
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
                    $("#number_format").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
                    $("#operands").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#status_taxsettings").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin_taxsettings").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});

	$(document).on('click', 'button[data-role=edit]', function () {
		var id = $(this).data('tax_id');
		$.ajax({
			url: base_url + "/dialog_tax_add",
			type: "GET",
			data: 'jd=1&is_ajax=3&mode=modal&type=edittax&tid=' + id,
			success: function (response) {
				if (response.redirect == true) {
					window.location.replace(login_url);
				} else {
					$("#ajax_modal_lg_bd").html(response);
					$("#add-modal-data-lg-bd").modal('show');
                    $("#number_format").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
                    $("#operands").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#status_taxsettings").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
					$("#origin_taxsettings").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_lg_bd') });
				}
			}
		});
	});
});

function fetchTaxSettings() {
    $('#xin_tax_settings').DataTable({
		"bDestroy": true,
		"lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
		"ajax": {
			url: base_url + "/tax_lists?originid=" + $("#origin_taxsetting").val(),
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