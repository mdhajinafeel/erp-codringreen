<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update', 'id' => 'update', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open("", $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originId; ?>">

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="supplier_name_report"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name_report" id="supplier_name_report" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php foreach ($suppliers as $supplier) { ?>
                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3" id="divICA">
        <label for="inventory_order"><?php echo $this->lang->line('inventory_order'); ?></label>
        <select class="form-control" name="inventory_order[]" id="inventory_order" data-plugin="select_erp" multiple>
        </select>
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
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_download_liquidation" id="btn_download_liquidation"><?php echo $this->lang->line("download"); ?></button>
</div>
<?php echo form_close(); ?>

<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>
<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var selecttext = "<?php echo $this->lang->line("select"); ?>";


    $("#error-fromdate").hide();
    $("#error-todate").hide();
    $("#divICA").hide();

    $(document).ready(function() {

        $("#supplier_name_report").change(function() {

            $("#divICA").hide();

            fetchInventoryOrderBySupplier($("#supplier_name_report").val());
        });

        $("#btn_download_liquidation").click(function() {
            var fromdate = $("#from_date_report").val();
            var todate = $("#to_date_report").val();
            var supplierid = $("#supplier_name_report").val();
            var farmid = $("#inventory_order").val();

            var isValid1 = true,
                isValid2 = true;

            if (fromdate.length == 0) {
                $("#error-fromdate").show();
                isValid1 = false;
            } else {
                $("#error-fromdate").hide();
                isValid1 = true;
            }

            if (todate.length == 0) {
                $("#error-todate").show();
                isValid2 = false;
            } else {
                $("#error-todate").hide();
                isValid2 = true;
            }

            if (isValid1 && isValid2) {

                var fd = new FormData();
                fd.append("supplierId", supplierid);
                fd.append("fromDate", fromdate);
                fd.append("toDate", todate);
                fd.append("farmId", farmid);
                fd.append("originId", $("#hdnOriginId").val());
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/download_liquidation_report_bulk",
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
                            wait(3000);
                            deletefilesfromfolder();
                            $("#add-modal-data-bd1").modal('hide');
                        }
                    }
                });
            }
        });

    });

    function fetchInventoryOrderBySupplier(supplierid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_inventory_order?supplier_id=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#inventory_order").multiselect("destroy");
                    $("#inventory_order").append(JSON.result);

                    $('select[multiple]').multiselect({
                        placeholder: selecttext,
                        search: true,
                        selectAll: false,
                    });
                    

                    if (supplierid > 0) {
                        $("#divICA").show();
                    } else {
                        $("#divICA").hide();
                    }
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