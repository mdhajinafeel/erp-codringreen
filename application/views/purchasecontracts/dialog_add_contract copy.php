<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_contract', 'id' => 'add_contract', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('purchasecontracts/add', $attributes, $hidden); ?>
<div class="modal-body supplier-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdncontractid" name="hdncontractid" value="<?php echo $contractid;  ?>">
    <input type="hidden" id="hdncontractcode" name="hdncontractcode" value="<?php echo isset($get_contract_details[0]->contract_code) ? $get_contract_details[0]->contract_code : ''; ?>">

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="origin_contracts"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_contracts" id="origin_contracts" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_contract_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="contract_type"><?php echo $this->lang->line('contract_type'); ?></label>
            <select class="form-control" name="contract_type" id="contract_type" data-plugin="select_erp">
                <option value="1" <?php if ($get_contract_details[0]->contract_type == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('warehouse'); ?></option>
                <option value="2" <?php if ($get_contract_details[0]->contract_type == 2) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('field_purchase'); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="supplier_pm_name"><?php echo $this->lang->line('supplier_pm_name'); ?></label>
            <select class="form-control" name="supplier_pm_name" id="supplier_pm_name" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->id; ?>" <?php if ($get_contract_details[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="product_name"><?php echo $this->lang->line('product_name'); ?></label>
            <select class="form-control" name="product_name" id="product_name" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product->product_id; ?>" <?php if ($get_contract_details[0]->product == $product->product_id) : ?> selected="selected" <?php endif; ?>><?php echo $product->product_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-productname" class="error-text"><?php echo $this->lang->line('error_product_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="product_type"><?php echo $this->lang->line('product_type'); ?></label>
            <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($product_types as $product_type) { ?>
                        <option value="<?php echo $product_type->type_id; ?>" <?php if ($get_contract_details[0]->product_type == $product_type->type_id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($product_type->product_type_name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-woodtype" class="error-text"><?php echo $this->lang->line('error_selectwoodtype'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="measuremet_system"><?php echo $this->lang->line('measuremet_system'); ?></label>
            <select class="form-control" name="measuremet_system" id="measuremet_system" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($measurement_systems as $measurement_system) { ?>
                        <option onchange="" value="<?php echo $measurement_system->id; ?>" <?php if ($get_contract_details[0]->unit_of_purchase == $measurement_system->id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($measurement_system->purchase_unit); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-measuremetsystem" class="error-text"><?php echo $this->lang->line('error_measuremet_system'); ?></label>
        </div>
    </div>

    <?php if ($pagetype == "edit" && ($get_contract_details[0]->product_type == 2 || $get_contract_details[0]->product_type == 4)) { ?>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="circumference_allowance"><?php echo $this->lang->line('circumference_allowance'); ?></label>
                <input type="text" name="circumference_allowance" id="circumference_allowance" value="<?php echo ($get_contract_details[0]->purchase_allowance + 0); ?>" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('circumference_allowance'); ?>" class="form-control" disabled />
                <label id="error-circumferenceallowance" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>
            <div class="col-md-6">
                <label for="length_allowance"><?php echo $this->lang->line('length_allowance'); ?></label>
                <input type="text" name="length_allowance" id="length_allowance" value="<?php echo ($get_contract_details[0]->purchase_allowance_length + 0); ?>" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('length_allowance'); ?>" class="form-control" disabled />
                <label id="error-lengthallowance" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>
        </div>
    <?php } else { ?>
        <div class="row mb-3" id="divAllowance">
            <div class="col-md-6 mb-2">
                <label for="circumference_allowance" id="txtcircallowance"><?php echo $this->lang->line('circumference_allowance'); ?></label>
                <input type="text" name="circumference_allowance" id="circumference_allowance" value="0" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('circumference_allowance'); ?>" class="form-control" disabled />
                <label id="error-circumferenceallowance" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>
            <div class="col-md-6">
                <label for="length_allowance"><?php echo $this->lang->line('length_allowance'); ?></label>
                <input type="text" name="length_allowance" id="length_allowance" value="0" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('length_allowance'); ?>" class="form-control" disabled />
                <label id="error-lengthallowance" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>
        </div>
    <?php } ?>

    <?php if ($get_contract_details[0]->product < 4) { ?>
        <?php if ($pagetype == "edit" && ($get_contract_details[0]->product_type == 1 || $get_contract_details[0]->product_type == 3)) { ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contract_square_price"><?php echo $this->lang->line('contract_price'); ?></label>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade1'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade1" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade1" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo ($contractprice[0]->minrange_grade1 + 0); ?>">
                            <label id="error-pricegrade1" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade2'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade2" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade2" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo ($contractprice[0]->maxrange_grade2 + 0); ?>">
                            <label id="error-pricegrade2" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade3'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade3" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade3" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo ($contractprice[0]->pricerange_grade3 + 0); ?>">
                            <label id="error-pricegrade3" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="row mb-3" id="divSquareBlockPrice">
                <div class="col-md-6">
                    <label for="contract_square_price"><?php echo $this->lang->line('contract_price'); ?></label>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade1'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade1" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade1" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                            <label id="error-pricegrade1" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade2'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade2" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade2" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                            <label id="error-pricegrade2" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"><?php echo $this->lang->line('grade3'); ?></label>
                        <div class="col-md-5">
                            <input type="number" id="price_grade3" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_grade3" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                            <label id="error-pricegrade3" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if ($pagetype == "edit") { ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="contract_round_price"><?php echo $this->lang->line('contract_price'); ?></label>
            </div>

            <div>
                <fieldset id="rdo_price_type" class="mb-3">
                    <input type="radio" id="rdo_manual_entry" name="rdo_price_type" value="1" checked><label class="radio-inline" for="rdo_manual_entry"><?php echo $this->lang->line('manual_entry'); ?></label>
                    <input type="radio" id="rdo_excel_upload" name="rdo_price_type" value="2" class="ml-20"><label class="radio-inline" for="rdo_excel_upload"><?php echo $this->lang->line('excel_upload'); ?></label>
                </fieldset>

                <div id="divManualEntry">

                    <?php $j = 1; ?>
                    <?php foreach ($contractprice as $price) { ?>

                        <?php if ($j == 1) { ?>
                            <div class="row DataRow">
                                <div class="col-md-3">
                                    <input type="number" id="min_range" placeholder="<?php echo $this->lang->line('min_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="min_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->minrange_grade1 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <input type="number" id="max_range" placeholder="<?php echo $this->lang->line('max_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="max_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->maxrange_grade2 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <input type="number" id="price_range" placeholder="<?php echo $this->lang->line('range_price'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->pricerange_grade3 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <button type="button" name="add_price" id="add_price" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row DataRow" id="row_removeprice_<?php echo $j; ?>">
                                <div class="col-md-3">
                                    <input type="number" id="min_range" placeholder="<?php echo $this->lang->line('min_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="min_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->minrange_grade1 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <input type="number" id="max_range" placeholder="<?php echo $this->lang->line('max_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="max_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->maxrange_grade2 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <input type="number" id="price_range" placeholder="<?php echo $this->lang->line('range_price'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="any" value="<?php echo ($price->pricerange_grade3 + 0); ?>">
                                </div>

                                <div class="col-md-3">
                                    <button type="button" name="remove" id="removeprice_<?php echo $j; ?>" class="btn btn-danger btn_remove_price addicon"><i class="fas fa-remove"></i></button>
                                </div>
                            </div>

                        <?php } ?>

                        <?php $j++;
                        echo "<script type='text/javascript'>";
                        echo "var j = " . $j;
                        echo "</script>";
                        ?>
                    <?php } ?>
                </div>

                <div class="row" id="divExcelUpload">
                    <div class="col-md-5">
                        <input name="filePriceExcel" type="file" accept=".xlsx" id="filePriceExcel" onchange="loadFile(event)" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url() . 'assets/templates/Template_PriceUpload.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template'); ?></span></a>
                    </div>

                    <div id="divRoundLogPrice">
                        <div class="row mt-3 mb-2">
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('min_range'); ?></label>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('max_range'); ?></label>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('range_price'); ?></label>
                            </div>
                        </div>
                        <div id="divPriceFields">
                            <div class="row DataRow">
                                <div class="col-md-3">
                                    <label class="col-form-label" id="minRangeUpload" name="minRangeUpload"></label>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-form-label" id="maxRangeUpload" name="maxRangeUpload"></label>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-form-label" id="priceRangeUpload" name="priceRangeUpload"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row mb-3" id="divRoundLogsPrice">
            <div class="col-md-6">
                <label for="contract_round_price"><?php echo $this->lang->line('contract_price'); ?></label>
            </div>

            <div>
                <fieldset id="rdo_price_type" class="mb-3">
                    <input type="radio" id="rdo_manual_entry" name="rdo_price_type" value="1" checked><label class="radio-inline" for="rdo_manual_entry"><?php echo $this->lang->line('manual_entry'); ?></label>
                    <input type="radio" id="rdo_excel_upload" name="rdo_price_type" value="2" class="ml-20"><label class="radio-inline" for="rdo_excel_upload"><?php echo $this->lang->line('excel_upload'); ?></label>
                </fieldset>

                <div id="divManualEntry">
                    <?php
                    echo "<script type='text/javascript'>";
                    echo "var j = 1";
                    echo "</script>";
                    ?>
                    <div class="row DataRow">
                        <div class="col-md-3">
                            <input type="number" id="min_range" placeholder="<?php echo $this->lang->line('min_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="min_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                        </div>

                        <div class="col-md-3">
                            <input type="number" id="max_range" placeholder="<?php echo $this->lang->line('max_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="max_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                        </div>

                        <div class="col-md-3">
                            <input type="number" id="price_range" placeholder="<?php echo $this->lang->line('range_price'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                        </div>

                        <div class="col-md-3">
                            <button type="button" name="add_price" id="add_price" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row" id="divExcelUpload">
                    <div class="col-md-5">
                        <input name="filePriceExcel" type="file" accept=".xlsx" id="filePriceExcel" onchange="loadFile(event)" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url() . 'assets/templates/Template_PriceUpload.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template'); ?></span></a>
                    </div>

                    <div id="divRoundLogPrice">
                        <div class="row mt-3 mb-2">
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('min_range'); ?></label>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('max_range'); ?></label>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('range_price'); ?></label>
                            </div>
                        </div>
                        <div id="divPriceFields">
                            <div class="row DataRow">
                                <div class="col-md-3">
                                    <label class="col-form-label" id="minRangeUpload" name="minRangeUpload"></label>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-form-label" id="maxRangeUpload" name="maxRangeUpload"></label>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-form-label" id="priceRangeUpload" name="priceRangeUpload"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="currency"><?php echo $this->lang->line('currency'); ?></label>
            <select class="form-control" name="currency" id="currency" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($currencies as $currency) { ?>
                        <option value="<?php echo $currency->currency_id; ?>" <?php if ($get_contract_details[0]->currency == $currency->currency_id) : ?> selected="selected" <?php endif; ?>><?php echo $currency->currency; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-currency" class="error-text"><?php echo $this->lang->line('error_currency'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="payment_method"><?php echo $this->lang->line('payment_method'); ?></label>
            <select class="form-control" name="payment_method" id="payment_method" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($payment_methods as $payment_method) { ?>
                        <option value="<?php echo $payment_method->id; ?>" <?php if ($get_contract_details[0]->payment_method == $payment_method->id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($payment_method->payment_name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-paymentmethod" class="error-text"><?php echo $this->lang->line('error_payment_method'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="start_date"><?php echo $this->lang->line('start_date'); ?></label>
            <?php if ($pagetype == 'edit') { ?>
                <input type="text" id="start_date" name="start_date" class="form-control start_date" value="<?php echo $get_contract_details[0]->start_date; ?>" readonly />
            <?php } else { ?>
                <input type="text" id="start_date" name="start_date" class="form-control start_date" readonly />
            <?php } ?>
            <label id="error-startdate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="end_date"><?php echo $this->lang->line('end_date'); ?></label>
            <?php if ($pagetype == 'edit') { ?>
                <input type="text" id="end_date" name="end_date" class="form-control end_date" value="<?php echo $get_contract_details[0]->end_date; ?>" readonly />
            <?php } else { ?>
                <input type="text" id="end_date" name="end_date" class="form-control end_date" readonly />
            <?php } ?>
            <label id="error-enddate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="total_volume"><?php echo $this->lang->line('total_volume'); ?></label>
            <?php if ($pagetype == 'edit') { ?>
                <input type="number" id="total_volume" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="total_volume" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo ($get_contract_details[0]->total_volume + 0); ?>">
            <?php } else { ?>
                <input type="number" id="total_volume" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="total_volume" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <?php } ?>
            <label id="error-totalvolume" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="status"><?php echo $this->lang->line('status'); ?></label>
            <select class="form-control" name="status" id="status" data-plugin="select_erp">
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('active'); ?></option>
                    <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_contract_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
                    <option value="0" <?php if ($get_contract_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success add_contract', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var upload_price_details = "<?php echo $this->lang->line('upload_price_details'); ?>";
    var enter_min_range = "<?php echo $this->lang->line('enter_min_range'); ?>";
    var enter_max_range = "<?php echo $this->lang->line('enter_max_range'); ?>";
    var enter_price_range = "<?php echo $this->lang->line('enter_price_range'); ?>";
    var enter_min_range_check = "<?php echo $this->lang->line('enter_min_range_check'); ?>";
    var enter_last_range_check = "<?php echo $this->lang->line('enter_last_range_check'); ?>";
    var error_value = "<?php echo $this->lang->line('error_value'); ?>";
    var error_zero_value = "<?php echo $this->lang->line('error_zero_value'); ?>";
    var circumference_allowance = "<?php echo $this->lang->line('circumference_allowance'); ?>";
    var dia_allowance = "<?php echo $this->lang->line('dia_allowance'); ?>";

    var selectedPriceTypeOption = 1;
    var price_array = [];
    var pricefields = "";

    //$("#myMultipleSelect2").val(5).trigger('change');

    $("#error-origin").hide();
    $("#error-name").hide();
    $("#error-productname").hide();
    $("#error-woodtype").hide();
    $("#error-measuremetsystem").hide();
    $("#error-circumferenceallowance").hide();
    $("#error-lengthallowance").hide();
    $("#divAllowance").hide();
    $("#divPrice").hide();
    $("#divSquareBlockPrice").hide();
    $("#divRoundLogsPrice").hide();
    $("#error-currency").hide();
    $("#error-paymentmethod").hide();
    $("#error-startdate").hide();
    $("#error-enddate").hide();
    $("#error-totalvolume").hide();
    $("#divExcelUpload").hide();
    $("#divRoundLogPrice").hide();
    $("#error-pricegrade1").hide();
    $("#error-pricegrade2").hide();
    $("#error-pricegrade3").hide();

    $(document).ready(function() {

        $("#origin_contracts").change(function() {

            if ($("#origin_contracts").val() == 0) {
                $("#supplier_pm_name").attr("disabled", true);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#measuremet_system").attr("disabled", true);
                fetchSupplierPurchaseManager(0, 0);
                fetchProducts(0, 0, 0);
                fetchProductTypes(0, 0, 0, 0);
                fetchMeasurementSystem(0, 0);
                showPrices(0, 0);
                fetchCurrencies(0);
                $("#currency").attr("disabled", true);
                fetchPaymentMethods(0);
                $("#payment_method").attr("disabled", false);
            } else {
                fetchSupplierPurchaseManager($("#origin_contracts").val(), $("#contract_type").val());
                $("#supplier_pm_name").attr("disabled", false);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#measuremet_system").attr("disabled", true);
                fetchProducts(0, 0, 0);
                fetchProductTypes(0, 0, 0, 0);
                fetchMeasurementSystem(0, 0);
                showPrices(0, 0);
                $("#error-origin").hide();
                fetchCurrencies($("#origin_contracts").val());
                $("#currency").attr("disabled", false);
                fetchPaymentMethods($("#origin_contracts").val());
                $("#payment_method").attr("disabled", false);
            }
        });

        $("#contract_type").change(function() {
            fetchSupplierPurchaseManager($("#origin_contracts").val(), $("#contract_type").val());
            fetchProducts(0, 0, 0);
            fetchProductTypes(0, 0, 0, 0);
            fetchMeasurementSystem(0,0);
            showPrices(0, 0);
            $("#product_name").attr("disabled", true);
            $("#product_type").attr("disabled", true);
            $("#measuremet_system").attr("disabled", true);
        });

        $("#supplier_pm_name").change(function() {

            if ($("#supplier_pm_name").val() == 0) {
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#measuremet_system").attr("disabled", true);
                fetchProducts(0, 0, 0);
                fetchProductTypes(0, 0, 0, 0);
                fetchMeasurementSystem(0);
                showPrices(0, 0);
            } else {
                fetchProducts($("#origin_contracts").val(), $("#contract_type").val(), $("#supplier_pm_name").val());
                $("#product_name").attr("disabled", false);
                $("#product_type").attr("disabled", true);
                $("#measuremet_system").attr("disabled", true);
                fetchProductTypes(0, 0, 0, 0);
                fetchMeasurementSystem(0);
                showPrices(0, 0);
                $("#error-name").hide();
            }
        });

        $("#product_name").change(function() {

            if ($("#product_name").val() == 0) {
                $("#product_type").attr("disabled", true);
                $("#measuremet_system").attr("disabled", true);
                fetchProductTypes(0, 0, 0, 0);
                fetchMeasurementSystem(0, 0);
                showPrices(0, 0);
            } else {
                fetchProductTypes($("#origin_contracts").val(), $("#contract_type").val(), $("#supplier_pm_name").val(), $("#product_name").val());
                $("#product_type").attr("disabled", false);
                $("#measuremet_system").attr("disabled", true);
                fetchMeasurementSystem(0, 0);
                showPrices(0, 0);
                $("#error-productname").hide();
            }
        });

        $("#product_type").change(function() {

            if ($("#product_type").val() == 0) {
                $("#measuremet_system").attr("disabled", true);
                fetchMeasurementSystem(0, 0);
                showPrices(0, 0);
            } else {
                $("#measuremet_system").attr("disabled", false);
                fetchMeasurementSystem($("#product_type").val(), $("#origin_contracts").val());
                showPrices($("#product_type").val(), $("#product_name").val());
                $("#error-woodtype").hide();
                $("#divManualEntry").show();
                $("#divExcelUpload").hide();
            }
            $("input[name=rdo_price_type][value='1']").prop('checked', true);
            price_array = [];
            $("#divRoundLogPrice").hide();
            $("#filePriceExcel").val("");
            $("#circumference_allowance").attr("disabled", true);
            $("#length_allowance").attr("disabled", true);
            $("#circumference_allowance").val("0");
            $("#length_allowance").val("0");
        });

        $("#measuremet_system").change(function() {
            if ($("#measuremet_system").val() == 3) {
                $("#circumference_allowance").attr("disabled", true);
                $("#length_allowance").attr("disabled", true);

                $("#circumference_allowance").val("0");
                $("#length_allowance").val("0");
            } else {
                $("#circumference_allowance").attr("disabled", false);
                $("#length_allowance").attr("disabled", false);

                $("#circumference_allowance").val("0");
                $("#length_allowance").val("0");
            }
            $("#error-measuremetsystem").hide();
        });

        $("#currency").change(function() {
            if ($("#currency").val() > 0) {
                $("#error-currency").hide();
            }
        });

        $("#payment_method").change(function() {
            if ($("#payment_method").val() > 0) {
                $("#error-paymentmethod").hide();
            }
        });

        $("#total_volume").change(function() {
            $("#error-totalvolume").hide();
        });

        $('input[type=radio][name=rdo_price_type]').change(function() {
            if (this.value == '1') {
                $("#divManualEntry").show();
                $("#divExcelUpload").hide();
                selectedPriceTypeOption = 1;
            } else if (this.value == '2') {
                $("#divManualEntry").hide();
                $("#divExcelUpload").show();
                selectedPriceTypeOption = 2;
            }

            $("#divRoundLogPrice").hide();
            $("#filePriceExcel").val("");
            price_array = [];

            if ($("#pagetype").val() == "add") {
                $("#price_grade1").val("");
                $("#price_grade2").val("");
                $("#price_grade3").val("");
            }
        });

        $('#add_price').click(function() {
            var lastMaxRange = 0;
            $("[id*=divManualEntry] .DataRow").each(function() {
                var minRange = $(this).closest('div').find('#min_range').val().trim();
                var maxRange = $(this).closest('div').find('#max_range').val().trim();
                var priceRange = $(this).closest('div').find('#price_range').val().trim();
                if (minRange != "" && Number(minRange) > 0) {
                    if (maxRange != "" && Number(maxRange) > 0) {
                        if (priceRange != "" && Number(priceRange) > 0) {
                            if (Number(minRange) >= Number(maxRange)) {
                                toastr.clear();
                                toastr.warning(enter_min_range_check);

                                $(this).closest('div').find('#min_range').focus();
                                isValid = false;
                            } else if (Number(lastMaxRange) >= Number(minRange)) {
                                toastr.clear();
                                toastr.warning(enter_last_range_check);

                                $(this).closest('div').find('#min_range').focus();
                                isValid = false;
                            } else {
                                var priceData = {};
                                priceData.minRange = minRange;
                                priceData.maxRange = maxRange;
                                priceData.price = priceRange;
                                price_array.push(priceData);
                                isValid = true;
                                lastMaxRange = maxRange;
                            }
                        } else {
                            toastr.clear();
                            toastr.warning(enter_price_range);

                            $(this).closest('div').find('#price_range').focus();
                            isValid = false;
                        }
                    } else {

                        toastr.clear();
                        toastr.warning(enter_max_range);

                        $(this).closest('div').find('#max_range').focus();
                        isValid = false;
                    }
                } else {

                    toastr.clear();
                    toastr.warning(enter_min_range);

                    $(this).closest('div').find('#min_range').focus();
                    isValid = false;
                }
            });

            if (isValid) {
                if (j > 45) {
                    toastr.clear();
                    toastr.warning("<?php echo $this->lang->line('price_limit'); ?>");
                    return false;
                }
                j++;

                pricefields = '<div class="row DataRow" id="row_removeprice_' + j + '">';
                pricefields = pricefields + '<div class="col-md-3">';
                pricefields = pricefields + '<input type="number" id="min_range" placeholder="<?php echo $this->lang->line('min_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="min_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">';
                pricefields = pricefields + '</div>';
                pricefields = pricefields + '<div class="col-md-3">';
                pricefields = pricefields + '<input type="number" id="max_range" placeholder="<?php echo $this->lang->line('max_range'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="max_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">';
                pricefields = pricefields + '</div>';
                pricefields = pricefields + '<div class="col-md-3">';
                pricefields = pricefields + '<input type="number" id="price_range" placeholder="<?php echo $this->lang->line('range_price'); ?>" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="price_range" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">';
                pricefields = pricefields + '</div>';
                pricefields = pricefields + '<div class="col-md-3">';
                pricefields = pricefields + '<button type="button" name="remove" id="removeprice_' + j + '" class="btn btn-danger btn_remove_price addicon"><i class="fas fa-remove"></i></button>';
                pricefields = pricefields + '</div>';
                pricefields = pricefields + '</div>';

                $('#divManualEntry').append(pricefields);
            }
        });

        $(document).on('click', '.btn_remove_price', function() {
            var button_id = $(this).attr("id");
            $('#row_' + button_id + '').remove();
            j--;
        });

        $("#add_contract").submit(function(e) {
            e.preventDefault();

            var pagetype = $("#pagetype").val().trim();
            var contractid = $("#hdncontractid").val().trim();
            var contractcode = $("#hdncontractcode").val().trim();
            var origin = $("#origin_contracts").val();
            var contracttype = $("#contract_type").val();
            var suppliername = $("#supplier_pm_name").val();
            var productname = $("#product_name").val();
            var producttype = $("#product_type").val();
            var measuremetsystem = $("#measuremet_system").val();
            var circumferenceallowance = $("#circumference_allowance").val().trim();
            var lengthallowance = $("#length_allowance").val().trim();
            var contractcurrency = $("#currency").val();
            var paymentmethod = $("#payment_method").val();
            var startdate = $("#start_date").val();
            var enddate = $("#end_date").val();
            var totalvolume = $("#total_volume").val();
            var contractstatus = $("#status").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true,
                isValid9 = true,
                isValid10 = true,
                isValid11 = true,
                isValid12 = true,
                isValid13 = true,
                isValid14 = true,
                isValid15 = true;

            if (origin == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (suppliername == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
                isValid2 = true;
            }

            if (productname == 0) {
                $("#error-productname").show();
                isValid3 = false;
            } else {
                $("#error-productname").hide();
                isValid3 = true;
            }

            if (producttype == 0) {
                $("#error-woodtype").show();
                isValid4 = false;
            } else {
                $("#error-woodtype").hide();
                isValid4 = true;

                if (productname == 4 && (producttype == 1 || producttype == 3)) {
                    if (selectedPriceTypeOption == 1) {
                        var lastMaxRange = 0;
                        price_array = [];
                        $("[id*=divManualEntry] .DataRow").each(function() {
                            var minRange = $(this).closest('div').find('#min_range').val().trim();
                            var maxRange = $(this).closest('div').find('#max_range').val().trim();
                            var priceRange = $(this).closest('div').find('#price_range').val().trim();
                            if (minRange != "" && Number(minRange) > 0) {
                                if (maxRange != "" && Number(maxRange) > 0) {
                                    if (priceRange != "" && Number(priceRange) > 0) {

                                        if (Number(minRange) >= Number(maxRange)) {

                                            toastr.clear();
                                            toastr.warning(enter_min_range_check);

                                            $(this).closest('div').find('#min_range').focus();
                                            isValid11 = false;
                                        } else if (Number(lastMaxRange) >= Number(minRange)) {
                                            toastr.clear();
                                            toastr.warning(enter_last_range_check);

                                            $(this).closest('div').find('#min_range').focus();
                                            isValid = false;
                                        } else {
                                            var priceData = {};
                                            priceData.minRange = minRange;
                                            priceData.maxRange = maxRange;
                                            priceData.price = priceRange;
                                            price_array.push(priceData);
                                            isValid11 = true;
                                            lastMaxRange = maxRange;
                                        }
                                    } else {
                                        toastr.clear();
                                        toastr.warning(enter_price_range);

                                        $(this).closest('div').find('#price_range').focus();
                                        isValid11 = false;
                                    }
                                } else {

                                    toastr.clear();
                                    toastr.warning(enter_max_range);

                                    $(this).closest('div').find('#max_range').focus();
                                    isValid11 = false;
                                }
                            } else {

                                toastr.clear();
                                toastr.warning(enter_min_range);

                                $(this).closest('div').find('#min_range').focus();
                                isValid11 = false;
                            }
                        });
                    } else if (selectedPriceTypeOption == 2) {
                        // DO NOTHING
                    }

                    if (isValid11) {
                        if (price_array.length == 0) {
                            toastr.clear();
                            toastr.warning(upload_price_details);
                            isValid11 = false;
                        } else {
                            isValid11 = true;
                        }
                    }
                } else if (producttype == 1 || producttype == 3) {

                    var grade1price = $("#price_grade1").val().trim();
                    var grade2price = $("#price_grade2").val().trim();
                    var grade3price = $("#price_grade3").val().trim();

                    if (grade1price.length == 0) {
                        isValid11 = false;
                        $("#error-pricegrade1").text(error_value);
                        $("#error-pricegrade1").show();
                        $("#price_grade1").focus();
                    } else {
                        if (Number(grade1price) > 0) {
                            isValid11 = true;
                            $("#error-pricegrade1").hide();
                        } else {
                            isValid11 = false;
                            $("#error-pricegrade1").text(error_zero_value);
                            $("#error-pricegrade1").show();
                            $("#price_grade1").focus();
                        }
                    }

                    if (grade2price.length == 0) {
                        isValid12 = false;
                        $("#error-pricegrade2").text(error_value);
                        $("#error-pricegrade2").show();
                        $("#price_grade2").focus();
                    } else {
                        if (Number(grade2price) > 0) {
                            isValid12 = true;
                            $("#error-pricegrade2").hide();
                        } else {
                            isValid12 = false;
                            $("#error-pricegrade2").text(error_zero_value);
                            $("#error-pricegrade2").show();
                            $("#price_grade2").focus();
                        }
                    }

                    if (grade3price.length == 0) {
                        isValid13 = false;
                        $("#error-pricegrade3").text(error_value);
                        $("#error-pricegrade3").show();
                        $("#price_grade3").focus();
                    } else {
                        if (Number(grade3price) > 0) {
                            isValid13 = true;
                            $("#error-pricegrade3").hide();
                        } else {
                            isValid13 = false;
                            $("#error-pricegrade3").text(error_zero_value);
                            $("#error-pricegrade3").show();
                            $("#price_grade3").focus();
                        }
                    }

                    if (isValid11 && isValid12 && isValid13) {
                        var priceData = {};
                        priceData.minRange = grade1price;
                        priceData.maxRange = grade2price;
                        priceData.price = grade3price;
                        price_array.push(priceData);
                    }

                } else if (producttype == 2 || producttype == 4) {
                    if (selectedPriceTypeOption == 1) {
                        var lastMaxRange = 0;
                        price_array = [];
                        $("[id*=divManualEntry] .DataRow").each(function() {
                            var minRange = $(this).closest('div').find('#min_range').val().trim();
                            var maxRange = $(this).closest('div').find('#max_range').val().trim();
                            var priceRange = $(this).closest('div').find('#price_range').val().trim();
                            if (minRange != "" && Number(minRange) > 0) {
                                if (maxRange != "" && Number(maxRange) > 0) {
                                    if (priceRange != "" && Number(priceRange) > 0) {

                                        if (Number(minRange) >= Number(maxRange)) {

                                            toastr.clear();
                                            toastr.warning(enter_min_range_check);

                                            $(this).closest('div').find('#min_range').focus();
                                            isValid11 = false;
                                        } else if (Number(lastMaxRange) >= Number(minRange)) {
                                            toastr.clear();
                                            toastr.warning(enter_last_range_check);

                                            $(this).closest('div').find('#min_range').focus();
                                            isValid = false;
                                        } else {
                                            var priceData = {};
                                            priceData.minRange = minRange;
                                            priceData.maxRange = maxRange;
                                            priceData.price = priceRange;
                                            price_array.push(priceData);
                                            isValid11 = true;
                                            lastMaxRange = maxRange;
                                        }
                                    } else {
                                        toastr.clear();
                                        toastr.warning(enter_price_range);

                                        $(this).closest('div').find('#price_range').focus();
                                        isValid11 = false;
                                    }
                                } else {

                                    toastr.clear();
                                    toastr.warning(enter_max_range);

                                    $(this).closest('div').find('#max_range').focus();
                                    isValid11 = false;
                                }
                            } else {

                                toastr.clear();
                                toastr.warning(enter_min_range);

                                $(this).closest('div').find('#min_range').focus();
                                isValid11 = false;
                            }
                        });
                    } else if (selectedPriceTypeOption == 2) {
                        // DO NOTHING
                    }

                    if (isValid11) {
                        if (price_array.length == 0) {
                            toastr.clear();
                            toastr.warning(upload_price_details);
                            isValid11 = false;
                        } else {
                            isValid11 = true;
                        }
                    }
                }
            }

            if (isValid11 && isValid12 && isValid13) {
                if (measuremetsystem == 0) {
                    $("#error-measuremetsystem").show();
                    isValid5 = false;
                } else {
                    $("#error-measuremetsystem").hide();
                    isValid5 = true;
                }

                if (circumferenceallowance.length == 0) {
                    $("#error-circumferenceallowance").show();
                    isValid14 = false;
                } else {
                    $("#error-circumferenceallowance").hide();
                    isValid14 = true;
                }

                if (lengthallowance.length == 0) {
                    $("#error-lengthallowance").show();
                    isValid15 = false;
                } else {
                    $("#error-lengthallowance").hide();
                    isValid15 = true;
                }

                if (contractcurrency == 0) {
                    $("#error-currency").show();
                    isValid6 = false;
                } else {
                    $("#error-currency").hide();
                    isValid6 = true;
                }

                if (paymentmethod == 0) {
                    $("#error-paymentmethod").show();
                    isValid7 = false;
                } else {
                    $("#error-paymentmethod").hide();
                    isValid7 = true;
                }

                if (startdate.length == 0) {
                    $("#error-startdate").show();
                    isValid8 = false;
                } else {
                    $("#error-startdate").hide();
                    isValid8 = true;
                }

                if (enddate.length == 0) {
                    $("#error-enddate").show();
                    isValid9 = false;
                } else {
                    $("#error-enddate").hide();
                    isValid9 = true;
                }

                if (totalvolume.length == 0) {
                    $("#error-totalvolume").text(error_value);
                    $("#error-totalvolume").show();
                    $("#total_volume").focus();
                    isValid10 = false;
                } else {
                    if (Number(totalvolume) > 0) {
                        isValid10 = true;
                        $("#error-totalvolume").hide();
                    } else {
                        isValid10 = false;
                        $("#error-totalvolume").text(error_zero_value);
                        $("#error-totalvolume").show();
                        $("#total_volume").focus();
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8 &&
                    isValid9 && isValid10 && isValid11 && isValid12 && isValid13 && isValid14 && isValid15) {

                    var fd = new FormData(this);
                    if (pagetype == "edit") {
                        fd.append("contractid", contractid);
                        fd.append("contractcode", contractcode);
                    } else {
                        fd.append("contractid", "");
                        fd.append("contractcode", "");
                    }

                    fd.append("is_ajax", 2);
                    fd.append("form", action);
                    fd.append("add_type", "contract");
                    fd.append("page_type", pagetype);
                    fd.append("origin", origin);
                    fd.append("contracttype", contracttype);
                    fd.append("suppliername", suppliername);
                    fd.append("productname", productname);
                    fd.append("producttype", producttype);
                    fd.append("measuremetsystem", measuremetsystem);
                    fd.append("circumferenceallowance", circumferenceallowance);
                    fd.append("lengthallowance", lengthallowance);
                    fd.append("contractcurrency", contractcurrency);
                    fd.append("paymentmethod", paymentmethod);
                    fd.append("startdate", startdate);
                    fd.append("enddate", enddate);
                    fd.append("totalvolume", totalvolume);
                    fd.append("contractstatus", contractstatus);
                    fd.append("pricearray", JSON.stringify(price_array));

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
                                $('.add_contract').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            } else {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $('.add_contract').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#add-modal-data-bd").modal('hide');

                                $('#xin_table_contracts').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                }
            }
        });
    });

    function fetchSupplierPurchaseManager(originid, contracttype) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_supplier_purchasemanager?originid=" + originid + "&contracttype=" + contracttype,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#supplier_pm_name").empty();
                    $("#supplier_pm_name").append(JSON.result);
                }
            }
        });
    }

    function fetchProducts(originid, contracttype, supplierid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_by_supplier_origin?originid=" + originid + "&contracttype=" + contracttype + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#product_name").empty();
                    $("#product_name").append(JSON.result);
                }
            }
        });
    }

    function fetchProductTypes(originid, contracttype, supplierid, productid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_type_by_supplier?originid=" + originid + "&contracttype=" + contracttype + "&supplierid=" + supplierid + "&productid=" + productid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#product_type").empty();
                    $("#product_type").append(JSON.result);
                }
            }
        });
    }

    function fetchMeasurementSystem(producttypeid, originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_measurement_system?originid=" + originid + "&producttypeid=" + producttypeid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#measuremet_system").empty();
                    $("#measuremet_system").append(JSON.result);
                }
            }
        });
    }

    function fetchCurrencies(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_currencies_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#currency").empty();
                    $("#currency").append(JSON.result);
                }
            }
        });
    }

    function fetchPaymentMethods(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_payment_methods?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#payment_method").empty();
                    $("#payment_method").append(JSON.result);
                }
            }
        });
    }

    function showPrices(producttypeid, product) {
        if (producttypeid == 2 || producttypeid == 4) {

            if($("#origin_contracts").val() == 3) {
                $("#txtcircallowance").text(dia_allowance);
                $("#circumference_allowance").attr("placeholder", dia_allowance);
            } else {
                $("#txtcircallowance").text(circumference_allowance);
                $("#circumference_allowance").attr("placeholder", circumference_allowance);
            }

            $("#divAllowance").show();
            $("#divRoundLogsPrice").show();
            $("#divSquareBlockPrice").hide();
        } else if (product == 4 && producttypeid == 1 || producttypeid == 3) {
            $("#divAllowance").show();

            if($("#origin_contracts").val() == 3) {
                $("#txtcircallowance").text(dia_allowance);
                $("#circumference_allowance").attr("placeholder", dia_allowance);
            } else {
                $("#txtcircallowance").text(circumference_allowance);
                $("#circumference_allowance").attr("placeholder", circumference_allowance);
            }

            $("#divRoundLogsPrice").show();
            $("#divSquareBlockPrice").hide();
        } else if (producttypeid == 1 || producttypeid == 3) {
            $("#divSquareBlockPrice").show();
            $("#divRoundLogsPrice").hide();
            $("#divAllowance").hide();
        } else {
            $("#divSquareBlockPrice").hide();
            $("#divRoundLogsPrice").hide();
            $("#divAllowance").hide();
        }


    }

    var loadFile = function(event) {
        $('#loading').show();
        price_array = [];
        var fd = new FormData();
        var files = $('#filePriceExcel')[0].files[0];
        if (files != null && files != "") {
            fd.append('filePriceExcel', files);
        }
        
        fd.append('originId', $("#origin_contracts").val());

        $.ajax({
            url: base_url + "/load_price_template",
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(JSON) {
                $('#loading').hide();
                $("#filePriceExcel").val("");
                deletefilesfromfolder();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    toastr.clear();
                    price_array = JSON.result;
                    $("#divPriceFields").empty();
                    if (price_array.length > 0) {
                        var priceuploaddata = "";
                        for (var counter in price_array) {
                            priceuploaddata = "";
                            priceuploaddata = priceuploaddata + "<div class='row DataRow'><div class='col-md-3'>";
                            priceuploaddata = priceuploaddata + "<label class='col-form-label' id='minRangeUpload' name='minRangeUpload'>" + price_array[counter].minRange + "</label>";
                            priceuploaddata = priceuploaddata + "</div>";
                            priceuploaddata = priceuploaddata + "<div class='col-md-3'>";
                            priceuploaddata = priceuploaddata + "<label class='col-form-label' id='maxRangeUpload' name='maxRangeUpload'>" + price_array[counter].maxRange + "</label>";
                            priceuploaddata = priceuploaddata + "</div>";
                            priceuploaddata = priceuploaddata + "<div class='col-md-3'>";
                            priceuploaddata = priceuploaddata + "<label class='col-form-label' id='priceRangeUpload' name='priceRangeUpload'>" + price_array[counter].price + "</label>";
                            priceuploaddata = priceuploaddata + "</div></div>";
                            $('#divPriceFields').append(priceuploaddata);
                            $("#divRoundLogPrice").show();
                        }
                    }
                } else if (JSON.error != '') {
                    toastr.clear();
                    toastr.error(JSON.error);
                    price_array = [];
                    $("#divRoundLogPrice").hide();
                } else if (JSON.warning != '') {
                    toastr.clear();
                    toastr.warning(JSON.warning);
                    price_array = [];
                    $("#divRoundLogPrice").hide();
                }
            }
        });
    };

    function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
            if (txt.value.indexOf('.') === -1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (charCode > 31 &&
                (charCode < 48 || charCode > 57))
                return false;
        }
        return true;
    }
</script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".start_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-6m',
            maxDate: '5y',
            onSelect: function(date) {
                $('#ui-datepicker-div table td a').attr('href', 'javascript:;');
                var selectedDate = $(".start_date").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $(".end_date").datepicker("option", "minDate", endDate);
                $("#error-startdate").hide();
            }
        });

        $(".end_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1m',
            maxDate: '5y',
            onSelect: function(date) {
                $("#error-enddate").hide();
            }
        });

        $('.ui-datepicker').addClass('notranslate');
    });
</script>