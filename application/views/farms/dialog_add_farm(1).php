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
<?php $attributes = array('name' => 'add_farm', 'id' => 'add_farm', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('farms/add', $attributes, $hidden); ?>
<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnpurchaseunit" name="hdnpurchaseunit" value="0">
    <input type="hidden" id="hdncurrency" name="hdncurrency" value="0">
    <div class="row mb-3">
        <div class="col-auto ms-auto">
            <a href="<?php echo base_url() . 'assets/templates/Template_Farm_SquareBlock.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Square Blocks'); ?></span></a>
            <a href="<?php echo base_url() . 'assets/templates/Template_Farm_RoundLogs.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Round Logs'); ?></span></a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="origin_farms"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_farms" id="origin_farms" data-plugin="select_erp">
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
            <label for="supplier_name"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->id; ?>" <?php if ($get_contract_details[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
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

        <div class="col-md-6">
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
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="purchase_contract"><?php echo $this->lang->line('purchase_contract'); ?></label>
            <select class="form-control" name="purchase_contract" id="purchase_contract" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($measurement_systems as $measurement_system) { ?>
                        <option onchange="" value="<?php echo $measurement_system->id; ?>" <?php if ($get_contract_details[0]->unit_of_purchase == $measurement_system->id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($measurement_system->purchase_unit); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-purchasecontract" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
        </div>

        <div class="row col-md-6" id="divShowDetails">
            <label class="label-showdetails" for="lblpurchaseunit" id="lblpurchaseunit"><?php echo $this->lang->line('measuremet_system') . ': ---'; ?></label>
            <label class="label-showdetails" for="lblcurrency" id="lblcurrency"><?php echo $this->lang->line('currency') . ': ---'; ?></label>
            <label class="label-showdetails" for="lblremainingvolume" id="lblremainingvolume"><?php echo $this->lang->line('remaining_volume') . ': ---'; ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="inventory_order_add"><?php echo $this->lang->line('inventory_order'); ?></label>
            <input type="text" id="inventory_order_add" name="inventory_order_add" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('inventory_order'); ?>">
            <label id="error-inventoryorder_add" class="error-text"><?php echo $this->lang->line('error_inventory_order'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="truck_plate_number_add"><?php echo $this->lang->line('truck_plate_number'); ?></label>
            <input type="text" id="truck_plate_number_add" name="truck_plate_number_add" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('truck_plate_number'); ?>">
            <label id="error-truckplatenumber_add" class="error-text"><?php echo $this->lang->line('error_truck_plate_number'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="service_cost"><?php echo $this->lang->line('service_cost'); ?></label>
            <input type="number" id="service_cost" step="any" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="service_cost" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" placeholder="<?php echo $this->lang->line('service_cost'); ?>">
            <label id="error-servicecost" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="service_payto"><?php echo $this->lang->line('service_payto'); ?></label>
            <select class="form-control" name="service_payto" id="service_payto" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-servicepayto" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="logistic_cost"><?php echo $this->lang->line('logistic_cost'); ?></label>
            <input type="number" id="logistic_cost" step="any" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="logistic_cost" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" placeholder="<?php echo $this->lang->line('logistic_cost'); ?>">
            <label id="error-logisticcost" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="logistic_payto"><?php echo $this->lang->line('logistic_payto'); ?></label>
            <select class="form-control" name="logistic_payto" id="logistic_payto" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-logisticpayto" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="adjustment"><?php echo $this->lang->line('adjustment'); ?></label>
            <input type="number" id="adjustment" step="any" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="adjustment" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" placeholder="<?php echo $this->lang->line('adjustment'); ?>">
            <label id="error-adjustment" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
        </div>
        <!-- <div class="col-md-3 form-check" style="display: flex;">
            <input class="form-check-input" id="adjust_rf" name="adjust_rf" type="checkbox" value="">
            <label for="adjust_rf"><?php echo $this->lang->line('adjust_rf'); ?></label>
        </div> -->
        <div class="col-md-6" id="divConversionRate">
            <label for="conversion_rate"><?php echo $this->lang->line('conversion_rate'); ?></label>
            <input type="number" id="conversion_rate" step="any" min="0" maxlength="10" onkeypress="return isNumberKey(this, event)" name="conversion_rate" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <label id="error-conversionrate" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
        </div>
    </div>

    <div class="row mb-3" id="divAdjustmentTaxes">
        <div class="col-md-6">
            <label for="adjustment_tax"><?php echo $this->lang->line('adjustment_taxes'); ?></label>
            <select class="form-control" name="adjustment_tax[]" id="adjustment_tax" data-plugin="select_erp" multiple>
            </select>
        </div>
    </div>

    <div class="row mb-3" id="divReceptionDetail">
        <div class="col-md-6 mb-2">
            <label for="wh_name"><?php echo $this->lang->line('wh_name'); ?></label>
            <select class="form-control" name="wh_name" id="wh_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-warehouse" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="reception_date"><?php echo $this->lang->line('reception_date'); ?></label>
            <input type="text" id="reception_date" name="reception_date" class="form-control" readonly placeholder="<?php echo $this->lang->line('received_date'); ?>" />
            <label id="error-receptiondate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="fileFarmExcel"><?php echo $this->lang->line('farm_upload'); ?></label>
            <input name="fileFarmExcel" type="file" accept=".xlsx" id="fileFarmExcel" onchange="loadFile(event)" class="form-control">
            <label id="error-farmupload" class="error-text"><?php echo $this->lang->line('error_farm_upload'); ?></label>
        </div>

        <div class="col-md-6" id="divUploadedDetails">
            <div class="next-line">
                <label class="label-showdetails" for="lblTotalPieces" id="lblTotalPieces"><?php echo $this->lang->line('total_pieces') . ': ---'; ?></label>
                <label class="label-showdetails" for="lblVolume" id="lblVolume"><?php echo $this->lang->line('total_volume') . ': ---'; ?></label>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success add_farm', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    var measuremet_system = "<?php echo $this->lang->line('measuremet_system'); ?>";
    var currency = "<?php echo $this->lang->line('currency'); ?>";
    var remaining_volume = "<?php echo $this->lang->line('remaining_volume'); ?>";
    var error_value = "<?php echo $this->lang->line('error_value'); ?>";
    var error_zero_value = "<?php echo $this->lang->line('error_zero_value'); ?>";
    var error_inventory_order = "<?php echo $this->lang->line('error_inventory_order'); ?>";
    var error_purchase_contract = "<?php echo $this->lang->line('error_purchase_contract'); ?>";
    var total_pieces = "<?php echo $this->lang->line('total_no_of_pieces'); ?>";
    var total_volume = "<?php echo $this->lang->line('total_volume'); ?>";
    var total_pie = "<?php echo $this->lang->line('total_pie'); ?>";
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var selecttext = "<?php echo $this->lang->line("select"); ?>";

    var selectedPriceTypeOption = 1;
    var farm_data_array = [];
    var pricefields = "";

    $("#error-origin").hide();
    $("#error-name").hide();
    $("#error-productname").hide();
    $("#error-woodtype").hide();
    $("#error-purchasecontract").hide();
    $("#error-inventoryorder_add").hide();
    $("#error-truckplatenumber_add").hide();
    $("#error-servicecost").hide();
    $("#error-servicepayto").hide();
    $("#error-logisticcost").hide();
    $("#error-logisticpayto").hide();
    $("#error-conversionrate").hide();
    $("#error-farmupload").hide();
    $("#error-receptiondate").hide();
    $("#error-adjustment").hide();
    $("#divReceptionDetail").hide();
    $("#divShowDetails").hide();
    $("#divConversionRate").hide();
    $("#divUploadedDetails").hide();
    $("#error-warehouse").hide();
    $("#divAdjustmentTaxes").hide();

    $(document).ready(function() {

        $("#origin_farms").change(function() {
            if ($("#origin_farms").val() == 0) {
                $("#supplier_name").attr("disabled", true);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#logistic_payto").attr("disabled", true);
                $("#service_payto").attr("disabled", true);
                fetchSuppliers(0);
                fetchProducts(0, 0);
                fetchProviders(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
                $("#divAdjustmentTaxes").hide();
                fetchSupplierTaxes(0);
            } else {
                fetchSuppliers($("#origin_farms").val());
                $("#supplier_name").attr("disabled", false);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#purchase_contract").attr("disabled", true);
                $("#logistic_payto").attr("disabled", true);
                $("#service_payto").attr("disabled", true);
                fetchProducts(0, 0);
                fetchProviders(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
                fetchSupplierTaxes($("#origin_farms").val());
                $("#error-origin").hide();
            }
        });

        $("#supplier_name").change(function() {

            if ($("#supplier_name").val() == 0) {
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#purchase_contract").attr("disabled", true);
                $("#logistic_payto").attr("disabled", true);
                $("#service_payto").attr("disabled", true);
                fetchProducts(0, 0);
                fetchProviders(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
            } else {
                fetchProducts($("#origin_farms").val(), $("#supplier_name").val());
                fetchProviders($("#origin_farms").val(), $("#supplier_name").val());
                $("#product_name").attr("disabled", false);
                $("#product_type").attr("disabled", true);
                $("#purchase_contract").attr("disabled", true);
                $("#logistic_payto").attr("disabled", false);
                $("#service_payto").attr("disabled", false);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
                $("#error-name").hide();
            }
        });

        $("#product_name").change(function() {

            if ($("#product_name").val() == 0) {
                $("#product_type").attr("disabled", true);
                $("#purchase_contract").attr("disabled", true);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
            } else {
                fetchProductTypes($("#origin_farms").val(), $("#supplier_name").val(), $("#product_name").val());
                $("#product_type").attr("disabled", false);
                $("#purchase_contract").attr("disabled", true);
                $("#error-productname").hide();
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
            }
        });

        $("#product_type").change(function() {

            if ($("#product_type").val() == 0) {
                $("#purchase_contract").attr("disabled", true);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
            } else {
                fetchContracts($("#origin_farms").val(), $("#supplier_name").val(), $("#product_name").val(), $("#product_type").val());
                $("#purchase_contract").attr("disabled", false);
                $("#error-woodtype").hide();
                fetchContractDetails(0, 0, 0, 0, 0);
                if ($("#product_type").val() == 1 || $("#product_type").val() == 3) {
                    fetchWarehouses($("#origin_farms").val());
                }
            }
        });

        $("#purchase_contract").change(function() {

            if ($("#purchase_contract").val() == 0) {
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
            } else {
                fetchContractDetails($("#origin_farms").val(), $("#supplier_name").val(), $("#product_name").val(), $("#product_type").val(), $("#purchase_contract").val());
                $("#purchase_contract").attr("disabled", false);
                $("#error-purchasecontract").hide();
            }
        });

        $("#add_farm").submit(function(e) {

            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var origin = $("#origin_farms").val();
            var supplierid = $("#supplier_name").val();
            var productid = $("#product_name").val();
            var producttypeid = $("#product_type").val();
            var purchasecontractid = $("#purchase_contract").val();
            var inventoryorder = $("#inventory_order_add").val().trim();
            var truckplatenumber = $("#truck_plate_number_add").val().trim();
            var servicecost = $("#service_cost").val().trim();
            var servicepayto = $("#service_payto").val();
            var logisticcost = $("#logistic_cost").val().trim();
            var logisticpayto = $("#logistic_payto").val();
            var farmadjustment = $("#adjustment").val().trim();
            var conversionrate = $("#conversion_rate").val().trim();
            var adjustrf = $("#adjust_rf").is(':checked');
            var warehouseid = $("#wh_name").val();
            var receptiondate = $("#reception_date").val().trim();

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
                isValid15 = true,
                isValid16 = true,
                isValid17 = true,
                isValid18 = true,
                isValid19 = true;

            if (origin == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (supplierid == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
                isValid2 = true;
            }

            if (productid == 0) {
                $("#error-productname").show();
                isValid3 = false;
            } else {
                $("#error-productname").hide();
                isValid3 = true;
            }

            if (producttypeid == 0) {
                $("#error-woodtype").show();
                isValid4 = false;
            } else {
                $("#error-woodtype").hide();
                isValid4 = true;
            }

            if (purchasecontractid == 0) {
                $("#error-purchasecontract").show();
                isValid5 = false;
            } else {
                $("#error-purchasecontract").hide();
                isValid5 = true;
            }

            if (inventoryorder.length == 0) {
                $("#error-inventoryorder_add").show();
                isValid6 = false;
            } else {
                $("#error-inventoryorder_add").hide();
                isValid6 = true;
            }

            if (truckplatenumber.length == 0) {
                $("#error-truckplatenumber_add").show();
                isValid7 = false;
            } else {
                $("#error-truckplatenumber_add").hide();
                isValid7 = true;
            }

            if (servicecost.length > 0 && servicecost > 0) {
                $("#error-servicecost").hide();
                if (servicepayto > 0) {
                    $("#error-servicepayto").hide();
                    isValid8 = true;
                } else {
                    $("#error-servicepayto").show();
                    isValid8 = false;
                }
            } else {
                if (servicecost.length == 0) {
                    $("#error-servicecost").hide();
                    isValid8 = true;
                } else {
                    $("#error-servicecost").show();
                    $("#error-servicecost").text(error_zero_value);
                    isValid8 = false;
                }
            }

            if (servicepayto > 0) {
                $("#error-servicepayto").hide();
                if (servicecost.length == 0) {
                    $("#error-servicecost").show();
                    $("#error-servicecost").text(error_value);
                    isValid8 = false;
                } else {
                    if (servicecost == 0) {
                        $("#error-servicecost").show();
                        $("#error-servicecost").text(error_zero_value);
                        isValid8 = false;
                    } else {
                        $("#error-servicecost").hide();
                        isValid8 = true;
                    }
                }
            }

            if (logisticcost.length > 0 && logisticcost > 0) {
                $("#error-logisticcost").hide();
                if (logisticpayto > 0) {
                    $("#error-logisticpayto").hide();
                    isValid9 = true;
                } else {
                    $("#error-logisticpayto").show();
                    isValid9 = false;
                }
            } else {
                if (logisticcost.length == 0) {
                    $("#error-logisticcost").hide();
                    isValid9 = true;
                } else {
                    $("#error-logisticcost").show();
                    $("#error-logisticcost").text(error_zero_value);
                    isValid9 = false;
                }
            }

            if (logisticpayto > 0) {
                $("#error-logisticpayto").hide();
                if (logisticcost.length == 0) {
                    $("#error-logisticcost").show();
                    $("#error-logisticcost").text(error_value);
                    isValid9 = false;
                } else {
                    if (logisticcost == 0) {
                        $("#error-logisticcost").show();
                        $("#error-logisticcost").text(error_zero_value);
                        isValid9 = false;
                    } else {
                        $("#error-logisticcost").hide();
                        isValid9 = true;
                    }
                }
            }

            if (farmadjustment.length > 0 && farmadjustment == 0) {
                $("#error-adjustment").show();
                isValid10 = false;
            } else {
                $("#error-adjustment").hide();
                isValid10 = true;
            }

            if ($("#hdncurrency").val() == 1) {
                if (conversionrate.length == 0) {
                    $("#error-conversionrate").show();
                    $("#error-conversionrate").text(error_value);
                    isValid16 = false;
                } else if (conversionrate.length > 0 && conversionrate == 0) {
                    $("#error-conversionrate").show();
                    $("#error-conversionrate").text(error_zero_value);
                    isValid16 = false;
                } else {
                    $("#error-conversionrate").hide();
                    isValid16 = true;
                }
            } else {
                $("#error-conversionrate").hide();
                isValid16 = true;
            }

            if (producttypeid == 1 || producttypeid == 3) {
                if (warehouseid == 0) {
                    $("#error-warehouse").show();
                    isValid18 = false;
                } else {
                    $("#error-warehouse").hide();
                    isValid18 = true;
                }

                if (receptiondate.length == 0) {
                    $("#error-receptiondate").show();
                    isValid19 = false;
                } else {
                    $("#error-receptiondate").hide();
                    isValid19 = true;
                }
            }

            if (farm_data_array.length == 0) {
                $("#error-farmupload").show();
                isValid17 = false;
            } else {
                $("#error-farmupload").hide();
                isValid17 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8 && isValid9 && isValid10 &&
                isValid11 && isValid12 && isValid13 && isValid14 && isValid15 && isValid16 && isValid17 && isValid18 && isValid19) {

                var fd = new FormData(this);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("action_type", pagetype);
                fd.append("add_type", "farm");
                fd.append("originid", origin);
                fd.append("supplierid", supplierid);
                fd.append("productid", productid);
                fd.append("producttypeid", producttypeid);
                fd.append("purchasecontractid", purchasecontractid);
                fd.append("inventoryorder", inventoryorder);
                fd.append("truckplatenumber", truckplatenumber);
                fd.append("servicecost", servicecost);
                fd.append("servicepayto", servicepayto);
                fd.append("logisticcost", logisticcost);
                fd.append("logisticpayto", logisticpayto);
                fd.append("farmadjustment", farmadjustment);
                fd.append("conversionrate", conversionrate);
                fd.append("adjustrf", $("#adjustment_tax").val());
                fd.append("warehouseid", warehouseid);
                fd.append("receptiondate", receptiondate);
                fd.append("purchaseunit", $("#hdnpurchaseunit").val());
                fd.append("farmdata", JSON.stringify(farm_data_array));

                $(".add_farm").prop('disabled', true);
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
                            $('.add_farm').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.add_farm').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-bd").modal('hide');

                            $('#xin_table_farms').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });

    function fetchSuppliers(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_suppliers?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#supplier_name").empty();
                    $("#supplier_name").append(JSON.result);
                }
            }
        });
    }

    function fetchProducts(originid, supplierid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_by_supplier_origin?originid=" + originid + "&supplierid=" + supplierid,
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

    function fetchProviders(originid, supplierid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_providers?originid=" + originid + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#service_payto").empty();
                    $("#service_payto").append(JSON.result);

                    $("#logistic_payto").empty();
                    $("#logistic_payto").append(JSON.result);
                }
            }
        });
    }

    function fetchProductTypes(originid, supplierid, productid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_type_by_supplier?originid=" + originid + "&supplierid=" + supplierid + "&productid=" + productid,
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

    function fetchContracts(originid, supplierid, productid, producttypeid) {
        $("#divReceptionDetail").hide();
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_contracts_by_supplier?originid=" + originid + "&supplierid=" + supplierid + "&productid=" + productid + "&producttypeid=" + producttypeid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#purchase_contract").empty();
                    $("#purchase_contract").append(JSON.result);

                    if (producttypeid == 1 || producttypeid == 3) {
                        $("#divReceptionDetail").show();
                    } else {
                        $("#divReceptionDetail").hide();
                    }
                }
            }
        });
    }

    function fetchContractDetails(originid, supplierid, productid, producttypeid, contractid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_contract_details?originid=" + originid + "&supplierid=" + supplierid +
                "&productid=" + productid + "&producttypeid=" + producttypeid + "&contractid=" + contractid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                $("#divShowDetails").hide();
                $("#divConversionRate").hide();
                $("#divUploadedDetails").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.error != '') {
                    toastr.clear();
                    toastr.error(JSON.error);
                } else if (JSON.result != '') {
                    toastr.clear();
                    $("#divShowDetails").show();
                    $("#lblpurchaseunit").text(measuremet_system + ": " + JSON.result["purchaseunit"]);
                    $("#lblcurrency").text(currency + ": " + JSON.result["currencycode"]);
                    $("#lblremainingvolume").text(remaining_volume + ": " + JSON.result["remainingvolume"]);
                    $("#hdnpurchaseunit").val(JSON.result["unit_of_purchase"]);
                    $("#hdncurrency").val(JSON.result["currency"]);
                    if (JSON.result["currency"] == 1) {
                        $("#divConversionRate").show();
                    } else {
                        $("#divConversionRate").hide();
                    }
                }
            }
        });
    }

    function fetchWarehouses(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_warehouse_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#wh_name").empty();
                    $("#wh_name").append(JSON.result);
                }
            }
        });
    }

    var loadFile = function(event) {
        $("#error-farmupload").hide();
        var inventoryorder = $("#inventory_order_add").val().trim();
        var purchaseContractId = $("#purchase_contract").val();
        event.preventDefault();

        if (purchaseContractId == 0) {
            toastr.clear();
            toastr.error(error_purchase_contract);
            $("#error-purchasecontract").show();
            $("#fileFarmExcel").val("");
        } else {
            $("#error-purchasecontract").hide();
        }

        if (inventoryorder.length == 0) {
            toastr.clear();
            toastr.error(error_inventory_order);
            $("#error-inventoryorder_add").show();
            $("#fileFarmExcel").val("");
        } else {
            $("#error-inventoryorder_add").hide();
        }

        toastr.clear();

        $('#loading').show();
        farm_data_array = [];
        var fd = new FormData();
        var files = $('#fileFarmExcel')[0].files[0];
        if (files != null && files != "") {
            fd.append('fileFarmExcel', files);
            fd.append('inventoryOrder', inventoryorder);
            fd.append('purchaseUnit', $("#hdnpurchaseunit").val());
            fd.append('originId', $("#origin_farms").val());
            fd.append('productTypeId', $("#product_type").val());
            fd.append('purchaseContractId', purchaseContractId);

            $.ajax({
                url: base_url + "/load_farm_template",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(JSON) {
                    $('#loading').hide();
                    $("#fileFarmExcel").val("");
                    deletefilesfromfolder();
                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {
                        toastr.clear();
                        if (JSON.result["purchaseUnit"] == "1") {
                            $("#lblVolume").text(total_pie + ": " + JSON.result["totalVolume"]);
                        } else {
                            $("#lblVolume").text(total_volume + ": " + JSON.result["totalVolume"]);
                        }

                        $("#lblTotalPieces").text(total_pieces + ": " + JSON.result["totalPieces"]);
                        farm_data_array = JSON.result["farmData"];
                        $("#divUploadedDetails").show();
                    } else if (JSON.error != '') {
                        toastr.clear();
                        toastr.error(JSON.error);
                        $("#divUploadedDetails").hide();
                    } else if (JSON.warning != '') {
                        toastr.clear();
                        toastr.warning(JSON.warning);
                        $("#divUploadedDetails").hide();
                    }
                }
            });
        } else {
            toastr.clear();
            toastr.error(common_error);
            $("#fileFarmExcel").val("");
        }
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

    function fetchSupplierTaxes(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_supplier_taxes_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#divAdjustmentTaxes").show();
                    $('#adjustment_tax').append(JSON.result);
                    $('#adjustment_tax').multiselect({
                        placeholder: selecttext,
                        search: false,
                        selectAll: false,
                    });
                }
            }
        });
    }
</script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#reception_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1y',
            maxDate: '0d',
            onSelect: function(date) {
                $("#error-receptiondate").hide();
            }
        });
        
        $('.ui-datepicker').addClass('notranslate');
    });
</script>