$(document).ready(function () {

    $("#loading").hide();

    //SALES REPORT

    $("#origin_salesreport").select2({ dropdownCssClass: "myFont" });
   // $("#report_downloadby").select2({ dropdownCssClass: "myFont" });

    $("#origin_salesreport").change(function () {
        $("#error-origin").hide();
        if ($("#origin_salesreport").val() == 0) {
            $("#report_downloadby").attr("disabled", true);
        } else {
            $("#report_downloadby").attr("disabled", false);
        }

        $("#lbl_product_type").css("display", "none");
        $("#div_product_type").css("display", "none");
         $("#product_type").select2({ dropdownCssClass: "myFont" });
        // $("#product_type").select2("val", "0");
        // $("#report_downloadby").select2("val", "0");
        
        $("#product_type").attr('selectedIndex', 0);
        $("#report_downloadby").attr('selectedIndex', 0);
        
        
    });

    $("#btn_reset_salesreports").click(function () {
        $("#origin_salesreport").select2("val", "0");
        
    });

    $("#btn_download_containerreports").click(function () {

        var isValid1 = true,
            isValid2 = true,
            isValid3 = true,
            isValid4 = true,
            isValid5 = true;

        var originid = $("#origin_costsummaryreport").val();
        var report_downloadby = $("#report_downloadby").val();
        var report_from_date = $("#report_from_date").val().trim();
        var report_to_date = $("#report_to_date").val().trim();
        var product_type = $("#product_type").val();

        if (originid == 0) {
            $("#error-origin").show();
            isValid1 = false;
        } else {
            $("#error-origin").hide();
            isValid1 = true;
        }

        if (report_downloadby == 0) {
            $("#error-selectoption").show();
            isValid2 = false;
        } else {
            $("#error-selectoption").hide();
            isValid2 = true;
        }

        if (isValid2) {
            if (report_downloadby == 1) {
                if (report_from_date.length == 0) {
                    isValid3 = false;
                    $("#error-startdate").show();
                } else {
                    isValid3 = true;
                    $("#error-startdate").hide();
                }

                if (report_to_date.length == 0) {
                    isValid4 = false;
                    $("#error-enddate").show();
                } else {
                    isValid4 = true;
                    $("#error-enddate").hide();
                }

                if (product_type == 0) {
                    isValid5 = false;
                    $("#error-producttype").show();
                    $("#error-producttype").text(error_selectwoodtype);
                } else {
                    isValid5 = true;
                    $("#error-producttype").hide();
                    $("#error-producttype").text("");
                }
            } else if (report_downloadby == 2) {
                if (product_type == 0) {
                    isValid5 = false;
                    $("#error-producttype").show();
                    $("#error-producttype").text(error_selectsa);
                } else {
                    isValid5 = true;
                    $("#error-producttype").hide();
                    $("#error-producttype").text("");
                }
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

                var fd = new FormData();
                fd.append("originid", originid);
                fd.append("downloadtype", report_downloadby);
                fd.append("startdate", report_from_date);
                fd.append("enddate", report_to_date);
                fd.append("downloadtype_processid", product_type);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.clear();
                toastr.info(processing_request);
                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/fetch_containers",
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
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
            }
        }
    });

    $("#btn_upload_sales_data").click(function () {

        if ($("#origin_salesreport").val() == 0) {
            $("#error-origin").show();
            return false;
        } else {

            $("#error-origin").hide();
            toastr.clear();
            toastr.info(processing_request);
            $("#loading").show();
            $.ajax({
                type: "GET",
                url: base_url + "/dialog_upload_sales?originId=" + $("#origin_salesreport").val(),
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    toastr.clear();
                    $("#loading").hide();
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#ajax_modal_lg").html(response);
                        $("#add-modal-data-lg").modal('show');
                        $("#fileContainerExcel").val("");
                    }
                }
            });
        }
    });

    $("#btn_download_salesreports").click(function () {

        var fd = new FormData();
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/generate_sales_report",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
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
                }
            }
        });
    });

    $("#btn_view_salesreports").click(function(){

        var fd = new FormData();
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("originId", $("#origin_salesreport").val());
        fd.append("saNumber", $("#product_type").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/generate_sa_report",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
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
                }
            }
        });
    });

    //END SALES REPORT
});