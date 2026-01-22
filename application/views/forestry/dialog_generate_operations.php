<?php
defined('BASEPATH') or exit('No direct script access allowed');

$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
$site_lang = $this->load->helper('language');
$wz_lang = $site_lang->session->userdata('site_lang'); ?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php echo form_open("", $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">
    <input type="hidden" id="hdnDownloadType" name="hdnDownloadType" value="<?php echo $downloadtype; ?>">

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="origin_report"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_report" id="origin_report" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($extractionDetails[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="supplier_name_report"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name_report" id="supplier_name_report" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="purchase_contract_report"><?php echo $this->lang->line('purchase_contract'); ?></label>
            <select class="form-control" name="purchase_contract_report" id="purchase_contract_report" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-purchasecontract" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="from_date_report"><?php echo $this->lang->line('from_date'); ?></label>
            <input type="text" id="from_date_report" name="from_date_report" class="form-control from_date_report" readonly />
            <label id="error-fromdate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="to_date_report"><?php echo $this->lang->line('to_date'); ?></label>
            <input type="text" id="to_date_report" name="to_date_report" class="form-control to_date_report" readonly />
            <label id="error-todate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-sm btn-secondary mb-1" name="btn_close" data-bs-dismiss="modal" id="btn_close"><?php echo $this->lang->line("close"); ?></button>
    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_download_extraction" id="btn_download_extraction"><?php echo $this->lang->line("download"); ?></button>
</div>
<?php echo form_close(); ?>

<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>
<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var selecttext = "<?php echo $this->lang->line("select"); ?>";

    var processing_request = "<?php echo $this->lang->line("processing_request"); ?>";
    $("#error-origin").hide();
    $("#error-name").hide();
    $("#error-purchasecontract").hide();
    $("#error-fromdate").hide();
    $("#error-todate").hide();

    $(document).ready(function() {

        $("#origin_report").change(function() {
            if ($("#origin_report").val() == 0) {
                $("#supplier_name_report").attr("disabled", true);
                fetchSuppliers(0);
                fetchContracts(0, 0);
            } else {
                fetchSuppliers($("#origin_report").val());
                $("#supplier_name_report").attr("disabled", false);
                $("#purchase_contract_report").attr("disabled", true);

                fetchContracts(0, 0);
                $("#error-origin").hide();
            }
        });

        $("#supplier_name_report").change(function() {

            if ($("#supplier_name_report").val() == 0) {
                $("#purchase_contract_report").attr("disabled", true);
                fetchContracts(0, 0);
            } else {
                fetchContracts($("#origin_report").val(), $("#supplier_name_report").val());
            }
        });

        $("#btn_download_extraction").click(function() {
            var fromdate = $("#from_date_report").val();
            var todate = $("#to_date_report").val();
            var supplierid = $("#supplier_name_report").val();
            var farmid = $("#purchase_contract_report").val();
            var originid = $("#origin_report").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true;

            if (originid == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
            }

            if (supplierid == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
            }

            if (farmid == 0) {
                $("#error-purchasecontract").show();
                isValid3 = false;
            } else {
                $("#error-purchasecontract").hide();
            }

            if (isValid1 && isValid2 && isValid3) {

                var fd = new FormData();
                fd.append("supplierId", supplierid);
                fd.append("fromDate", fromdate);
                fd.append("toDate", todate);
                fd.append("farmId", farmid);
                fd.append("originId", originid);
                fd.append("downloadType", $("#hdnDownloadType").val());
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/download_operations_report",
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.successmessage);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            window.location = JSON.result;
                            $("#add-modal-data-bd1").modal('hide');
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
                    $("#supplier_name_report").empty();
                    $("#supplier_name_report").append(JSON.result);
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
                    $("#purchase_contract_report").attr("disabled", false);
                    $("#purchase_contract_report").empty();
                    $("#purchase_contract_report").append(JSON.result);
                } else {
                    $("#purchase_contract_report").attr("disabled", true);
                }
            }
        });
    }
</script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".from_date_report").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-5y',
            maxDate: '5y',
            onSelect: function(date) {
                $('#ui-datepicker-div table td a').attr('href', 'javascript:;');
                var selectedDate = $(".from_date_report").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $(".to_date_report").datepicker("option", "minDate", endDate);
            }
        });

        $(".to_date_report").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-5y',
            maxDate: '5y',
            onSelect: function(date) {}
        });

        $('.ui-datepicker').addClass('notranslate');
    });
</script>