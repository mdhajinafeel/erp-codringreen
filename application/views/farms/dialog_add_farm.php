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
    <input type="hidden" id="hdnmandatoryreceptiondetails" name="hdnmandatoryreceptiondetails" value="0">
    <div class="row mb-3">
        <div class="col-auto ms-auto">
            <a href="<?php echo base_url() . 'assets/templates/Template_Farm_SquareBlock.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Square Blocks'); ?></span></a>
            <a href="<?php echo base_url() . 'assets/templates/Template_Farm_RoundLogs.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Round Logs'); ?></span></a>

            <?php foreach ($applicable_origins as $origin) {
                if ($origin->id == 3) { ?>
                    <a href="<?php echo base_url() . 'assets/templates/Template_Farm_Pine_RoundLogs.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Round Logs') .  ' - ' . $this->lang->line('farm_pine'); ?></span></a>
            <?php }
            } ?>
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
        <div class="col-md-6">
            <label for="circumference_allowance" id="txtcircallownace"><?php echo $this->lang->line('circumference_allowance'); ?></label>
            <input type="number" id="circumference_allowance" step="any" maxlength="10" name="circumference_allowance" class="form-control" placeholder="<?php echo $this->lang->line('circumference_allowance'); ?>">
            <label id="error-circumference_allowance" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="length_allowance"><?php echo $this->lang->line('length_allowance'); ?></label>
            <input type="number" id="length_allowance" step="any" maxlength="10" name="length_allowance" class="form-control" placeholder="<?php echo $this->lang->line('length_allowance'); ?>">
            <label id="error-length_allowance" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
        </div>
    </div>

    <div id="divSingleUpload">

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
                <input type="number" id="service_cost" step="any" maxlength="10" name="service_cost" class="form-control" placeholder="<?php echo $this->lang->line('service_cost'); ?>">
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
                <input type="number" id="logistic_cost" step="any" maxlength="10" name="logistic_cost" class="form-control" placeholder="<?php echo $this->lang->line('logistic_cost'); ?>">
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
                <input type="number" id="adjustment" step="any" maxlength="10" name="adjustment" class="form-control" placeholder="<?php echo $this->lang->line('adjustment'); ?>">
                <label id="error-adjustment" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
            </div>

            <div class="col-md-6" id="divConversionRate">
                <label for="conversion_rate"><?php echo $this->lang->line('conversion_rate'); ?></label>
                <input type="number" id="conversion_rate" step="any" maxlength="10" name="conversion_rate" class="form-control">
                <label id="error-conversionrate" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>
        </div>

        <div class="row mb-3" id="divAdjustmentTaxes">
            <div class="col-md-6">
                <label for="adjustment_tax"><?php echo $this->lang->line('adjustment_taxes'); ?></label>
                <select class="form-control" name="adjustment_tax[]" id="adjustment_tax" data-plugin="select_erp" multiple>
                </select>
            </div>

            <div class="col-md-6">
                <label for="process"><?php echo $this->lang->line('process'); ?></label>
                <select class="form-control" name="process" id="process" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <option value="1"><?php echo $this->lang->line("sawmill"); ?></option>
                    <option value="2"><?php echo $this->lang->line("local_sales"); ?></option>
                </select>
            </div>
        </div>

        <div class="row mb-3" id="divReceptionDetail">
            <h6 style="text-decoration: underline; font-weight: 600; color: #000;"><?php echo $this->lang->line('reception_details'); ?></h6>

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

        <div class="row mb-3" id="divReceptionDetailRoundLogs">
            <h6 style="text-decoration: underline; font-weight: 600; color: #000;"><?php echo $this->lang->line('reception_details'); ?></h6>

            <div class="col-md-6 mb-2">
                <label for="wh_name_roundlogs"><?php echo $this->lang->line('wh_name'); ?></label>
                <select class="form-control" name="wh_name_roundlogs" id="wh_name_roundlogs" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-warehouse_roundlogs" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
            </div>

            <div class="col-md-6 mb-2">
                <label for="measurement_system_roundlogs"><?php echo $this->lang->line('measuremet_system'); ?></label>
                <select class="form-control" name="measurement_system_roundlogs" id="measurement_system_roundlogs" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-measurementsystem_roundlogs" class="error-text"><?php echo $this->lang->line('error_measuremet_system'); ?></label>
            </div>
        </div>
        
        <div class="row mb-3" id="divWarehouseSingaporeDetails">
            <div class="col-md-4 mb-2">
                <label for="wh_name_singapore"><?php echo $this->lang->line('wh_name'); ?></label>
                <select class="form-control" name="wh_name_singapore" id="wh_name_singapore" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-warehouse_singapore" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
            </div>

            <div class="col-md-4 mb-2">
                <label for="measurement_system_singapore"><?php echo $this->lang->line('measuremet_system'); ?></label>
                <select class="form-control" name="measurement_system_singapore" id="measurement_system_singapore" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-measurementsystem_singapore" class="error-text"><?php echo $this->lang->line('error_measuremet_system'); ?></label>
            </div>

            <div class="col-md-4 mb-2">
                <label for="reception_date_signapore"><?php echo $this->lang->line('received_date'); ?></label>
                <input type="text" id="reception_date_signapore" name="reception_date_signapore" class="form-control" readonly placeholder="<?php echo $this->lang->line('received_date'); ?>" />
                <label id="error-received_date_singapore" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
            </div>

            <div class="col-md-4 mb-2">
                <label for="seal_number_signapore"><?php echo $this->lang->line('seal_number'); ?></label>
                <input type="text" id="seal_number_signapore" name="seal_number_signapore" class="form-control" placeholder="<?php echo $this->lang->line('seal_number'); ?>" />
            </div>

            <div class="col-md-4 mb-2">
                <label for="shipping_name_singapore"><?php echo $this->lang->line('shipping_line'); ?></label>
                <select class="form-control" name="shipping_name_singapore" id="shipping_name_singapore" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-shipping_singapore" class="error-text"><?php echo $this->lang->line('error_shipping_line'); ?></label>
            </div>
            
            <div class="col-md-4 mb-2">
                <label for="rounding_factor_singapore"><?php echo $this->lang->line('rounding_factor'); ?></label>
                <input type="number" id="rounding_factor_singapore" name="rounding_factor_singapore" class="form-control" step="any" placeholder="<?php echo $this->lang->line('rounding_factor'); ?>" />
            </div>
            
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
                <label class="label-showdetails" for="lblMetriTons" id="lblMetriTons"><?php echo $this->lang->line('total_MT') . ': ---'; ?></label>
                <label class="label-showdetails" for="lblShortTons" id="lblShortTons"><?php echo $this->lang->line('total_ST') . ': ---'; ?></label>
                <label class="label-showdetails" for="lblNetLbs" id="lblNetLbs"><?php echo $this->lang->line('total_LBS') . ': ---'; ?></label>

            </div>
        </div>
    </div>

    <div class="row mb-3" id="divWarehousePineDetails">
        <div class="col-md-6 mb-2">
            <label for="wh_name_pine"><?php echo $this->lang->line('wh_name'); ?></label>
            <select class="form-control" name="wh_name_pine" id="wh_name_pine" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-warehouse_pine" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
        </div>

        <div class="col-md-6 mb-2">
            <label for="shipping_name_pine"><?php echo $this->lang->line('shipping_line'); ?></label>
            <select class="form-control" name="shipping_name_pine" id="shipping_name_pine" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-shipping_line" class="error-text"><?php echo $this->lang->line('error_shipping_line'); ?></label>
        </div>
    </div>

    <div class="row mb-3" id="divTableContainer">
        <div class="table-responsive scrollable-table">
            <table class="table table-striped table-bordered" id="xin_table_container" style="width: 100% !important;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line("CONTAINER #"); ?></th>
                        <th><?php echo $this->lang->line("seal"); ?></th>
                        <th><?php echo $this->lang->line("metric_ton"); ?></th>
                        <th><?php echo $this->lang->line("short_tons"); ?></th>
                        <th><?php echo $this->lang->line("net_lbs"); ?></th>
                        <th><?php echo $this->lang->line("diameter"); ?></th>
                        <th><?php echo $this->lang->line("length"); ?></th>
                        <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                        <th><?php echo $this->lang->line("total_jas_volume"); ?></th>
                        <th><?php echo $this->lang->line("purchase_price"); ?></th>
                        <th><?php echo $this->lang->line("total_purchase_price"); ?></th>
                        <th><?php echo $this->lang->line("sales_price"); ?></th>
                        <th><?php echo $this->lang->line("total_sales_price"); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="row mb-3" id="divMessage">
        <label class="error-text-1"><?php echo $this->lang->line("note_farm_upload"); ?></label>
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
    var total_MT = "<?php echo $this->lang->line('total_MT'); ?>";
    var total_ST = "<?php echo $this->lang->line('total_ST'); ?>";
    var total_LBS = "<?php echo $this->lang->line('total_LBS'); ?>";
    var total_jas_volume = "<?php echo $this->lang->line('total_jas_volume'); ?>";
    var total_pie = "<?php echo $this->lang->line('total_pie'); ?>";
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var selecttext = "<?php echo $this->lang->line("select"); ?>";
    var circumference_allowance = "<?php echo $this->lang->line("circumference_allowance"); ?>";
    var dia_allowance = "<?php echo $this->lang->line("dia_allowance"); ?>";

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
    $("#error-warehouse_roundlogs").hide();
    $("#error-circumference_allowance").hide();
    $("#error-length_allowance").hide();
    $("#error-measurementsystem_roundlogs").hide();
    $("#divReceptionDetailRoundLogs").hide();
    $("#divSingleUpload").show();
    $("#divTableContainer").hide();
    $("#lblShortTons").hide();
    $("#lblMetriTons").hide();
    $("#lblNetLbs").hide();
    $("#divMessage").hide();
    $("#divWarehousePineDetails").hide();
    $("#error-warehouse_pine").hide();
    $("#error-shipping_line").hide();
    
    $("#divWarehouseSingaporeDetails").hide();
    $("#error-warehouse_singapore").hide();
    $("#error-measurementsystem_singapore").hide();
    $("#error-received_date_singapore").hide();
    $("#error-shipping_singapore").hide();

    $(document).ready(function() {

        $("#origin_farms").change(function() {
            if ($("#origin_farms").val() == 0) {
                $("#supplier_name").attr("disabled", true);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#logistic_payto").attr("disabled", true);
                $("#service_payto").attr("disabled", true);

                $("#circumference_allowance").attr("placeholder", circumference_allowance);
                $("#txtcircallownace").text(circumference_allowance);

                fetchSuppliers(0);
                fetchProducts(0, 0);
                fetchProviders(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
                $("#divAdjustmentTaxes").hide();
                fetchSupplierTaxes(0);
                fetchCompanySettings(0);
                $("#divSingleUpload").show();
                $("#divTableContainer").hide();
                $("#divMessage").hide();
            } else {
                fetchSuppliers($("#origin_farms").val());
                $("#supplier_name").attr("disabled", false);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                $("#purchase_contract").attr("disabled", true);
                $("#logistic_payto").attr("disabled", true);
                $("#service_payto").attr("disabled", true);

                if ($("#origin_farms").val() == 3) {
                    $("#circumference_allowance").attr("placeholder", dia_allowance);
                    $("#txtcircallownace").text(dia_allowance);
                    $("#divSingleUpload").hide();
                    $("#divWarehouseSingaporeDetails").hide();
                    $("#divWarehousePineDetails").show();
                    fetchShippingLines($("#origin_farms").val());
                } else if ($("#origin_farms").val() == 4) {
                    $("#circumference_allowance").attr("placeholder", dia_allowance);
                    $("#txtcircallownace").text(dia_allowance);
                    $("#divSingleUpload").show();
                    $("#divWarehousePineDetails").hide();
                    $("#divReceptionDetailRoundLogs").hide();
                    $("#divWarehouseSingaporeDetails").show();
                    fetchShippingLines($("#origin_farms").val());
                } else {
                    $("#circumference_allowance").attr("placeholder", circumference_allowance);
                    $("#txtcircallownace").text(circumference_allowance);
                    $("#divSingleUpload").show();
                    $("#divWarehouseSingaporeDetails").hide();
                    $("#divWarehousePineDetails").hide();
                }

                fetchProducts(0, 0);
                fetchProviders(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchContracts(0, 0, 0, 0);
                fetchContractDetails(0, 0, 0, 0, 0);
                fetchWarehouses(0);
                fetchSupplierTaxes($("#origin_farms").val());
                fetchCompanySettings($("#origin_farms").val());
                $("#error-origin").hide();
                $("#divTableContainer").hide();
                $("#divMessage").hide();
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
                //if ($("#product_type").val() == 1 || $("#product_type").val() == 3) {
                fetchWarehouses($("#origin_farms").val());
                //}
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

            var mandatoryreceptiondetails = $("#hdnmandatoryreceptiondetails").val().trim();
            var warehouseid_roundlogs = $("#wh_name_roundlogs").val();
            var measurement_system_roundlogs = $("#measurement_system_roundlogs").val();
            var process = $("#process").val();
            

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
                isValid19 = true,
                isValid20 = true,
                isValid21 = true;
                isValid22 = true;
                isValid23 = true;

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

            if (origin == 3) {
                
                isValid6 = true;
                isValid7 = true;

                if ($("#wh_name_pine").val() == 0) {
                    $("#error-warehouse_pine").show();
                    isValid20 = false;
                } else {
                    $("#error-warehouse_pine").hide();
                    isValid20 = true;
                }

                if ($("#shipping_name_pine").val() == 0) {
                    $("#error-shipping_line").show();
                    isValid21 = false;
                } else {
                    $("#error-shipping_line").hide();
                    isValid21 = true;
                }

            } else {
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

                if (servicecost.length > 0) {
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

                if (logisticcost.length > 0) {
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

                if (producttypeid == 2 || producttypeid == 4) {
                    if (mandatoryreceptiondetails == 1) {
                        if (warehouseid_roundlogs == 0) {
                            $("#error-warehouse_roundlogs").show();
                            isValid20 = false;
                        } else {
                            $("#error-warehouse_roundlogs").hide();
                            isValid20 = true;
                        }

                        if (measurement_system_roundlogs == 0) {
                            $("#error-measurementsystem_roundlogs").show();
                            isValid21 = false;
                        } else {
                            $("#error-measurementsystem_roundlogs").hide();
                            isValid21 = true;
                        }
                    }
                }
            }

            if (farm_data_array.length == 0) {
                $("#error-farmupload").show();
                isValid17 = false;
            } else {
                $("#error-farmupload").hide();
                isValid17 = true;
            }
            
            if (origin == 4) {
                if ($("#wh_name_singapore").val() == 0) {
                    $("#error-warehouse_singapore").show();
                    isValid20 = false;
                } else {
                    $("#error-warehouse_singapore").hide();
                    isValid20 = true;
                }

                if ($("#measurement_system_singapore").val() == 0) {
                    $("#error-measurementsystem_singapore").show();
                    isValid21 = false;
                } else {
                    $("#error-measurementsystem_singapore").hide();
                    isValid21 = true;
                }

                if ($("#reception_date_signapore").val().trim().length == 0) {
                    $("#error-received_date_singapore").show();
                    isValid22 = false;
                } else {
                    $("#error-received_date_singapore").hide();
                    isValid22 = true;
                }

                if ($("#shipping_name_singapore").val() == 0) {
                    $("#error-shipping_singapore").show();
                    isValid23 = false;
                } else {
                    $("#error-shipping_singapore").hide();
                    isValid23 = true;
                }
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8 && isValid9 && isValid10 &&
                isValid11 && isValid12 && isValid13 && isValid14 && isValid15 && isValid16 && isValid17 && isValid18 && isValid19 &&
                isValid20 && isValid21 && isValid22 && isValid23) {

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

                if (origin == 3) {
                    fd.append("inventoryorder", "");
                    fd.append("truckplatenumber", "");
                    fd.append("servicecost", 0);
                    fd.append("servicepayto", 0);
                    fd.append("logisticcost", 0);
                    fd.append("logisticpayto", 0);
                    fd.append("farmadjustment", 0);
                    fd.append("conversionrate", 1);
                    fd.append("adjustrf", 0);
                    fd.append("warehouseid", $("#wh_name_pine").val());
                    fd.append("receptiondate", "");

                    fd.append("warehouseid_rounglogs", 0);
                    fd.append("measurement_system_roundlogs", 0);
                    fd.append("mandatoryreception", true);
                    fd.append("shippingline", $("#shipping_name_pine").val());
                    fd.append("processType", 0);
                } else {
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

                    fd.append("warehouseid_rounglogs", warehouseid_roundlogs);
                    fd.append("measurement_system_roundlogs", measurement_system_roundlogs);
                    fd.append("mandatoryreception", $("#hdnmandatoryreceptiondetails").val());
                    fd.append("shippingline", 0);
                    fd.append("processType", process);
                }
                
                if (origin == 4) {
                    fd.append("warehouseid", $("#wh_name_singapore").val());
                    fd.append("warehouseid_rounglogs", $("#wh_name_singapore").val());
                    fd.append("measurement_system_roundlogs", $("#measurement_system_singapore").val());
                    fd.append("shippingline", $("#shipping_name_singapore").val());
                    fd.append("receptiondate", $("#reception_date_signapore").val());
                    fd.append("sealnumber", $("#seal_number_signapore").val());
                   
                    if($("#rounding_factor_singapore").val().length == 0) {
                        fd.append("roundingfactor", 0);
                    } else {
                        fd.append("roundingfactor", $("#rounding_factor_singapore").val());
                    }
                    
                    if($("#length_allowance").val().length == 0) {
                        fd.append('length_allowance', 0);
                    } else {
                        fd.append('length_allowance', $("#length_allowance").val());
                    }
                    
                    if($("#circumference_allowance").val().length == 0) {
                        fd.append('circumference_allowance', 0);
                    } else {
                        fd.append('circumference_allowance', $("#circumference_allowance").val());
                    }
                    
                    
                } else {
                    fd.append("sealnumber", "");
                    fd.append("roundingfactor", 0);
                    
                    if($("#length_allowance").val().length == 0) {
                        fd.append('length_allowance', 0);
                    } else {
                        fd.append('length_allowance', $("#length_allowance").val());
                    }
                    
                    if($("#circumference_allowance").val().length == 0) {
                        fd.append('circumference_allowance', 0);
                    } else {
                        fd.append('circumference_allowance', $("#circumference_allowance").val());
                    }
                }

                fd.append("purchaseunit", $("#hdnpurchaseunit").val());

                fd.append("farmdata", JSON.stringify(farm_data_array));

                $(".add_farm").prop('disabled', true);
                toastr.info(processing_request);
                var obj = $(this),
                    action = obj.attr('name'),
                    form_table = obj.data('form-table');

                //$("#loading").show();

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

    function fetchCompanySettings(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_companysetting_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#hdnmandatoryreceptiondetails").val(JSON.result);
                }
            }
        });
    }

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
                        $("#divReceptionDetailRoundLogs").hide();
                    } else if (producttypeid == 2 || producttypeid == 4) {
                        $("#divReceptionDetail").hide();
                        if(originid == 4) {
                            $("#divReceptionDetailRoundLogs").hide();
                        } else {
                            $("#divReceptionDetailRoundLogs").show();
                        }
                    } else {
                        $("#divReceptionDetail").hide();
                        $("#divReceptionDetailRoundLogs").hide();
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
                    $("#length_allowance").val(JSON.result["length_allowance"]);
                    $("#circumference_allowance").val(JSON.result["circumference_allowance"]);
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

                    $("#wh_name_roundlogs").empty();
                    $("#wh_name_roundlogs").append(JSON.result);

                    $("#wh_name_pine").empty();
                    $("#wh_name_pine").append(JSON.result);
                    
                    $("#wh_name_singapore").empty();
                    $("#wh_name_singapore").append(JSON.result);
                }
            }
        });

        $.ajax({
            url: base_url + "/get_measurement_system?originid=" + originid + "&producttypeid=" + $("#product_type").val(),
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#measurement_system_roundlogs").empty();
                    $("#measurement_system_roundlogs").append(JSON.result);
                    
                    $("#measurement_system_singapore").empty();
                    $("#measurement_system_singapore").append(JSON.result);
                }
            }
        });
    }

    function fetchShippingLines(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_shipping_lines_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#shipping_name_pine").empty();
                    $("#shipping_name_pine").append(JSON.result);
                    
                    $("#shipping_name_singapore").empty();
                    $("#shipping_name_singapore").append(JSON.result);
                }
            }
        });
    }

    var loadFile = function(event) {
        $("#error-farmupload").hide();
        var inventoryorder = $("#inventory_order_add").val().trim();
        var purchaseContractId = $("#purchase_contract").val();
        var productId = $("#product_name").val();
        event.preventDefault();

        if (purchaseContractId == 0) {
            toastr.clear();
            toastr.error(error_purchase_contract);
            $("#error-purchasecontract").show();
            $("#fileFarmExcel").val("");
        } else {
            $("#error-purchasecontract").hide();
        }

        if ($("#origin_farms").val() == 3) {
            //DO NOTHING
        } else {
            if (inventoryorder.length == 0) {
                toastr.clear();
                toastr.error(error_inventory_order);
                $("#error-inventoryorder_add").show();
                $("#fileFarmExcel").val("");
            } else {
                $("#error-inventoryorder_add").hide();
            }
        }

        toastr.clear();

        $('#loading').show();
        farm_data_array = [];
        var fd = new FormData();
        var files = $('#fileFarmExcel')[0].files[0];
        if (files != null && files != "") {
            fd.append('fileFarmExcel', files);

            if ($("#origin_farms").val() == 3) {
                fd.append('inventoryOrder', "");
            } else {
                fd.append('inventoryOrder', inventoryorder);
            }

            fd.append('purchaseUnit', $("#hdnpurchaseunit").val());
            fd.append('originId', $("#origin_farms").val());
            fd.append('productTypeId', $("#product_type").val());
            fd.append('purchaseContractId', purchaseContractId);
            fd.append('productId', productId);
            fd.append('length_allowance', $("#length_allowance").val());
            fd.append('circumference_allowance', $("#circumference_allowance").val());
            
            if($("#origin_farms").val() == 4) {
                if($("#rounding_factor_singapore").val().length == 0) {
                    fd.append("roundingfactor", 0);
                } else {
                    fd.append("roundingfactor", $("#rounding_factor_singapore").val());
                }
               
            } else {
                fd.append("roundingfactor", 0);
            }
            
            

            $.ajax({
                url: base_url + "/load_farm_template",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();
                    $("#fileFarmExcel").val("");
                    deletefilesfromfolder();
                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();
                        if (jsonResult.result["purchaseUnit"] == "1") {
                            $("#lblShortTons").hide();
                            $("#lblMetriTons").hide();
                            $("#lblNetLbs").hide();
                            $("#lblVolume").text(total_pie + ": " + jsonResult.result["totalVolume"]);
                        } else if (jsonResult.result["purchaseUnit"] == "11") {
                            $("#lblShortTons").show();
                            $("#lblMetriTons").show();
                            $("#lblNetLbs").show();

                            $("#lblShortTons").text(total_ST + ": " + jsonResult.result["totalST"]);
                            $("#lblMetriTons").text(total_MT + ": " + jsonResult.result["totalMT"]);
                            $("#lblNetLbs").text(total_LBS + ": " + jsonResult.result["totalNetLbs"]);
                            $("#lblVolume").text(total_jas_volume + ": " + jsonResult.result["totalVolume"]);
                        } else {
                            $("#lblShortTons").hide();
                            $("#lblMetriTons").hide();
                            $("#lblNetLbs").hide();
                            $("#lblVolume").text(total_volume + ": " + jsonResult.result["totalVolume"]);
                        }

                        $("#lblTotalPieces").text(total_pieces + ": " + jsonResult.result["totalPieces"]);
                        farm_data_array = jsonResult.result["farmData"];

                        if (jsonResult.datatable == true) {
                            $("#divTableContainer").show();
                            $("#divMessage").show();
                            var jsonData = JSON.parse(JSON.stringify(farm_data_array));
                            var length = Object.keys(farm_data_array).length;

                            $("#xin_table_container").DataTable({
                                "bDestroy": true,
                                "scrollCollapse": true,
                                ordering: false,
                                paging: false,
                                info: false,
                                searching: false,
                                fixedColumns: true,
                                responsive: true,
                                aoColumnDefs: [{
                                    bSortable: false,
                                    aTargets: [0]
                                }],
                                "language": {
                                    "url": datatable_language
                                }
                            });

                            $("#xin_table_container").DataTable().clear();

                            for (var i = 0; i < length; i++) {
                                var containerDetails = jsonData[i];

                                $("#xin_table_container").dataTable().fnAddData([
                                    containerDetails.containerNumber,
                                    containerDetails.seal,
                                    containerDetails.metricTon,
                                    containerDetails.shortTons,
                                    containerDetails.netLbs,
                                    containerDetails.diameter,
                                    containerDetails.length,
                                    containerDetails.totalCount,
                                    containerDetails.jasVolume,
                                    containerDetails.purchasePrice,
                                    containerDetails.totalPurchasePrice,
                                    containerDetails.salesPrice,
                                    containerDetails.totalSalesPrice,
                                ]);
                            }
                        } else {
                            $("#divTableContainer").hide();
                            $("#divMessage").hide();
                        }

                        $("#divUploadedDetails").show();
                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $("#divUploadedDetails").hide();
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
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
        
        $("#reception_date_signapore").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1y',
            maxDate: '0d',
        });

        $('.ui-datepicker').addClass('notranslate');
    });
</script>