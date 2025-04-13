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
            <label for="product_type"><?php echo $this->lang->line("no_of_fcls"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_containers; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces; ?></label>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <label for="total_net_weight"><?php echo $this->lang->line("total_net_weight"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_net_weight + 0; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="buyer"><?php echo $this->lang->line("buyer_name"); ?></label>
            <select class="form-control" name="buyer_name" id="buyer_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($buyers as $buyer) { ?>
                    <option value="<?php echo $buyer->id; ?>" <?php if ($export_details[0]->invoice_buyer_id == $buyer->buyer_name) { ?> selected="selected" <?php } ?>><?php echo $buyer->buyer_name; ?></option>
                <?php } ?>
            </select>
            <label id="error-buyername" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
        </div>
        <div class="col-md-6 mb-2">
            <div class="input-group">
                <textarea name="buyer_name_details" id="buyer_name_details" maxlength="400" rows="8" class="form-control" disabled readonly></textarea>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="buyer"><?php echo $this->lang->line("bank_name"); ?></label>
            <select class="form-control" name="bank_name" id="bank_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($banks as $bank) { ?>
                    <option value="<?php echo $bank->id; ?>" <?php if ($export_details[0]->invoice_bank_id == $bank->bank_name) { ?> selected="selected" <?php } ?>><?php echo $bank->bank_name; ?></option>
                <?php } ?>
            </select>
            <label id="error-bankname" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
        </div>
        <div class="col-md-6 mb-2">
            <div class="input-group">
                <textarea name="bank_name_details" id="bank_name_details" maxlength="400" rows="8" class="form-control" disabled readonly></textarea>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <button type="button" id="btn_generate_proforma_invoice" name="btn_generate_proforma_invoice" class="btn btn-success"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-buyername").hide();
        $("#error-bankname").hide();

        $("#btn_generate_proforma_invoice").click(function() {

            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var sanumber = $("#hdnSaNumber").val();
            var producttypeid = $("#hdnProductTypeId").val();
            var exportid = $("#hdnExportId").val();

            var isValid1 = true;

            if (isValid1) {
                $("#loading").show();
                var fd = new FormData();
                fd.append("type", "generateinvoice");
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("originid", origin);
                fd.append("sanumber", sanumber);
                fd.append("exportid", exportid);
                fd.append("buyerid", $("#buyer_name").val());
                fd.append("bankid", $("#bank_name").val());

                toastr.info(processing_request);
                $.ajax({
                    url: base_url + "/generate_proforma_invoice",
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
                            wait(3000);
                            deletefilesfromfolder();
                        }
                    }
                });
            }
        });

        $("#buyer_name").change(function() {
            if ($("#buyer_name").val() == 0) {
                $("#buyer_name_details").text("");
            } else {
                fetchBuyerDetails($("#buyer_name").val());
            }
        });

        $("#bank_name").change(function() {
            if ($("#bank_name").val() == 0) {
                $("#bank_name_details").text("");
            } else {
                fetchBankDetails($("#bank_name").val());
            }
        });
    });

    function fetchBuyerDetails(buyerid) {

        var fd = new FormData();
        fd.append("originId", $("#hdnOriginId").val());
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("buyerId", buyerid);

        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_buyer_details",
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
                    $("#buyer_name_details").text(JSON.result);
                } else {
                    toastr.clear();
                    toastr.error(JSON.error);
                }
            }
        });
    }

    function fetchBankDetails(bankid) {

        var fd = new FormData();
        fd.append("originId", $("#hdnOriginId").val());
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("bankId", bankid);

        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_bank_details",
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
                    $("#bank_name_details").text(JSON.result);
                } else {
                    toastr.clear();
                    toastr.error(JSON.error);
                }
            }
        });
        }
</script>