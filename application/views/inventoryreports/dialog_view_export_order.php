<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update_export', 'id' => 'update_export', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open($formsubmit, $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnExportId" name="hdnExportId" value="<?php echo $exportid; ?>">
    <input type="hidden" id="hdnSaNumber" name="hdnSaNumber" value="<?php echo $sanumber; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originid; ?>">
    <input type="hidden" id="hdnProductTypeId" name="hdnProductTypeId" value="<?php echo $product_type_id ?>">

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->sa_number; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pol_name; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="port_of_discharge"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipping_line; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="bl_number"><?php echo $this->lang->line("bl_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->bl_no; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="bl_date"><?php echo $this->lang->line("bl_date"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->bl_date; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="shipped_date"><?php echo $this->lang->line("shipped_date"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipped_date; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="vessel_name"><?php echo $this->lang->line("vessel_name"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->vessel_name; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="client_pno"><?php echo $this->lang->line("client_pno"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->client_pno; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $this->lang->line($export_details[0]->product_type_name); ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("no_of_fcls"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_containers; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("total_gross_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_gross_volume; ?></label>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("total_net_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_net_volume; ?></label>
            </div>
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
    <?php } ?>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="circumference_adjustment"><?php echo $this->lang->line("circumference_adjustment"); ?></label>
            <input type="number" id="circumference_adjustment" name="circumference_adjustment" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("circumference_adjustment"); ?>" />
        </div>
        <div class="col-md-6 mb-2">
            <label for="measurement_system"><?php echo $this->lang->line("measuremet_system"); ?></label>
            <select class="form-control" name="measurement_system" id="measurement_system" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($measurementsystems as $measurementsystem) { ?>
                    <option value="<?php echo $measurementsystem->measurement_id; ?>" <?php if ($export_details[0]->measurement_system == $measurementsystem->measurement_id) { ?> selected="selected" <?php } ?>><?php echo $measurementsystem->measurement_name; ?></option>
                <?php } ?>
            </select>
            <label id="error-measurementsystem" class="error-text"><?php echo $this->lang->line("error_measuremet_system"); ?></label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <button type="button" id="btn_generate_export_report" name="btn_generate_export_report" class="btn btn-success"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

    $(document).ready(function() {

        $("#error-measurementsystem").hide();

        $("#btn_generate_export_report").click(function() {

            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var sanumber = $("#hdnSaNumber").val();
            var producttypeid = $("#hdnProductTypeId").val();
            var exportid = $("#hdnExportId").val();
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
                fd.append("type", "generate_order_report");
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("originid", origin);
                fd.append("sanumber", sanumber);
                fd.append("producttypeid", producttypeid);
                fd.append("exportid", exportid);
                fd.append("measurementsystem", measurementsystem);
                fd.append("circumferenceallowance", circumferenceallowance);
                fd.append("lengthallowance", lengthallowance);
                fd.append("circumferenceadjustment", circumferenceadjustment);

                toastr.info(processing_request);
                $.ajax({
                    url: base_url + "/generate_export_order_report",
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#loading").hide();
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.clear();
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            toastr.clear();
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

    function fetchExportSummaryDetails(measurementId) {

        totalPieces = 0;
        totalGrossVolume = 0;
        totalNetVolume = 0;
        totalContainers = 0;
        containerDataArray = [];

        var fd = new FormData();
        fd.append("dispatchIds", $("#hdnDispatchIds").val());
        fd.append("originId", $("#hdnOriginId").val());
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("productTypeId", $("#hdnProductTypeId").val());
        fd.append("measurementId", measurementId);

        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_export_summary_details",
            cache: false,
            method: "POST",
            data: fd,
            contentType: false,
            processData: false,
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    toastr.clear();
                    $("#lblTotalPieces").text(total_pieces + ": " + JSON.result["totalPieces"]);
                    $("#lblGrossVolume").text(total_gross_volume + ": " + JSON.result["totalGrossVolume"]);
                    $("#lblNetVolume").text(total_net_volume + ": " + JSON.result["totalNetVolume"]);

                    totalContainers = JSON.result["totalContainers"];
                    totalPieces = JSON.result["totalPieces"];
                    totalNetVolume = JSON.result["totalNetVolume"];
                    totalGrossVolume = JSON.result["totalGrossVolume"];
                    containerDataArray = JSON.result["dataContainers"];

                    $("#divUploadedDetails").show();
                } else {
                    toastr.clear();
                    toastr.error(JSON.error);
                    $("#divUploadedDetails").hide();
                }
            }
        });
    }
</script>