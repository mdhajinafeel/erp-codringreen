$(document).ready(function () {

    $("#loading").hide();

    //LIQUIDATION REPORT

    //$("#origin_liquidationreport").select2({ dropdownCssClass: "myFont" });

    fetchLiquidationContracts($("#origin_liquidationreport").val());

    $("#origin_liquidationreport").change(function () {
        fetchLiquidationContracts($("#origin_liquidationreport").val());
    });

    $(document).on('click', 'button[data-role=downloadliquidation_warehouse]', function () {

        var contract_id = $(this).data("contract_id");
        var contract_code = $(this).data("contract_code");
        var supplier_id = $(this).data("supplier_id");
        var origin_id = $(this).data("origin_id");

        var fd = new FormData();
        fd.append("contractId", contract_id);
        fd.append("contractCode", contract_code);
        fd.append("supplierId", supplier_id);
        fd.append("originId", origin_id);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/generate_warehouse_liquidation_report",
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
                    //wait(3000);
                    //deletefilesfromfolder();
                }
            }
        });
    });

    $(document).on('click', 'button[data-role=downloadliquidation_fieldpurchase]', function () {

        var contract_id = $(this).data("contract_id");
        var contract_code = $(this).data("contract_code");
        var origin_id = $(this).data("origin_id");

        var fd = new FormData();
        fd.append("contractId", contract_id);
        fd.append("contractCode", contract_code);
        fd.append("originId", origin_id);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("type", "generate_liquidation");

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/dialog_generate_liquidation",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                $("#loading").hide();
                if (response.redirect == true) {
                    window.location.replace(login_url);
                } else {
                    // if (response.error != "") {
                    //     toastr.clear();
                    //     toastr.error(response.error);
                    // } else {
                    $("#ajax_modal_bd1").html(response);
                    $("#add-modal-data-bd1").modal('show');
                    //$("#supplier_name").select2({ dropdownCssClass: "myFont" });
                    //}
                }
            }
        });
    });

    $(document).on('click', 'button[data-role=updateinvoice_warehouse]', function () {

        var contract_id = $(this).data("contract_id");
        var contract_code = $(this).data("contract_code");
        var supplier_id = $(this).data("supplier_id");
        var origin_id = $(this).data("origin_id");

        var fd = new FormData();
        fd.append("contract_id", contract_id);
        fd.append("contract_code", contract_code);
        fd.append("supplier_id", supplier_id);
        fd.append("origin_id", origin_id);
        fd.append("type_id", 1);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/fetch_inventory",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                $("#loading").hide();
                if (response.redirect == true) {
                    window.location.replace(login_url);
                } else {
                    // if (response.error != "") {
                    //     toastr.clear();
                    //     toastr.error(response.error);
                    // } else {
                    $("#ajax_modal_bd1").html(response);
                    $("#add-modal-data-bd1").modal('show');
                    //}
                }
            }
        });
    });

    $(document).on('click', 'button[data-role=updateinvoice_fieldpurchase]', function () {

        var contract_id = $(this).data("contract_id");
        var contract_code = $(this).data("contract_code");
        var supplier_id = $(this).data("supplier_id");
        var origin_id = $(this).data("origin_id");

        var fd = new FormData();
        fd.append("contract_id", contract_id);
        fd.append("contract_code", contract_code);
        fd.append("supplier_id", supplier_id);
        fd.append("origin_id", origin_id);
        fd.append("type_id", 2);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/fetch_inventory",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                $("#loading").hide();
                if (response.redirect == true) {
                    window.location.replace(login_url);
                } else {
                    // if (response.error != "") {
                    //     toastr.clear();
                    //     toastr.error(response.error);
                    // } else {
                    $("#ajax_modal_bd1").html(response);
                    $("#add-modal-data-bd1").modal('show');
                    //}
                }
            }
        });
    });
    
    $("#generate_liquidation_report").click(function () {

        var origin_id = $("#origin_liquidationreport").val();

        var fd = new FormData();
        fd.append("origin_id", origin_id);
        fd.append("type_id", 1);
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: base_url + "/dialog_liquidation_report",
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
                    //$("#supplier_name_report").select2({ dropdownCssClass: "myFont", dropdownParent: $('#ajax_modal_bd1') });
                }
            }
        });
    });

    //END LIQUIDATION REPORT

    //STOCK REPORT

    $("#divStockDetails").hide();
    $("#divTransactions").hide();
    $("#divDataAvailable").hide();
    //$("#origin_stockreport").select2({ dropdownCssClass: "myFont" });

    $("#origin_stockreport").change(function () {

        $("#divStockDetails").hide();
        $("#divTransactions").hide();
        $("#divDataAvailable").hide();

        fetchStockDetails($("#origin_stockreport").val());
    });

    $("#btn_reset").click(function () {
        //$("#origin_stockreport").select2("val", "0");
        $("#origin_stockreport").attr('selectedIndex', 0);
        $("#divStockDetails").hide();
        $("#divTransactions").hide();
        $("#divDataAvailable").hide();
    });

    $("#btn_download_reports").click(function (e) {
        e.preventDefault();
        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            url: base_url + "/generate_stock_report?originid=" + $("#origin_stockreport").val(),
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

    //END STOCK REPORT

    //COST SUMMARY REPORT

    //$("#origin_costsummaryreport").select2({ dropdownCssClass: "myFont" });
    //$("#report_downloadby").select2({ dropdownCssClass: "myFont" });


    $("#origin_costsummaryreport").change(function () {

        if ($("#origin_costsummaryreport").val() == 0) {
            $("#report_downloadby").attr("disabled", true);
        } else {
            $("#report_downloadby").attr("disabled", false);
        }

        $("#lbl_report_from_date").css("display", "none");
        $("#lbl_report_to_date").css("display", "none");
        $("#lbl_product_type").css("display", "none");
        $("#lbl_container_number").css("display", "none");
        $("#report_from_date").css("display", "none");
        $("#report_from_date").val("");
        $("#report_to_date").css("display", "none");
        $("#report_to_date").val("");
        $("#div_product_type").css("display", "none");
        $("#divstartdate").css("display", "none");
        $("#divenddate").css("display", "none");
        $("#div_container_number").css("display", "none");

        $("#lbl_export_sa").css("display", "none");
        $("#div_export_sa").css("display", "none");
        //$("#product_type").select2({ dropdownCssClass: "myFont" });
        //$("#product_type").select2("val", "0");
        //$("#report_downloadby").select2("val", "0");
        
        $("#product_type").attr('selectedIndex', 0);
        $("#report_downloadby").attr('selectedIndex', 0);
        
        $("#container_number").select2({ dropdownCssClass: "myFont" });
        $("#container_number").select2("val", "0");
        //$("#container_number").attr('selectedIndex', 0);
    });

    $("#btn_reset_costsummary").click(function () {
        //$("#origin_costsummaryreport").select2("val", "0");
        $("#origin_costsummaryreport").attr('selectedIndex', 0);
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
        var container_number = $("#container_number").val();

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
            } else if (report_downloadby == 3) {
                if (container_number == 0) {
                    isValid5 = false;
                    $("#error-container").show();
                    $("#error-container").text(error_select_the_container);
                } else {
                    isValid5 = true;
                    $("#error-container").hide();
                    $("#error-container").text("");
                }
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

                var fd = new FormData();
                fd.append("originid", originid);
                fd.append("downloadtype", report_downloadby);
                fd.append("startdate", report_from_date);
                fd.append("enddate", report_to_date);
                
                if (report_downloadby == 3) {
                    fd.append("downloadtype_processid", container_number);
                } else {
                    fd.append("downloadtype_processid", product_type);
                }
                
                
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
                            //wait(3000);
                            //deletefilesfromfolder();
                        }
                    }
                });
            }
        }
    });

    $("#btn_upload_container_data").click(function () {

        toastr.clear();
        toastr.info(processing_request);
        $("#loading").show();
        $.ajax({
            type: "GET",
            url: base_url + "/dialog_upload_container",
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
    });

    $("#btn_download_costsummaryreports").click(function () {

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
        var container_number = $("#container_number").val();

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
            } else if (report_downloadby == 3) {
                if (container_number == 0) {
                    isValid5 = false;
                    $("#error-container").show();
                    $("#error-container").text(error_select_the_container);
                } else {
                    isValid5 = true;
                    $("#error-container").hide();
                    $("#error-container").text("");
                }
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

                var fd = new FormData();
                fd.append("originid", originid);
                fd.append("downloadtype", report_downloadby);
                fd.append("startdate", report_from_date);
                fd.append("enddate", report_to_date);
                
                if (report_downloadby == 3) {
                    fd.append("downloadtype_processid", container_number);
                } else {
                    fd.append("downloadtype_processid", product_type);
                }
                
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.clear();
                toastr.info(processing_request);
                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/generate_report",
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
                            //wait(3000);
                            //deletefilesfromfolder();
                        }
                    }
                });
            }
        }
    });

    //END COST SUMMARY REPORT
});

