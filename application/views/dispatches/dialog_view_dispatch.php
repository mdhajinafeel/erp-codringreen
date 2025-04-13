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
<?php echo form_open($dispatch_submit, $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnDispatchId" name="hdnDispatchId" value="<?php echo $dispatchid; ?>">
    <input type="hidden" id="hdnContainerNumber" name="hdnContainerNumber" value="<?php echo $containernumber; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $dispatch_details[0]->origin_id; ?>">
    <input type="hidden" id="hdnExportStatus" name="hdnExportStatus" value="<?php echo $dispatch_details[0]->isexport; ?>">
    <input type="hidden" id="hdnWHId" name="hdnWHId" value="<?php echo $dispatch_details[0]->warehouse_id; ?>">
    <input type="hidden" id="hdnDispatchDate" name="hdnDispatchDate" value="<?php echo $dispatch_details[0]->dispatch_date; ?>">
    <input type="hidden" id="hdnShippingId" name="hdnShippingId" value="<?php echo $dispatch_details[0]->shipping_line_id; ?>">

    <div class="row">
        <div class="col-md-6 mb-2">
            <label for="container_number"><?php echo $this->lang->line("container_number"); ?></label>
            <?php if ($dispatch_details[0]->isexport == 0) { ?>
                <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("container_number"); ?>" name="container_number" id="container_number" type="text" value="<?php echo isset($dispatch_details[0]->container_number) ? $dispatch_details[0]->container_number : ''; ?>" />
                <label id="error-viewcontainernumber" class="error-text"><?php echo $this->lang->line("error_containernumber"); ?></label>
            <?php } else { ?>
                <div class="input-group">
                    <label class="control-label"><?php echo isset($dispatch_details[0]->container_number) ? $dispatch_details[0]->container_number : ''; ?></label>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("shipping_line"); ?></label>
            <?php if ($dispatch_details[0]->isexport == 0) { ?>
                <select class="form-control" name="shipping_line" id="shipping_line" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>

                    <?php foreach ($shippinglines as $shippingline) { ?>
                        <option value="<?php echo $shippingline->id; ?>" <?php if ($dispatch_details[0]->shipping_line_id == $shippingline->id) : ?> selected="selected" <?php endif; ?>><?php echo $shippingline->shipping_line; ?></option>
                    <?php } ?>
                </select>
                <label id="error-shippingline" class="error-text"><?php echo $this->lang->line("error_shipping_line"); ?></label>
            <?php } else { ?>
                <div class="input-group">
                    <label class="control-label"><?php echo isset($dispatch_details[0]->shipping_line) ? $dispatch_details[0]->shipping_line : ''; ?></label>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("warehouse"); ?></label>
            <?php if ($dispatch_details[0]->isexport == 0) { ?>
                <select class="form-control" name="wh_name" id="wh_name" data-plugin="select_erp" disabled="<?php echo $dispatch_details[0]->isexport == 1 ? 'disabled' : ''; ?>">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>

                    <?php foreach ($warehouses as $warehouse) { ?>
                        <option value="<?php echo $warehouse->whid; ?>" <?php if ($dispatch_details[0]->warehouse_id == $warehouse->whid) : ?> selected="selected" <?php endif; ?>><?php echo $warehouse->warehouse_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-warehouse" class="error-text"><?php echo $this->lang->line("error_warehouse_farm"); ?></label>
            <?php } else { ?>
                <div class="input-group">
                    <label class="control-label"><?php echo isset($dispatch_details[0]->warehouse_name) ? $dispatch_details[0]->warehouse_name : ''; ?></label>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("product_title"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $dispatch_details[0]->product_name . ' - ' . $this->lang->line($dispatch_details[0]->product_type_name); ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("dispatch_date"); ?></label>
            <?php if ($dispatch_details[0]->isexport == 0) { ?>
                <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("dispatch_date"); ?>" name="dispatcheddate" id="dispatcheddate" type="text" value="<?php echo isset($dispatch_details[0]->dispatch_date) ? $dispatch_details[0]->dispatch_date : ''; ?>" readonly>
            <?php } else { ?>
                <div class="input-group">
                    <label class="control-label"><?php echo isset($dispatch_details[0]->dispatch_date) ? $dispatch_details[0]->dispatch_date : ''; ?></label>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-6">
            <label for="seal_number"><?php echo $this->lang->line("seal_number"); ?></label>
                <input type="text" id="seal_number" name="seal_number" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("seal_number"); ?>" value="<?php echo isset($dispatch_details[0]->seal_number) ? $dispatch_details[0]->seal_number : ''; ?>" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label for="container_image_url"><?php echo $this->lang->line("container_image_url"); ?></label>
                <textarea name="container_image_url" id="container_image_url" maxlength="500" rows="3" class="form-control" placeholder="<?php echo $this->lang->line("container_image_url"); ?>"><?php echo isset($dispatch_details[0]->container_pic_url) ? htmlspecialchars($dispatch_details[0]->container_pic_url) : ''; ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("upload_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->is_special_uploaded) ? $dispatch_details[0]->is_special_uploaded : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->total_pieces) ? $dispatch_details[0]->total_pieces : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("total_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->total_volume) ? ($dispatch_details[0]->total_volume + 0) : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("uploaded_by"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->uploadedby) ? $dispatch_details[0]->uploadedby : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("origin"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->origin) ? $dispatch_details[0]->origin : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("status"); ?></label>
            <div class="input-group">
                <?php if ($dispatch_details[0]->isexport == 1) {
                    $status = "exported"; ?>
                <?php } else if ($dispatch_details[0]->isclosed == 1) {
                    $status = "closed";
                } else {
                    $status = "open";
                } ?>
                <label class="control-label"><?php echo $this->lang->line($status); ?></label>
            </div>
        </div>
        <?php if ($dispatch_details[0]->isexport == 1) { ?>
            <!--<div class="col-md-6">-->
            <!--    <label class="head-label"><?php echo $this->lang->line("sa_number"); ?></label>-->
            <!--    <div class="input-group">-->
            <!--        <label class="control-label"><?php echo "" ?></label>-->
            <!--    </div>-->
            <!--</div>-->
        <?php } ?>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>

    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-primary action_button', 'content' => $this->lang->line("update"))); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    var error_containernumber = "<?php echo $this->lang->line("error_containernumber"); ?>";

    $("#error-viewcontainernumber").hide();
    $("#error-shippingline").hide();
    $("#error-warehouse").hide();

    $(document).ready(function() {

        $("#update").submit(function(e) {

            e.preventDefault();

            var exportStatus = $("#hdnExportStatus").val();

            var pagetype = $("#pagetype").val().trim();
            var dispatchid = $("#hdnDispatchId").val();
            var originid = $("#hdnOriginId").val().trim();

            if(exportStatus == 1) {
                var containernumber = $("#hdnContainerNumber").val().trim();
                var inputcontainernumber = $("#hdnContainerNumber").val().trim();
                var inputwarehouse = $("#hdnWHId").val();
                var inputdispatcheddate = $("#hdnDispatchDate").val().trim();
                var inputshippingline = $("#hdnShippingId").val();
            } else {
                var containernumber = $("#hdnContainerNumber").val().trim();
                var inputcontainernumber = $("#container_number").val().trim();
                var inputwarehouse = $("#wh_name").val();
                var inputdispatcheddate = $("#dispatcheddate").val().trim();
                var inputshippingline = $("#shipping_line").val();
            }

            var inputsealnumber = $("#seal_number").val().trim();
            var inputcontainerimageurl = $("#container_image_url").val().trim();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true;

            if (inputcontainernumber.length == 0) {
                $("#error-viewcontainernumber").show();
                isValid1 = false;
            } else {
                $("#error-viewcontainernumber").hide();
                isValid1 = true;
            }

            if (inputwarehouse == 0) {
                $("#error-warehouse").show();
                isValid2 = false;
            } else {
                $("#error-warehouse").hide();
                isValid2 = true;
            }

            if (inputshippingline == 0) {
                $("#error-shippingline").show();
                isValid3 = false;
            } else {
                $("#error-shippingline").hide();
                isValid3 = true;
            }

            if (isValid1 && isValid2 && isValid3) {
                var fd = new FormData(this);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "dispatch");
                fd.append("action_type", pagetype);
                fd.append("dispatch_id", dispatchid);
                fd.append("origin_id", originid);
                fd.append("container_number", containernumber);
                fd.append("input_container_number", inputcontainernumber);
                fd.append("warehouse_id", inputwarehouse);
                fd.append("shippingline_id", inputshippingline);
                fd.append("sealnumber", inputsealnumber);
                fd.append("dispatched_date", inputdispatcheddate);
                fd.append("containerimageurl", inputcontainerimageurl);

                $(".action_button").prop('disabled', true);
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
                            $('.action_button').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.action_button').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data").modal('hide');

                            $('#xin_table_dispatches').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });
    $(function() {
        $("#dispatcheddate").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1y',
            maxDate: '0d',
            onSelect: function(date) {
                //$("#error-purchasedate").hide();
            }
        });
        $('.ui-datepicker').addClass('notranslate');
    });
</script>