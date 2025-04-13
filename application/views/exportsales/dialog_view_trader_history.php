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

    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->sa_number; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pol_name; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="port_of_discharge"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="no_of_fcls"><?php echo $this->lang->line("no_of_fcls"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->d_total_containers; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <label for="total_no_of_pieces"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces; ?></label>
            </div>
        </div>

        <?php if ($originid == 3) { ?>

            <div class="col-md-3 mb-2">
                <label for="total_net_weight"><?php echo $this->lang->line("total_net_weight"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_net_weight + 0; ?></label>
                </div>
            </div>

        <?php } else { ?>

            <div class="col-md-3 mb-2">
                <label for="total_gross_volume"><?php echo $this->lang->line("total_gross_volume"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_gross_volume + 0; ?></label>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label for="total_net_volume"><?php echo $this->lang->line("total_net_volume"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_net_volume + 0; ?></label>
                </div>
            </div>

        <?php } ?>
    </div>

    <?php if (count($invoiceHistory) > 0) { ?>

        <div class="row mb-4">
            <div class="tab-content">
                <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-ec0fa1e3-6325-4caf-a468-7691ef065d01" id="dom-ec0fa1e3-6325-4caf-a468-7691ef065d01">
                    <div class="accordion" id="accordionExample">

                        <?php foreach ($invoiceHistory as $invoice) { ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" style="display: inline-grid !important;" type="button" data-bs-toggle="collapse" data-bs-target="#invoice_<?php echo $invoice->id; ?>" aria-expanded="true" aria-controls="collapse1">
                                        <div class="row mb-2">
                                            <div class="col-md-2 mb-2">
                                                <label for="invoice_date"><?php echo $this->lang->line("invoice_date"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo $invoice->invoice_date; ?></label>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <label for="seller_name_invoice"><?php echo $this->lang->line("seller_name_invoice"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo $invoice->seller_name; ?></label>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <label for="buyer_name_invoice"><?php echo $this->lang->line("buyer_name_invoice"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo $invoice->buyer_name; ?></label>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <label for="bank_name_invoice"><?php echo $this->lang->line("bank_name_invoice"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo $invoice->bank_name; ?></label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 mb-2 mt-2">
                                                <label for="total_invoice_value"><?php echo $this->lang->line("total_sales_value"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->total_sales_value); ?></label>
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-2 mt-2">
                                                <label for="total_invoice_value"><?php echo $this->lang->line("total_invoice_value"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->total_invoice_value); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div class="accordion-collapse collapse" id="invoice_<?php echo $invoice->id; ?>" aria-labelledby="heading1" data-bs-parent="#accordionExample" style="padding: 20px;">

                                    <?php if($originid == 1 || $originid == 2) { ?>
                                    
                                        <div class="row mb-2">
                                            <label for="base_price"><u><?php echo $this->lang->line("base_price"); ?></u></label>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3 mb-2">
                                                <label for="price_shorts"><?php echo $this->lang->line("text_shorts"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo "$" . sprintf('%.2f',$invoice->shorts_base_price); ?></label>
                                                </div>

                                                <input class="form-check-input" id="enablejumpprice_shorts" name="enablejumpprice_shorts" type="checkbox" value="1" <?php if ($invoice->enabled_jump_shorts == 1) {
                                                                                                                                                                        echo "checked";
                                                                                                                                                                    } else {
                                                                                                                                                                        "";
                                                                                                                                                                    } ?> disabled>
                                                <label for="enablejumpprice_shorts"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label for="price_semi"><?php echo $this->lang->line("text_semi"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->semi_base_price); ?></label>
                                                </div>

                                                <input class="form-check-input" id="enablejumpprice_semi" name="enablejumpprice_semi" type="checkbox" value="1" <?php if ($invoice->enabled_jump_semi == 1) {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                } else {
                                                                                                                                                                    "";
                                                                                                                                                                } ?> disabled>
                                                <label for="enablejumpprice_semi"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label for="price_longs"><?php echo $this->lang->line("text_longs"); ?></label>
                                                <div class="input-group">
                                                    <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->long_base_price); ?></label>
                                                </div>

                                                <input class="form-check-input" id="enablejumpprice_longs" name="enablejumpprice_longs" type="checkbox" value="1" <?php if ($invoice->enabled_jump_long == 1) {
                                                                                                                                                                        echo "checked";
                                                                                                                                                                    } else {
                                                                                                                                                                        "";
                                                                                                                                                                    } ?> disabled>
                                                <label for="enablejumpprice_longs"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                                            </div>
                                        </div>

                                    <?php } ?>

                                    <div class="row mb-2">

                                        <div class="col-md-2 mb-2">
                                            <label for="service_sales"><?php echo $this->lang->line("service_sales"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo sprintf("%.2f",$invoice->service_sales_percentage) . "%"; ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label for="total_service_cost"><?php echo $this->lang->line("total_service_cost"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->total_service_cost); ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label for="sales_advance"><?php echo $this->lang->line("sales_advance"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->advance_cost); ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label for="total_advance_cost"><?php echo $this->lang->line("total_advance_cost"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->total_advance_cost); ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label for="total_claim_cost"><?php echo $this->lang->line("total_claim_cost"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->claim_amount); ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label for="total_invoice_value"><?php echo $this->lang->line("total_invoice_value"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->total_invoice_value); ?></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label for="transfer_price"><?php echo $this->lang->line("transfer_price"); ?></label>
                                            <div class="input-group">
                                                <label class="control-label"><?php echo "$" . sprintf("%.2f",$invoice->tp_cost); ?></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">

                                        <div class="mb-2" style="display: flex; justify-content: end;">

                                            <div class="form-check" style="display: flex; align-items: center; margin: 10px;padding-right: 20px;">
                                                <input class="form-check-input" id="accountinginvoice_<?php echo $invoice->id; ?>" name="accountinginvoice_<?php echo $invoice->id; ?>" type="checkbox" value="1" <?php if ($invoice->accounting_invoice == 1) {
                                                                                                                                                                                                                        echo "checked";
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        echo "";
                                                                                                                                                                                                                    } ?>>
                                                <label for="accountinginvoice_<?php echo $invoice->id; ?>"><?php echo $this->lang->line('accounting_invoice'); ?></label>
                                            </div>

                                            <button type="button" id="btngenerateproformainvoice_<?php echo $invoice->id; ?>" name="btngenerateproformainvoice_<?php echo $invoice->id; ?>" class="btn btn-success btn_generate_invoice"><?php echo $this->lang->line("generate"); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>

        <div class="col-md-12 mb-4 div-no-data">
            <label class="control-label"><?php echo $this->lang->line("no_data_available"); ?></label>
        </div>
    <?php } ?>
</div>

<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    var selecttext = "<?php echo $this->lang->line("select"); ?>";
    $(document).ready(function() {

        $('select[multiple]').multiselect({
            placeholder: selecttext,
            search: true,
            selectAll: false,
        });

        $("#error-buyername").hide();
        $("#error-bankname").hide();

        $(".btn_generate_invoice").click(function() {
            var buttonId = this.id;
            var buttonInvoiceId = buttonId.split("_")[1];

            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var sanumber = $("#hdnSaNumber").val();
            var exportid = $("#hdnExportId").val();

            var fd = new FormData();
            fd.append("type", "generateinvoice");
            fd.append("csrf_cgrerp", $("#hdnCsrf").val());
            fd.append("originid", origin);
            fd.append("sanumber", sanumber);
            fd.append("exportid", exportid);
            fd.append("invoiceId", buttonInvoiceId);

            if ($("#accountinginvoice_" + buttonInvoiceId).is(":checked") == true) {
                fd.append("accountingInvoice", 1);
            } else {
                fd.append("accountingInvoice", 0);
            }

            $("#loading").show();
            toastr.info(processing_request);
            $.ajax({
                url: base_url + "/generate_invoice_from_history",
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
                        //$("#add-modal-data-xl").modal('hide');
                        toastr.success(response.successmessage);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        window.location = response.result;
                        wait(3000);
                        deletefilesfromfolder();
                    }
                }
            });

        });
    });
</script>