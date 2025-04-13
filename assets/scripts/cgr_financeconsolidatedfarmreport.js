$(document).ready(function () {

    $("#loading").hide();

    $("#generate_farm_report").click(function () {

        var origin_id = $("#origin_consolidatedfarmreport").val();

        var fd = new FormData();
        fd.append("origin_id", origin_id);
        fd.append("type_id", 1);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/dialog_farm_report",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                $("#loading").hide();
                toastr.clear();
                if (response.redirect == true) {
                    window.location.replace(login_url);
                } else {
                    $("#ajax_modal_bd1").html(response);
                    $("#add-modal-data-bd1").modal('show');
                }
            }
        });
    });
});