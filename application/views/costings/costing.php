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
                <input type="hidden" id="hdnEditId_extraction" name="hdnEditId_extraction" value="0">
                <input type="hidden" id="hdnEditId_acpm" name="hdnEditId_acpm" value="0">
                <input type="hidden" id="hdnEditId_maintenance" name="hdnEditId_maintenance" value="0">
                <input type="hidden" id="hdnEditId_miscellaneous" name="hdnEditId_miscellaneous" value="0">
            </div>

            <div class="col-auto ms-auto">
                <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_summary_report'); ?>" type="button" id="generate_report">
                    <span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                        <?php echo $this->lang->line('download_summary_report'); ?></span>
                </button>
            </div>
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
                                <button class="nav-link text-white active" id="nav-extraction-tab" data-bs-toggle="tab" data-bs-target="#nav-extraction" type="button" role="tab" aria-controls="nav-extraction" aria-selected="true">
                                    <?php echo $this->lang->line("extraction"); ?>
                                </button>
                                <button class="nav-link text-white" id="nav-acpm-tab" data-bs-toggle="tab" data-bs-target="#nav-acpm" type="button" role="tab" aria-controls="nav-acpm" aria-selected="true">
                                    <?php echo $this->lang->line("acpm"); ?>
                                </button>
                                <button class="nav-link text-white" id="nav-maintenance-tab" data-bs-toggle="tab" data-bs-target="#nav-maintenance" type="button" role="tab" aria-controls="nav-itr" aria-selected="false">
                                    <?php echo $this->lang->line("maintenance"); ?>
                                </button>
                                <button class="nav-link text-white" id="nav-miscellaneous-tab" data-bs-toggle="tab" data-bs-target="#nav-miscellaneous" type="button" role="tab" aria-controls="nav-miscellaneous" aria-selected="false">
                                    <?php echo $this->lang->line("miscellaneous"); ?>
                                </button>
                            </div>
                        </nav>

                        <div class="tab-content" id="nav-tabContent">

                            <div class="tab-pane fade show active" id="nav-extraction" role="tabpanel" aria-labelledby="nav-extraction-tab">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="extraction_suppliers"><?php echo $this->lang->line("suppliercredit_title"); ?><span class="mandatory"> *</span></label></label>
                                        <select class="form-control" name="extraction_suppliers" id="extraction_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-extraction_suppliers" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="extraction_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label></label>
                                        <input type="text" id="extraction_date" name="extraction_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-extraction_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="extraction_quantity"><?php echo $this->lang->line("tree_count"); ?><span class="mandatory"> *</span></label></label>
                                        <input type="number" step="any" id="extraction_quantity" name="extraction_quantity" class="form-control" value="" placeholder="<?php echo $this->lang->line("tree_count"); ?>" />
                                        <label id="error-extraction_quantity" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="extraction_total_value"><?php echo $this->lang->line("amount"); ?><span class="mandatory"> *</span></label></label>
                                        <input type="number" step="any" id="extraction_total_value" name="extraction_total_value" class="form-control" value="" readonly placeholder="<?php echo $this->lang->line("amount"); ?>" />
                                        <label id="error-extraction_total_value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="extraction_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="extraction_claim_remarks" name="extraction_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingExtraction" name="btnResetCostingExtraction"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingExtraction" name="btnSaveCostingExtraction"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('extraction_lists'); ?></h5>
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_extraction" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('tree_count'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade show" id="nav-acpm" role="tabpanel" aria-labelledby="nav-acpm-tab">

                                <div class="row flex-between-end">

                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_acpm_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="acpm_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="acpm_xmlupload" type="file" accept=".xml" id="acpm_xmlupload" onchange="loadFileACPM(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="acpm_suppliers"><?php echo $this->lang->line("suppliercredit_title"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="acpm_suppliers" id="acpm_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-acpm_suppliers" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="acpm_purchaser"><?php echo $this->lang->line("purchaser"); ?></label>
                                        <select class="form-control" name="acpm_purchaser" id="acpm_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-acpm_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="acpm_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="acpm_invoice_number" name="acpm_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-acpm_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

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
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <div class="d-inline-flex align-items-center" style="margin-bottom: 8px;">
                                            <label for="flexSwitchCheckChecked" class="mb-0 me-2"><?php echo $this->lang->line("purchase"); ?></label>

                                            <div class="form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" />
                                            </div>

                                            <label for="flexSwitchCheckChecked" class="mb-0"><?php echo $this->lang->line("spend"); ?></label><span class="mandatory"> *</span></label>
                                        </div>
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
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingACPM" name="btnResetCostingACPM"><?php echo $this->lang->line('reset'); ?></button>
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
                                                    <th><?php echo $this->lang->line('purchaser_name'); ?></th>
                                                    <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                    <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                    <th><?php echo $this->lang->line('quantity'); ?></th>
                                                    <th><?php echo $this->lang->line('total_cost'); ?></th>
                                                    <th><?php echo $this->lang->line('ledger_type'); ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <!-- <div class="col-md-4">
                                        
                                        <h5 class="mb-3" data-anchor="data-anchor"><?php echo $this->lang->line('acpm_list'); ?></h5>
                                    </div> -->
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-maintenance" role="tabpanel" aria-labelledby="nav-maintenance-tab">

                                <div class="row flex-between-end">
                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_maintainance_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="maintenance_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="maintenance_xmlupload" type="file" accept=".xml" id="maintenance_xmlupload" onchange="loadFileMaintenance(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="maintainance_suppliers"><?php echo $this->lang->line("suppliercredit_title"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="maintainance_suppliers" id="maintainance_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_suppliers" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="maintainance_purchaser"><?php echo $this->lang->line("purchaser"); ?></label>
                                        <select class="form-control" name="maintainance_purchaser" id="maintainance_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="maintainance_invoice_number" name="maintainance_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-maintainance_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>


                                    <div class="col-md-2">
                                        <label for="maintainance_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="maintainance_date" name="maintainance_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-maintainance_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="maintainance_machine_type"><?php echo $this->lang->line("machine_type"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="maintainance_machine_type" id="maintainance_machine_type" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-maintainance_machine_type" class="error-text"><?php echo $this->lang->line("error_machine"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="maintainance_concept"><?php echo $this->lang->line("concept"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="maintainance_concept" name="maintainance_concept" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("concept"); ?>" />
                                        <label id="error-maintainance_concept" class="error-text"><?php echo $this->lang->line("error_concept"); ?></label>
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
                                                <th><?php echo $this->lang->line('purchaser_name'); ?></th>
                                                <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('machine_type'); ?></th>
                                                <th><?php echo $this->lang->line('concept'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-miscellaneous" role="tabpanel" aria-labelledby="nav-miscellaneous-tab">

                                <div class="row flex-between-end">
                                    <div class="col-auto ms-auto">
                                        <button class="btn btn-warning btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_miscellaneous_report">
                                            <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                                                <?php echo $this->lang->line('download_reports'); ?></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="miscellaneous_xmlupload"><?php echo $this->lang->line('upload_document'); ?></label>
                                        <input name="miscellaneous_xmlupload" type="file" accept=".xml" id="miscellaneous_xmlupload" onchange="loadFileMiscellaneous(event)" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="miscellaneous_suppliers"><?php echo $this->lang->line("suppliercredit_title"); ?><span class="mandatory"> *</span></label>
                                        <select class="form-control" name="miscellaneous_suppliers" id="miscellaneous_suppliers" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-miscellaneous_suppliers" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="miscellaneous_purchaser"><?php echo $this->lang->line("purchaser"); ?></label>
                                        <select class="form-control" name="miscellaneous_purchaser" id="miscellaneous_purchaser" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <label id="error-miscellaneous_purchaser" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="miscellaneous_invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="miscellaneous_invoice_number" name="miscellaneous_invoice_number" class="form-control" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-miscellaneous_invoice_number" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="miscellaneous_date"><?php echo $this->lang->line("expense_date"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="miscellaneous_date" name="miscellaneous_date" class="form-control text-uppercase" value="" readonly placeholder="<?php echo $this->lang->line("expense_date"); ?>" />
                                        <label id="error-miscellaneous_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="miscellaneous_concept"><?php echo $this->lang->line("concept"); ?><span class="mandatory"> *</span></label>
                                        <input type="text" id="miscellaneous_concept" name="miscellaneous_concept" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("concept"); ?>" />
                                        <label id="error-miscellaneous_concept" class="error-text"><?php echo $this->lang->line("error_concept"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="miscellaneous_amount"><?php echo $this->lang->line("amount"); ?><span class="mandatory"> *</span></label>
                                        <input type="number" step="any" id="miscellaneous_amount" name="miscellaneous_amount" class="form-control" value="" placeholder="<?php echo $this->lang->line("amount"); ?>" />
                                        <label id="error-miscellaneous_amount" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="miscellaneous_claim_remarks"><?php echo $this->lang->line("claim_remarks"); ?></label>
                                        <textarea id="miscellaneous_claim_remarks" name="miscellaneous_claim_remarks" class="form-control" placeholder="<?php echo $this->lang->line("claim_remarks"); ?>"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger col-md-1" style="margin-right: 5px;" id="btnResetCostingMiscellaneous" name="btnResetCostingMiscellaneous"><?php echo $this->lang->line('reset'); ?></button>
                                        <button type="button" class="btn btn-primary col-md-1" id="btnSaveCostingMiscellaneous" name="btnSaveCostingMiscellaneous"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4 mb-3">
                                    <h5 class="mb-0" data-anchor="data-anchor"><?php echo $this->lang->line('miscellaneous_list'); ?></h5>
                                    <table class="datatables-demo table table-striped table-bordered nowrap" id="xin_table_miscellaneous" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                                <th><?php echo $this->lang->line('purchaser_name'); ?></th>
                                                <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                                <th><?php echo $this->lang->line('expense_date'); ?></th>
                                                <th><?php echo $this->lang->line('concept'); ?></th>
                                                <th><?php echo $this->lang->line('total_cost'); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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

        //EXTRACTION

        var extraction_cost_farm = <?php echo $extraction_cost; ?>;

        $("#error-extraction_suppliers").hide();
        $("#error-extraction_date").hide();
        $("#error-extraction_quantity").hide();
        $("#error-extraction_total_value").hide();

        $("#extraction_total_value").val("0");

        $("#extraction_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingExtraction").click(function() {
            var extraction_suppliers = $("#extraction_suppliers").val();
            var extraction_date = $("#extraction_date").val();
            var extraction_quantity = $("#extraction_quantity").val();
            var extraction_total_value = $("#extraction_total_value").val();
            var extraction_claim_remarks = $("#extraction_claim_remarks").val();
            var editId = $("#hdnEditId_extraction").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (extraction_date == "") {
                $("#error-extraction_date").show();
                isValid1 = false;
            } else {
                $("#error-extraction_date").hide();
                isValid1 = true;
            }

            if (extraction_quantity == "") {
                $("#error-extraction_quantity").show();
                isValid2 = false;
            } else {
                if (extraction_quantity <= 0) {
                    $("#error-extraction_quantity").text(error_zero_value);
                    $("#error-extraction_quantity").show();
                    isValid2 = false;
                } else {
                    $("#error-extraction_quantity").hide();
                    isValid2 = true;
                }
            }

            if (extraction_total_value == "") {
                $("#error-extraction_total_value").show();
                isValid3 = false;
            } else {
                if (extraction_total_value <= 0) {
                    $("#error-extraction_total_value").text(error_zero_value);
                    $("#error-extraction_total_value").show();
                    isValid3 = false;
                } else {
                    $("#error-extraction_total_value").hide();
                    isValid3 = true;
                }
            }

            if (extraction_suppliers == 0) {
                $("#error-extraction_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-extraction_suppliers").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("extractionDate", extraction_date);
                fd.append("extractionQuantity", extraction_quantity);
                fd.append("extractionTotalValue", extraction_total_value);
                fd.append("extractionClaimRemarks", extraction_claim_remarks);
                fd.append("extractionSuppliers", extraction_suppliers);
                fd.append("costType", 1);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "farmcosting");
                fd.append("action_type", "saveExtraction");
                fd.append("edit_id", editId);

                $('#loading').show();
                $.ajax({
                    url: base_url + "/save_farm_costing",
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

                            $("#extraction_date").val("");
                            $("#extraction_quantity").val("");
                            $("#extraction_total_value").val("");
                            $("#extraction_claim_remarks").val("");
                            $("#error-extraction_date").hide();
                            $("#error-extraction_quantity").hide();
                            $("#error-extraction_total_value").hide();
                            $("#error-extraction_suppliers").hide();
                            $("#extraction_total_value").removeAttr("disabled");
                            $("#hdnEditId_extraction").val("0");
                            $("#btnSaveCostingExtraction").text("<?php echo $this->lang->line('save'); ?>");
                            $('#extraction_suppliers').val('0'); // Set the value
                            $('#extraction_suppliers').trigger('change'); // Trigger the change event to update Select2
                        }

                        $('#xin_table_extraction').DataTable().ajax.reload(null, false);
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

        $("#btnResetCostingExtraction").click(function() {
            $("#extraction_date").val("");
            $("#extraction_quantity").val("");
            $("#extraction_total_value").val("");
            $("#extraction_claim_remarks").val("");
            $("#error-extraction_date").hide();
            $("#error-extraction_quantity").hide();
            $("#error-extraction_total_value").hide();
            $("#error-extraction_suppliers").hide();
            $("#extraction_total_value").removeAttr("disabled");
            $("#hdnEditId_extraction").val("0");
            $('#extraction_suppliers').val('0'); // Set the value
            $('#extraction_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#xin_table_extraction').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingExtraction").text("<?php echo $this->lang->line('save'); ?>");
        });

        $('#xin_table_extraction').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=1",
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

        $('#extraction_quantity').on('keyup', function() {
            var extraction_quantity = $("#extraction_quantity").val();

            if (extraction_quantity != "" && extraction_cost_farm > 0) {
                $("#extraction_total_value").val((extraction_quantity * extraction_cost_farm).toFixed(2));
            } else if (extraction_quantity == "") {
                $("#extraction_total_value").val("0");
            } else {
                $("#extraction_total_value").val("0");
            }
        });

        //END EXTRACTION

        //ACPM

        $("#error-acpm_suppliers").hide();
        $("#error-acpm_date").hide();
        $("#error-acpm_quantity").hide();
        $("#error-acpm_total_value").hide();
        $("#error-acpm_purchaser").hide();
        $("#error-acpm_invoice_number").hide();

        $("#acpm_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#flexSwitchCheckChecked").change(function() {
            if ($(this).is(":checked")) {
                $("#acpm_total_value").attr("disabled", true);
                $("#acpm_total_value").val("");
            } else {
                $("#acpm_total_value").attr("disabled", false);
            }
        });

        $("#btnSaveCostingACPM").click(function() {
            var acpm_suppliers = $("#acpm_suppliers").val();
            var acpm_date = $("#acpm_date").val();
            var acpm_quantity = $("#acpm_quantity").val();
            var acpm_total_value = $("#acpm_total_value").val();
            var acpm_claim_remarks = $("#acpm_claim_remarks").val();
            var flexSwitchCheckChecked = $("#flexSwitchCheckChecked").is(":checked");
            var acpm_purchaser = $("#acpm_purchaser").val();
            var acpm_invoice_number = $("#acpm_invoice_number").val();
            var editId = $("#hdnEditId_acpm").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true;

            if (acpm_date == "") {
                $("#error-acpm_date").show();
                isValid1 = false;
            } else {
                $("#error-acpm_date").hide();
                isValid1 = true;
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

            if (flexSwitchCheckChecked) {
                acpm_total_value = 0;
                isValid3 = true;
            } else {

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
            }

            if (acpm_suppliers == 0) {
                $("#error-acpm_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-acpm_suppliers").hide();
                isValid4 = true;
            }

            // if (acpm_purchaser == 0) {
            //     $("#error-acpm_purchaser").show();
            //     isValid5 = false;
            // } else {
            //     $("#error-acpm_purchaser").hide();
            //     isValid5 = true;
            // }

            // if (acpm_invoice_number == "") {
            //     $("#error-acpm_invoice_number").show();
            //     isValid6 = false;
            // } else {
            //     $("#error-acpm_invoice_number").hide();
            //     isValid6 = true;
            // }

            expenseType = 0;
            if (flexSwitchCheckChecked == true) {
                expenseType = 1;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("acpmDate", acpm_date);
                fd.append("acpmQuantity", acpm_quantity);
                fd.append("acpmTotalValue", acpm_total_value);
                fd.append("acpmClaimRemarks", acpm_claim_remarks);
                fd.append("acpmSuppliers", acpm_suppliers);
                fd.append("isPurchasedSpend", expenseType);
                fd.append("acpmPurchaser", acpm_purchaser);
                fd.append("acpmInvoiceNumber", acpm_invoice_number);
                fd.append("costType", 4);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "farmcosting");
                fd.append("action_type", "saveACPM");
                fd.append("edit_id", editId);

                $('#loading').show();
                $.ajax({
                    url: base_url + "/save_farm_costing",
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
                            $("#flexSwitchCheckChecked").prop("checked", false);
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
                            $("#hdnEditId_acpm").val("0");
                            $("#btnSaveCostingACPM").text("<?php echo $this->lang->line('save'); ?>");
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
            $("#flexSwitchCheckChecked").prop("checked", false);
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
            $("#hdnEditId_acpm").val("0");
            $('#xin_table_acpm').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingACPM").text("<?php echo $this->lang->line('save'); ?>");
        });

        $('#xin_table_acpm').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=4",
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

        //END ACPM

        //MAINTENANCE

        $("#error-maintainance_suppliers").hide();
        $("#error-maintainance_date").hide();
        $("#error-maintainance_machine_type").hide();
        $("#error-maintainance_concept").hide();
        $("#error-maintainance_subtotal").hide();
        $("#error-maintainance_tax").hide();
        $("#error-maintainance_amount").hide();
        $("#error-maintainance_purchaser").hide();
        $("#error-maintainance_invoice_number").hide();

        $("#maintainance_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingMaintainance").click(function() {
            var maintainance_date = $("#maintainance_date").val();
            var maintainance_machine_type = $("#maintainance_machine_type").val();
            var maintainance_concept = $("#maintainance_concept").val();
            var maintainance_subtotal = $("#maintainance_subtotal").val();
            var maintainance_tax = $("#maintainance_tax").val();
            var maintainance_amount = $("#maintainance_amount").val();
            var maintainance_claim_remarks = $("#maintainance_claim_remarks").val();
            var maintainance_suppliers = $("#maintainance_suppliers").val();
            var maintainance_purchaser = $("#maintainance_purchaser").val();
            var maintainance_invoice_number = $("#maintainance_invoice_number").val();
            var editId = $("#hdnEditId_maintenance").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true;

            if (maintainance_date == "") {
                $("#error-maintainance_date").show();
                isValid1 = false;
            } else {
                $("#error-maintainance_date").hide();
                isValid1 = true;
            }

            if (maintainance_machine_type == "") {
                $("#error-maintainance_machine_type").show();
                isValid2 = false;
            } else {
                $("#error-maintainance_machine_type").hide();
                isValid2 = true;
            }

            if (maintainance_concept == "") {
                $("#error-maintainance_concept").show();
                isValid3 = false;
            } else {
                if (maintainance_concept <= 0) {
                    $("#error-maintainance_concept").text(error_zero_value);
                    $("#error-maintainance_concept").show();
                    isValid3 = false;
                } else {
                    $("#error-maintainance_concept").hide();
                    isValid3 = true;
                }
            }

            if (maintainance_amount == "") {
                $("#error-maintainance_amount").show();
                isValid4 = false;
            } else {
                if (maintainance_amount <= 0) {
                    $("#error-maintainance_amount").text(error_zero_value);
                    $("#error-maintainance_amount").show();
                    isValid4 = false;
                } else {
                    $("#error-maintainance_amount").hide();
                    isValid4 = true;
                }
            }

            if (maintainance_subtotal == "") {
                $("#error-maintainance_subtotal").show();
                isValid6 = false;
            } else {
                if (maintainance_subtotal <= 0) {
                    $("#error-maintainance_subtotal").text(error_zero_value);
                    $("#error-maintainance_subtotal").show();
                    isValid6 = false;
                } else {
                    $("#error-maintainance_subtotal").hide();
                    isValid6 = true;
                }
            }

            if (maintainance_tax == "") {
                $("#error-maintainance_tax").show();
                isValid7 = false;
            } else {
                // if (maintainance_tax <= 0) {
                //     $("#error-maintainance_tax").text(error_zero_value);
                //     $("#error-maintainance_tax").show();
                //     isValid7 = false;
                // } else {
                    $("#error-maintainance_tax").hide();
                    isValid7 = true;
                //}
            }

            if (maintainance_suppliers == 0) {
                $("#error-maintainance_suppliers").show();
                isValid5 = false;
            } else {
                $("#error-maintainance_suppliers").hide();
                isValid5 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("maintainanceDate", maintainance_date);
                fd.append("maintainanceMachineType", maintainance_machine_type);
                fd.append("maintainanceConcept", maintainance_concept);
                fd.append("maintainanceSubTotal", maintainance_subtotal);
                fd.append("maintainanceTax", maintainance_tax);
                fd.append("maintainanceAmount", maintainance_amount);
                fd.append("maintainanceClaimRemarks", maintainance_claim_remarks);
                fd.append("maintainanceSuppliers", maintainance_suppliers);
                fd.append("maintainanceInvoiceNumber", maintainance_invoice_number);
                fd.append("maintainancePurchaser", maintainance_purchaser);
                fd.append("costType", 5);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "farmcosting");
                fd.append("action_type", "saveMaintenance");
                fd.append("edit_id", editId);

                $('#loading').show();
                $.ajax({
                    url: base_url + "/save_farm_costing",
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
                            $("#maintainance_machine_type").val("0");
                            $("#maintainance_machine_type").trigger('change');
                            $("#maintainance_concept").val("");
                            $("#maintainance_subtotal").val("");
                            $("#maintainance_tax").val("");
                            $("#maintainance_amount").val("");
                            $("#maintainance_claim_remarks").val("");
                            $("#error-maintainance_date").hide();
                            $("#error-maintainance_machine_type").hide();
                            $("#error-maintainance_concept").hide();
                            $("#error-maintainance_subtotal").hide();
                            $("#error-maintainance_tax").hide();
                            $("#error-maintainance_amount").hide();
                            $("#error-maintainance_suppliers").hide();
                            $('#maintainance_suppliers').val('0'); // Set the value
                            $('#maintainance_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#maintainance_invoice_number').val("");
                            $('#maintainance_purchaser').val('0'); // Set the value
                            $('#maintainance_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $("#hdnEditId_maintenance").val("0");
                            $("#btnSaveCostingMaintainance").text("<?php echo $this->lang->line('save'); ?>");
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
            $("#maintainance_date").val("");
            $("#maintainance_machine_type").val("0");
            $("#maintainance_machine_type").trigger('change');
            $("#maintainance_concept").val("");
            $("#maintainance_subtotal").val("");
            $("#maintainance_tax").val("");
            $("#maintainance_amount").val("");
            $("#maintainance_claim_remarks").val("");
            $("#error-maintainance_date").hide();
            $("#error-maintainance_machine_type").hide();
            $("#error-maintainance_concept").hide();
            $("#error-maintainance_subtotal").hide();
            $("#error-maintainance_tax").hide();
            $("#error-maintainance_amount").hide();
            $("#error-maintainance_suppliers").hide();
            $('#maintainance_suppliers').val('0'); // Set the value
            $('#maintainance_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#maintainance_invoice_number').val("");
            $('#maintainance_purchaser').val('0'); // Set the value
            $('#maintainance_purchaser').trigger('change'); // Trigger the change event to update Select2
            $("#hdnEditId_maintenance").val("0");
            $('#xin_table_maintenance').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingMaintainance").text("<?php echo $this->lang->line('save'); ?>");
        });

        $('#xin_table_maintenance').DataTable({
            destroy: true,
            lengthMenu: [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            ajax: {
                url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=5",
                type: 'GET'
            },
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            searching: true,
            order: [
                [0, "asc"]
            ],
            language: {
                url: datatable_language
            },
            fixedColumns: {
                leftColumns: 1 // change to 0 if you don’t need fixed col
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
            $("#loading").show();
            $.ajax({
                url: base_url + "/fetch_machines?originid=" + $("#origin").val() + "&supplierid=" + $(this).val(),
                cache: false,
                method: "GET",
                dataType: 'json',
                success: function(JSON) {
                    $("#loading").hide();
                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {
                        $("#maintainance_machine_type").empty();
                        $("#maintainance_machine_type").append(JSON.result);
                        $("#maintainance_machine_type").select2({
                            dropdownCssClass: "myFont",
                        });
                    }
                }
            });
        });

        //END MAINTENANCE

        //MISCELLANEOUS

        $("#error-miscellaneous_suppliers").hide();
        $("#error-miscellaneous_date").hide();
        $("#error-miscellaneous_concept").hide();
        $("#error-miscellaneous_amount").hide();
        $("#error-miscellaneous_purchaser").hide();
        $("#error-miscellaneous_invoice_number").hide();

        $("#miscellaneous_date").datepicker({
            dateFormat: "dd/mm/yy"
        });

        $("#btnSaveCostingMiscellaneous").click(function() {
            var miscellaneous_date = $("#miscellaneous_date").val();
            var miscellaneous_concept = $("#miscellaneous_concept").val();
            var miscellaneous_amount = $("#miscellaneous_amount").val();
            var miscellaneous_claim_remarks = $("#miscellaneous_claim_remarks").val();
            var miscellaneous_suppliers = $("#miscellaneous_suppliers").val();
            var miscellaneous_purchaser = $("#miscellaneous_purchaser").val();
            var miscellaneous_invoice_number = $("#miscellaneous_invoice_number").val();
            var editId = $("#hdnEditId_miscellaneous").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (miscellaneous_date == "") {
                $("#error-miscellaneous_date").show();
                isValid1 = false;
            } else {
                $("#error-miscellaneous_date").hide();
                isValid1 = true;
            }

            if (miscellaneous_concept == "") {
                $("#error-miscellaneous_concept").show();
                isValid2 = false;
            } else {
                if (miscellaneous_concept <= 0) {
                    $("#error-miscellaneous_concept").text(error_zero_value);
                    $("#error-miscellaneous_concept").show();
                    isValid2 = false;
                } else {
                    $("#error-miscellaneous_concept").hide();
                    isValid2 = true;
                }
            }

            if (miscellaneous_amount == "") {
                $("#error-miscellaneous_amount").show();
                isValid3 = false;
            } else {
                if (miscellaneous_amount <= 0) {
                    $("#error-miscellaneous_amount").text(error_zero_value);
                    $("#error-miscellaneous_amount").show();
                    isValid3 = false;
                } else {
                    $("#error-miscellaneous_amount").hide();
                    isValid3 = true;
                }
            }

            if (miscellaneous_suppliers == 0) {
                $("#error-miscellaneous_suppliers").show();
                isValid4 = false;
            } else {
                $("#error-miscellaneous_suppliers").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {
                var fd = new FormData();
                fd.append("originId", $("#origin").val());
                fd.append("miscellaneousDate", miscellaneous_date);
                fd.append("miscellaneousConcept", miscellaneous_concept);
                fd.append("miscellaneousAmount", miscellaneous_amount);
                fd.append("miscellaneousClaimRemarks", miscellaneous_claim_remarks);
                fd.append("miscellaneousSuppliers", miscellaneous_suppliers);
                fd.append("miscellaneousPurchaser", miscellaneous_purchaser);
                fd.append("miscellaneousInvoiceNumber", miscellaneous_invoice_number);
                fd.append("costType", 6);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", "farmcosting");
                fd.append("action_type", "saveMiscellaneous");
                fd.append("edit_id", editId);

                $('#loading').show();
                $.ajax({
                    url: base_url + "/save_farm_costing",
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

                            $("#miscellaneous_date").val("");
                            $("#miscellaneous_concept").val("");
                            $("#miscellaneous_amount").val("");
                            $("#miscellaneous_claim_remarks").val("");
                            $("#error-miscellaneous_date").hide();
                            $("#error-miscellaneous_concept").hide();
                            $("#error-miscellaneous_amount").hide();
                            $("#error-miscellaneous_suppliers").hide();
                            $('#miscellaneous_suppliers').val('0'); // Set the value
                            $('#miscellaneous_suppliers').trigger('change'); // Trigger the change event to update Select2
                            $('#miscellaneous_invoice_number').val("");
                            $('#miscellaneous_purchaser').val('0'); // Set the value
                            $('#miscellaneous_purchaser').trigger('change'); // Trigger the change event to update Select2
                            $("#hdnEditId_miscellaneous").val("0");
                            $("#btnSaveCostingMiscellaneous").text("<?php echo $this->lang->line('save'); ?>");
                        }

                        $('#xin_table_miscellaneous').DataTable().ajax.reload(null, false);
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

        $("#btnResetCostingMiscellaneous").click(function() {
            $("#miscellaneous_date").val("");
            $("#miscellaneous_concept").val("");
            $("#miscellaneous_amount").val("");
            $("#miscellaneous_claim_remarks").val("");
            $("#error-miscellaneous_date").hide();
            $("#error-miscellaneous_concept").hide();
            $("#error-miscellaneous_amount").hide();
            $("#error-miscellaneous_suppliers").hide();
            $('#miscellaneous_suppliers').val('0'); // Set the value
            $('#miscellaneous_suppliers').trigger('change'); // Trigger the change event to update Select2
            $('#miscellaneous_invoice_number').val("");
            $('#miscellaneous_purchaser').val('0'); // Set the value
            $('#miscellaneous_purchaser').trigger('change'); // Trigger the change event to update Select2
            $("#hdnEditId_miscellaneous").val("0");
            $('#xin_table_miscellaneous').DataTable().ajax.reload(null, false);
            $("#btnSaveCostingMiscellaneous").text("<?php echo $this->lang->line('save'); ?>");
        });

        $('#xin_table_miscellaneous').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            "ajax": {
                url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=6",
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

        //END MISCELLANEOUS

        $("#origin").change(function() {

            $('#xin_table_extraction').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                "ajax": {
                    url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=1",
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

            $('#xin_table_acpm').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                "ajax": {
                    url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=4",
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

            $('#xin_table_maintenance').DataTable({
                destroy: true,
                lengthMenu: [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                ajax: {
                    url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=5",
                    type: 'GET'
                },
                scrollX: true,
                scrollCollapse: true,
                paging: true,
                searching: true,
                order: [
                    [0, "asc"]
                ],
                language: {
                    url: datatable_language
                },
                fixedColumns: {
                    leftColumns: 1 // change to 0 if you don’t need fixed col
                }
            });

            $('#xin_table_miscellaneous').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                "ajax": {
                    url: base_url + "/farmcosting_list?originId=" + $("#origin").val() + "&costType=6",
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

            fetchSuppliers();
            fetchPurchasers();
        });

        // $("#btnResetCostingExtraction").trigger("click");
        // $("#btnResetCostingACPM").trigger("click");
        // $("#btnResetCostingMaintainance").trigger("click");
        // $("#btnResetCostingMiscellaneous").trigger("click");

        fetchSuppliers();
        fetchPurchasers();
    });

    function fetchSuppliers() {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_suppliers?originid=" + $("#origin").val(),
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#extraction_suppliers").empty();
                    $("#extraction_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#extraction_suppliers").append(JSON.result);

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

                    $("#miscellaneous_suppliers").empty();
                    $("#miscellaneous_suppliers").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#miscellaneous_suppliers").append(JSON.result);
                }
            }
        });
    }

    function fetchPurchasers() {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=4",
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
            url: base_url + "/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=5",
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
            url: base_url + "/fetch_purchasers?originid=" + $("#origin").val() + "&costingtype=6",
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {

                    $("#miscellaneous_purchaser").empty();
                    $("#miscellaneous_purchaser").select2({
                        dropdownCssClass: "myFont"
                    });
                    $("#miscellaneous_purchaser").append(JSON.result);
                }
            }
        });
    }

    var loadFileACPM = function(event) {
        event.preventDefault();

        var files = $('#acpm_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('acpm_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 4);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
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

    var loadFileMaintenance = function(event) {
        event.preventDefault();

        var files = $('#maintenance_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('maintenance_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 5);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
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

    var loadFileMiscellaneous = function(event) {
        event.preventDefault();

        var files = $('#miscellaneous_xmlupload')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('miscellaneous_xmlupload', files);
            fd.append('originId', $("#origin").val());
            fd.append('costingType', 6);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
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

                            $("#miscellaneous_purchaser").append($("<option>", {
                                value: jsonResult.result["supplierId"],
                                text: jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]
                            }));
                        }

                        $("#miscellaneous_purchaser").select2().val(jsonResult.result["supplierId"]).trigger("change");
                        $("#miscellaneous_invoice_number").val(jsonResult.result["documentId"]);
                        $("#miscellaneous_date").val(jsonResult.result["issueDate"]);
                        $("#miscellaneous_concept").val(jsonResult.result["description"]);
                        $("#miscellaneous_amount").val(jsonResult.result["payableAmount"]);
                        $("#miscellaneous_xmlupload").val("");

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#miscellaneous_xmlupload").val("");

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#miscellaneous_xmlupload").val("");
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            $("#miscellaneous_xmlupload").val("");
        }
    };
</script>