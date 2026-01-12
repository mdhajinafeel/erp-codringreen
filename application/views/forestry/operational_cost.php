<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h3> <?php echo $this->lang->line('costings'); ?> </h3>
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
            </div>

            <!-- <div class="col-auto ms-auto">
                <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_summary_report'); ?>" type="button" id="generate_report">
                    <span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                        <?php echo $this->lang->line('download_summary_report'); ?></span>
                </button>
            </div> -->
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="row mb-3">
            <div class="col-md-4 align-self-center">
                <label for="origin"><?php echo $this->lang->line("origin"); ?></label>
                <select class="form-control" name="origin" id="origin" data-plugin="select_erp">

                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs mb-3 flex-nowrap overflow-auto d-flex" id="nav-tab" role="tablist" style="white-space: nowrap; overflow-x: auto;">

                                <button class="nav-link text-white active" id="nav-maintenance-tab" data-bs-toggle="tab" data-bs-target="#nav-maintenance" type="button" role="tab" aria-controls="nav-itr" aria-selected="false">
                                    <?php echo $this->lang->line("maintenance"); ?>
                                </button>

                                <button class="nav-link text-white" id="nav-machinerental-tab" data-bs-toggle="tab" data-bs-target="#nav-machinerental" type="button" role="tab" aria-controls="nav-machinerental" aria-selected="true">
                                    <?php echo $this->lang->line("machine_rental"); ?>
                                </button>

                                <button class="nav-link text-white" id="nav-manuallabour-tab" data-bs-toggle="tab" data-bs-target="#nav-manuallabour" type="button" role="tab" aria-controls="nav-manuallabour" aria-selected="true">
                                    <?php echo $this->lang->line("manual_labour"); ?>
                                </button>

                                <button class="nav-link text-white" id="nav-acpm-tab" data-bs-toggle="tab" data-bs-target="#nav-acpm" type="button" role="tab" aria-controls="nav-acpm" aria-selected="true">
                                    <?php echo $this->lang->line("acpm"); ?>
                                </button>

                                <button class="nav-link text-white" id="nav-lubricants-tab" data-bs-toggle="tab" data-bs-target="#nav-lubricants" type="button" role="tab" aria-controls="nav-lubricants" aria-selected="true">
                                    <?php echo $this->lang->line("lubricants"); ?>
                                </button>

                                <button class="nav-link text-white" id="nav-others-tab" data-bs-toggle="tab" data-bs-target="#nav-others" type="button" role="tab" aria-controls="nav-others" aria-selected="true">
                                    <?php echo $this->lang->line("others"); ?>
                                </button>
                            </div>
                        </nav>

                        <div class="tab-content" id="nav-tabContent">

                            <div class="tab-pane fade show active" id="nav-maintenance" role="tabpanel" aria-labelledby="nav-maintenance-tab">

                                <!-- <div class="row flex-between-end">
                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_maintainance_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="maintenance_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="maintenance_xmlupload" type="file" accept=".xml" id="maintenance_xmlupload" onchange="loadFileMaintenance(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="maintainance_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="maintainance_suppliers" id="maintainance_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="maintainance_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="maintainance_purchasecontract" id="maintainance_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="maintainance_purchaser"><?php echo $this->lang->line("suppliercredit_title"); ?></label>
                                        <select class="form-control" name="maintainance_purchaser" id="maintainance_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="maintainance_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="maintainance_invoice_number" name="maintainance_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-maintainance_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="maintainance_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="maintainance_date" name="maintainance_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-maintainance_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_subtotal"><?php echo $this->lang->line("export_subtotal"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="maintainance_subtotal" name="maintainance_subtotal" class="form-control" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-maintainance_subtotal" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_tax"><?php echo $this->lang->line("export_iva"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="maintainance_tax" name="maintainance_tax" class="form-control" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-maintainance_tax" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_amount"><?php echo $this->lang->line("amount"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="maintainance_amount" name="maintainance_amount" class="form-control" value="" readonly placeholder="<?php echo $this->lang->line("amount"); ?>" />
                                        <label id="error-maintainance_amount" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="maintainance_claim_remarks" name="maintainance_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingMaintainance" name="btnResetCostingMaintainance"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingMaintainance" name="btnSaveCostingMaintainance"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('maintenance_list'); ?></h5>
                                    <table class="datatables-demo table table-striped table-bordered nowrap" id="xin_table_maintenance" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade show" id="nav-machinerental" role="tabpanel" aria-labelledby="nav-machinerental-tab">

                                <!-- <div class="row flex-between-end">
                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_maintainance_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="machinerental_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="machinerental_xmlupload" type="file" accept=".xml" id="machinerental_xmlupload" onchange="loadFileMachineRental(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="machinerental_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="machinerental_suppliers" id="machinerental_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-machinerental_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="machinerental_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="machinerental_purchasecontract" id="machinerental_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-machinerental_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="machinerental_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="machinerental_date" name="machinerental_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-machinerental_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="machinerental_amount"><?php echo $this->lang->line("amount"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="machinerental_amount" name="machinerental_amount" class="form-control" value="" placeholder="<?php echo $this->lang->line("amount"); ?>" />
                                        <label id="error-machinerental_amount" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="machinerental_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="machinerental_claim_remarks" name="machinerental_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingMachineRental" name="btnResetCostingMachineRental"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingMachineRental" name="btnSaveCostingMachineRental"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('machinerental_list'); ?></h5>
                                    <table class="datatables-demo table table-striped table-bordered nowrap" id="xin_table_machinerental" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade show" id="nav-manuallabour" role="tabpanel" aria-labelledby="nav-manuallabour-tab">

                                <!-- <div class="row flex-between-end">
                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_maintainance_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <!-- <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="manuallabour_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="manuallabour_xmlupload" type="file" accept=".xml" id="manuallabour_xmlupload" onchange="loadFileManualLabour(event)" class="form-control">
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="manuallabour_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="manuallabour_suppliers" id="manuallabour_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-manuallabour_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="manuallabour_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="manuallabour_purchasecontract" id="manuallabour_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-manuallabour_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="manuallabour_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="manuallabour_date" name="manuallabour_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-manuallabour_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="manuallabour_amount"><?php echo $this->lang->line("amount"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="manuallabour_amount" name="manuallabour_amount" class="form-control" value="" placeholder="<?php echo $this->lang->line("amount"); ?>" />
                                        <label id="error-manuallabour_amount" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="manuallabour_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="manuallabour_claim_remarks" name="manuallabour_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingManualLabour" name="btnResetCostingManualLabour"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingManualLabour" name="btnSaveCostingManualLabour"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('manuallabour_list'); ?></h5>
                                    <table class="datatables-demo table table-striped table-bordered nowrap" id="xin_table_manuallabour" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-acpm" role="tabpanel" aria-labelledby="nav-acpm-tab">

                                <!-- <div class="row flex-between-end">

                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_acpm_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="acpm_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="acpm_xmlupload" type="file" accept=".xml" id="acpm_xmlupload" onchange="loadFileACPM(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="acpm_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="acpm_suppliers" id="acpm_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-acpm_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="acpm_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="acpm_purchasecontract" id="acpm_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-acpm_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="acpm_purchaser"><?php echo $this->lang->line("suppliercredit_title"); ?></label>
                                        <select class="form-control" name="acpm_purchaser" id="acpm_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-acpm_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="acpm_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="acpm_invoice_number" name="acpm_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-acpm_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="acpm_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="acpm_date" name="acpm_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-acpm_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="acpm_quantity"><?php echo $this->lang->line("quantity"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="acpm_quantity" name="acpm_quantity" class="form-control" value="" placeholder="<?php echo $this->lang->line("quantity"); ?>" />
                                        <label id="error-acpm_quantity" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="acpm_total_value"><?php echo $this->lang->line("total_value"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="acpm_total_value" name="acpm_total_value" class="form-control" value="" placeholder="<?php echo $this->lang->line("total_value"); ?>" />
                                        <label id="error-acpm_total_value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="acpm_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="acpm_claim_remarks" name="acpm_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 10px;" id="btnResetCostingACPM" name="btnResetCostingACPM"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingACPM" name="btnSaveCostingACPM"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <div class="col-md-12">
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('acpm_list'); ?></h5>
                                        <table class="datatables-demo table table-striped table-bordered" id="xin_table_acpm" style="width: 100% !important;">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('action'); ?></th>
                                                    <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                    <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                    <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                    <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                    <th><?php echo $this->lang->line('quantity'); ?></th>
                                                    <th><?php echo $this->lang->line('total_cost'); ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <!-- <div class="col-md-4">
                                        
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('acpm_list'); ?></h5>
                                    </div> -->
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-lubricants" role="tabpanel" aria-labelledby="nav-lubricants-tab">

                                <!-- <div class="row flex-between-end">

                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_acpm_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="lubricants_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="lubricants_xmlupload" type="file" accept=".xml" id="lubricants_xmlupload" onchange="loadFileLubricants(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="lubricants_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="lubricants_suppliers" id="lubricants_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-lubricants_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="lubricants_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="lubricants_purchasecontract" id="lubricants_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-lubricants_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="lubricants_purchaser"><?php echo $this->lang->line("suppliercredit_title"); ?></label>
                                        <select class="form-control" name="lubricants_purchaser" id="lubricants_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-lubricants_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="lubricants_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="lubricants_invoice_number" name="lubricants_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-lubricants_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="lubricants_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="lubricants_date" name="lubricants_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-lubricants_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="lubricants_quantity"><?php echo $this->lang->line("quantity"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="lubricants_quantity" name="lubricants_quantity" class="form-control" value="" placeholder="<?php echo $this->lang->line("quantity"); ?>" />
                                        <label id="error-lubricants_quantity" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="lubricants_total_value"><?php echo $this->lang->line("total_value"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="lubricants_total_value" name="lubricants_total_value" class="form-control" value="" placeholder="<?php echo $this->lang->line("total_value"); ?>" />
                                        <label id="error-lubricants_total_value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="lubricants_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="lubricants_claim_remarks" name="lubricants_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 10px;" id="btnResetCostingLubricants" name="btnResetCostingLubricants"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingLubricants" name="btnSaveCostingLubricants"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <div class="col-md-12">
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('lubricants_list'); ?></h5>
                                        <table class="datatables-demo table table-striped table-bordered" id="xin_table_lubricants" style="width: 100% !important;">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('action'); ?></th>
                                                    <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                    <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                    <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                    <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                    <th><?php echo $this->lang->line('quantity'); ?></th>
                                                    <th><?php echo $this->lang->line('total_cost'); ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <!-- <div class="col-md-4">
                                        
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('acpm_list'); ?></h5>
                                    </div> -->
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-others" role="tabpanel" aria-labelledby="nav-others-tab">

                                <!-- <div class="row flex-between-end">

                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_acpm_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div> -->

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="others_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="others_xmlupload" type="file" accept=".xml" id="others_xmlupload" onchange="loadFileOthers(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="others_suppliers"><?php echo $this->lang->line("assigned_to"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="others_suppliers" id="others_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-others_suppliers" class="error-text"><?php echo $this->lang->line("error_assigned_to"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="others_purchasecontract"><?php echo $this->lang->line("purchase_contract"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="others_purchasecontract" id="others_purchasecontract" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-others_purchasecontract" class="error-text"><?php echo $this->lang->line("error_purchase_contract"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="others_purchaser"><?php echo $this->lang->line("suppliercredit_title"); ?></label>
                                        <select class="form-control" name="others_purchaser" id="others_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-others_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="others_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="others_invoice_number" name="others_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-others_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="others_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="others_date" name="others_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-others_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="others_total_value"><?php echo $this->lang->line("total_value"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="others_total_value" name="others_total_value" class="form-control" value="" placeholder="<?php echo $this->lang->line("total_value"); ?>" />
                                        <label id="error-others_total_value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="others_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="others_claim_remarks" name="others_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 10px;" id="btnResetCostingOthers" name="btnResetCostingOthers"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingOthers" name="btnSaveCostingOthers"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <div class="col-md-12">
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('others_list'); ?></h5>
                                        <table class="datatables-demo table table-striped table-bordered" id="xin_table_others" style="width: 100% !important;">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('action'); ?></th>
                                                    <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                    <th><?php echo $this->lang->line('purchase_contract'); ?></th>
                                                    <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                    <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                    <th><?php echo $this->lang->line('total_cost'); ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <!-- <div class="col-md-4">
                                        
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('acpm_list'); ?></h5>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/jquery-ui.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>

<script type="text/javascript">
    var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";

    $(document).ready(function() {

        //ACPM

        $("#error-acpm_suppliers").hide();
        $("#error-acpm_date").hide();
        $("#error-acpm_quantity").hide();
        $("#error-acpm_total_value").hide();
        $("#error-acpm_purchaser").hide();
        $("#error-acpm_purchasecontract").hide();
        $("#error-acpm_invoice_number").hide();

        $("#acpm_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingACPM").click(function() {
            var acpm_suppliers = $("#acpm_suppliers").val();
            var acpm_purchasecontract = $("#acpm_purchasecontract").val();
            var acpm_date = $("#acpm_date").val();
            var acpm_quantity = $("#acpm_quantity").val();
            var acpm_total_value = $("#acpm_total_value").val();
            var acpm_claim_remarks = $("#acpm_claim_remarks").val();
            var acpm_purchaser = $("#acpm_purchaser").val();
            var acpm_invoice_number = $("#acpm_invoice_number").val();
            let mode = $("#btnSaveCostingACPM").data('mode') || 'add';
            let costingId = $("#btnSaveCostingACPM").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true;

            if (acpm_date == "") {
                $("#error-acpm_date").show();
                isValid1 = false;
            } else {
                $("#error-acpm_date").hide();
                isValid1 = true;
            }

            if (acpm_purchasecontract == 0) {
                $("#error-acpm_purchasecontract").show();
                isValid6 = false;
            } else {
                $("#error-acpm_purchasecontract").hide();
                isValid6 = true;
            }

            if (acpm_quantity == "") {
                $("#error-acpm_quantity").show();
                isValid2 = false;
            } else {
                if (acpm_quantity <= 0) {
                    $("#error-acpm_quantity").text(error_zero_value);
                    $("#error-acpm_quantity").show();
                    isValid2 = false;
                } else {
                    $("#error-acpm_quantity").hide();
                    isValid2 = true;
                }
            }

            if (acpm_total_value == "") {
                $("#error-acpm_total_value").show();
                isValid3 = false;
            } else {
                if (acpm_total_value <= 0) {
                    $("#error-acpm_total_value").text(error_zero_value);
                    $("#error-acpm_total_value").show();
                    isValid3 = false;
                } else {
                    $("#error-acpm_total_value").hide();
                    isValid3 = true;
                }
            }

            if (acpm_suppliers == 0) {
                $("#error-acpm_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-acpm_suppliers").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("acpmDate", acpm_date);
                fd.append("acpmQuantity", acpm_quantity);
                fd.append("acpmTotalValue", acpm_total_value);
                fd.append("acpmClaimRemarks", acpm_claim_remarks);
                fd.append("acpmSuppliers", acpm_suppliers);
                fd.append("acpmPurchaseContract", acpm_purchasecontract);
                fd.append("acpmPurchaser", acpm_purchaser);
                fd.append("acpmInvoiceNumber", acpm_invoice_number);
                fd.append("costType", 4);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveACPM");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $("#acpm_date").val("");
                            $("#acpm_quantity").val("");
                            $("#acpm_total_value").val("");
                            $("#acpm_claim_remarks").val("");
                            $("#error-acpm_date").hide();
                            $("#error-acpm_quantity").hide();
                            $("#error-acpm_total_value").hide();
                            $('#acpm_invoice_number').val("");
                            $("#error-acpm_suppliers").hide();
                            $("#acpm_total_value").removeAttr("disabled");
                            $('#acpm_suppliers').val('0'); // Set the value
                            $('#acpm_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#acpm_purchaser').val('0'); // Set the value
                            $('#acpm_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $('#acpm_purchasecontract').val('0'); // Set the value
                            $('#acpm_purchasecontract').trigger('change'); // Trigger the change event to update Select2
                            $("#btnSaveCostingACPM").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingACPM").data('mode', 'add');
                            $("#btnSaveCostingACPM").data('costingId', 0);
                        }

                        $('#xin_table_acpm').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingACPM").click(function() {
            $("#acpm_date").val("");
            $("#acpm_quantity").val("");
            $("#acpm_total_value").val("");
            $("#acpm_claim_remarks").val("");
            $("#error-acpm_date").hide();
            $("#error-acpm_quantity").hide();
            $("#error-acpm_total_value").hide();
            $("#error-acpm_suppliers").hide();
            $("#acpm_total_value").removeAttr("disabled");
            $('#acpm_invoice_number').val("");
            $('#acpm_suppliers').val('0'); // Set the value
            $('#acpm_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#acpm_purchaser').val('0'); // Set the value
            $('#acpm_purchaser').trigger('change'); // Trigger the change event to update Select2
            $('#acpm_purchasecontract').val('0'); // Set the value
            $('#acpm_purchasecontract').trigger('change'); // Trigger the change event to update Select2
            $('#xin_table_acpm').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingACPM").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingACPM").data('mode', 'add');
            $("#btnSaveCostingACPM").data('costingId', 0);
        });

        $('#xin_table_acpm').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=4",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#acpm_suppliers").change(function() {

            $("#error-acpm_purchasecontract").hide();
            if ($("#acpm_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#acpm_suppliers").val(), 4);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_acpm]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editacpm',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#acpm_invoice_number").val(JSON.result.invoiceNumber);
                    $("#acpm_date").val(JSON.result.expenseDate);
                    $("#acpm_quantity").val(JSON.result.quantity);
                    $("#acpm_total_value").val(JSON.result.amount);
                    $("#acpm_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingACPM").data('mode', 'edit');
                    $("#btnSaveCostingACPM").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setACPMEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_acpm]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteacpmconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setACPMEditData(data) {

            // 1. Set supplier
            $("#acpm_suppliers").val(data.supplierId).trigger('change');
            $("#acpm_purchasers").val(data.purchaserId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('acpm_contracts_loaded', function () {
                $("#acpm_purchasecontract").val(data.contractId);
            });
        }

        //END ACPM

        //MACHINE MAINTAINANCE

        $("#error-maintainance_suppliers").hide();
        $("#error-maintainance_purchasecontract").hide();
        $("#error-maintainance_purchaser").hide();
        $("#error-maintainance_invoice_number").hide();
        $("#error-maintainance_date").hide();
        $("#error-maintainance_subtotal").hide();
        $("#error-maintainance_tax").hide();
        $("#error-maintainance_amount").hide();

        $("#maintainance_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingMaintainance").click(function() {
            var maintainance_date = $("#maintainance_date").val();
            var maintainance_subtotal = $("#maintainance_subtotal").val();
            var maintainance_tax = $("#maintainance_tax").val();
            var maintainance_amount = $("#maintainance_amount").val();
            var maintainance_claim_remarks = $("#maintainance_claim_remarks").val();
            var maintainance_suppliers = $("#maintainance_suppliers").val();
            var maintainance_purchaser = $("#maintainance_purchaser").val();
            var maintainance_purchasecontract = $("#maintainance_purchasecontract").val();
            var maintainance_invoice_number = $("#maintainance_invoice_number").val();
            let mode = $("#btnSaveCostingMaintainance").data('mode') || 'add';
            let costingId = $("#btnSaveCostingMaintainance").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true;

            if (maintainance_date == "") {
                $("#error-maintainance_date").show();
                isValid1 = false;
            } else {
                $("#error-maintainance_date").hide();
                isValid1 = true;
            }

            if (maintainance_amount == "") {
                $("#error-maintainance_amount").show();
                isValid2 = false;
            } else {
                if (maintainance_amount <= 0) {
                    $("#error-maintainance_amount").text(error_zero_value);
                    $("#error-maintainance_amount").show();
                    isValid2 = false;
                } else {
                    $("#error-maintainance_amount").hide();
                    isValid2 = true;
                }
            }

            if (maintainance_subtotal == "") {
                $("#error-maintainance_subtotal").show();
                isValid3 = false;
            } else {
                if (maintainance_subtotal <= 0) {
                    $("#error-maintainance_subtotal").text(error_zero_value);
                    $("#error-maintainance_subtotal").show();
                    isValid3 = false;
                } else {
                    $("#error-maintainance_subtotal").hide();
                    isValid3 = true;
                }
            }

            if (maintainance_tax == "") {
                $("#error-maintainance_tax").show();
                isValid4 = false;
            } else {
                $("#error-maintainance_tax").hide();
                isValid4 = true;
            }

            if (maintainance_suppliers == 0) {
                $("#error-maintainance_suppliers").show();
                isValid6 = false;
            } else {
                $("#error-maintainance_suppliers").hide();
                isValid6 = true;
            }

            if (maintainance_suppliers == 0) {
                $("#error-maintainance_purchasecontract").show();
                isValid7 = false;
            } else {
                $("#error-maintainance_purchasecontract").hide();
                isValid7 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("maintainanceDate", maintainance_date);
                fd.append("maintainanceContract", maintainance_purchasecontract);
                fd.append("maintainanceSubTotal", maintainance_subtotal);
                fd.append("maintainanceTax", maintainance_tax);
                fd.append("maintainanceAmount", maintainance_amount);
                fd.append("maintainanceClaimRemarks", maintainance_claim_remarks);
                fd.append("maintainanceSuppliers", maintainance_suppliers);
                fd.append("maintainanceInvoiceNumber", maintainance_invoice_number);
                fd.append("maintainancePurchaser", maintainance_purchaser);
                fd.append("costType", 5);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveMaintenance");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $("#maintainance_date").val("");
                            $("#maintainance_purchasecontract").val("0");
                            $("#maintainance_purchasecontract").trigger('change');
                            $("#maintainance_subtotal").val("");
                            $("#maintainance_tax").val("");
                            $("#maintainance_amount").val("");
                            $("#maintainance_claim_remarks").val("");
                            $("#error-maintainance_date").hide();
                            $("#error-maintainance_purchasecontract").hide();
                            $("#error-maintainance_subtotal").hide();
                            $("#error-maintainance_tax").hide();
                            $("#error-maintainance_amount").hide();
                            $("#error-maintainance_suppliers").hide();
                            $('#maintainance_suppliers').val('0'); // Set the value
                            $('#maintainance_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#maintainance_invoice_number').val("");
                            $('#maintainance_purchaser').val('0'); // Set the value
                            $('#maintainance_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $("#btnSaveCostingMaintainance").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingMaintainance").data('mode', 'add');
                            $("#btnSaveCostingMaintainance").data('costingId', 0);
                        }

                        $('#xin_table_maintenance').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingMaintainance").click(function() {
            $("#error-maintainance_suppliers").hide();
            $("#error-maintainance_purchasecontract").hide();
            $("#error-maintainance_purchaser").hide();
            $("#error-maintainance_invoice_number").hide();
            $("#error-maintainance_date").hide();
            $("#error-maintainance_subtotal").hide();
            $("#error-maintainance_tax").hide();
            $("#error-maintainance_amount").hide();
            $("#maintainance_date").val("");
            $("#maintainance_subtotal").val("");
            $("#maintainance_tax").val("");
            $("#maintainance_amount").val("");
            $("#maintainance_claim_remarks").val("");
            $('#maintainance_amount').val("");
            $('#maintainance_suppliers').val('0'); // Set the value
            $('#maintainance_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#maintainance_purchasecontract').val('0'); // Set the value
            $('#maintainance_purchasecontract').trigger('change'); // Trigger the change event to update Select2
            $('#maintainance_purchaser').val('0'); // Set the value
            $('#maintainance_purchaser').trigger('change'); // Trigger the change event to update Select2
            $('#xin_table_maintenance').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingMaintainance").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingMaintainance").data('mode', 'add');
            $("#btnSaveCostingMaintainance").data('costingId', 0);
        });

        $('#xin_table_maintenance').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=5",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#maintainance_subtotal").on("keyup", function() {
            var maintainance_subtotal = parseFloat($("#maintainance_subtotal").val()) || 0;
            var maintainance_tax = parseFloat($("#maintainance_tax").val()) || 0;

            var total = maintainance_subtotal + maintainance_tax;
            $("#maintainance_amount").val(total);
        });

        $("#maintainance_tax").on("keyup", function() {
            var maintainance_subtotal = parseFloat($("#maintainance_subtotal").val()) || 0;
            var maintainance_tax = parseFloat($("#maintainance_tax").val()) || 0;

            var total = maintainance_subtotal + maintainance_tax;
            $("#maintainance_amount").val(total);
        });

        $("#maintainance_suppliers").change(function() {

            $("#error-maintainance_purchasecontract").hide();
            if ($("#maintainance_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#maintainance_suppliers").val(), 5);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_maintenance]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editmaintainance',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#maintainance_invoice_number").val(JSON.result.invoiceNumber);
                    $("#maintainance_date").val(JSON.result.expenseDate);
                    $("#maintainance_subtotal").val(JSON.result.subTotal);
                    $("#maintainance_tax").val(JSON.result.taxAmount);
                    $("#maintainance_amount").val(JSON.result.amount);
                    $("#maintainance_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingMaintainance").data('mode', 'edit');
                    $("#btnSaveCostingMaintainance").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setMaintenanceEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_maintenance]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deletemaintainanceconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setMaintenanceEditData(data) {

            // 1. Set supplier
            $("#maintainance_suppliers").val(data.supplierId).trigger('change');
            $("#maintainance_purchaser").val(data.purchaserId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('maintenance_contracts_loaded', function () {
                $("#maintainance_purchasecontract").val(data.contractId);
            });
        }

        //END MACHINE MAINTAINANCE

        //MACHINE RENTAL

        $("#error-machinerental_suppliers").hide();
        $("#error-machinerental_purchasecontract").hide();
        $("#error-machinerental_date").hide();
        $("#error-machinerental_amount").hide();

        $("#machinerental_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingMachineRental").click(function() {
            var machinerental_date = $("#machinerental_date").val();
            var machinerental_amount = $("#machinerental_amount").val();
            var machinerental_claim_remarks = $("#machinerental_claim_remarks").val();
            var machinerental_suppliers = $("#machinerental_suppliers").val();
            var machinerental_purchasecontract = $("#machinerental_purchasecontract").val();
            let mode = $("#btnSaveCostingMachineRental").data('mode') || 'add';
            let costingId = $("#btnSaveCostingMachineRental").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (machinerental_date == "") {
                $("#error-machinerental_date").show();
                isValid1 = false;
            } else {
                $("#error-machinerental_date").hide();
                isValid1 = true;
            }

            if (machinerental_amount == "") {
                $("#error-machinerental_amount").show();
                isValid2 = false;
            } else {
                if (machinerental_amount <= 0) {
                    $("#error-machinerental_amount").text(error_zero_value);
                    $("#error-machinerental_amount").show();
                    isValid2 = false;
                } else {
                    $("#error-machinerental_amount").hide();
                    isValid2 = true;
                }
            }

            if (machinerental_suppliers == 0) {
                $("#error-machinerental_suppliers").show();
                isValid3 = false;
            } else {
                $("#error-machinerental_suppliers").hide();
                isValid3 = true;
            }

            if (machinerental_purchasecontract == 0) {
                $("#error-machinerental_purchasecontract").show();
                isValid4 = false;
            } else {
                $("#error-machinerental_purchasecontract").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("machinerentalDate", machinerental_date);
                fd.append("machinerentalContract", machinerental_purchasecontract);
                fd.append("machinerentalAmount", machinerental_amount);
                fd.append("machinerentalClaimRemarks", machinerental_claim_remarks);
                fd.append("machinerentalSuppliers", machinerental_suppliers);
                fd.append("costType", 7);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveMachineRental");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $('#machinerental_suppliers').val('0'); // Set the value
                            $('#machinerental_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $("#machinerental_purchasecontract").val("0");
                            $("#machinerental_purchasecontract").trigger('change');
                            $("#machinerental_date").val("");
                            $("#machinerental_amount").val("");
                            $("#machinerental_claim_remarks").val("");

                            $("#error-machinerental_suppliers").hide();
                            $("#error-machinerental_purchasecontract").hide();
                            $("#error-machinerental_date").hide();
                            $("#error-machinerental_amount").hide();
                            $("#btnSaveCostingMachineRental").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingMachineRental").data('mode', 'add');
                            $("#btnSaveCostingMachineRental").data('costingId', 0);
                        }

                        $('#xin_table_machinerental').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingMachineRental").click(function() {
            $('#machinerental_suppliers').val('0'); // Set the value
            $('#machinerental_suppliers').trigger('change'); // Trigger the change event to update Select2
            $("#machinerental_purchasecontract").val("0");
            $("#machinerental_purchasecontract").trigger('change');
            $("#machinerental_date").val("");
            $("#machinerental_amount").val("");
            $("#machinerental_claim_remarks").val("");

            $("#error-machinerental_suppliers").hide();
            $("#error-machinerental_purchasecontract").hide();
            $("#error-machinerental_date").hide();
            $("#error-machinerental_amount").hide();

            $("#btnSaveCostingMachineRental").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingMachineRental").data('mode', 'add');
            $("#btnSaveCostingMachineRental").data('costingId', 0);
        });

        $('#xin_table_machinerental').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=7",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#machinerental_suppliers").change(function() {

            $("#error-machinerental_purchasecontract").hide();
            if ($("#machinerental_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#machinerental_suppliers").val(), 7);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_machinerental]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editmachinerental',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#machinerental_date").val(JSON.result.expenseDate);
                    $("#machinerental_amount").val(JSON.result.amount);
                    $("#machinerental_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingMachineRental").data('mode', 'edit');
                    $("#btnSaveCostingMachineRental").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setMachineRentalEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_machinerental]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deletemachinerentalconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setMachineRentalEditData(data) {

            // 1. Set supplier
            $("#machinerental_suppliers").val(data.supplierId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('machinerental_contracts_loaded', function () {
                $("#machinerental_purchasecontract").val(data.contractId);
            });
        }

        //END MACHINE RENTAL

        //MANUAL LABOUR

        $("#error-manuallabour_suppliers").hide();
        $("#error-manuallabour_purchasecontract").hide();
        $("#error-manuallabour_date").hide();
        $("#error-manuallabour_amount").hide();

        $("#manuallabour_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingManualLabour").click(function() {
            var manuallabour_date = $("#manuallabour_date").val();
            var manuallabour_amount = $("#manuallabour_amount").val();
            var manuallabour_claim_remarks = $("#manuallabour_claim_remarks").val();
            var manuallabour_suppliers = $("#manuallabour_suppliers").val();
            var manuallabour_purchasecontract = $("#manuallabour_purchasecontract").val();
            let mode = $("#btnSaveCostingManualLabour").data('mode') || 'add';
            let costingId = $("#btnSaveCostingManualLabour").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (manuallabour_date == "") {
                $("#error-manuallabour_date").show();
                isValid1 = false;
            } else {
                $("#error-manuallabour_date").hide();
                isValid1 = true;
            }

            if (manuallabour_amount == "") {
                $("#error-manuallabour_amount").show();
                isValid2 = false;
            } else {
                if (manuallabour_amount <= 0) {
                    $("#error-manuallabour_amount").text(error_zero_value);
                    $("#error-mmanuallabour_amount").show();
                    isValid2 = false;
                } else {
                    $("#error-manuallabour_amount").hide();
                    isValid2 = true;
                }
            }

            if (manuallabour_suppliers == 0) {
                $("#error-manuallabour_suppliers").show();
                isValid3 = false;
            } else {
                $("#error-manuallabour_suppliers").hide();
                isValid3 = true;
            }

            if (manuallabour_purchasecontract == 0) {
                $("#error-manuallabour_purchasecontract").show();
                isValid4 = false;
            } else {
                $("#error-manuallabour_purchasecontract").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("manuallabourDate", manuallabour_date);
                fd.append("manuallabourContract", manuallabour_purchasecontract);
                fd.append("manuallabourAmount", manuallabour_amount);
                fd.append("manuallabourClaimRemarks", manuallabour_claim_remarks);
                fd.append("manuallabourSuppliers", manuallabour_suppliers);
                fd.append("costType", 8);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveManualLabour");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $('#manuallabour_suppliers').val('0'); // Set the value
                            $('#manuallabour_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $("#manuallabour_purchasecontract").val("0");
                            $("#manuallabour_purchasecontract").trigger('change');
                            $("#manuallabour_date").val("");
                            $("#manuallabour_amount").val("");
                            $("#mmanuallabour_claim_remarks").val("");

                            $("#error-manuallabour_suppliers").hide();
                            $("#error-manuallabour_purchasecontract").hide();
                            $("#error-manuallabour_date").hide();
                            $("#error-manuallabour_amount").hide();

                            $("#btnSaveCostingManualLabour").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingManualLabour").data('mode', 'add');
                            $("#btnSaveCostingManualLabour").data('costingId', 0);
                        }

                        $('#xin_table_manuallabour').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingManualLabour").click(function() {
            $('#manuallabour_suppliers').val('0'); // Set the value
            $('#manuallabour_suppliers').trigger('change'); // Trigger the change event to update Select2
            $("#manuallabour_purchasecontract").val("0");
            $("#manuallabour_purchasecontract").trigger('change');
            $("#manuallabour_date").val("");
            $("#manuallabour_amount").val("");
            $("#manuallabour_claim_remarks").val("");

            $("#error-manuallabour_suppliers").hide();
            $("#error-manuallabour_purchasecontract").hide();
            $("#error-manuallabour_date").hide();
            $("#error-manuallabour_amount").hide();

            $("#btnSaveCostingManualLabour").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingManualLabour").data('mode', 'add');
            $("#btnSaveCostingManualLabour").data('costingId', 0);
        });

        $('#xin_table_manuallabour').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=8",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#manuallabour_suppliers").change(function() {

            $("#error-manuallabour_purchasecontract").hide();
            if ($("#manuallabour_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#manuallabour_suppliers").val(), 8);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_manuallabour]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editmanuallabour',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#manuallabour_date").val(JSON.result.expenseDate);
                    $("#manuallabour_amount").val(JSON.result.amount);
                    $("#manuallabour_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingManualLabour").data('mode', 'edit');
                    $("#btnSaveCostingManualLabour").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setManualLabourEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_manuallabour]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deletemanuallabourconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setManualLabourEditData(data) {

            // 1. Set supplier
            $("#manuallabour_suppliers").val(data.supplierId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('manuallabour_contracts_loaded', function () {
                $("#manuallabour_purchasecontract").val(data.contractId);
            });
        }

        //END MANUAL LABOUR

        //LUBRICANTS

        $("#error-lubricants_suppliers").hide();
        $("#error-lubricants_date").hide();
        $("#error-lubricants_quantity").hide();
        $("#error-lubricants_total_value").hide();
        $("#error-lubricants_purchaser").hide();
        $("#error-lubricants_purchasecontract").hide();
        $("#error-lubricants_invoice_number").hide();

        $("#lubricants_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingLubricants").click(function() {
            var lubricants_suppliers = $("#lubricants_suppliers").val();
            var lubricants_purchasecontract = $("#lubricants_purchasecontract").val();
            var lubricants_date = $("#lubricants_date").val();
            var lubricants_quantity = $("#lubricants_quantity").val();
            var lubricants_total_value = $("#lubricants_total_value").val();
            var lubricants_claim_remarks = $("#lubricants_claim_remarks").val();
            var lubricants_purchaser = $("#lubricants_purchaser").val();
            var lubricants_invoice_number = $("#lubricants_invoice_number").val();
            let mode = $("#btnSaveCostingLubricants").data('mode') || 'add';
            let costingId = $("#btnSaveCostingLubricants").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true;

            if (lubricants_date == "") {
                $("#error-lubricants_date").show();
                isValid1 = false;
            } else {
                $("#error-lubricants_date").hide();
                isValid1 = true;
            }

            if (lubricants_purchasecontract == 0) {
                $("#error-lubricants_purchasecontract").show();
                isValid6 = false;
            } else {
                $("#error-lubricants_purchasecontract").hide();
                isValid6 = true;
            }

            if (lubricants_quantity == "") {
                $("#error-lubricants_quantity").show();
                isValid2 = false;
            } else {
                if (lubricants_quantity <= 0) {
                    $("#error-lubricants_quantity").text(error_zero_value);
                    $("#error-lubricants_quantity").show();
                    isValid2 = false;
                } else {
                    $("#error-lubricants_quantity").hide();
                    isValid2 = true;
                }
            }

            if (lubricants_total_value == "") {
                $("#error-lubricants_total_value").show();
                isValid3 = false;
            } else {
                if (lubricants_total_value <= 0) {
                    $("#error-lubricants_total_value").text(error_zero_value);
                    $("#error-lubricants_total_value").show();
                    isValid3 = false;
                } else {
                    $("#error-lubricants_total_value").hide();
                    isValid3 = true;
                }
            }

            if (lubricants_suppliers == 0) {
                $("#error-lubricants_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-lubricants_suppliers").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("lubricantsDate", lubricants_date);
                fd.append("lubricantsQuantity", lubricants_quantity);
                fd.append("lubricantsTotalValue", lubricants_total_value);
                fd.append("lubricantsClaimRemarks", lubricants_claim_remarks);
                fd.append("lubricantsSuppliers", lubricants_suppliers);
                fd.append("lubricantsPurchaseContract", lubricants_purchasecontract);
                fd.append("lubricantsPurchaser", lubricants_purchaser);
                fd.append("lubricantsInvoiceNumber", lubricants_invoice_number);
                fd.append("costType", 9);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveLubricants");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $("#lubricants_date").val("");
                            $("#lubricants_quantity").val("");
                            $("#lubricants_total_value").val("");
                            $("#lubricants_claim_remarks").val("");
                            $("#error-lubricants_date").hide();
                            $("#error-lubricants_quantity").hide();
                            $("#error-lubricants_total_value").hide();
                            $('#lubricants_invoice_number').val("");
                            $("#error-lubricants_suppliers").hide();
                            $("#lubricants_total_value").removeAttr("disabled");
                            $('#lubricants_suppliers').val('0'); // Set the value
                            $('#lubricants_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#lubricants_purchaser').val('0'); // Set the value
                            $('#lubricants_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $('#lubricants_purchasecontract').val('0'); // Set the value
                            $('#lubricants_purchasecontract').trigger('change'); // Trigger the change event to update Select2
                            $("#btnSaveCostingLubricants").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingLubricants").data('mode', 'add');
                            $("#btnSaveCostingLubricants").data('costingId', 0);
                        }

                        $('#xin_table_lubricants').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingLubricants").click(function() {
            $("#lubricants_date").val("");
            $("#lubricants_quantity").val("");
            $("#lubricants_total_value").val("");
            $("#lubricants_claim_remarks").val("");
            $("#error-lubricants_date").hide();
            $("#error-lubricants_quantity").hide();
            $("#error-lubricants_total_value").hide();
            $("#error-lubricants_suppliers").hide();
            $("#lubricants_total_value").removeAttr("disabled");
            $('#lubricants_invoice_number').val("");
            $('#lubricants_suppliers').val('0'); // Set the value
            $('#lubricants_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#lubricants_purchaser').val('0'); // Set the value
            $('#lubricants_purchaser').trigger('change'); // Trigger the change event to update Select2
            $('#lubricants_purchasecontract').val('0'); // Set the value
            $('#lubricants_purchasecontract').trigger('change'); // Trigger the change event to update Select2
            $('#xin_table_lubricants').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingLubricants").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingLubricants").data('mode', 'add');
            $("#btnSaveCostingLubricants").data('costingId', 0);
        });

        $('#xin_table_lubricants').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=9",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#lubricants_suppliers").change(function() {

            $("#error-lubricants_purchasecontract").hide();
            if ($("#lubricants_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#lubricants_suppliers").val(), 9);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_lubricants]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editlubricants',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#lubricants_invoice_number").val(JSON.result.invoiceNumber);
                    $("#lubricants_date").val(JSON.result.expenseDate);
                    $("#lubricants_quantity").val(JSON.result.quantity);
                    $("#lubricants_total_value").val(JSON.result.amount);
                    $("#lubricants_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingLubricants").data('mode', 'edit');
                    $("#btnSaveCostingLubricants").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setLubricantsEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_lubricants]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deletelubricantsconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setLubricantsEditData(data) {

            // 1. Set supplier
            $("#lubricants_suppliers").val(data.supplierId).trigger('change');
            $("#lubricants_purchaser").val(data.purchaserId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('lubricants_contracts_loaded', function () {
                $("#lubricants_purchasecontract").val(data.contractId);
            });
        }

        //END LUBRICANTS

        //OTHERS

        $("#error-others_suppliers").hide();
        $("#error-others_date").hide();
        $("#error-others_total_value").hide();
        $("#error-others_purchaser").hide();
        $("#error-others_purchasecontract").hide();
        $("#error-others_invoice_number").hide();

        $("#others_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingOthers").click(function() {
            var others_suppliers = $("#others_suppliers").val();
            var others_purchasecontract = $("#others_purchasecontract").val();
            var others_date = $("#others_date").val();
            var others_total_value = $("#others_total_value").val();
            var others_claim_remarks = $("#others_claim_remarks").val();
            var others_purchaser = $("#others_purchaser").val();
            var others_invoice_number = $("#others_invoice_number").val();
            let mode = $("#btnSaveCostingOthers").data('mode') || 'add';
            let costingId = $("#btnSaveCostingOthers").data('costingId') || 0;

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true;

            if (others_date == "") {
                $("#error-others_date").show();
                isValid1 = false;
            } else {
                $("#error-others_date").hide();
                isValid1 = true;
            }

            if (others_purchasecontract == 0) {
                $("#error-others_purchasecontract").show();
                isValid6 = false;
            } else {
                $("#error-others_purchasecontract").hide();
                isValid6 = true;
            }

            if (others_total_value == "") {
                $("#error-others_total_value").show();
                isValid3 = false;
            } else {
                if (others_total_value <= 0) {
                    $("#error-others_total_value").text(error_zero_value);
                    $("#error-others_total_value").show();
                    isValid3 = false;
                } else {
                    $("#error-others_total_value").hide();
                    isValid3 = true;
                }
            }

            if (others_suppliers == 0) {
                $("#error-others_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-others_suppliers").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("othersDate", others_date);
                fd.append("othersTotalValue", others_total_value);
                fd.append("othersClaimRemarks", others_claim_remarks);
                fd.append("othersSuppliers", others_suppliers);
                fd.append("othersPurchaseContract", others_purchasecontract);
                fd.append("othersPurchaser", others_purchaser);
                fd.append("othersInvoiceNumber", others_invoice_number);
                fd.append("costType", 6);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "operationalcosting");
                fd.append("action_type", "saveOthers");
                fd.append("edit_id", costingId);
                fd.append("pageType", mode);

                $('#loading').show();
                $.ajax({
                    url: BASE_URL_SUBFOLDER + "forestry/operationalcost/save_opertaional_cost",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $("#others_date").val("");
                            $("#others_total_value").val("");
                            $("#others_claim_remarks").val("");
                            $("#error-others_date").hide();
                            $("#error-others_quantity").hide();
                            $("#error-others_total_value").hide();
                            $('#others_invoice_number').val("");
                            $("#error-others_suppliers").hide();
                            $("#others_total_value").removeAttr("disabled");
                            $('#others_suppliers').val('0'); // Set the value
                            $('#others_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#others_purchaser').val('0'); // Set the value
                            $('#others_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $('#others_purchasecontract').val('0'); // Set the value
                            $('#others_purchasecontract').trigger('change'); // Trigger the change event to update Select2
                            $("#btnSaveCostingOthers").text("<?php echo $this->lang->line('save'); ?>");
                            $("#btnSaveCostingOthers").data('mode', 'add');
                            $("#btnSaveCostingOthers").data('costingId', 0);
                        }

                        $('#xin_table_others').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        toastr.error(error);
                    }
                });
            } else {
                return false;
            }
        });

        $("#btnResetCostingOthers").click(function() {
            $("#others_date").val("");
            $("#others_total_value").val("");
            $("#others_claim_remarks").val("");
            $("#error-others_date").hide();
            $("#error-others_total_value").hide();
            $("#error-others_suppliers").hide();
            $("#others_total_value").removeAttr("disabled");
            $('#others_invoice_number').val("");
            $('#others_suppliers').val('0'); // Set the value
            $('#others_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#others_purchaser').val('0'); // Set the value
            $('#others_purchaser').trigger('change'); // Trigger the change event to update Select2
            $('#others_purchasecontract').val('0'); // Set the value
            $('#others_purchasecontract').trigger('change'); // Trigger the change event to update Select2
            $('#xin_table_others').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingOthers").text("<?php echo $this->lang->line('save'); ?>");
            $("#btnSaveCostingOthers").data('mode', 'add');
            $("#btnSaveCostingOthers").data('costingId', 0);
        });

        $('#xin_table_others').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/operationalcosting_list?originId=" + $("#origin").val() + "&costType=6",
                type: 'GET'
            },
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });

        $("#others_suppliers").change(function() {

            $("#error-others_purchasecontract").hide();
            if ($("#others_suppliers").val() == 0) {
                fetchContracts(0, 0, 0);
            } else {
                fetchContracts($("#origin").val(), $("#others_suppliers").val(), 6);
            }
        });

        $(document).on('click', 'button[data-role=editcosting_others]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: {
                    jd: 1,
                    is_ajax: 3,
                    type: 'editothers',
                    costingId: costingId,
                    originId: $("#origin").val()
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#others_date").val(JSON.result.expenseDate);
                    $("#others_total_value").val(JSON.result.amount);
                    $("#others_claim_remarks").val(JSON.result.remarks);

                    $("#btnSaveCostingOthers").data('mode', 'edit');
                    $("#btnSaveCostingOthers").data('costingId', costingId);

                    // ---------- IMPORTANT PART ----------
                    setOthersEditData(JSON.result);
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecosting_others]', function() {

            let costingId = $(this).data('costing_id');

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/dialog_operational_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteothersconfirmation&cid=' + costingId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#loading").hide();
                    $("#add-modal-data").css('z-index', zIndex).modal('show');
                }
            });
        });

        function setOthersEditData(data) {

            // 1. Set supplier
            $("#others_suppliers").val(data.supplierId).trigger('change');
            $("#others_purchaser").val(data.purchaserId).trigger('change');

            // // 2. After contracts are loaded
            $(document).one('others_contracts_loaded', function () {
                $("#others_purchasecontract").val(data.contractId);
            });
        }

        //END OTHERS

        fetchSuppliers();
        fetchPurchasers();
    });

    function fetchContracts(originid, supplierid, type) {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/get_contracts_by_supplier?originid=" + originid + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    if (type == 4) {
                        $("#acpm_purchasecontract").empty();
                        $("#acpm_purchasecontract").append(JSON.result);
                    } else if (type == 5) {
                        $("#maintainance_purchasecontract").empty();
                        $("#maintainance_purchasecontract").append(JSON.result);
                    } else if (type == 7) {
                        $("#machinerental_purchasecontract").empty();
                        $("#machinerental_purchasecontract").append(JSON.result);
                    } else if (type == 8) {
                        $("#manuallabour_purchasecontract").empty();
                        $("#manuallabour_purchasecontract").append(JSON.result);
                    } else if (type == 9) {
                        $("#lubricants_purchasecontract").empty();
                        $("#lubricants_purchasecontract").append(JSON.result);
                    } else if (type == 6) {
                        $("#others_purchasecontract").empty();
                        $("#others_purchasecontract").append(JSON.result);
                    }
                } else {

                    if (type == 4) {
                        $("#acpm_purchasecontract").attr("disabled", true);
                    } else if (type == 5) {
                        $("#maintainance_purchasecontract").attr("disabled", true);
                    } else if (type == 7) {
                        $("#machinerental_purchasecontract").attr("disabled", true);
                    } else if (type == 8) {
                        $("#manuallabour_purchasecontract").attr("disabled", true);
                    } else if (type == 9) {
                        $("#lubricants_purchasecontract").attr("disabled", true);
                    } else if (type == 6) {
                        $("#others_purchasecontract").attr("disabled", true);
                    }
                }

                // 🔔 notify that contracts are ready
                $(document).trigger('maintenance_contracts_loaded');
                $(document).trigger('machinerental_contracts_loaded');
                $(document).trigger('manuallabour_contracts_loaded');
                $(document).trigger('acpm_contracts_loaded');
                $(document).trigger('lubricants_contracts_loaded');
                $(document).trigger('others_contracts_loaded');
                
            }
        });
    }

    function fetchSuppliers() {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/operationalcost/fetch_suppliers?originid=" + $("#origin").val(),
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#acpm_suppliers").empty();
                    $("#acpm_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#acpm_suppliers").append(JSON.result);

                    $("#maintainance_suppliers").empty();
                    $("#maintainance_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#maintainance_suppliers").append(JSON.result);

                    $("#machinerental_suppliers").empty();
                    $("#machinerental_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#machinerental_suppliers").append(JSON.result);

                    $("#manuallabour_suppliers").empty();
                    $("#manuallabour_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#manuallabour_suppliers").append(JSON.result);

                    $("#lubricants_suppliers").empty();
                    $("#lubricants_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#lubricants_suppliers").append(JSON.result);

                    $("#others_suppliers").empty();
                    $("#others_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#others_suppliers").append(JSON.result);
                }
            }
        });
    }

    function fetchPurchasers() {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/operationalcost/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=4",
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#acpm_purchaser").empty();
                    $("#acpm_purchaser").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#acpm_purchaser").append(JSON.result);
                }
            }
        });

        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/operationalcost/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=5",
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#maintainance_purchaser").empty();
                    $("#maintainance_purchaser").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#maintainance_purchaser").append(JSON.result);
                }
            }
        });

        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/operationalcost/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=6",
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#others_purchaser").empty();
                    $("#others_purchaser").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#others_purchaser").append(JSON.result);
                }
            }
        });

        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/operationalcost/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=9",
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#lubricants_purchaser").empty();
                    $("#lubricants_purchaser").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#lubricants_purchaser").append(JSON.result);
                }
            }
        });
    }

    var loadFileMaintenance = function(event) {
        event.preventDefault();

        var files = $('#maintenance_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            var fd = new FormData();

            fd.append('maintenance_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 5);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        if (jsonResult.result["isNewSupplier"]) {

                            $("#maintainance_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#maintainance_purchaser").select2().val(jsonResult.result["supplierId"]).trigger("change");
                        $("#maintainance_invoice_number").val(jsonResult.result["documentId"]);
                        $("#maintainance_date").val(jsonResult.result["issueDate"]);
                        $("#maintainance_concept").val(jsonResult.result["description"]);
                        $("#maintainance_subtotal").val(jsonResult.result["payableAmount"]);
                        $("#maintainance_amount").val(jsonResult.result["payableAmount"] + $("#maintainance_tax").val());
                        $("#maintenance_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#maintenance_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#maintenance_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#maintenance_xmlupload").val("");
        }
    };

    var loadFileMachineRental = function(event) {
        event.preventDefault();

        var files = $('#machinerental_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            var fd = new FormData();

            fd.append('machinerental_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 7);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        if (jsonResult.result["isNewSupplier"]) {

                            $("#machinerental_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#machinerental_date").val(jsonResult.result["issueDate"]);
                        $("#machinerental_amount").val(jsonResult.result["payableAmount"]);
                        $("#machinerental_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#machinerental_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#machinerental_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#machinerental_xmlupload").val("");
        }
    };

    var loadFileACPM = function(event) {
        event.preventDefault();

        var files = $('#acpm_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            var fd = new FormData();

            fd.append('acpm_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 4);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        if (jsonResult.result["isNewSupplier"]) {

                            $("#acpm_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#acpm_purchaser").select2().val(jsonResult.result["supplierId"]).trigger("change");
                        $("#acpm_invoice_number").val(jsonResult.result["documentId"]);
                        $("#acpm_date").val(jsonResult.result["issueDate"]);
                        $("#acpm_quantity").val(jsonResult.result["invoicedQuantity"]);
                        $("#acpm_total_value").val(jsonResult.result["payableAmount"]);
                        $("#acpm_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#acpm_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#acpm_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#acpm_xmlupload").val("");
        }
    };

    var loadFileLubricants = function(event) {
        event.preventDefault();

        var files = $('#lubricants_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            var fd = new FormData();

            fd.append('lubricants_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 9);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        if (jsonResult.result["isNewSupplier"]) {

                            $("#lubricants_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#lubricants_purchaser").select2().val(jsonResult.result["supplierId"]).trigger("change");
                        $("#lubricants_invoice_number").val(jsonResult.result["documentId"]);
                        $("#lubricants_date").val(jsonResult.result["issueDate"]);
                        $("#lubricants_quantity").val(jsonResult.result["invoicedQuantity"]);
                        $("#lubricants_total_value").val(jsonResult.result["payableAmount"]);
                        $("#lubricants_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#lubricants_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lubricants_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#lubricants_xmlupload").val("");
        }
    };

    var loadFileOthers = function(event) {
        event.preventDefault();

        var files = $('#others_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            var fd = new FormData();

            fd.append('others_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 6);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/operationalcost/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        if (jsonResult.result["isNewSupplier"]) {

                            $("#others_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#others_purchaser").select2().val(jsonResult.result["supplierId"]).trigger("change");
                        $("#others_invoice_number").val(jsonResult.result["documentId"]);
                        $("#others_date").val(jsonResult.result["issueDate"]);
                        $("#others_total_value").val(jsonResult.result["payableAmount"]);
                        $("#others_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#others_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#others_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#others_xmlupload").val("");
        }
    };
</script>