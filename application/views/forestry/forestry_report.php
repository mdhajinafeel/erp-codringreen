<?php
$session = $this->session->userdata("fullname");
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper("language"); ?>
<?php $wz_lang = $site_lang->session->userdata("site_lang"); ?>
<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $this->lang->line("forestry_report"); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="report_origin_name"><?php echo $this->lang->line("origin"); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="report_origin_name" id="report_origin_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="report_supplier_name"><?php echo $this->lang->line('supplier_name'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="report_supplier_name" id="report_supplier_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-name" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="report_purchase_contract"><?php echo $this->lang->line('purchase_contract'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="report_purchase_contract" id="report_purchase_contract" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-purchase_contract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" id="lbl_report_from_date" for="report_from_date"><?php echo $this->lang->line('start_date'); ?></label>
            <div class="col-sm-10" id="divstartdate">
                <input type="text" id="report_from_date" name="report_from_date" class="form-control" placeholder="<?php echo $this->lang->line("start_date"); ?>" readonly />
                <label id="error-startdate" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" id="lbl_report_to_date" for="report_to_date"><?php echo $this->lang->line('end_date'); ?></label>
            <div class="col-sm-10" id="divenddate">
                <input type="text" id="report_to_date" name="report_to_date" class="form-control" placeholder="<?php echo $this->lang->line("end_date"); ?>" readonly />
                <label id="error-enddate" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-success btn-block ml-10" title="<?php echo $this->lang->line("generate_report"); ?>" type="button" id="btn_download_reports">
                        <span class="ms-1"><?php echo $this->lang->line("generate_report"); ?></span></button>

                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset_reports">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
<script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>

<script type="text/javascript">
    
    $(document).ready(function() {
        $("#error-origin").hide();
        $("#error-name").hide();
        $("#error-purchase_contract").hide();
        $("#error-startdate").hide();
        $("#error-enddate").hide();

        $("#report_from_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "3m",
            onSelect: function(date) {
                var selectedDate = $("#report_from_date").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $("#report_to_date").datepicker("option", "minDate", endDate);
            }
        });

        $("#report_to_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "3m",
            onSelect: function(date) {}
        });

        $('.ui-datepicker').addClass('notranslate');

        $("#report_origin_name").change(function() {
            if ($("#report_origin_name").val() == 0) {
                $("#report_supplier_name").attr("disabled", true);
                fetchSuppliers(0);
                fetchContracts(0, 0);
            } else {
                fetchSuppliers($("#report_origin_name").val());
                $("#report_supplier_name").attr("disabled", false);
                $("#report_purchase_contract").attr("disabled", true);

                fetchContracts(0, 0);
                $("#error-origin").hide();
            }
        });

        $("#report_supplier_name").change(function() {

            if ($("#report_supplier_name").val() == 0) {
                $("#report_purchase_contract").attr("disabled", true);
                fetchContracts(0, 0);
            } else {
                fetchContracts($("#report_origin_name").val(), $("#report_supplier_name").val());
                $("#error-name").hide();
            }
        });

        $("#btn_download_reports").click(function () {

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true;

            if ($("#report_origin_name").val() == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            } 

            // if ($("#report_supplier_name").val() == 0) {
            //     $("#error-name").show();
            //     isValid2 = false;
            // } else {
            //     $("#error-name").hide();
            //     isValid2 = true;
            // }

            // if ($("#report_purchase_contract").val() == 0) {
            //     $("#error-purchase_contract").show();
            //     isValid3 = false;
            // } else {
            //     $("#error-purchase_contract").hide();
            //     isValid3 = true;
            // }

            if ($("#report_from_date").val() == '') {
                $("#error-startdate").show();
                isValid4 = false;
            } else {
                $("#error-startdate").hide();
                isValid4 = true;
            }

            if ($("#report_to_date").val() == '') {
                $("#error-enddate").show();
                isValid5 = false;
            } else {
                $("#error-enddate").hide();
                isValid5 = true;
            }

            if (isValid1 == true && isValid2 == true && isValid3 == true && isValid4 == true && isValid5 == true) {
            toastr.clear();
            toastr.info(processing_request);
            $("#loading").show();
            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/forestryreports/dialog_report_action?type=downloadreport&oid=" + $("#report_origin_name").val() 
                    + "&sid=" + $("#report_supplier_name").val() + "&cid=" + $("#report_purchase_contract").val() + "&fromdate=" + $("#report_from_date").val() 
                    + "&todate=" + $("#report_to_date").val(),
                type: "GET",
                processData: false,
                contentType: false,
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
            }
        });
    });

    function fetchSuppliers(originid) {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/fetch_suppliers?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#report_supplier_name").empty();
                    $("#report_supplier_name").append(JSON.result);
                }
            }
        });
    }

    function fetchContracts(originid, supplierid) {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/get_contracts_by_supplier?originid=" + originid + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#report_purchase_contract").attr("disabled", false);
                    $("#report_purchase_contract").empty();
                    $("#report_purchase_contract").append(JSON.result);
                } else {
                    $("#report_purchase_contract").attr("disabled", true);
                }
            }
        });
    }
</script>