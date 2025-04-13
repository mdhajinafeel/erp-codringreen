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
    <input type="hidden" id="hdnSupplierId" name="hdnSupplierId" value="<?php echo $supplierId; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originId; ?>">

    <?php foreach ($inventoryData as $inventory) {
        $invoiceNumber = $inventory->invoice_number; ?>

        <div class="row mb-3">

            <input type="hidden" id="hdnInventoryNumber" name="inventory_number[<?php echo $inventory->mapping_id; ?>]" value="<?php echo $inventory->inventory_order; ?>">

            <label class="col-sm-5 col-form-label lbl-font header-profile-menu1 fontsize" for="lblInventory" name="inventoryOrder[]" value="<?php echo $inventory->mapping_id; ?>">
                <?php echo strtoupper($inventory->inventory_order); ?>
            </label>

            <div class="col-sm-7">
                <input type="text" value="<?php echo $invoiceNumber; ?>" class="form-control text-uppercase" id="invoice_number" name="invoice_number[<?php echo $inventory->mapping_id; ?>]" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>

    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_update_invoice" id="btn_update_invoice"><?php echo $this->lang->line("update"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";

    $(document).ready(function() {

        $("#btn_update_invoice").click(function() {

            var isValid = true;
            var arrUpdateInvoiceNumber = [];

            var inventoryData = <?php echo json_encode($inventoryData); ?>;

            $.each(inventoryData, function(i, item) {

                var mappingId = item.mapping_id;
                var inventoryOrder = item.inventory_order;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedInvoiceNumber = $('input[name="invoice_number[' + mappingId + ']"]').val();

                    arrUpdateInvoiceNumber.push({
                        mappingid: mappingId,
                        inventoryorder: inventoryOrder,
                        updatedinvoicenumber: updatedInvoiceNumber
                    });
                }
            });

            if (arrUpdateInvoiceNumber.length > 0) {
                var fd = new FormData();
                fd.append("contractId", $("#hdnContractId").val());
                fd.append("supplierId", $("#hdnSupplierId").val());
                fd.append("updateInvoiceNumberData", JSON.stringify(arrUpdateInvoiceNumber));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.clear();
                toastr.info(processing_request);
                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/update_inventory_invoice_number",
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
                            $("#add-modal-data-bd1").modal('hide');
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
            }
        });
    });
</script>