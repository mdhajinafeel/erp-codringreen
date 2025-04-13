<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'generate_export_summary_report', 'id' => 'generate_export_summary_report', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open("", $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originid; ?>">
    <input type="hidden" id="hdnDispatchIds" name="hdnDispatchIds" value="<?php echo $dispatchids; ?>">

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $shippingline; ?></label>
            </div>
        </div>

        <div class="col-md-6">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $warehouse; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="port_of_loading"><?php echo $this->lang->line("product_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $producttype; ?></label>
            </div>
        </div>
        <div class="col-md-6 ">
            <label for="measurement_system"><?php echo $this->lang->line("measuremet_system"); ?></label>
            <select class="form-control" name="measurement_system" id="measurement_system" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($measurementsystems as $measurementsystem) { ?>
                    <option value="<?php echo $measurementsystem->measurement_id; ?>"><?php echo $measurementsystem->measurement_name; ?></option>
                <?php } ?>
            </select>
            <label id="error-measurementsystem" class="error-text"><?php echo $this->lang->line("error_measuremet_system"); ?></label>
        </div>
    </div>
    
    <?php if($originid == 1 || $originid == 2) { ?>
    
    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="circumference_allowance"><?php echo $this->lang->line("circumference_allowance"); ?></label>
            <input type="number" id="circumference_allowance" name="circumference_allowance" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("circumference_allowance"); ?>" />
        </div>
        <div class="col-md-6 mb-2">
            <label for="length_allowance"><?php echo $this->lang->line("length_allowance"); ?></label>
            <input type="number" id="length_allowance" name="length_allowance" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("length_allowance"); ?>" />
        </div>
    </div>
    
    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="circumference_adjustment"><?php echo $this->lang->line("circumference_adjustment"); ?></label>
            <input type="number" id="circumference_adjustment" name="circumference_adjustment" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("circumference_adjustment"); ?>" />
        </div>
    </div>
    <? } ?>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <button type="button" id="btn_generate_summary_report" name="btn_generate_summary_report" class="btn btn-success"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-measurementsystem").hide();

        $("#btn_generate_summary_report").click(function() {

            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var dispatchids = $("#hdnDispatchIds").val();
            var measurementsystem = $("#measurement_system").val();
            var circumferenceallowance = $("#circumference_allowance").val();
            var lengthallowance = $("#length_allowance").val();
            var circumferenceadjustment = $("#circumference_adjustment").val();

            var isValid1 = true;

            if (measurementsystem == 0) {
                $("#error-measurementsystem").show();
                isValid1 = false;
            } else {
                $("#error-measurementsystem").hide();
                isValid1 = true;
            }
            
            if (circumferenceallowance == "") {
                circumferenceallowance = 0;
            }
            
            if (lengthallowance == "") {
                lengthallowance = 0;
            }
            
            if (circumferenceadjustment == "") {
                circumferenceadjustment = 0;
            }

            if (isValid1) {
                $("#loading").show();
                var fd = new FormData();
                fd.append("type", "generate_summary_report");
                fd.append("dispatchids", dispatchids);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("originid", origin);
                fd.append("measurementsystem", measurementsystem);
                fd.append("circumferenceallowance", circumferenceallowance);
                fd.append("lengthallowance", lengthallowance);
                fd.append("circumferenceadjustment", circumferenceadjustment);

                $.ajax({
                    url: base_url + "/generate_export_summary_report",
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#loading").hide();
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            $("#add-modal-data-bd").modal('hide');
                            toastr.success(response.successmessage);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            window.location = response.result;
                            //wait(3000);
                            //deletefilesfromfolder();
                        }
                    }
                });
            }
        });
    });
</script>