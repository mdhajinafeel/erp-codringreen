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
    <input type="hidden" id="hdnDispatchIds" name="hdnDispatchIds" value="<?php echo $dispatchids; ?>">
    <input type="hidden" id="hdnProductTypeId" name="hdnProductTypeId" value="<?php echo $product_type_id ?>">

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <input type="text" id="sa_number" name="sa_number" class="form-control text-uppercase" value="<?php echo isset($export_details[0]->sa_number) ? $export_details[0]->sa_number : ''; ?>" placeholder="<?php echo $this->lang->line("sa_number"); ?>" />
            <label id="error-sanumber" class="error-text"><?php echo $this->lang->line("error_sanumber"); ?></label>
        </div>

        <div class="col-md-6">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pol_name; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="port_of_discharge"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <select class="form-control" name="port_of_discharge" id="port_of_discharge" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($exportpod as $pod) { ?>
                    <option value="<?php echo $pod->id; ?>" <?php if ($export_details[0]->pod == $pod->id) { ?> selected="selected" <?php } ?>><?php echo $pod->pod_name; ?></option>
                <?php } ?>
            </select>
            <label id="error-portofdischarge" class="error-text"><?php echo $this->lang->line("error_pod"); ?></label>
        </div>
        <div class="col-md-6">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipping_line; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="bl_number"><?php echo $this->lang->line("bl_number"); ?></label>
            <input type="text" id="bl_number" name="bl_number" class="form-control text-uppercase" value="<?php echo $export_details[0]->bl_no; ?>" placeholder="<?php echo $this->lang->line("bl_number"); ?>" />
        </div>
        <div class="col-md-6">
            <label for="bl_date"><?php echo $this->lang->line("bl_date"); ?></label>
            <input type="text" id="bl_date" name="bl_date" class="form-control text-uppercase" value="<?php echo $export_details[0]->bl_date; ?>" placeholder="<?php echo $this->lang->line("bl_date"); ?>" readonly />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="shipped_date"><?php echo $this->lang->line("shipped_date"); ?></label>
            <input type="text" id="shipped_date" name="shipped_date" class="form-control" value="<?php echo $export_details[0]->shipped_date; ?>" placeholder="<?php echo $this->lang->line("shipped_date"); ?>" readonly />
        </div>
        <div class="col-md-6">
            <label for="vessel_name"><?php echo $this->lang->line("vessel_name"); ?></label>
            <input type="text" id="vessel_name" name="vessel_name" class="form-control text-uppercase" value="<?php echo $export_details[0]->vessel_name; ?>" placeholder="<?php echo $this->lang->line("vessel_name"); ?>" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="client_pno"><?php echo $this->lang->line("client_pno"); ?></label>
            <input type="text" id="client_pno" name="client_pno" class="form-control text-uppercase" value="<?php echo $export_details[0]->client_pno; ?>" placeholder="<?php echo $this->lang->line("client_pno"); ?>" />
        </div>
        <div class="col-md-6">
            <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $this->lang->line($export_details[0]->product_type_name); ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
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

        <div class="col-md-6" id="divUploadedDetails">
            <div class="next-line">
                <label class="label-showdetails" for="lblTotalContainers" id="lblTotalContainers"><?php echo $this->lang->line("total_containers") . ": "; ?><?php echo $export_details[0]->total_containers; ?></label>
                <label class="label-showdetails" for="lblTotalPieces" id="lblTotalPieces"><?php echo $this->lang->line("total_no_of_pieces") . ": " ?><?php echo $export_details[0]->total_pieces; ?></label>
                <label class="label-showdetails" for="lblGrossVolume" id="lblGrossVolume"><?php echo $this->lang->line("total_gross_volume") . ": "; ?><?php echo $export_details[0]->total_gross_volume; ?></label>
                <label class="label-showdetails" for="lblNetVolume" id="lblNetVolume"><?php echo $this->lang->line("total_net_volume") . ": "; ?><?php echo $export_details[0]->total_net_volume; ?></label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <?php echo form_button(array("name" => "cgrerp_form_origin", "type" => "submit", "class" => "btn btn-success update_export", "content" => $pagetype == "view" ? $this->lang->line("update") : $this->lang->line("add"))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var error_sanumber = "<?php echo $this->lang->line("error_sanumber"); ?>";
    var error_sanumber_exists = "<?php echo $this->lang->line("error_sanumber_exists"); ?>";
    var error_measuremet_system = "<?php echo $this->lang->line("error_measuremet_system"); ?>";
    var error_total_pieces = "<?php echo $this->lang->line("error_total_pieces"); ?>";
    var total_pieces = "<?php echo $this->lang->line("total_no_of_pieces"); ?>";
    var total_gross_volume = "<?php echo $this->lang->line("total_gross_volume"); ?>";
    var total_net_volume = "<?php echo $this->lang->line("total_net_volume"); ?>";
    var error_total_pieces_volume = "<?php echo $this->lang->line("error_total_pieces_volume"); ?>";
    var totalPieces = <?php echo $export_details[0]->total_pieces; ?>;
    var totalGrossVolume = <?php echo $export_details[0]->total_gross_volume; ?>;
    var totalNetVolume = <?php echo $export_details[0]->total_net_volume; ?>;
    var totalContainers = <?php echo $export_details[0]->total_containers; ?>;
    var containerDataArray = [];

    $(document).ready(function() {

        $("#error-sanumber").hide();
        $("#error-portofloading").hide();
        $("#error-portofdischarge").hide();
        $("#error-shippingline").hide();
        $("#error-measurementsystem").hide();
        $("#divUploadedDetails").show();

        $("#measurement_system").change(function() {
            if ($("#measurement_system").val() == 0) {
                $("#divUploadedDetails").hide();
            } else {
                fetchExportSummaryDetails($("#measurement_system").val());
            }
        });

        $("#update_export").submit(function(e) {

            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var sanumber = $("#sa_number").val().trim();
            var portofdischarge = $("#port_of_discharge").val();
            var blnumber = $("#bl_number").val().trim();
            var bldate = $("#bl_date").val().trim();
            var shippeddate = $("#shipped_date").val().trim();
            var clientpno = $("#client_pno").val();
            var vessel_name = $("#vessel_name").val().trim();
            var measurementsystem = $("#measurement_system").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (sanumber.length == 0) {
                $("#error-sanumber").show();
                $("#error-sanumber").text(error_sanumber);
                isValid1 = false;
            } else {
                $("#error-sanumber").hide();
                isValid1 = true;
            }

            if (portofdischarge == 0) {
                $("#error-portofdischarge").show();
                isValid2 = false;
            } else {
                $("#error-portofdischarge").hide();
                isValid2 = true;
            }

            if (measurementsystem == 0) {
                $("#error-measurementsystem").show();
                $("#error-measurementsystem").text(error_measuremet_system);
                isValid3 = false;
            } else {
                $("#error-measurementsystem").hide();
                isValid3 = true;
            }

            if (isValid1 && isValid2 && isValid3) {

                if (totalPieces == 0 || totalNetVolume == 0 || totalContainers == 0) {
                    $("#error-measurementsystem").show();
                    $("#error-measurementsystem").text(error_total_pieces_volume);
                    isValid4 = false;
                } else {
                    isValid4 = true;
                    $("#error-measurementsystem").hide();
                }

                if (isValid4) {
                    var fd = new FormData(this);
                    fd.append("is_ajax", 2);
                    fd.append("form", action);
                    fd.append("add_type", "export");
                    fd.append("action_type", pagetype);

                    fd.append("exportid", $("#hdnExportId").val());
                    fd.append("sanumber", $("#hdnSaNumber").val());
                    fd.append("originid", origin);
                    fd.append("inputsanumber", sanumber);
                    fd.append("producttypeid", $("#hdnProductTypeId").val());
                    fd.append("measurementsystemid", measurementsystem);
                    fd.append("dispatchids", $("#hdnDispatchIds").val());
                    fd.append("portofdischarge", portofdischarge);
                    fd.append("blnumber", blnumber);
                    fd.append("bldate", bldate);
                    fd.append("shippeddate", shippeddate);
                    fd.append("clientpno", clientpno);
                    fd.append("vesselname", vessel_name);
                    fd.append("totalcontainers", totalContainers);
                    fd.append("totalpiecesuploaded", totalPieces);
                    fd.append("totalgrossvolume", totalGrossVolume);
                    fd.append("totalnetvolume", totalNetVolume);
                    fd.append("containerdata", JSON.stringify(containerDataArray));

                    $(".update_export").prop('disabled', false);
                    toastr.info(processing_request);

                    var obj = $(this),
                        action = obj.attr('name'),
                        form_table = obj.data('form-table');

                    $("#loading").show();
                    $.ajax({
                        type: "POST",
                        url: e.target.action,
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
                                $('.update_export').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            } else if (JSON.duplicateerror != '') {
                                $("#error-sanumber").show();
                                $("#error-sanumber").text(JSON.duplicateerror);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                toastr.clear();
                                toastr.error(JSON.duplicateerror);
                            } else {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $('.update_export').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#add-modal-data-bd").modal('hide');
                                $('#xin_table_exports').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function(jqXHR, exception) {
                            toastr.clear();
                            $('.add_export').prop('disabled', false);
                        }
                    });
                }
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

<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#shipped_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-3m",
            maxDate: "3m",
            onSelect: function(date) {}
        });

        $("#bl_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-3m",
            maxDate: "3m",
            onSelect: function(date) {}
        });
        
        $('.ui-datepicker').addClass('notranslate');
    });
</script>