function fetchLiquidationContracts(originid) {


    $("#xin_table_wh_liquidation").DataTable({
        "bDestroy": true,
        "lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
        "ajax": {
            url: base_url + "/liquidation_contract_lists?originid=" + $("#origin_liquidationreport").val() + "&type=1",
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
        ],
        "order": [
            [0, "asc"]
        ], "language": {
            "url": datatable_language
        }
    });

    setTimeout(function () {
        $("#xin_table_field_liquidation").DataTable({
            "bDestroy": true,
            "lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
            "ajax": {
                url: base_url + "/liquidation_contract_lists?originid=" + $("#origin_liquidationreport").val() + "&type=2",
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
                    "targets": 4,
                    "type": 'date'
                }
            ],
            "order": [
                [0, "asc"]
            ], "language": {
                "url": datatable_language
            }
        });
    }, 2000);
}

function fetchStockDetails(originid) {
    $("#loading").show();
    $.ajax({
        url: base_url + "/get_stock_details?originid=" + originid,
        cache: false,
        method: "GET",
        dataType: "json",
        success: function (JSON) {
            $("#loading").hide();
            if (JSON.redirect == true) {
                window.location.replace(login_url);
            } else if (JSON.result != '') {

                $("#divDataAvailable").hide();

                $("#txtTotalInventory").text(JSON.result["totalInventory"]);
                $("#txtTotalPieces").text(JSON.result["totalPieces"]);
                $("#txtTotalCosts").text(JSON.result["totalCosts"]);
                $("#txtTotalVolume").text(JSON.result["totalVolume"]);

                $("#divStockDetails").show();

                parseTransactions(JSON.result["stockTransactions"]);
                $("#divTransactions").show();
            } else if (JSON.error != '') {
                if (originid == 0) {
                    $("#divDataAvailable").hide();
                } else {
                    $("#divDataAvailable").show();
                }
            }
        }
    });
}

function parseTransactions(stocktransactions) {
    $('#xin_table_stocks').DataTable({
        "bDestroy": true,
        "lengthMenu": [
            [50, 100, 200, -1],
            [50, 100, 200, "All"]
        ],
        data: stocktransactions,
        columns: [{
            data: 'inventoryOrder'
        }, {
            data: 'supplierName'
        }, {
            data: 'remaningStock'
        }, {
            data: 'remaningVolume'
        }, {
            data: 'costOfWood'
        }],
        //dom: 'lBfrtip',
        "sScrollX": "100%",
        "scrollCollapse": true,
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        paging: true,
        searching: true,
        fixedColumns: true,
        responsive: true,
        "columnDefs": [{
            "searchable": true,
            "orderable": true,

        }],
        "order": [
            [0, "asc"]
        ],
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
            //$("#loading").hide();
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