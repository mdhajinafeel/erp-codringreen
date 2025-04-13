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
    <input type="hidden" id="hdnProcessId" name="hdnProcessId" value="<?php echo $processId; ?>">
    <input type="hidden" id="hdnDownloadtype" name="hdnDownloadtype" value="<?php echo $downloadType; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originId; ?>">

    <?php foreach ($exportcontainers as $exportcontainer) {
        $exportCost = "";
        $freightCost = "";
        $exchangeRate = "";
        $salesCost = ""; ?>
        <div class="row mb-3">

            <?php foreach ($costSummaryData as $costsummary) {
                 if (
                     $costsummary->dispatch_id == ($exportcontainer->dispatch_id + 0)
                 ) {
                    $exportCost = $costsummary->export_cost;
                    $freightCost = $costsummary->freight_cost;
                    $exchangeRate = $costsummary->tasa_cost;
                    $salesCost = $costsummary->sales_cost;
                }
            } ?>

            <input type="hidden" id="hdnContainerNumber" name="container_number[<?php echo $exportcontainer->dispatch_id; ?>]" value="<?php echo $exportcontainer->container_number; ?>">
            <input type="hidden" id="hdnCftValue" name="cft_value[<?php echo $exportcontainer->dispatch_id; ?>]" value="<?php echo ($exportcontainer->cft_value + 0); ?>">

            <label class="col-sm-4 col-form-label lbl-font header-profile-menu1 fontsize" for="lblcontainer" name="dispatchids[]" value="<?php echo $exportcontainer->dispatch_id; ?>">
                <?php if ($exportcontainer->product_type_id == 1 || $exportcontainer->product_type_id == 3) { ?>
                    <?php echo strtoupper($exportcontainer->container_number); ?>
                <?php } else if ($exportcontainer->product_type_id == 2 || $exportcontainer->product_type_id == 4) { ?>
                    <?php echo strtoupper($exportcontainer->container_number) . " (" . $this->lang->line("text_cft") . ": " . ($exportcontainer->cft_value + 0) . ")"; ?>
                <?php } ?>
            </label>

            <div class="col-sm-2">
                <label class="col-form-label lbl-font" for="export_cost"><?php echo $this->lang->line("export_cost") . " (" . $currencycode . ")"; ?></label>
                <input type="number" value="<?php echo ($exportCost + 0); ?>" step="any" class="form-control" id="export_cost" name="export_cost[<?php echo $exportcontainer->dispatch_id; ?>]" placeholder="<?php echo $this->lang->line("export_cost") . " (" . $currencycode . ")"; ?>" />
            </div>
            <div class="col-sm-2">
                <label class="col-form-label lbl-font" for="freight_cost"><?php echo $this->lang->line("freight_cost") . " (" . $currencycode . ")"; ?></label>
                <input type="number" value="<?php echo ($freightCost + 0); ?>" step="any" class="form-control" id="freight_cost" name="freight_cost[<?php echo $exportcontainer->dispatch_id; ?>]" placeholder="<?php echo $this->lang->line("freight_cost") . " (" . $currencycode . ")"; ?>" />
            </div>
            <div class="col-sm-2">
                <label class="col-form-label lbl-font" for="exchange_rate"><?php echo $this->lang->line("exchange_rate") . " (" . $currencycode . ")"; ?></label>
                <input type="number" value="<?php echo ($exchangeRate + 0); ?>" step="any" class="form-control" id="exchange_rate" name="exchange_rate[<?php echo $exportcontainer->dispatch_id; ?>]" placeholder="<?php echo $this->lang->line("exchange_rate") . " (" . $currencycode . ")"; ?>" />
            </div>
            <div class="col-sm-2">
                <label class="col-form-label lbl-font" for="sales_cost"><?php echo $this->lang->line("sales_cost"); ?></label>
                <input type="number" value="<?php echo ($salesCost + 0); ?>" step="any" class="form-control" id="sales_cost" name="sales_cost[<?php echo $exportcontainer->dispatch_id; ?>]" placeholder="<?php echo $this->lang->line("sales_cost"); ?>" />
            </div>
        </div>
    <?php } ?>

    <label class="mt-4 note-text"><?php echo $this->lang->line("export_note"); ?></label>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>

    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_export_report" id="btn_export_report"><?php echo $this->lang->line("export_report"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";

    $(document).ready(function() {

        $("#btn_export_report").click(function() {

            var isValid = true;
            var arrDispatchIds = [];

            var exportcontainers = <?php echo json_encode($exportcontainers); ?>;

            $.each(exportcontainers, function(i, item) {
                var dispatchId = item.dispatch_id;
                if (dispatchId != null && dispatchId != '' && dispatchId != undefined && dispatchId > 0) {

                    var exportCost = $('input[name="export_cost[' + dispatchId + ']"]').val();
                    var freightCost = $('input[name="freight_cost[' + dispatchId + ']"]').val();
                    var exchangeRate = $('input[name="exchange_rate[' + dispatchId + ']"]').val();
                    var salesCost = $('input[name="sales_cost[' + dispatchId + ']"]').val();
                    var containerNumber = $('input[name="container_number[' + dispatchId + ']"]').val();
                    var cftValue = $('input[name="cft_value[' + dispatchId + ']"]').val();


                    var exportCostValue = 0;
                    var exportCostEnabled = false;
                    var freightCostValue = 0;
                    var freightCostEnabled = false;
                    var exchangeRateValue = 0;
                    var exchangeRateEnabled = false;
                    var salesCostValue = 0;
                    var salesCostEnabled = false;

                    if (exportCost == null || exportCost == "" || exportCost == 0 || exportCost == undefined) {
                        exportCostEnabled = false;
                        exportCostValue = 0;
                    } else {
                        exportCostEnabled = true;
                        exportCostValue = exportCost;
                    }

                    if (freightCost == null || freightCost == "" || freightCost == 0 || freightCost == undefined) {
                        freightCostEnabled = false;
                        freightCostValue = 0;
                    } else {
                        freightCostEnabled = true;
                        freightCostValue = freightCost;
                    }

                    if (exchangeRate == null || exchangeRate == "" || exchangeRate == 0 || exchangeRate == undefined) {
                        exchangeRateEnabled = false;
                        exchangeRateValue = 0;
                    } else {
                        exchangeRateEnabled = true;
                        exchangeRateValue = exchangeRate;
                    }

                    if (salesCost == null || salesCost == "" || salesCost == 0 || salesCost == undefined) {
                        salesCostEnabled = false;
                        salesCostValue = 0;
                    } else {
                        salesCostEnabled = true;
                        salesCostValue = salesCost;
                    }

                    arrDispatchIds.push({
                        dispatchid: dispatchId,
                        containernumber: containerNumber,
                        exportcost: exportCostValue,
                        exportcostenabled: exportCostEnabled,
                        freightcost: freightCostValue,
                        freightcostenabled: freightCostEnabled,
                        exchangerate: exchangeRateValue,
                        exchangerateenabled: exchangeRateEnabled,
                        salescost: salesCostValue,
                        salescostenabled: salesCostEnabled,
                        cftvalue: cftValue
                    });
                }
            });

            if (isValid && arrDispatchIds.length > 0) {
                var fd = new FormData();
                fd.append("processId", $("#hdnProcessId").val());
                fd.append("downloadType", $("#hdnDownloadtype").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append("dispatchIds", JSON.stringify(arrDispatchIds));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.clear();
                toastr.info(processing_request);
                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/generate_cost_summaryreport",
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
                            $("#add-modal-data-bd").modal('hide');
                            window.location = response.result;
                            //wait(3000);
                            //deletefilesfromfolder();
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