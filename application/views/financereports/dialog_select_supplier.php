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
    <input type="hidden" id="hdnContractId" name="hdnContractId" value="<?php echo $contractId; ?>">
    <input type="hidden" id="hdnContractCode" name="hdnContractCode" value="<?php echo $contractCode; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originId; ?>">

    <div class="row mb-3">

        <label class="col-sm-3 col-form-label lbl-font" for="supplier_name"><?php echo $this->lang->line('supplier_name'); ?></label>
        <div class="col-sm-9">
            <select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line('select'); ?></option>
                <?php foreach ($suppliers as $supplier) { ?>
                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>

    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_generate_liquidation" id="btn_generate_liquidation"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";

    $(document).ready(function() {

        $("#btn_generate_liquidation").click(function() {

            var fd = new FormData();
            fd.append("contractId", $("#hdnContractId").val());
            fd.append("contractCode", $("#hdnContractCode").val());
            fd.append("supplierId", $("#supplier_name").val());
            fd.append("originId", $("#hdnOriginId").val());
            fd.append("csrf_cgrerp", $("#hdnCsrf").val());
            fd.append("type", "generate_liquidation");

            toastr.clear();
            toastr.info(processing_request);
            $("#loading").show();
            $.ajax({
                type: "POST",
                url: base_url + "/generate_fieldpurchase_liquidation_report",
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
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
    });
</script>